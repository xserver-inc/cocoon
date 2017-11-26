<?php //関数テキスト関係の関数

//関数テキストテーブルの作成
if ( !function_exists( 'create_function_text_table' ) ):
function create_function_text_table() {
   global $wpdb;
   //_v('$wpdb');
   $sql = "";
   $charset_collate = "";

   // 接頭辞の追加（socal_count_cache）
   $table_name = $wpdb->prefix . 'function_texts';

   // charsetを指定する
   if ( !empty($wpdb->charset) )
      $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} ";

   // 照合順序を指定する（ある場合。通常デフォルトのutf8_general_ci）
   if ( !empty($wpdb->collate) )
      $charset_collate .= "COLLATE {$wpdb->collate}";

    // SQL文でテーブルを作る
    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      text text NOT NULL,
      #categories varchar(200),
      PRIMARY KEY (id)
    ) $charset_collate;";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   _v($sql);
   $res = dbDelta( $sql );
   _v($res);
}
endif;
create_function_text_table();