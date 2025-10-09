<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration {
        // Kör denna migration mot landlord-anslutningen
        protected $connection = 'landlord';

        public function up(): void
        {
            Schema::connection($this->connection)->table('tenants', function (Blueprint $table) {
                // valfria, men bra att ha
                $table->string('db_host')->nullable();
                $table->unsignedSmallInteger('db_port')->nullable();
                $table->string('domain')->nullable();
                $table->string('plan')->nullable();
                $table->boolean('is_active')->default(true);
            });
        }

        public function down(): void
        {
            Schema::connection($this->connection)->table('tenants', function (Blueprint $table) {
                $table->dropColumn(['db_host', 'db_port', 'domain', 'plan', 'is_active']);
            });
        }
    };
