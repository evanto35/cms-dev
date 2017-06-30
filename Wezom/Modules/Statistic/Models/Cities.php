<?php
    namespace Wezom\Modules\Statistic\Models;

    use Core\HTML;
    use Core\QB\DB;

    class Cities extends \Core\Common {

        public static $table = 'statistic_days_cities';
        public static $filters;

		public static function getRows( $date_s = NULL, $date_po = NULL, $sort = NULL, $type = NULL, $limit = NULL, $offset = NULL, $filter=true) {
            $result = DB::select()->from(static::$table);
            if( $date_s ) {
                $result->where(static::$table . '.date', '>=', $date_s);
            }
            if( $date_po ) {
                $result->where(static::$table.'.date', '<=', $date_po );
            }
			
			if( $filter ) {
                $result = static::setFilter($result);
            }
			
            if( $sort !== NULL ) {
                if( $type !== NULL ) {
                    $result->order_by($sort, $type);
                } else {
                    $result->order_by($sort);
                }
            }
            $result->order_by('id', 'DESC');
            if( $limit !== NULL ) {
                $result->limit($limit);
            }
            if( $offset !== NULL ) {
                $result->offset($offset);
            }
            return $result->find_all();
        }

        public static function countRows( $date_s = NULL, $date_po = NULL, $filter=true) {
            $result = DB::select([DB::expr('COUNT('.static::$table.'.id)'), 'count'])->from(static::$table);

            if( $date_s ) {
                $result->where(static::$table . '.date', '>=', $date_s);
            }
            if( $date_po ) {
                $result->where(static::$table.'.date', '<=', $date_po);
            }
			
			if( $filter ) {
                $result = static::setFilter($result);
            }
			
            return $result->count_all();
        }
      

    }