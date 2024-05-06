<?php

use App\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->smallIncrements('id')->unsigned();
            $table->string('name', 64)->default(Role::getName(Role::User))->unique();
            $table->unique(['id', 'name']);
        });

        Role::getAllRoles()->each(function ($roleId): void {
            Role::create([
                'id' => $roleId,
                'name' => Role::getName($roleId),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
}
