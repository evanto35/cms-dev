<?php
namespace Modules\Api\Controllers;


use Core\Config;
use Core\Arr;
use Core\HTML;
use Core\Route;
use Core\Common;
use Core\Log;
use Core\System;
use Core\Email;
use Modules\Api;
use Modules\Catalog\Models\Groups;
use Modules\Catalog\Models\Items;
use Modules\Catalog\Models\Filter;

class Catalog extends Api
{



    public function before()
    {
        parent::before();
    }

    public function categoriesAction()
    {
			
		$parent = Arr::get($_GET, 'category_id', NULL);
		if ($parent) {
			$category = Groups::getRow($parent, 'id', 1);
			if (!$category) {
				$this->error('No such category');
			}
		}
		$image_density = Arr::get($_GET, 'image_density', 0);
		
		if ($image_density == 1) {
			$size = '190_150';
		} else {
			$size = '219_180';
		}
		
		$groups = Groups::getInnerGroups($parent, 'sort', 'asc');
		$data = [];
		foreach ($groups as $item) {
			
			$image = '';
			if (is_file(HOST.HTML::media('images/mobile_categories/'.$size.'/'.$item->image))) {
				$image = HTML::link(HTML::media('images/mobile_categories/'.$size.'/'.$item->image), true);
			}
			$cnt = Groups::countInnerGroups($item->id);
			if ($cnt>0) $subcategory_id = true; else $subcategory_id = false;
			$products_id = false;
			if (!$subcategory_id) {
				$cnt = Items::countItems($item->id);
				if ($cnt>0) $products_id = true;
			}
			
			$data[] = [
				'id' => (int) $item->id,
				'name' => $item->name,
				'preview' => $image,
				'subcategory_id' => $subcategory_id,
				'products_id' => $products_id,
				'parent_id' => (int) $item->parent_id,
				'sort' => (int) $item->sort
			];
		}
						
		$this->response(['categories' => $data]);
    }
	
	public function productsAction()
    {
		
		$parent = Arr::get($_GET, 'category_id', NULL);
		$limit  = Arr::get($_GET, 'limit', NULL);
		$offset  = Arr::get($_GET, 'offset', NULL);
		$sort  = Arr::get($_GET, 'sort', 'default');
		$image_density = Arr::get($_GET, 'image_density', 0);
		
		if ($parent or $parent !== NULL) {
			$category = Groups::getRow($parent, 'id', 1);
			if (!$category) {
				$this->error('No such category');
			}
		}
		
		if ($image_density == 1) {
			$size = '495_352';
		} else {
			$size = '570_480';
		}
		
		if ($sort == 'cost_asc') {
			$sort = 'cost'; $type = 'asc';
		} elseif ($sort == 'cost_desc') {
			$sort = 'cost'; $type = 'desc';
		} else {
			$sort = 'sort'; $type = 'asc';
		}

		$items = Items::getItems($parent, 1, $sort, $type, $limit, $offset);
		$count = Items::countItems($parent, 1);
		
		$data = ['total_count' => (int) $count, 'products' => []];
		
		foreach ($items as $item) {
			
			$image = '';
			if (is_file(HOST.HTML::media('images/mobile_catalog/'.$size.'/'.$item->image))) {
				$image = HTML::link(HTML::media('images/mobile_catalog/'.$size.'/'.$item->image), true);
			}
			
			$spec = Items::getItemSpecifications($item->id, $item->parent_id);
			$spec_arr = [];
			if (sizeof($spec)) {
				foreach ($spec as $key=>$val) {
					$spec_arr[] = [
						'param' => $key,
						'value' => $val
					];
				}
			}
			
			$data['products'][] = [
				'category_id' => (int) $item->parent_id,
				'id' => (int) $item->id,
				'name' => $item->name,
				'description' => $item->text,
				'price' => (int) $item->cost,
				'sort' => (int) $item->sort,
				'image' => $image,
				'parameters' => $spec_arr,
				
			];
		}
				
		$this->response($data);

    }
	
	public function productsV2Action()
    {
		
		$parent = Arr::get($_GET, 'category_id', NULL);
		$limit  = Arr::get($_GET, 'limit', NULL);
		$offset  = Arr::get($_GET, 'offset', NULL);
		$sort  = Arr::get($_GET, 'sort', 'default');
		$image_density = Arr::get($_GET, 'image_density', 0);
		
		if ($parent or $parent !== NULL) {
			$category = Groups::getRow($parent, 'id', 1);
			if (!$category) {
				$this->error('No such category');
			}
		}
		
		if ($image_density == 1) {
			$size = '495_352';
		} else {
			$size = '570_480';
		}
		
		if ($sort == 'cost_asc') {
			$sort = 'cost'; $type = 'asc';
		} elseif ($sort == 'cost_desc') {
			$sort = 'cost'; $type = 'desc';
		} else {
			$sort = 'sort'; $type = 'asc';
		}

		$items = Items::getItems($parent, 1, $sort, $type, $limit, $offset);
		$count = Items::countItems($parent, 1);
		
		$data = ['total_count' => (int) $count, 'products' => []];
		
		foreach ($items as $item) {
			
			$image = '';
			if (is_file(HOST.HTML::media('images/mobile_catalog/'.$size.'/'.$item->image))) {
				$image = HTML::link(HTML::media('images/mobile_catalog/'.$size.'/'.$item->image), true);
			}
			
			$spec = Items::getItemSpecifications($item->id, $item->parent_id);
			$spec_arr = [];
			if (sizeof($spec)) {
				foreach ($spec as $key=>$val) {
					$spec_arr[] = [
						'param' => $key,
						'value' => $val
					];
				}
			}
			
			$reviews = Items::getReviews($item->id);
			$reviews_arr = [];
			$rate = 0;
			if (sizeof($reviews)) {
				foreach ($reviews as $review) {
					$reviews_arr[] = [
						"message" => $review->text,
						"rating" => (int) $review->rate,
						"date" => $review->date*1000,
						"user_name" => $review->name
					];
					$rate = $rate + $review->rate;
				}
				$rate = (int) ($rate/sizeof($reviews));
			}
			
			
			
			$data['products'][] = [
				'category_id' => (int) $item->parent_id,
				'id' => (int) $item->id,
				'name' => $item->name,
				'description' => $item->text,
				'price' => (int) $item->cost,
				'sort' => (int) $item->sort,
				'image' => $image,
				'parameters' => $spec_arr,
				'general_rating' => (int) $rate,
				'feedback' => $reviews_arr
			];
		}
				
		$this->response($data);

    }
	
	public function productAction()
    {
		
		$id  = Arr::get($_GET, 'product_id');
		if (!$id) {
			$this->error('Product ID is empty');
		}
		$item = Items::getRow($id, 'id', 1);
		if (!$item) {
			$this->error('No such product');
		}
		$images = Items::getItemImages($id);
		$spec = Items::getItemSpecifications($id, $item->parent_id);
		$images_arr = [];
		$size = '570_480';
		if (isset($_GET['image_density']) and $_GET['image_density']==1) {
			$size = '495_352';
		} else if (isset($_GET['image_density']) and $_GET['image_density']==2) {
			$size = 'original';
		}
		if (sizeof($images)) {
			foreach ($images as $image) {
				if (is_file(HOST.HTML::media('images/mobile_catalog/'.$size.'/'.$image->image))) {
					$images_arr[] = [
						'id' => (int) $image->id,
						'main' => ( $image->main) ? true : false,
						'sort' => (int) $image->sort,
						'image' => HTML::link(HTML::media('images/mobile_catalog/'.$size.'/'.$image->image), true)
					];
				}
			}
		}
		
		$spec_arr = [];
		if (sizeof($spec)) {
			foreach ($spec as $key=>$val) {
				$spec_arr[] = [
					'param' => $key,
					'value' => $val
				];
			}
		}
		
		$statuses = ['0' => 'Нет в наличии', '1' => 'Еть в наличии', '2' => 'Под заказ'];
	
		$data = [
			'id' => (int) $item->id,
			'name' => $item->name,
			'description' => $item->text,
			'price' => (int) $item->cost,
			'available' => Arr::get($statuses, $item->available),
			'parameters' => $spec_arr,
			'images' => $images_arr,
			'sort' => (int) $item->sort,
		];
		
		$this->response($data);
		
    }
	
	public function productV2Action()
    {
		
		$id  = Arr::get($_GET, 'product_id');
		if (!$id) {
			$this->error('Product ID is empty');
		}
		$item = Items::getRow($id, 'id', 1);
		if (!$item) {
			$this->error('No such product');
		}
		$images = Items::getItemImages($id);
		$spec = Items::getItemSpecifications($id, $item->parent_id);
		$images_arr = [];
		$size = '570_480';
		if (isset($_GET['image_density']) and $_GET['image_density']==1) {
			$size = '495_352';
		} else if (isset($_GET['image_density']) and $_GET['image_density']==2) {
			$size = 'original';
		}
		if (sizeof($images)) {
			foreach ($images as $image) {
				if (is_file(HOST.HTML::media('images/mobile_catalog/'.$size.'/'.$image->image))) {
					$images_arr[] = [
						'id' => (int) $image->id,
						'main' => ( $image->main) ? true : false,
						'sort' => (int) $image->sort,
						'image' => HTML::link(HTML::media('images/mobile_catalog/'.$size.'/'.$image->image), true)
					];
				}
			}
		}
		
		$spec_arr = [];
		if (sizeof($spec)) {
			foreach ($spec as $key=>$val) {
				$spec_arr[] = [
					'param' => $key,
					'value' => $val
				];
			}
		}
		
		$statuses = ['0' => 'Нет в наличии', '1' => 'Еть в наличии', '2' => 'Под заказ'];
		
		$reviews = Items::getReviews($item->id);
		$reviews_arr = [];
		$rate = 0;
		if (sizeof($reviews)) {
			foreach ($reviews as $review) {
				$reviews_arr[] = [
					"message" => $review->text,
					"rating" => (int) $review->rate,
					"date" => $review->date*1000,
					"user_name" => $review->name
				];
				$rate = $rate + $review->rate;
			}
			$rate = (int) ($rate/sizeof($reviews));
		}
	
		$data = [
			'id' => (int) $item->id,
			'name' => $item->name,
			'description' => $item->text,
			'price' => (int) $item->cost,
			'available' => Arr::get($statuses, $item->available),
			'parameters' => $spec_arr,
			'images' => $images_arr,
			'sort' => (int) $item->sort,
			'general_rating' => (int) $rate,
			'feedback' => $reviews_arr
		];
		
		$this->response($data);
		
    }
	
	public function searchAction()
    {
		
		$query = trim(strip_tags(Arr::get($_GET, 'query', NULL)));
		if (!$query) {
			$this->error('Query is empty');
		}

		$limit  = Arr::get($_GET, 'limit', NULL);
		$offset  = Arr::get($_GET, 'offset', NULL);
		$sort  = Arr::get($_GET, 'sort', 'default');
		$image_density = Arr::get($_GET, 'image_density', 0);
		
		if ($image_density == 1) {
			$size = '495_352';
		} else {
			$size = '570_480';
		}
		
		if ($sort == 'cost_asc') {
			$sort = 'cost'; $type = 'asc';
		} elseif ($sort == 'cost_desc') {
			$sort = 'cost'; $type = 'desc';
		} else {
			$sort = 'sort'; $type = 'asc';
		}

		$queries = Items::getQueries($query);
		$items = Items::searchItems($queries, $sort, $type, $limit, $offset);
		$count = Items::countSearchRows($queries);
		
		$data = ['total_count' => (int) $count, 'products' => []];
		
		foreach ($items as $item) {
			
			$image = '';
			if (is_file(HOST.HTML::media('images/mobile_catalog/'.$size.'/'.$item->image))) {
				$image = HTML::link(HTML::media('images/mobile_catalog/'.$size.'/'.$item->image), true);
			}
			
			$spec = Items::getItemSpecifications($item->id, $item->parent_id);
			$spec_arr = [];
			if (sizeof($spec)) {
				foreach ($spec as $key=>$val) {
					$spec_arr[] = [
						'param' => $key,
						'value' => $val
					];
				}
			}
			
			$data['products'][] = [
				'category _id' => (int) $item->parent_id,
				'id' => (int) $item->id,
				'name' => $item->name,
				'description' => $item->text,
				'price' => (int) $item->cost,
				'sort' => (int) $item->sort,
				'image' => $image,
				'parameters' => $spec_arr,
				
			];
		}
				
		$this->response($data);

    }
	
	public function getFilterParamsAction() {
		
		$category_id = Arr::get($_GET, 'category_id', 0);
		if ($category_id <= null) {
			$this->error('Category id is wrong', 21);
		}
		$category = Common::factory('catalog_tree')->getRow($category_id,'id',1);
		if (!sizeof($category)) {
			$this->error('Category does not exist', 22);
		}
		Route::factory()->setParam('group',$category_id);
		Filter::setSortElements();
		$array = Filter::getClickableFilterElements();
		$brands = Filter::getBrandsWidget();
		$models = Filter::getModelsWidget();
		$specifications = Filter::getSpecificationsWidget();
		
		$answer = [];
		
		$answer['min_price'] = (int) $array['min'];
		$answer['max_price'] = (int) $array['max'];
		$answer['sections'] = [];
		if (sizeof($brands)) {
			$properties = [];
			foreach ($brands as $obj) {
				$properties[] = [/*'id' => $obj->id, */'name' => $obj->name, 'alias' => $obj->alias];
			}
			$answer['sections'][]=[/*'id' => 0, */'name' => 'Производитель', 'alias' => 'brand', 'properties' => $properties];
		}
		if (sizeof($models)) {
			$properties = [];
			foreach ($models as $obj) {
				$properties[] = [/*'id' => $obj->id, */'name' => $obj->name, 'alias' => $obj->alias];
			}
			$answer['sections'][]=[/*'id' => 0, */'name' => 'Производитель', 'alias' => 'model', 'properties' => $properties];
		}
		
		if (sizeof($specifications) and sizeof($specifications['list'])) {
			foreach ($specifications['list'] as $key=>$name) {
				if (sizeof($specifications['values'][$key])) {
					$properties = [];
					foreach ($specifications['values'][$key] as $val) {
						$properties[] = [/*'id' => $alias, */'name' => $val->name, 'alias' => $val->alias];
					}
					$answer['sections'][]=[/*'id' => 0, */'name' => $name, 'alias' => $key, 'properties' => $properties];
				}
			}
		}
		$this->response($answer);
		
	}
	
	public function productsFilteredAction()
    {
		
		$parent = Arr::get($this->data, 'category_id', NULL);
		$limit  = Arr::get($this->data, 'limit', NULL);
		$offset  = Arr::get($this->data, 'offset', NULL);
		$sort  = Arr::get($this->data, 'sort', 'default');
		$image_density = Arr::get($this->data, 'image_density', 0);
		$filter = Arr::get($this->data, 'sections');
		$structured_filter = [];
		if (sizeof($filter)) {
			foreach ($filter as $obj) {
				$structured_filter[$obj['section_alias']] = $obj['properties'];
			}
		}

		Config::set('filter_array', $structured_filter);
		Route::factory()->setParam('group', $parent);
		
		if ($parent or $parent !== NULL) {
			$category = Groups::getRow($parent, 'id', 1);
			if (!$category) {
				$this->error('Category does not exist', 22);
			}
		}
		
		if ($image_density == 1) {
			$size = '495_352';
		} else {
			$size = '570_480';
		}
		
		if ($sort == 'cost_asc') {
			$sort = 'cost'; $type = 'asc';
		} elseif ($sort == 'cost_desc') {
			$sort = 'cost'; $type = 'desc';
		} else {
			$sort = 'sort'; $type = 'asc';
		}

		$products = Filter::getFilteredItemsList($limit, $offset, $sort, $type);
		
		$items = $products['items'];
		$count = $products['total'];
		
		$data = ['total_count' => (int) $count, 'products' => []];
		
		foreach ($items as $item) {
			
			$image = '';
			if (is_file(HOST.HTML::media('images/mobile_catalog/'.$size.'/'.$item->image))) {
				$image = HTML::link(HTML::media('images/mobile_catalog/'.$size.'/'.$item->image), true);
			}
			
			$spec = Items::getItemSpecifications($item->id, $item->parent_id);
			$spec_arr = [];
			if (sizeof($spec)) {
				foreach ($spec as $key=>$val) {
					$spec_arr[] = [
						'param' => $key,
						'value' => $val
					];
				}
			}
			
			$data['products'][] = [
				'category_id' => (int) $item->parent_id,
				'id' => (int) $item->id,
				'name' => $item->name,
				'description' => $item->text,
				'price' => (int) $item->cost,
				'sort' => (int) $item->sort,
				'image' => $image,
				'parameters' => $spec_arr,
				
			];
		}
		$this->response($data);

    }
	
	public function addCommentAction() {
		
        $id = Arr::get($this->data, 'product_id');
        if (!$id) {
            $this->error('Product does not exist!', 23);
        }
        $item = Items::getRow($id, 'id', 1);
        if (!$item) {
            $this->error('Product does not exist!', 23);
        }
        $name = Arr::get($this->data, 'name');
        if (!$name or mb_strlen($name, 'UTF-8') < 2) {
            $this->error('Name is to short', 18);
        }
        $text = trim(strip_tags(Arr::get($this->data, 'message')));
        if (!$text or mb_strlen($text, 'UTF-8') < 5) {
            $this->error('Comment is to short!', 25);
        }
		$rate = Arr::get($this->data, 'rating');
        if (!$rate or $rate>5) {
            $this->error('Rate is wrong!', 24);
        }
		$ip = System::getRealIP();

		$data = [
			'name' => $name,
			'catalog_id' => $id,
			'text' => $text,
			'date' => time(),
			'ip' => $ip,
			'rate' => $rate
		];
        $lastID = Common::factory('catalog_comments')->insert($data);

        // Create links
        $link = 'http://' . Arr::get($_SERVER, 'HTTP_HOST') . '/' . $item->alias . '/p' . $item->id;
        $link_admin = 'http://' . Arr::get($_SERVER, 'HTTP_HOST') . '/wezom/catalog/edit/' . $item->id;

        // Save log
        $qName = 'Отзыв к товару';
        $url = '/wezom/comments/edit/' . $lastID;
        Log::add($qName, $url, 6);
        
        // Send message to admin if need
        Email::sendTemplate(7, [
            '{{site}}' => Arr::get($_SERVER, 'HTTP_HOST'),
            '{{ip}}' => $ip, 
            '{{date}}' => date('d.m.Y H:i'),
            '{{name}}' => $name, 
            '{{text}}' => $text, 
            '{{link}}' => $link, 
            '{{admin_link}}' => $link_admin,
            '{{item_name}}' => $item->name
        ]);
		
		$this->response();
		
	}


}