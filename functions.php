<?php
require_once 'lib/_defins.php'; //定数を定義
require_once 'lib/admin.php'; //管理者機能（functions.phpで呼ばないと動作しないので）
require_once 'lib/admin-tinymce-qtag.php'; //管理者用編集ボタン機能（functions.phpで呼ばないと動作しないので）


//本文部分の冒頭を綺麗に抜粋する
if ( !function_exists( 'get_content_excerpt' ) ):
function get_content_excerpt($content, $length = 70){
  $content =  preg_replace('/<!--more-->.+/is', '', $content); //moreタグ以降削除
  $content =  strip_shortcodes($content);//ショートコード削除
  $content =  strip_tags($content);//タグの除去
  $content =  str_replace('&nbsp;', '', $content);//特殊文字の削除（今回はスペースのみ）
  $content =  preg_replace('/\[.+?\]/i', '', $content); //ショートコードを取り除く
  $content =  preg_replace(URL_REG, '', $content); //URLを取り除く
  // $content =  preg_replace('/\s/iu',"",$content); //余分な空白を削除
  $over    =  intval(mb_strlen($content)) > intval($length);
  $content =  mb_substr($content, 0, $length);//文字列を指定した長さで切り取る

  return $content;
}
endif;

//WP_Queryの引数を取得
if ( !function_exists( 'get_related_wp_query_args' ) ):
function get_related_wp_query_args(){
  global $post;
  if (!$post) {
    $post = get_random_posts(1);
  }
  //var_dump($post);
  if ( true ) {
  //if ( is_related_entry_association_category() ) {
    //カテゴリ情報から関連記事をランダムに呼び出す
    $categories = get_the_category($post->ID);
    $category_IDs = array();
    foreach($categories as $category):
      array_push( $category_IDs, $category->cat_ID);
    endforeach ;
    if ( empty($category_IDs) ) return;
    return $args = array(
      'post__not_in' => array($post->ID),
      'posts_per_page'=> intval(get_related_entry_count()),
      'category__in' => $category_IDs,
      'orderby' => 'rand',
    );
  } else {
    //タグ情報から関連記事をランダムに呼び出す
    $tags = wp_get_post_tags($post->ID);
    $tag_IDs = array();
    foreach($tags as $tag):
      array_push( $tag_IDs, $tag->term_id);
    endforeach ;
    if ( empty($tag_IDs) ) return;
    return $args = array(
      'post__not_in' => array($post -> ID),
      'posts_per_page'=> intval(10),
      //'posts_per_page'=> intval(get_related_entry_count()),
      'tag__in' => $tag_IDs,
      'orderby' => 'rand',
    );
  }
}
endif;

//images/no-image.pngを使用するimgタグに出力するサイズ関係の属性
if ( !function_exists( 'get_noimage_sizes_attr' ) ):
function get_noimage_sizes_attr($image = null){
  if (!$image) {
    $image = get_template_directory_uri().'/images/no-image-160.png';
  }
  $sizes = ' srcset="'.$image.' 160w" width="160" height="90" sizes="(max-width: 160px) 160vw, 90px"';
  return $sizes;
}
endif;

//投稿ナビのサムネイルタグを取得する
if ( !function_exists( 'get_post_navi_thumbnail_tag' ) ):
function get_post_navi_thumbnail_tag($id, $width = 120, $height = 67){
  $thumb = get_the_post_thumbnail( $id, array($width, $height), array('alt' => '') );
  if ( !$thumb ) {
    $image = get_template_directory_uri().'/images/no-image-%s.png';
    //表示タイプ＝デフォルト
    if ($width == 120) {
      $w = '160';
      $image = sprintf($image, $w);
      $wh_attr = ' srcset="'.$image.' 120w" width="120" height="67" sizes="(max-width: 120px) 120vw, 67px"';
    } else {//表示タイプ＝スクエア
      $w = '150';
      $image = sprintf($image, $w);
      $wh_attr = ' srcset="'.$image.' 120w" width="120" height="120" sizes="(max-width: 120px) 120vw, 120px"';
    }
    $thumb = '<img src="'.$image.'" alt="NO IMAGE" class="no-image post-navi-no-image"'.$wh_attr.' />';
  }
  return $thumb;
}
endif;

///////////////////////////////////////
// グローバルナビに説明文を加えるウォーカークラス
///////////////////////////////////////
class menu_description_walker extends Walker_Nav_Menu {
  function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    global $wp_query;
    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

    $class_names = $value = '';

    $classes = empty( $item->classes ) ? array() : (array) $item->classes;
    //$classes[] = 'fa';
    if ($item->description) {
      $classes[] = 'menu-item-has-description';
    }
    //var_dump($classes);

    $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
    $class_names = ' class="'. esc_attr( $class_names ) . '"';
    $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

    $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
    $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
    $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
    $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

    $prepend = '<div class="item-label">';
    $append = '</div>';
    $description  = ! empty( $item->description ) ? '<div class="item-description sub-caption">'.esc_attr( $item->description ).'</div>' : '';

    // if($depth != 0) {
    //   $description = $append = $prepend = "";
    // }

    $item_output = $args->before;
    $item_output .= '<a'. $attributes .'>';
    $item_output .= '<div class="caption-wrap">';
    $item_output .= $args->link_before .$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
    $item_output .= $description.$args->link_after;
    $item_output .= '</div>';
    $item_output .= '</a>';
    $item_output .= $args->after;

    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }
}

//アップデートチェックの初期化
require 'theme-update-checker.php'; //ライブラリのパス
$example_update_checker = new ThemeUpdateChecker(
  strtolower(THEME_NAME), //テーマフォルダ名
  'http://example.com/example-theme/update-info.json' //JSONファイルのURL
);

//アーカイブタイトルの取得
if ( !function_exists( 'get_archive_chapter_title' ) ):
function get_archive_chapter_title(){
  $chapter_title = null;
  if( is_category() ) {//カテゴリページの場合
    $chapter_title .= single_cat_title( '<span class="fa fa-folder-open"></span>', false );
  } elseif( is_tag() ) {//タグページの場合
    $chapter_title .= single_tag_title( '<span class="fa fa-tags"></span>
', false );
  } elseif( is_tax() ) {//タクソノミページの場合
    $chapter_title .= single_term_title( '', false );
  } elseif (is_day()) {
    //年月日のフォーマットを取得
    $chapter_title .= '<span class="fa fa-calendar"></span>
'.get_the_time('Y-m-n');
  } elseif (is_month()) {
    //年と月のフォーマットを取得
    $chapter_title .= '<span class="fa fa-calendar"></span>
'.get_the_time('Y-m');
  } elseif (is_year()) {
    //年のフォーマットを取得
    $chapter_title .= '<span class="fa fa-calendar"></span>
'.get_the_time('Y');
  } elseif (is_author()) {//著書ページの場合
    $chapter_title .= esc_html(get_queried_object()->display_name);
  } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
    $chapter_title .= 'Archives';
  } else {
    $chapter_title .= 'Archives';
  }
  return $chapter_title;
}
endif;

//アーカイブ見出しの取得
if ( !function_exists( 'get_archive_chapter_text' ) ):
function get_archive_chapter_text(){
  $chapter_text = null;
  //アーカイブタイトル前
  //$chapter_text .= '<span class="archive-title-pb">'.__( '"', THEME_NAME ).'</span><span class="archive-title-text">';
  //アーカイブタイトルの取得
  $chapter_text .= get_archive_chapter_title();
  //アーカイブタイトル後
  //$chapter_text .= '</span><span class="archive-title-pa">'.__( '"', THEME_NAME );//.'</span><span class="archive-title-list-text">'.get_theme_text_list().'</span>';
  //返り値として返す
  return $chapter_text;
}
endif;

// add_action('admin_print_styles', 'my_admin_print_styles');
// function my_admin_print_styles() {
//  wp_enqueue_style( 'wp-color-picker' );
// }

//'wp-color-picker'の呼び出し順操作（最初の方に読み込む）
add_action('admin_enqueue_scripts', 'admin_scripts');
function admin_scripts($hook) {
    wp_enqueue_script('colorpicker-script', get_template_directory_uri() . '/js/color-picker.js', array( 'wp-color-picker' ), false, true);
}

//不要なテーマカスタマイザー項目を削除
//https://tenman.info/labo/snip/archives/8682
add_action( "customize_register", "customize_register_custom" );
if ( !function_exists( 'customize_register_custom' ) ):
function customize_register_custom( $wp_customize ) {
  $wp_customize->remove_control("header_image");
  //$wp_customize->remove_panel("widgets");
  $wp_customize->remove_section("colors");
  $wp_customize->remove_section("background_image");
  //$wp_customize->remove_section("static_front_page");
  //$wp_customize->remove_section("title_tagline");
  //$wp_customize->remove_control('nav');
}
endif;

//最初のH2タグの前に目次を挿入する
//ref:https://qiita.com/wkwkrnht/items/c2ee485ff1bbd81325f9
if (is_toc_visible()) {
  //優先順位の設定
  if (is_toc_before_ads()) {
    $priority = 9;
  } else {
    $priority = 10;
  }
  add_filter('the_content', 'add_toc_before_1st_h2', $priority);
}
if ( !function_exists( 'add_toc_before_1st_h2' ) ):
function add_toc_before_1st_h2($the_content){

  $content     = $the_content;
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
  $number_visible   = is_toc_number_visible(); //見出しの数字を表示するか
  if ($number_visible) {
    $list_tag = 'ol';
  } else {
    $list_tag = 'ul';
  }


  if($targetclass===''){$targetclass = get_post_type();}
  for($h = $top_level; $h <= 6; $h++){$harray[] = 'h' . $h . '';}
  //$harray = implode(',',$harray);

  preg_match_all('/<([hH][1-6]).*?>(.*?)<\/[hH][1-6].*?>/u', $content, $headers);
  $header_count = count($headers[0]);
  if($header_count > 0){
    $level = strtolower($headers[1][0]);
    if($top_level < $level){$top_level = $level;}
  }
  if($top_level < 1){$top_level = 1;}
  if($top_level > 6){$top_level = 6;}
  $top_level = $top_level;
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
    //var_dump($depth);
    if($depth >= 1 && $depth <= $max_depth){
      if($current_depth == $depth){$toc_list .= '</li>';}
      while($current_depth > $depth){
        $toc_list .= '</li></'.$list_tag.'>';
        $current_depth--;
        $counters[$current_depth] = 0;
      }
      if($current_depth != $prev_depth){$toc_list .= '</li>';}
      if($current_depth < $depth){
        $toc_list .= '<'.$list_tag.'>';
        $current_depth++;
      }
      $counters[$current_depth - 1] ++;
      $counter++;
      $toc_list .= '<li><a href="#toc' . $counter . '" tabindex="0">' . $headers[2][$i] . '</a>';
      $prev_depth = $depth;
    }
  }
  while($current_depth >= 1 ){
    $toc_list .= '</li></'.$list_tag.'>';
    $current_depth--;
  }
  if($counter >= $showcount){
    if($id!==''){$id = ' id="' . $id . '"';}else{$id = '';}
    $html .= '
    <div' . $id . ' class="' . $class . get_additional_toc_classes() . '">
      <div class="toc-title">' . $title . '</div>
      ' . $toc_list .'
    </div>';
    ///////////////////////////////////////
    // jQueryの見出し処理（PHPの置換処理と比べてこちらの方が信頼度高い）
    ///////////////////////////////////////
    // $script = '
    // (function($){
    //   $(document).ready(function(){
    //     var hxs = $(".'.$targetclass.'").find("' . implode(',', $harray) . '");
    //     //console.log(hxs);
    //     hxs.each(function(i, e) {
    //       //console.log(e);
    //       //console.log(i+1);
    //       $(e).attr("id", "toc"+(i+1));
    //     });
    //   });
    // })(jQuery);';
    // //JavaScriptの縮小化
    // $script_min = minify_js($script);
    // //javascript.jsの後に読み込む
    // wp_add_inline_script( THEME_JS, $script_min, 'after' ) ;

    ///////////////////////////////////////
    // PHPの見出し処理（条件によっては失敗するかも）
    ///////////////////////////////////////
    $res = preg_match_all('/<('.implode('|', $harray).')[^>]*?>.*?<\/h[2-6]>/i', $the_content, $m);
    // var_dump($harray);
    // var_dump($res);
    //var_dump($m);
    if ($res && $m[0] && $m[1]) {
      $i = 0;
      foreach ($m[0] as $value) {
        //var_dump($m[0][$i]);
        $h_tag = $m[1][$i];
        $new = str_replace('<'.$h_tag, '<'.$h_tag.' id="toc'.strval($i+1).'"', $value);
        // var_dump($value);
        // var_dump($new);

        $the_content = str_replace($value, $new, $the_content);

        $i++;
      }
    }

  }
  $h2result = get_h2_included_in_body( $the_content );//本文にH2タグが含まれていれば取得
  $the_content = preg_replace(H2_REG, $html.$h2result, $the_content, 1);
  //var_dump($the_content);
  return $the_content;
}
endif;

//投稿を管理画面のカテゴリリストの階層を保つ
add_filter('wp_terms_checklist_args', 'solecolor_wp_terms_checklist_args', 10, 2);
if ( !function_exists( 'solecolor_wp_terms_checklist_args' ) ):
function solecolor_wp_terms_checklist_args( $args, $post_id ){
 if ( $args['checked_ontop'] !== false ){
    $args['checked_ontop'] = false;
 }
 return $args;
}
endif;

