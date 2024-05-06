<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsVoutRow extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            $table->tinyInteger('vout')->unsigned()->nullable()->default(null)->after('tx_id');
            $table->dropUnique(['tx_id', 'address']);
            $table->index(['tx_id', 'address', 'amount']); // временный индекс
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            $table->dropIndex(['tx_id', 'address', 'amount']);
            $table->dropColumn('vout');
            // $table->unique(['tx_id', 'address']); // скорее всего не сработает unique, т.к. tx_id уже дублируются
            $table->index(['tx_id', 'address']);     // предлагаю мутить уникальный индекс с колонками tx_id, address и vout. но vout сейчас может быть null :(
        });
    }
}
