<?php
/**
 * Created by PhpStorm.
 * User: hanxiao
 * Date: 19/7/10
 * Time: 下午8:15
 */

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Token
{

    public function handle(Request $request, Closure $next)
    {
        $request->user = new DefaultUser();
        return $next($request);
        if (empty($request->header('token'))) {
            return response()->json([
                'code' => 1002,
                'message' => '缺少token'
            ]);
        }
        $token = $request->header('token');
        $user = $this->getUserByToken($token);

        if ($user == null) {
            return response()->json([
                'code' => 1003,
                'message' => 'token不存在'
            ]);
        }
        $time = Carbon::now()->timestamp;
        if ($time > $user->token_created_at + 3600) {
            return response()->json([
                'code' => 1004,
                'message' => 'token 过期'
            ]);
        }
        $request->user = $user;

        return $next($request);
    }

    private function getUserByToken(string $token)
    {
        $user = DB::table('users')
            ->where('token', $token)
            ->first();
        return $user;
    }
}

class DefaultUser{
    public $id = 1;
    public $name = '测试用户';
    public $card_num = '20160000';
    public $role = 'student';
}