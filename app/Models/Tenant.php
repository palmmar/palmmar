<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Tenant extends Model
    {
        use HasFactory;

        /**
         * Alla tenant-rader ligger i landlord-databasen.
         */
        protected $connection = 'landlord';

        /**
         * Tabellnamn i landlord-DB.
         */
        protected $table = 'tenants';

        /**
         * Primärnyckeln är en sträng (t.ex. subdomän: "acme").
         */
        public $incrementing = false;
        protected $keyType = 'string';

        /**
         * Mass-assignable fält.
         */
        protected $fillable = [
            'id',
            'name',
            'db_name',
            'db_user',
            'db_pass',
            'db_host',   // valfritt: lämna null för att använda global TENANT_DB_HOST
            'db_port',   // valfritt: lämna null för att använda global TENANT_DB_PORT
            'domain',    // valfritt: egen domän för tenanten
            'plan',      // valfritt: abonnemangsplan
            'is_active', // bool
        ];

        /**
         * Dölj känsliga fält när modellen serialiseras.
         */
        protected $hidden = [
            'db_pass',
        ];

        protected $casts = [
            'is_active' => 'bool',
        ];

        /**
         * Bygg en komplett Laravel-DB-connection-array för tenanten.
         * Används i middleware/jobb för att växla anslutning.
         */
        public function databaseConfig(array $overrides = []): array
        {
            $host = $this->db_host ?: config('database.connections.tenant.host', env('TENANT_DB_HOST', '127.0.0.1'));
            $port = $this->db_port ?: config('database.connections.tenant.port', (int) env('TENANT_DB_PORT', 3306));

            $config = [
                'driver'   => 'mysql',
                'host'     => $host,
                'port'     => $port,
                'database' => $this->db_name,
                'username' => $this->db_user,
                'password' => $this->db_pass,
                'charset'  => 'utf8mb4',
                'collation'=> 'utf8mb4_unicode_ci',
                'strict'   => true,
            ];

            return array_replace($config, $overrides);
        }

        /**
         * Scope: endast aktiva tenants.
         */
        public function scopeActive($query)
        {
            return $query->where('is_active', true);
        }

        /**
         * Hjälpmetoder för att aktivera/inaktivera.
         */
        public function markActive(): void
        {
            $this->is_active = true;
            $this->save();
        }

        public function markInactive(): void
        {
            $this->is_active = false;
            $this->save();
        }
    }
