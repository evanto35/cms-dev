<?php
namespace Modules\User\Models;

use Core\Common;
use Core\QB\DB;

class Users extends Common
{

    public static $table = 'users';
	
	public static function checkEmail ($email, $id = null) {
		
		$result = DB::select([DB::expr('COUNT(id)'), 'count'])
            ->from(static::$table)
            ->where('email', '=', $email);
		if ($id !== null) {
			$result->where('id', '!=', $id);
		}
            
        $result = $result->count_all();
		
		return $result;
		
	}
	
	public static function checkNetwork($network, $uid) {
		
		$result = DB::select()->from('users_networks')
					->where('network', '=', $network)
					->where('uid', '=',$uid)
					->find();
		
		return $result;
		
	}

}