<?php

    namespace App\Http\Middleware;

    use App\Models\Tenant;
    use Closure;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Facades\Config;
    use Illuminate\Support\Facades\DB;

    class IdentifyTenant
    {
        public function handle(Request $request, Closure $next)
        {
            $host = $request->getHost();              // ex: acme.local.test
            $parts = explode('.', $host);
            $subdomain = $parts[0] ?? null;

            if (!$subdomain || in_array($subdomain, ['www', 'local']))
            {
                abort(404, 'Tenant saknas.');
            }

            $tenant = Tenant::query()->findOrFail($subdomain);

            // bygg dynamisk connection för tenant
            $tenant_connection = $tenant->databaseConfig();
            Config::set('database.connections.tenant', $tenant_connection);

            DB::purge('tenant');
            DB::setDefaultConnection('tenant');

            // prefix för cache/session om du vill isolera ytterligare
            Config::set('cache.prefix', "tenant:{$tenant->id}");

            // gör tenanten tillgänglig i appen
            App::instance('tenant', $tenant);

            return $next($request);
        }
    }
