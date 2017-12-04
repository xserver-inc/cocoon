<?php //吹き出し関係の関数

//関数テキストテーブルのバージョン
define('SPEECH_BALLOON_TABLE_VERSION', '0.0');
define('SPEECH_BALLOON_TABLE_NAME',  $wpdb->prefix . THEME_NAME . '_speech_balloons');

//関数テキスト移動用URL
define('SB_LIST_URL',   add_query_arg(array('action' => false,   'id' => false)));
define('SB_NEW_URL',    add_query_arg(array('action' => 'new',   'id' => false)));