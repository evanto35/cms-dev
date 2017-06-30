<?php
namespace Modules\Content\Controllers;

use Core\Route;
use Core\View;
use Core\Config;
use Modules\Base;
use Modules\Content\Models\Content AS Model;

class Content extends Base
{

    public function indexAction()
    {
        // Check for existance
        $page = Model::getRow(Route::param('alias'), 'alias', 1);
        if (!$page) {
            return Config::error();
        }
        // Seo
        $this->_seo['h1'] = $page->h1;
        $this->_seo['title'] = $page->title;
        $this->_seo['keywords'] = $page->keywords;
        $this->_seo['description'] = $page->description;
        $this->generateParentBreadcrumbs($page->parent_id, 'content', 'parent_id');
        $this->setBreadcrumbs($page->name);
        // Add plus one to views
        $page = Model::addView($page);
        // Get content page children
        $kids = Model::getKids($page->id, 1);
        // Render template
        $this->_content = View::tpl(['text' => $page->text, 'kids' => $kids], 'Content/Page');
    }

}
    