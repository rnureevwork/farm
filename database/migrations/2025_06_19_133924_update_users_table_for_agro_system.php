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
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name', 100)->after('email');
            $table->enum('role', ['farmer', 'agronomist', 'technician', 'admin'])->after('full_name');
            $table->string('phone', 20)->nullable()->after('role');
            
            // Удаляем старые поля если они есть
            $table->dropColumn(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('email');
            $table->dropColumn(['full_name', 'role', 'phone']);
        });
    }
};
