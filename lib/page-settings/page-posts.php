<?php //固定ページ設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// コメント
///////////////////////////////////////

//コメント表示
update_theme_option(OP_PAGE_COMMENT_VISIBLE);

///////////////////////////////////////
// パンくずリスト
//////////////////////////////////////

//パンくずリストの位置
update_theme_option(OP_PAGE_BREADCRUMBS_POSITION);

//パンくずリストに当該記事を含めるか
update_theme_option(OP_PAGE_BREADCRUMBS_INCLUDE_POST);
