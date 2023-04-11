<?php

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->enum('role', [RoleEnum::Administrator->value, RoleEnum::Driver->value, RoleEnum::Responsible->value])->unique();
            $table->timestamps();
        });

        Role::query()->create(['role' => RoleEnum::Administrator->value]);
        Role::query()->create(['role' => RoleEnum::Driver->value]);
        Role::query()->create(['role' => RoleEnum::Responsible->value]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
