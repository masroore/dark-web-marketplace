<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopOrdersGoodsCount extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            $table->integer('buy_count')->default(0)->after('qiwi_count');
        });

        Schema::table('goods', function (Blueprint $table): void {
            $table->integer('buy_count')->default(0)->after('priority');
        });

        $shop = null;

        try {
            $shop = App\Shop::getDefaultShop();
        } catch (Exception $e) {
        }

        if (!$shop) {
            return;
        }
        $orders = $shop->orders()->where('status', App\Order::STATUS_FINISHED)->get();
        $shop->buy_count = $orders->count();
        $shop->save();

        $goodsMap = [];
        foreach ($orders as $order) {
            if (!isset($goodsMap[$order->good_id])) {
                $goodsMap[$order->good_id] = 0;
            }
            ++$goodsMap[$order->good_id];
        }

        foreach ($goodsMap as $goodId => $count) {
            $good = App\Good::find($goodId);
            if (!$good) {
                continue;
            }

            $good->buy_count = $count;
            $good->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table): void {

        });
    }
}
