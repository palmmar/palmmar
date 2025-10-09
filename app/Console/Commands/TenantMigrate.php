<?php


    namespace App\Console\Commands;

    use App\Models\Tenant;
    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\Artisan;
    use Illuminate\Support\Facades\Config;
    use Illuminate\Support\Facades\DB;

    class TenantMigrate extends Command
    {
        protected $signature = 'tenant:migrate {tenant_id} {--fresh} {--seed}';
        protected $description = 'Kör tenant-migreringar för en specifik tenant';

        public function handle(): int
        {
            $tenant_id = $this->argument('tenant_id');
            /** @var Tenant $tenant */
            $tenant = Tenant::findOrFail($tenant_id);

            // Bygg connection (använder databaseConfig() om du har den)
            if (method_exists($tenant, 'databaseConfig'))
            {
                $conn = $tenant->databaseConfig();
            }
            else
            {
                $host = config('database.connections.tenant.host', env('TENANT_DB_HOST', '127.0.0.1'));
                $port = config('database.connections.tenant.port', (int)env('TENANT_DB_PORT', 3306));
                $conn = [
                    'driver' => 'mysql',
                    'host' => $host,
                    'port' => $port,
                    'database' => $tenant->db_name,
                    'username' => $tenant->db_user,
                    'password' => $tenant->db_pass,
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'strict' => true,
                ];
            }

            Config::set('database.connections.tenant', $conn);
            DB::purge('tenant');

            if ($this->option('fresh'))
            {
                Artisan::call('migrate:fresh', [
                    '--database' => 'tenant',
                    '--path' => 'database/migrations/tenant',
                    '--force' => true,
                ]);
            }
            else
            {
                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--path' => 'database/migrations/tenant',
                    '--force' => true,
                ]);
            }
            $this->line(Artisan::output());

            if ($this->option('seed') && file_exists(base_path('database/seeders/TenantDatabaseSeeder.php')))
            {
                Artisan::call('db:seed', [
                    '--database' => 'tenant',
                    '--class' => 'TenantDatabaseSeeder',
                    '--force' => true,
                ]);
                $this->line(Artisan::output());
            }

            $this->info("Tenant '{$tenant_id}' migrerad.");
            return self::SUCCESS;
        }
    }
