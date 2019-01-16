<?php //ショートコード
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//プロフィールショートコード関数
if (!shortcode_exists('author_box')) {
  add_shortcode('author_box', 'author_box_shortcode');
}
if ( !function_exists( 'author_box_shortcode' ) ):
function author_box_shortcode($atts) {
  extract(shortcode_atts(array(
    'label' => '',
  ), $atts));
  $label = sanitize_shortcode_value($label);
  ob_start();
  generate_author_box_tag($label);
  $res = ob_get_clean();
  return $res;
}
endif;

//新着記事ショートコード関数
if (!shortcode_exists('new_list')) {
  add_shortcode('new_list', 'new_entries_shortcode');
}
if ( !function_exists( 'new_entries_shortcode' ) ):
function new_entries_shortcode($atts) {
  extract(shortcode_atts(array(
    'count' => 5,
    'cats' => 'all',
    'type' => 'default',
    'children' => 0,
    'post_type' => 'post',
    'taxonomy' => 'category',
    'random' => 0,
    'action' => null,
  ), $atts));
  $categories = array();
  if ($cats && $cats != 'all') {
    $categories = explode(',', $cats);
  }
  ob_start();
  generate_widget_entries_tag($count, $type, $categories, $children, $post_type, $taxonomy, $random, $action);
  $res = ob_get_clean();
  return $res;
}
endif;

//人気記事ショートコード関数
if (!shortcode_exists('popular_list')) {
  add_shortcode('popular_list', 'popular_entries_shortcode');
}
if ( !function_exists( 'popular_entries_shortcode' ) ):
function popular_entries_shortcode($atts) {
  extract(shortcode_atts(array(
    'days' => 'all',
    'count' => 5,
    'type' => 'default',
    'rank' => 0,
    'pv' => 0,
    'cats' => 'all',
  ), $atts));
  $categories = array();
  if ($cats && $cats != 'all') {
    $categories = explode(',', $cats);
  }
  ob_start();
  generate_popular_entries_tag($days, $count, $type, $rank, $pv, $categories);
  $res = ob_get_clean();
  return $res;
}
endif;

define('AFFI_SHORTCODE', 'affi');
//アフィリエイトタグショートコード関数
if (!shortcode_exists(AFFI_SHORTCODE)) {
  add_shortcode(AFFI_SHORTCODE, 'affiliate_tag_shortcode');
}
if ( !function_exists( 'affiliate_tag_shortcode' ) ):
function affiliate_tag_shortcode($atts) {
  extract(shortcode_atts(array(
    'id' => 0,
  ), $atts));
  if ($id) {
    if ($recode = get_affiliate_tag($id)) {

      global $post;
      $atag = $recode->text;

      //無限ループ要素の除去
      // $shortcode = get_affiliate_tag_shortcode($id);
      // $atag = str_replace($shortcode, '', $atag);
      $atag = preg_replace('{\['.AFFI_SHORTCODE.'[^\]]*?id=[\'"]?'.$id.'[\'"]?[^\]]*?\]}i', '', $atag);

      $post_id = null;
      if (isset($post->ID)) {
        $post_id = 'data-post-id="'.$post->ID.'" ';
      }
      //計測用の属性付与
      $atag = str_replace('<a ', '<a data-atag-id="'.$id.'" '.$post_id, $atag);

      return do_shortcode($atag);
    }
  }

}
endif;
//アフィリエイトショートコードの生成
if ( !function_exists( 'get_affiliate_tag_shortcode' ) ):
function get_affiliate_tag_shortcode($id) {
  return "[".AFFI_SHORTCODE." id={$id}]";
}
endif;

define('TEMPLATE_SHORTCODE', 'temp');
//関数テキストショートコード関数
if (!shortcode_exists(TEMPLATE_SHORTCODE)) {
  add_shortcode(TEMPLATE_SHORTCODE, 'function_text_shortcode');
}
if ( !function_exists( 'function_text_shortcode' ) ):
function function_text_shortcode($atts) {
  extract(shortcode_atts(array(
    'id' => 0,
  ), $atts));
  if ($id) {
    if ($recode = get_function_text($id)) {
      //無限ループ要素の除去
      //$shortcode = get_function_text_shortcode($id);
      $template = preg_replace('{\['.TEMPLATE_SHORTCODE.'[^\]]*?id=[\'"]?'.$id.'[\'"]?[^\]]*?\]}i', '', $recode->text);

      return do_shortcode($template);
    }
  }
}
endif;

//テンプレートショートコードの取得
if ( !function_exists( 'get_function_text_shortcode' ) ):
function get_function_text_shortcode($id) {
  return "[".TEMPLATE_SHORTCODE." id={$id}]";
}
endif;

define('RANKING_SHORTCODE', 'rank');
//ランキングショートコード関数
if (!shortcode_exists(RANKING_SHORTCODE)) {
  add_shortcode(RANKING_SHORTCODE, 'item_ranking_shortcode');
}
if ( !function_exists( 'item_ranking_shortcode' ) ):
function item_ranking_shortcode($atts) {
  extract(shortcode_atts(array(
    'id' => 0,
  ), $atts));
  if ($id) {
    // //無限ループ回避
    // if ($recode->id == $id) return;

    ob_start();
    generate_item_ranking_tag($id);
    return ob_get_clean();
  }

}
endif;

//ショートコードの取得
if ( !function_exists( 'get_item_ranking_shortcode' ) ):
function get_item_ranking_shortcode($id) {
  return "[".RANKING_SHORTCODE." id={$id}]";
}
endif;

//ログインユーザーのみに表示するコンテンツ
if (!shortcode_exists('login_user_only')) {
  add_shortcode('login_user_only', 'login_user_only_shortcode');
}
if ( !function_exists( 'login_user_only_shortcode' ) ):
function login_user_only_shortcode( $atts, $content = null ) {
  extract( shortcode_atts( array(
      'msg' => __( 'こちらのコンテンツはログインユーザーのみに表示されます。', THEME_NAME ),
  ), $atts ) );
  $msg = sanitize_shortcode_value($msg);
  if (is_user_logged_in()) {
    return do_shortcode($content);
  } else {
    return '<div class="login-user-only">'.htmlspecialchars_decode($msg).'</div>';
  }
}
endif;
//タイムラインの作成（timelineショートコード）
if (!shortcode_exists('timeline')) {
  add_shortcode('timeline', 'timeline_shortcode');
}
if ( !function_exists( 'timeline_shortcode' ) ):
function timeline_shortcode( $atts, $content = null ){
  extract( shortcode_atts( array(
    'title' => null,
  ), $atts ) );
  $content = remove_wrap_shortcode_wpautop('ti', $content);
  $content = do_shortcode( shortcode_unautop( $content ) );
  $title = sanitize_shortcode_value($title);
  $title_tag = null;
  if ($title) {
    $title_tag = '<div class="timeline-title">'.$title.'</div>';
  }
  $tag = '<div class="timeline-box">'.
            $title_tag.
            '<ul class="timeline">'.
              $content.
            '</ul>'.
          '</div>';
  return apply_filters('timeline_tag', $tag);
}
endif;

//timelineショートコードコンテンツ内に余計な改行や文字列が入らないように除外
if ( !function_exists( 'remove_wrap_shortcode_wpautop' ) ):
function remove_wrap_shortcode_wpautop($shortcode, $content){
  //tiショートコードのみを抽出
  $pattern = '/\['.$shortcode.'.*?\].*?\[\/'.$shortcode.'\]/is';
  if (preg_match_all($pattern, $content, $m)) {
    $all = null;
    foreach ($m[0] as $code) {
      $all .= $code;
    }
    return $all;
  }
}
endif;

//タイムラインアイテム作成（タイムラインの中の項目）
if (!shortcode_exists('ti')) {
  add_shortcode('ti', 'timeline_item_shortcode');
}
if ( !function_exists( 'timeline_item_shortcode' ) ):
function timeline_item_shortcode( $atts, $content = null ){
  extract( shortcode_atts( array(
    'title' => null,
    'label' => null,
  ), $atts ) );
  $title = sanitize_shortcode_value($title);
  $label = sanitize_shortcode_value($label);
  $title_tag = null;
  if ($title) {
    $title_tag = '<div class="timeline-item-title">'.$title.'</div>';
  }

  $content = do_shortcode( shortcode_unautop( $content ) );
  $tag = '<li class="timeline-item">'.
            '<div class="timeline-item-label">'.$label.'</div>'.
            '<div class="timeline-item-content">'.
              '<div class="timeline-item-title">'.$title.'</div>'.
              '<div class="timeline-item-snippet">'.$content.'</div>'.
            '</div>'.
          '</li>';
  return apply_filters('timeline_item_tag', $tag);
}
endif;

define('AGO_ERROR_MESSAGE', '<span class="ago-error">'.__( '日付未入力', THEME_NAME ).'</span>');
//相対的な時間経過を取得するショートコード
if (!shortcode_exists('ago')) {
  add_shortcode('ago', 'ago_shortcode');
}
if ( !function_exists( 'ago_shortcode' ) ):
function ago_shortcode( $atts ){
  extract( shortcode_atts( array(
    'from' => null,
  ), $atts ) );
  if (!$from) {
    return AGO_ERROR_MESSAGE;
  }
  $from = sanitize_shortcode_value($from);
  $from = strtotime($from);
  return get_human_time_diff_advance($from);
}
endif;

//誕生日から年齢を取得するショートコード
add_shortcode('age', 'age_shortcode');
if ( !function_exists( 'age_shortcode' ) ):
function age_shortcode( $atts ){
  extract( shortcode_atts( array(
    'from' => null,
    'birth' => null,
    'unit' => __( '歳', THEME_NAME ),
  ), $atts ) );
  if (!$from) {
    $from = $birth;
  }
  //入力エラー出力
  if (!$from) {
    return AGO_ERROR_MESSAGE;
  }
  $from = strtotime($from);
  return get_human_years_ago($from, $unit);
}
endif;

//相対的な時間経過（年）を取得するショートコード
add_shortcode('yago', 'yago_shortcode');
if ( !function_exists( 'yago_shortcode' ) ):
function yago_shortcode( $atts ){
  extract( shortcode_atts( array(
    'from' => null,
    'unit' => '',
  ), $atts ) );
  //入力エラー出力
  if (!$from) {
    return AGO_ERROR_MESSAGE;
  }
  $from = strtotime($from);
  return get_human_years_ago($from, $unit);
}
endif;


//星ショートコード
if (!shortcode_exists('star')) {
  add_shortcode('star', 'rating_star_shortcode');
}
if ( !function_exists( 'rating_star_shortcode' ) ):
function rating_star_shortcode( $atts, $content = null ) {
  extract( shortcode_atts( array(
      'rate' => 5,
      'max' => 5,
      'number' => 1,
  ), $atts ) );
  return get_rating_star_tag($rate, $max, $number);
}
endif;

//目次ショートコード
if (!shortcode_exists('toc')) {
  add_shortcode('toc', 'toc_shortcode');
}
if ( !function_exists( 'toc_shortcode' ) ):
function toc_shortcode( $atts, $content = null ) {
  if (is_singular()) {
    global $_TOC_SHORTCODE_USE;
    $_TOC_SHORTCODE_USE = true;
    $harray = array();
    //_v(get_the_content());
    return get_toc_tag(get_the_content(), $harray);

  }
}
endif;

//サイトマップショートコード
add_shortcode('sitemap', 'sitemap_shortcode');
if ( !function_exists( 'sitemap_shortcode' ) ):
function sitemap_shortcode( $atts, $content = null ) {
  extract( shortcode_atts( array(
    'page' => 1,
    'single' => 1,
    'category' => 1,
    'archive' => 0,
  ), $atts ) );
  ob_start();?>
  <div class="sitemap">
    <?php if ($page): ?>
    <h2><?php _e( '固定ページ', THEME_NAME ) ?></h2>
    <ul>
      <?php wp_list_pages('title_li='); ?>
    </ul>
    <?php endif; ?>
    <?php if ($single): ?>
    <h2><?php _e( '記事一覧', THEME_NAME ) ?></h2>
    <ul>
      <?php wp_get_archives( 'type=alpha' ); ?>
    </ul>
    <?php endif; ?>
    <?php if ($category): ?>
    <h2><?php _e( 'カテゴリー', THEME_NAME ) ?></h2>
    <ul>
      <?php wp_list_categories('title_li='); ?>
    </ul>
    <?php endif; ?>
    <?php if ($archive): ?>
    <h2><?php _e( '月別アーカイブ', THEME_NAME ) ?></h2>
    <ul>
      <?php wp_get_archives('type=monthly'); ?>
    </ul>
    <?php endif; ?>
  </div>
  <?php
  return ob_get_clean();
}
endif;

//ブログカードショートコード
if (!shortcode_exists('blogcard')) {
}
  add_shortcode('blogcard', 'blogcard_shortcode');
if ( !function_exists( 'blogcard_shortcode' ) ):
function blogcard_shortcode( $atts, $content = null ) {
  extract( shortcode_atts( array(
    'url' => null,
  ), $atts ) );
  if ($url) {
    $tag = url_to_internal_blogcard_tag($url);
    if (!$tag) {
      $tag = url_to_external_blog_card($url);
    }
    return $tag;
  }
}
endif;
