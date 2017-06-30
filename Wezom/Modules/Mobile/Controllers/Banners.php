<?php
    namespace Wezom\Modules\Mobile\Controllers;

    use Core\Config;
    use Core\Route;
    use Core\Widgets;
    use Core\Message;
    use Core\Arr;
    use Core\HTTP;
    use Core\View;
    use Core\Pager\Pager;

    use Wezom\Modules\Mobile\Models\Banners AS Model;

    class Banners extends \Wezom\Modules\Base {

        public $tpl_folder = 'Mobile/Banners';
        public $page;
        public $limit;
        public $offset;

        function before() {
            parent::before();
            $this->_seo['h1'] = __('Банеры');
            $this->_seo['title'] = __('Банеры');
            $this->setBreadcrumbs(__('Банеры'), 'wezom/mobile_banners/index');
            $this->page = (int) Route::param('page') ? (int) Route::param('page') : 1;
            $this->limit = Config::get('basic.limit_backend');
            $this->offset = ($this->page - 1) * $this->limit;
        }

        function indexAction () {
            $status = NULL;
            if ( isset($_GET['status']) ) { $status = Arr::get($_GET, 'status', 1); }
            $count = Model::countRows($status);
            $result = Model::getRows($status, 'sort', 'ASC', $this->limit, $this->offset);
            $pager = Pager::factory( $this->page, $count, $this->limit )->create();
            $this->_toolbar = Widgets::get( 'Toolbar_List', [ 'delete' => 1, 'addLink' => '/wezom/mobile_banners/add']);
            $this->_content = View::tpl(
                [
                    'result' => $result,
                    'tpl_folder' => $this->tpl_folder,
                    'tablename' => Model::$table,
                    'count' => $count,
                    'pager' => $pager,
                    'pageName' => $this->_seo['h1'],
                ], $this->tpl_folder.'/Index');
        }

        function editAction () {
            if ($_POST) {
                $post = $_POST['FORM'];
                $post['status'] = Arr::get( $_POST, 'status', 0 );
                $res = Model::update($post, Route::param('id'));
                if($res) {
                    Model::uploadImage(Route::param('id'));
                    Message::GetMessage(1, __('Вы успешно изменили данные!'));
                } else {
                    Message::GetMessage(0, __('Не удалось изменить данные!'));
                }
                $this->redirectAfterSave(Route::param('id'), 'mobile_banners');
                $result = Arr::to_object($post);
            } else {
                $result = Model::getRow(Route::param('id'));
            }
            $this->_toolbar = Widgets::get( 'Toolbar_Edit', ['list_link' => '/wezom/mobile_banners/index']);
            $this->_seo['h1'] = __('Редактирование');
            $this->_seo['title'] = __('Редактирование');
            $this->setBreadcrumbs(__('Редактирование'), 'wezom/'.Route::controller().'/edit/'.(int) Route::param('id'));
            $this->_content = View::tpl(
                [
                    'obj' => $result,
                    'tpl_folder' => $this->tpl_folder,
                ], $this->tpl_folder.'/Form');
        }

        function addAction () {
            if ($_POST) {
                $post = $_POST['FORM'];
                $post['status'] = Arr::get( $_POST, 'status', 0 );
                $res = Model::insert($post);
                if($res) {
                    Model::uploadImage($res);
                    Message::GetMessage(1, __('Вы успешно добавили данные!'));
					$this->redirectAfterSave($res, 'mobile_banners');
                } else {
                    Message::GetMessage(0, __('Не удалось добавить данные!'));
                }
                $result = Arr::to_object($post);
            } else {
                $result = [];
            }
            $this->_toolbar = Widgets::get( 'Toolbar_Edit', ['list_link' => '/wezom/mobile_banners/index']);
            $this->_seo['h1'] = __('Добавление');
            $this->_seo['title'] = __('Добавление');
            $this->setBreadcrumbs(__('Добавление'), 'wezom/'.Route::controller().'/add');
            $this->_content = View::tpl(
                [
                    'obj' => $result,
                    'tpl_folder' => $this->tpl_folder,
                ], $this->tpl_folder.'/Form');
        }

        function deleteAction() {
            $id = (int) Route::param('id');
            $page = Model::getRow($id);
            if(!$page) {
                Message::GetMessage(0, __('Данные не существуют!'));
                HTTP::redirect('wezom/mobile_banners/index');
            }
            Model::deleteImage($page->image);
            Model::delete($id);
            Message::GetMessage(1, __('Данные удалены!'));
            HTTP::redirect('wezom/mobile_banners/index');
        }

        function deleteImageAction() {
            $id = (int) Route::param('id');
            $page = Model::getRow($id);
            if(!$page) {
                Message::GetMessage(0, __('Данные не существуют!'));
                HTTP::redirect('wezom/mobile_banners/index');
            }
            Model::deleteImage($page->image, $id);
            Message::GetMessage(1, __('Данные удалены!'));
            HTTP::redirect('wezom/mobile_banners/edit/'.$id);
        }
    }