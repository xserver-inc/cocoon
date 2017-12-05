<?php //データベース共通関数

//テーブル作成関数
if ( !function_exists( 'create_db_table' ) ):
function create_db_table( $sql ){
   global $wpdb;
   $charset_collate = null;

   // charsetを指定する
   if ( !empty($wpdb->charset) )
      $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} ";

   // 照合順序を指定する（ある場合。通常デフォルトのutf8_general_ci）
   if ( !empty($wpdb->collate) )
      $charset_collate .= "COLLATE {$wpdb->collate}";

    // SQL文でテーブルを作る
    $sql = $sql." $charset_collate;";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   //_v($sql);
   $res = dbDelta( $sql );
}
endif;

//関数テキストテーブルのアップデート
if ( !function_exists( 'is_update_db_table' ) ):
function is_update_db_table($installed_ver, $now_ver) {
  // if ( function_exists( '_v' ) ):
  //   _v(THEME_NAME.'アップデートチェック');
  // endif;
  // オプションに登録されたデータベースのバージョンを取得
  // $installed_ver = get_function_texts_table_version();
  // $now_ver = FUNCTION_TEXTS_TABLE_VERSION;
  if ( $installed_ver != $now_ver ) {
    // if ( function_exists( '_v' ) ):
    //   _v(THEME_NAME.'がアップデートされました');
    // endif;
    return true;
  }
}
endif;


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


//関数テキストテーブルのアンインストール
if ( !function_exists( 'uninstall_db_table' ) ):
function uninstall_db_table($table_name) {
  global $wpdb;

  $wpdb->query('DROP TABLE IF EXISTS '.$table_name);

  //delete_option(OP_FUNCTION_TEXTS_TABLE_VERSION);
}
endif;
