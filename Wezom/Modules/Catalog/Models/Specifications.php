<?php
    namespace Wezom\Modules\Catalog\Models;

    use Core\Arr;
    use Core\Message;
    use Core\QB\DB;

    class Specifications extends \Core\Common {

        public static $table = 'specifications';
        public static $filters = [
            'name' => [
                'table' => NULL,
                'action' => 'LIKE',
            ],
        ];
        public static $rules = [];


        /**
         * Get specifications that communicate with current group
         * @param null|integer $parent_id - Group ID
         * @return object
         */
        public static function getGroupRows($parent_id = NULL) {
            $result = DB::select(static::$table.'.*')
                ->from(static::$table)
                ->join('catalog_tree_specifications')->on('catalog_tree_specifications.specification_id', '=', static::$table.'.id');
            if( $parent_id !== NULL ) {
                $result->where('catalog_tree_specifications.catalog_tree_id', '=', $parent_id);
            }
            return $result->order_by(static::$table.'.sort', 'ASC')->group_by(static::$table.'.id')->find_all();
        }

        public static function valid($data = [])
        {
            static::$rules = [
                'name' => [
                    [
                        'error' => __('Название характеристики не может быть пустым!'),
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