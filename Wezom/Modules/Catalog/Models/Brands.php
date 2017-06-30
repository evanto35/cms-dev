<?php
    namespace Wezom\Modules\Catalog\Models;

    use Core\QB\DB;

    class Brands extends \Core\Common {

        public static $table = 'brands';
        public static $filters = [
            'name' => [
                'table' => NULL,
                'action' => 'LIKE',
            ],
        ];
        public static $rules = [];


        /**
         * Get brands that communicate with current group
         * @param null|integer $parent_id - Group ID
         * @return object
         */
        public static function getGroupRows($parent_id = NULL) {
            $result = DB::select(static::$table.'.*')
                ->from(static::$table)
                ->join('catalog_tree_brands')->on('catalog_tree_brands.brand_id', '=', static::$table.'.id');
            if( $parent_id !== NULL ) {
                $result->where('catalog_tree_brands.catalog_tree_id', '=', $parent_id);
            }
            return $result->order_by(static::$table.'.name', 'ASC')->group_by(static::$table.'.id')->find_all();
        }

        public static function valid($data = [])
        {
            static::$rules = [
                'name' => [
                    [
                        'error' => __('Название производителя не может быть пустым!'),
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
            ];
            return parent::valid($data);
        }

    }