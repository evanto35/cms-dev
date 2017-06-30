<?php
    namespace Minify;
    /**
     *  // Minify given string
     *  $min = Minify::factory('js')->set( $filecontents )->min();
     *  $min = Minify::factory('css')->set( $filecontents )->min();
     *
     *  // Minify list of files; write result into media folder
     *  Controller::after()
     * 	$this->template->jsFiles = Minify::factory('js')->minify( $this->template->jsFiles, $build );
     * 	$this->template->cssFiles = Minify::factory('css')->minify( $this->template->cssFiles, $build );
     *
     *  View:
    foreach ($cssFiles as $css) {
    if ( ! Kohana::config('minify.enabled') || $debug )
    $js = "views/css/{$css}?{$build}";
    echo HTML::style($css),"\n";
    }
    // application js files
    foreach ($jsFiles as $js) {
    if ( ! Kohana::config('minify.enabled') || $debug )
    $js = "views/jscript/{$js}?{$build}";
    echo HTML::script($js),"\n";
    }
     */
    class Core {

        protected $type;
        protected $file;
        protected $filepath; //путь к файлу
        protected $input = '';
        protected $inputLength = 0;

        protected $root;

        public function __construct($type) {
            $this->root = DS.'Media'.DS.'cache'.DS;
            $this->type = $type;
        }

        public static function factory($type) {
            $class = 'Minify\\' . ucfirst($type);
            return new $class($type);
        }

        // Dateien zusammenfassen, komprimieren und im media verzeichnis ablegen
        public function minify($files, $build = '') {
            if (\Core\Config::get('speed.minify')) {
                $name = 'minify_'.substr(md5(json_encode($files)),0,16);
                $outfile = $this->root . $name . $build . '.' . $this->type;
                if (!is_file(HOST.$outfile)) {
                    if (!is_array($files))
                        $files = array($files);

                    $files = array_unique($files);
                    $output = '';
                    foreach ($files as $file) {

                        if (stripos($file, '/') === 0) {
                            $this->filepath = mb_substr($file, 1);
                            $this->file = $file;
                        } else {
                            $this->file = $this->root . $file;
                            $this->filepath = $this->root . $file;
                        }

                        if (strpos($this->filepath, '?') != 0)
                            $this->filepath = substr($this->filepath, 0, strpos($this->filepath, '?'));

                        if (is_file($this->filepath)) {
                            $this->set(file_get_contents($this->filepath));
                            $output .= "/*" . $this->filepath . "*/\r\n" . $this->min() . "\r\n";
                        }
                    }

                    $f = fopen(HOST.'/'.trim($outfile, '/'), 'w');
                    if ($f) {
                        fwrite($f, $output);
                        fclose($f);
                    }

                    $gzdata = gzencode($output, 5);
                    file_put_contents(HOST.'/'.trim($outfile, '/') . '.gz', $gzdata);
                }
                return array($outfile);
            } else
                return $files;
        }

        // text an minifier bergeben (per member variable)
        public function set($input) {
            $this->input = str_replace("\r\n", "\n", $input);
            $this->inputLength = strlen($this->input);
            return $this;
        }

        // text komprimieren (abgeleitete Klasse)
        public function min() {
            return $this->input;
        }

        public static function clearCache() {
            $path = HOST.DS.'Media'.DS.'cache'.DS;
            array_map("unlink", glob($path."minify_*.css"));
            array_map("unlink", glob($path."minify_*.css.gz"));
            array_map("unlink", glob($path."minify_*.js"));
            array_map("unlink", glob($path."minify_*.js.gz"));
        }

}
