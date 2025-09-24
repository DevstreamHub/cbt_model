<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultsTable extends Migration
{
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('course_registration_id');
            $table->integer('ca_score')->default(0);
            $table->integer('exam_score')->default(0);
            $table->integer('total')->default(0); // ca_score + exam_score
            $table->enum('status', ['submitted', 'pending'])->default('submitted');
            $table->timestamps();

            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('course_registration_id')->references('id')->on('course_registrations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('results');
    }
}
