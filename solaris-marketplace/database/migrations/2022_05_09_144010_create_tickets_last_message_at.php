<?php

use App\Models\Tickets\Message;
use App\Models\Tickets\Ticket;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsLastMessageAt extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table): void {
            $table->timestamp('last_message_at')->after('updated_at')->index();
        });

        // обновляем время последних сообщений в тикетах
        Ticket::get()->each(function ($t): void {
            if ($last_message = Message::where('ticket_id', '=', $t->id)->orderBy('created_at', 'DESC')->first()) {
                $t->last_message_at = $last_message->created_at;
            } else {
                $t->last_message_at = Carbon\Carbon::now();
            }

            $t->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table): void {
            $table->dropColumn('last_message_at');
        });
    }
}
