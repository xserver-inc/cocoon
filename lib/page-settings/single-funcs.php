<?php //投稿設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;
///////////////////////////////////////
// 関連記事
///////////////////////////////////////

//カテゴリー・タグ表示タイプ
define('OP_CATEGORY_TAG_DISPLAY_TYPE', 'category_tag_display_type');
if ( !function_exists( 'get_category_tag_display_type' ) ):
function get_category_tag_display_type(){
  return get_theme_option(OP_CATEGORY_TAG_DISPLAY_TYPE, 'one_row');
}
endif;

//カテゴリー・タグ表示位置
define('OP_CATEGORY_TAG_DISPLAY_POSITION', 'category_tag_display_position');
if ( !function_exists( 'get_category_tag_display_position' ) ):
function get_category_tag_display_position(){
  return get_theme_option(OP_CATEGORY_TAG_DISPLAY_POSITION, 'content_bottom');
}
endif;
if ( !function_exists( 'is_category_tag_display_position_title_top' ) ):
function is_category_tag_display_position_title_top(){
  return get_category_tag_display_position() == 'title_top';
}
endif;
if ( !function_exists( 'is_category_tag_display_position_content_top' ) ):
function is_category_tag_display_position_content_top(){
  return get_category_tag_display_position() == 'content_top';
}
endif;
if ( !function_exists( 'is_category_tag_display_position_content_bottom' ) ):
function is_category_tag_display_position_content_bottom(){
  return get_category_tag_display_position() == 'content_bottom';
}
endif;

//関連記事の表示
define('OP_RELATED_ENTRIES_VISIBLE', 'related_entries_visible');
if ( !function_exists( 'is_related_entries_visible' ) ):
function is_related_entries_visible(){
  return get_theme_option(OP_RELATED_ENTRIES_VISIBLE, 1);
}
endif;

//関連記事の関連性
define('OP_RELATED_ASSOCIATION_TYPE', 'related_association_type');
if ( !function_exists( 'get_related_association_type' ) ):
function get_related_association_type(){
  return get_theme_option(OP_RELATED_ASSOCIATION_TYPE, 'category');
}
endif;
//関連記事がカテゴリーに関連付けられているか
if ( !function_exists( 'is_related_association_type_category' ) ):
function is_related_association_type_category(){
  return get_related_association_type() == 'category';
}
endif;
//関連記事がタグに関連付けられているか
if ( !function_exists( 'is_related_association_type_tag' ) ):
function is_related_association_type_tag(){
  return get_related_association_type() == 'tag';
}
endif;

//関連記事の見出し
define('OP_RELATED_ENTRY_HEADING', 'related_entry_heading');
if ( !function_exists( 'get_related_entry_heading' ) ):
function get_related_entry_heading(){
  return stripslashes_deep(get_theme_option(OP_RELATED_ENTRY_HEADING, __( '関連記事', THEME_NAME )));
}
endif;

//関連記事のサブ見出し
define('OP_RELATED_ENTRY_SUB_HEADING', 'related_entry_sub_heading');
if ( !function_exists( 'get_related_entry_sub_heading' ) ):
function get_related_entry_sub_heading(){
  return stripslashes_deep(get_theme_option(OP_RELATED_ENTRY_SUB_HEADING));
}
endif;

//関連記事の表示タイプ
define('OP_RELATED_ENTRY_TYPE', 'related_entry_type');
if ( !function_exists( 'get_related_entry_type' ) ):
function get_related_entry_type(){
  $res = get_theme_option(OP_RELATED_ENTRY_TYPE, 'entry_card');
  $res = str_replace('vartical', 'vertical', $res);
  return $res;
}
endif;

//関連記事の表示数
define('OP_RELATED_ENTRY_COUNT', 'related_entry_count');
if ( !function_exists( 'get_related_entry_count' ) ):
function get_related_entry_count(){
  return get_theme_option(OP_RELATED_ENTRY_COUNT, 6);
}
endif;

//関連記事取得期間
define('OP_RELATED_ENTRY_PERIOD', 'related_entry_period');
if ( !function_exists( 'get_related_entry_period' ) ):
function get_related_entry_period(){
  return get_theme_option(OP_RELATED_ENTRY_PERIOD);
}
endif;

//関連記事枠線の表示
define('OP_RELATED_ENTRY_BORDER_VISIBLE', 'related_entry_border_visible');
if ( !function_exists( 'is_related_entry_border_visible' ) ):
function is_related_entry_border_visible(){
  return get_theme_option(OP_RELATED_ENTRY_BORDER_VISIBLE);
}
endif;

//関連記事抜粋文の最大文字数
define('OP_RELATED_EXCERPT_MAX_LENGTH', 'related_excerpt_max_length');
if ( !function_exists( 'get_related_excerpt_max_length' ) ):
function get_related_excerpt_max_length(){
  $max_length = get_theme_option(OP_RELATED_EXCERPT_MAX_LENGTH, 120);
  if ((intval($max_length) < 1) || (intval($max_length) > 500)) {
    $max_length = '';
  }
  return $max_length;
}
endif;

//スニペットを表示
define('OP_RELATED_ENTRY_CARD_SNIPPET_VISIBLE', 'related_entry_card_snippet_visible');
if ( !function_exists( 'is_related_entry_card_snippet_visible' ) ):
function is_related_entry_card_snippet_visible(){
  return get_theme_option(OP_RELATED_ENTRY_CARD_SNIPPET_VISIBLE, 1);
}
endif;

//スマートフォンスニペット表示
define('OP_SMARTPHONE_RELATED_ENTRY_CARD_SNIPPET_VISIBLE', 'smartphone_related_entry_card_snippet_visible');
if ( !function_exists( 'is_smartphone_related_entry_card_snippet_visible' ) ):
function is_smartphone_related_entry_card_snippet_visible(){
  return get_theme_option(OP_SMARTPHONE_RELATED_ENTRY_CARD_SNIPPET_VISIBLE, 1);
}
endif;

//投稿日を表示
define('OP_RELATED_ENTRY_CARD_POST_DATE_VISIBLE', 'related_entry_card_post_date_visible');
if ( !function_exists( 'is_related_entry_card_post_date_visible' ) ):
function is_related_entry_card_post_date_visible(){
  return get_theme_option(OP_RELATED_ENTRY_CARD_POST_DATE_VISIBLE);
}
endif;

//投稿日を表示しない場合、更新日がなければ投稿日を表示
define('OP_RELATED_ENTRY_CARD_POST_DATE_OR_UPDATE_VISIBLE', 'related_entry_card_post_date_or_update_visible');
if ( !function_exists( 'is_related_entry_card_post_date_or_update_visible' ) ):
function is_related_entry_card_post_date_or_update_visible(){
  // return get_theme_option(OP_RELATED_ENTRY_CARD_POST_DATE_OR_UPDATE_VISIBLE, 1);
  //ややこしいので表示にして廃止
  return true;
}
endif;

//更新日を表示
define('OP_RELATED_ENTRY_CARD_POST_UPDATE_VISIBLE', 'related_entry_card_post_update_visible');
if ( !function_exists( 'is_related_entry_card_post_update_visible' ) ):
function is_related_entry_card_post_update_visible(){
  return get_theme_option(OP_RELATED_ENTRY_CARD_POST_UPDATE_VISIBLE);
}
endif;

//投稿者を表示
define('OP_RELATED_ENTRY_CARD_POST_AUTHOR_VISIBLE', 'related_entry_card_post_author_visible');
if ( !function_exists( 'is_related_entry_card_post_author_visible' ) ):
function is_related_entry_card_post_author_visible(){
  return get_theme_option(OP_RELATED_ENTRY_CARD_POST_AUTHOR_VISIBLE);
}
endif;
///////////////////////////////////////
// ページ送りナビ
///////////////////////////////////////

//ページ送りナビの表示
define('OP_POST_NAVI_VISIBLE', 'post_navi_visible');
if ( !function_exists( 'is_post_navi_visible' ) ):
function is_post_navi_visible(){
  return get_theme_option(OP_POST_NAVI_VISIBLE, 1);
}
endif;

//ページ送りナビの表示タイプ
define('OP_POST_NAVI_TYPE', 'post_navi_type');
if ( !function_exists( 'get_post_navi_type' ) ):
function get_post_navi_type(){
  return get_theme_option(OP_POST_NAVI_TYPE, 'default');
}
endif;
if ( !function_exists( 'is_post_navi_type_spuare' ) ):
function is_post_navi_type_spuare(){
  return get_post_navi_type() == 'square';
}
endif;

//ページ送りナビの表示位置
define('OP_POST_NAVI_POSITION', 'post_navi_position');
if ( !function_exists( 'get_post_navi_position' ) ):
function get_post_navi_position(){
  return get_theme_option(OP_POST_NAVI_POSITION, 'under_related');
}
endif;
if ( !function_exists( 'is_post_navi_position_under_content' ) ):
function is_post_navi_position_under_content(){
  return get_post_navi_position() == 'under_content';
}
endif;
if ( !function_exists( 'is_post_navi_position_over_related' ) ):
function is_post_navi_position_over_related(){
  return get_post_navi_position() == 'over_related';
}
endif;
if ( !function_exists( 'is_post_navi_position_under_related' ) ):
function is_post_navi_position_under_related(){
  return get_post_navi_position() == 'under_related';
}
endif;
if ( !function_exists( 'is_post_navi_position_under_comment' ) ):
function is_post_navi_position_under_comment(){
  return get_post_navi_position() == 'under_comment';
}
endif;

//ページ送りナビに表示するカテゴリーは同一にする
define('OP_POST_NAVI_SAME_CATEGORY_ENABLE', 'post_navi_same_category_enable');
if ( !function_exists( 'is_post_navi_same_category_enable' ) ):
function is_post_navi_same_category_enable(){
  return get_theme_option(OP_POST_NAVI_SAME_CATEGORY_ENABLE);
}
endif;

//ページ送りナビで除外するカテゴリーID
define('OP_POST_NAVI_EXCLUDE_CATEGORY_IDS', 'post_navi_exclude_category_ids');
if ( !function_exists( 'get_post_navi_exclude_category_ids' ) ):
function get_post_navi_exclude_category_ids(){
  return get_theme_option(OP_POST_NAVI_EXCLUDE_CATEGORY_IDS, array());
}
endif;

//ページ送りナビ枠線の表示
define('OP_POST_NAVI_BORDER_VISIBLE', 'post_navi_border_visible');
if ( !function_exists( 'is_post_navi_border_visible' ) ):
function is_post_navi_border_visible(){
  return get_theme_option(OP_POST_NAVI_BORDER_VISIBLE);
}
endif;

///////////////////////////////////////
// コメント
///////////////////////////////////////

//コメント表示
define('OP_SINGLE_COMMENT_VISIBLE', 'single_comment_visible');
if ( !function_exists( 'is_single_comment_visible' ) ):
function is_single_comment_visible(){
  return get_theme_option(OP_SINGLE_COMMENT_VISIBLE, 1);
}
endif;

///////////////////////////////////////
// パンくずリスト
//////////////////////////////////////

//パンくずリストの位置
define('OP_SINGLE_BREADCRUMBS_POSITION', 'single_breadcrumbs_position');
if ( !function_exists( 'get_single_breadcrumbs_position' ) ):
function get_single_breadcrumbs_position(){
  return get_theme_option(OP_SINGLE_BREADCRUMBS_POSITION, 'main_bottom');
}
endif;
if ( !function_exists( 'is_single_breadcrumbs_visible' ) ):
function is_single_breadcrumbs_visible(){
  return get_single_breadcrumbs_position() != 'none';
}
endif;
if ( !function_exists( 'is_single_breadcrumbs_position_main_before' ) ):
function is_single_breadcrumbs_position_main_before(){
  return get_single_breadcrumbs_position() == 'main_before';
}
endif;
if ( !function_exists( 'is_single_breadcrumbs_position_main_top' ) ):
function is_single_breadcrumbs_position_main_top(){
  return get_single_breadcrumbs_position() == 'main_top';
}
endif;
if ( !function_exists( 'is_single_breadcrumbs_position_main_bottom' ) ):
function is_single_breadcrumbs_position_main_bottom(){
  return get_single_breadcrumbs_position() == 'main_bottom';
}
endif;
if ( !function_exists( 'is_single_breadcrumbs_position_footer_before' ) ):
function is_single_breadcrumbs_position_footer_before(){
  return get_single_breadcrumbs_position() == 'footer_before';
}
endif;

//パンくずリストに当該記事を含めるか
define('OP_SINGLE_BREADCRUMBS_INCLUDE_POST', 'single_breadcrumbs_include_post');
if ( !function_exists( 'is_single_breadcrumbs_include_post' ) ):
function is_single_breadcrumbs_include_post(){
  return get_theme_option(OP_SINGLE_BREADCRUMBS_INCLUDE_POST);
}
endif;
