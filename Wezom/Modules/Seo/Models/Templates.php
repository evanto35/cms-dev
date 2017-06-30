<?php
    namespace Wezom\Modules\Seo\Models;

    class Templates extends \Core\Common {

        public static $table = 'seo_templates';
        public static $rules = [];

        public static function valid($data = [])
        {
            static::$rules = [
                'h1' => [
                    [
                        'error' => 'H1 не может быть пустым!',
                        'key' => 'not_empty',
                    ],
                ],
                'title' => [
                    [
                        'error' => 'Title не может быть пустым!',
                        'key' => 'not_empty',
                    ],
                ],
            ];
            return parent::valid($data);
        }

    }