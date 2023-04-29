<?php

use App\Models\Itinerary;
use App\Models\Student;
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
        Schema::create('itinerary_student', function (Blueprint $table) {
            $table->id();
            $table->timestamp('embarked_at')->nullable();
            $table->timestamp('disembarked_at')->nullable();
            $table->foreignIdFor(Itinerary::class)->constrained();
            $table->foreignIdFor(Student::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_student');
    }
};
