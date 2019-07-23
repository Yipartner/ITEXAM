<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problems', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->comment('题目类型 1.单选 2.多选 3.阅读');
            $table->tinyInteger('option_num')->comment('选项个数 0,1,n');
            $table->integer('subject')->default(0)->comment('学科，对应学科表,0 为未分类');
            $table->jsonb('content')->comment('题目/选项内容');
            /**
            {
                "problem":"problem string",
                "options":{
                "A":"A balabala",
                "B":"B balabala",
                ...
                }
            }
             */
            $table->string('answer')->nullable()->comment('答案，多选如 A,B, 阅读题为 null');
            $table->string('knowledge')->nullable()->comment('知识点');
            $table->string('status')->default('normal')->comment('题目状态 ：init ,normal,delete');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('problems');
    }
}
