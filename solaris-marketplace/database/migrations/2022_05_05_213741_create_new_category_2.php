<?php

use Illuminate\Database\Migrations\Migration;

class CreateNewCategory2 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $category = new App\Category(['parent_id' => null, 'title' => 'Работа', 'priority' => 6000]);
        $category->save();

        $categories = [
            ['parent_id' => $category->id, 'title' => 'Курьер', 'priority' => 5900],
            ['parent_id' => $category->id, 'title' => 'Перевозчик', 'priority' => 5890],
            ['parent_id' => $category->id, 'title' => 'Удаленная работа', 'priority' => 5880],
            ['parent_id' => $category->id, 'title' => 'Наружная работа', 'priority' => 5870],
        ];
        App\Category::insert($categories);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
}
