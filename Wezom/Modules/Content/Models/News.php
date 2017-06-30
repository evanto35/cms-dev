<?php
    namespace Wezom\Modules\Content\Models;

    use Core\Arr;
    use Core\Message;
    use Core\QB\DB;

    class News extends \Core\Common {

        public static $table = 'news';
        public static $image = 'news';
        public static $filters = [
            'name' => [
                'table' => NULL,
                'action' => 'LIKE',
            ],
        ];
        public static $rules = [];

        public static function getRows($status = NULL, $date_s = NULL, $date_po = NULL, $sort = NULL, $type = NULL, $limit = NULL, $offset = NULL, $filter=true) {
            $result = DB::select()->from(static::$table);
            if( $status !== NULL ) {
                $result->where(static::$table.'.status', '=', $status);
            }
            if( $date_s ) {
                $result->where(static::$table . '.date', '>=', $date_s);
            }
            if( $date_po ) {
                $result->where(static::$table.'.date', '<=', $date_po + 24 * 60 * 60 - 1);
            }
			
			if( $filter ) {
                $result = static::setFilter($result);
            }
			
            if( $sort !== NULL ) {
                if( $type !== NULL ) {
                    $result->order_by($sort, $type);
                } else {
                    $result->order_by($sort);
                }
            }
            $result->order_by('id', 'DESC');
            if( $limit !== NULL ) {
                $result->limit($limit);
            }
            if( $offset !== NULL ) {
                $result->offset($offset);
            }
            return $result->find_all();
        }

        public static function countRows($status = NULL, $date_s = NULL, $date_po = NULL, $filter=true) {
            $result = DB::select([DB::expr('COUNT('.static::$table.'.id)'), 'count'])->from(static::$table);
            if( $status !== NULL ) {
                $result->where(static::$table.'.status', '=', $status);
            }
            if( $date_s ) {
                $result->where(static::$table . '.date', '>=', $date_s);
            }
            if( $date_po ) {
                $result->where(static::$table.'.date', '<=', $date_po + 24 * 60 * 60 - 1);
            }
			
			if( $filter ) {
                $result = static::setFilter($result);
            }
			
            return $result->count_all();
        }

        public static function valid($post) {
            static::$rules = [
                'name' => [
                    [
                        'error' => __('Название новости не может быть пустым!'),
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
                'date' => [
                    [
                        'error' => __('Дата не может быть пустой!'),
                        'key' => 'not_empty',
                    ],
                    [
                        'error' => __('Укажите правильную дату!'),
                        'key' => 'date',
                    ],
                ],
            ];
            if( !trim(Arr::get($post, 'alias')) ) {
                Message::GetMessage(0, __('Алиас не может быть пустым!'));
                return 0;
            }
            if( !trim(Arr::get($post, 'date')) OR !strtotime(Arr::get($post, 'date')) ) {
                Message::GetMessage(0, __('Дата не может быть пустой!'));
                return 0;
            }
            if( !trim(Arr::get($post, 'name')) ) {
                Message::GetMessage(0, __('Наименование страницы не может быть пустым!'));
                return 0;
            }
            return 1;
        }

    }