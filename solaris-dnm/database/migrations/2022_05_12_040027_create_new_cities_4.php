<?php

use Illuminate\Database\Migrations\Migration;

class CreateNewCities4 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $cities = App\City::where('priority', '<=', '8000')->pluck('title');
        $newCities = ['Аксай', 'Апрелевка', 'Балахна', 'Балтийск', 'Балтийск', 'Барановичи', 'Бобруйск', 'Борисов', 'Боровичи', 'Брест', 'Валдай', 'Венёв', 'Верхнее Дуброво', 'Витебск', 'Вязники', 'Гвардейск', 'Гомель', 'Городец', 'Гродно', 'Гусев', 'Гусев', 'Гусев', 'Гусь-Хрустальный', 'Дедовск', 'Дедовск', 'Жодино', 'Заволжье', 'Заокский', 'Зарайск', 'Звенигород', 'Звенигород', 'Зеленоградск', 'Зеленоградск', 'Зеленокумск', 'Зерноград', 'Зимовники', 'Знаменск', 'Исток', 'Касимов', 'Кобрин', 'Ковдор', 'Колпино', 'Константиновск', 'Красногорск', 'Красный Сулин', 'Кременки', 'Кременки', 'Кудымкар', 'Кукуштан', 'Ладушкин', 'Лида', 'Луховицы', 'Матвеев Курган', 'Меленки', 'Миллерово', 'Могилев', 'Мозырь', 'Молодечно', 'Новополоцк', 'Новосокольники', 'Опочка', 'Орша', 'Остров', 'Прибрежный (пос.)', 'Пинск', 'Пионерский', 'Пионерский', 'Полесск', 'Полоцк', 'Правдинск', 'Протвино', 'Пустошка', 'Пыталово', 'Речица', 'Сафоново', 'Светлогорск', 'Светлогорск', 'Светлый', 'Светлый', 'Себеж', 'Семикаракорск', 'Смоливичи', 'Советск', 'Солигорск', 'Софрино', 'Среднеуральск ', 'Староминская', 'Старощербиновская', 'Суворов', 'Топки', 'Троицк (Челябинская обл.)', 'Тучково', 'Ульяновка', 'Усть-Донецкий', 'Черняховск', 'Шилово', 'Янтарный', 'Ясногорск'];
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