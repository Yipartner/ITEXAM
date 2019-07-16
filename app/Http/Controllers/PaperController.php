<?php

namespace App\Http\Controllers;

use App\Services\PaperService;
use App\Services\UserPaperService;
use Illuminate\Http\Request;

class PaperController extends Controller
{
    //
    private $paperService;
    private $userPaperService;

    public function __construct(PaperService $paperService,UserPaperService $userPaperService)
    {
        $this->paperService = $paperService;
        $this->userPaperService = $userPaperService;
        $this->middleware('token')->except(['getPaperInfo']);
        $this->middleware('teacher')->except(['getPaperInfo']);

    }

    public function createWhitePaper(Request $request)
    {
        $name = $request->input('paper_name',null);
        if ($name == null){
            return response()->json([
                'code' => 1001,
                'message' => '缺少试卷名称字段'
            ]);
        }
        $this->paperService->createPaper([
            'paper_name'=> $name,
            'status' => 'init'
        ]);
        return response()->json([
            'code' => 1000,
            'message' => '创建空白试卷成功'
        ]);
    }

    public function addProblem(Request $request){
        $paper = $request->input('paper_id',-1);
        if ($this->paperService->getPaperStatus($paper)!= 'init'){
            return response()->json([
                'code' => 3001,
                'message' => '当前时间的状态不允许修改'
            ]);
        }
        $problems = $request->input('problems',[]);
        if ($paper == -1){
            return response()->json([
                'code'=> 1001,
                'message' => '缺少试卷id'
            ]);
        }
        $this->paperService->updateProblemPaper($paper,$problems);
        return response()->json([
            'code'=> 1000,
            'message'=> '添加题目成功'
        ]);
    }

    public function updateProblem(Request $request){
        $paper = $request->input('paper_id',-1);
        if ($paper == -1){
            return response()->json([
                'code'=> 1001,
                'message' => '缺少试卷id'
            ]);
        }
        if ($this->paperService->getPaperStatus($paper)!= 'init'){
            return response()->json([
                'code' => 3001,
                'message' => '当前时间的状态不允许修改'
            ]);
        }
        $problems = $request->input('problems',[]);

        $this->paperService->updateProblemPaper($paper,$problems);
        return response()->json([
            'code'=> 1000,
            'message'=> '修改试卷题目成功'
        ]);
    }

    public function finishPaper(Request $request){
        $paper = $request->input('paper_id',-1);
        $this->paperService->changePaperStatus($paper,'normal');
        return response()->json([
            'code' => 1000,
            'message'=> '试卷已锁定'
        ]);
    }

    public function updatePaperBaseInfo(Request $request){
        $paper = $request->input('paper_id',-1);
        $name = $request->input('paper_name',null);
        if ($name == null){
            return response()->json([
                'code' => 1001,
                'message' => '缺少试卷名称字段'
            ]);
        }
        if ($paper == -1){
            return response()->json([
                'code'=> 1001,
                'message' => '缺少试卷id'
            ]);
        }
        $this->paperService->updatePaperBaseInfo($paper,[
            'paper_name'=>$name
        ]);
        return response()->json([
            'code'=> 1000,
            'message'=> '试卷基础信息修改成功'
        ]);
    }

    public function getPaperInfo(Request $request){
        $paper = $request->input('paper_id');
        if ($paper == -1){
            return response()->json([
                'code'=> 1001,
                'message' => '缺少试卷id'
            ]);
        }
        $info = $this->paperService->getPaperInfo($paper);
        return response()->json([
            'code' => 1000,
            'message'=> $info
        ]);
    }

    public function deletePaper(Request $request){
        $paper = $request->input('paper_id',-1);
        if ($paper == -1){
            return response()->json([
                'code'=> 1001,
                'message' => '缺少试卷id'
            ]);
        }
        $this->paperService->deletePaperBaseInfo($paper);
        return response()->json([
            'code'=>1000,
            'message' => '删除成功'
        ]);
    }

    public function quickCreatePaper(Request $request){
        $total_num = $request->input('problem_num',10);
        // todo
    }

    public function addPaperUser(Request $request){
        $paper = $request->input('paper_id');
        $users = $request->input('users');
        $this->userPaperService->addUsersToPaper($paper,$users);
        return response()->json([
            'code'=> 1000,
            'message' => "添加成功"
        ]);
    }

    public function deletePaperUser(Request $request){
        $paper = $request->input('paper_id');
        $users = $request->input('users');
        $this->userPaperService->deletePaperUser($paper,$users);
        return response()->json([
            'code'=> 1000,
            'message' => "删除成功"
        ]);
    }
}
