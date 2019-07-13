<?php
/**
 * Created by PhpStorm.
 * User: hanxiao
 * Date: 19/7/13
 * Time: 下午11:45
 */

namespace App\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JudgeService
{
    // 判题，返回正确与错误题号
    public function judge($user_id,$paper_id){
        // 获取试卷题目列表
        $problems = DB::table('paper_problems')
            ->where('paper_id',$paper_id)
            ->pluck('problem_id');
        // 获取正确答案数组
        $problem_answer = DB::table('problems')
            ->whereIn('id',$problems)
            ->select('id','answer')
            ->get()->toArray();
        // 获取用户作答情况
        $user_problem = DB::table('user_paper_problems')
            ->where('user_id',$user_id)
            ->where('paper_id',$paper_id)
            ->get()->toArray();
        $right = [];
        $wrong = [];
        // 判题
        foreach ($user_problem as $problem){
            if ($problem['user_answer'] == $problem_answer[$problem['problem_id']] || $problem_answer[$problem['problem_id']] == null){
                array_push($right,$problem['problem_id']);
            }else{
                array_push($wrong,$problem['problem_id']);
            }
        }
        $time = Carbon::now();
        // 写入判题结果
        DB::table('user_paper_problems')
            ->where('user_id',$user_id)
            ->whereIn('problem_id',$right)
            ->update([
                'judge_result'=>'right',
                'judge_time'=>$time
            ]);
        DB::table('user_paper_problems')
            ->where('user_id',$user_id)
            ->whereIn('problem_id',$wrong)
            ->update([
                'judge_result'=>'wrong',
                'judge_time'=>$time
            ]);
        return [
            'right'=>$right,
            'wrong'=>$wrong
        ];
    }

}