<?php //タイトル設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//外部ブログカードが有効
update_theme_option(OP_EXTERNAL_BLOGCARD_ENABLE);

//外部ブログカードのサムネイル設定
update_theme_option(OP_EXTERNAL_BLOGCARD_THUMBNAIL_STYLE);

//外部ブログカードを新しいタブで開くか
update_theme_option(OP_EXTERNAL_BLOGCARD_TARGET_BLANK);

//外部ブログカードキャッシュは保存期間
update_theme_option(OP_EXTERNAL_BLOGCARD_CACHE_RETENTION_PERIOD);

//外部ブログカードがリフレッシュモードか
update_theme_option(OP_EXTERNAL_BLOGCARD_REFRESH_MODE);
