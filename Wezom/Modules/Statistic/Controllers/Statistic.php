<?php
    namespace Wezom\Modules\Statistic\Controllers;

    use Core\Config;
    use Core\HTML;
    use Core\Route;
    use Core\Widgets;
    use Core\Arr;
    use Core\Image;
    use Core\View;
    use Core\Pager\Pager;

    use Wezom\Modules\Statistic\Models\Cities;
	use Wezom\Modules\Statistic\Models\Devices;

    class Statistic extends \Wezom\Modules\Base {

        public $tpl_folder = 'Statistic';
        public $page;
        public $limit;
        public $offset;

        function before() {
            parent::before();
            $this->page = (int )Route::param('page') ?: 1;
            $this->limit = (int) Arr::get($_GET, 'limit', Config::get('basic.limit_backend')) ?: 1;
            $this->offset = ($this->page - 1) * $this->limit;
			$this->sort = 'date';
			$this->type = (Arr::get($_GET,'sort')) ? Arr::get($_GET,'sort') : 'desc';

			
        }


        function citiesAction() {
			
			
            $this->_seo['title'] = __('Статистика по городам');
            $this->setBreadcrumbs(__('Статистика по городам'), 'wezom/statistic/devices');
			$date = date('Y-m-d'); 
            if ( Arr::get($_GET, 'date') ) { $date = date('Y-m-d', strtotime( Arr::get($_GET, 'date') )); }
			$this->_seo['h1'] = __('Статистика по городам').' '.date('d.m.Y', strtotime($date));
            $result = Cities::getRows($date, $date);			
            $this->_content = View::tpl(
                array(
                    'result' => $result,
                    'pageName' => $this->_seo['h1'],
                ), $this->tpl_folder . '/Cities');
        }
		
		function devicesAction() {
			
            $this->_seo['title'] = __('Статистика по устройствам');
            $this->setBreadcrumbs(__('Статистика по устройствам'), 'wezom/statistic/devices');
			$date_s = date('Y-m-d', time()-14*24*60*60); $date_po = date('Y-m-d');
            if ( Arr::get($_GET, 'date_s') ) { $date_s = date('Y-m-d', strtotime( Arr::get($_GET, 'date_s') )); }
            if ( Arr::get($_GET, 'date_po') ) { $date_po = date('Y-m-d', strtotime( Arr::get($_GET, 'date_po') )); }
			$this->_seo['h1'] = __('Статистика по устройствам').' '.date('d.m.Y', strtotime($date_s)).' - '.date('d.m.Y', strtotime($date_po));
            $result = Devices::getDevicesDiagramData($date_s, $date_po, 'date', 'asc');

			$this->_content = View::tpl(
                array(
                    'data' => $result,
                    'pageName' => $this->_seo['h1'],
                ), $this->tpl_folder . '/Devices');
		}
		

    }