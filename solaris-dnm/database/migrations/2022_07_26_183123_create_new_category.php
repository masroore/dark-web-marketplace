<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CreateNewCategory extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $min = DB::table('categories')->select(DB::raw('min(priority) as priority'))->first();

        $category = (new App\Category());
        $category->title = 'Обнал';
        $category->priority = $min->priority - 10;
        $category->save();

        $subcategory = (new App\Category());
        $subcategory->parent_id = $category->id;
        $subcategory->title = 'Обнал BTC';
        $subcategory->priority = $min->priority - 20;
        $subcategory->save();

        Cache::forget('categories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categories')->whereIn('title', ['Обнал', 'Обнал BTC'])->delete();
        Cache::forget('categories');
    }
}
