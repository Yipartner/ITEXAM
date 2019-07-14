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

class TeacherPermission
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user->role == 'teacher') {
            return $next($request);
        } else {
            return response()->json([
                'code' => 101,
                'message' => '权限不足'
            ]);
        }
    }
}