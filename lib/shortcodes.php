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
    'id' => null,
    'label' => null,
  ), $atts, 'author_box'));
  $label = sanitize_shortcode_value($label);
  ob_start();
  generate_author_box_tag($id, $label);
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
    'tags' => null,
    'type' => 'default',
    'children' => 0,
    'post_type' => 'post',
    'taxonomy' => 'category',
    'sticky' => 0,
    'random' => 0,
    'modified' => 0,
    'order' => 'desc',
    'action' => null,
    'bold' => 0,
    'arrow' => 0,
    'class' => null,
    'snippet' => 0,
    'author' => null,
  ), $atts, 'new_list'));

  //カテゴリを配列化
  $cat_ids = array();
  if ($cats && $cats != 'all') {
    $cat_ids = explode(',', $cats);
  }
  //タグを配列化
  $tag_ids = array();
  if ($tags && $tags != 'all') {
    $tag_ids = explode(',', $tags);
  }
  //引数配列のセット
  $atts = array(
    'entry_count' => $count,
    'cat_ids' => $cat_ids,
    'tag_ids' => $tag_ids,
    'type' => $type,
    'include_children' => $children,
    'post_type' => $post_type,
    'taxonomy' => $taxonomy,
    'sticky' => $sticky,
    'random' => $random,
    'modified' => $modified,
    'order' => $order,
    'action' => $action,
    'bold' => $bold,
    'arrow' => $arrow,
    'class' => $class,
    'snippet' => $snippet,
    'author' => $author,
  );
  ob_start();
  generate_widget_entries_tag($atts);
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
    'children' => 0,
    'bold' => 0,
    'arrow' => 0,
    'class' => null,
    'author' => null,
  ), $atts, 'popular_list'));
  $cat_ids = array();
  if ($cats && $cats != 'all') {
    $cat_ids = explode(',', $cats);
  }
  $atts = array(
    'days' => $days,
    'entry_count' => $count,
    'entry_type' => $type,
    'ranking_visible' => $rank,
    'pv_visible' => $pv,
    'cat_ids' => $cat_ids,
    'children' => $children,
    'bold' => $bold,
    'arrow' => $arrow,
    'class' => $class,
    'author' => $author,
  );
  ob_start();
  generate_popular_entries_tag($atts);
  //_v($atts);
  //generate_popular_entries_tag($days, $count, $type, $rank, $pv, $categories);
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
  ), $atts, AFFI_SHORTCODE));
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
  ), $atts, TEMPLATE_SHORTCODE));
  if ($id) {
    if ($recode = get_function_text($id)) {
      //無限ループ要素の除去
      //$shortcode = get_function_text_shortcode($id);
      $template = preg_replace('{\['.TEMPLATE_SHORTCODE.'[^\]]*?id=[\'"]?'.$id.'[\'"]?[^\]]*?\]}i', '', $recode->text);
      //余計な改行を取り除く
      $template = shortcode_unautop($template);

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
  ), $atts, RANKING_SHORTCODE));
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
  ), $atts, 'login_user_only' ) );
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
  ), $atts, 'timeline' ) );
  $content = remove_wrap_shortcode_wpautop('ti', $content);
  $content = do_shortcode( shortcode_unautop( $content ) );
  $title = sanitize_shortcode_value($title);
  $title_tag = null;
  if ($title) {
    $title_tag = '<div class="timeline-title">'.$title.'</div>';
  }
  $tag = '<div class="timeline-box cf">'.
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
  ), $atts, 'ti' ) );
  $title = sanitize_shortcode_value($title);
  $label = sanitize_shortcode_value($label);
  $title_tag = null;
  if ($title) {
    $title_tag = '<div class="timeline-item-title">'.$title.'</div>';
  }

  $content = do_shortcode( shortcode_unautop( $content ) );
  $tag = '<li class="timeline-item cf">'.
            '<div class="timeline-item-label">'.$label.'</div>'.
            '<div class="timeline-item-content">'.
              '<div class="timeline-item-title">'.$title.'</div>'.
              '<div class="timeline-item-snippet">'.$content.'</div>'.
            '</div>'.
          '</li>';
  return apply_filters('timeline_item_tag', $tag);
}
endif;

define('TIME_ERROR_MESSAGE', '<span class="time-error">'.__( '日付未入力', THEME_NAME ).'</span>');
//相対的な時間経過を取得するショートコード
if (!shortcode_exists('ago')) {
  add_shortcode('ago', 'ago_shortcode');
}
if ( !function_exists( 'ago_shortcode' ) ):
function ago_shortcode( $atts ){
  extract( shortcode_atts( array(
    'from' => null,
  ), $atts, 'ago' ) );
  if (!$from) {
    return TIME_ERROR_MESSAGE;
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
  ), $atts, 'age' ) );
  if (!$from) {
    $from = $birth;
  }
  //入力エラー出力
  if (!$from) {
    return TIME_ERROR_MESSAGE;
  }
  $from = strtotime($from);
  return get_human_years_ago($from, $unit);
}
endif;

//相対的な時間経過（年）を取得するショートコード
//参考：https://fullnoteblog.com/age-short-code/
add_shortcode('yago', 'yago_shortcode');
if ( !function_exists( 'yago_shortcode' ) ):
function yago_shortcode( $atts ){
  extract( shortcode_atts( array(
    'from' => null,
    'unit' => '',
  ), $atts, 'yago' ) );
  //入力エラー出力
  if (!$from) {
    return TIME_ERROR_MESSAGE;
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
  ), $atts, 'star' ) );
  return get_rating_star_tag($rate, $max, $number);
}
endif;

//目次ショートコード
if (!shortcode_exists('toc')) {
  add_shortcode('toc', 'toc_shortcode');
}
if ( !function_exists( 'toc_shortcode' ) ):
function toc_shortcode( $atts, $content = null ) {
  extract(shortcode_atts(array(
    'depth' => 0,
  ), $atts, 'author_box'));
  if (is_singular()) {
    global $_TOC_WIDGET_OR_SHORTCODE_USE;
    $_TOC_WIDGET_OR_SHORTCODE_USE = true;
    $harray = array();
    $the_content = get_toc_expanded_content();
    return get_toc_tag($the_content, $harray, false, $depth);

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
  ), $atts, 'sitemap' ) );
  ob_start();?>
  <div class="sitemap">
    <?php if ($page): ?>
    <h2><?php echo apply_filters('sitemap_page_caption', __( '固定ページ', THEME_NAME )); ?></h2>
    <ul>
      <?php wp_list_pages('title_li='); ?>
    </ul>
    <?php endif; ?>
    <?php if ($single): ?>
    <h2><?php echo apply_filters('sitemap_single_caption', __( '投稿一覧', THEME_NAME )); ?></h2>
    <ul>
      <?php wp_get_archives( 'type=alpha' ); ?>
    </ul>
    <?php endif; ?>
    <?php if ($category): ?>
    <h2><?php echo apply_filters('sitemap_category_caption', __( 'カテゴリー', THEME_NAME )); ?></h2>
    <ul>
      <?php wp_list_categories('title_li='); ?>
    </ul>
    <?php endif; ?>
    <?php if ($archive): ?>
    <h2><?php  echo apply_filters('sitemap_archive_caption', __( '月別アーカイブ', THEME_NAME )); ?></h2>
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
  add_shortcode('blogcard', 'blogcard_shortcode');
}
if ( !function_exists( 'blogcard_shortcode' ) ):
function blogcard_shortcode( $atts, $content = null ) {
  extract( shortcode_atts( array(
    'url' => null,
  ), $atts, 'blogcard' ) );
  if ($url) {
    $tag = url_to_internal_blogcard_tag($url);
    if (!$tag) {
      $tag = url_to_external_blog_card($url);
    }
    return $tag;
  }
}
endif;

//カウントダウンタイマーの取得
if ( !function_exists( 'get_countdown_days' ) ):
function get_countdown_days( $to ) {
  $now = date_i18n('U');
  $diff = (int) ($to - $now);
  $days = ceil($diff / 86400);
  if ($days <= 0) {
    $days = 0;
  }
  $till = sprintf('%s', $days);
  return $till;
}
endif;

//カウントダウンショートコード
//参考：https://fullnoteblog.com/count-down-timer/
add_shortcode('countdown', 'countdown_shortcode');
if ( !function_exists( 'countdown_shortcode' ) ):
function countdown_shortcode( $atts ){
  extract( shortcode_atts( array(
    'to' => null,
    'unit' => null,
  ), $atts, 'countdown' ) );
  //入力エラー出力
  if (!$to) {
    return TIME_ERROR_MESSAGE;
  }
  $to = strtotime($to);
  return get_countdown_days($to).$unit;
}
endif;

//ナビメニューショートコード
//参考：https://www.orank.net/1972
add_shortcode('navi', 'get_ord_navi_card_list_tag');
if ( !function_exists( 'get_ord_navi_card_list_tag' ) ):
function get_ord_navi_card_list_tag($atts){
  extract(shortcode_atts(array(
    'name' => '', // メニュー名
    'type' => '',
    'bold' => 1,
    'arrow' => 1,
    'class' => null,
  ), $atts, 'navi'));
  $atts = array(
    'name' => $name,
    'type' => $type,
    'bold' => $bold,
    'arrow' => $arrow,
    'class' => $class,
  );
  $tag = get_navi_card_list_tag($atts);

  return apply_filters('get_ord_navi_card_list_tag', $tag);
}
endif;

//ナビメニューリストショートコード
//参考：https://www.orank.net/1972
add_shortcode('navi_list', 'get_navi_card_list_tag');
if ( !function_exists( 'get_navi_card_list_tag' ) ):
function get_navi_card_list_tag($atts){
  extract(shortcode_atts(array(
    'name' => '', // メニュー名
    'type' => '',
    'bold' => 0,
    'arrow' => 0,
    'class' => null,
  ), $atts, 'navi_list'));

  if (is_admin() && !is_admin_php_page()) {
    return;
  }

  $tag = null;
  $menu_items = wp_get_nav_menu_items($name); // name: カスタムメニューの名前
  if (!$menu_items) {
    return;
  }
  // _v($menu_items);

  foreach ($menu_items as $menu):
    //画像情報の取得
    $image_attributes = get_navi_card_image_attributes($menu, $type);

    $url = $menu->url;
    $title = $menu->title;
    $snippet = $menu->description;
    $classes = $menu->classes;
    $object = $menu->object;
    $object_id = $menu->object_id;
    $ribbon_no = isset($menu->classes[0]) ? $menu->classes[0] : null;

    //アイテムタグの取得
    $atts = array(
      'prefix' => WIDGET_NAVI_ENTRY_CARD_PREFIX,
      'url' => $url,
      'title' => $title,
      'snippet' => $snippet,
      'image_attributes' => $image_attributes,
      'ribbon_no' => $ribbon_no,
      'type' => $type,
      'classes' => $classes,
      'object' => $object,
      'object_id' => $object_id,
    );
    $tag .= get_widget_entry_card_link_tag($atts);

  endforeach;

  //ラッパーの取り付け
  if ($menu_items) {
    $atts = array(
      'tag' => $tag,
      'type' => $type,
      'bold' => $bold,
      'arrow' => $arrow,
      'class' => $class,
    );
    $tag = get_navi_card_wrap_tag($atts);
  }

  return apply_filters('get_navi_card_list_tag', $tag);
}
endif;

//おすすめカード
if ( !function_exists( 'get_recommend_cards_tag' ) ):
function get_recommend_cards_tag($atts){
  extract(shortcode_atts(array(
    'name' => '', // メニュー名
    'style' => '', //表示スタイル
    'margin' => null, //カード毎の余白
    'wrap' => null, //全体の左右余白
    'class' => null, //拡張クラス
  ), $atts, 'recommend'));
  if ($name) {
    ob_start();
    $wrap_class = $wrap ? ' wrap' : null;
    $class = $class ? ' '.$class : null;
    ?>
    <!-- Recommended -->
    <div id="recommended" class="recommended cf<?php echo get_additional_recommend_cards_classes($style, $margin); ?>">
      <div id="recommended-in" class="recommended-in<?php echo $wrap_class; ?><?php echo $class; ?> cf">
        <?php
        $atts = array(
          'name' => $name,
          'type' => ET_LARGE_THUMB_ON,
        );
        echo get_navi_card_list_tag($atts);
        ?>
      </div><!-- /#recommended-in -->
    </div><!-- /.recommended -->
    <?php
    $tag = ob_get_clean();
    return apply_filters('get_recommend_cards_tag', $tag);
  }
}
endif;

//ナビメニューショートコード
//参考：https://www.orank.net/1972
add_shortcode('navi', 'get_ord_navi_card_list_tag');
if ( !function_exists( 'get_ord_navi_card_list_tag' ) ):
function get_ord_navi_card_list_tag($atts){
  extract(shortcode_atts(array(
    'name' => '', // メニュー名
    'type' => '',
    'bold' => 1,
    'arrow' => 1,
    'class' => null,
  ), $atts, 'navi'));
  $atts = array(
    'name' => $name,
    'type' => $type,
    'bold' => $bold,
    'arrow' => $arrow,
    'class' => $class,
  );
  $tag = get_navi_card_list_tag($atts);

  return apply_filters('get_ord_navi_card_list_tag', $tag);
}
endif;

//ボックスメニューショートコード
add_shortcode('box_menu', 'get_box_menu_tag');
if ( !function_exists( 'get_box_menu_tag' ) ):
function get_box_menu_tag($atts){
  extract(shortcode_atts(array(
    'name' => '', // メニュー名
    'target' => '_self',
    'class' => null,
  ), $atts, 'box_menu'));

  if (is_admin() && !is_admin_php_page()) {
    return;
  }

  $tag = null;
  $menu_items = wp_get_nav_menu_items($name); // name: カスタムメニューの名前
  if (!$menu_items) {
    return;
  }

  //_v($menu_items);
  foreach ($menu_items as $menu):

    $url = $menu->url;
    $title = $menu->title;
    $title_tag = '<div class="box-menu-label">'.$title.'</div>';
    $description_tag = '<div class="box-menu-description">'.$menu->description.'</div>';
    $attr_title = $menu->attr_title;
    $classes = implode(' ', $menu->classes);
    $icon_tag = '<div class="fa fa-star" aria-hidden="true"></div>';
    //画像URLの場合
    if (preg_match('/(https?)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)\.(jpg|jpeg|gif|png)/', $attr_title)) {
      $img_url = $attr_title;
      $icon_tag = '<img src="'.esc_url($img_url).'" alt="'.esc_attr($title).'" />';
    } //アイコンフォントの場合
    elseif (preg_match('/fa.? fa-[a-z\-]+/', $classes)) {
      $icon_tag = '<div class="'.esc_attr($classes).'" aria-hidden="true"></div>';
    }
    $icon_tag = '<div class="box-menu-icon">'.$icon_tag.'</div>';

    $target_value = apply_filters('box_menu_link_target', $target, $url);
    $tag .= '<a class="box-menu" href="'.esc_url($url).'" target="'.$target_value.'"'.get_rel_by_target($target_value).'>'.
      $icon_tag.
      $title_tag.
      $description_tag.
    '</a>';
  endforeach;
  $add_class = null;
  if ($class) {
    $add_class = ' '.$class;
  }
  //ラッパーで囲む
  $tag = '<div class="box-menus'.$add_class.' no-icon">'.$tag.'</div>';

  return apply_filters('get_box_menu_tag', $tag);
}
endif;

/* RSSフィードショートコード */
add_shortcode( 'rss', 'get_rss_feed_tag' );
if ( !function_exists( 'get_rss_feed_tag' ) ):
function get_rss_feed_tag( $atts ) {
  include_once(ABSPATH . WPINC . '/feed.php');
  extract(shortcode_atts(
    array(
      'url' => 'https://ja.wordpress.org/feed/', //取得するRSSフィードURL
      'count' => '5', //取得する数
      'img' => NO_IMAGE_RSS, //画像が取得できなかった場合のイメージ
      'target' => '_blank', //ブラウザの開き方（target属性）
      'cache_minute' => '60', //キャッシュ時間（分）
      'desc' => '1', //説明文表示 1 or 0
      'date' => '1', //日付表示 1 or 0
      'type' => '', //表示タイプ
      'bold' => 0, //タイトルを太字にするか
      'arrow' => 0, //矢印を出すか
      'class' => null, //拡張クラス
    ),
    $atts,
    'rss'
  ));

  $feed_url = $url;
  $feed_count = $count;
  $img_url = $img;
  $feed_content = '';
  $feed_contents = '';

  //Cache処理（かなり簡易的なもの）
  $transient_id = 'ree_feed_'.md5($feed_url.'_'.$count.'_'.$img_url.'_'.$target.'_'.$desc.'_'.$date.'_'.$type.'_'.$bold.'_'.$arrow.'_'.$class);
  $feed_contents = get_transient( $transient_id );
  if ($feed_contents) {
    return $feed_contents;
  } else {
    $rss = fetch_feed( $feed_url );
  }
  if ( !is_wp_error( $rss ) ) {
    $maxitems = $rss->get_item_quantity( $feed_count );
    $rss_items = $rss->get_items( 0, $maxitems );

    foreach ( $rss_items as $item ) :
      $first_img = '';
      if ( preg_match( '/<img.+?src=[\'"]([^\'"]+?)[\'"].*?>/msi', $item->get_content(), $matches )) $first_img = $matches[1];
      if ( !empty( $first_img ) ) :
        $feed_img = esc_attr( $first_img );
      else:
        $feed_img = $img_url;
      endif;
      $feed_url = $item->get_permalink();
      $feed_title = $item->get_title();
      $feed_date = $item->get_date('Y.m.d');
      $feed_text = mb_substr(strip_tags($item->get_content()), 0, 110);

      $feed_content .= '<a href="' . esc_url($feed_url) . '" title="' . esc_attr($feed_title) . '" class="rss-entry-card-link widget-entry-card-link a-wrap" target="'.esc_attr($target).'"'.get_rel_by_target($target).'>';
      $feed_content .= '<div class="rss-entry-card widget-entry-card e-card cf">';
      $feed_content .= '<figure class="rss-entry-card-thumb widget-entry-card-thumb card-thumb">';
      $feed_content .= '<img src="' . esc_url($feed_img) . '" class="rss-entry-card-thumb-image widget-entry-card-thumb-image card-thumb-image" alt="">';
      $feed_content .= '</figure>';
      $feed_content .= '<div class="rss-entry-card-content widget-entry-card-content card-content">';
      $feed_content .= '<div class="rss-entry-card-title widget-entry-card-title card-title">' . esc_html($feed_title) . '</div>';
      if ($desc) {
        $feed_content .= '<div class="rss-entry-card-snippet widget-entry-card-snippet card-snippet">' . esc_html($feed_text) . '…</div>';
      }
      if ($date) {
        $feed_content .= '<div class="rss-entry-card-date widget-entry-card-date">
        <span class="rss-entry-card-post-date widget-entry-card-post-date post-date">' . esc_html($feed_date) . '</span>
      </div>';
      }
      $feed_content .= '</div>';//card-content
      $feed_content .= '</div>';
      $feed_content .= '</a>';
    endforeach;
  } else {
    $feed_content = '<p>RSSフィードを取得できません</p>';
  }
  // $add_class = null;
  // if ($class) {
  //   $add_class = ' '.$class;
  // }

// _v($arrow);
// _v($type);
  $atts = array(
    'type' => $type,
    'bold' => $bold,
    'arrow' => $arrow,
    'class' => $class,
  );
  $card_class = get_additional_widget_entry_cards_classes($atts);
  $feed_contents = '<div class="rss-entry-cards widget-entry-cards'.$card_class.' no-icon">' . $feed_content . '</div>';
  set_transient($transient_id, $feed_contents, 60 * intval($cache_minute));

  return apply_filters( 'get_rss_feed_tag',  $feed_contents);

}
endif;


// //数式
// add_shortcode('formula', 'formula_shortcode');
// if ( !function_exists( 'formula_shortcode' ) ):
// function formula_shortcode( $atts, $content = null ) {
//   extract( shortcode_atts( array(
//     'class' => null, //拡張クラス
//   ), $atts, 'formula' ) );
//   if ($class) {
//     $class = ' '.$class;
//   }
//   return '<figure class="tex2jax_process'.$class.'">'.$content.'</figure>';
// }
// endif;


//キャンペーン（指定期間中のみ表示）
add_shortcode('campaign', 'campaign_shortcode');
if ( !function_exists( 'campaign_shortcode' ) ):
function campaign_shortcode( $atts, $content = null ) {
  extract( shortcode_atts( array(
    'from' => null, //いつから（開始日時）
    'to' => null, //いつまで（終了日時）
    'class' => null, //拡張クラス
  ), $atts, 'campaign' ) );

  //内容がない場合は何も表示しない
  if (!$content) return null;
  //現在の日時を取得
  $now = date_i18n('U');

  //いつから（開始日時）
  $from_time = strtotime($from);
  if (!$from_time) {
    $from_time = strtotime('-1 day');
  };

  //いつまで（終了日時）
  $to_time = strtotime($to);
  if (!$to_time) {
    $to_time = strtotime('+1 day');
  };

  //拡張クラス
  if ($class) {
    $class = ' '.$class;
  }

  $tag = null;
  $content = apply_filters('campaign_shortcode_content', $content);
  if (($from_time < $now) && ($to_time > $now)) {
    $tag = '<div class="campaign'.esc_attr($class).'">'.
      // date_i18n('開始日時：Y年m月d日 H時i分s秒', $from_time).'<br>'.
      // date_i18n('終了日時：Y年m月d日 H時i分s秒', $to_time).'<br>'.
      $content.
    '</div>';
  }

  return $tag;
}
endif;
