<?php

namespace Core;

use Modules\Api\Models\Config as MConfig;

class Google
{


 
	public static function factory() {
		$obj = new self();
		return $obj;
	}
	
	public function checkToken($token) {
		
		$link = 'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token='.$token;
		$answer = $this->sendCurl($link);
		//return $answer;
		$check = 0;
		if (isset($answer['expires_in']) and $answer['expires_in']>0) $check = 1;
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
