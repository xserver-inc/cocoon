<?php //アクセス集計設定保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//アクセス数を取得するか
update_theme_option(OP_ACCESS_COUNT_ENABLE);

//アクセス数のキャッシュ有効
update_theme_option(OP_ACCESS_COUNT_CACHE_ENABLE);

//アクセス数のキャッシュインターバル（分）
update_theme_option(OP_ACCESS_COUNT_CACHE_INTERVAL);
