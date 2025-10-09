<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection($this->connection)->create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();        // ex: subdomän, t.ex. "acme"
            $table->string('name');
            $table->string('db_name');
            $table->string('db_user');
            $table->string('db_pass');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('tenants');
    }
};
