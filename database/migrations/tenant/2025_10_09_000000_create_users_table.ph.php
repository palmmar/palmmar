<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration {
        protected $connection = 'tenant';

        public function up(): void
        {
            Schema::connection('tenant')->create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('zipcode')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::connection('tenant')->dropIfExists('users');
        }
    };
