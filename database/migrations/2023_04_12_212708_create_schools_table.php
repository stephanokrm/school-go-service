<?php

use App\Models\Address;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('morning')->default(false);
            $table->boolean('afternoon')->default(false);
            $table->boolean('night')->default(false);
            $table->time('morning_entry_time')->nullable();
            $table->time('morning_departure_time')->nullable();
            $table->time('afternoon_entry_time')->nullable();
            $table->time('afternoon_departure_time')->nullable();
            $table->time('night_entry_time')->nullable();
            $table->time('night_departure_time')->nullable();
            $table->foreignIdFor(Address::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
