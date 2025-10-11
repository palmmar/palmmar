<?php

    declare(strict_types=1);

    namespace App\Services;

    use App\Models\Tenant;
    use Illuminate\Support\Facades\Artisan;
    use Illuminate\Support\Facades\Config;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Log;
    use Throwable;

    class TenantProvisioner
    {
        public static function make(): self
        {
            return new self();
        }

        /**
         * Provisionerar en tenant-databas: skapar DB (valfritt), sätter connection,
         * kör migrationer och (valfritt) skapar en admin-användare.
         *
         * $opts:
         *  - create_database (bool)  : skapa databasen om den saknas
         *  - migrate (bool)          : kör migrationer mot tenant-connection
         *  - seed_admin (bool)       : skapa/uppdatera admin-användare
         *  - admin_email (?string)
         *  - admin_password (string) : default 'password'
         *  - migration_path (?string): t.ex. 'database/migrations/tenant' om du vill separera
         */
        public function provision(Tenant $tenant, array $opts = []): void
        {
            $opts = array_merge([
                'create_database' => false,
                'migrate'         => true,
                'seed_admin'      => false,
                'admin_email'     => null,
                'admin_password'  => 'password',
                'migration_path'  => null, // sätt t.ex. 'database/migrations/tenant' om du delar upp migreringar
            ], $opts);

            // 1) Skapa DB om begärt – använd landlord-connection för CREATE DATABASE
            if ($opts['create_database']) {
                $dbName = $tenant->db_name;

                Log::info('PROVISION:create_database', ['tenant' => $tenant->id, 'db' => $dbName]);

                // OBS: körs mot landlord-anslutningen
                DB::connection('landlord')->statement(
                    sprintf('CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci', str_replace('`', '``', $dbName))
                );
            }

            // 2) Bygg/registrera runtime-connection "tenant" från tenant->databaseConfig()
            $cfg = $tenant->databaseConfig();
            if (!array_key_exists('password', $cfg) || $cfg['password'] === null) {
                $cfg['password'] = ''; // undvik null -> PDO kan falla tillbaka till NO password
            }

            Config::set('database.connections.tenant', $cfg);
            DB::purge('tenant'); // rensa ev. cached connection

            // Liten hälsokontroll så vi ser att vi kommer åt tenant-db:n
            try {
                $ping = DB::connection('tenant')->select('select 1 as ok');
                Log::info('PROVISION:tenant_connection_ok', [
                    'tenant' => $tenant->id,
                    'db'     => $cfg['database'] ?? null,
                    'host'   => $cfg['host'] ?? null,
                    'ok'     => $ping[0]->ok ?? null,
                ]);
            } catch (Throwable $e) {
                Log::error('PROVISION:tenant_connection_failed', [
                    'tenant' => $tenant->id,
                    'cfg'    => $cfg,
                    'error'  => $e->getMessage(),
                ]);
                throw $e;
            }

            // 3) Migrera ENDAST tenant-connection
            if ($opts['migrate']) {
                $params = [
                    '--database' => 'tenant',
                    '--force'    => true,
                ];

                // Om du har separata tenant-migrationer:
                if (!empty($opts['migration_path'])) {
                    $params['--path'] = $opts['migration_path'];
                }

                Log::info('PROVISION:migrate', ['tenant' => $tenant->id, 'params' => $params]);

                Artisan::call('migrate', $params);
                Log::info('PROVISION:migrate_done', [
                    'tenant' => $tenant->id,
                    'output' => Artisan::output(),
                ]);
            }

            // 4) Skapa/uppdatera admin-user (valfritt)
            if ($opts['seed_admin'] && filled($opts['admin_email'])) {
                $email = (string) $opts['admin_email'];
                $plain = (string) ($opts['admin_password'] ?? 'password');

                Log::info('PROVISION:seed_admin', ['tenant' => $tenant->id, 'email' => $email]);

                // Använder tenant-anslutningen
                DB::connection('tenant')->table('users')->updateOrInsert(
                    ['email' => $email],
                    [
                        'name'              => 'Admin',
                        'password'          => Hash::make($plain),
                        'email_verified_at' => now(),
                        'updated_at'        => now(),
                        'created_at'        => now(),
                    ]
                );
            }
        }
    }
