<?php
namespace Modules;

use Core\Arr;
use Core\Config;
use Core\Encrypt;
use Core\GeoIP;
use Core\HTTP;
use Core\Route;
use Core\View;
use Core\System;
use Core\Cron;
use Core\HTML;
use Core\QB\DB;
use Core\User;
use Core\Statistic;
use Modules\Api\Models\UsersAccessTokens;

class Api
{


	public $data;
	public $headersData;
	public $user;


    public function before()
    {
        $_POST = Arr::clearArray($_POST);
        $_GET = Arr::clearArray($_GET);
        $this->_method = $_SERVER['REQUEST_METHOD'];
        $this->config();
		$postdata_json = file_get_contents("php://input");
		$this->data = json_decode($postdata_json, true);
		if (!is_array($this->data)) $this->data = [];
		if (is_array($_GET)) {
			$this->data = array_merge($_GET, $this->data);
		}
		$this->authorize();
		
    }
	
	private function authorize() {
		
		$this->headersData = getallheaders();

        foreach($this->headersData as $key => $val) {
            unset($this->headersData[$key]);
            $this->headersData[strtolower($key)] = $val;
        }
		
		if (isset($this->headersData['authorization'])) {
			$this->user = UsersAccessTokens::checkAccessToken($this->headersData['authorization']);
			if (!$this->user) {
				$this->error('Access token does not exist!', 401);
			}
			if ($this->user->status != 1) {
				$this->error('User is blocked!', 402);
			}
		}
		
	}



    private function config()
    {
        $result = DB::select('key', 'zna', 'group')
            ->from('config')
            ->join('config_groups')->on('config.group', '=', 'config_groups.alias')
            ->where('config.status', '=', 1)
            ->where('config_groups.status', '=', 1)
            ->find_all();
        $groups = [];
        foreach ($result as $obj) {
            $groups[$obj->group][$obj->key] = $obj->zna;
        }
        foreach ($groups as $key => $value) {
            Config::set($key, $value);
        }
    }

	
	public function response($data = array()) {
		
		$data['success'] = true;
		$data['error'] = null;
		
		$response = json_encode($data);
			
		echo $response;
		die;
		
	}
	
	public function error($message, $code = 404) {
		
		$data= [];
		$data['success'] = false;
		$data['error'] = [
			'code' => $code,
			'message' => $message
		];
		echo json_encode($data);
		die;
	}

}
