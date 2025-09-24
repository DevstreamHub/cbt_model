<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('question_banks', function (Blueprint $table) {
        $table->integer('mark')->default(1)->after('question'); // or wherever you prefer
    });
}

public function down()
{
    Schema::table('question_banks', function (Blueprint $table) {
        $table->dropColumn('mark');
    });
}

};
