<?php //定数をまとめて定義

//テーマ名
define('THEME_NAME', 'Cocoon');
//ウィジェット名プレフィックス
define('WIDGET_NAME_PLEFIX', '[C] ');
//トップレベルオリジナル設定名
define('SETTING_NAME_TOP', THEME_NAME.__( ' 設定', THEME_NAME ));

//URLの正規表現
define('URL_REG', '/(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)/');

//Font AwesomeのCDN
define('FONT_AWESOME_CDN_URL', 'https://max'.'cdn.boot'.'strapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');


require_once ABSPATH.'wp-admin/includes/file.php';//WP_Filesystemの使用
require_once 'settings.php'; //Wordpressの設定
require_once 'seo.php';      //SEO関係の設定
require_once 'scripts.php'; //スクリプト関係の関数
require_once 'utils.php'; //Wordpressの設定
require_once 'widget-areas.php'; //ウィジェットエリアの指定
require_once 'widget.php'; //ウィジェット操作関数
require_once 'admin.php'; //管理者機能
require_once 'original-menu.php'; //オリジナルメニューによる設定項目