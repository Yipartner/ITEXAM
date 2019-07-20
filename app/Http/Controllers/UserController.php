<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('token')->only(['addSingleUser', 'deleteUsers']);
    }

    public function addSingleUser(Request $request)
    {
        if ($request->user->role != 'admin') {
            return response()->json([
                'code' => 101,
                'message' => '权限不足'
            ]);
        }
        $rule = [
            'name' => 'required',
            'sex' => 'required',
            'card_num' => 'required',
            'email' => '',
            'role' => 'required'
        ];
        $res = ValidationHelper::validateCheck($request->input(), $rule);
        if ($res->fails()) {
            return response()->json([
                'code' => 1001,
                'message' => $res->errors(),
            ]);
        }
        $data = ValidationHelper::getInputData($request, $rule);
        if ($this->userService->isUserExist($data['card_num'])){
            return response()->json([
                'code'=>1008,
                'message'=>"用户已存在"
            ]);
        }
        $this->userService->addSingleUser($data);
        return response()->json([
            'code' => 1000,
            'message' => "用户添加成功"
        ]);

    }

    public function login(Request $request)
    {
        $rule = [
            'card_num' => 'required',
            'password' => 'required'
        ];
        $res = ValidationHelper::validateCheck($request->input(), $rule);
        if ($res->fails()) {
            return response()->json([
                'code' => 1001,
                'message' => $res->errors(),
            ]);
        }
        $data = ValidationHelper::getInputData($request, $rule);
        if (!$this->userService->checkPasswordForCardNum($data['card_num'], $data['password'])) {
            return response()->json([
                'code' => 1005,
                'message' => '密码错误'
            ]);
        }
        $tokenAndUser = $this->userService->generateToken($data['card_num']);
        return response()->json([
            'code' => 1000,
            'data' => $tokenAndUser
        ]);

    }

    public function getUser(Request $request)
    {
        $condition = $request->input('condition', 'no');
        $conditionValue = $request->input('condition_value', null);
        if (!in_array($condition, ['id', 'card_num'])) {
            return response()->json([
                'code' => 1006,
                'message' => '查询条件错误或缺少查询条件'
            ]);
        }
        if ($conditionValue == null) {
            return response()->json([
                'code' => 1001,
                'message' => '缺少condition值'
            ]);
        }
        if ($condition == 'card_num') {
            $user = $this->userService->getUserByCardNum($conditionValue);
        } else {
            $user = $this->userService->getUserById($conditionValue);
        }
        if (!$user) {
            return response()->json([
                'code' => 1007,
                'message' => '用户不存在'
            ]);
        }

        return response()->json([
            'code' => 1000,
            'data' => $user
        ]);

    }

    public function deleteUsers(Request $request)
    {
        if ($request->user->role != 'admin') {
            return response()->json([
                'code' => 101,
                'message' => '权限不足'
            ]);
        }
        $ids = $request->input('ids', []);
        $this->userService->deleteUsers($ids);
        return response()->json([
            'code' => 1000,
            'message' => '删除成功'
        ]);
    }

    public function import(Request $request)
    {
        $file = $request->file('users');
        $path = $file->store('users');
        //TODO
    }

    public function getUsers(Request $request){
        $role = $request->input('role',null);
        $users = $this->userService->getAllUser($role);
        return response()->json([
            'code'=>1000,
            'data'=>$users,
            'message'=>'查询用户列表成功'
        ]);
    }
}
