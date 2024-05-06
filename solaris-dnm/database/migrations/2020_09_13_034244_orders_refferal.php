<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrdersRefferal extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->double('referrer_fee')->nullable()->default(null)->after('comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
}
