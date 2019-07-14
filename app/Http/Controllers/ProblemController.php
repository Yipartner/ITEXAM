<?php

namespace App\Http\Controllers;

use App\Services\ProblemService;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;

class ProblemController extends Controller
{

    private $problemService;
    private $rules = [
        'type' => 'required',
        'option_num' => 'required',
        'subject' => 'required',
        'content.problem' => 'required',
        'answer' => 'required',
        'knowledge' => 'required',
        'status' => ''
    ];

    /**
     * ProblemController constructor.
     */
    public function __construct(ProblemService $problemService)
    {
        $this->problemService = $problemService;
    }


    public function createProblem(Request $request)
    {
        $this->checkUserPermission($request);
        $rules = $this->rules;
        $validator = ValidationHelper::validateCheck($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'code' => '1001',
                'message' => $validator->errors()
            ]);
        }
        $probInfo = ValidationHelper::getInputData($request, $rules);
        $probInfo['content'] = json_encode($probInfo['content']);

        $this->problemService->addProblem($probInfo);
        return response()->json([
            'code' => '1000',
            'message' => '创建题目成功'
        ]);
    }

    public function updateProblem($probId, Request $request)
    {
        $rules = $this->rules;
        $validator = ValidationHelper::validateCheck($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'code' => '1001',
                'message' => $validator->errors()
            ]);
        }
        $probInfo = ValidationHelper::getInputData($request, $rules);
        $probInfo['content'] = json_encode($probInfo['content']);

        $this->problemService->updateProblem($probId, $probInfo);
        return response()->json([
            'code' => '1000',
            'message' => '更新题目成功'
        ]);
    }

    public function getProblemList(Request $request)
    {
        $pageSize = $request->pageSize ?? 15;
        $problems = $this->problemService->getAll($pageSize);
        return response()->json([
            'code' => '1000',
            'message' => '查询成功',
            'problems' => $problems
        ]);
    }

    public function getProblemsBySubject($subject, Request $request)
    {
        $pageSize = $request->pageSize ?? 15;
        $problems = $this->problemService->getBySubject($subject, $pageSize);
        return response()->json([
            'code' => '1000',
            'message' => '查询成功',
            'problems' => $problems
        ]);
    }

    public function searchProblem(Request $request)
    {
        $probId = $request->id;
        $type = $request->type;
        $subject = $request->subject;
        $knowledge = $request->knowledge;
        $pageSize = $request->pageSize ?? 15;

        $problems = $this->problemService->searchProblems($probId, $type, $subject, $knowledge, $pageSize);
        return response()->json([
            'code' => '1000',
            'message' => '查询成功',
            'problems' => $problems
        ]);
    }

    public function deleteProblem($probId, Request $request)
    {
        $this->problemService->deleteProblem($probId);
        return response()->json([
            'code' => '1000',
            'message' => '删除题目成功'
        ]);
    }

    private function checkUserPermission(Request $request)
    {
        if (!in_array($request->user->role, ['teacher', 'admin'])) {
            return response()->json([
                'code' => '1010',
                'message' => '无操作权限'
            ]);
        }
    }
}
