<?php //タイトル設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//内部ブログカードが有効
update_theme_option(OP_INTERNAL_BLOGCARD_ENABLE);

//内部ブログカードのサムネイル設定
update_theme_option(OP_INTERNAL_BLOGCARD_THUMBNAIL_STYLE);

//内部ブログカードの月表示
update_theme_option(OP_INTERNAL_BLOGCARD_DATE_TYPE);

//内部ブログカードを新しいタブで開くか
update_theme_option(OP_INTERNAL_BLOGCARD_TARGET_BLANK);
