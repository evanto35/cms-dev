<?php

namespace Wezom\Modules\Catalog\Models;

use Core\Arr;
use Core\Common;
use Core\HTML;
use Core\Message;
use Core\QB\DB;
use Wezom\Modules\Catalog\Models\CatalogSpecificationsValues AS CSV;

class Items extends \Core\Common {

    public static $table = 'catalog';
    public static $filters = [
        'name' => [
            'table' => NULL,
            'action' => 'LIKE',
        ],
        'artikul' => [
            'table' => NULL,
            'action' => 'LIKE',
        ],
        'parent_id' => [
            'table' => NULL,
            'action' => '=',
        ],
    ];
    public static $rules = [];

    /**
     * @param null/integer $status - 0 or 1
     * @param null/string $sort
     * @param null/string $type - ASC or DESC. No $sort - no $type
     * @param null/integer $limit
     * @param null/integer $offset - no $limit - no $offset
     * @return object
     */
    public static function getRows($status = NULL, $sort = NULL, $type = NULL, $limit = NULL, $offset = NULL) {
        $result = DB::select(static::$table . '.*', ['catalog_tree.name', 'catalog_tree_name'])
                        ->from(static::$table)
                        ->join('catalog_tree', 'LEFT')->on('catalog_tree.id', '=', static::$table . '.parent_id');
        $result = parent::setFilter($result);
        if ($status !== NULL) {
            $result->where(static::$table . '.status', '=', $status);
        }
        if ($sort !== NULL) {
            if ($type !== NULL) {
                $result->order_by($sort, $type);
            } else {
                $result->order_by($sort);
            }
        }
        $result->order_by(static::$table . '.id', 'DESC');
        if ($limit !== NULL) {
            $result->limit($limit);
            if ($type !== NULL) {
                $result->offset($offset);
            }
        }
        return $result->group_by(static::$table . '.id')->find_all();
    }

    public static function getRow($value, $field = 'id', $status = NULL) {
        $result = DB::select(static::$table . '.*', ['catalog_tree.name', 'catalog_tree_name'])
                ->from(static::$table)
                ->join('catalog_tree', 'LEFT')->on('catalog_tree.id', '=', static::$table . '.parent_id')
                ->where('catalog.' . $field, '=', $value);
        if ($status !== NULL) {
            $result->where('status', '=', $status);
        }
        return $result->find();
    }

    /**
     * Add communications specifications - items
     * @param mixed $specArray - integer specification value id | array specification values ids
     * @param integer $id - item id
     * @return bool
     */
    public static function changeSpecificationsCommunications($specArray, $id) {
        CSV::delete($id, 'catalog_id');
        if (!$specArray) {
            return false;
        }
        if (!is_array($specArray)) {
            $specArray = [$specArray];
        }
        foreach ($specArray as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $specification_value_alias) {
                    CSV::insert([
                        'catalog_id' => $id,
                        'specification_value_alias' => $specification_value_alias,
                        'specification_alias' => $key,
                    ]);
                }
            } else if ($value) {
                CSV::insert([
                    'catalog_id' => $id,
                    'specification_value_alias' => $value,
                    'specification_alias' => $key,
                ]);
            }
        }
        Common::factory('catalog')->update(['specifications' => json_encode($specArray)], $id);
        return true;
    }

    /**
     * Get specifications ids that belongs to item with ID = $id
     * @param integer $id - item id from catalog_tree table
     * @return array
     */
    public static function getItemSpecificationsAliases($id) {
        $specArray = [];
        $res = CSV::getRowsAliases($id);
        foreach ($res as $obj) {
            if ($obj->type_id == 3) {
                $specArray[$obj->specification_alias][] = $obj->specification_value_alias;
            } else {
                $specArray[$obj->specification_alias] = $obj->specification_value_alias;
            }
        }
        return $specArray;
    }

    public static function getItemSpecificationsIDS($id) {
        $specArray = [0];
        $res = CSV::getRows($id);
        foreach ($res as $obj) {
            if ($obj->type_id == 3) {
                $specArray[$obj->specification_id][] = $obj->specification_value_id;
            } else {
                $specArray[$obj->specification_id] = $obj->specification_value_id;
            }
        }
        return $specArray;
    }

    public static function valid($data = [])
    {
        static::$rules = [
            'name' => [
                [
                    'error' => __('Название товара не может быть пустым!'),
                    'key' => 'not_empty',
                ],
            ],
            'alias' => [
                [
                    'error' => __('Алиас не может быть пустым!'),
                    'key' => 'not_empty',
                ],
                [
                    'error' => __('Алиас должен содержать только латинские буквы в нижнем регистре, цифры, "-" или "_"!'),
                    'key' => 'regex',
                    'value' => '/^[a-z0-9\-_]*$/',
                ],
            ],
            'cost' => [
                [
                    'error' => __('Цена должна быть больше нуля!'),
                    'key' => 'pos_numeric',
                ],
            ],
            'parent_id' => [
                [
                    'error' => __('Выберите группу из списка!'),
                    'key' => 'pos_numeric',
                ],
            ],
        ];
        return parent::valid($data);
    }

}
