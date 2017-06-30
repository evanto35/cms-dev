<?php
    namespace Core\Validation;

    use Core\Arr;

    class Valid {

        public $_rules = [
            // EXAMPLE
//            'name' => array(
//                array(
//                    'error' => 'Enter your name!',
//                    'key' => 'not_empty',
//                ),
//                array(
//                    'error' => 'Name is too short!',
//                    'key' => 'min_length',
//                    'value' => 2,
//                ),
//                array(
//                    'error' => 'Name is too long!',
//                    'key' => 'max_length',
//                    'value' => 3,
//                ),
//                array(
//                    'error' => 'Name must contains only letters!',
//                    'key' => 'regex',
//                    'value' => '/^[А-Я]{1}[а-я]*$/u',
//                ),
//            ),
        ];
        public $_data = [];
        public $_errors = [];


        public function __construct(array $data = [], array $rules = []) {
            $this->_data = $data;
            $this->_rules = $rules;
        }


        /**
         * @return array
         */
        public function execute() {
            $r = new \Core\Validation\Rules;
            foreach($this->_rules AS $field => $rules) {
                if( is_array($rules) && count($rules) ) {
                    foreach($rules AS $rule) {
                        $method = Arr::get($rule, 'key');
                        if( !method_exists($r, $method) ) {
                            continue;
                        }
                        if( in_array($rule['key'], ['regex', 'min_length', 'max_length']) ) {
                            $success = $r::$method(trim(Arr::get($this->_data, $field)), Arr::get($rule, 'value'));
                        } else if($rule['key'] == 'unique') {
                            $success = $r::$method(trim(Arr::get($this->_data, $field)), $field, Arr::get($rule, 'value'));
                        } else {
                            $success = $r::$method(trim(Arr::get($this->_data, $field)));
                        }
                        if( !$success ) {
                            $this->_errors[] = $rule['error'];
                        }
                    }
                }
            }
            return $this->_errors;
        }

        /**
         * @param array $errors
         * @return string
         */
        public static function message(array $errors) {
            $message = '<p>Во время заполнения формы возникли следующие ошибки</p>';
            $message .= '<ul>';
            foreach($errors AS $error) {
                $message .= '<li>'.$error.'</li>';
            }
            $message .= '</ul>';
            return $message;
        }

    }