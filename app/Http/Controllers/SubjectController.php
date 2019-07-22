<?php

namespace App\Http\Controllers;

use App\Services\SubjectService;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    private $subjectService;
    private $rules = [
        'name' => 'required',
    ];

    public function __construct(SubjectService $subjectService)
    {
        $this->subjectService = $subjectService;
        $this->middleware(['token', 'teacher'])->except(['getSubjectById', 'getSubjectList']);
    }


    public function createSubject(Request $request)
    {
        $rules = $this->rules;
        $validator = ValidationHelper::validateCheck($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'code' => '1001',
                'message' => $validator->errors()
            ]);
        }
        $subInfo = ValidationHelper::getInputData($request, $rules);

        $this->subjectService->addSubject($subInfo);
        return response()->json([
            'code' => '1000',
            'message' => '创建学科成功'
        ]);
    }

    public function updateSubject($subId, Request $request)
    {
        $rules = $this->rules;
        $validator = ValidationHelper::validateCheck($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'code' => '1001',
                'message' => $validator->errors()
            ]);
        }
        $subInfo = ValidationHelper::getInputData($request, $rules);

        $this->subjectService->updateSubject($subId, $subInfo);
        return response()->json([
            'code' => '1000',
            'message' => '更新学科成功'
        ]);
    }


    public function getSubjectById($id, Request $request)
    {

        $subject = $this->subjectService->getOneSubject($id);
        return response()->json([
            'code' => '1000',
            'message' => '查询成功',
            'subject' => $subject
        ]);
    }


    public function getSubjectList(Request $request)
    {
        $pageSize = intval($request->pageSize) > 0 ? intval($request->pageSize) : 15;
        $subjects = $this->subjectService->getAllSubjects($pageSize);
        return response()->json([
            'code' => '1000',
            'message' => '查询成功',
            'subjects' => $subjects
        ]);
    }

    public function deleteSubject($subId, Request $request)
    {
        $res = $this->subjectService->deleteSubject($subId);
        if (!$res) {
            return response()->json([
                'code' => '1002',
                'message' => '删除学科失败，请检查学科id是否为0'
            ]);
        }
        return response()->json([
            'code' => '1000',
            'message' => '删除学科成功'
        ]);
    }
}
