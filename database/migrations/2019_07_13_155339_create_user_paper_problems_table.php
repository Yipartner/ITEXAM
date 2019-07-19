<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPaperProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 提交记录表
        Schema::create('user_paper_problems', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('paper_id');
            $table->integer('problem_id');
            $table->string('user_answer')->nullable();
            $table->string('user_answer_history')->nullable();
            $table->string('user_answer_time')->nullable();
            $table->string('judge_result')->nullable();
            $table->dateTime('judge_time')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_paper_problems');
    }
}
