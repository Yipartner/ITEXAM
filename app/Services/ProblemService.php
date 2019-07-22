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
            ->select(self::$tbName . '.*', DB::Raw("IFNULL(subjects.name,'未分类') as subject_name"))
            ->leftJoin('subjects', 'subjects.id', '=', self::$tbName . '.subject')
            ->orderBy('id', 'asc')
            ->paginate($pageSize);

        $problems = self::resolveProblems($problems);
        return $problems;
    }

    public function getOne($probId)
    {
        $problem = DB::table(self::$tbName)
            ->select(self::$tbName . '.*', DB::Raw("IFNULL(subjects.name,'未分类') as subject_name"))
            ->leftJoin('subjects', 'subjects.id', '=', self::$tbName . '.subject')
            ->where(self::$tbName . '.id', $probId)
            ->first();

        $problem = self::resolveProblem($problem);
        return $problem;
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
            ->select(self::$tbName . '.*', DB::Raw("IFNULL(subjects.name,'未分类') as subject_name"))
            ->leftJoin('subjects', 'subjects.id', '=', self::$tbName . '.subject')
            ->where('subject', $subject)
            ->orderBy('id', 'asc')
            ->paginate($pageSize);

        $problems = self::resolveProblems($problems);
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
            ->select(self::$tbName . '.*', "IFNULL('subjects.name', '未分类')")
            ->leftJoin('subjects', 'subjects.id', '=', self::$tbName . '.subject')
            ->whereIn(self::$tbName . '.id', $probIds)
            ->orderBy('id', 'asc')
            ->get();

        $problems = self::resolveProblems($problems);
        return $problems;
    }

    public function getByCondition(array $condition)
    {
        $problems = DB::table(self::$tbName)
            ->where($condition)
            ->get();

        $problems = self::resolveProblems($problems);
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
        if (isset($problem->content)) {
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
            if (isset($problem->content)) {
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
        if (!empty($probId)) {
            $condition[] = [self::$tbName . '.id', '=', $probId];
        }
        if ($type > 0) {
            $condition[] = ['type', '=', $type];

        }
        if ($subject > 0) {
            $condition[] = ['subject', '=', $subject];

        }
        if (!empty($knowledge)) {
            $condition[] = ['knowledge', 'like', '%' . $knowledge . '%'];

        }

        $problems = DB::table(self::$tbName)
            ->select(self::$tbName . '.*', DB::Raw("IFNULL(subjects.name,'未分类') as subject_name"))
            ->leftJoin('subjects', 'subjects.id', '=', self::$tbName . '.subject')
            ->where($condition)
            ->orderBy('id', 'asc')
            ->paginate($pageSize);

        $problems = self::resolveProblems($problems);
        return $problems;
    }
}