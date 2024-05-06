<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
         * common_seller_question   Общие вопросы от продавцов.
         * common_buyer_question    Общие вопросы от покупателей.
         * app_for_opening          Заявки на открытие магазина.
         * cooperation              Сотрудничество.
         */
        $ticket_categories = ['common_seller_question', 'common_buyer_question', 'app_for_opening', 'cooperation'];

        Schema::create('tickets', function (Blueprint $table) use ($ticket_categories): void {
            $table->increments('id');
            $table->string('title', 128);
            $table->enum('category', $ticket_categories)->nullable(false)->default($ticket_categories[0])->index();
            $table->integer('user_id')->nullable(false)->default(0)->unsigned()->index();
            $table->boolean('closed')->nullable(false)->default(false)->index();
            $table->timestamps();

            $table->index(['closed', 'category']);
            $table->index(['user_id', 'closed']);
            $table->index(['user_id', 'closed', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
}
