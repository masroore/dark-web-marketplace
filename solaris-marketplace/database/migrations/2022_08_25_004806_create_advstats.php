<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvstats extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('adv_stats', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('title');
            $table->integer('views')->default(0);
            $table->integer('uniques')->default(0);
            $table->integer('registrations')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adv_stats');
    }
}
