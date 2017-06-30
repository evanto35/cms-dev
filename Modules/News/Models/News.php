<?php
namespace Modules\News\Models;

use Core\QB\DB;
use Core\Common;

class News extends Common
{

    public static $table = 'news';

    /**
     * @param mixed $value - value
     * @param string $field - field
     * @param null /integer $status - 0 or 1
     * @return object
     */
    public static function getRow($value, $field = 'id', $status = null)
    {
        $result = DB::select()
            ->from(static::$table)
            ->where($field, '=', $value)
            ->where('date', '<=', time());
        if ($status !== null) {
            $result->where('status', '=', $status);
        }
        return $result->find();
    }


    /**
     * @param null /integer $status - 0 or 1
     * @param null /string $sort
     * @param null /string $type - ASC or DESC. No $sort - no $type
     * @param null /integer $limit
     * @param null /integer $offset - no $limit - no $offset
     * @return object
     */
    public static function getRows($status = null, $sort = null, $type = null, $limit = null, $offset = null, $filter = true)
    {
        $result = DB::select()
            ->from(static::$table)
            ->where('date', '<=', time());
        if ($status !== null) {
            $result->where('status', '=', $status);
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


    /**
     * @param null /integer $status - 0 or 1
     * @return int
     */
    public static function countRows($status = null, $filter = true)
    {
        $result = DB::select([DB::expr('COUNT(' . static::$table . '.id)'), 'count'])
            ->from(static::$table)
            ->where('date', '<=', time());
        if ($status !== null) {
            $result->where(static::$table . '.status', '=', $status);
        }
        return $result->count_all();
    }

}