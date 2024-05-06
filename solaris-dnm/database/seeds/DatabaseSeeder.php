<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call(CitiesTableSeeder::class);
        // $this->call(CategoriesTableSeeder::class);
        $this->call(RegionTableSeeder::class);
    }
}
