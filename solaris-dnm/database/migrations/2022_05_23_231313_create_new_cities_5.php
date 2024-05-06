<?php

use Illuminate\Database\Migrations\Migration;

class CreateNewCities5 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $cities = App\City::where('priority', '<=', '8000')->pluck('title');
        $newCities = [
            'Ташкент',
            'Отправка по Узбекистану',
            'Жлобин',
            'Печора',
            'Усинск',
            'Салехард',
            'Надым',
            'Беломорск',
            'Сегежа',
            'Медвежегорск',
            'Гирвас',
            'Вичуга',
            'Тейково',
            'Родники',
            'Плёс',
            'Красноармейск',
            'Кольчугино',
            'Киржач',
            'Приозерск',
            'Черноголовка',
            'Котельники',
            'Дзержинский',
            'Стрельна',
            'Лакинск',
            'Собинка',
            'Железноводск',
            'Кубинка',
            'Детчино',
            'Медвежьегорск',
            'Кондопога',
            'Кандалакша',
            'Костомушка',
            'Горячий Ключ',
            'Динская',
            'Усть-Лабинск',
            'Кореновск',
            'Абинск',
            'Калининская',
            'Хадыженск',
            'Апшеронск',
            'Архипово-Осиповка',
            'Тбилисская',
            'Луга',
            'Североуральск',
            'Волчанск',
            'Ивдель',
            'Талдом',
            'Моршанск',
            'Отрадное',
            'Кировск',
            'Мурино',
            'Приморско-Ахтарск',
            'Каневская',
            'станица Ленинградская',
            'Шахунья',
            'Щербинка',
            'Лангепас',
            'Радужный',
            'Стрижи',
        ];

        //        foreach ($newCities as $c) {
        //            if($city = \App\City::where('title', 'LIKE', '%' . $c . '%')->first()) {
        //                echo $city->title.' looks like '.$c."\n"
        //            }
        //        }
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
