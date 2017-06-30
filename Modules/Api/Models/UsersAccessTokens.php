<?php

namespace Modules\Api\Models;

use Core\Common;
use Core\QB\DB;

class UsersAccessTokens extends Common {

    static $table = 'users';

    /**
     * @param $login
     * @param $uid
     * @return string
     */
    public static function generateAccessToken($email, $uid = null){
		
		$user = self::getRow($uid);
		if ($user->access_token) {
			return $user->access_token;
		} else {
			$access_token = hash('SHA256', $email.time());
			if ($uid !== null) {
				DB::update(static::$table)
					->set([
						'auth_token' => $access_token
					])
					->where('id', '=', $uid)
					->execute();
			}
			return $access_token;
		}



    }


    public static function checkAccessToken($access_token){

        $user = DB::select()
            ->from('users')
            ->where('auth_token', '=', $access_token)
            ->where('auth_token', 'IS NOT', null)
            ->find();

        if($user){
            return $user;
        } else {
            return false;
        }

    }
	
	public static function clearAccessToken($uid) {
		return DB::update('users')->set(['auth_token' => null])->where('id', '=', $uid)->execute();
	}

}