<?php
/**
 * Created by PhpStorm.
 * User: hanxiao
 * Date: 19/7/14
 * Time: 下午2:28
 */

namespace App\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserPaperService
{
    public static $tableName = 'user_papers';

    public function addUsersToPaper($paper_id,$users){
        $old_users = DB::table(self::$tableName)
            ->where('paper_id',$paper_id)
            ->pluck('user_id')->toArray();
        $need_insert = array_diff($users,$old_users);
        $data = [];
        $time = Carbon::now();
        foreach ($need_insert as $user){
            array_push($data,[
                'user_id'=>$user,
                'paper_id' => $paper_id,
                'created_at'=>$time,
                'updated_at'=>$time
            ]);
        }
        DB::table(self::$tableName)
            ->insert($data);
    }

    public function getPaperUser($paper_id){
        $user_ids = DB::table(self::$tableName)
            ->where('paper_id',$paper_id)
            ->pluck('user_id');
        $user_count =  DB::table(self::$tableName)
            ->where('paper_id',$paper_id)
            ->count();
        $users = DB::table('users')
            ->whereIn('users.id',$user_ids)
            ->join(self::$tableName,self::$tableName.".user_id",'=',"users.id")
            ->select('users.id','users.name','users.sex','users.card_num','users.role',self::$tableName.".status as paper_status" )
            ->get();
        return [
            'count'=>$user_count,
            'users'=>$users
        ];
    }

    public function deletePaperUser($paper_id,$users){
        DB::table(self::$tableName)
            ->where('paper_id' ,$paper_id)
            ->whereIn('user_id',$users)
            ->delete();
    }

    public function saveUserProblem($user,$paper,$data){
        DB::table('user_paper_problems')
            ->where([
                ['user_id','=',$user],
                ['paper_id','=',$paper]
            ])->delete();
        DB::table('user_paper_problems')
            ->insert($data);
    }

    public function finishDoing($user_id,$paper_id){
        DB::table(self::$tableName)
            ->where('user_id',$user_id)
            ->where('paper_id',$paper_id)
            ->update([
                'status' => 'finish'
            ]);
    }

    // 获取用户试卷做题情况
    public function getUserPaperDoStatus($user,$paper_id){
        $status = DB::table('user_paper_problems')
            ->where([
                ['user_id','=',$user],
                ['paper_id','=',$paper_id]
            ])->get();
        $data = [];
        foreach ($status as $solution){
            $data[$solution->problem_id]=$solution;
            unset($data[$solution->id]->problem_id);
        }
        return $data;
    }
    // 获取试卷正确错误数量
    public function getPaperResult($user,$paper_id){
        $res = DB::table('user_paper_problems')
            ->where([
                ['user_id','=',$user],
                ['paper_id','=',$paper_id]
            ])->groupby('judge_result')
            ->select(DB::raw('judge_result,count(*) as count'))
            ->get();
        return $res;
    }
}