<?php
namespace Modules\Catalog\Models;

use Core\Config;
use Core\Cookie;
use Core\QB\DB;
use Core\Common;

class Items extends Common
{

    public static $table = 'catalog';
    public static $tableImages = 'catalog_images';
	
	public static function getItems($parent = null, $status = null, $sort = null, $type = null, $limit = null, $offset = null, $filter = true)
    {
        $result = DB::select()->from(static::$table);
		
		if ($parent !== null) {
			$result->where('parent_id', '=', $parent);
		}
						
        if ($status !== null) {
            $result->where('status', '=', $status);
        }
        if ($filter) {
            $result = static::setFilter($result);
        }
        if ($sort !== null) {
            if ($type !== null) {
                $result->order_by($sort, $type);
            } else {
                $result->order_by($sort);
            }
        }
        $result->order_by('id', 'DESC');
        if ($limit !== null) {
            $result->limit($limit);
            if ($offset !== null) {
                $result->offset($offset);
            }
        }
        return $result->find_all();
    }
	
	public static function countItems($parent = null, $status = null, $filter = true)
    {
        $result = DB::select([DB::expr('COUNT(' . static::$table . '.id)'), 'count'])->from(static::$table);
		if ($parent !== null) {
			$result->where('parent_id', '=', $parent);
		}
        if ($status !== null) {
            $result->where('status', '=', $status);
        }
        if ($filter) {
            $result = static::setFilter($result);
        }
        return $result->count_all();
    }

    public static function searchRows($queries, $limit = null, $offset = null, $category = NULL)
    {
        $result = DB::select(
            static::$table . '.*'
        )
            ->from(static::$table)
            ->join('brands')->on('brands.alias', '=', static::$table . '.brand_alias')
            ->where(static::$table . '.status', '=', 1);
			
		if ($category!==NULL) {
			$result->where(static::$table.'.parent_id', '=', $category);
		}
			
        $result->and_where_open();
        $result->or_where_open();
        foreach ($queries as $query) {
            $result->where(static::$table . '.name', 'LIKE', '%' . $query . '%');
        }
        $result->or_where_close();
        $result->or_where_open();
        foreach ($queries as $query) {
            $result->where(static::$table . '.artikul', 'LIKE', '%' . $query . '%');
        }
        $result->or_where_close();
        $result->or_where_open();
        foreach ($queries as $query) {
            $result->where('brands.name', 'LIKE', '%' . $query . '%');
        }
        $result->or_where_close();
        $result->and_where_close();
        $result->order_by(static::$table . '.sort', 'ASC');
        $result->order_by(static::$table . '.id', 'DESC');
        if ($limit !== null) {
            $result->limit($limit);
            if ($offset !== null) {
                $result->offset($offset);
            }
        }
        return $result->find_all();
    }


    public static function countSearchRows($queries, $category = NULL)
    {
        $result = DB::select([DB::expr('COUNT(' . static::$table . '.id)'), 'count'])
            ->from(static::$table)
            ->join('brands')->on('brands.alias', '=', static::$table . '.brand_alias')
            ->where(static::$table . '.status', '=', 1);
			
		if ($category!==NULL) {
			$result->where(static::$table.'.parent_id', '=', $category);
		}
		
        $result->and_where_open();
        $result->or_where_open();
        foreach ($queries as $query) {
            $result->where(static::$table . '.name', 'LIKE', '%' . $query . '%');
        }
        $result->or_where_close();
        $result->or_where_open();
        foreach ($queries as $query) {
            $result->where(static::$table . '.artikul', 'LIKE', '%' . $query . '%');
        }
        $result->or_where_close();
        $result->or_where_open();
        foreach ($queries as $query) {
            $result->where('brands.name', 'LIKE', '%' . $query . '%');
        }
        $result->or_where_close();
        $result->and_where_close();
        return $result->count_all();
    }
	
	public static function searchItems($queries, $sort = null, $type = null, $limit = null, $offset = null, $category = NULL)
    {
        $result = DB::select(
            static::$table . '.*'
        )
            ->from(static::$table)
            ->join('brands')->on('brands.alias', '=', static::$table . '.brand_alias')
            ->where(static::$table . '.status', '=', 1);
			
		if ($category!==NULL) {
			$result->where(static::$table.'.parent_id', '=', $category);
		}
			
        $result->and_where_open();
        $result->or_where_open();
        foreach ($queries as $query) {
            $result->where(static::$table . '.name', 'LIKE', '%' . $query . '%');
        }
        $result->or_where_close();
        $result->or_where_open();
        foreach ($queries as $query) {
            $result->where(static::$table . '.artikul', 'LIKE', '%' . $query . '%');
        }
        $result->or_where_close();
        $result->or_where_open();
        foreach ($queries as $query) {
            $result->where('brands.name', 'LIKE', '%' . $query . '%');
        }
        $result->or_where_close();
        $result->and_where_close();
		if ($sort !== null) {
            if ($type !== null) {
                $result->order_by($sort, $type);
            } else {
                $result->order_by($sort);
            }
        }
        $result->order_by(static::$table . '.id', 'DESC');
        if ($limit !== null) {
            $result->limit($limit);
            if ($offset !== null) {
                $result->offset($offset);
            }
        }
        return $result->find_all();
    }


    public static function getQueries($query)
    {
        $spaces = ['-', '_', '/', '\\', '=', '+', '*', '$', '@', '(', ')', '[', ']', '|', ',', '.', ';', ':', '{', '}'];
        $query = str_replace($spaces, ' ', $query);
        $arr = preg_split("/[\s,]+/", $query);
        return $arr;
    }


    public static function getBrandItems($brand_alias, $sort = null, $type = null, $limit = null, $offset = null)
    {
        $result = DB::select(static::$table . '.*')
            ->from(static::$table)
            ->where(static::$table . '.brand_alias', '=', $brand_alias)
            ->where(static::$table . '.status', '=', 1);
        if ($sort !== null) {
            if ($type !== null) {
                $result->order_by(static::$table . '.' . $sort, $type);
            } else {
                $result->order_by(static::$table . '.' . $sort);
            }
        }
        if ($limit !== null) {
            $result->limit($limit);
            if ($offset !== null) {
                $result->offset($offset);
            }
        }
        return $result->find_all();
    }


    public static function countBrandItems($brand_alias)
    {
        $result = DB::select([DB::expr('COUNT(' . static::$table . '.id)'), 'count'])
            ->from(static::$table)
            ->where(static::$table . '.brand_alias', '=', $brand_alias)
            ->where(static::$table . '.status', '=', 1);
        return $result->count_all();
    }


    public static function getItemsByFlag($flag, $sort = null, $type = null, $limit = null, $offset = null)
    {
        $result = DB::select(static::$table . '.*')
            ->from(static::$table)
            ->where(static::$table . '.' . $flag, '=', 1)
            ->where(static::$table . '.status', '=', 1);
        if ($sort !== null) {
            if ($type !== null) {
                $result->order_by(static::$table . '.' . $sort, $type);
            } else {
                $result->order_by(static::$table . '.' . $sort);
            }
        }
        if ($limit !== null) {
            $result->limit($limit);
            if ($offset !== null) {
                $result->offset($offset);
            }
        }
        return $result->find_all();
    }


    public static function countItemsByFlag($flag)
    {
        $result = DB::select([DB::expr('COUNT(' . static::$table . '.id)'), 'count'])
            ->from(static::$table)
            ->where(static::$table . '.' . $flag, '=', 1)
            ->where(static::$table . '.status', '=', 1);
        return $result->count_all();
    }


    public static function addViewed($id)
    {
        $ids = static::getViewedIDs();
        if (!in_array($id, $ids)) {
            $ids[] = $id;
            Cookie::setArray('viewed', $ids, 60 * 60 * 24 * 30);
        }
        return;
    }


    public static function getViewedIDs()
    {
        $ids = Cookie::getArray('viewed', []);
        return $ids;
    }


    public static function getViewedItems($sort = null, $type = null, $limit = null, $offset = null)
    {
        $ids = Items::getViewedIDs();
        if (!$ids) {
            return [];
        }
        $result = DB::select(static::$table . '.*')
            ->from(static::$table)
            ->where(static::$table . '.id', 'IN', $ids)
            ->where(static::$table . '.status', '=', 1);
        if ($sort !== null) {
            if ($type !== null) {
                $result->order_by(static::$table . '.' . $sort, $type);
            } else {
                $result->order_by(static::$table . '.' . $sort);
            }
        }
        if ($limit !== null) {
            $result->limit($limit);
            if ($offset !== null) {
                $result->offset($offset);
            }
        }
        return $result->find_all();
    }


    public static function countViewedItems()
    {
        $ids = Items::getViewedIDs();
        if (!$ids) {
            return 0;
        }
        $result = DB::select([DB::expr('COUNT(' . static::$table . '.id)'), 'count'])
            ->from(static::$table)
            ->where(static::$table . '.id', 'IN', $ids)
            ->where(static::$table . '.status', '=', 1);
        return $result->count_all();
    }


    public static function getRow($value, $field = 'id', $status = null)
    {
        $result = DB::select(
            static::$table . '.*',
            ['brands.name', 'brand_name'],
            ['models.name', 'model_name'],
            ['catalog_tree.name', 'parent_name']
        )
            ->from(static::$table)
            ->join('catalog_tree', 'LEFT')
            ->on(static::$table . '.parent_id', '=', 'catalog_tree.id')
            ->join('brands', 'LEFT')
            ->on(static::$table . '.brand_alias', '=', 'brands.alias')
            ->on('brands.status', '=', DB::expr('1'))
            ->join('models', 'LEFT')
            ->on(static::$table . '.model_alias', '=', 'models.alias')
            ->on('models.status', '=', DB::expr('1'))
            ->where(static::$table . '.status', '=', 1)
            ->where(static::$table . '.id', '=', $value);
        return $result->find();
    }


    public static function getItemImages($item_id)
    {
        $result = DB::select()
            ->from(static::$tableImages)
            ->where(static::$tableImages . '.catalog_id', '=', $item_id)
            ->order_by(static::$tableImages . '.sort');
        return $result->find_all();
    }


    public static function getItemSpecifications($item_id, $parent_id)
    {
        $specifications = DB::select('specifications.*')->from('specifications')
            ->join('catalog_tree_specifications', 'LEFT')->on('catalog_tree_specifications.specification_id', '=', 'specifications.id')
            ->where('catalog_tree_specifications.catalog_tree_id', '=', $parent_id)
            ->where('specifications.status', '=', 1)
            ->order_by('specifications.name')
            ->as_object()->execute();
        $res = DB::select()->from('specifications_values')
            ->join('catalog_specifications_values', 'LEFT')->on('catalog_specifications_values.specification_value_alias', '=', 'specifications_values.alias')
            ->where('catalog_specifications_values.catalog_id', '=', $item_id)
            ->where('status', '=', 1)
            ->as_object()->execute();
        $specValues = [];
        foreach ($res as $obj) {
            $specValues[$obj->specification_id][] = $obj;
        }
        $spec = [];
        foreach ($specifications as $obj) {
            if (isset($specValues[$obj->id]) and is_array($specValues[$obj->id]) and count($specValues[$obj->id])) {
                if ($obj->type_id == 3) {
                    $spec[$obj->name] = '';
                    foreach ($specValues[$obj->id] AS $o) {
                        $spec[$obj->name] .= $o->name . ', ';
                    }
                    $spec[$obj->name] = substr($spec[$obj->name], 0, -2);
                } else {
                    $spec[$obj->name] = $specValues[$obj->id][0]->name;
                }
            }
        }
        return $spec;
    }
	
	public static function getReviews($catalog_id) {
		
		$result = DB::select()->from('catalog_comments')
						->where('catalog_id','=',$catalog_id)
						->where('status','=',1)
						->order_by('date','DESC')
						->find_all();
						
		return $result;
		
	}

}