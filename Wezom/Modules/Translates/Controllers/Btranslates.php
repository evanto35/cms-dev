<?php
    namespace Wezom\Modules\Translates\Controllers;

    use Core\Config;
    use Core\Route;
    use Core\Image;
    use Core\View;

    class Btranslates extends \Wezom\Modules\Base {

        public $tpl_folder = 'Translates';

        function before() {
            parent::before();
            $this->_seo['h1'] = __('Переводы админ панели');
            $this->_seo['title'] = __('Переводы админ панели');
            $this->setBreadcrumbs(__('Переводы админ панели'), 'backend/'.Route::controller().'/index');
        }

        function indexAction () {
            $result = [];
            $key = '';
            foreach( Config::get('i18n.languages') AS $key => $lang ) {
                $filename = HOST.'/Plugins/I18n/TranslatesBackend/'.$lang['alias'].'/general.php';
                if(is_file($filename)) {
                    $result[$key] = include $filename;
                }
            }
            if(!$result) {
                return Config::error();
            }
            $this->_content = View::tpl(
                [
                    'result' => $result,
                    'pageName' => __('Переводы админ панели'),
                    'count' => count($result[$key]),
                    'languages' => Config::get('i18n.languages'),
                ], $this->tpl_folder.'/BIndex');
        }

    }