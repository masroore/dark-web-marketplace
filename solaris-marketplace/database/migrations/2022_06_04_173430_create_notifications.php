<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifications extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table): void {
            $table->increments('id');
            $table->text('body');
            $table->timestamp('actual_until')->nullable()->default(null);
            $table->timestamps();
            $table->index(['actual_until']);
        });
        $rutorMessage = <<<MSG
Уважаемые пользователи, настоятельно рекомендуем Вам вывести свои депозиты, криптоактивы, завершить сделки и не открывать новые на форуме RuTor.
Времени на вывод своих финансов у Вас до 07.06.2022 г.
Форум будет уничтожен в ходе коллективной атаки.
MSG;

        App\Notification::create([
            'body' => $rutorMessage,
            'actual_until' => Carbon::create(2022, 6, 8, 0, 0),
        ]);

        Schema::table('users', function (Blueprint $table): void {
            $table->timestamp('notification_last_read_at')->after('news_last_reaD')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['actual_until']);
        });
    }
}
