<?php

namespace App\Http\Controllers;

use App\Services\JudgeService;
use App\Services\UserPaperService;
use Illuminate\Http\Request;

class UserPaperController extends Controller
{
    //
    private $userPaperService;
    private $judgeService;

    public function __construct(UserPaperService $userPaperService, JudgeService $judgeService)
    {
        $this->userPaperService = $userPaperService;
        $this->judgeService = $judgeService;
        $this->middleware('token');
    }

    public function save(Request $request)
    {
        $paper_id = $request->input('paper_id', null);
        if ($paper_id == null) {
            return response()->json([
                'code' => 1001,
                'message' => '缺少试卷id'
            ]);
        }
        $user = $request->user;
        $user_paper_problem = $request->input('user_paper_problem', null);
        $data = [];
        foreach ($user_paper_problem as $problem) {
            array_push($data, [
                'user_id' => $user->id,
                'paper_id' => $paper_id,
                'problem_id' => $problem['id'],
                'user_answer' => isset($problem['user_answer']) ? $problem['user_answer'] : null,
                'answer_info' => isset($problem['answer_info']) ? json_encode($problem['answer_info']) : json_encode([
                    'history' => null
                ]),
                'answer_cost' => isset($problem['answer_cost']) ? $problem['answer_cost'] : 0,
            ]);
        }
        $this->userPaperService->saveUserProblem($user->id, $paper_id, $data);
        return response()->json([
            'code' => 1000,
            'message' => '保存成功'
        ]);

    }

    public function submit(Request $request)
    {
        $paper_id = $request->input('paper_id', null);
        if ($paper_id == null) {
            return response()->json([
                'code' => 1001,
                'message' => '缺少试卷id'
            ]);
        }
        $user = $request->user;
        $user_paper_problem = $request->input('user_paper_problem', null);
        $data = [];
        foreach ($user_paper_problem as $problem) {
            array_push($data, [
                'user_id' => $user->id,
                'paper_id' => $paper_id,
                'problem_id' => $problem['id'],
                'user_answer' => isset($problem['user_answer']) ? $problem['user_answer'] : null,
                'answer_info' => isset($problem['answer_info']) ? json_encode($problem['answer_info']) : json_encode([
                    'history' => null
                ]),
                'answer_cost' => isset($problem['answer_cost']) ? $problem['answer_cost'] : 0,
            ]);
        }
        // 提交
        $this->userPaperService->saveUserProblem($user->id, $paper_id, $data);
        // 判题
        $res = $this->judgeService->judge($user->id, $paper_id);
        // 卷子状态修改为 finish
        $this->userPaperService->finishDoing($user->id, $paper_id);
        return response()->json([
            'code' => 1000,
            'message' => '提交成功',
            'data' => $res
        ]);
    }

    public function getPaperSaveStatus(Request $request)
    {
        $paper_id = $request->input('paper_id', -1);
        if ($paper_id == -1) {
            return response()->json([
                'code' => 1001,
                'message' => '缺少paper_id'
            ]);
        }
        $status = $this->userPaperService->getUserPaperDoStatus($request->user->id, $paper_id);
        return response()->json([
            'code' => 1000,
            'message' => '查询成功',
            'data' => $status
        ]);
    }

    public function getUserPaperRes(Request $request)
    {
        $paper_id = $request->input('paper_id', -1);
        if ($paper_id == -1) {
            return response()->json([
                'code' => 1001,
                'message' => '缺少paper_id'
            ]);
        }
        $res = $this->userPaperService->getPaperResult($request->user->id, $paper_id);
        return response()->json([
            'code' => 1000,
            'message' => '查询成功',
            'data' => $res
        ]);
    }
}
