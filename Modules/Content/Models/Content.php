<?php
namespace Modules\Content\Models;

use Core\QB\DB;
use Core\Common;

class Content extends Common
{

    public static $table = 'content';

    public static function getKids($id, $status = null)
    {
        $kids = DB::select()
            ->from(static::$table)
            ->where(static::$table . '.parent_id', '=', $id);
        if ($status !== null) {
            $kids->where('status', '=', $status);
        }
        return $kids->order_by('sort', 'ASC')->find_all();
    }

}