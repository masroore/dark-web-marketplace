<?php

use Illuminate\Database\Migrations\Migration;

class NewCities8 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $cities = App\City::where('priority', '<=', '8000')->pluck('title');
        $newCities = [
            'Небуг',
            'Агой',
            'Ольгинка',
            'Новомихайловский',
            'Лермонтово',
            'Джубга',
            'Архипо-Осиповка',
            'Дивноморское',
            'Саки',
            'Сердобск',
            'Шлиссельбург',
            'Кировск (Ленинградская область)',
            'Кировск (Мурманская область)',
            'Мга (городской посёлок)',
            'Благовещенск (Башкортостан)',
            'Губкинский',
            'Тарко-Сале',
            'Октябрьский (Архангельская область)',
            'Вельск',
            'Кулой (рабочий посёлок)',
            'Слоним',
            'Краснозаводск',
            'Струнино',
            'Ростов',
            'Бавлы',
            'Стрежевой',
            'Мегион',
            'Пыть-Ях',
            'Шексна',
            'Сокол (Вологодская область)',
            'Коноша',
            'Великий Устюг',
            'Коряжма',
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

        // Всеволжск - удаление города с опечаткой
        $city = App\City::where('title', '=', 'Всеволожск')->first();
        $cityTypo = App\City::where('title', '=', 'Всеволжск')->first();

        $changed = [
            'goods_cities' => 0,
            'goods_packages' => 0,
            'orders' => 0,
        ];

        if ($city && $cityTypo) {
            $changed['employees'] = App\Employee::where('city_id', '=', $cityTypo->id)->update([
                'city_id' => $city->id,
            ]);

            $typoGoodsCities = App\GoodsCity::where('city_id', '=', $cityTypo->id)->get();
            foreach ($typoGoodsCities as $typoGoodsCity) {
                try {
                    $typoGoodsCity->city_id = $city->id;
                    $typoGoodsCity->save();
                    ++$changed['goods_cities'];
                } catch (PDOException $exception) {
                    $typoGoodsCity->delete();
                }
            }

            $typoPackages = App\GoodsPackage::where('city_id', '=', $cityTypo->id)->get();
            foreach ($typoPackages as $typoPackage) {
                try {
                    $typoPackage->city_id = $city->id;
                    $typoPackage->save();
                    ++$changed['goods_packages'];
                } catch (PDOException $exception) {
                    $typoPackage->delete();
                }
            }

            $changed['orders'] = App\Order::where('city_id', '=', $cityTypo->id)->update([
                'city_id' => $city->id,
            ]);

            $cityTypo->delete();

            foreach ($changed as $k => $v) {
                if ($v > 0) {
                    echo "Table $k: $v row(s) updated.\n"; // оставить?
                }
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
