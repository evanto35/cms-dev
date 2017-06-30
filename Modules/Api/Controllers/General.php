<?php
namespace Modules\Api\Controllers;


use Core\Config;
use Core\HTML;
use Core\Common;
use Modules\Api;
use Modules\Api\Models\Config as MConfig;

class General extends Api
{



    public function before()
    {
        parent::before();
    }

	
	public function aboutAction()
    {
		
		$data = ['title' => '', 'content' => '', 'image' => '', 'terms' => ''];
		$title = MConfig::getRow('about_title', 'key', 1);
		if ($title) {
			$data['title'] = $title->value;
		} 
		$content = MConfig::getRow('about_text', 'key', 1);
		if ($content) {
			$data['content'] = $content->value;
		}
		$terms = MConfig::getRow('terms_text', 'key', 1);
		if ($terms) {
			$data['terms'] = $terms->value;
		}
		$image = MConfig::getRow('mobile_about', 'key', 1);
		if (isset($_GET['image_density']) and $_GET['image_density'] == 1) {
			if (is_file(HOST.HTML::media('images/mobile_about/1080_745/'.$image->value))) {
				$data['image'] = HTML::link(HTML::media('images/mobile_about/1080_745/'.$image->value), true);
			}
		} else {
			if (is_file(HOST.HTML::media('images/mobile_about/1242_855/'.$image->value))) {
				$data['image'] = HTML::link(HTML::media('images/mobile_about/1242_855/'.$image->value), true);
			}
		}
		
		
		$this->response($data);
		
    }
	
	public function aboutV2Action()
    {
		
		$data = [ 'about_shop' => '', 'terms' => '', 'payment' => ''];
		/*$title = MConfig::getRow('about_title', 'key', 1);
		if ($title) {
			$data['title'] = $title->value;
		} */
		$about = MConfig::getRow('about_text', 'key', 1);
		if ($about) {
			$data['about_shop'] = $about->value;
		}
		$terms = MConfig::getRow('terms_text', 'key', 1);
		if ($terms) {
			$data['terms'] = $terms->value;
		}
		/*$image = MConfig::getRow('mobile_about', 'key', 1);
		if (isset($_GET['image_density']) and $_GET['image_density'] == 1) {
			if (is_file(HOST.HTML::media('images/mobile_about/1080_745/'.$image->value))) {
				$data['image'] = HTML::link(HTML::media('images/mobile_about/1080_745/'.$image->value), true);
			}
		} else {
			if (is_file(HOST.HTML::media('images/mobile_about/1242_855/'.$image->value))) {
				$data['image'] = HTML::link(HTML::media('images/mobile_about/1242_855/'.$image->value), true);
			}
		}*/
		$payment = MConfig::getRow('payment_text', 'key', 1);
		if ($payment) {
			$data['payment'] = $payment->value;
		}
		
		
		$this->response($data);
		
    }
	
	public function contactAction()
    {
		
		$data = ['emails' => [], 'phones' => [], 'addresses' => []];
		$phone1 = MConfig::getRow('phone1', 'key', 1);
		
		$email1 = MConfig::getRow('email1', 'key', 1);
		if ($email1 and trim($email1->value) != '') {
			$data['emails'][] = $email1->value;
		}
		$email2 = MConfig::getRow('email2', 'key', 1);
		if ($email2 and trim($email2->value) != '') {
			$data['emails'][] = $email2->value;
		}
		$email3 = MConfig::getRow('email3', 'key', 1);
		if ($email3 and trim($email3->value) != '') {
			$data['emails'][] = $email3->value;
		}
		
		if ($phone1 and trim($phone1->value) != '') {
			$data['phones'][] = $phone1->value;
		}
		$phone2 = MConfig::getRow('phone2', 'key', 1);
		if ($phone2 and trim($phone2->value) != '') {
			$data['phones'][] = $phone2->value;
		}
		$phone3 = MConfig::getRow('phone3', 'key', 1);
		if ($phone3 and trim($phone3->value) != '') {
			$data['phones'][] = $phone3->value;
		}
		
		$address1 = MConfig::getRow('address1', 'key', 1);
		if ($address1 and trim($address1->value) != '') {
			$data['addresses'][] = $address1->value;
		}
		$address2 = MConfig::getRow('address2', 'key', 1);
		if ($address2 and trim($address2->value) != '') {
			$data['addresses'][] = $address2->value;
		}
		$address3 = MConfig::getRow('address3', 'key', 1);
		if ($address3 and trim($address3->value) != '') {
			$data['addresses'][] = $address3->value;
		}
		
		$this->response($data);
		
    }
	
	public function contactV2Action()
    {
		
		$data = ['emails' => [], 'phones' => [], 'addresses' => []];
		
		$email1 = MConfig::getRow('email1', 'key', 1);
		if ($email1 and trim($email1->value) != '') {
			$data['emails'][] =['value' => $email1->value, 'desccription' => 'home'];
		}
		$email2 = MConfig::getRow('email2', 'key', 1);
		if ($email2 and trim($email2->value) != '') {
			$data['emails'][] =['value' => $email1->value, 'desccription' => 'shop'];
		}
		/*$email3 = MConfig::getRow('email3', 'key', 1);
		if ($email3 and trim($email3->value) != '') {
			$data['emails'][] = $email3->value;
		}*/
		$phone1 = MConfig::getRow('phone1', 'key', 1);
		if ($phone1 and trim($phone1->value) != '') {
			$data['phones'][] =['value' => $phone1->value, 'desccription' => 'home'];
		}
		$phone2 = MConfig::getRow('phone2', 'key', 1);
		if ($phone2 and trim($phone2->value) != '') {
			$data['phones'][] =['value' => $phone2->value, 'desccription' => 'shop'];
		}
		/*$phone3 = MConfig::getRow('phone3', 'key', 1);
		if ($phone3 and trim($phone3->value) != '') {
			$data['phones'][] = $phone3->value;
		}
		*/
		$address1 = MConfig::getRow('address1', 'key', 1);
		if ($address1 and trim($address1->value) != '') {
			$data['addresses'][] =['value' => $address1->value, 'desccription' => 'home'];
		}
		$address2 = MConfig::getRow('address2', 'key', 1);
		if ($address2 and trim($address2->value) != '') {
			$data['addresses'][] =['value' => $address2->value, 'desccription' => 'shop'];
		}
		/*$address3 = MConfig::getRow('address3', 'key', 1);
		if ($address3 and trim($address3->value) != '') {
			$data['addresses'][] = $address3->value;
		}*/
		
		$this->response($data);
		
    }
	
	public function bannersAction() {
		
		$banners = Common::factory('mobile_banners')->getRows(1,'sort','ASC');
		$banners_arr = [];
		if (sizeof($banners)) {
			
			foreach ($banners as $banner) {
				$image = '';
				if (isset($_GET['image_density']) and $_GET['image_density'] == 1) {
					if (is_file(HOST.HTML::media('images/mobile_banners/1080_745/'.$banner->image))) {
						$image = HTML::link(HTML::media('images/mobile_banners/1080_745/'.$banner->image), true);
					}
				} else {
					if (is_file(HOST.HTML::media('images/mobile_banners/1242_855/'.$banner->image))) {
						$image = HTML::link(HTML::media('images/mobile_banners/1242_855/'.$banner->image), true);
					}
				}
				if ($image !='') {
					$banners_arr[] = [
						'id' => (int) $banner->id,
						'sort' => (int) $banner->sort,
						'image' => $image
					];
				}
			}
			
		} 
		$this->response(['images' => $banners_arr]);
		
	}


}