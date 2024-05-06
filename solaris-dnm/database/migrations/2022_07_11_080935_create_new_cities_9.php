<?php

use Illuminate\Database\Migrations\Migration;

class CreateNewCities9 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $cities = App\City::where('priority', '<=', '8000')->pluck('title');
        $newCities = [
            'Бологое',
            'Городец (Нижегородская обл.)',
            'Кизляр',
            'Нижняя Салда',
            'Руза',
            'Гаврило-Ям',
            'Красково (Московская обл.)',
            'Малаховка (Московская обл.)',
            'Удомля',
            'Окуловка (Новгородская обл.)',
            'Лихославль',
            'Хвойная (Новгородская обл.)',
            'Любань (Ленинградская обл.)',
            'Шапки (Ленинградская обл.)',
            'Будогощь',
            'Кириши',
            'Чудово',
            'Малая Вишера',
            'Внуково',
            'Обухово',
            'Коммунар (Ленинградская обл.)',
            'Мегион',
            'Стрежевой',
            'Пыть-Ях',
            'Гулькевичи',
            'Дальнегорск',
            'Судогда',
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
