<?php

use App\Models\Address;
use App\Models\Responsible;
use App\Models\School;
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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->boolean('goes')->default(false);
            $table->boolean('return')->default(false);
            $table->boolean('morning')->default(false);
            $table->boolean('afternoon')->default(false);
            $table->boolean('night')->default(false);
            $table->foreignIdFor(Address::class)->constrained();
            $table->foreignIdFor(Responsible::class)->constrained();
            $table->foreignIdFor(School::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
