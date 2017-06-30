<?php
namespace Modules\Api\Controllers;


use Core\Config;
use Core\Arr;
use Core\HTML;
use Core\Common;
use Core\User as CoreUser;
use Core\Log;
use Core\Email;
use Core\System;
use Core\FB;
use Core\VK;
use Core\Google;
use Modules\Api;
use Modules\User\Models\Users;
use Modules\Api\Models\UsersAccessTokens;

class User extends Api
{

	public $networks = [0 => 'googleplus', 1 => 'facebook', 2 => 'vkontakte'];


    public function before()
    {
        parent::before();
    }

	public function registrationAction() {
		
		$email = Arr::get($this->data,'email');
		
		if (!$email or !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Wrong Email',11);
        }
		
		$user = Users::getRow($email, 'email');
        if ($user) {
            if ($user->status) {
                $this->error('Users with such email registrated', 12);
            }
            $this->error('User with such email is inactive', 13);
        }
		$password = trim(Arr::get($this->data, 'password'));
        if (mb_strlen($password, 'UTF-8') < Config::get('main.password_min_length')) {
            $this->error('Password can not had less than ' . Config::get('main.password_min_length') . ' symbols!', 14);
        }
		
		$data = [
            'email' => $email,
            'password' => $password,
        ];
		
		// Create user. Then send an email to user with confirmation link or authorize him to site
        $mail = Common::factory('mail_templates')->getRow(4,'id',1);
        if ($mail) {
            // Creating of the new user and set his status to zero. He need to confirm his email
            $data['status'] = 0;
            CoreUser::factory()->registration($data);
			$user = Users::getRow($email,'email');

            // Save log
            $qName = 'Регистрация пользователя, требующая подтверждения';
            $url = '/wezom/users/edit/' . $user->id;
            Log::add($qName, $url, 1);

            // Sending letter to email
            $from = ['{{site}}', '{{ip}}', '{{date}}', '{{link}}'];
            $to = [
                Arr::get($_SERVER, 'HTTP_HOST'), Arr::get($data, 'ip'), date('d.m.Y'),
                'http://' . Arr::get($_SERVER, 'HTTP_HOST') . '/account/confirm/hash/' . $user->hash,
            ];
            $subject = str_replace($from, $to, $mail->subject);
            $text = str_replace($from, $to, $mail->text);
            Email::send($subject, $text, $user->email);

            // Inform user if mail is sended
            $this->response(['type'=>1]);
        } else {
            // Creating of the new user and set his status to 1. He must be redirected to his cabinet
            $data['status'] = 1;
			$data['auth_token'] = UsersAccessTokens::generateAccessToken($email);
            CoreUser::factory()->registration($data);
            $user = Users::getRow($email,'email');

            // Save log
            $qName = 'Регистрация пользователя';
            $url = '/wezom/users/edit/' . $user->id;
            Log::add($qName, $url, 1);
            
            Email::sendTemplate(13, [
                '{{site}}' => Arr::get($_SERVER, 'HTTP_HOST'),
                '{{ip}}' => Arr::get($data, 'ip'),
                '{{date}}' => date('d.m.Y'),
                '{{email}}' => $user->email,
                '{{password}}' => $password,
                '{{name}}' => $user->name
            ], Arr::get($data, 'email'));

            // Authorization of the user
			$this->response(['type'=>2, 'token' => $user->auth_token, 'uid' => (int)$user->id]);
        }
	}
	
	public function loginAction() {
		
		$email = Arr::get($this->data, 'email');
        $password = Arr::get($this->data, 'password');
		
		if (!$password) {
            $this->error('Password is empty', 15);
        }
		// Check user for existance and ban
        $user = CoreUser::factory()->get_user_by_email($email, $password);
        if (!$user) {
            $this->error('Error in login or password!', 16);
        }
        if (!$user->status) {
            $this->error('User with such email is inactive', 13);
        }
		$token = UsersAccessTokens::generateAccessToken($user->email, $user->id);
		Users::update(['last_login' => time(), 'logins' => (int)$user->logins + 1, 'updated_at' => time()],$user->id);
		$this->response(['token' => $token, 'uid' => (int) $user->id]);
		
	}
	
	public function restorePasswordAction() {		

        $email = Arr::get($this->data, 'email');
        if (!$email or !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Wrong Email', 11);
        }
        $user = Users::getRow($email, 'email');
        if (!$user) {
            $this->error('User with such email does not exist!', 17);
        }
        if (!$user->status) {
            $this->error('User is blocked!', 402);
        }

        // Generate new password for user and save it to his account
        $password = CoreUser::factory()->generate_random_password();
        CoreUser::factory()->update_password($user->id, $password);

        // Send E-Mail to user with instructions how recover password
        Email::sendTemplate(5, [
            '{{site}}' => Arr::get($_SERVER, 'HTTP_HOST'),
            '{{ip}}' => System::getRealIP(),
            '{{date}}' => date('d.m.Y H:i'),
            '{{password}}' => $password
        ], $user->email);

        $this->response([]);
		
	}
	
	public function logoutAction() {
		
		if (!$this->user) {
			$this->error('Function not available!',404);
		}
		
		UsersAccessTokens::clearAccessToken($this->user->id);
		$this->response();
		
	}
	
	public function getProfileAction() {
		if (!$this->user) {
			$this->error('Function not available!',404);
		}
		
		$data = [
			'name' => $this->user->name,
			'email' => $this->user->email,
			'phone' => $this->user->phone
		];
		
		$this->response($data);
	}
	
	public function updateProfileAction() {
		
		if (!$this->user) {
			$this->error('Function not available!',404);
		}
		
		$name = trim(Arr::get($this->data, 'name'));
        if (!$name or mb_strlen($name, 'UTF-8') < 2) {
            $this->error('Name is to short', 18);
        }
        $email = Arr::get($this->data, 'email');
        if (!$email or !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Wrong Email', 11);
        }
        $check = Users::checkEmail($email, $this->user->id);
        if ($check>0) {
            $this->error('Users with such email registrated', 12);
        }
        $phone = trim(Arr::get($this->data, 'phone'));
        if (!$phone) {
            $this->error('Phone is wrong', 19);
        }
		
		$password = trim(Arr::get($this->data, 'password'));
		if ($password) {
			if (mb_strlen($password, 'UTF-8') < Config::get('main.password_min_length')) {
				$this->error('Password can not had less than ' . Config::get('main.password_min_length') . ' symbols!', 14);
			}
			if (CoreUser::factory()->check_password($password, $this->user->password)) {
				$this->error('Password is the same!',26);
			}

			// Change password for new
			CoreUser::factory()->update_password($this->user->id, $password);

			// Send email to user with new data
			Email::sendTemplate(6, [
				'{{site}}' => Arr::get($_SERVER, 'HTTP_HOST'), 
				'{{ip}}' => System::getRealIP(),
				'{{date}}' => date('d.m.Y H:i'),
				'{{password}}' => $password
			],$this->user->email);
		}	
		
        // Save new users data
        Users::update(['name' => $name, 'email' => $email, 'phone' => $phone], $this->user->id);
		
		$this->response();
		
	}
	
	public function socialAction() {
		
		//0 - google+, 1-facebook, 2 - vk
		$social_type = Arr::get($this->data, 'social_type', null);
		if ($social_type === null or !in_array($social_type,[0,1,2])) {
			$this->error('Social type is wrong', 31);
		}
		
		$token = Arr::get($this->data, 'social_token');
		if (!$token) {
			$this->error('Token is empty', 32);
		}
		
		$email = Arr::get($this->data, 'email');
        if (!$email or !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Wrong Email', 11);
        }
		
		$social_id = Arr::get($this->data, 'social_id');
        if (!$social_id) {
            $this->error('User id is empty', 33);
        }
		
		$ip = Arr::get($this->data, 'ip');
		
		$ckeck = 0;
		if ($social_type==0) {
			$check = Google::factory()->checkToken($token);
		} elseif ($social_type ==1) {
			$check = FB::factory()->checkToken($token);
		} elseif ($social_type ==2) {
			$check = VK::factory()->checkToken($token, $ip);;
		}
		
		if (!$check) {
			$this->error('Token does not exist', 34);
		}
		
		$row = Users::checkNetwork(Arr::get($this->networks,$social_type), $social_id);
        if ($row) {
            $user = Common::factory('users')->getRow($row->user_id);
            if ($user) {
				$token = UsersAccessTokens::generateAccessToken($user->email, $user->id);
				$this->response(['token' => $token, 'uid' => (int) $user->id]);
			} else {
				Common::factory('users_networks')->delete($row->id);
			}
		} 
		
		$user = Users::getRow($email,'email');
        if (!$user) {
            $password = CoreUser::generate_random_password();
            $id = Common::factory('users')->insert([
                'email' => $email,
                'name' => Arr::get($this->data, 'name', ''),
				'phone' => Arr::get($this->data, 'phone', ''),
                'status' => 1,
                'last_login' => time(),
                'logins' => 1,
                'role_id' => 1,
                'ip' => $ip,
                'hash' => CoreUser::factory()->hash_user($email, $password),
                'password' => CoreUser::factory()->hash_password($password),
            ]);
            if (!$id) {
                $this->error('Can not create user',35);
            }
            
            Email::sendTemplate(13, [
                '{{site}}' => Arr::get($_SERVER, 'HTTP_HOST'),
                '{{ip}}' =>  $ip,
                '{{date}}' => date('d.m.Y'),
                '{{email}}' => $email,
                '{{password}}' => $password,
                '{{name}}' => Arr::get($this->data,'name'),
            ], Arr::get($data, 'email'));
            
            $user = Common::factory('users')->getRow($id);
        }
		
        Common::factory('users_networks')->insert([
            'user_id' => $user->id,
            'network' => Arr::get($this->networks, $social_type),
            'uid' => $social_id,
            /*'profile' => Arr::get($data, 'profile'),
            'first_name' => Arr::get($data, 'first_name'),
            'last_name' => Arr::get($data, 'last_name'),*/
            'email' => $email,
        ]);
		$token = UsersAccessTokens::generateAccessToken($user->email, $user->id);
		$this->response(['token' => $token, 'uid' => (int) $user->id]);
		
	}

}