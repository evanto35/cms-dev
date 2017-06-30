<?php
    namespace Wezom\Modules\Menu\Models;

    use Core\Arr;
    use Core\Message;

    class Menu extends \Core\Common {

        public static $table = 'sitemenu';
        public static $rules = [];

        public static function valid($data = [])
        {
            static::$rules = [
                'name' => [
                    [
                        'error' => __('Название пункта меню не может быть пустым!'),
                        'key' => 'not_empty',
                    ],
                ],
                'url' => [
                    [
                        'error' => __('Ссылка не может быть пустой!'),
                        'key' => 'not_empty',
                    ],
                    [
                        'error' => __('Ссылка должна содержать только латинские буквы в нижнем регистре, цифры, "/", "?", "&", "=", "-" или "_"!'),
                        'key' => 'regex',
                        'value' => '/^[a-z0-9\-_\/\?\=\&]*$/',
                    ],
                ],
            ];
            return parent::valid($data);
        }

    }