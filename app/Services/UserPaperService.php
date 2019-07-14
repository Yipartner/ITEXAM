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
        DB::table(self::$tableName)
            ->insert();
    }
}