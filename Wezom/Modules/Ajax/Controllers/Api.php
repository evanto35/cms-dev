<?php
    namespace Wezom\Modules\Ajax\Controllers;

    use Core\Config;
    use Core\HTML;
    use Core\QB\DB;

    class Api extends \Wezom\Modules\Ajax {

        public function translatesAction() {
            $base = HOST.'/Wezom';
            $_translates = $this->openFolder($base, []);

            $_translates = array_unique($_translates);


            $translates = [];
            $ru = [];
            foreach ($_translates AS $val) {
                $translates[$val] = '';
                $ru[$val] = $val;
            }

            $_result = DB::select('name')->from('config')->find_all();
            foreach ($_result as $obj) {
                $translates[$obj->name] = '';
                $ru[$obj->name] = $obj->name;
            }

            $_result = DB::select('name')->from('config_groups')->find_all();
            foreach ($_result as $obj) {
                $translates[$obj->name] = '';
                $ru[$obj->name] = $obj->name;
            }

            $_result = DB::select('name')->from('menu')->find_all();
            foreach ($_result as $obj) {
                $translates[$obj->name] = '';
                $ru[$obj->name] = $obj->name;
            }

            $_result = DB::select('name')->from('mail_templates')->find_all();
            foreach ($_result as $obj) {
                $translates[$obj->name] = '';
                $ru[$obj->name] = $obj->name;
            }

            foreach (Config::get('i18n.languages') as $key => $lang) {
                $tr = $translates;

                $tr_ru = $ru;
                $array = include HOST.'/Plugins/I18n/TranslatesBackend/'.$lang['alias'].'/general.php';

                $check = [];
                foreach (scandir(HOST.'/Plugins/I18n/TranslatesBackend/'.$lang['alias']) as $file) {
                    if($file != '.' && $file != '..' && $file != 'general.php') {
                        $check += include HOST.'/Plugins/I18n/TranslatesBackend/'.$lang['alias'].'/'.$file;
                    }
                }
                foreach ($tr as $key => $value) {
                    if(isset($check[$key])) {
                        unset($tr[$key]);
                        if(isset($tr_ru[$key])) {
                            unset($tr_ru[$key]);
                        }
                    }
                }

                if($lang['alias'] == 'ru') {
                    foreach ($array as $key => $value) {
                        if(!isset($tr_ru[$key])) {
                            unset($array[$key]);
                        }
                    }
                    $array = array_merge($tr_ru, $array);
                } else {
                    foreach ($array as $key => $value) {
                        if(!isset($tr[$key])) {
                            unset($array[$key]);
                        }
                    }
                    $array = array_merge($tr, $array);
                }
                $text = "<?php";
                $text .= "\n\t return array(";
                foreach( $array AS $key => $value ) {
                    $text .= "\n\t\t'".str_replace("'", "\'", $key)."' => '".str_replace("'", "\'", $value)."',";
                }
                $text .= "\n\t);";
                file_put_contents(HOST.'/Plugins/I18n/TranslatesBackend/'.$lang['alias'].'/general.php', $text);
            }

            $this->success('Done!');
        }
        public function openFolder($folder, $translates) {
            set_time_limit(0);
            foreach (scandir($folder) as $element) {
                if($element != '.' && $element != '..') {
                    if(is_dir($folder.'/'.$element) && $element != 'Media') {
                        $translates = $this->openFolder($folder.'/'.$element, $translates);
                    } elseif(is_file($folder.'/'.$element) && $element != 'ItemsMail.php' && $element != 'Api.php') {
                        $arr = explode('.', $element);
                        if(end($arr) == 'php') {
                            $string = file_get_contents($folder.'/'.$element);

                            $re_message = '/
    # match __(...(...)...) message lines (having arbitrary nesting depth).
    __\(                     # Outermost opening bracket (with leading __().
    (                        # Group $1: Bracket contents (subroutine).
      (?:                    # Group of bracket contents alternatives.
        [^()"\']++           # Either one or more non-brackets, non-quotes,
      | "[^"\\\\]*(?:\\\\[\S\s][^"\\\\]*)*"      # or a double quoted string,
      | \'[^\'\\\\]*(?:\\\\[\S\s][^\'\\\\]*)*\'  # or a single quoted string,
      | \( (?1) \)          # or a nested bracket (repeat group 1 here!).
      )*                    # Zero or more bracket contents alternatives.
    )                       # End $1: recursed subroutine.
    \)                      # Outermost closing bracket.
    /mx';

//                            preg_match_all('/__\([\'\"](.*)[\'\"]\)/', $string, $matches);
                            preg_match_all($re_message, $string, $matches);

                            if(isset($matches[1])) {
                                foreach ($matches[1] as $el) {
                                    if($el && !preg_match('/array\(/', $el) && preg_match('/\'.*\'/', $el)) {
                                        $e = explode("'),", $el);
                                        $translates[] = $this->_substr($e[0], 1, $this->_strlen($e[0]) - 2);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $translates;
        }


        function _substr($str, $offset, $length = NULL)
        {
            // Normalize params
            $str    = (string) $str;
            $strlen = $this->_strlen($str);
            $offset = (int) ($offset < 0) ? max(0, $strlen + $offset) : $offset; // Normalize to positive offset
            $length = ($length === NULL) ? NULL : (int) $length;
            // Impossible
            if ($length === 0 OR $offset >= $strlen OR ($length < 0 AND $length <= $offset - $strlen))
                return '';
            // Whole string
            if ($offset == 0 AND ($length === NULL OR $length >= $strlen))
                return $str;
            // Build regex
            $regex = '^';
            // Create an offset expression
            if ($offset > 0)
            {
                // PCRE repeating quantifiers must be less than 65536, so repeat when necessary
                $x = (int) ($offset / 65535);
                $y = (int) ($offset % 65535);
                $regex .= ($x == 0) ? '' : '(?:.{65535}){'.$x.'}';
                $regex .= ($y == 0) ? '' : '.{'.$y.'}';
            }
            // Create a length expression
            if ($length === NULL)
            {
                $regex .= '(.*)'; // No length set, grab it all
            }
            // Find length from the left (positive length)
            elseif ($length > 0)
            {
                // Reduce length so that it can't go beyond the end of the string
                $length = min($strlen - $offset, $length);
                $x = (int) ($length / 65535);
                $y = (int) ($length % 65535);
                $regex .= '(';
                $regex .= ($x == 0) ? '' : '(?:.{65535}){'.$x.'}';
                $regex .= '.{'.$y.'})';
            }
            // Find length from the right (negative length)
            else
            {
                $x = (int) (-$length / 65535);
                $y = (int) (-$length % 65535);
                $regex .= '(.*)';
                $regex .= ($x == 0) ? '' : '(?:.{65535}){'.$x.'}';
                $regex .= '.{'.$y.'}';
            }
            preg_match('/'.$regex.'/us', $str, $matches);
            return $matches[1];
        }

        function _strlen($str)
        {
            return strlen(utf8_decode($str));
        }

    }