<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserService
{
    public static $tableName = 'users';

    public function addSingleUser(array $userInfo)
    {
        $userInfo = array_merge($userInfo, [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table(self::$tableName)
            ->insert($userInfo);
    }

    public function addUsers(array $users)
    {
        DB::table(self::$tableName)
            ->insert($users);
    }

    public function isUserExist(string $card)
    {
        return boolval(DB::table(self::$tableName)->where('card_num', $card)->first());
    }

    /**
     * @param $oldMd5Password
     * @param $newPassword
     * @param $userId
     * @return bool true:修改成功 false:user_id与password 组合不正确
     */
    public function changePassword(string $oldMd5Password, string $newPassword, int $userId)
    {
        $row = DB::table(self::$tableName)
            ->where([
                ['id', '=', $userId],
                ['password', '=', $oldMd5Password]
            ])->update([
                'password', md5($newPassword)
            ]);
        return $row > 0;
    }

    public function updateUser($user_id, $user_info)
    {
        $user_info = array_merge($user_info, ['updated_at', Carbon::now()]);
        DB::table(self::$tableName)
            ->where('id', $user_id)
            ->update($user_info);
    }

    public function getUserById(int $user_id)
    {
        $user = DB::table(self::$tableName)
            ->where('id', $user_id)
            ->first();
        unset($user->password);
        unset($user->token);
        unset($user->token_created_at);
        return $user;
    }

    public function getUserByCardNum($card_num)
    {
        $user = DB::table(self::$tableName)
            ->where('card_num', $card_num)
            ->first();
        unset($user->password);
        unset($user->token);
        unset($user->token_created_at);
        return $user;
    }

    public function generateToken($card_num)
    {
        $token = md5(Carbon::now()) . md5($card_num);
        DB::table(self::$tableName)
            ->where('card_num', $card_num)
            ->update([
                'token' => $token,
                'token_created_at' => Carbon::now()->timestamp
            ]);
        $user = $this->getUserByCardNum($card_num);

        return ['token' => $token, 'user' => $user];
    }

    public function checkPassword($user_id, $password): bool
    {
        $user = DB::table(self::$tableName)
            ->where('id', $user_id)
            ->where('password', md5($password))
            ->first();
        return boolval($user);
    }

    public function checkPasswordForCardNum($card_num, $password): bool
    {
        $user = DB::table(self::$tableName)
            ->where('card_num', $card_num)
            ->where('password', md5($password))
            ->first();
        return boolval($user);
    }

    public function deleteUsers($ids)
    {
        DB::table(self::$tableName)
            ->whereIn('id', $ids)
            ->delete();
    }

}