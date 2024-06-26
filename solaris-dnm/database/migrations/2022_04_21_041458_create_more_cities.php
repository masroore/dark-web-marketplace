<?php

use Illuminate\Database\Migrations\Migration;

class CreateMoreCities extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $cities = App\City::where('priority', '<=', '8000')->pluck('title');
        $newCities = ['Алапаевск', 'Арамиль', 'Артемовский', 'Асбест', 'Аша', 'Бакал', 'Белорецк', 'Белоярский', 'Березовский', 'Билимбай', 'Богданович', 'Буланаш', 'Васкелово', 'Верх-Нейвинск', 'Верхнеуральск', 'Верхний Тагил', 'Верхний Уфалей', 'Верхняя Пышма', 'Всеволжск', 'Далматово', 'Двуреченск', 'Дегтярск', 'Екатеринбург', 'Еманжелинск', 'Заречный', 'Златоуст', 'Ирбит', 'Ириновка', 'Каменск-Уральский', 'Камышлов', 'Каргаполье', 'Карпинск', 'Касли', 'Катав-Ивановск', 'Катайск', 'Качканар', 'Кемерово', 'Кировград', 'Комарово', 'Копейск', 'Краснотурьинск', 'Красноуральск', 'Красноуфимск', 'Кропачево', 'Кунгур', 'Курган', 'Куртамыш', 'Куса', 'Кушва', 'Кыштым', 'Миасс', 'Миньяр', 'Михайловск', 'Невьянск', 'Нижние Серьги', 'Нижний Тагил', 'Нижний Уфалей', 'Нижняя Тура', 'Новая Ляля', 'Новосибирск', 'Новоуральск', 'Новоуткинск', 'Нягань', 'Нязепетровск', 'Озерск', 'Омск', 'Орехово', 'Первоуральск', 'Песочный', 'Пласт', 'Полевской', 'Пышма', 'Ревда', 'Реж', 'Рефтинский', 'Санкт-Петербург', 'Сатка', 'Серов', 'Сим', 'Снежинск', 'Советский', 'Сосново', 'Сухой Лог', 'Сысерть', 'Тавда', 'Талица', 'Тобольск', 'Токсово', 'Томск', 'Трехгорный', 'Троицк', 'Троицкий', 'Тугулым', 'Туринск', 'Тюмень', 'Уйское', 'Усть-Катав', 'Уфа', 'Учалы', 'Чебаркуль', 'Челябинск', 'Шадринск', 'Югорск', 'Южноуральск', 'Юрюзань'];
        $allCities = collect($newCities)->merge($cities)->unique()->sort()->values();
        foreach ($allCities as $i => $title) {
            $priority = 8000 - ($i * 15);
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
