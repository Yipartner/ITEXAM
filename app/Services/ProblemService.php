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
            ->insert($probInfo);
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
            ->paginate($pageSize);
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
            ->paginate($pageSize);
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

    /**
     * 题目content 字符串 转为 json
     * @param $problem
     * @return mixed
     */
    public static function resolveProblem($problem)
    {
        if ($problem->content) {
            $problem->content = json_decode($problem->content);
        }
        return $problem;
    }

    /**
     * 题目content 字符串 转为 json
     * @param $problem
     * @return mixed
     */
    public static function resolveProblems($problems)
    {
        foreach ($problems as $problem) {
            if ($problem->content) {
                $problem->content = json_decode($problem->content);
            }
        }
        return $problems;
    }

    /**
     * 条件查询题目
     * @param $probId
     * @param $type
     * @param $subject
     * @param $knowledge
     * @param int $pageSize
     * @return mixed
     */
    public function searchProblems($probId, $type, $subject, $knowledge, $pageSize = 15)
    {
        $condition = [];
        if (isset($probId)) {
            $condition[] = ['id', '=', $probId];
        }
        if (isset($type)) {
            $condition[] = ['type', '=', $type];

        }
        if (isset($subject)) {
            $condition[] = ['subject', '=', $subject];

        }
        if (isset($knowledge)) {
            $condition[] = ['knowledge', 'like', '%' . $knowledge . '%'];

        }

        $problems = DB::table(self::$tbName)
            ->where($condition)
            ->paginate($pageSize);
        return $problems;
    }
}