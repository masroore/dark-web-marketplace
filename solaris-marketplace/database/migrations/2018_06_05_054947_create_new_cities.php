<?php

use Illuminate\Database\Migrations\Migration;

class CreateNewCities extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        App\City::create([
            'title' => 'Полевской',
            'priority' => 4625,
        ]);

        App\City::create([
            'title' => 'Ревда',
            'priority' => 4575,
        ]);

        App\City::create([
            'title' => 'Заречный',
            'priority' => 6875,
        ]);

        App\City::create([
            'title' => 'Асбест',
            'priority' => 7875,
        ]);

        App\City::create([
            'title' => 'Каменск-Уральский',
            'priority' => 6475,
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
}
