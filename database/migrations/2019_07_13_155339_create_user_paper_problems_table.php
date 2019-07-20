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
            $table->jsonb('answer_info')->nullable();
            $table->bigInteger('answer_cost')->default(0);
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
