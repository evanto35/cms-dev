<?php
namespace Modules\Sitemap\Controllers;

use Core\Common;
use Core\Config;
use Core\Route;
use Core\View;
use Modules\Base;
use Modules\Content\Models\Content;
use Modules\Content\Models\Control;
use Modules\News\Models\News;

class Sitemap extends Base
{

    public $current;

    public function before()
    {
        parent::before();
        $this->current = Control::getRow(Route::controller(), 'alias', 1);
        if (!$this->current) {
            return Config::error();
        }
        $this->setBreadcrumbs($this->current->name, $this->current->alias);
    }

    // Search list
    public function indexAction()
    {
        if (Config::get('error')) {
            return false;
        }
        // Seo
        $this->_seo['h1'] = $this->current->h1;
        $this->_seo['title'] = $this->current->title;
        $this->_seo['keywords'] = $this->current->keywords;
        $this->_seo['description'] = $this->current->description;

        // Get pages
        $result = Content::getRows(1, 'sort');
        $pages = [];
        foreach ($result as $obj) {
            $pages[$obj->parent_id][] = $obj;
        }

        // Get catalog groups
        $result = Common::factory('catalog_tree')->getRows(1, 'sort');
        $groups = [];
        foreach ($result as $obj) {
            $groups[$obj->parent_id][] = $obj;
        }

        // Get catalog groups
        $brands = Common::factory('brands')->getRows(1, 'sort');

        // Get news
        $news = News::getRows(1, 'date', 'DESC');

        // Get articles
        $articles = Common::factory('articles')->getRows(1, 'id', 'DESC');

        // Render page
        $this->_content = View::tpl(['pages' => $pages, 'groups' => $groups, 'news' => $news, 'articles' => $articles, 'brands' => $brands], 'Sitemap/Index');
    }

}