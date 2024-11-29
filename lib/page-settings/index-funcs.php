<?php //インデックス設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// リスト表示
///////////////////////////////////////

//フロントページタイプ
define('OP_FRONT_PAGE_TYPE', 'front_page_type');
if ( !function_exists( 'get_front_page_type' ) ):
function get_front_page_type(){
  return get_theme_option(OP_FRONT_PAGE_TYPE, 'index');
}
endif;
if ( !function_exists( 'is_front_page_type_index' ) ):
function is_front_page_type_index(){
  return get_front_page_type() == 'index';
}
endif;
if ( !function_exists( 'is_front_page_type_tab_index' ) ):
function is_front_page_type_tab_index(){
  return get_front_page_type() == 'tab_index';
}
endif;
if ( !function_exists( 'is_front_page_type_category' ) ):
function is_front_page_type_category(){
  return get_front_page_type() == 'category';
}
endif;
if ( !function_exists( 'is_front_page_type_category_2_columns' ) ):
function is_front_page_type_category_2_columns(){
  return get_front_page_type() == 'category_2_columns';
}
endif;
if ( !function_exists( 'is_front_page_type_category_3_columns' ) ):
function is_front_page_type_category_3_columns(){
  return get_front_page_type() == 'category_3_columns';
}
endif;

//タブインデックスカテゴリー
define('OP_INDEX_CATEGORY_IDS', 'index_category_ids');
if ( !function_exists( 'get_index_category_ids' ) ):
function get_index_category_ids(){
  return get_theme_option(OP_INDEX_CATEGORY_IDS, array());
}
endif;

//インデックス新着エントリーカード数
define('OP_INDEX_NEW_ENTRY_CARD_COUNT', 'index_new_entry_card_count');
if ( !function_exists( 'get_index_new_entry_card_count' ) ):
function get_index_new_entry_card_count(){
  return get_theme_option(OP_INDEX_NEW_ENTRY_CARD_COUNT, 4);
}
endif;

//インデックスカテゴリーエントリーカード数
define('OP_INDEX_CATEGORY_ENTRY_CARD_COUNT', 'index_category_entry_card_count');
if ( !function_exists( 'get_index_category_entry_card_count' ) ):
function get_index_category_entry_card_count(){
  return get_theme_option(OP_INDEX_CATEGORY_ENTRY_CARD_COUNT, 4);
}
endif;

//タブインデックスページかどうか
//紛らわしいのでis_front_top_page関数に統一。is_front_index_pageはエイリアスとして復活
if ( !function_exists( 'is_front_index_page' ) ):
function is_front_index_page(){
  return is_front_top_page();
}
endif;

//タブインデックスカテゴリー（カンマテキスト）
define('OP_INDEX_CATEGORY_IDS_COMMA_TEXT', 'index_category_ids_comma_text');
if ( !function_exists( 'get_index_category_ids_comma_text' ) ):
function get_index_category_ids_comma_text(){
  return get_theme_option(OP_INDEX_CATEGORY_IDS_COMMA_TEXT);
}
endif;

//インデックスの並び順
define('OP_INDEX_SORT_ORDERBY', 'index_sort_orderby');
if ( !function_exists( 'get_index_sort_orderby' ) ):
function get_index_sort_orderby(){
  return get_theme_option(OP_INDEX_SORT_ORDERBY);
}
endif;
//インデックスの並び順は投稿日順か
if ( !function_exists( 'is_index_sort_orderby_date' ) ):
function is_index_sort_orderby_date(){
  return get_index_sort_orderby() === '';
}
endif;
//インデックスの並び順は更新日順か
if ( !function_exists( 'is_index_sort_orderby_modified' ) ):
function is_index_sort_orderby_modified(){
  return get_index_sort_orderby() === 'modified';
}
endif;
//インデックスの並び順はランダムか
if ( !function_exists( 'is_index_sort_orderby_ramdom' ) ):
function is_index_sort_orderby_ramdom(){
  return get_index_sort_orderby() === 'rand';
}
endif;

//エントリーカードタイプ
define('OP_ENTRY_CARD_TYPE', 'entry_card_type');
if ( !function_exists( 'get_entry_card_type' ) ):
function get_entry_card_type(){
  return get_theme_option(OP_ENTRY_CARD_TYPE, 'entry_card');
}
endif;
if ( !function_exists( 'is_entry_card_type_entry_card' ) ):
function is_entry_card_type_entry_card(){
  return get_entry_card_type() == 'entry_card';
}
endif;
if ( !function_exists( 'is_entry_card_type_big_card_first' ) ):
function is_entry_card_type_big_card_first(){
  return get_entry_card_type() == 'big_card_first';
}
endif;
if ( !function_exists( 'is_entry_card_type_big_card' ) ):
function is_entry_card_type_big_card(){
  return get_entry_card_type() == 'big_card';
}
endif;
if ( !function_exists( 'is_entry_card_type_big_card' ) ):
function is_entry_card_type_big_card(){
  return get_entry_card_type() == 'big_card';
}
endif;
if ( !function_exists( 'is_entry_card_type_vertical_card_2' ) ):
function is_entry_card_type_vertical_card_2(){
  return get_entry_card_type() == 'vertical_card_2';
}
endif;
if ( !function_exists( 'is_entry_card_type_vertical_card_3' ) ):
function is_entry_card_type_vertical_card_3(){
  return get_entry_card_type() == 'vertical_card_3';
}
endif;
if ( !function_exists( 'is_entry_card_type_tile_card_2' ) ):
function is_entry_card_type_tile_card_2(){
  return get_entry_card_type() == 'tile_card_2';
}
endif;
if ( !function_exists( 'is_entry_card_type_tile_card_3' ) ):
function is_entry_card_type_tile_card_3(){
  return get_entry_card_type() == 'tile_card_3';
}
endif;
if ( !function_exists( 'is_entry_card_type_tile_card' ) ):
function is_entry_card_type_tile_card(){
  return (get_entry_card_type() == 'tile_card_2') || (get_entry_card_type() == 'tile_card_3');
}
endif;
if ( !function_exists( 'is_entry_card_type_2_columns' ) ):
function is_entry_card_type_2_columns(){
  return (get_entry_card_type() == 'vertical_card_2') || (get_entry_card_type() == 'tile_card_2');
}
endif;
if ( !function_exists( 'is_entry_card_type_3_columns' ) ):
function is_entry_card_type_3_columns(){
  return (get_entry_card_type() == 'vertical_card_3') || (get_entry_card_type() == 'tile_card_3');
}
endif;

//スマートフォンのエントリーカードを1カラムに
define('OP_SMARTPHONE_ENTRY_CARD_1_COLUMN', 'smartphone_entry_card_1_column');
if ( !function_exists( 'is_smartphone_entry_card_1_column' ) ):
function is_smartphone_entry_card_1_column(){
  return get_theme_option(OP_SMARTPHONE_ENTRY_CARD_1_COLUMN);
}
endif;

//エントリーカード枠線の表示
define('OP_ENTRY_CARD_BORDER_VISIBLE', 'entry_card_border_visible');
if ( !function_exists( 'is_entry_card_border_visible' ) ):
function is_entry_card_border_visible(){
  return get_theme_option(OP_ENTRY_CARD_BORDER_VISIBLE);
}
endif;

//エントリーカード抜粋文の最大文字数
define('OP_ENTRY_CARD_EXCERPT_MAX_LENGTH', 'entry_card_excerpt_max_length');
if ( !function_exists( 'get_entry_card_excerpt_max_length' ) ):
function get_entry_card_excerpt_max_length(){
  $max_length = get_theme_option(OP_ENTRY_CARD_EXCERPT_MAX_LENGTH, 120);
  if ((intval($max_length) < 1) || (intval($max_length) > 500)) {
    $max_length = '';
  }
  return $max_length;
}
endif;

//エントリーカード抜粋文の最大文字数を超えたときの文字列
define('OP_ENTRY_CARD_EXCERPT_MORE', 'entry_card_excerpt_more');
if ( !function_exists( 'get_entry_card_excerpt_more' ) ):
function get_entry_card_excerpt_more(){
  return get_theme_option(OP_ENTRY_CARD_EXCERPT_MORE, __( '...', THEME_NAME ));
}
endif;


//スニペットを表示
define('OP_ENTRY_CARD_SNIPPET_VISIBLE', 'entry_card_snippet_visible');
if ( !function_exists( 'is_entry_card_snippet_visible' ) ):
function is_entry_card_snippet_visible(){
  return get_theme_option(OP_ENTRY_CARD_SNIPPET_VISIBLE, 1);
}
endif;

//スマートフォンスニペット表示
define('OP_SMARTPHONE_ENTRY_CARD_SNIPPET_VISIBLE', 'smartphone_entry_card_snippet_visible');
if ( !function_exists( 'is_smartphone_entry_card_snippet_visible' ) ):
function is_smartphone_entry_card_snippet_visible(){
  return get_theme_option(OP_SMARTPHONE_ENTRY_CARD_SNIPPET_VISIBLE);
}
endif;

//投稿日を表示
define('OP_ENTRY_CARD_POST_DATE_VISIBLE', 'entry_card_post_date_visible');
if ( !function_exists( 'is_entry_card_post_date_visible' ) ):
function is_entry_card_post_date_visible(){
  return get_theme_option(OP_ENTRY_CARD_POST_DATE_VISIBLE, 1);
}
endif;

//投稿日を表示しない場合、更新日がなければ投稿日を表示
define('OP_ENTRY_CARD_POST_DATE_OR_UPDATE_VISIBLE', 'entry_card_post_date_or_update_visible');
if ( !function_exists( 'is_entry_card_post_date_or_update_visible' ) ):
function is_entry_card_post_date_or_update_visible(){
  // return get_theme_option(OP_ENTRY_CARD_POST_DATE_OR_UPDATE_VISIBLE, 1);
  //ややこしいので表示にして廃止
  return true;
}
endif;

//更新日を表示
define('OP_ENTRY_CARD_POST_UPDATE_VISIBLE', 'entry_card_post_update_visible');
if ( !function_exists( 'is_entry_card_post_update_visible' ) ):
function is_entry_card_post_update_visible(){
  return get_theme_option(OP_ENTRY_CARD_POST_UPDATE_VISIBLE);
}
endif;

//投稿者を表示
define('OP_ENTRY_CARD_POST_AUTHOR_VISIBLE', 'entry_card_post_author_visible');
if ( !function_exists( 'is_entry_card_post_author_visible' ) ):
function is_entry_card_post_author_visible(){
  return get_theme_option(OP_ENTRY_CARD_POST_AUTHOR_VISIBLE);
}
endif;

//コメント数を表示
define('OP_ENTRY_CARD_POST_COMMENT_COUNT_VISIBLE', 'entry_card_post_comment_count_visible');
if ( !function_exists( 'is_entry_card_post_comment_count_visible' ) ):
function is_entry_card_post_comment_count_visible(){
  return get_theme_option(OP_ENTRY_CARD_POST_COMMENT_COUNT_VISIBLE);
}
endif;

//インデックスリストに表示しないカテゴリーID
define('OP_ARCHIVE_EXCLUDE_CATEGORY_IDS', 'archive_exclude_category_ids');
if ( !function_exists( 'get_archive_exclude_category_ids' ) ):
function get_archive_exclude_category_ids(){
  return get_theme_option(OP_ARCHIVE_EXCLUDE_CATEGORY_IDS, array());
}
endif;

//フォントページタイプ用のカテゴリーID取得
if ( !function_exists( 'get_index_list_category_ids' ) ):
function get_index_list_category_ids(){
  //チェックリストのカテゴリーを読み込む
  $cat_ids = get_index_category_ids();
  //順番を変更したい場合はカンマテキストのほうを読み込む
  $cat_comma = trim(get_index_category_ids_comma_text());
  if ($cat_comma) {
    $cat_ids = explode(',', $cat_comma);
  }
  if (!is_array($cat_ids)) {
    $cat_ids = array();
  }
  //カテゴリーをPHP独自カスタマイズで制御したい人用のフック
  $cat_ids = apply_filters('get_index_list_category_ids', $cat_ids);
  return $cat_ids;
}
endif;

//インデックスリストのクラス取得
if ( !function_exists( 'get_index_list_classes' ) ):
function get_index_list_classes(){
  //インデクスリスト用のクラス
  $list_classes = 'list'.get_additional_entry_card_classes();
  //タブインデックスのクラス名をPHP独自カスタマイズで制御したい人用のフック
  $list_classes = apply_filters('get_index_list_classes', $list_classes);
  return $list_classes;
}
endif;

//インデックスエントリーカードの取得
if ( !function_exists( 'get_category_index_list_entry_card_tag' ) ):
function get_category_index_list_entry_card_tag($categories, $count){
  ob_start();
  $args = array(
    'posts_per_page' => $count,
    'ignore_sticky_posts' => true,
    //アーカイブに表示しないページのID
    'post__not_in' =>  get_archive_exclude_post_ids(),
  );
  if ($categories) {
    $args += array(
      'cat' => $categories,
    );
  }

  //順番変更
  if (!is_index_sort_orderby_date()) {
    //投稿日順じゃないときは設定値を挿入する
    $args += array(
      'orderby' => get_index_sort_orderby(),
    );
  }

  //カテゴリーの除外
  $exclude_category_ids = get_archive_exclude_category_ids();
  if (!$categories && $exclude_category_ids && is_array($exclude_category_ids)) {
    $args += array(
      'category__not_in' => $exclude_category_ids,
    );
  }
  $args = apply_filters('get_category_index_list_entry_card_args', $args, $categories);
  $query = new WP_Query( $args );
  $count = 0;
  ////////////////////////////
  //一覧の繰り返し処理
  ////////////////////////////
  if ($query->have_posts()) { //投稿があるとき
    while ($query->have_posts()) {
      $query->the_post(); // 繰り返し処理開始
      $count++;
      //エントリーカウントをテンプレートファイルに渡す
      set_query_var('count', $count);
      cocoon_template_part('tmp/entry-card');
    } // 繰り返し処理終了
    $count = 0;
  } else { // ここから記事が見つからなかった場合の処理
    cocoon_template_part('tmp/list-not-found-posts');
  }
  wp_reset_postdata();
  return ob_get_clean();
}
endif;


