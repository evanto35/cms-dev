<?php
namespace Modules;

use Core\Arr;
use Core\Config;
use Core\Encrypt;
use Core\GeoIP;
use Core\HTTP;
use Core\Route;
use Core\View;
use Core\System;
use Core\Cron;
use Core\HTML;
use Core\QB\DB;
use Core\User;
use Core\Statistic;

class Base
{

    protected $_template = 'Text';
    protected $_content;
    protected $_config = [];
    protected $_seo = [];
    protected $_breadcrumbs = [];
    protected $_method;
	protected $_page = 1;
    protected $_limit;
    protected $_offset;
	protected $_pager;
	protected $_canonical;
	protected $_use_canonical=0;

    public function before()
    {
        $_POST = Arr::clearArray($_POST);
        $_GET = Arr::clearArray($_GET);
        $this->CSRF();
        $this->_method = $_SERVER['REQUEST_METHOD'];
        $this->config();
        $this->access();
        $this->redirects();
        User::factory()->is_remember();
        $cron = new Cron;
        $cron->check();
    }


    public function after()
    {
		$this->set_canonicals();
        $this->seo();
        $this->visitors();
        $this->render();
    }


    private function CSRF()
    {
		
        $_SESSION['token'] = Encrypt::instance()->encode(hash('sha256', Config::get('main.token')));
        if (Route::controller() != 'form') {
            return true;
        }
        if ($_POST) {
            if (!array_key_exists('token', $_POST)) {
                die('Error!');
            }
            $token = Encrypt::instance()->decode($_POST['token']);
            if ($token != hash('sha256', Config::get('main.token'))) {
                die('Error!');
            }
        }
    }


    private function access()
    {
        if (!Config::get('security.auth') || !Config::get('security.username') || !Config::get('security.password')) {
            return false;
        }
        if (
            Arr::get($_SERVER, 'PHP_AUTH_USER') != Config::get('security.username') ||
            Arr::get($_SERVER, 'PHP_AUTH_PW') != Config::get('security.password')
        ) {
            header('HTTP/1.0 401 Unauthorized');
            header('WWW-Authenticate: Basic realm="My Realm"');
            echo "<h1>Authorization Required</h1><p>This server could not verify that you are authorized to access the document requested.  Either you supplied the wrong credentials (e.g., bad password), or your browser doesn't understand how to supply the credentials required.</p>";
            exit;
        }
    }


    public function redirects()
    {
        $row = DB::select('link_to', 'type')->from('seo_redirects')->where('link_from', '=', strip_tags($_SERVER['REQUEST_URI']))->where('status', '=', 1)->find();
        if ($row) {
            HTTP::redirect($row->link_to, $row->type);
        }
    }


    public function visitors()
    {
        if (!Config::get('main.visitor')) {
            return false;
        }
       //GeoIP::factory()->save();
	   Statistic::factory()->update();
    }


    private function config()
    {
        $result = DB::select('key', 'zna', 'group')
            ->from('config')
            ->join('config_groups')->on('config.group', '=', 'config_groups.alias')
            ->where('config.status', '=', 1)
            ->where('config_groups.status', '=', 1)
            ->find_all();
        $groups = [];
        foreach ($result as $obj) {
            $groups[$obj->group][$obj->key] = $obj->zna;
        }
        foreach ($groups as $key => $value) {
            Config::set($key, $value);
        }
        $result = DB::select('script', 'place')->from('seo_scripts')->where('status', '=', 1)->as_object()->execute();
        $this->_seo['scripts'] = ['body' => [], 'head' => [], 'counter' => []];
        foreach ($result as $obj) {
            $this->_seo['scripts'][$obj->place][] = $obj->script;
        }
        $this->setBreadcrumbs('Главная', '');
    }


    private function seo()
    {
        if (!Config::get('error')) {
            $seo = DB::select('h1', 'title', 'keywords', 'description', 'text')
                ->from('seo_links')
                ->where('status', '=', 1)
                ->where('link', '=', Arr::get($_SERVER, 'REQUEST_URI'))
                ->as_object()->execute()->current();
            if ($seo) {
                $this->_seo['h1'] = $seo->h1;
                $this->_seo['title'] = $seo->title;
                $this->_seo['keywords'] = $seo->keywords;
                $this->_seo['description'] = $seo->description;
                $this->_seo['seo_text'] = $seo->text;
            }
        } else {
            $this->_seo['h1'] = 'Ошибка 404! Страница не найдена';
            $this->_seo['title'] = 'Ошибка 404! Страница не найдена';
            $this->_seo['keywords'] = 'Ошибка 404! Страница не найдена';
            $this->_seo['description'] = 'Ошибка 404! Страница не найдена';
            $this->_seo['seo_text'] = null;
        }
    }


    private function render()
    {
        if (Config::get('error')) {
            $this->_template = '404';
        }
        $this->_breadcrumbs = HTML::breadcrumbs($this->_breadcrumbs);
        $data = [];
        foreach ($this as $key => $value) {
            $data[$key] = $value;
        }
        $data['GLOBAL_MESSAGE'] = System::global_massage();
        echo HTML::compress(View::tpl($data, $this->_template));
    }


    protected function setBreadcrumbs($name, $link = null)
    {
        $this->_breadcrumbs[] = ['name' => $name, 'link' => $link];
    }


    protected function generateParentBreadcrumbs($id, $table, $parentAlias, $pre = '/')
    {
        $bread = $this->generateParentBreadcrumbsElement($id, $table, $parentAlias, []);
        if ($bread) {
            $bread = array_reverse($bread);
        }
        foreach ($bread as $obj) {
            $this->setBreadcrumbs($obj->name, $pre . $obj->alias);
        }
    }


    protected function generateParentBreadcrumbsElement($id, $table, $parentAlias, $bread)
    {
        $page = DB::select('id', $parentAlias, 'alias', 'status', 'name')->from($table)->where('id', '=', $id)->as_object()->execute()->current();
        if (is_object($page) and $page->status) {
            $bread[] = $page;
        }
        if (is_object($page) and (int)$page->$parentAlias > 0) {
            return $this->generateParentBreadcrumbsElement($page->$parentAlias, $table, $parentAlias, $bread);
        }
        return $bread;
    }
	
	protected function set_canonicals() {
		
		if ($this->_use_canonical and $this->_canonical!='' and $this->_pager) {
			
			if ($this->_page>1) {
				$this->_seo['hide_meta']=1;
				$this->_seo['seo_text'] = '';
				$this->_seo['canonical'] = $this->_canonical;
				$this->_seo['title'] = $this->_seo['title'].', Страница '.$this->_page;
				if ($this->_page==2) {
					$this->_seo['prev'] = $this->_canonical;
				} else {
					$this->_seo['prev'] =$this->_canonical.'/page/'.($this->_page-1);
				}
				if ($this->_pager->_next>1) {
					$this->_seo['next'] =$this->_canonical.'/page/'.$this->_pager->_next;
				}
			} else {
				if ($this->_pager->_next>1) {
					$this->_seo['next'] = $this->_canonical.'/page/'.$this->_pager->_next;
				}
			}
			
		}
		if (isset($_GET['sort']) or isset($_GET['filter']) or Route::param('filter')) {
			
				$this->_seo['hide_meta']=1;
				$this->_seo['seo_text'] = '';
				$this->_seo['canonical'] = $this->_canonical;
				
		}
		
	}

}
