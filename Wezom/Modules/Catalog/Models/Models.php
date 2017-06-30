<?php
    namespace Wezom\Modules\Catalog\Models;

    use Core\Arr;
    use Core\Common;
    use Core\Message;
    use Core\QB\DB;

    class Models extends \Core\Common {

        public static $table = 'models';
        public static $filters = [
            'name' => [
                'table' => NULL,
                'action' => 'LIKE',
            ],
            'brand_id' => [
                'table' => NULL,
                'action' => '=',
            ],
        ];
        public static $rules = [];


        public static function valid($post) {
            static::$rules = [
                'name' => [
                    [
                        'error' => __('Название модели не может быть пустым!'),
                        'key' => 'not_empty',
                    ],
                ],
                'alias' => [
                    [
                        'error' => __('Алиас не может быть пустым!'),
                        'key' => 'not_empty',
                    ],
                    [
                        'error' => __('Алиас должен содержать только латинские буквы в нижнем регистре, цифры, "-" или "_"!'),
                        'key' => 'regex',
                        'value' => '/^[a-z0-9\-_]*$/',
                    ],
                ],
                'brand_alias' => [
                    [
                        'error' => __('Выберите производителя!'),
                        'key' => 'not_empty',
                    ],
                ],
            ];
            $brand = Common::factory('brands')->getRow(Arr::get($post, 'brand_alias'), 'alias');
            if(!$brand) {
                Message::GetMessage(0, __('Выберите бренд из списка!'));
                return false;
            }
            return parent::valid($post);
        }


        public static function getRows($status = NULL, $sort = NULL, $type = NULL, $limit = NULL, $offset = NULL) {
            $result = DB::select('models.*', ['brands.name', 'brand_name'])
                        ->from(static::$table)
                        ->join('brands', 'LEFT')->on(static::$table.'.brand_alias', '=', 'brands.alias');
            $result = parent::setFilter($result);
            if( $status !== NULL ) {
                $result->where(static::$table.'.status', '=', $status);
            }
            if( $sort !== NULL ) {
                if( $type !== NULL ) {
                    $result->order_by(static::$table.'.'.$sort, $type);
                } else {
                    $result->order_by(static::$table.'.'.$sort);
                }
            }
            if( $limit !== NULL ) {
                $result->limit($limit);
                if( $type !== NULL ) {
                    $result->offset($offset);
                }
            }
            return $result->find_all();
        }


        /**
         * Get models for current brand
         * @param integer $brand_id
         * @return object
         */
        public static function getBrandRows($brand_alias) {
            return DB::select()
                ->from(static::$table)
                ->where(static::$table.'.brand_alias', '=', $brand_alias)
                ->order_by(static::$table.'.name', 'ASC')
                ->find_all();
        }

    }