<?php
/**
 * Created by PhpStorm.
 * User: hanxiao
 * Date: 19/7/14
 * Time: 下午2:28
 */

namespace App\Services;


use Illuminate\Support\Facades\DB;

class UserPaperService
{
    public static $tableName = 'user_papers';

    public function addUsersToPaper($paper_id,$users){
        $old_users = $this->getPaperUser($paper_id);
        $need_insert = array_diff($users,$old_users);
        $data = [];
        foreach ($need_insert as $user){
            array_push($data,[
                'user_id'=>$user,
                'paper_id' => $paper_id,
            ]);
        }
        DB::table(self::$tableName)
            ->insert($data);
    }

    public function getPaperUser($paper_id){
        $users = DB::table(self::$tableName)
            ->where('paper_id',$paper_id)
            ->pluck('user_id');
        return $users;
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
}