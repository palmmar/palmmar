<?php

    namespace App\Http\Middleware;

    use App\Models\Tenant;
    use Closure;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Facades\Config;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;

    class IdentifyTenant
    {
        public function handle(Request $request, Closure $next)
        {
            // 1) Subdomän -> tenant-id
            $host = $request->getHost();           // t.ex. acme.palmmar.test
            $parts = explode('.', $host);
            $subdomain = $parts[0] ?? null;

            // BYPASS för landlord-panelen (allt under /landlord)
            if ($request->is('landlord*')) {
                return $next($request);
            }

            // Dev-fallback: tillåt ?tenant=acme
            if (!$subdomain || in_array($subdomain, ['www','local','localhost'])) {
                $subdomain = $request->query('tenant', $subdomain);
            }
            if (!$subdomain) {
                abort(404, 'Tenant saknas.');
            }

            // 2) Hämta tenant
            /** @var Tenant $tenant */
            $tenant = Tenant::query()->findOrFail($subdomain);
            if (property_exists($tenant, 'is_active') && !$tenant->is_active) {
                abort(403, 'Tenant är inaktiv.');
            }

            // 3) Bygg runtime-config för tenant
            $cfg = $tenant->databaseConfig(); // måste innehålla 'username' och 'password'
            // Säkerställ att password inte är null (MariaDB tolkar null som "NO")
            if (!array_key_exists('password', $cfg) || $cfg['password'] === null) {
                $cfg['password'] = '';
            }

            Config::set('database.connections.tenant', $cfg);

            // 4) Släpp ev. gammal PDO, koppla upp på nytt och LÅS uppkopplingen
            DB::purge('tenant');
            DB::reconnect('tenant');                 // bygg ny PDO med ovan värden
            Config::set('database.default', 'tenant'); // använd tenant som default i requesten

            // 5) Testa anslutningen direkt (så inget hinner ändras senare)
            try {
                // Skapa PDO nu
                DB::connection('tenant')->getPdo();

                // Verifiera mot DB vem vi är och vilken DB som används
                $who = DB::connection('tenant')->selectOne('SELECT USER() AS u, DATABASE() AS d');
                Log::info('TENANT_CONN_OK', [
                    'tenant'   => $tenant->id,
                    'cfg_user' => $cfg['username'] ?? null,
                    'cfg_db'   => $cfg['database'] ?? null,
                    'db_user'  => $who->u ?? null,
                    'db_name'  => $who->d ?? null,
                ]);
            } catch (\Throwable $e) {
                Log::error('TENANT_CONN_FAIL', [
                    'tenant'   => $tenant->id,
                    'cfg_user' => $cfg['username'] ?? null,
                    'cfg_db'   => $cfg['database'] ?? null,
                    'err'      => $e->getMessage(),
                ]);
                abort(500, 'Kunde inte ansluta till tenant-databasen.');
            }

            // 6) Isolera cache-prefix (valfritt) och exponera tenant i IoC
            Config::set('cache.prefix', "tenant:{$tenant->id}");
            App::instance('tenant', $tenant);

            return $next($request);
        }
    }
