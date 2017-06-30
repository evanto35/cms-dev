<?php

namespace Core;

use Modules\Api\Models\Config as MConfig;

class FB
{

	public $client_id;
	public $client_secret;
 
	public static function factory() {
		$obj = new self();
		$id_faecbook = MConfig::getRow('id_faecbook', 'key', 1);
		$obj->client_id = $id_faecbook->value;
		$secret_facebook = MConfig::getRow('secret_facebook', 'key', 1);
		$obj->client_secret = $secret_facebook->value;
		return $obj;
	}
	
	public function checkToken($token) {
		
		$link = 'https://graph.facebook.com/debug_token?input_token='.$token.'&access_token='.$this->client_id.'|'.$this->client_secret;
		$answer = $this->sendCurl($link);
		$check = 0;
		if (isset($answer['data']['is_valid']) and $answer['data']['is_valid']==1) $check = 1;
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
