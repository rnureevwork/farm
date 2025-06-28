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
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number', 50)->unique();
            $table->foreignId('field_id')->nullable()->constrained()->onDelete('set null');
            $table->json('gps_coordinates')->nullable();
            $table->json('expected_location')->nullable();
            $table->smallInteger('battery_level')->unsigned()->nullable();
            $table->integer('signal_strength')->nullable(); // dBm
            $table->decimal('movement_speed', 5, 1)->nullable(); // км/ч
            $table->timestamp('last_seen')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('api_token', 80)->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};
