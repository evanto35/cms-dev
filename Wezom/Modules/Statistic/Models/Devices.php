<?php
    namespace Wezom\Modules\Statistic\Models;

    use Core\HTML;
    use Core\QB\DB;

    class Devices extends \Core\Common {

        public static $table = 'statistic_days_devices';
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
		
		public static function getDiagramData( $date_s = NULL, $date_po = NULL, $sort = NULL, $type = NULL, $filter=true) {
            $result = DB::select('date', [DB::expr('sum(unique_visitors)'),'unique'],[DB::expr('sum(all_enters)'),'all'])->from(static::$table);
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

            $res=  $result->group_by('date')
					->find_all();
					
			$data = ['dates' => [], 'visitors' => [], 'visits' => [], 'hits' => 0, 'hits_tf' => 0, 'unique_hits_tf' => 0];		
			foreach ($res as $obj) {
				$data ['dates'][] = date('d.m.Y', strtotime($obj->date));
				$data ['visitors'][] = (int)$obj->unique;
				$data ['visits'][] = (int)$obj->all;
				$data['hits'] = $data['hits'] + $obj->all;
				if ($obj->date==$date_po) {
					$data['hits_tf']  = $obj->all;
					$data['unique_hits_tf']  = $obj->unique;
				}
			}

			return $data;			
			
        }
		
		public static function getDevicesDiagramData( $date_s = NULL, $date_po = NULL, $sort = NULL, $type = NULL, $filter=true) {
			
            $result = DB::select('*')->from(static::$table);
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

            $res=  $result->group_by('date')
					->find_all();
					
			$data = ['dates' => [], 'visitors' => [], 'visits' => [], 'hits' => [], 'unique_hits' => []];		
			foreach ($res as $obj) {
				$data ['dates'][] = date('d.m.Y', strtotime($obj->date));
				$data ['visitors'][$obj->device][] = (int) $obj->unique_visitors;
				$data ['visits'][$obj->device][] = (int) $obj->all_enters;
				$data['hits'][$obj->device] = $data['hits'][$obj->device] + $obj->all_enters;
			}

			return $data;			
			
        }
      

    }