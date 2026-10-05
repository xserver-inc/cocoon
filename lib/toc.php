<?php //目次関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( !function_exists( 'get_toc_filter_priority' ) ):
function get_toc_filter_priority(){
  //優先順位の設定
  if (is_toc_before_ads()) {
    $priority = BEFORE_1ST_H2_TOC_PRIORITY_HIGH;
  } else {
    $priority = BEFORE_1ST_H2_TOC_PRIORITY_STANDARD;
  }
  return $priority;
}
endif;

//見出し内容取得関数
if ( !function_exists( 'get_h_inner_content' ) ):
function get_h_inner_content($h_content){
  // アコーディオンボタンの場合はそのまま返す
  if (preg_match('/<button\b[^>]*\baria-expanded="(?:true|false)"/i', $h_content)) {
    return $h_content;
  }
  // 見出し内のHTMLタグを有効にするかどうかの処理
  if (is_toc_heading_inner_html_tag_enable()) {
    return $h_content;
  } else {
    return strip_tags($h_content);
  }
}
endif;

// 目次と本文の採番対象となる見出しの共通抽出
if ( !function_exists( 'get_toc_heading_matches' ) ):
function get_toc_heading_matches($content, $depth = 0){
  $depth = intval($depth) ?: 6;
  $headers = array();

  // 本文の再構築に必要なバイト位置を含む見出しの抽出
  preg_match_all('/(<([hH][1-6])\b[^>]*>)(.*?)(<\/([hH][1-6])\s*>)/s', $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
  foreach ($matches as $match) {
    $tag = strtolower($match[2][0]);
    $level = intval(substr($tag, 1));

    // H1・深さの対象外・入れ子・開始終了タグ不一致の見出しの除外
    if ($level < 2 || $level > $depth ||
        preg_match('/<[hH][1-6][\s>]/', $match[3][0]) ||
        $tag !== strtolower($match[5][0])) {
      continue;
    }

    $headers[] = array(
      'tag'    => $tag,
      'text'   => $match[3][0],
      'html'   => $match[0][0],
      'open'   => $match[1][0],
      'close'  => $match[4][0],
      'offset' => $match[0][1],
    );
  }
  return $headers;
}
endif;

// ブロック・クラシック形式の改ページによる本文の分割
if ( !function_exists( 'get_toc_raw_pages' ) ):
function get_toc_raw_pages($content){
  $content = preg_replace('/<!--\s*wp:nextpage\s*-->.*?<!--\s*\/wp:nextpage\s*-->|<!--\s*nextpage\s*-->/is', '<!--nextpage-->', $content);
  // WordPressと同じ改ページ前後の改行および先頭の改ページの除去
  $content = str_replace(array("\n<!--nextpage-->\n", "\n<!--nextpage-->", "<!--nextpage-->\n"), '<!--nextpage-->', $content);
  if (strpos($content, '<!--nextpage-->') === 0) {
    $content = substr($content, 15);
  }
  return explode('<!--nextpage-->', $content);
}
endif;

// WordPressが本文表示に使用するフィルター適用済みの分割ページの取得
if ( !function_exists( 'get_toc_post_pages' ) ):
function get_toc_post_pages($post){
  global $id, $pages;
  if (isset($post->ID) && intval($id) === intval($post->ID) && is_array($pages)) {
    return $pages;
  }
  if (function_exists('generate_postdata')) {
    $postdata = generate_postdata($post);
    if (is_array($postdata) && isset($postdata['pages']) && is_array($postdata['pages'])) {
      return $postdata['pages'];
    }
  }
  return get_toc_raw_pages($post->post_content);
}
endif;

// 別ページの採番に必要なブロック・同期パターン・ショートコードの展開
if ( !function_exists( 'get_toc_expanded_page_content' ) ):
function get_toc_expanded_page_content($content, $page_num = null){
  $page_exists = array_key_exists('page', $GLOBALS);
  $original_page = $GLOBALS['page'] ?? null;
  $query = $GLOBALS['wp_query'] ?? null;
  $query_page_exists = $query instanceof WP_Query && array_key_exists('page', $query->query_vars);
  $original_query_page = $query_page_exists ? $query->query_vars['page'] : null;

  // ページ依存のブロック・ショートコードに必要な対象ページの表示状態
  if ($page_num !== null) {
    $GLOBALS['page'] = $page_num;
    if ($query instanceof WP_Query) {
      $query->set('page', $page_num);
    }
  }
  try {
    $content = get_shortcode_removed_content($content);
    if (function_exists('do_blocks')) {
      $content = do_blocks($content);
    }
    $content = expand_synced_patterns($content);
    return apply_filters('get_toc_expanded_content', do_shortcode($content));
  } finally {
    // 例外発生時を含む元のページ番号とクエリー変数の復元
    if ($page_num !== null) {
      if ($page_exists) {
        $GLOBALS['page'] = $original_page;
      } else {
        unset($GLOBALS['page']);
      }
      if ($query instanceof WP_Query) {
        if ($query_page_exists) {
          $query->set('page', $original_query_page);
        } else {
          unset($query->query_vars['page']);
        }
      }
    }
  }
}
endif;

// 公開APIによるパーマリンク設定・固定フロントページ・プレビュー対応の改ページURL
if ( !function_exists( 'get_toc_page_url' ) ):
function get_toc_page_url($post, $page_num){
  global $wp_rewrite;
  $post = get_post($post);
  if (!$post) return '';
  $url = get_permalink($post);
  if (!$url) return '';

  if ($page_num > 1) {
    // クエリー形式のURLと公開前の投稿へのページ番号の付加
    if (!$wp_rewrite->using_permalinks() || in_array($post->post_status, array('draft', 'pending'), true) || strpos($url, '?') !== false) {
      $url = add_query_arg('page', $page_num, $url);
    } elseif (get_option('show_on_front') === 'page' && intval(get_option('page_on_front')) === intval($post->ID)) {
      $url = trailingslashit($url) . user_trailingslashit($wp_rewrite->pagination_base . '/' . $page_num, 'single_paged');
    } else {
      $url = trailingslashit($url) . user_trailingslashit($page_num, 'single_paged');
    }
  }
  if (is_preview()) {
    $query_args = array();
    if (!in_array($post->post_status, array('draft', 'pending'), true) && isset($_GET['preview_id'], $_GET['preview_nonce'])) {
      $query_args['preview_id'] = wp_unslash($_GET['preview_id']);
      $query_args['preview_nonce'] = wp_unslash($_GET['preview_nonce']);
    }
    $url = get_preview_post_link($post, $query_args, $url);
  }
  return $url;
}
endif;

/**
 * 展開済み本文から目次を生成し、本文の採番に必要な前ページの見出し数を返します。
 *
 * @param string $expanded_content 展開済みの現在ページの本文
 * @param array  $harray 本文のID付与対象となる見出しタグ
 * @param bool   $is_widget ウィジェットからの呼び出し
 * @param int    $depth_option 目次内で表示する深さ
 * @param int    $heading_offset 現在ページより前の対象見出し数
 * @return string|null 目次HTMLまたは目次非表示時の値
 */
if ( !function_exists( 'get_toc_tag' ) ):
function get_toc_tag($expanded_content, &$harray, $is_widget = false, $depth_option = 0, &$heading_offset = null){
  global $post, $page;
  $heading_offset = 0;
  // 投稿情報がない場合でも、カテゴリ/タグページは処理を続行する
  if ((empty($post) || !isset($post->post_content)) && !is_category() && !is_tag()) return '';

  $current_page = is_singular() ? max(1, intval($page)) : 1;
  // 分割ページ目次は投稿/固定ページのときだけ有効にする
  $is_multi_page_toc_visible = is_multi_page_toc_visible() && is_singular();

  //フォーラムページだと表示しない
  if (is_plugin_fourm_page()) {
    return;
  }

  $headers     = array();
  $html        = '';
  $toc_list    = '';
  $id          = '';
  $toggle      = '';
  $counter     = 0;
  $counters    = array(0,0,0,0,0,0);
  $harray      = array();

  $class       = 'toc';
  $title       = get_toc_title(); //目次タイトル
  $showcount   = 0;
  $depth       = intval(get_toc_depth()); //2-6 0で全て
  $top_level   = 2; //h2がトップレベル
  $targetclass = 'entry-content'; //目次対象となるHTML要素

  if ($title === '') {
    $title = __('目次', THEME_NAME);
  }

  $set_depth = intval(get_toc_depth()); //2-6 0で全て
  if (intval($set_depth) == 0) {
    $set_depth = 6;
  }

  $number_visible   = is_toc_number_visible(); //見出しの数字を表示するか
  if ($number_visible) {
    $list_tag = 'ol';
  } else {
    $list_tag = 'ul';
  }


  if($targetclass===''){$targetclass = get_post_type();}
  for($h = $top_level; $h <= 6; $h++){$harray[] = 'h' . $h . '';}

  if ($is_multi_page_toc_visible || (is_singular() && $current_page > 1)) {
    $raw_pages = get_toc_post_pages($post);
    $toc_counter = 0;
    foreach ($raw_pages as $page_index => $page_content) {
      $page_num = $page_index + 1;

      // 現在ページだけの目次で不要な後続ページの展開の省略
      if (!$is_multi_page_toc_visible && $page_num >= $current_page) {
        break;
      }

      // 現在ページの展開済み本文の再利用
      $page_content = $page_num === $current_page
        ? $expanded_content
        : get_toc_expanded_page_content($page_content, $page_num);
      $page_headers = get_toc_heading_matches($page_content, $set_depth);
      if ($page_num < $current_page) {
        $heading_offset += count($page_headers);
      }

      if ($is_multi_page_toc_visible) {
        foreach ($page_headers as $header) {
          $header['id'] = 'toc' . ++$toc_counter;
          $header['page'] = $page_num;
          $headers[] = $header;
        }
      }
    }
  }
  if (!$is_multi_page_toc_visible) {
    $headers = get_toc_heading_matches($expanded_content, $set_depth);
    foreach ($headers as $index => $header) {
      $headers[$index]['id'] = 'toc' . ($heading_offset + $index + 1);
      $headers[$index]['page'] = $current_page;
    }
  }
  $header_count = count($headers);

  if($top_level < 1){$top_level = 1;}
  if($top_level > 6){$top_level = 6;}

  $current_depth          = $top_level - 1;
  $prev_depth             = $top_level - 1;
  $max_depth              = (($depth == 0) ? 6 : intval($depth)) - $top_level + 1;


  if($header_count > 0){
    $toc_list .= '<' . $list_tag . (($current_depth == $top_level - 1) ? ' class="toc-list open"' : '') . '>';
  }
  $page_urls = array();
  for($i=0;$i < $header_count;$i++){
    $depth = 0;
    $h_actual = $headers[$i]['tag'];

    switch($h_actual) {
      case 'h1': $depth = 1 - $top_level + 1; break;
      case 'h2': $depth = 2 - $top_level + 1; break;
      case 'h3': $depth = 3 - $top_level + 1; break;
      case 'h4': $depth = 4 - $top_level + 1; break;
      case 'h5': $depth = 5 - $top_level + 1; break;
      case 'h6': $depth = 6 - $top_level + 1; break;
    }
    if($depth >= 1 && $depth <= $max_depth){
      if($current_depth == $depth && $i != 0){
        $toc_list .= '</li>';
        $counters[$current_depth - 1] ++;
      }
      while($current_depth > $depth){
        $toc_list .= '</li></'.$list_tag.'>';

        $current_depth--;

        $counters[$current_depth] = 0;
        $counters[$current_depth - 1] ++;
      }
      if($current_depth != $prev_depth){
        $toc_list .= '</li>';
        $counters[$current_depth - 1] ++;
      }
      while($current_depth < $depth){
        $toc_list .= '<'.$list_tag.'>';

        $current_depth++;
        $counters[$current_depth - 1] ++;
      }
      $hide_class = null;
      if ( $depth_option != 0 && $depth >= $depth_option ) {
        $hide_class = ' class="display-none"';
      }
      $counter++;

      // 見出しテキストを取得
      $text = $headers[$i]['text'];
      // アコーディオンボタンのアイコンを削除
      $text = str_replace(
        '<span class="wp-block-accordion-heading__toggle-icon" aria-hidden="true">+</span>',
        '',
        $text
      );
      $text = strip_tags($text);
      if ($is_multi_page_toc_visible) {
        $link = '';
        // 別ページの見出しへのページ付きURL
        if ($current_page !== intval($headers[$i]['page'])) {
          $target_page = intval($headers[$i]['page']);
          // 同じページの複数見出しに対するURL生成の重複防止
          if (!isset($page_urls[$target_page])) {
            $page_urls[$target_page] = get_toc_page_url($post, $target_page);
          }
          $link = $page_urls[$target_page];
        }

        $toc_list .= '<li'.$hide_class.'><a href="'.esc_url($link.'#'.$headers[$i]['id']).'" tabindex="0">' . $text . '</a>';
      } else {
        $toc_list .= '<li'.$hide_class.'><a href="#' . $headers[$i]['id'] . '" tabindex="0">' . $text . '</a>';
      }
      $prev_depth = $depth;
    }
  }
  while($current_depth >= 1 ){
    $toc_list .= '</li></'.$list_tag.'>';
    $current_depth--;
  }

  ///////////////////////////////////////////
  // 目次タグの生成
  ///////////////////////////////////////////
  if($id!==''){$id = ' id="' . $id . '"';}else{$id = '';}
  if (is_toc_toggle_switch_enable()) {
    $checked = null;
    $is_visible = apply_filters('is_toc_content_visible', is_toc_content_visible());
    if ($is_visible) {
      $checked = ' checked';
    }
    $title_elm = 'label';
    // if ($is_widget) {
    //   $toc_check = null;
    //   $label_for = null;
    // } else {
    //   global $_TOC_INDEX;
    //   $toc_id = 'toc-checkbox-'.$_TOC_INDEX;
    //   $toc_check = '<input type="checkbox" class="toc-checkbox" id="'.$toc_id.'"'.$checked.'>';
    //   $label_for = ' for="'.$toc_id.'"';
    //   $_TOC_INDEX++;
    // }
    global $_TOC_INDEX;
    $toc_id = 'toc-checkbox-'.$_TOC_INDEX;
    $toc_check = '<input type="checkbox" class="toc-checkbox" id="'.$toc_id.'"'.$checked.'>';
    $label_for = ' for="'.$toc_id.'"';
    $_TOC_INDEX++;
  } else {
    $title_elm = 'div';
    $toc_check = null;
    $label_for = null;
  }
  $html .= '
  <div' . $id . ' class="' . $class . get_additional_toc_classes() . ' border-element">'.$toc_check.
    '<'.$title_elm.' class="toc-title"'.$label_for.'>' . $title . '</'.$title_elm.'>
    <div class="toc-content">
    ' . $toc_list .'
    </div>
  </div>';

  global $_TOC_AVAILABLE_H_COUNT;
  $_TOC_AVAILABLE_H_COUNT = $counter;
  if (!is_toc_display_count_available($counter)){
    return ;
  }

  return apply_filters('get_toc_tag',$html, $harray, $is_widget );
}
endif;

if ( !function_exists( 'is_total_the_page_toc_visible' ) ):
function is_total_the_page_toc_visible(){
  //プラグインのフォーラムページの場合
  if (is_plugin_fourm_page()) {
    return false;
  }

  //投稿・固定・カテゴリー・タブページでない場合
  if (!is_singular() && !is_category() && !is_tag()) {
    return false;
  }

  //目次が非表示の場合
  if (!is_toc_visible()) {
    return false;
  }

  //投稿ページだと表示しない
  if (!is_single_toc_visible() && is_single()) {
    return false;
  }

  //固定ページだと表示しない
  if (!is_page_toc_visible() && is_page()) {
    return false;
  }

  //カテゴリーページだと表示しない
  if (!is_category_toc_visible() && is_category()) {
    return false;
  }

  //タグページだと表示しない
  if (!is_tag_toc_visible() && is_tag()) {
    return false;
  }

  //投稿ページで非表示になっていると表示しない
  if (!is_the_page_toc_visible()) {
    return false;
  }

  return true;
}
endif;

//最初のH2タグの前に目次を挿入する
//ref:https://qiita.com/wkwkrnht/items/c2ee485ff1bbd81325f9
add_filter('the_content', 'add_toc_before_1st_h2', get_toc_filter_priority());
add_filter('the_category_content', 'add_toc_before_1st_h2', get_toc_filter_priority());
add_filter('the_tag_content', 'add_toc_before_1st_h2', get_toc_filter_priority());
if ( !function_exists( 'add_toc_before_1st_h2' ) ):
function add_toc_before_1st_h2($the_content){
  global $_TOC_WIDGET_OR_SHORTCODE_USED;

  //Table of Contents Plusプラグインが有効な際は目次機能は無効
  if (class_exists( 'toc' )) {
    return $the_content;
  }

  //プラグインのフォーラムページの場合は目次機能は無効
  if (is_plugin_fourm_page()) {
    return $the_content;
  }

  //ページ上で目次が非表示設定（ショートコードも未使用）になっている場合
  if (!is_total_the_page_toc_visible() && !$_TOC_WIDGET_OR_SHORTCODE_USED && !is_active_widget( false, false, 'toc', true )) {
    return $the_content;
  }

  $harray      = array();

  $depth       = intval(get_toc_depth()); //2-6 0で全て
  $set_depth = $depth;//
  if (intval($set_depth) == 0) {
    $set_depth = 6;
  }

  $heading_offset = 0;
  $html = get_toc_tag($the_content, $harray, false, 0, $heading_offset);

  // 目次と同じ抽出条件による本文見出しの通し番号
  $headers = get_toc_heading_matches($the_content, $set_depth);
  if ($headers) {
    $count = $heading_offset + 1;
    $content_parts = array();
    $content_offset = 0;
    foreach ($headers as $header) {
      // 同一テキストの見出しの誤置換を防ぐための本文位置による再構築
      $content_parts[] = substr($the_content, $content_offset, $header['offset'] - $content_offset);
      // 目次非表示時の従来の見出し内HTMLの維持
      $heading_content = $html ? get_h_inner_content($header['text']) : $header['text'];
      $content_parts[] = $header['open'].'<span id="toc'.$count.'">'.
        $heading_content.'</span>'.$header['close'];
      $content_offset = $header['offset'] + strlen($header['html']);
      $count++;
    }
    $content_parts[] = substr($the_content, $content_offset);
    $the_content = implode('', $content_parts);
  }
  // 自動表示が有効な場合の目次HTMLの挿入
  if ($html && is_total_the_page_toc_visible()) {
    $h2result = get_h2_included_in_body( $the_content );//本文にH2タグが含まれていれば取得
    $html = str_replace('<div class="toc ', '<div id="toc" class="toc ', $html);
    $the_content = preg_replace(H2_REG, $html.PHP_EOL.PHP_EOL.$h2result, $the_content, 1);
  }

  //var_dump($the_content);
  return $the_content;
}
endif;


//ページ上で目次を利用しているか
if ( !function_exists( 'is_the_page_toc_use' ) ):
function is_the_page_toc_use(){
  global $_TOC_AVAILABLE_H_COUNT;
  global $_TOC_WIDGET_USED_IN_SINGULAR_CONTENT_MIDDLE_WIDET_AREA;
  if (is_category()) {
    $cat_id = get_query_var('cat');
    $content = get_the_category_content($cat_id, true);
  } elseif (is_tag()) {
    $tag_id = get_queried_object_id();
    $content = get_the_tag_content($tag_id, true);
  } else {
    $content = get_the_content();
  }
  return (is_singular() || is_category() || is_tag()) && !is_plugin_fourm_page() &&
    //最初のH2手前に表示する場合
    (
      is_toc_visible() &&
      is_the_page_toc_visible() &&
      (
        (is_single() && is_single_toc_visible()) ||
        (is_page() && is_page_toc_visible()) ||
        (is_category() && is_category_toc_visible()) ||
        (is_tag() && is_tag_toc_visible())
      ) &&
      is_toc_display_count_available($_TOC_AVAILABLE_H_COUNT)
    )
    //ショートコードで表示する場合
    || ((is_singular() || is_category() || is_tag()) && (
      //投稿・固定ページの本文中ウィジェットエリアで目次ウィジェットが使われているか
      $_TOC_WIDGET_USED_IN_SINGULAR_CONTENT_MIDDLE_WIDET_AREA
      //本文内で目次ウィジェットが使われているか
      || preg_match('/\[toc.*?\]/', $content)
    ));
}
endif;

//目次生成用の展開した本文の取得
if ( !function_exists( 'get_toc_expanded_content' ) ):
function get_toc_expanded_content(){
  if (is_singular() || is_category() || is_tag()) {
    if (is_category()) {
      $the_content = get_the_category_content(null, true);
    } elseif (is_tag()) {
      $the_content = get_the_tag_content(null, true);
    } else {
      $the_content = get_the_content();
    }
    $the_content = get_shortcode_removed_content($the_content);
    if (!is_classicpress()) {
      $the_content = do_blocks($the_content);
    }
    $the_content = do_shortcode($the_content);
    return apply_filters('get_toc_expanded_content', $the_content);
  }
}
endif;

//目次の表示数は満たしているか
if ( !function_exists( 'is_toc_display_count_available' ) ):
function is_toc_display_count_available($h_count){
  $display_count = intval(get_toc_display_count());
  return (intval($h_count) >= $display_count);
}
endif;

// 投稿・固定ページやウィジェットなどに目次が使われているか
if ( !function_exists( 'is_toc_widget_used_in_singular_content_widget_area' ) ):
function is_toc_widget_used_in_singular_content_widget_area($widget_id) {
  $widget_areas = wp_get_sidebars_widgets();
  foreach ($widget_areas as $key => $widget_area) {
    if (isset($key) && (($key === 'single-content-middle') || ($key === 'page-content-middle'))) {
      $widgets = $widget_area;
      foreach ($widgets as $keyw => $widget) {
        if ($widget === $widget_id) {
          return true;
        }
      }
    }
  }
  return false;
}
endif;

/**
 * 投稿のソースコードから同期パターンブロックを展開する
 *
 * @param string $content 投稿のソースコード（HTML形式のブロックコンテンツ）
 * @return string 同期パターンが展開されたコンテンツ
 */
if ( !function_exists( 'expand_synced_patterns' ) ):
function expand_synced_patterns($content) {
  // ブロックエディタが有効な場合のみ同期パターンを展開
  if (!function_exists('do_blocks')) {
    // ブロックエディタが無効な場合はそのまま返す
    return $content;
  }

  // 同期パターンブロックの正規表現パターン
  $pattern = '/<!-- wp:block \{"ref":(\d+)\} \/-->/';

  // パターンマッチして置換を実行
  $expanded_content = preg_replace_callback($pattern, function($matches) {
    $pattern_id = intval($matches[1]);

    // パターンブロックの投稿を取得
    $pattern_post = get_post($pattern_id);

    // パターンが存在し、正しいタイプかチェック
    // isset() でプロパティの存在を確認してから比較する（stdClass 等でのプロパティ未定義警告を防ぐ）
    if ($pattern_post &&
      isset($pattern_post->post_type) && $pattern_post->post_type === 'wp_block' &&
      isset($pattern_post->post_status) && $pattern_post->post_status === 'publish') {

      // パターンのコンテンツを再帰的に処理（入れ子の同期パターンにも対応）
      return expand_synced_patterns($pattern_post->post_content);
    }

    // パターンが見つからない場合は元のブロックをそのまま返す
    return $matches[0];

  }, $content);

  return $expanded_content;
}
endif;
