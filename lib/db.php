<?php //データベース共通関数

//汎用的なテーブルからレコードの取得
if ( !function_exists( 'get_db_table_records' ) ):
function get_db_table_records( $table_name, $column, $keyword = null, $order_by = null){
  global $wpdb;
  $where = null;
  if ($column && $keyword) {
    $where = $wpdb->prepare(' WHERE '.$column.' LIKE %s', '%'.$keyword.'%');
  }
  if ($order_by) {
    $order_by = esc_sql(' ORDER BY '.$order_by);
  }
  $query = "SELECT * FROM {$table_name}".
              $where.
              $order_by;

  $records = $wpdb->get_results( $query );
  //_v($query);

  return $records;
}
endif;