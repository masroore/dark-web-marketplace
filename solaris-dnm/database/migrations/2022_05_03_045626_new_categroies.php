<?php

use Illuminate\Database\Migrations\Migration;

class NewCategroies extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $categories = [['parent_id' => 1, 'title' => 'A-PVP', 'priority' => 6360]];
        App\Category::insert($categories);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        App\Category::whereIn('title', ['A-PVP'])->delete();
    }
}
