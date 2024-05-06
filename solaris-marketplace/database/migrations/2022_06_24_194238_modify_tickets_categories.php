<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ModifyTicketsCategories extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $values = [
            "'common_seller_question'",
            "'common_buyer_question'",
            "'app_for_opening'",
            "'cooperation'",
            "'security_service'",
        ];

        $this->migrate($values);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $values = [
            "'common_seller_question'",
            "'common_buyer_question'",
            "'app_for_opening'",
            "'cooperation'",
        ];

        // значение security_service можно не сбрасывать,т.к. security_service будет заменен на пустое значение.
        // в lang файле на этот случай предусмотрен ключ 'Category ' => 'Без категории',
        $this->migrate($values);
    }

    private function migrate($values): void
    {
        DB::statement('ALTER TABLE `tickets` CHANGE `category` `category` ENUM(' . implode(', ', $values) . ") NOT NULL DEFAULT 'common_seller_question'");
    }
}
