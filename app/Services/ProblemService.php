<?php
/**
 * Created by PhpStorm.
 * User: imyhui
 * Date: 2019/7/13
 * Time: 下午11:37
 */

namespace App\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProblemService
{
    public static $tbName = 'problems';

    public function addProblem($probInfo)
    {
        $probInfo = array_merge($probInfo, [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table(self::$tbName)
            ->insertGetId($probInfo);
    }

    public function updateProblem($probId, $probInfo)
    {
        $probInfo = array_merge($probInfo, [
            'updated_at' => Carbon::now()
        ]);
        DB::table(self::$tbName)
            ->where('id', $probId)
            ->update($probInfo);
    }

    /**
     * 软删除
     * @param $probId
     */
    public function deleteProblem($probId)
    {
        DB::table(self::$tbName)
            ->where('id', $probId)
            ->update([
                'status' => 'delete'
            ]);
    }


    /**
     * 分页获取所有题目
     * @param int $pageSize
     * @return mixed
     */
    public function getAll($pageSize = 15)
    {
        $problems = DB::table(self::$tbName)
            ->paginate($pageSize)
            ->get();
        return $problems;
    }

    /**
     * 根据学科获取题目
     * @param $subject
     * @param int $pageSize
     * @return mixed
     */
    public function getBySubject($subject, $pageSize = 15)
    {
        $problems = DB::table(self::$tbName)
            ->where('subject', $subject)
            ->paginate($pageSize)
            ->get();
        return $problems;
    }


    /**
     * 根据Id 获取题目列表
     * @param $probIds
     * @return mixed
     */
    public function getProblems(array $probIds)
    {
        $problems = DB::table(self::$tbName)
            ->whereIn('id', $probIds)
            ->get();
        return $problems;
    }

    public function getByCondition(array $condition)
    {
        $problems = DB::table(self::$tbName)
            ->where($condition)
            ->get();
        return $problems;
    }

    public function countByCondition(array $condition)
    {
        $num = DB::table(self::$tbName)
            ->where($condition)
            ->count();
        return $num;
    }
}