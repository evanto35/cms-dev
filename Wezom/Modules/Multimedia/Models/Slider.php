<?php
    namespace Wezom\Modules\Multimedia\Models;

    use Core\Arr;
    use Core\Message;

    class Slider extends \Core\Common {

        public static $table = 'slider';
        public static $image = 'slider';
        public static $rules = [];

        public static function valid($data = [])
        {
            static::$rules = [
                [
                    'error' => __('Название не может быть пустым!'),
                    'key' => 'not_empty',
                ],
            ];
            return parent::valid($data);
        }

    }