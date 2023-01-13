<?php //その他設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//簡単SSL対応
update_theme_option(OP_EASY_SSL_ENABLE);

//ファイルシステム認証を有効にする
update_theme_option(OP_REQUEST_FILESYSTEM_CREDENTIALS_ENABLE);

//Simplicityから設定情報の移行
update_theme_option(OP_MIGRATE_FROM_SIMPLICITY);

//スラッグが日本語の時はpost-XXXXのような連番形式にする
update_theme_option(OP_AUTO_POST_SLUG_ENABLE);

///////////////////////////////////////////
// JavaScriptライブラリ
///////////////////////////////////////////

// //jQueryのバージョン
// update_theme_option(OP_JQUERY_VERSION);

// //jQueryのバージョン
// update_theme_option(OP_JQUERY_MIGRATE_VERSION);
