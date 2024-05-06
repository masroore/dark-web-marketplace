<?php

use Illuminate\Database\Migrations\Migration;

class CategoriesRenameItem extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $category = App\Category::findOrFail(15);
        $category->title = '*-NBOMe';
        $category->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $category = App\Category::findOrFail(15);
        $category->title = 'Нбомы';
        $category->save();
    }
}
