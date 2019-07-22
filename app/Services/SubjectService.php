<?php
/**
 * Created by PhpStorm.
 * User: imyhui
 * Date: 2019/7/23
 * Time: 上午12:46
 */

namespace App\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubjectService
{
    public static $tbName = 'subjects';

    public function addSubject($subInfo)
    {
        $subInfo = array_merge($subInfo, [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table(self::$tbName)
            ->insert($subInfo);
    }

    public function updateSubject($subId, $subInfo)
    {
        $subInfo = array_merge($subInfo, [
            'updated_at' => Carbon::now()
        ]);
        DB::table(self::$tbName)
            ->where('id', $subId)
            ->update($subInfo);
    }

    public function deleteSubject($subId)
    {
        if ($subId <= 0)
            return false;
        $flag = false;
        DB::transaction(function () use ($subId, &$flag) {
            DB::table('problems')->where('subject', $subId)
                ->update([
                    'subject' => 0,
                    'updated_at' => Carbon::now()
                ]);
            DB::table(self::$tbName)
                ->where('id', $subId)
                ->delete();
            $flag = true;
        });
        return $flag;
    }


    public function getAllSubjects($pageSize = 15)
    {
        $subjects = DB::table(self::$tbName)
            ->paginate($pageSize);

        return $subjects;
    }

    public function getOneSubject($subId)
    {
        $subject = DB::table(self::$tbName)
            ->where('id', $subId)
            ->first();

        return $subject;
    }
}