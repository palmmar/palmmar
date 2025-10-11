<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration {
        protected $connection = 'landlord'; // viktigt!

        public function up(): void
        {
            if (Schema::connection($this->connection)->hasTable('tenants')) {
                Schema::connection($this->connection)->table('tenants', function (Blueprint $table) {
                    if (!Schema::connection($this->connection)->hasColumn('tenants', 'db_host')) {
                        $table->string('db_host')->nullable();
                    }
                    if (!Schema::connection($this->connection)->hasColumn('tenants', 'db_port')) {
                        $table->string('db_port')->nullable();
                    }
                    if (!Schema::connection($this->connection)->hasColumn('tenants', 'domain')) {
                        $table->string('domain')->nullable();
                    }
                    if (!Schema::connection($this->connection)->hasColumn('tenants', 'plan')) {
                        $table->string('plan')->nullable();
                    }
                    if (!Schema::connection($this->connection)->hasColumn('tenants', 'is_active')) {
                        $table->boolean('is_active')->default(true);
                    }
                });
            }
        }

        public function down(): void
        {
            if (Schema::connection($this->connection)->hasTable('tenants')) {
                Schema::connection($this->connection)->table('tenants', function (Blueprint $table) {
                    foreach (['db_host','db_port','domain','plan','is_active'] as $col) {
                        if (Schema::connection($this->connection)->hasColumn('tenants', $col)) {
                            $table->dropColumn($col);
                        }
                    }
                });
            }
        }
    };

