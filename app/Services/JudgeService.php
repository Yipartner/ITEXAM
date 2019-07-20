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
        $answer = [];
        foreach ($problem_answer as $problem){
            $answer[$problem->id] = $problem->answer;
        }
        // 获取用户作答情况
        $user_problem = DB::table('user_paper_problems')
            ->where('user_id',$user_id)
            ->where('paper_id',$paper_id)
            ->get()->toArray();
        $right = [];
        $wrong = [];
        $res = [];
        // 判题
//        dd($user_problem);
        foreach ($user_problem as $problem){
//            dd($problem,$answer);
            //如果答案相同，或者answer答案为空（阅读题答案为空，默认正确）
            if ($problem->user_answer == $answer[$problem->problem_id]
                || $answer[$problem->problem_id] == null){
                array_push($right,$problem->problem_id);
                array_push($res,[
                    'id'=>$problem->problem_id,
                    'user_answer'=>$problem->user_answer,
                    'res' => 'right',
                ]);
            }else{
                array_push($wrong,$problem->problem_id);
                array_push($res,[
                    'id'=>$problem->problem_id,
                    'user_answer'=>$problem->user_answer,
                    'res' => 'wrong',
                ]);
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
        return $res;
    }

}