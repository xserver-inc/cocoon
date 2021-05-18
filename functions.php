<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//ファイルのディレクトリパスを取得する（最後の/付き）
if ( !function_exists( 'abspath' ) ):
function abspath($file){return dirname($file).'/';}
endif;

require_once abspath(__FILE__).'lib/_defins.php'; //定数を定義

//アップデートチェックの初期化
require abspath(__FILE__).'lib/theme-update-checker.php'; //ライブラリのパス
$example_update_checker = new ThemeUpdateChecker(
  strtolower(THEME_PARENT_DIR), //テーマフォルダ名
  'https://raw.githubusercontent.com/yhira/cocoon/master/update-info.json' //JSONファイルのURL
);

//本文部分の冒頭を綺麗に抜粋する
if ( !function_exists( 'get_content_excerpt' ) ):
function get_content_excerpt($content, $length = 70){
  $content = apply_filters( 'content_excerpt_before', $content);
  $content = cancel_blog_card_deactivation($content, false);
  $content = preg_replace('/<!--more-->.+/is', '', $content); //moreタグ以降削除
  $content = strip_tags($content);//タグの除去
  $content = strip_shortcodes($content);//ショートコード削除
  $content = str_replace('&nbsp;', '', $content);//特殊文字の削除（今回はスペースのみ）
  $content = preg_replace('/\[.+?\]/i', '', $content); //ショートコードを取り除く
  $content = preg_replace(URL_REG, '', $content); //URLを取り除く

  //$lengthが整数じゃなかった場合の処理
  if (is_int(intval($length))) {
    $length = intval($length);
  } else {
    $length = 70;
  }
  $over    = intval(mb_strlen($content)) > $length;
  $content = mb_substr($content, 0, $length);//文字列を指定した長さで切り取る
  if ( $over && $more = get_entry_card_excerpt_more() ) {
    $content = $content.$more;
  }
  $content = esc_html($content);

  $content = apply_filters( 'content_excerpt_after', $content);

  return $content;
}
endif;

//images/no-image.pngを使用するimgタグに出力するサイズ関係の属性
if ( !function_exists( 'get_noimage_sizes_attr' ) ):
function get_noimage_sizes_attr($image = null){
  if (!$image) {
    $image = get_no_image_160x90_url();
  }
  $w = THUMB160WIDTH;
  $h = THUMB160HEIGHT;
  $sizes = ' srcset="'.$image.' '.$w.'w" width="'.$w.'" height="'.$h.'" sizes="(max-width: '.$w.'px) '.$w.'vw, '.$h.'px"';
  return $sizes;
}
endif;

//投稿ナビのサムネイルタグを取得する
if ( !function_exists( 'get_post_navi_thumbnail_tag' ) ):
function get_post_navi_thumbnail_tag($id, $width = THUMB120WIDTH, $height = THUMB120HEIGHT){
  $thumbnail_size = 'thumb'.strval($width);
  $thumbnail_size = apply_filters('get_post_navi_thumbnail_size', $thumbnail_size);
  $thumb = get_the_post_thumbnail( $id, $thumbnail_size, array('alt' => '') );
  if ( !$thumb ) {
    $image = get_template_directory_uri().'/images/no-image-%s.png';

    //表示タイプ＝デフォルト
    if ($width == THUMB120WIDTH) {
      $w = THUMB120WIDTH;
      $h = THUMB120HEIGHT;
      $image = get_no_image_160x90_url($id);
    } else {//表示タイプ＝スクエア
      $image = get_no_image_150x150_url($id);
      $w = THUMB150WIDTH;
      $h = THUMB150HEIGHT;
    }
    $thumb = get_original_image_tag($image, $w, $h, 'no-image post-navi-no-image');
  }
  return $thumb;
}
endif;

//アーカイブタイトルの取得
if ( !function_exists( 'get_archive_chapter_title' ) ):
function get_archive_chapter_title(){
  $chapter_title = null;
  if( is_category() ) {//カテゴリページの場合
    $cat_id = get_query_var('cat');
    $icon_font = '<span class="fa fa-folder-open" aria-hidden="true"></span>';
    if ($cat_id && get_the_category_title($cat_id)) {
      $chapter_title .= $icon_font.get_the_category_title($cat_id);
    } else {
      $chapter_title .= single_cat_title( $icon_font, false );
    }
  } elseif( is_tag() ) {//タグページの場合
    $tag_id = get_queried_object_id();
    $icon_font = '<span class="fa fa-tags" aria-hidden="true"></span>';
    if ($tag_id && get_the_tag_title($tag_id)) {
      $chapter_title .= $icon_font.get_the_tag_title($tag_id);
    } else {
      $chapter_title .= single_tag_title( $icon_font, false );
    }
  } elseif( is_tax() ) {//タクソノミページの場合
    $chapter_title .= single_term_title( '', false );
  } elseif( is_search() ) {//検索結果
    $search_query = trim(strip_tags(get_search_query()));
    if (empty($search_query)) {
      $search_query = __( 'キーワード指定なし', THEME_NAME );
    }
    $chapter_title .= '<span class="fa fa-search" aria-hidden="true"></span>"'.$search_query.'"';
  } elseif (is_day()) {
    //年月日のフォーマットを取得
    $chapter_title .= '<span class="fa fa-calendar" aria-hidden="true"></span>'.get_the_time('Y-m-d');
  } elseif (is_month()) {
    //年と月のフォーマットを取得
    $chapter_title .= '<span class="fa fa-calendar" aria-hidden="true"></span>'.get_the_time('Y-m');
  } elseif (is_year()) {
    //年のフォーマットを取得
    $chapter_title .= '<span class="fa fa-calendar" aria-hidden="true"></span>'.get_the_time('Y');
  } elseif (is_author()) {//著書ページの場合
    $chapter_title .= '<span class="fa fa-user" aria-hidden="true"></span>'.esc_html(get_the_author_meta( 'display_name', $author ));
  } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
    $chapter_title .= 'Archives';
  } else {
    $chapter_title .= 'Archives';
  }
  return apply_filters('get_archive_chapter_title', $chapter_title);
}
endif;

//アーカイブ見出しの取得
if ( !function_exists( 'get_archive_chapter_text' ) ):
function get_archive_chapter_text(){
  $chapter_text = null;

  //アーカイブタイトルの取得
  $chapter_text .= get_archive_chapter_title();

  //返り値として返す
  return $chapter_text;
}
endif;

//'wp-color-picker'の呼び出し順操作（最初の方に読み込む）
add_action('admin_enqueue_scripts', 'admin_enqueue_scripts_custom');
if ( !function_exists( 'admin_enqueue_scripts_custom' ) ):
function admin_enqueue_scripts_custom($hook) {
  wp_enqueue_script('colorpicker-script', get_template_directory_uri() . '/js/color-picker.js', array( 'wp-color-picker' ), false, true);
}
endif;

//投稿管理画面のカテゴリリストの階層を保つ
add_filter('wp_terms_checklist_args', 'solecolor_wp_terms_checklist_args', 10, 2);
if ( !function_exists( 'solecolor_wp_terms_checklist_args' ) ):
function solecolor_wp_terms_checklist_args( $args, $post_id ){
 if ( isset($args['checked_ontop']) && ($args['checked_ontop'] !== false )){
    $args['checked_ontop'] = false;
 }
 return $args;
}
endif;

//リダイレクト
add_action( 'wp','wp_singular_page_redirect', 0 );
if ( !function_exists( 'wp_singular_page_redirect' ) ):
function wp_singular_page_redirect() {
  //リダイレクト
  if (is_singular() && $redirect_url = get_singular_redirect_url()) {
    //URL形式にマッチする場合
    if (preg_match(URL_REG, $redirect_url)) {
      redirect_to_url($redirect_url);
    }
  }
}
endif;

//マルチページページャーの現在のページにcurrentクラスを追加
add_filter('wp_link_pages_link', 'wp_link_pages_link_custom');
if ( !function_exists( 'wp_link_pages_link_custom' ) ):
function wp_link_pages_link_custom($link){
  //リンク内にAタグが含まれていない場合は現在のページ
  if (!includes_string($link, '</a>')) {
    $link = str_replace('class="page-numbers"', 'class="page-numbers current"', $link);
  }
  return $link;
}
endif;

//メインクエリの出力変更
add_action( 'pre_get_posts', 'custom_main_query_pre_get_posts' );
if ( !function_exists( 'custom_main_query_pre_get_posts' ) ):
function custom_main_query_pre_get_posts( $query ) {
  if (is_admin()) return;

  //メインループ内
  if ($query->is_main_query()) {

    //順番変更
  if (!is_index_sort_orderby_date() && !is_search()) {
    //投稿日順じゃないときは設定値を挿入する
    $query->set( 'orderby', get_index_sort_orderby() );
  }

    //カテゴリーの除外
    $exclude_category_ids = get_archive_exclude_category_ids();
    if (!is_singular() && $exclude_category_ids && is_array($exclude_category_ids)) {
      $query->set( 'category__not_in', $exclude_category_ids );
    }

    //除外投稿
    $exclude_post_ids = get_archive_exclude_post_ids();
    if (!is_singular() && $exclude_post_ids && is_array($exclude_post_ids)) {
      $query->set( 'post__not_in', $exclude_post_ids );
    }

  }

  //フィード
  if ($query->is_feed) {
    $exclude_post_ids = get_rss_exclude_post_ids();
    if ($exclude_post_ids && is_array($exclude_post_ids)) {
      $query->set( 'post__not_in', $exclude_post_ids );
    }
  }
}
endif;

//強制付与されるnoreferrer削除
add_filter( 'wp_targeted_link_rel', 'wp_targeted_link_rel_custom', 10, 2 );
if ( !function_exists( 'wp_targeted_link_rel_custom' ) ):
function wp_targeted_link_rel_custom( $rel_value, $link_html ){
  $rel_value = str_replace('noopener noreferrer', '', $rel_value);
  return $rel_value;
}
endif;

//SmartNewsフィード追加
add_action('init', 'smartnews_feed_init');
if ( !function_exists( 'smartnews_feed_init' ) ):
function smartnews_feed_init(){
  add_feed('smartnews', 'smartnews_feed');
}
endif;

//domain.com/?feed=smartnewsで表示
if ( !function_exists( 'smartnews_feed' ) ):
function smartnews_feed() {
  get_template_part('/tmp/smartnews');
}
endif;

//SmartNewsのHTTP header for Content-type
add_filter( 'feed_content_type', 'smartnews_feed_content_type', 10, 2 );
if ( !function_exists( 'smartnews_feed_content_type' ) ):
function smartnews_feed_content_type( $content_type, $type ) {
  if ( 'smartnews' === $type ) {
    return feed_content_type( 'rss2' );
  }
  return $content_type;
}
endif;

//サイトマップにnoindex設定を反映させる
add_filter('wp_sitemaps_posts_query_args', 'wp_sitemaps_posts_query_args_noindex_custom');
if ( !function_exists( 'wp_sitemaps_posts_query_args_noindex_custom' ) ):
function wp_sitemaps_posts_query_args_noindex_custom($args){
  $args['post__not_in'] = get_noindex_post_ids();
  return $args;
}
endif;

//サイトマップにカテゴリー・タグのnoindex設定を反映させる
add_filter('wp_sitemaps_taxonomies_query_args', 'wp_sitemaps_taxonomies_query_args_noindex_custom');
if ( !function_exists( 'wp_sitemaps_taxonomies_query_args_noindex_custom' ) ):
function wp_sitemaps_taxonomies_query_args_noindex_custom($args){
  //カテゴリーの除外
  $category_ids = get_noindex_category_ids();
  if (($args['taxonomy'] == 'category') && $category_ids) {
    $args['exclude'] = $category_ids;
  }

  //タグの除外
  $tag_ids = get_noindex_tag_ids();
  if (($args['taxonomy'] == 'post_tag') && $tag_ids) {
    $args['exclude'] = $tag_ids;
  }
  return $args;
}
endif;

//サイトマップにカテゴリー・タグのnoindex設定を反映させる
add_filter('wp_sitemaps_taxonomies', 'wp_sitemaps_taxonomies_custum');
if ( !function_exists( 'wp_sitemaps_taxonomies_custum' ) ):
function wp_sitemaps_taxonomies_custum( $taxonomies ) {
  //サイトマップにカテゴリーを出力しない
  if (is_category_page_noindex()) {
    unset( $taxonomies['category'] );
  }

  //サイトマップにタグを出力しない
  if (is_tag_page_noindex()) {
    unset( $taxonomies['post_tag'] );
  }

  return $taxonomies;
}
endif;

//サイトマップにその他のアーカイブのnoindex設定を反映する
add_filter('wp_sitemaps_add_provider', 'wp_sitemaps_add_provider_custom', 10, 2);
if ( !function_exists( 'wp_sitemaps_add_provider_custom' ) ):
function wp_sitemaps_add_provider_custom( $provider, $name ) {
  if ( is_other_archive_page_noindex() && 'users' === $name ) {
      return false;
  }

  return $provider;
}
endif;

//ウィジェットの「最近の投稿」から「アーカイブ除外ページ」を削除
add_filter('widget_posts_args', 'remove_no_archive_pages_from_widget_recent_entries');
if ( !function_exists( 'remove_no_archive_pages_from_widget_recent_entries' ) ):
function remove_no_archive_pages_from_widget_recent_entries($args){
  $archive_exclude_post_ids = get_archive_exclude_post_ids();
  if ($archive_exclude_post_ids) {
    $args['post__not_in'] = $archive_exclude_post_ids;
  }
  return $args;
}
endif;
