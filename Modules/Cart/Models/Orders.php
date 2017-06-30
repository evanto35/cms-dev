<?php
namespace Modules\Cart\Models;

use Core\QB\DB;
use Core\Common;

class Orders extends Common
{

    public static $table = 'orders';


    public static function getUserOrders($user_id)
    {
        $orders = DB::select(static::$table . '.*', [DB::expr('SUM(orders_items.cost * orders_items.count)'), 'amount'], [DB::expr('SUM(orders_items.count)'), 'count_all'])
            ->from(static::$table)
            ->join('orders_items', 'LEFT')->on('orders_items.order_id', '=', static::$table . '.id')
            ->where(static::$table . '.user_id', '=', $user_id)
            ->group_by(static::$table . '.id')
            ->order_by(static::$table . '.created_at', 'DESC');
        return $orders->find_all();
    }


    public static function getOrder($order_id, $user_id = null)
    {
        $result = DB::select(
            static::$table . '.*',
            [DB::expr('SUM(orders_items.cost * orders_items.count)'), 'amount'],
            [DB::expr('SUM(orders_items.count)'), 'count']
        )
            ->from(static::$table)
            ->join('orders_items', 'LEFT')->on('orders_items.order_id', '=', static::$table . '.id')
            ->where(static::$table . '.id', '=', $order_id);
		
		if ($user_id !== null) {
			$result->where('orders.user_id', '=', $user_id);
		}
	
        return $result->find();
    }


    public static function getOrderItems($order_id)
    {
        $items = DB::select('catalog.alias', 'catalog.id', 'catalog.name', 'catalog_images.image', 'orders_items.count', ['orders_items.cost', 'price'])
            ->from('orders_items')
            ->join('catalog', 'LEFT')->on('orders_items.catalog_id', '=', 'catalog.id')
            ->join('catalog_images', 'LEFT')->on('catalog_images.main', '=', DB::expr('1'))->on('catalog_images.catalog_id', '=', 'catalog.id')
            ->where('orders_items.order_id', '=', $order_id);
        return $items->find_all();
    }

}