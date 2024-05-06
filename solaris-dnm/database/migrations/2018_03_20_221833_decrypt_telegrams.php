<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DecryptTelegrams extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (App\User::all() as $user) {
            if (!empty($user->contacts_telegram)) {
                $user->contacts_telegram = decrypt($user->contacts_telegram);
                $user->save();
            }
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->index('contacts_telegram');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
}
