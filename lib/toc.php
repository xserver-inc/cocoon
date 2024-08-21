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
  if (is_toc_heading_inner_html_tag_enable()) {
    return $h_content;
  } else {
    return strip_tags($h_content);
  }
}
endif;

//目次部分の取得（$expanded_contentには、ショートコードが展開された本文を入れる）
if ( !function_exists( 'get_toc_tag' ) ):
function get_toc_tag($expanded_content, &$harray, $is_widget = false, $depth_option = 0){
  //フォーラムページだと表示しない
  if (is_plugin_fourm_page()) {
    return;
  }

  $content     = $expanded_content;
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

  preg_match_all('/<([hH][1-6]).*?>(.*?)<\/[hH][1-6].*?>/us', $content, $headers);
  $header_count = count($headers[0]);

  if($top_level < 1){$top_level = 1;}
  if($top_level > 6){$top_level = 6;}

  $current_depth          = $top_level - 1;
  $prev_depth             = $top_level - 1;
  $max_depth              = (($depth == 0) ? 6 : intval($depth)) - $top_level + 1;


  if($header_count > 0){
    $toc_list .= '<' . $list_tag . (($current_depth == $top_level - 1) ? ' class="toc-list open"' : '') . '>';
  }
  for($i=0;$i < $header_count;$i++){
    $depth = 0;
    switch(strtolower($headers[1][$i])){
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
      $text = strip_tags($headers[2][$i]);
      if (is_toc_heading_inner_html_tag_enable()) {
        $text = $headers[2][$i];
        $text = preg_replace('{<a.*?>(.*?)</a>}', "$1", $text);
      }
      $toc_list .= '<li'.$hide_class.'><a href="#toc' . $counter . '" tabindex="0">' . $text . '</a>';
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

  $html = get_toc_tag($the_content, $harray);

  //目次タグが出力されない（目次が不要）時は、そのまま本文を返す
  if (!$html) {
    return $the_content;
  }

  ///////////////////////////////////////
  // PHPの見出し処理（条件によっては失敗するかも）
  ///////////////////////////////////////
  $res = preg_match_all('/(<('.implode('|', $harray).')[^>]*?>)(.*?)(<\/h[2-6]>)/is', $the_content, $m);

  $tag_all_index = 0;
  $tag_index = 1;
  $h_index = 2;
  $h_content_index = 3;
  $tag_end_index = 4;
  if ($res && $m[0]) {
    $i = 0;
    $count = 1;
    foreach ($m[$tag_all_index] as $value) {
      //var_dump($m[0][$i]);
      $tag_all = $m[$tag_all_index][$i];
      $tag = $m[$tag_index][$i];
      $h = $m[$h_index][$i];
      $h_content = get_h_inner_content($m[$h_content_index][$i]);
      $tag_end = $m[$tag_end_index][$i];

      $now_depth = intval(str_replace('h', '', $h));

      //設定より見出しが深い場合はスキップ
      if ($set_depth < $now_depth) {
        $i++;
        continue;
      }

      $new = $tag.'<span id="toc'.strval($count).'">'.$h_content.'</span>'.$tag_end;

      $the_content = preg_replace('/'.preg_quote($value, '/').'/', $new, $the_content, 1);

      $i++;
      $count++;
    }

  }
  //機能が有効な時のみ（ショートコードでは実行しない）
  if (is_total_the_page_toc_visible()) {
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

//
if ( !function_exists( 'is_toc_widget_used_in_singular_content_widget_area' ) ):
  function is_toc_widget_used_in_singular_content_widget_area($widget_id) {
     $widget_areas = wp_get_sidebars_widgets();
     foreach ($widget_areas as $key => $widget_area) {
        // _v($key);
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