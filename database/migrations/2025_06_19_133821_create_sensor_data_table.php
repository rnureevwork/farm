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
        Schema::create('sensor_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->timestamp('timestamp');
            $table->decimal('temperature', 4, 1)->nullable(); // °C
            $table->smallInteger('humidity')->unsigned()->nullable(); // %
            $table->smallInteger('soil_moisture')->unsigned()->nullable(); // %
            $table->decimal('precipitation', 5, 1)->nullable(); // мм
            $table->decimal('wind_speed', 5, 1)->nullable(); // м/с
            $table->smallInteger('wind_direction')->unsigned()->nullable(); // 0-360°
            $table->decimal('pressure', 6, 1)->nullable(); // гПа
            $table->timestamps();
            
            $table->index(['station_id', 'timestamp'], 'idx_sensor_data_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_data');
    }
};
