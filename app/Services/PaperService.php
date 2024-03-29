<?php
/**
 * Created by PhpStorm.
 * User: hanxiao
 * Date: 19/7/13
 * Time: 下午11:25
 */

namespace App\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaperService
{
    public static $tableName = 'papers';

    // 创建试卷记录
    public function createPaper($paper)
    {
        $time = Carbon::now();
        $paper['created_at'] =$time;
        $paper['updated_at'] =$time;
        $id = DB::table(self::$tableName)
            ->insertGetId($paper);
        return $id;
    }

    public function getPaperStatus($paper_id){
        $status = DB::table(self::$tableName)
            ->where('id',$paper_id)
            ->value('status');
        return $status;
    }

    // 更新试卷基本信息（试卷名称）
    public function updatePaperBaseInfo($paper_id, $paper_info)
    {
        DB::table(self::$tableName)
            ->where('id', $paper_id)
            ->update($paper_info);
    }

    // 删除试卷（软删除）
    public function deletePaperBaseInfo($paper_id)
    {
        DB::table(self::$tableName)
            ->where('id', $paper_id)
            ->update([
                'status' => 'delete'
            ]);
    }

    // 给试卷添加题目（初次创建试卷时使用）
    public function addProblemToPaper($paper_id, $problems)
    {
        $array = [];
        foreach ($problems as $problem) {
            array_push($array, [
                'paper_id' => $paper_id,
                'problem_id' => $problem
            ]);
        }
        DB::table('paper_problems')
            ->insert($array);
    }

    // 修改试卷题目
    public function updateProblemPaper($paper_id, $problems)
    {
        // 懒得加事务了，我感觉ok
        DB::table('paper_problems')
            ->where('paper_id', $paper_id)
            ->delete();
        $this->addProblemToPaper($paper_id, $problems);
    }

    // 获取试卷信息
    public function getPaperInfo($paper_id)
    {
        $info = DB::table(self::$tableName)
            ->where('id', $paper_id)
            ->first();
        $problems = DB::table('paper_problems')
            ->where('paper_problems.paper_id', $paper_id)
            ->join('problems','paper_problems.problem_id','=','problems.id')
            ->select('problems.id','problems.type','problems.option_num','problems.subject','content','knowledge','status')
            ->get();
        foreach ($problems as $problem){
            if (isset($problem->content)) {
                $problem->content = json_decode($problem->content);
            }
        }
        return [
            'info' => $info,
            'problems' => $problems
        ];
    }

    public function changePaperStatus($paper_id,$status){
        DB::table(self::$tableName)
            ->where('id',$paper_id)
            ->update([
                'status'=>$status
            ]);
    }

    public function getPaperListByUser($user_id,$page_size){
        $papers = DB::table('user_papers')
            ->where('user_papers.user_id','=',$user_id)
            ->join(self::$tableName,self::$tableName.".id",'=','user_papers.paper_id')
            ->select(self::$tableName.".*")
            ->paginate($page_size);
        return $papers;
    }


}