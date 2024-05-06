<?php

use Illuminate\Database\Seeder;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['city' => 'Москва', 'priority' => 10000],
            ['city' => 'Московская область', 'priority' => 9950],
            ['city' => 'Санкт-Петербург', 'priority' => 9900],
            ['city' => 'Отправка по России', 'priority' => 9850],
            ['city' => 'Отправка по Украине', 'priority' => 9800],
            ['city' => 'Отправка по России и СНГ', 'priority' => 9750],
            ['city' => 'Без региона', 'priority' => 9700],
            ['city' => 'Адлер'],
            ['city' => 'Анапа'],
            ['city' => 'Архангельск'],
            ['city' => 'Астрахань'],
            ['city' => 'Барнаул'],
            ['city' => 'Белгород'],
            ['city' => 'Бийск'],
            ['city' => 'Благовещенск'],
            ['city' => 'Боровск (Калужская область)'],
            ['city' => 'Брянск'],
            ['city' => 'Великий Новгород'],
            ['city' => 'Владивосток'],
            ['city' => 'Владимир'],
            ['city' => 'Волгоград'],
            ['city' => 'Вологда'],
            ['city' => 'Воронеж'],
            ['city' => 'Вышний Волочек'],
            ['city' => 'Геленджик'],
            ['city' => 'Дзержинск (Нижегородская обл.)'],
            ['city' => 'Днепропетровск'],
            ['city' => 'Евпатория'],
            ['city' => 'Екатеринбург'],
            ['city' => 'Запорожье'],
            ['city' => 'Зеленоград'],
            ['city' => 'Иваново'],
            ['city' => 'Ижевск'],
            ['city' => 'Иркутск'],
            ['city' => 'Йошкар-Ола'],
            ['city' => 'Казань'],
            ['city' => 'Калуга'],
            ['city' => 'Кемерово'],
            ['city' => 'Керчь'],
            ['city' => 'Киев'],
            ['city' => 'Киров'],
            ['city' => 'Клин'],
            ['city' => 'Коктебель'],
            ['city' => 'Коломна'],
            ['city' => 'Кострома'],
            ['city' => 'Краснодар'],
            ['city' => 'Красноярск'],
            ['city' => 'Крым'],
            ['city' => 'Кстово (Нижегородская обл.)'],
            ['city' => 'Курган'],
            ['city' => 'Курск'],
            ['city' => 'Липецк'],
            ['city' => 'Львов'],
            ['city' => 'Люберцы МО'],
            ['city' => 'Магнитогорск'],
            ['city' => 'Миасс'],
            ['city' => 'Минск'],
            ['city' => 'Набережные Челны'],
            ['city' => 'Наро-Фоминск'],
            ['city' => 'Нижневартовск'],
            ['city' => 'Нижний Новгород'],
            ['city' => 'Новокузнецк'],
            ['city' => 'Новомосковск'],
            ['city' => 'Новороссийск'],
            ['city' => 'Новосибирск'],
            ['city' => 'Обнинск'],
            ['city' => 'Одесса'],
            ['city' => 'Омск'],
            ['city' => 'Орел'],
            ['city' => 'Оренбург'],
            ['city' => 'Пенза'],
            ['city' => 'Первоуральск'],
            ['city' => 'Пермь'],
            ['city' => 'Петрозаводск'],
            ['city' => 'Псков'],
            ['city' => 'Ростов-на-Дону'],
            ['city' => 'Рязань'],
            ['city' => 'Самара'],
            ['city' => 'Саратов'],
            ['city' => 'Севастополь'],
            ['city' => 'Серпухов'],
            ['city' => 'Симферополь'],
            ['city' => 'Смоленск'],
            ['city' => 'Сочи'],
            ['city' => 'Ставрополь'],
            ['city' => 'Стерлитамак'],
            ['city' => 'Судак'],
            ['city' => 'Сургут'],
            ['city' => 'Сухиничи'],
            ['city' => 'Тамбов'],
            ['city' => 'Тверь'],
            ['city' => 'Тобольск'],
            ['city' => 'Тольятти'],
            ['city' => 'Томск'],
            ['city' => 'Торжок'],
            ['city' => 'Тула'],
            ['city' => 'Тюмень'],
            ['city' => 'Ульяновск'],
            ['city' => 'Уфа'],
            ['city' => 'Феодосия'],
            ['city' => 'Хабаровск'],
            ['city' => 'Харьков'],
            ['city' => 'Чебоксары'],
            ['city' => 'Челябинск'],
            ['city' => 'Череповец'],
            ['city' => 'Щёкино'],
            ['city' => 'Якутск'],
            ['city' => 'Ялта'],
            ['city' => 'Ярославль'],
        ];

        $i = 8000;
        foreach ($cities as $city) {
            if (isset($city['priority'])) {
                $priority = $city['priority'];
            } else {
                $priority = $i;
                $i -= 50;
            }

            App\City::create([
                'title' => $city['city'],
                'priority' => $priority,
            ]);
        }
    }
}