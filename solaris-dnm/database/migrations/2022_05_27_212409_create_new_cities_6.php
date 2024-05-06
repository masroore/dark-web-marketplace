<?php

use Illuminate\Database\Migrations\Migration;

class CreateNewCities6 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // фикс
        App\City::where('id', '=', 606)
            ->where('title', '=', 'Приморско Ахтарск')
            ->update(['title' => 'Приморско-Ахтарск']);

        $cities = App\City::where('priority', '<=', '8000')->pluck('title');
        $newCities = [
            'Верхняя Тура',
            'Балабаново',
            'Малоярославец',
        ];

        $allCities = collect($newCities)->merge($cities)->unique()->sort()->values();
        foreach ($allCities as $i => $title) {
            $priority = 8000 - ($i * 10);
            $cityModel = App\City::whereTitle($title)->first();
            if ($cityModel) {
                $cityModel->priority = $priority;
                $cityModel->save();
            } else {
                App\City::create(['title' => $title, 'priority' => $priority]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
}
