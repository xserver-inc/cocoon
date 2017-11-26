<?php //関数テキスト関係の関数

function create_function_text_table() {
   global $wpdb;

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
   $sql = "
      CREATE TABLE {$table_name} (
        text TEXT,
        categories VARCHAR(64),
        date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
        modified datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY  (postid)
      ) {$charset_collate};";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
}