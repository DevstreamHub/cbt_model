<?php

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

            $table->string('matric_no')->unique();         // Required
            $table->string('surname');                     // Required
            $table->string('other_names');                 // Required
            $table->string('email')->nullable();           // Optional
            $table->foreignId('department_id')->constrained()->cascadeOnDelete(); // Required, FK
            $table->foreignId('level_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('application_id')->nullable(); // Optional
            $table->string('passport')->nullable();                   // Optional

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
