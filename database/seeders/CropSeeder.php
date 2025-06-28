<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Crop;

class CropSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $crops = [
            [
                'name' => 'Пшеница',
                'description' => 'Озимая и яровая пшеница - основная зерновая культура'
            ],
            [
                'name' => 'Кукуруза',
                'description' => 'Кукуруза на зерно и силос'
            ],
            [
                'name' => 'Подсолнечник',
                'description' => 'Подсолнечник на масло'
            ],
            [
                'name' => 'Соя',
                'description' => 'Соя - бобовая культура'
            ],
            [
                'name' => 'Рапс',
                'description' => 'Рапс озимый и яровой'
            ],
            [
                'name' => 'Ячмень',
                'description' => 'Ячмень озимый и яровой'
            ],
            [
                'name' => 'Овес',
                'description' => 'Овес - кормовая культура'
            ],
            [
                'name' => 'Рожь',
                'description' => 'Рожь озимая'
            ],
            [
                'name' => 'Гречиха',
                'description' => 'Гречиха - крупяная культура'
            ],
            [
                'name' => 'Лен',
                'description' => 'Лен масличный'
            ]
        ];

        foreach ($crops as $crop) {
            Crop::firstOrCreate(
                ['name' => $crop['name']],
                $crop
            );
        }
    }
} 