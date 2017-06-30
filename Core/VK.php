<?php

namespace Core;

use Modules\Api\Models\Config as MConfig;

class VK
{

	public $client_id;
	public $client_secret;
	public $version = '5.64';
 
	public static function factory() {
		$obj = new self();
		$id_faecbook = MConfig::getRow('id_vk', 'key', 1);
		$obj->client_id = $id_faecbook->value;
		$secret_facebook = MConfig::getRow('secret_vk', 'key', 1);
		$obj->client_secret = $secret_facebook->value;
		return $obj;
	}
	
	public function getToken() {
		$link = 'https://oauth.vk.com/access_token?client_id='.$this->client_id.'&client_secret='.$this->client_secret.'&grant_type=client_credentials&v='.$this->version;
		$answer = $this->sendCurl($link);
		if (isset($answer['access_token'])) {
			return $answer['access_token'];
		}
		return false;
	}
	
	public function checkToken($token, $ip) {
		
		$access_token = $this->getToken();
		$link = 'https://api.vk.com/method/secure.checkTokent?token='.$token.'&ip='.$ip.'&client_secret='.$this->client_secret.'&v='.$this->version.'&access_token='.$access_token;
		$answer = $this->sendCurl($link);
		//return $answer;
		$check = 0;
		if (isset($answer['data']['success']) and $answer['data']['success']) $check = 1;
		return $check;
		
	}
	
	public function sendCurl($link) {
		
		$curl=curl_init();
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_HEADER,false);
		$answer=curl_exec($curl);
		curl_close($curl);
		
		return json_decode($answer,true);
		
	}
	
}
