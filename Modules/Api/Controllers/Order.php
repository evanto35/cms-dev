<?php
namespace Modules\Api\Controllers;


use Core\Config;
use Core\Arr;
use Core\Common;
use Modules\Api;
use Modules\Cart\Models\Orders;


class Order extends Api
{



    public function before()
    {
        parent::before();
    }

    public function createAction()
    {
		$data = $this->data;
		$user = Arr::get($data, 'user_data');
		$products = Arr::get($data, 'products');
		
		if (!$user or  (Arr::get($user,'phone')=='' or Arr::get($user,'email')=='')) {
			$this->error('No user data');
		}
		
		if (!sizeof($products)) {
			$this->error('No products');
		}
		
		$post = ['name' =>  Arr::get($user, 'name'),
				'email' => Arr::get($user, 'email'),
				'phone' => Arr::get($user, 'phone'),
				'address' => Arr::get($user, 'address'),
				'status' => 0
				];
		$order_id = Common::factory('mobile_orders')->insert($post);
		
		if (!$order_id) {
			$this->response($this->error('Can not create order. Try later'));
		}
		foreach ($products as $key=>$arr) {
			$post = ['order_id' => $order_id,
					'catalog_id' => Arr::get($arr, 'id'),
					'count' => Arr::get($arr, 'count'),
					'cost' => Arr::get($arr, 'price'),
			];
			Common::factory('mobile_order_items')->insert($post);
		}
		$this->response([]);
    }
	
	public function getDataAction() {
		
		$payment = Config::get('order.payment');
		$delivery = Config::get('order.delivery');
		
		$data = ['payment_methods' => [], 'delivery_methods' => []];
		
		if (sizeof($payment)) {
			foreach ($payment as $key => $val) {
				$data['payment_methods'][] = ['id' => (int) $key, 'name' => $val];
			}
		}
		
		if (sizeof($delivery)) {
			foreach ($delivery as $key => $val) {
				$data['delivery_methods'][] = ['id' => (int) $key, 'name' => $val];
			}
		}
		
		$this->response($data);
		
	}
	
	public function getOrdersListAction() {
		
		if (!$this->user) {
			$this->error('Function not available!',404);
		}
		
		$orders = Orders::getUserOrders($this->user->id);
		$orders_arr = [];
		$statuses = Config::get('order.statuses');
		if (sizeof($orders)) {
			foreach ($orders as $order) {
				$orders_arr[] = [
					'number' => (int) $order->id,
					'delivery_status' => Arr::get($statuses, $order->status,""),
					'count' => (int) $order->count_all,
					'price' => (int) $order->amount,
					'date' => $order->created_at*1000
				];
			}
		}
		$this->response(['orders' => $orders_arr]);
		
	}
	
	public function getOrderAction() {
		
		if (!$this->user) {
			$this->error('Function not available!',404);
		}
		
		$order_id = Arr::get($this->data,'order_number');
		if (!$order_id) {
			$thid->error('Order does not exist', 27);
		}
		
		$order = Orders::getOrder($order_id, $this->user->id);
		
		if (!$order) {
			$thid->error('Order does not exist', 27);
		}
		$items = Orders::getOrderItems($order_id);
		
		$statuses = Config::get('order.statuses');
		$products = [];
		foreach ($items as $obj) {
			$products[] = [
				'id' => (int) $obj->id,
				'name' => $obj->name,
				'count' => (int) $obj->count,
				'price' => (int) $obj->price
			];
		}
		
		$data = [
			'total_price' => (int) $order->amount,
			'delivery_status' => Arr::get($statuses, $order->status,""),
			'date' => $order->created_at*1000,
			'payment_method' => (int) $order->payment,
			'delivery_method' => (int) $order->delivery,
			'products' => $products
		];
		
		$this->response($data);
		
	}
	
	public function createV2Action()
    {
		if (!$this->user) {
			$this->error('Function not available!',404);
		}
		
		$user_data = Arr::get($this->data, 'user_data');
		$products = Arr::get($this->data, 'products');	
		if (!$user_data or  (Arr::get($user_data,'phone')=='' or Arr::get($user_data,'email')=='')) {
			$this->error('No user data', 28);
		}
		
		if (!sizeof($products)) {
			$this->error('No products', 29);
		}
		
		$post = ['name' =>  Arr::get($user_data, 'name'),
				'email' => Arr::get($user_data, 'email'),
				'phone' => Arr::get($user_data, 'phone'),
				'status' => 0,
				'payment' => Arr::get($user_data, 'payment_method'),
				'delivery' => Arr::get($user_data, 'delivery_method'),
				'comment' => Arr::get($user_data,'commentary'),
				'user_id' => $this->user->id
				];
		$order_id = Common::factory('orders')->insert($post);
		
		if (!$order_id) {
			$this->error('Can not create order. Try later',30);
		}
		foreach ($products as $key=>$arr) {
			$post = ['order_id' => $order_id,
					'catalog_id' => Arr::get($arr, 'id'),
					'count' => Arr::get($arr, 'count'),
					'cost' => Arr::get($arr, 'price'),
			];
			Common::factory('orders_items')->insert($post);
		}
		$this->response();
    }



}