<?php
    namespace Wezom\Modules\Mobile\Controllers;

    use Core\Arr;
    use Core\HTML;
    use Core\QB\DB;
    use Core\Route;
    use Core\Validation\Valid;
    use Core\Widgets;
    use Core\Message;
    use Core\HTTP;
    use Core\View;
    use Core\Common;
	use Core\Files;

    class Mobile extends \Wezom\Modules\Base {

        public $tpl_folder = 'Mobile';

        function before() {
            parent::before();
            $this->_seo['h1'] = __('Настройки мобильного приложения');
            $this->_seo['title'] = __('Настройки мобильного приложения');
            $this->setBreadcrumbs(__('Настройки мобильного приложения'), 'wezom/'.Route::controller().'/index');
        }

        function configAction () {
            if ($_POST) {
                $result = Common::factory('mobile_config')->getRows(1);
                $errors = [];
                foreach($result AS $obj) {
                    if (array_key_exists($obj->key, $_POST)) {
                        $value = Arr::get($_POST,$obj->key);
                        if( $value === NULL && $obj->valid ) {
                            $errors[] = __('Параметр должен быть заполнен!', [':param' => $obj->name]);
                        }
                    } else if($obj->type != 'checkbox' and $obj->type != 'image') {
                        $errors[] = __('Параметр должен быть заполнен!', [':param' => $obj->name]);
                    }
                }
                if( !$errors ) {
                    foreach($result AS $obj) {
                        if (array_key_exists($obj->key, $_POST)) {
                            $value = Arr::get($_POST, $obj->key);
                            DB::update('mobile_config')->set([
                                'value' => $value
                            ])->where('key', '=', $obj->key)->execute();
                        } else if($obj->type == 'checkbox') {
                            DB::update('mobile_config')->set([
                                'value' => 0
                            ])->where('key', '=', $obj->key)->execute();
                        } else if ($obj->type == 'image') {
							$filename = Files::uploadImage($obj->key, $obj->key);
							if ($filename) {
								DB::update('mobile_config')->set([
									'value' => $filename
								])->where('key', '=', $obj->key)->execute();
							}
						}
                    }
                    Message::GetMessage(1, __('Вы успешно изменили данные!'));
                    HTTP::redirect( 'wezom/'.Route::controller().'/config' );
                }
                Message::GetMessage(0, Valid::message($errors), FALSE);
            }
            $result = Common::factory('mobile_config')->getRows(1, 'sort', 'ASC');
            $this->_toolbar = Widgets::get( 'Toolbar_EditSaveOnly' );
            $this->_content = View::tpl(
                [
                    'result' => $result,
                ], $this->tpl_folder.'/Edit');
        }
		
		function deleteImageAction() {
			
			$id = (int) Route::param('id');
            $page = Common::factory('mobile_config')->getRow($id);
            if(!$page) {
                Message::GetMessage(0, __('Данные не существуют!'));
                HTTP::redirect('wezom/'.Route::controller().'/config');
            }
			Files::deleteImage($page->key, $page->value);
			DB::update('mobile_config')->set([
				'value' => ''
			])->where('id', '=', $id)->execute();
            Message::GetMessage(1, __('Данные удалены!'));
            HTTP::redirect('wezom/'.Route::controller().'/config');
			
		}
		
		
		function imagesAction() {
			
			/*$items = Common::factory('catalog_images')->getRows(null, 'id', 'asc', 100, 400);
			foreach ($items as $one) {
				if (is_file(HOST.\Core\HTML::media('images/catalog/original/'.$one->image))) {
					$_FILES['file'] = ['tmp_name'=>HOST.\Core\HTML::media('images/catalog/original/'.$one->image), 'name' => $one->image];
					\Core\Files::uploadImage('mobile_catalog', 'file', $one->image);
				}
			}
			
			$categories = Common::factory('catalog_tree')->getRows();
			foreach ($categories as $one) {
				if (is_file(HOST.\Core\HTML::media('images/catalog_tree/'.$one->image))) {
					$_FILES['file'] = ['tmp_name'=>HOST.\Core\HTML::media('images/catalog_tree/'.$one->image), 'name' => $one->image];
					\Core\Files::uploadImage('mobile_categories', 'file', $one->image);
				}
			}*/
			
			die;
			
		}
    }