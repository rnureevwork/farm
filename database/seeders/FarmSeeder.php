<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Farm;

class FarmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $farms = [
            [
                'name' => 'Ферма "Золотое поле"',
                'contact_phone' => '+7 (999) 123-45-67',
            ],
            [
                'name' => 'Агрохолдинг "Весна"',
                'contact_phone' => '+7 (999) 234-56-78',
            ],
            [
                'name' => 'КФХ "Урожай"',
                'contact_phone' => '+7 (999) 345-67-89',
            ],
            [
                'name' => 'Сельхозпредприятие "Рассвет"',
                'contact_phone' => '+7 (999) 456-78-90',
            ],
        ];

        foreach ($farms as $farm) {
            Farm::create($farm);
        }
    }
} 