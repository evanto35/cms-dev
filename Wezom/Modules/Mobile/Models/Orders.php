<?php
    namespace Wezom\Modules\Mobile\Models;

    use Core\QB\DB;
    use Core\Arr;
    use Core\Message;
    use Core\Validation\Valid;

    class Orders extends \Core\Common {

        public static $table = 'mobile_orders';
        public static $rules = [];


        public static function getRow($value) {
            $result = DB::select(
                static::$table.'.*',
                [DB::expr('SUM(mobile_order_items.count)'), 'count'],
                [DB::expr('SUM(mobile_order_items.cost * mobile_order_items.count)'), 'amount']
            )
                ->from(static::$table)
                ->join('mobile_order_items', 'LEFT')->on('mobile_order_items.order_id', '=', static::$table.'.id')
                ->where(static::$table.'.id', '=', $value);
            return $result->find();
        }


        public static function getRows($status = NULL, $date_s = NULL, $date_po = NULL, $sort = NULL, $type = NULL, $limit = NULL, $offset = NULL) {
            $result = DB::select(
                static::$table.'.*',
                [DB::expr('SUM(mobile_order_items.count)'), 'count'],
                [DB::expr('SUM(mobile_order_items.cost * mobile_order_items.count)'), 'amount']
            )
                ->from(static::$table)
                ->join('mobile_order_items', 'LEFT')->on('mobile_order_items.order_id', '=', static::$table.'.id');
            if( $status !== NULL ) {
                $result->where(static::$table.'.status', '=', $status);
            }
            if( $date_s ) {
                $result->where(static::$table . '.created_at', '>=', $date_s);
            }
            if( $date_po ) {
                $result->where(static::$table.'.created_at', '<=', $date_po + 24 * 60 * 60 - 1);
            }
            $result = parent::setFilter($result);
            $result->group_by(static::$table.'.id');
            if( $sort !== NULL ) {
                if( $type !== NULL ) {
                    $result->order_by($sort, $type);
                } else {
                    $result->order_by($sort);
                }
            }
            if( $limit !== NULL ) {
                $result->limit($limit);
            }
            if( $offset !== NULL ) {
                $result->offset($offset);
            }
            return $result->find_all();
        }


        public static function countRows($status = NULL, $date_s = NULL, $date_po = NULL) {
            $result = DB::select([DB::expr('COUNT('.static::$table.'.id)'), 'count'])->from(static::$table);
            if( $status !== NULL ) {
                $result->where(static::$table.'.status', '=', $status);
            }
            if( $date_s ) {
                $result->where(static::$table . '.created_at', '>=', $date_s);
            }
            if( $date_po ) {
                $result->where(static::$table.'.created_at', '<=', $date_po + 24 * 60 * 60 - 1);
            }
            $result = parent::setFilter($result);
            return $result->count_all();
        }


        public static function getAmount($status = NULL, $date_s = NULL, $date_po = NULL) {
            $result = DB::select([DB::expr('SUM(mobile_order_items.count * mobile_order_items.cost)'), 'amount'])
                ->from(static::$table)
                ->join('mobile_order_items')->on('mobile_order_items.order_id', '=', 'mobile_orders.id');
            if( $status !== NULL ) {
                $result->where(static::$table.'.status', '=', $status);
            }
            if( $date_s ) {
                $result->where(static::$table . '.created_at', '>=', $date_s);
            }
            if( $date_po ) {
                $result->where(static::$table.'.created_at', '<=', $date_po + 24 * 60 * 60 - 1);
            }
            $result = parent::setFilter($result);
            return (int) $result->find()->amount;
        }

        public static function valid($data = [])
        {
            static::$rules = [
                'name' => [
                    [
                        'error' => __('Имя не может быть пустым!'),
                        'key' => 'not_empty',
                    ],
                ],

                'email' => [
                    [
                        'error' => __('E-Mail не может быть пустым!'),
                        'key' => 'not_empty',
                    ],
                    [
                        'error' => __('E-Mail указан некорректно!'),
                        'key' => 'email',
                    ],
                ],
                'phone' => [
                    [
                        'error' => __('Укажите верный номер телефона!'),
                        'key' => 'not_empty',
                    ],
                ],


            ];
            return parent::valid($data);
        }

    }