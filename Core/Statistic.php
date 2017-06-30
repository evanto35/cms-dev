<?php
namespace Core;

use Core\QB\DB;
use Core\GeoIP;
use Core\Cookie;
use Core\Common;

class Statistic
{

	private $user_hash;
	private $record_id;
	public $ip;

	static $instance;

    public static function factory()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
	

    public function __construct()
    {
		$this->ip = ($ip !== null && filter_var($ip, FILTER_VALIDATE_IP)) ? $ip : $this->ip();
    }
	
	public static function ip()
    {
		
		return '178.136.229.251';
        $_server = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];
        foreach ($_server as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return null;
    }
	
    // Setting cart ID
    public function update()
    {

		$arr = explode('/', Arr::get($_SERVER, 'REQUEST_URI'));
		if ($arr[1] == 'Media' or $arr[1] == 'wezom' or $arr[1] == 'Wezom' or Route::module() == 'ajax' or  Route::module() == 'api') {
			return false;
		}
        // Check cookie for existance of the statistic
        $hash = Cookie::get('statistic');
        if (!$hash) {
            $hash = sha1(microtime() . Text::random());
			$day_end=strtotime(date('d.m.Y').'23:59:59');
			Cookie::set('statistic', $hash, ($day_end-time()));
        }
		$this->user_hash=$hash;
		
		// find in DB
		$user = DB::select()
						->from('statistic_temp')
						->where('user_hash', '=', $this->user_hash)
						->where('date', '=', date('Y-m-d'))
						->find();
						
		if (!$user) {
			$user = $this->new_user($hash);
			$this->updateStatistic(date('Y-m-d'), $user, 1);
		} else {
			$this->updateStatistic(date('Y-m-d'), $user);
		}

		
		// Save referer
		$referer = Arr::get($_SERVER, 'HTTP_REFERER');
		if( $referer && strpos($referer, Arr::get($_SERVER, 'HTTP_HOST')) === false) {
			Common::factory('visitors_referers')->insert(array(
				'ip' => $this->ip,
				'url' => Arr::get($_SERVER, 'HTTP_REFERER'),
			));
		}

        return true;
    }
	
	public function updateStatistic($date, $user, $new = NULL) {
		
		if ($new) {
			$city = DB::select()
					->from('statistic_days_cities')
					->where('date','=', $date)
					->where('country','=', $user->country)
					->where('region','=', $user->region)
					->where('city','=',$user->city)
					->find();
			if (!$city) {
				Common::factory('statistic_days_cities')->insert(array(
					'date' => $date,
					'country' => $user->country,
					'region' => $user->region,
					'city' => $user->city,
					'unique_visitors' => 0,
					'all_enters' => 0
 				));
			}
			
			$device = DB::select()
					->from('statistic_days_devices')
					->where('date','=', $date)
					->where('device','=', $user->device)
					->find();
			if (!$city) {
				Common::factory('statistic_days_devices')->insert(array(
					'date' => $date,
					'device' => $user->device,
					'unique_visitors' => 0,
					'all_enters' => 0
 				));
			}
		}
		
		DB::update('statistic_temp')->set(['cnt_enters'=>($user->cnt_enters+1)])->where('id', '=', $user->id)->execute();
		$data = ['all_enters'=>DB::expr('all_enters+1')];
		if ($new) {
			$data['unique_visitors']  = DB::expr('unique_visitors+1');
		}
		DB::update('statistic_days_cities')
			->set($data)
			->where('date','=', $date)
			->where('country','=', $user->country)
			->where('region','=', $user->region)
			->where('city','=',$user->city)
			->execute();
		DB::update('statistic_days_devices')
			->set($data)
			->where('date','=', $date)
			->where('device','=', $user->device)
			->execute();
		$last_date=Config::get('system.last_update_statistic');
		if ($last_date!=$date) {
			DB::delete('statistic_temp')->where('date','=', $last_date)->execute();
			DB::update('config')->set(['zna'=>$date])->where('group','=','system')->where('key','=','last_update_statistic')->execute();
		}
		
	}
	
	public function new_user($hash) {
		
		$data=[];
		
		$data['user_hash'] = $hash;
		$data['date'] = date('Y-m-d');
		
		$detect = new DeviceDetect;
		$data['device'] = ($detect->isMobile() ? ($detect->isTablet() ? 'Tablet' : 'Phone') : 'Computer');
		
		$geo=GeoIP::factory($this->ip);
		if (!$geo) {
            return false;
        }
		$data['country'] = $geo->country;
		$data['region'] = $geo->region;
		$data['city'] = $geo->city;
		$data['cnt_enters'] = 0;
		
		$id = Common::factory('statistic_temp')->insert($data);
		$user = DB::select()->from('statistic_temp')->where('id','=',$id)->find();
		return $user;
	}
	
	/*public function saveFromTemp() {

		$last_date=Config::get('system.last_update_statistic');
			
		if (date('Y-m-d',(time()-86400))!=$last_date) {

			$dates = DB::select()
						->from('statistic_temp')
						->group_by('date')
						->where('date','!=', date('Y-m-d'))
						->find_all();
			foreach ($dates as $date) {
				
				$byDevices = DB::select('statistic_temp.*', [DB::expr('count(statistic_temp.id)'), 'cnt'], [DB::expr('sum(statistic_temp.cnt_enters)'), 'sum'])
								->from('statistic_temp')
								->where('date', '=', $date->date)
								->group_by('device')
								->find_all();
				if (sizeof($byDevices)) {
					foreach ($byDevices as $item) {
						$data=[
							'date' => $date->date,
							'device' => $item->device,
							'all_enters' => $item->sum,
							'unique_visitors' => $item->cnt
						];
						
						Common::factory('statistic_days_devices')->insert($data);
					}
				}
				
				$byCity = DB::select('statistic_temp.*', [DB::expr('count(statistic_temp.id)'), 'cnt'], [DB::expr('sum(statistic_temp.cnt_enters)'), 'sum'])
					->from('statistic_temp')
					->where('date', '=', $date->date)
					->group_by('country')
					->group_by('region')
					->group_by('city')
					->find_all();
				if (sizeof($byCity)) {
					foreach ($byCity as $item) {
						$data=[
							'date' => $date->date,
							'country' => $item->country,
							'region' => $item->region,
							'city' => $item->city,
							'all_enters' => $item->sum,
							'unique_visitors' => $item->cnt
						];
						Common::factory('statistic_days_cities')->insert($data);
					}
				}
				
				DB::delete('statistic_temp')->where('date','=', $date->date)->execute();
				DB::update('config')->set(['zna'=>$date->date])->where('group','=','system')->where('key','=','last_update_statistic')->execute();

			}
			
		}
	}*/

}
