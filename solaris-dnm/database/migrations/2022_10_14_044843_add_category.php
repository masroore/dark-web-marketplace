<?php

use Doctrine\Common\Cache\Cache;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddCategory extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $min = DB::table('categories')->select(DB::raw('min(priority) as priority'))->first();

        $category = (new App\Category());
        $category->title = 'Официальные документы';
        $category->priority = $min->priority - 10;
        $category->save();

        \Cache::forget('categories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categories')->where('title', '=', 'Официальные документы')->delete();
        Cache::forget('categories');
    }
}
