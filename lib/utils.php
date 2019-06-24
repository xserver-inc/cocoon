<?php //汎用関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//カテゴリーラベルのスタイルタグ属性を取得
if ( !function_exists( 'get_category_label_style_attr' ) ):
function get_category_label_style_attr($cat_id){
  $color = get_category_color($cat_id);
  if ($color) {
   return ' style="background-color: '.$color.';border-color: '.$color.';"';
  }
}
endif;


//リンクのないカテゴリーの取得（複数）
if ( !function_exists( 'get_the_nolink_categories' ) ):
function get_the_nolink_categories(){
  $categories = null;
  foreach((get_the_category()) as $category){
    $categories .= '<span class="entry-category">'.$category->cat_name.'</span>';
  }
  return $categories;
}

endif;


//リンクのないカテゴリーの出力（複数）
if ( !function_exists( 'the_nolink_categories' ) ):
function the_nolink_categories(){
  echo get_the_nolink_categories();
}
endif;



//カテゴリリンクの取得
if ( !function_exists( 'get_the_category_links' ) ):
function get_the_category_links(){
  $categories = null;
  foreach((get_the_category()) as $category){
    $style = null;//get_category_label_style_attr($category->cat_ID);
    $categories .= '<a class="cat-link cat-link-'.$category->cat_ID.'" href="'.get_category_link( $category->cat_ID ).'"'.$style.'>'.$category->cat_name.'</a>';
  }
  return $categories;
}
endif;


//カテゴリリンクの出力
if ( !function_exists( 'the_category_links' ) ):
function the_category_links(){
  echo get_the_category_links();
}
endif;

//リンクのないカテゴリーの取得
if ( !function_exists( 'get_the_nolink_category' ) ):
function get_the_nolink_category($id = null, $is_visible = true){
  if ($id) {
    $categories = get_the_category($id);
  } else {
    $categories = get_the_category();
  }
  $display_class = null;
  if (!$is_visible) {
    return;
    //$display_class = ' display-none';
  }

  //var_dump($categories);
  if ( isset($categories[0]) ) {
    $category = $categories[0];
    return '<span class="cat-label cat-label-'.$category->cat_ID.$display_class.'">'.$category->cat_name.'</span>';
  }
}
endif;


//リンクのないカテゴリーの出力
if ( !function_exists( 'the_nolink_category' ) ):
function the_nolink_category($id = null, $is_visible = true){
  $is_visible = apply_filters( 'is_category_label_visible', $is_visible );
  echo get_the_nolink_category($id, $is_visible);
}
endif;


//タグリンクの取得
if ( !function_exists( 'get_the_tag_links' ) ):
function get_the_tag_links(){
  $tags = null;
  $posttags = get_the_tags();
  if ( $posttags ) {
    foreach(get_the_tags() as $tag){
      $tags .= '<a class="tag-link tag-link-'.$tag->term_id.' border-element" href="'.get_tag_link( $tag->term_id ).'">'.$tag->name.'</a>';
    }
  }
  return $tags;
}
endif;


//タグリンクの出力
if ( !function_exists( 'the_tag_links' ) ):
function the_tag_links(){
  echo get_the_tag_links();
}
endif;


//コメントが許可されているか
if ( !function_exists( 'is_comment_allow' ) ):
function is_comment_allow(){
  global $post;
  if ( isset($post->comment_status) ) {
    return $post->comment_status == 'open';
  }
  return false;
}
endif;


//現在のカテゴリをカンマ区切りテキストで取得する
if ( !function_exists( 'get_category_ids' ) ):
function get_category_ids(){
  if ( is_single() ) {//投稿ページでは全カテゴリー取得
    $categories = get_the_category();
    $category_IDs = array();
    foreach($categories as $category):
      array_push( $category_IDs, $category -> cat_ID);
    endforeach ;
    return $category_IDs;
  } elseif ( is_category() ) {//カテゴリページではトップカテゴリーのみ取得
    // $categories = get_the_category();
    // //_v($categories);
    // $cat_now = $categories[0];
    // //_v($cat_now->cat_ID);
    // return array( $cat_now->cat_ID );
    // global $cat;
    // return $cat;
    // $cat_info = get_category( $cat );
    // _v($cat_info);
    $obj = get_queried_object();
    return array( $obj->cat_ID );
  }
  return null;
}
endif;


//AdSense用のフォーマットに変換
if ( !function_exists( 'to_adsense_format' ) ):
function to_adsense_format($format){
  switch ($format) {
    case AD_FORMAT_SINGLE_RECTANGLE:
      $format = DATA_AD_FORMAT_RECTANGLE;
      break;
    case AD_FORMAT_DABBLE_RECTANGLE:
      $format = DATA_AD_FORMAT_RECTANGLE;
      break;
  }
  return $format;
}
endif;

//フォーマットを指定して広告テンプレートファイル呼び出す
if ( !function_exists( 'get_template_part_with_ad_format' ) ):
function get_template_part_with_ad_format($format = DATA_AD_FORMAT_AUTO, $wrap_class = null, $label_visible = 1, $ad_code = null){
  // if ($wrap_class) {
  //   echo '<div class="'.$wrap_class.'">';
  // }
  if (isset($wrap_class)) {
    $wrap_class = ' '.trim($wrap_class).' ad-'.$format;
  }
  if ($label_visible) {
    $wrap_class .= ' ad-label-visible';
  } else {
    $wrap_class .= ' ad-label-invisible';
  }
  //var_dump($format);
  //$format変数をテンプレートファイルに渡す
  set_query_var('format', $format);
  //$wrap_class変数をテンプレートファイルに渡す
  set_query_var('wrap_class', $wrap_class);
  //$ad_code変数をテンプレートファイルに渡す
  set_query_var('ad_code', $ad_code);
  //広告テンプレートの呼び出し
  get_template_part('tmp/ad');
  // if ($wrap_class) {
  //   echo '</div>';
  // }
}
endif;

//オプション付きのテンプレート呼び出し
if ( !function_exists( 'get_template_part_with_option' ) ):
function get_template_part_with_option($slug, $option = null){
  //$option変数をテンプレートファイルに渡す
  set_query_var('option', $option);
  //広告テンプレートの呼び出し
  get_template_part($slug);
}
endif;

//スキンオプションが存在するか
if ( !function_exists( 'is_skin_option' ) ):
function is_skin_option($name){
  global $_THEME_OPTIONS;
  return isset($_THEME_OPTIONS[$name]);
}
endif;

//スキンオプションの取得
if ( !function_exists( 'get_skin_option' ) ):
function get_skin_option($name){
  global $_THEME_OPTIONS;
  //スキンにより固定値がある場合は採用する
  if (isset($_THEME_OPTIONS[$name])) {
    return $_THEME_OPTIONS[$name];
  }
}
endif;

//オプションの値をデータベースに保存する
if ( !function_exists( 'update_theme_option' ) ):
function update_theme_option($option_name){
  // //スキンにより固定値がある場合はデータベースに保存しない
  // $skin_option = get_skin_option($option_name);
  // if ($skin_option) {
  //   // _v($option_name);
  //   // _v($skin_option);
  //   return;
  // } else {
  // }
  $opt_val = isset($_POST[$option_name]) ? $_POST[$option_name] : '';
  // //広告コードからscriptを除外する（サーバーのファイアウォール・403エラー対策）
  // if (($option_name == OP_AD_CODE) || ($option_name == OP_AD_LINK_UNIT_CODE)) {
  //   $opt_val = preg_replace('{<script.+?</script>}is', '', $opt_val);
  // }
  set_theme_mod($option_name, $opt_val);
}
endif;

//オプションの値をデータベースから取得する
if ( !function_exists( 'get_theme_option' ) ):
function get_theme_option($option_name, $default = null){
  //スキンにより固定値がある場合は採用する
  $skin_option = get_skin_option($option_name);
  if ($skin_option !== null && !is_admin_php_page()) {
    if ($skin_option == '0') {
      $skin_option = 0;
    }
    // _v($_POST);
    // _v($option_name);
    // _v($skin_option);
    return $skin_option;
  }

  return get_theme_mod($option_name, $default);
}
endif;

//highlight-jsのCSS URLを取得
if ( !function_exists( 'get_highlight_js_css_url' ) ):
function get_highlight_js_css_url(){
  return get_template_directory_uri() . '/plugins/highlight-js/styles/'.get_code_highlight_style().'.css';
}
endif;

//親テーマstyle.cssの読み込み
if ( !function_exists( 'wp_enqueue_style_theme_style' ) ):
function wp_enqueue_style_theme_style(){
  wp_enqueue_style( THEME_NAME.'-style', PARENT_THEME_STYLE_CSS_URL );
}
endif;

//親テーマkeyframes.cssの読み込み
if ( !function_exists( 'wp_enqueue_style_theme_keyframes' ) ):
function wp_enqueue_style_theme_keyframes(){
  if (file_exists(PARENT_THEME_KEYFRAMES_CSS_FILE)) {
    wp_enqueue_style( THEME_NAME.'-keyframes', PARENT_THEME_KEYFRAMES_CSS_URL );
  }
}
endif;

//子テーマstyle.cssの読み込み
if ( !function_exists( 'wp_enqueue_style_theme_child_style' ) ):
function wp_enqueue_style_theme_child_style(){
  if (is_child_theme()) {
    wp_enqueue_style( THEME_NAME.'-child-style', CHILD_THEME_STYLE_CSS_URL );
  }
}
endif;

//子テーマkeyframes.cssの読み込み
if ( !function_exists( 'wp_enqueue_style_theme_child_keyframes' ) ):
function wp_enqueue_style_theme_child_keyframes(){
  if (file_exists(CHILD_THEME_KEYFRAMES_CSS_FILE)) {
    wp_enqueue_style( THEME_NAME.'-child-keyframes', CHILD_THEME_KEYFRAMES_CSS_URL );
  }
}
endif;

//スキンスタイルの読み込み
if ( !function_exists( 'wp_enqueue_style_theme_skin_style' ) ):
function wp_enqueue_style_theme_skin_style(){
  if ($skin_url = get_skin_url()) {
    wp_enqueue_style( THEME_NAME.'-skin-style', $skin_url );
  }
}
endif;

//スキンスタイルの読み込み
if ( !function_exists( 'wp_enqueue_style_theme_skin_keyframes' ) ):
function wp_enqueue_style_theme_skin_keyframes(){
  if ($skin_url = get_skin_url()) {
    $skin_keyframes_url = get_theme_skin_keyframes_url();
    //_v($skin_keyframes_url);
    if ($skin_keyframes_url) {
      wp_enqueue_style( THEME_NAME.'-skin-keyframes', $skin_keyframes_url );
    }
  }
}
endif;

//スキンのkeyframes.css URLを取得
if ( !function_exists( 'get_theme_skin_keyframes_url' ) ):
function get_theme_skin_keyframes_url(){
  if ($skin_url = get_skin_url()) {
    $keyframes_url = str_replace('style.css', 'keyframes.css', $skin_url);
    $keyframes_file = url_to_local($keyframes_url);
    if (file_exists($keyframes_file)) {
      return $keyframes_url;
    } else {
      return ;
    }
  } else {
    return ;
  }
}
endif;

//Font Awesomeの読み込み
if ( !function_exists( 'wp_enqueue_style_font_awesome' ) ):
function wp_enqueue_style_font_awesome(){
  if (!is_web_font_lazy_load_enable() || is_admin()) {
    wp_enqueue_style( 'font-awesome-style', FONT_AWESOME4_URL );
  }
}
endif;

//IcoMoonの読み込み
if ( !function_exists( 'wp_enqueue_style_icomoon' ) ):
function wp_enqueue_style_icomoon(){
  if (!is_web_font_lazy_load_enable() || is_admin()) {
    wp_enqueue_style( 'icomoon-style', FONT_ICOMOON_URL );
  }
}
endif;

//jQueryコアURLの取得
if ( !function_exists( 'get_jquery_core_url' ) ):
function get_jquery_core_url($ver){
  $url = null;
  switch ($ver) {
    case '3':
      $url = 'https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js';
      break;
    case '2':
      $url = 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js';
      break;
    case '1':
      $url = 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js';
      break;
  }
  return $url;
}
endif;

//jQueryコアのフルバージョンの取得
if ( !function_exists( 'get_jquery_core_full_version' ) ):
function get_jquery_core_full_version($ver){
  $full_ver = null;
  switch ($ver) {
    case '3':
      $full_ver = '3.3.1';
      break;
    case '2':
      $full_ver = '2.2.4';
      break;
    case '1':
      $full_ver = '1.12.4';
      break;
  }
  return $full_ver;
}
endif;

//jQuery MigrateURLの取得
if ( !function_exists( 'get_jquery_migrate_url' ) ):
function get_jquery_migrate_url($ver){
  $url = null;
  switch ($ver) {
    case '3':
      $url = 'https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/3.0.1/jquery-migrate.min.js';
      break;
    case '1':
      $url = 'https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/1.4.1/jquery-migrate.min.js';
      break;
  }
  return $url;
}
endif;

//jQuery Migrateのフルバージョンの取得
if ( !function_exists( 'get_jquery_migrate_full_version' ) ):
function get_jquery_migrate_full_version($ver){
  $full_ver = null;
  switch ($ver) {
    case '3':
      $full_ver = '3.0.1';
      break;
    case '1':
      $full_ver = '1.4.1';
      break;
  }
  return $full_ver;
}
endif;

//jQueryファイルの読み込み
if ( !function_exists( 'wp_enqueue_script_jquery_js' ) ):
function wp_enqueue_script_jquery_js(){
  wp_deregister_script('jquery');
  wp_deregister_script('jquery-core');
  wp_deregister_script('jquery-migrate');

  $ver = get_jquery_version();
  $in_footer = is_footer_javascript_enable() ? true : false;

  wp_register_script('jquery', false, array('jquery-core', 'jquery-migrate'), get_jquery_core_full_version($ver), $in_footer);

  //jQueryの読み込み
  wp_enqueue_script('jquery-core', get_jquery_core_url($ver), array(), get_jquery_core_full_version($ver), $in_footer);

  //jQuery Migrateの読み込み
  $ver = get_jquery_migrate_version();
  wp_enqueue_script('jquery-migrate', get_jquery_migrate_url($ver), array(), get_jquery_migrate_full_version($ver), $in_footer);

}
endif;

//親テーマのjavascript.jsの読み込み
if ( !function_exists( 'wp_enqueue_script_theme_js' ) ):
function wp_enqueue_script_theme_js(){

  wp_enqueue_script( THEME_JS, THEME_JS_URL, array( 'jquery' ), false, true );

  //親テーマのjavascript.jsに設定値を渡す
  $name = apply_filters( 'cocoon_localize_script_options_name', 'cocoon_localize_script_options' );
  $value = apply_filters( 'cocoon_localize_script_options_value', array(
    'is_lazy_load_enable' => is_lazy_load_enable(),
    'is_fixed_mobile_buttons_enable' => is_fixed_mobile_buttons_enable(),
  ) );
  wp_localize_script( THEME_JS, $name, $value );

  // TODO: ファイル読みこみ位置 もしくは HTML側に直接出力など よい方法を考慮
  wp_enqueue_script( 'set-event-passive', SET_EVENT_PASSIVE_JS_URL, array( ), false, true );
}
endif;

//Lazy Loadの読み込み
//https://apoorv.pro/lozad.js/
//https://qiita.com/yukagil/items/369a9972fd45223677fd
//https://qiita.com/ryo_hisano/items/42f5980720bc832e6e09
if ( !function_exists( 'wp_enqueue_lazy_load' ) ):
function wp_enqueue_lazy_load(){
  if (is_lazy_load_enable() && !is_admin() && !is_login_page()) {
    wp_enqueue_script( 'polyfill-js', get_template_directory_uri() . '/plugins/polyfill/intersection-observer.js', array(), false, true );
    wp_enqueue_script( 'lazy-load-js', get_template_directory_uri() . '/plugins/lozad.js-master/dist/lozad.min.js', array('polyfill-js'), false, true );
    $data = 'const observer = lozad(".lozad", {rootMargin: "500px"});observer.observe();';
    wp_add_inline_script( 'lazy-load-js', $data, 'after' ) ;
  }
}
endif;

//スキンのjavascript.jsの読み込み
if ( !function_exists( 'wp_enqueue_script_theme_skin_js' ) ):
function wp_enqueue_script_theme_skin_js(){
  $js_url = get_skin_js_url();
  $js_path = url_to_local($js_url);
  //javascript.jsファイルがスキンフォルダに存在する場合
  if ($js_url && file_exists($js_path)) {
    wp_enqueue_script( THEME_SKIN_JS, $js_url, array( 'jquery', THEME_JS ), false, true );
  }
}
endif;

//子テーマのjavascript.jsの読み込み
if ( !function_exists( 'wp_enqueue_script_theme_child_js' ) ):
function wp_enqueue_script_theme_child_js(){
  if (is_child_theme()) {
    wp_enqueue_script( THEME_CHILD_JS, THEME_CHILD_JS_URL, array( 'jquery', THEME_JS ), false, true );
  }
}
endif;

//はてなシェアボタンスクリプトの読み込み
if ( !function_exists( 'wp_enqueue_script_hatebu_share_button_js' ) ):
function wp_enqueue_script_hatebu_share_button_js(){
  if ( is_bottom_hatebu_share_button_visible() && is_singular() ){
    wp_enqueue_script( 'st-hatena-js', '//b.st-hatena.com/js/bookmark_button.js', array(), false, true );
  }
}
endif;

//clipboard.jsスクリプトの読み込み
if ( !function_exists( 'wp_enqueue_script_clipboard_js' ) ):
function wp_enqueue_script_clipboard_js(){
  if ( is_singular() && (is_top_copy_share_button_visible() || is_bottom_copy_share_button_visible()) ){
    wp_enqueue_script( 'clipboard-js', get_template_directory_uri().'/plugins/clipboard.js-master/dist/clipboard.min.js', array( 'jquery' ), false, true );
    $data = ("
    (function($){
      var clipboard = new Clipboard('.copy-button');//clipboardで使う要素を指定
      clipboard.on('success', function(e) {
        // console.info('Action:', e.action);
        // console.info('Text:', e.text);
        // console.info('Trigger:', e.trigger);
        $('.copy-info').fadeIn(500).delay(1000).fadeOut(500);

        e.clearSelection();
      });
    })(jQuery);
    ");
    wp_add_inline_script( 'clipboard-js', $data, 'after' ) ;
  }
}
endif;

//アイコンフォントの読み込み
if ( !function_exists( 'wp_enqueue_web_font_lazy_load_js' ) ):
function wp_enqueue_web_font_lazy_load_js(){
  if ( is_web_font_lazy_load_enable() && !is_admin() ){
    wp_enqueue_script( 'web-font-lazy-load-js', get_template_directory_uri().'/js/web-font-lazy-load.js', array(), false, true );
    $data = ('
      loadWebFont("'.FONT_AWESOME4_URL.'");
      loadWebFont("'.FONT_ICOMOON_URL.'");
    ');
    wp_add_inline_script( 'web-font-lazy-load-js', $data, 'after' ) ;
  }
}
endif;

//ソースコードのハイライト表示に必要なリソースの読み込み
if ( !function_exists( 'wp_enqueue_highlight_js' ) ):
function wp_enqueue_highlight_js(){
  //global $pagenow;
  if ( (is_code_highlight_enable() && is_singular()) || is_admin_php_page() ) {

    //ソースコードハイライト表示用のスタイル
    wp_enqueue_style( 'code-highlight-style',  get_highlight_js_css_url() );
    wp_enqueue_script( 'code-highlight-js', get_template_directory_uri() . '/plugins/highlight-js/highlight.min.js', array( 'jquery' ), false, true );
    if (is_admin_php_page()) {
      $selector = '.entry-content pre';
    } else {
      $selector = get_code_highlight_css_selector();
    }

    $data = minify_js('
          (function($){
           $("'.$selector.'").each(function(i, block) {
            hljs.highlightBlock(block);
           });
          })(jQuery);
        ');
    wp_add_inline_script( 'code-highlight-js', $data, 'after' ) ;
  }
}
endif;

// //LightboxプラグインURLの取得
// if ( !function_exists( 'get_lightbox_css_url' ) ):
// function get_lightbox_css_url(){
//   return get_template_directory_uri() . '/plugins/lightbox2/dist/css/lightbox.min.css';
// }
// endif;

// //画像ズームエフェクト用のスタイルタグを出力（管理画面用）
// if ( !function_exists( 'the_zoom_effect_link_tag' ) ):
// function the_zoom_effect_link_tag(){
//   if (is_admin_php_page()) {
//     if (is_lightbox_effect_enable()) {
//       echo '<link rel="stylesheet" href="'.get_lightbox_css_url().'" />';
//     }
//   }

// }
// endif;

//Lightboxの読み込み
if ( !function_exists( 'wp_enqueue_lightbox' ) ):
function wp_enqueue_lightbox(){
  //_v(get_image_zoom_effect());
 if ( ((is_lightbox_effect_enable() && (is_singular() || is_category())) || is_admin_php_page()) ) {
    //Lightboxスタイルの呼び出し
    wp_enqueue_style( 'lightbox-style', get_template_directory_uri() . '/plugins/lightbox2/dist/css/lightbox.min.css' );
    //Lightboxスクリプトの呼び出し
    wp_enqueue_script( 'lightbox-js', get_template_directory_uri() . '/plugins/lightbox2/dist/js/lightbox.min.js', array( 'jquery' ), false, true  );
  }
}
endif;

//lityの読み込み
if ( !function_exists( 'wp_enqueue_lity' ) ):
function wp_enqueue_lity(){
 if ( ((is_lity_effect_enable() && (is_singular() || is_category())) || is_admin_php_page()) ) {
    //lityスタイルの呼び出し
    wp_enqueue_style( 'lity-style', get_template_directory_uri() . '/plugins/lity/dist/lity.min.css' );
    //lityスクリプトの呼び出し
    wp_enqueue_script( 'lity-js', get_template_directory_uri() . '/plugins/lity/dist/lity.min.js', array( 'jquery' ), false, true  );
  }
}
endif;


//baguetteboxの読み込み
if ( !function_exists( 'wp_enqueue_baguettebox' ) ):
function wp_enqueue_baguettebox(){
 if ( ((is_baguettebox_effect_enable() && (is_singular() || is_category())) || is_admin_php_page()) ) {
    //baguetteboxスタイルの呼び出し
    wp_enqueue_style( 'baguettebox-style', get_template_directory_uri() . '/plugins/baguettebox/dist/baguetteBox.min.css' );
    //baguetteboxスクリプトの呼び出し
    wp_enqueue_script( 'baguettebox-js', get_template_directory_uri() . '/plugins/baguettebox/dist/baguetteBox.min.js', array( 'jquery' ), false, true  );
    if (is_singular() || is_category()) {
      $selector = '.entry-content';
    } else {
      $selector = '.entry-demo';
    }
    $data = minify_js('
          (function($){
           baguetteBox.run("'.$selector.'");
          })(jQuery);
        ');
    wp_add_inline_script( 'baguettebox-js', $data, 'after' ) ;

  }
}
endif;


//clingifyの読み込み
if ( !function_exists( 'wp_enqueue_clingify' ) ):
function wp_enqueue_clingify(){
  $browser_info = get_browser_info();
  $is_ie = $browser_info['browser_name'] == 'IE';
  $is_edge_version_under_16 = ($browser_info['browser_name'] == 'IE') && (intval($browser_info['browser_version']) < 16);
  //グローバルナビ追従が有効な時
  if ( is_global_navi_fixed() || is_scrollable_sidebar_enable() ) {
    //clingifyスタイルの呼び出し
    //wp_enqueue_style( 'clingify-style', get_template_directory_uri() . '/plugins/clingify/clingify.css' );
    //clingifyスクリプトの呼び出し
    wp_enqueue_script( 'clingify-js', get_template_directory_uri() . '/plugins/clingify/jquery.clingify.min.js', array( 'jquery' ), false, true  );
    if (is_global_navi_fixed()) {
      switch (get_header_layout_type()) {
        case 'center_logo':
          $selector = '.navi';
          break;
        default:
          $selector = '.header-container';
          break;
      }
      //$selector = '.sidebar-scroll';
      $data = minify_js('
              (function($){
               $("'.$selector.'").clingify();
              })(jQuery);
            ');
      wp_add_inline_script( 'clingify-js', $data, 'after' );
    }

    //position: sticky;に対応していないブラウザの場合はclingifyを実行
    if (is_scrollable_sidebar_enable() && ($is_ie || $is_edge_version_under_16)) {
      $data = minify_js('
              (function($){
               $(".sidebar-scroll").clingify();
              })(jQuery);
            ');
      wp_add_inline_script( 'clingify-js', $data, 'after' );
    }
    if (is_scrollable_main_enable() && ($is_ie || $is_edge_version_under_16)) {
      $data = minify_js('
              (function($){
               $(".main-scroll").clingify();
              })(jQuery);
            ');
      wp_add_inline_script( 'clingify-js', $data, 'after' );
    }

  }
}
endif;


//Slickの読み込み
if ( !function_exists( 'wp_enqueue_slick' ) ):
function wp_enqueue_slick(){
  //wp_enqueue_style( 'slick-style', get_template_directory_uri() . '/plugins/slick/slick.css' );
  wp_enqueue_style( 'slick-theme-style', get_template_directory_uri() . '/plugins/slick/slick-theme.css' );
  //Slickスクリプトの呼び出し
  wp_enqueue_script( 'slick-js', get_template_directory_uri() . '/plugins/slick/slick.min.js', array( 'jquery' ), false, true  );
  $autoplay = null;
  if (is_carousel_autoplay_enable()) {
    $autoplay = 'autoplay: true,';
  }
  $data = minify_js('
            (function($){
              $(".carousel-content").slick({
                dots: true,'.
                $autoplay.
                'autoplaySpeed: '.strval(intval(get_carousel_autoplay_interval())*1000).',
                infinite: true,
                slidesToShow: 6,
                slidesToScroll: 6,
                responsive: [
                    {
                      breakpoint: 1240,
                      settings: {
                        slidesToShow: 5,
                        slidesToScroll: 5
                      }
                    },
                    {
                      breakpoint: 1023,
                      settings: {
                        slidesToShow: 4,
                        slidesToScroll: 4
                      }
                    },
                    {
                      breakpoint: 834,
                      settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3
                      }
                    },
                    {
                      breakpoint: 480,
                      settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                      }
                    }
                  ]
              });

            })(jQuery);
          ');
  wp_add_inline_script( 'slick-js', $data, 'after' ) ;
}
endif;

//SlickNav
if ( !function_exists( 'wp_enqueue_slicknav' ) ):
function wp_enqueue_slicknav(){
  if (is_slicknav_visible() || is_admin_php_page()) {
    //wp_enqueue_style( 'slicknav-style', get_template_directory_uri() . '/plugins/slicknav/slicknav.css' );
    //SlickNavスクリプトの呼び出し
    wp_enqueue_script( 'slicknav-js', get_template_directory_uri() . '/plugins/slicknav/jquery.slicknav.min.js', array( 'jquery' ), false, true  );
    $data = minify_js('
              (function($){
                $(".menu-header").slicknav();
              })(jQuery);
            ');
    wp_add_inline_script( 'slicknav-js', $data, 'after' ) ;

  }
}
endif;


//ScrollHint
if ( !function_exists( 'wp_enqueue_scrollhint' ) ):
function wp_enqueue_scrollhint(){
  if (is_responsive_table_enable()) {
    //ScrollHintスタイルの呼び出し
    wp_enqueue_style( 'scrollhint-style', get_template_directory_uri() . '/plugins/scroll-hint-master/css/scroll-hint.css' );
    //ScrollHintスクリプトの呼び出し
    wp_enqueue_script( 'scrollhint-js', get_template_directory_uri() . '/plugins/scroll-hint-master/js/scroll-hint.min.js', array( 'jquery' ), false, true  );
    $data = minify_js('
          (function($){
            new ScrollHint(".scrollable-table", {
              suggestiveShadow: true,
              i18n: {
                scrollable: "'.__( 'スクロールできます', THEME_NAME ).'"
              }
            });
          })(jQuery);
        ');
    wp_add_inline_script( 'scrollhint-js', $data, 'after' ) ;

  }
}
endif;

//clingifyの読み込み
if ( !function_exists( 'wp_enqueue_stickyfill' ) ):
function wp_enqueue_stickyfill(){
  $browser_info = get_browser_info();
  $is_ie = $browser_info['browser_name'] == 'IE';
  $is_edge_version_under_16 = ($browser_info['browser_name'] == 'IE') && (intval($browser_info['browser_version']) < 16);
  //グローバルナビ追従が有効な時
  if ( is_scrollable_sidebar_enable() || is_scrollable_main_enable() ) {
    //stickyfillスクリプトの呼び出し
    wp_enqueue_script( 'stickyfill-js', get_template_directory_uri() . '/plugins/stickyfill/dist/stickyfill.min.js', array( 'jquery' ), false, true  );

    //position: sticky;に対応していないブラウザの場合はstickyfillを実行
    if (is_scrollable_sidebar_enable() && ($is_ie || $is_edge_version_under_16)) {
      $data = minify_js('
              (function($){
                var elements = $(".sidebar-scroll");
                Stickyfill.add(elements);
              })(jQuery);
            ');
      wp_add_inline_script( 'stickyfill-js', $data, 'after' );
    }

    if (is_scrollable_main_enable() && ($is_ie || $is_edge_version_under_16)) {
      $data = minify_js('
              (function($){
                var elements = $(".main-scroll");
                Stickyfill.add(elements);
              })(jQuery);
            ');
      wp_add_inline_script( 'stickyfill-js', $data, 'after' );
    }

  }
}
endif;

//Masonryの読み込み
if ( !function_exists( 'wp_enqueue_jquery_masonry' ) ):
function wp_enqueue_jquery_masonry(){
    if ((is_entry_card_type_tile_card() || is_admin()) && !is_singular()) {
      //wp_deregister_script('jquery-masonry');
      wp_register_script('jquery-masonry', false, array('jquery'), false, true);
      wp_enqueue_script('jquery-masonry');

      $common_code = '
        $("#list.ect-tile-card").masonry({
          itemSelector: ".entry-card-wrap",
          isAnimated: true
        });
      ';
      $admin_code = null;
      if (1 || is_admin()) {
        $admin_code = '
          $(function(){
            setInterval(function(){'.
              $common_code
            .'},1000);
          });
        ';
      }
      //実行コードの記入
      $code = minify_js('
        (function($){'.
          $common_code.
          $admin_code
        .'})(jQuery);
            ');
        wp_add_inline_script( 'jquery-masonry', $code, 'after' );
      }

}
endif;

//投稿画面ポスト時の確認ダイアログ
if ( !function_exists( 'wp_enqueue_confirmation_before_publish' ) ):
function wp_enqueue_confirmation_before_publish(){
  if (is_confirmation_before_publish()) {
    $post_text = __( '公開', THEME_NAME );
    $confirm_text = __( '記事を公開してもよろしいですか？', THEME_NAME );
    $data = <<< EOM
window.onload = function() {
  var id = document.getElementById('publish');
  if (id && id.value.indexOf("$post_text", 0) != -1) {
    id.onclick = publish_confirm;
  }
}
function publish_confirm() {
  if (window.confirm("$confirm_text")) {
    return true;
  } else {
    var elements = document.getElementsByTagName('span');
    for (var i = 0; i < elements.length; i++) {
      var element = elements[i];
      if (element.className.indexOf("spinner", 0) != -1) {
        element.classList.remove('spinner');
      }
    }
    document.getElementById('publish').classList.remove('button-primary-disabled');
    document.getElementById('save-post').classList.remove('button-disabled');

      return false;
  }
}
EOM;
    wp_add_inline_script( 'admin-javascript', $data, 'after' ) ;
  }
}
endif;


//Google Fontsの読み込み
if ( !function_exists( 'wp_enqueue_google_fonts' ) ):
function wp_enqueue_google_fonts(){
  if (!is_site_font_family_local()) {
    wp_enqueue_style( 'google-fonts-'.get_site_font_source(), get_site_font_source_url() );
  }
}
endif;


//設定変更CSSを読み込む
if ( !function_exists( 'wp_add_css_custome_to_inline_style' ) ):
function wp_add_css_custome_to_inline_style(){
  ob_start();//バッファリング
  get_template_part('tmp/css-custom');
  $css_custom = ob_get_clean();
  //CSSの縮小化
  $css_custom = minify_css($css_custom);
  //HTMLにインラインでスタイルを書く
  if (get_skin_url()) {
    //スキンがある場合
    $skin_keyframes_url = get_theme_skin_keyframes_url();
    if ($skin_keyframes_url) {
      wp_add_inline_style( THEME_NAME.'-skin-keyframes', $css_custom );
    } else {
      wp_add_inline_style( THEME_NAME.'-skin-style', $css_custom );
    }
  } else {
    //スキンを使用しない場合
    wp_add_inline_style( THEME_NAME.'-style', $css_custom );
  }


}
endif;


//投稿を1つランダム取得
if ( !function_exists( 'get_random_posts' ) ):
function get_random_posts($count = 1){
  $count = intval($count);
  $posts = get_posts('numberposts='.$count.'&orderby=rand');
  if ($count == 1) {
    foreach( $posts as $post ) {
      return $post;
    }
  } else {
    return $posts;
  }
}
endif;


//最新の投稿を取得
if ( !function_exists( 'get_latest_posts' ) ):
function get_latest_posts($count = 1){
  $count = intval($count);
  $posts = get_posts('numberposts='.$count);
  if ($couut == 1) {
    foreach( $posts as $post ) {
      return $post;
    }
  } else {
    return $posts;
  }
}
endif;



//更新日の取得（更新日がない場合はnullを返す）
if ( !function_exists( 'get_update_time' ) ):
function get_update_time($format = null, $post_id = null) {
  if (empty($format)) {
    $format = get_site_date_format();
  }
  $mtime = get_post_modified_time('Ymd', false, $post_id);
  $ptime = get_the_time('Ymd', $post_id);
  if ($ptime > $mtime) {
    return get_the_time($format, $post_id);
  } elseif ($ptime === $mtime) {
    return null;
  } else {
    return get_post_modified_time($format, false, $post_id);
  }
}
endif;


//ターゲットに文字列が含まれているか
if ( !function_exists( 'includes_string' ) ):
function includes_string($target, $searchstr){
  if (strpos($target, $searchstr) === false) {
    return false;
  } else {
    return true;
  }
}
endif;

//サイトアドレスが含まれているか
if ( !function_exists( 'includes_site_url' ) ):
function includes_site_url($url){
  //URLにサイトアドレスが含まれていない場合
  if (strpos($url, site_url()) === false) {
    return false;
  } else {
    return true;
  }
}
endif;

//ホームアドレスが含まれているか
if ( !function_exists( 'includes_home_url' ) ):
function includes_home_url($url){
  //URLにホームアドレスが含まれていない場合
  if (!includes_string($url, home_url())) {
    return false;
  } else {
    return true;
  }
}
endif;

//Wordpressインストールフォルダが含まれているか
if ( !function_exists( 'includes_abspath' ) ):
function includes_abspath($local){
  //URLにサイトアドレスが含まれていない場合
  if (includes_string($local, ABSPATH)) {
    return false;
  } else {
    return true;
  }
}
endif;

//ホームパスが含まれているか
if ( !function_exists( 'includes_home_path' ) ):
function includes_home_path($local){
  //URLにサイトアドレスが含まれていない場合
  if (!includes_string($local, get_abs_home_path())) {
    return false;
  } else {
    return true;
  }
}
endif;

//内部URLをローカルパスに変更
if ( !function_exists( 'url_to_local' ) ):
function url_to_local($url){
  //URLにサイトアドレスが含まれていない場合
  if (!includes_home_url($url)) {
    return false;
  }

  $path = str_replace(home_url('/'), get_abs_home_path(), $url);
  $path = str_replace('//', '/', $path);
  $path = str_replace('\\', '/', $path);

  // $path = str_replace(content_url(), WP_CONTENT_DIR, $url);
  // $path = str_replace('\\', '/', $path);
  return $path;
}
endif;

//ローカルパスを内部URLに変更
if ( !function_exists( 'local_to_url' ) ):
function local_to_url($local){
  // _v($local);
  // _v(get_abs_home_path());
  // _v(includes_home_path($local));
  // _v('----------');
  //URLにサイトアドレスが含まれていない場合
  if (!includes_home_path($local)) {
    return false;
  }
  $url = str_replace(get_abs_home_path(), home_url('/'), $local);
  $url = str_replace('\\', '/', $url);
  // _v($local);
  // _v(ABSPATH);
  // _v(site_url());
  // _v($url);

  return $url;
}
endif;


//テーマのリソースディレクトリ
if ( !function_exists( 'get_theme_resources_path' ) ):
function get_theme_resources_path(){
  $dir = WP_CONTENT_DIR.'/uploads/'.THEME_NAME.'-resources/';
  if (!file_exists($dir)) mkdir($dir, 0777, true);
  return $dir;
}
endif;

//テーマの汎用キャッシュディレクトリ
if ( !function_exists( 'get_theme_cache_path' ) ):
function get_theme_cache_path(){
  $dir = get_theme_resources_path().'cache/';
  if (!file_exists($dir)) mkdir($dir, 0777, true);
  return $dir;
}
endif;

//テーマのブログカードキャッシュディレクトリ
if ( !function_exists( 'get_theme_blog_card_cache_path' ) ):
function get_theme_blog_card_cache_path(){
  $dir = get_theme_resources_path().'blog-card-cache/';
  if (!file_exists($dir)) mkdir($dir, 0777, true);
  return $dir;
}
endif;

//AMPキャッシュディレクトリ
if ( !function_exists( 'get_theme_amp_cache_path' ) ):
function get_theme_amp_cache_path(){
  $dir = get_theme_resources_path().'amp-cache/';
  if (!file_exists($dir)) mkdir($dir, 0777, true);
  return $dir;
}
endif;

//テーマのCSSキャッシュディレクトリ
if ( !function_exists( 'get_theme_css_cache_path' ) ):
function get_theme_css_cache_path(){
  $dir = get_theme_resources_path().'css-cache/';
  if (!file_exists($dir)) mkdir($dir, 0777, true);
  return $dir;
}
endif;

//テーマのPWAキャッシュディレクトリ
if ( !function_exists( 'get_theme_pwa_cache_path' ) ):
function get_theme_pwa_cache_path(){
  $dir = get_theme_resources_path().'pwa-cache/';
  if (!file_exists($dir)) mkdir($dir, 0777, true);
  return $dir;
}
endif;

//テーマのログディレクトリ
if ( !function_exists( 'get_theme_logs_path' ) ):
function get_theme_logs_path(){
  $dir = get_theme_resources_path().'logs/';
  if (!file_exists($dir)) mkdir($dir, 0777, true);
  return $dir;
}
endif;

//Amazonログファイル
if ( !function_exists( 'get_theme_amazon_product_error_log_file' ) ):
function get_theme_amazon_product_error_log_file(){
  return get_theme_logs_path().'amazon_product_error.log';
}
endif;

//楽天ログファイル
if ( !function_exists( 'get_theme_rakuten_product_error_log_file' ) ):
function get_theme_rakuten_product_error_log_file(){
  return get_theme_logs_path().'rakuten_product_error.log';
}
endif;

//PWAのマニフェストファイルへのパス
if ( !function_exists( 'get_theme_pwa_manifest_json_file' ) ):
function get_theme_pwa_manifest_json_file(){
  //_v(get_abs_home_path().THEME_NAME.'-manifest.json');
  return get_abs_home_path().THEME_NAME.'-manifest.json';
}
endif;

//PWAのマニフェストファイルへのURL
if ( !function_exists( 'get_theme_pwa_manifest_json_url' ) ):
function get_theme_pwa_manifest_json_url(){
  return local_to_url(get_theme_pwa_manifest_json_file());
  // $url = local_to_url(get_theme_pwa_manifest_json_file());
  // return str_replace(site_url(), '', $url);
}
endif;

//PWAのサービスワーカーへのパス
if ( !function_exists( 'get_theme_pwa_service_worker_js_file' ) ):
function get_theme_pwa_service_worker_js_file(){
  //_v(get_abs_home_path().THEME_NAME.'-service-worker.js');
  return get_abs_home_path().THEME_NAME.'-service-worker.js';
}
endif;

//PWAのサービスワーカーへのパス
if ( !function_exists( 'get_theme_pwa_service_worker_js_url' ) ):
function get_theme_pwa_service_worker_js_url(){
  return local_to_url(get_theme_pwa_service_worker_js_file());
  // $url = local_to_url(get_theme_pwa_service_worker_js_file());
  // return str_replace(site_url(), '', $url);
}
endif;

//テーマのカスタムCSSファイル
if ( !function_exists( 'get_theme_css_cache_file' ) ):
function get_theme_css_cache_file(){
  $file = get_theme_css_cache_path().'css-custom.css';
  //キャッシュファイルが存在しない場合はからのファイルを生成
  if (!file_exists($file)) {
    wp_filesystem_put_contents($file,  '');
  }
  return $file;
}
endif;

//テーマのカスタムCSSファイルURL
if ( !function_exists( 'get_theme_css_cache_file_url' ) ):
function get_theme_css_cache_file_url(){
  $url = local_to_url(get_theme_css_cache_file());
  return $url;
}
endif;

// //テーマのカスタム管理者用CSSファイル
// if ( !function_exists( 'get_theme_admin_css_cache_file' ) ):
// function get_theme_admin_css_cache_file(){
//   $file = get_theme_css_cache_path().'css-custom-admin.css';
//   //キャッシュファイルが存在しない場合はからのファイルを生成
//   if (!file_exists($file)) {
//     wp_filesystem_put_contents($file,  '');
//   }
//   return $file;
// }
// endif;

// //テーマのカスタム管理者用CSSファイルURL
// if ( !function_exists( 'get_theme_admin_css_cache_file_url' ) ):
// function get_theme_admin_css_cache_file_url(){
//   $url = local_to_url(get_theme_admin_css_cache_file());
//   return $url;
// }
// endif;

//画像URLから幅と高さを取得する（同サーバー内ファイルURLのみ）
if ( !function_exists( 'get_image_width_and_height' ) ):
function get_image_width_and_height($image_url){

    //相対パスの場合はURLを追加
    if (preg_match('{^/wp-content/uploads/}', $image_url)) {
      $image_url = site_url() . $image_url;
    }

  //URLにサイトアドレスが含まれていない場合
  if (!includes_site_url($image_url)) {
    return false;
  }

  $image_file = url_to_local($image_url);

  if (file_exists($image_file)) {
    $imagesize = getimagesize($image_file);
    if ($imagesize) {
      $res = array();
      $res['width'] = $imagesize[0];
      $res['height'] = $imagesize[1];

      return $res;
    }
  }
}
endif;

//テーマ設定ページか
if ( !function_exists( 'is_admin_php_page' ) ):
function is_admin_php_page(){
  global $pagenow;
  $is_theme_settings = isset($_GET['page']) && $_GET['page'] == THEME_SETTINGS_PAFE;
  return $pagenow == 'admin.php' && $is_theme_settings;
}
endif;

//投稿新規作成ページ
if ( !function_exists( 'is_admin_post_new_php_page' ) ):
function is_admin_post_new_php_page(){
  global $pagenow;
  return $pagenow == 'post-new.php';
}
endif;

//投稿ページ
if ( !function_exists( 'is_admin_post_php_page' ) ):
function is_admin_post_php_page(){
  global $pagenow;
  return $pagenow == 'post.php';
}
endif;

//投稿・新規作成ページかどうか
if ( !function_exists( 'is_admin_post_page' ) ):
function is_admin_post_page(){
  return is_admin_post_new_php_page() || is_admin_post_php_page();
}
endif;

//ウィジェットページか
if ( !function_exists( 'is_widgets_php_page' ) ):
function is_widgets_php_page(){
  global $pagenow;
  return $pagenow == 'widgets.php';
}
endif;



//サイトのドメインを取得
if ( !function_exists( 'get_the_site_domain' ) ):
function get_the_site_domain(){
  //ドメイン情報を$results[1]に取得する
  if (preg_match( '/https?:\/\/(.+?)\//i', admin_url(), $results )) {
    return $results[1];
  } else {
    return get_domain_name(home_url());
  }
}
endif;



//URLからドメインを取得
if ( !function_exists( 'get_domain_name' ) ):
function get_domain_name($url){
  return parse_url($url, PHP_URL_HOST);
}
endif;


//拡張子のみを取得する
if ( !function_exists( 'get_extention' ) ):
function get_extention($filename){
  return preg_replace('/^.*\.([^.]+)$/D', '$1', $filename);
}
endif;


//ファイル名のみを取得する
if ( !function_exists( 'get_basename' ) ):
function get_basename($filename){
  $p = pathinfo($filename);
  return basename ( $filename, ".{$p['extension']}" );
}
endif;

//bbPressがインストールされているか
if ( !function_exists( 'is_bbpress_exist' ) ):
function is_bbpress_exist(){
  return class_exists( 'bbPress' );
}
endif;

//bbPressのページかどうか
if ( !function_exists( 'is_bbpress_page' ) ):
function is_bbpress_page(){
  if (is_bbpress_exist()) {
    if (bbp_is_topic() ||
        bbp_is_forum() ||
        bbp_is_forum_archive() ||
        bbp_is_single_forum() ||
        bbp_is_forum_edit() ||
        bbp_is_single_topic() ||
        bbp_is_topic_archive() ||
        bbp_is_topic_edit() ||
        bbp_is_topic_tag() ||
        bbp_is_topic_tag_edit() ||
        bbp_is_reply()||
        bbp_is_reply_edit() ||
        bbp_is_single_reply() ||
        bbp_is_favorites() ||
        bbp_is_subscriptions()) {
      return true;
    }
  }
}
endif;

//BuddyPressが存在するか
if ( !function_exists( 'is_buddypress_exist' ) ):
function is_buddypress_exist(){
  return class_exists('BuddyPress');
}
endif;

//buddypressのページかどうか
if ( !function_exists( 'is_buddypress_page' ) ):
function is_buddypress_page(){
  if (is_buddypress_exist()) {
    //bp-core-template.phpファイルから
    if (!bp_is_blog_page()
      ) {
      return true;
    }
  }
}
endif;

//wpForoが存在するか
if ( !function_exists( 'is_wpforo_exist' ) ):
function is_wpforo_exist(){
  return class_exists('wpForo');
}
endif;

//wpforoのページかどうか
if ( !function_exists( 'is_wpforo_plugin_page' ) ):
function is_wpforo_plugin_page($url = ''){
  if (is_wpforo_exist()) {
    //functions-template.phpファイルから
    if (
         is_wpforo_page($url)
      ) {
      return true;
    }
  }
}
endif;

//スクロール追従領域が有効化
if ( !function_exists( 'is_scrollable_sidebar_enable' ) ):
function is_scrollable_sidebar_enable(){
  return is_active_sidebar('sidebar-scroll');
}
endif;

//bbPress、BuddyPress、wpForo大インストールされているか
if ( !function_exists( 'is_plugin_fourm_exist' ) ):
function is_plugin_fourm_exist(){
  if (
       is_bbpress_exist()
    || is_buddypress_exist()
    || is_wpforo_exist()
  ) {
    return true;
  }
}
endif;

//bbPress、BuddyPress、wpForoフォーラムページかどうか
if ( !function_exists( 'is_plugin_fourm_page' ) ):
function is_plugin_fourm_page(){
  if (
       is_bbpress_page()
    || is_buddypress_page()
    || is_wpforo_plugin_page()
  ) {
    return true;
  }
}
endif;

//スクロール追従領域が有効化
if ( !function_exists( 'is_scrollable_main_enable' ) ):
function is_scrollable_main_enable(){
  return is_active_sidebar('main-scroll');
}
endif;

//ブラウザ情報の取得
//http://web-pixy.com/php-device-browser/
if ( !function_exists( 'get_browser_info' ) ):
function get_browser_info(){

  $ua = $_SERVER['HTTP_USER_AGENT'];
  $browser_name = $browser_version = $webkit_version = $platform = NULL;
  $is_webkit = false;

  //Browser
  if(preg_match('/Edge/i', $ua)){

    $browser_name = 'Edge';

    if(preg_match('/Edge\/([0-9.]*)/', $ua, $match)){

      $browser_version = $match[1];
    }

  }elseif(preg_match('/(MSIE|Trident)/i', $ua)){

    $browser_name = 'IE';

    if(preg_match('/MSIE\s([0-9.]*)/', $ua, $match)){

      $browser_version = $match[1];

    }elseif(preg_match('/Trident\/7/', $ua, $match)){

      $browser_version = 11;
    }

  }elseif(preg_match('/Presto|OPR|OPiOS/i', $ua)){

    $browser_name = 'Opera';

    if(preg_match('/(Opera|OPR|OPiOS)\/([0-9.]*)/', $ua, $match)) $browser_version = $match[2];

  }elseif(preg_match('/Firefox/i', $ua)){

    $browser_name = 'Firefox';

    if(preg_match('/Firefox\/([0-9.]*)/', $ua, $match)) $browser_version = $match[1];

  }elseif(preg_match('/Chrome|CriOS/i', $ua)){

    $browser_name = 'Chrome';

    if(preg_match('/(Chrome|CriOS)\/([0-9.]*)/', $ua, $match)) $browser_version = $match[2];

  }elseif(preg_match('/Safari/i', $ua)){

    $browser_name = 'Safari';

    if(preg_match('/Version\/([0-9.]*)/', $ua, $match)) $browser_version = $match[1];
  }

  //Webkit
  if(preg_match('/AppleWebkit/i', $ua)){

    $is_webkit = true;

    if(preg_match('/AppleWebKit\/([0-9.]*)/', $ua, $match)) $webkit_version = $match[1];
  }

  //Platform
  if(preg_match('/ipod/i', $ua)){

    $platform = 'iPod';

  }elseif(preg_match('/iphone/i', $ua)){

    $platform = 'iPhone';

  }elseif(preg_match('/ipad/i', $ua)){

    $platform = 'iPad';

  }elseif(preg_match('/android/i', $ua)){

    $platform = 'Android';

  }elseif(preg_match('/windows phone/i', $ua)){

    $platform = 'Windows Phone';

  }elseif(preg_match('/linux/i', $ua)){

    $platform = 'Linux';

  }elseif(preg_match('/macintosh|mac os/i', $ua)) {

    $platform = 'Mac';

  }elseif(preg_match('/windows/i', $ua)){

    $platform = 'Windows';
  }

  return array(

    'ua' => $ua,
    'browser_name' => $browser_name,
    'browser_version' => intval($browser_version),
    'is_webkit' => $is_webkit,
    'webkit_version' => intval($webkit_version),
    'platform' => $platform
  );
}//get_browser_info()
endif;


//サイトフォントソースコードの取得
if ( !function_exists( 'get_site_font_source' ) ):
function get_site_font_source(){
  $font_source = get_site_font_family();
  //空白を取り除く
  $font_source = str_replace('_', '', $font_source);
  //大文字を小文字に
  $font_source = strtolower($font_source);
  return $font_source;
}
endif;


//サイトフォントソースコードURLの取得
if ( !function_exists( 'get_site_font_source_url' ) ):
function get_site_font_source_url(){
  return 'https://fonts.googleapis.com/earlyaccess/'.get_site_font_source().'.css';
}
endif;

//カラーコードをRGBに変換
if ( !function_exists( 'colorcode_to_rgb' ) ):
function colorcode_to_rgb($colorcode){
  $colorcode = str_replace('#', '', $colorcode);
  $a['red'] = hexdec(substr($colorcode, 0, 2));
  $a['green'] = hexdec(substr($colorcode, 2, 2));
  $a['blue'] = hexdec(substr($colorcode, 4, 2));
  return $a;
}
endif;

//カラーコードをRGBのCSSコードに変換
if ( !function_exists( 'colorcode_to_rgb_css_code' ) ):
function colorcode_to_rgb_css_code($colorcode, $opacity = 0.2){
  $a = colorcode_to_rgb($colorcode);
  return 'rgba('.$a['red'].', '.$a['green'].', '.$a['blue'].', '.$opacity.')';
}
endif;

//投稿者が書いたページかどうか
if ( !function_exists( 'in_authors' ) ):
function in_authors($authers){
  global $post;
  if ($post) {
    //$author = get_userdata($post->post_author);
    // var_dump($post->post_author);
    // var_dump($authers);
    // var_dump(in_array($post->post_author, $authers));
    return in_array($post->post_author, $authers);
  } else {
    return false;
  }
}
endif;

//投稿者リストページか
if ( !function_exists( 'is_authors' ) ):
function is_authors($authers){
  foreach ($authers as $auther) {
    if (is_author($auther)) {
      return true;
    }
  }
  return false;
}
endif;

//テキストリストを配列に変換
if ( !function_exists( 'list_text_to_array' ) ):
function list_text_to_array($list){
  $array = explode("\n", $list); // とりあえず行に分割
  $array = array_map('trim', $array); // 各行にtrim()をかける
  $array = array_filter($array, 'strlen'); // 文字数が0の行を取り除く
  $array = array_values($array); // これはキーを連番に振りなおしてるだけ
  return $array;
}
endif;

//Rubyタイプのデバッグ関数
if ( !function_exists( 'p' ) ):
function p($value){
  var_dump($value);
}
endif;

//フォルダごとファイルを全て削除
if ( !function_exists( 'remove_all_directory' ) ):
function remove_all_directory($dir) {
  //ディレクトリが存在しないときは何もしない
  if ( !file_exists($dir) ) {
    return ;
  }
  //ディレクトリが存在する時はすべて削除する
  if ($handle = opendir("$dir")) {
    while (false !== ($item = readdir($handle))) {
      if ($item != "." && $item != "..") {
        if (is_dir("$dir/$item")) {
          remove_all_directory("$dir/$item");
        } else {
          unlink("$dir/$item");
        }
      }
    }
  closedir($handle);
  rmdir($dir);
  return true;
  }
}
endif;

//リダイレクト
if ( !function_exists( 'redirect_to_url' ) ):
function redirect_to_url($url){
  header( "HTTP/1.1 301 Moved Permanently" );
  header( "location: " . $url  );
  exit;
}
endif;

//カンマテキストのサニタイズ
if ( !function_exists( 'sanitize_comma_text' ) ):
function sanitize_comma_text($value){
  $value = str_replace(' ', '', $value);
  $value = str_replace('、', ',', $value);
  return trim($value);
}
endif;

//アクセステーブル用の現在の日時文字列
if ( !function_exists( 'get_current_db_date' ) ):
function get_current_db_date(){
  return date_i18n('Y-m-d');
}
endif;

//アクセステーブル用の現在の日時文字列（$days日前）
if ( !function_exists( 'get_current_db_date_before' ) ):
function get_current_db_date_before($days){
  return date_i18n('Y-m-d', strtotime(current_time('Y-m-d').' -'.$days.' day'));
}
endif;

//ユーザーが管理者か
if ( !function_exists( 'is_user_administrator' ) ):
function is_user_administrator(){
  $cocoon_admin_capability = apply_filters('cocoon_admin_capability', 'administrator');
  $res = current_user_can( $cocoon_admin_capability );
  return apply_filters('is_user_administrator', $res);
}
endif;

//ファイル内容の取得
if ( !function_exists( 'wp_filesystem_get_contents' ) ):
function wp_filesystem_get_contents($file, $is_exfile = false, $credentials_enable = true){
  //$creds = false;

  //ファイルが存在しないときはfalseを返す
  if (!file_exists($file) && !$is_exfile) {
    return false;
  }

  $options = array(
    'http' => array(
        'method'  => 'GET',
        'timeout' => 1.5, // タイムアウト時間
        //'protocol_version'  => '1.1',
    )
  );

  if (!$is_exfile) {//ローカル
    return file_get_contents($file);
  } else {//外部URL
    return @file_get_contents($file, false, stream_context_create($options));
  }

  // if ($credentials_enable && is_request_filesystem_credentials_enable()){
  //   $creds = request_filesystem_credentials('', '', false, false, null);
  // }

  // if (WP_Filesystem($creds)) {//WP_Filesystemの初期化
  //   global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し
  //   $contents = $wp_filesystem->get_contents($file);
  //   return $contents;
  // }

  // if (file_exists($file)) {
  //   ob_start();
  //   include($file);
  //   $contents = ob_get_clean();
  //   return $contents;
  // }
}
endif;
if ( !function_exists( 'get_file_contents' ) ):
function get_file_contents($file){
  return wp_filesystem_get_contents($file);
}
endif;

// _v(ABSPATH);
// include_once(ABSPATH.'wp-admin/includes/file.php');
if (!defined('FS_CHMOD_FILE')) {
  define( 'FS_CHMOD_FILE', ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );
}
//ファイル内容の出力
if ( !function_exists( 'wp_filesystem_put_contents' ) ):
function wp_filesystem_put_contents($new_file, $file_data, $chmod = FS_CHMOD_FILE ){
  return file_put_contents($new_file, $file_data, $chmod);
  // $creds = false;
  // if (is_request_filesystem_credentials_enable())
  //   $creds = request_filesystem_credentials('', '', false, false, null);

  // if (WP_Filesystem($creds)) {//WP_Filesystemの初期化
  //   global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し
  //   //$wp_filesystemオブジェクトのメソッドとしてファイルに書き込む
  //   return $wp_filesystem->put_contents($new_file, $file_data, $chmod);
  // }
}
endif;
if ( !function_exists( 'put_file_contents' ) ):
function put_file_contents($file, $data){
  return wp_filesystem_put_contents($file, $data);
}
endif;

//ファイルの削除
if ( !function_exists( 'wp_filesystem_delete' ) ):
function wp_filesystem_delete($file){
  $creds = false;
  if (is_request_filesystem_credentials_enable())
    $creds = request_filesystem_credentials('', '', false, false, null);

  if (WP_Filesystem($creds)) {//WP_Filesystemの初期化
    global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し
    return $wp_filesystem->delete($file);
  }
}
endif;

//最初の投稿の年を取得
if ( !function_exists( 'get_first_post_year' ) ):
function get_first_post_year(){
  $year = null;
  $args = array(
    'posts_per_page' => 1,
    'order' => 'ASC',
    'no_found_rows' => true,
  );

  //記事を古い順に1件だけ取得
  $query = new WP_Query( $args );
  if ( $query -> have_posts() ) : while ( $query -> have_posts() ) : $query -> the_post();
    $year = intval(get_the_time('Y'));//最初の投稿の年を取得
  endwhile; endif;
  wp_reset_postdata();
  return $year;

  // //記事を古い順に1件だけ取得
  // query_posts($args);
  // //query_posts('posts_per_page=1&order=ASC');
  // if ( have_posts() ) : while ( have_posts() ) : the_post();
  //   $year = intval(get_the_time('Y'));//最初の投稿の年を取得
  // endwhile; endif;
  // wp_reset_query();
  // return $year;
}
endif;

//URLエンコード後の文字列を返す
if ( !function_exists( 'get_encoded_url' ) ):
function get_encoded_url($url){
  $url = str_replace('&amp;', '&', $url);
  $url = urlencode($url);
  return $url;
}
endif;

//現在表示しているページのURL
if ( !function_exists( 'get_requested_url' ) ):
function get_requested_url(){
  // _v($_SERVER['HTTPS']);
  // _v((empty($_SERVER['HTTPS']) ? 'http://' : 'https://'));
  return (!is_ssl() ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
endif;

//クエリを取り除いたURLを取得
if ( !function_exists( 'get_query_removed_url' ) ):
function get_query_removed_url($url){
  $url = preg_replace('{\?.+$}', '', $url);
  return $url;
}
endif;

//現在表示しているページのURL（クエリを取り除く）
if ( !function_exists( 'get_query_removed_requested_url' ) ):
function get_query_removed_requested_url(){
  $url = get_requested_url();
  $url = get_query_removed_url($url);
  $url = user_trailingslashit($url);
  return $url;
}
endif;

if ( !function_exists( 'object_to_array' ) ):
function object_to_array($object){
  return json_decode(json_encode($object), true);
}
endif;

if ( !function_exists( 'add_admin_menu_separator' ) ):
function add_admin_menu_separator($position) {
  global $menu;
  $index = 0;
  foreach($menu as $offset => $section) {
    if (substr($section[2],0,9)=='separator')
      $index++;
    if ($offset>=$position) {
      $menu[$position] = array('','read',"separator{$index}",'','wp-menu-separator');
      break;
    }
  }
  ksort( $menu );
}
endif;

//ユーザーエージェントが、Windows Live Writer、もしくはOpen Live Writerかどうか
if ( !function_exists( 'is_user_agent_live_writer' ) ):
function is_user_agent_live_writer(){
  $ua = $_SERVER['HTTP_USER_AGENT'];// ユーザエージェントを取得
  if((strpos($ua, 'Windows Live Writer') !== false)
      || (strpos($ua, 'Open Live Writer') !== false)
    ){
    return true;
  }
  return false;
}
endif;

//タブレットをモバイルとしないモバイル判定関数
if ( !function_exists( 'is_useragent_robot' ) ):
//スマホ表示分岐
function is_useragent_robot(){
  $useragents = array(
    // 'Googlebot', //http://www.google.com/bot.html
    // 'YPBot', //http://www.yellowpages.com/about/legal/crawl
    // 'Yahoo! Slurp', //http://help.yahoo.com/help/us/ysearch/slurp
    // 'bingbot', //http://www.bing.com/bingbot.htm
    // 'Yeti', //http://help.naver.com/robots/
    // 'Baiduspider', //http://www.baidu.com/search/spider.html
    // 'Feedfetcher-Google', //http://www.google.com/feedfetcher.html
    // 'livedoor FeedFetcher', //http://reader.livedoor.com/
    // 'ia_archiver', //http://www.alexa.com/site/help/webmasters
    // 'YandexBot', //http://yandex.com/bots
    // 'SISTRIX Crawler', //http://crawler.sistrix.net/
    // 'msnbot', //http://search.msn.com/msnbot.htm
    // 'zenback bot', //http://www.logly.co.jp/
    // 'Y!J-BRI', //http://help.yahoo.co.jp/help/jp/search/indexing/indexing-15.html
    // 'TurnitinBot', //http://www.turnitin.com/robot/crawlerinfo.html
    // 'Google Desktop', //http://desktop.google.com/
    // 'newzia crawler', //http://www.logly.co.jp/
    // 'BaiduMobaider', //http://www.baidu.jp/spider/
    'Y!J-SRD', //検索 日本最大のポータルサイト Yahoo Japan の携帯向け検索ロボット
    'Y!J-MBS/1.0', //検索 日本最大のポータルサイト Yahoo Japan の携帯向け検索ロボット
    'Yahoo! Slurp', //検索 日本最大のポータルサイト Yahoo Japan のＰＣ向け検索ロボット
    'Yahoo! DE Slurp', //検索 日本最大のポータルサイト Yahoo Japan のＰＣ向け検索ロボット
    'Googlebot-Mobile/', //検索 世界最大の検索サイト Google の携帯向け検索ロボット
    'Googlebot/', //検索 世界最大の検索サイト Google のＰＣ向け検索ロボット
    'Google Page Speed Insights', //PageSpeed Insights
    'adsence-Google', //広告 Google社の広告確認用ロボット
    'msnbot', //検索 マイクロソフト社の検索ロボット
    'bingbot/', //検索 マイクロソフト系のMSN の検索ロボット
    'Hatena', //検索 株式会社はてな の検索ロボット
    'MicroAd/', //広告 MicroAd社の広告確認用ロボット
    'Baidu', //検索 中国最大の検索サイト 百度(バイドゥー)の検索ロボット
    'MJ12bo', //検索 majestic12.co.uk という、英国のサイト。集中攻撃を受けた。
    'Steeler', //検索 東京大学の教育用の検索ロボット。害はない。
    'YodaoBot', //検索 中国系とされる検索エンジン。集中攻撃を受けた。
    'OutfoxBot', //検索 正体不明。そうアクセスが多くないので無視。
    'Pockey', //検索 正体不明。そうアクセスが多くないので無視。
    'psbot', //検索 http://www.picsearch.com/ のイメージ検索エンジン。
    'Yeti/', //検索 韓国最大の検索サイト Never の検索ロボット(2007年からか？)
    'Websi', //検索 正体不明。そうアクセスが多くないので無視。
    'Wget/', //検索 正体不明。そうアクセスが多くないので無視。
    'NaverBot', //検索 韓国最大の検索サイト Never の検索ロボット
    'BecomeBot', //検索 ショッピングリサーチツールのアメリカ Become.comの検索ロボット
    'heritr', //検索 Web Archive 製のオープンソースのクローラ
    'DotBot', //検索 アメリカ、シアトルのクローズの検索ロボット？
    'Twiceler', //検索 http://cuil.com/ の検索ロボット。集中攻撃で悪評、サイトも消去か？
    'ichiro', //検索 NTTレゾナント株式会社(goo を提供)の検索ロボット
    'archive.org_bot', //検索 書籍、動画、音楽のライブラリ www.archive.org の検索ロボット
    'YandexBot/', //検索 ロシアの画像系の検索サイト
    'ICC-Crawler', //検索 情報通信研究機構による学術研究用の検索ロボット
    'SiteBot/', //検索 正体不明の検索ロボット。なんだろう？悪さもしない。
    'TurnitinBot/', //検索 turnitin.comの検索ロボット。悪さもしない。
    'Purebot/', //検索 puritysearchの検索ロボット。悪さはしないが、ＨＰは偽装だと思う。
    'GTmetrix', //GTmetrix
    'seocheki', //SEOチェキ
    'validator', //各種バリデーター
  );
  $pattern = '{'.implode('|', $useragents).'}i';
  return preg_match($pattern, $_SERVER['HTTP_USER_AGENT']);
}
endif;

//カスタムフィールドのショートコードをロケーションURIに置換
if ( !function_exists( 'replace_directory_uri' ) ):
function replace_directory_uri($code){
  $code = str_replace('[template_directory_uri]', get_template_directory_uri(), $code);
  $code = str_replace('[stylesheet_directory_uri]', get_stylesheet_directory_uri(), $code);
  $code = str_replace('<?php echo template_directory_uri(); ?>', get_template_directory_uri(), $code);
  $code = str_replace('<?php echo get_stylesheet_directory_uri(); ?>', get_stylesheet_directory_uri(), $code);
  return $code;
}
endif;

if ( !function_exists( 'is_field_checkbox_value_default' ) ):
function is_field_checkbox_value_default($value){
  if ($value === '' || $value === null) {
    return true;
  } else {
    return false;
  }
}
endif;

//URL最後が/でない場合スラッシュをつける
if ( !function_exists( 'add_delimiter_to_url_if_last_nothing' ) ):
function add_delimiter_to_url_if_last_nothing($url){
  if (!preg_match('{^.+/$}', $url)) {
    $url = $url.'/';
  }
  return $url;
}
endif;

//マルチページの2ページ目以降か
if ( !function_exists( 'is_multi_paged' ) ):
function is_multi_paged(){
  global $page;
  //2ページ目以降の場合
  return $page != 1;
}
endif;

//投稿日の取得
if ( !function_exists( 'get_the_postdate_time' ) ):
function get_the_postdate_time(){
  return the_time(get_site_date_format());
}
endif;

//更新日の取得（更新日がないときは投稿日）
if ( !function_exists( 'get_the_update_time' ) ):
function get_the_update_time(){
  $update_time = get_update_time(get_site_date_format());
  if ($update_time) {
    return $update_time;
  } else {
    return get_the_postdate_time();
  }

}
endif;

//指定されたURLはWordpressホームURLかどうか
if ( !function_exists( 'is_home_url' ) ):
function is_home_url($url){
  //_v(home_url());
  return $url == home_url() || $url == home_url('/');
}
endif;

//POSTリクエストか
if ( !function_exists( 'is_server_request_post' ) ):
function is_server_request_post(){
  $res = false;
  if (isset($_SERVER['REQUEST_METHOD'])) {
    $res = $_SERVER['REQUEST_METHOD'] == 'POST';
  }
  return $res;
}
endif;

//GETリクエストか
if ( !function_exists( 'is_server_request_get' ) ):
function is_server_request_get(){
  $res = false;
  if (isset($_SERVER['REQUEST_METHOD'])) {
    $res = $_SERVER['REQUEST_METHOD'] == 'GET';
  }
  return $res;
}
endif;

//リクエストURIがダウンロードボタンか
if ( !function_exists( 'is_server_request_uri_backup_download_php' ) ):
function is_server_request_uri_backup_download_php(){
  $res = false;
  if (isset($_SERVER['REQUEST_URI'])) {
    $res = $_SERVER['REQUEST_URI'] == '/wp-content/themes/cocoon-master/lib/page-backup/backup-download.php';
  }
  return $res;
}
endif;

//現在のページでサイドバーが表示されるか
if ( !function_exists( 'is_the_page_sidebar_visible' ) ):
function is_the_page_sidebar_visible(){
  //サイドバー表示設定
  $sidebar_visible = true;
  //var_dump(get_sidebar_display_type());
  switch (get_sidebar_display_type()) {
    case 'no_display_all':
      $sidebar_visible = false;
      break;
    case 'no_display_front_page':
      if (is_front_page() && !is_home()) {
        $sidebar_visible = false;
      }
      break;
    case 'no_display_index_pages':
      if (!is_singular()) {
        $sidebar_visible = false;
      }
      break;
    case 'no_display_pages':
      if (is_page()) {
        $sidebar_visible = false;
      }
      break;
    case 'no_display_singles':
      if (is_single()) {
        $sidebar_visible = false;
      }
      break;
    default:

      break;
  }

  //投稿管理画面で「1カラム」が選択されている場合
  if (is_singular() && is_singular_page_type_column1()) {
    $sidebar_visible = false;
  }

  //投稿管理画面で「本文のみ」が選択されている場合
  if (is_singular() && is_singular_page_type_content_only()) {
    $sidebar_visible = false;
  }

  //投稿管理画面で「狭い」が選択されている場合
  if (is_singular() && is_singular_page_type_narrow()) {
    $sidebar_visible = false;
  }

  //投稿管理画面で「広い」が選択されている場合
  if (is_singular() && is_singular_page_type_wide()) {
    $sidebar_visible = false;
  }

  //サイドバーにウィジェットが入っていない場合
  if (!is_active_sidebar( 'sidebar' )) {
    $sidebar_visible = false;
  }
  return $sidebar_visible;
}
endif;

//インデックスページでサイドバーが表示されるか
if ( !function_exists( 'is_index_page_sidebar_visible' ) ):
function is_index_page_sidebar_visible(){
  $sidebar_visible = true;

  switch (get_sidebar_display_type()) {
    case 'no_display_all':
      $sidebar_visible = false;
      break;
    case 'no_display_index_pages':
      if (!is_singular()) {
        $sidebar_visible = false;
      }
      break;
  }

  //サイドバーにウィジェットが入っていない場合
  if (!is_active_sidebar( 'sidebar' )) {
    $sidebar_visible = false;
  }
  return $sidebar_visible;
}
endif;

//フロントトップページかどうか
if ( !function_exists( 'is_front_top_page' ) ):
function is_front_top_page(){
  return is_front_page() && !is_paged();
}
endif;

//アクセス解析を行うか
if ( !function_exists( 'is_analytics' ) ):
function is_analytics(){
  return !is_user_administrator() || is_analytics_admin_include();
}
endif;

// htmlspecialcharsを簡略化して関数化する
if ( !function_exists( 'h' ) ):
function h($s) {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
endif;

//IDに配列が適切か
if ( !function_exists( 'is_ids_exist' ) ):
function is_ids_exist($array){
  return !empty($array) && isset($array[0]) && is_numeric($array[0]);
}
endif;

//ファビコンを取得
if ( !function_exists( 'get_site_favicon_url' ) ):
function get_site_favicon_url(){
  $icon = get_site_icon_url();
  if (!$icon) {
    $icon = get_template_directory_uri().'/images/site-icon32x32.png';
  }
  return $icon;
}
endif;

//ブログカード用のURLデコード
if ( !function_exists( 'ampersand_urldecode' ) ):
function ampersand_urldecode($url){
  //$url = urldecode($url);//urlエンコードされている場合に元に戻す（?が&amp;になっている時など）
  $url = str_replace('&amp;', '&', $url);
  $url = str_replace('#038;', '&', $url);
  $url = str_replace('&&', '&', $url);
  return $url;
}
endif;

//URLパラメーターを取得
if ( !function_exists( 'get_url_params' ) ):
function get_url_params($url){
  $url = ampersand_urldecode($url);
  $parse_url = parse_url($url);//URLを配列に分解
  if (isset($parse_url['query'])) {
    $query = $parse_url['query'];//URLの中からクエリ部分を取り出す
    parse_str($query, $arr);//クエリを配列にする
    return $arr;
  }
}
endif;

//パブリシャーロゴのサイズ修正
if ( !function_exists( 'calc_publisher_image_sizes' ) ):
function calc_publisher_image_sizes($width, $height){
  //ロゴの幅が大きすぎる場合は仕様の範囲内（600px）にする
  if ($width > 600) {
    $height = round($height * (600/$width));
    $width = 600;
  }
  //ロゴの高さが大きすぎる場合は仕様の範囲内（60px）にする
  if ($height > 60) {
    $width = round($width * (60/$height));
    $height = 60;
  }
  $sizes = array();
  $sizes['width'] = $width;
  $sizes['height'] = $height;
  return $sizes;
}
endif;

//ショートコードの値をサニタイズする
if ( !function_exists( 'sanitize_shortcode_value' ) ):
function sanitize_shortcode_value($value){
  $value = strip_tags($value);
  $value = esc_html(trim($value));
  $value = str_replace('"', '', $value);
  $value = str_replace("'", '', $value);
  $value = str_replace('[', '', $value);
  $value = str_replace(']', '', $value);
  return $value;
}
endif;

//改行を削除
if ( !function_exists( 'remove_crlf' ) ):
function remove_crlf($content){
  $content = str_replace(array("\r", "\n"), '', $content);
  return $content;
}
endif;

//wpautopがつけた段落を削除
if ( !function_exists( 'wpunautop' ) ):
function wpunautop($content){
  $content = str_replace(array('<p>', '</p>', '<br />'), '', $content);
  return $content;
}
endif;

//人間感覚的な時間取得
if ( !function_exists( 'get_human_time_diff_advance' ) ):
function get_human_time_diff_advance( $from, $to = '' ) {
  if ( empty($to) )
      $to = time();
  $diff = (int) abs($to - $from);
  // 条件: 3600秒 = 1時間以下なら (元のまま)
  if ($diff <= 3600) {
      $mins = floor($diff / 60);
      if ($mins <= 1) {
          $mins = 1;
      }
      $since = sprintf(_n('%s min', '%s mins', $mins), $mins);
  }
  // 条件: 86400秒 = 24時間以下かつ、3600秒 = 1時間以上なら (元のまま)
  else if (($diff <= 86400) && ($diff > 3600)) {
      $hours = floor($diff / 3600);
      if ($hours <= 1) {
          $hours = 1;
      }
      $since = sprintf(_n('%s hour', '%s hours', $hours), $hours);
  }
  // 条件: 604800秒 = 7日以下かつ、86400秒 = 24時間以上なら (条件追加)
  elseif (($diff <= 604800) && ($diff > 86400)) {
      $days = floor($diff / 86400);
      if ($days <= 1) {
          $days = 1;
      }
      $since = sprintf(_n('%s day', '%s days', $days), $days);
  }
  // 条件: 2678400秒 = 31日以下かつ、2678400秒 = 7日以上なら (条件追加)
  elseif (($diff <= 2678400) && ($diff > 604800) ) {
      $weeks = floor($diff / 604800);
      if ($weeks <= 1) {
          $weeks = 1;
      }
      $since = sprintf(_n('%s週間', '%s週間', $weeks), $weeks);
  }
  // 条件: 31536000秒 = 365日以下かつ、2678400秒 = 31日以上なら (条件追加)
  elseif (($diff <= 31536000) && ($diff > 2678400) ) {
      $months = floor($diff / 2678400);
      if ($months <= 1) {
          $months = 1;
      }
      $since = sprintf(_n('%sヶ月', '%sヶ月', $months), $months);
  }
  // 条件: 31536000秒 = 365日以上なら (条件追加)
  elseif ($diff >= 31536000) {
    $years = floor($diff / 31536000);
    $months = floor(($diff % 31536000) / 2678400);
    if ($years <= 1) {
        $years = 1;
    }
    //3年以上経っている場合は年だけでOK
    if (($months == 0) || ($years >= 3)) {
      $since = sprintf(__('%s年', THEME_NAME), $years);
    } else {
      $since = sprintf(__('%s年%sヶ月', THEME_NAME), $years, $months);
    }
  }
  return $since;
}
endif;

//人間感覚の年の取得
if ( !function_exists( 'get_human_years_ago' ) ):
function get_human_years_ago( $from, $unit = '' ) {
  $to = time();
  $diff = (int) abs($to - $from);
  $years = floor($diff / 31536000);
  $since = sprintf('%s'.$unit, $years);
  return $since;
}
endif;

//文字列をBoolean型に変換
if ( !function_exists( 'str_to_bool' ) ):
function str_to_bool($string){
  if (!$string || $string === '0' || $string === 'false') {
    return false;
  } else {
    return true;
  }
}
endif;

//カスタム投稿タイプの配列取得
if ( !function_exists( 'get_custum_post_types' ) ):
function get_custum_post_types(){
  $args = array(
    'public' => true,
    '_builtin' => false
  );
  $post_types = get_post_types( $args );
  return $post_types;
}
endif;

//カスタムポストタイプをまとめて登録
if ( !function_exists( 'add_meta_box_custom_post_types' ) ):
function add_meta_box_custom_post_types($id, $title, $callback, $screen = null, $context = 'advanced', $priority = 'default', $callback_args = null){
  $post_types = get_custum_post_types();
  foreach ($post_types as $post_type) {
    //_v($post_type);
    add_meta_box($id, $title, $callback, $post_type, $context, $priority, $callback_args);
  }
}
endif;

//別スキームのURLの取得
if ( !function_exists( 'get_another_scheme_url' ) ):
function get_another_scheme_url($url){
  if (preg_match('{^https://}', $url)) {
    $another_scheme_url = preg_replace('{^https://}', 'http://', $url);
  } else {
    $another_scheme_url = preg_replace('{^http://}', 'https://', $url);
  }
  return $another_scheme_url;
}
endif;

//ブログカードの無効化を解除
if ( !function_exists( 'cancel_blog_card_deactivation' ) ):
function cancel_blog_card_deactivation($the_content, $is_p = true){
  $not_url_reg = '!(https?://[-_.!~*\'()a-zA-Z0-9;/?:\@&=+\$,%#]+)';
  if ($is_p) {
    $pattern = '{^<p>'.$not_url_reg.'</p>}im';
    $append = '<p>$1</p>';
  } else {
    $pattern = '{^'.$not_url_reg.'}im';
    $append = '$1';
  }
  $the_content = preg_replace($pattern, $append, $the_content);
  return $the_content;
}
endif;

//投稿・固定ページのSNSシェア画像の取得
if ( !function_exists( 'get_singular_sns_share_image_url' ) ):
function get_singular_sns_share_image_url(){
  //NO IMAGE画像で初期化
  $sns_image_url = get_no_image_url();
  //本文を取得
  global $post;
  $content = '';
  if ( isset( $post->post_content ) ){
    $content = $post->post_content;
  }
  //投稿にイメージがあるか調べるための正規表現
  $searchPattern = '/<img.*?src=(["\'])(.+?)\1.*?>/i';
  if ($singular_sns_image_url = get_singular_sns_image_url()) {
    $sns_image_url = $singular_sns_image_url;
  } else if (has_post_thumbnail()){//投稿にサムネイルがある場合の処理
    $image_id = get_post_thumbnail_id();
    $image = wp_get_attachment_image_src( $image_id, 'full');
    $sns_image_url = $image[0];
  } else if ( preg_match( $searchPattern, $content, $image ) && !is_archive()) {//投稿にサムネイルは無いが画像がある場合の処理
    $sns_image_url = $image[2];
  } else if ( $ogp_home_image_url = get_ogp_home_image_url() ){//ホームイメージが設定されている場合
    $sns_image_url = $ogp_home_image_url;
  }
  return $sns_image_url;
}
endif;

//wpForo URLが含まれている場合
if ( !function_exists( 'includes_wpforo_url' ) ):
function includes_wpforo_url($url){
  return is_wpforo_exist() && includes_string($url, WPF()->url);
}
endif;

//robots.txtページかどうか
if ( !function_exists( 'is_robots_txt_page' ) ):
function is_robots_txt_page(){
  return isset($_SERVER['REQUEST_URI']) && ($_SERVER['REQUEST_URI'] == '/robots.txt');
}
endif;

//access.phpページかどうか
if ( !function_exists( 'is_analytics_access_php_page' ) ):
function is_analytics_access_php_page(){
  return isset($_SERVER['REQUEST_URI']) && includes_string($_SERVER['REQUEST_URI'], '/lib/analytics/access.php?');
}
endif;

//ログインページかどうか
if ( !function_exists( 'is_login_page' ) ):
function is_login_page() {
  return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}
endif;

//ホームURLを除いた文字列
if ( !function_exists( 'get_remove_home_url' ) ):
function get_remove_home_url($url){
  return str_replace(home_url(), '', $url);
}
endif;

//WordPressで設定されているタイムゾーンを取得する
if ( !function_exists( 'get_wordpress_timezone' ) ):
function get_wordpress_timezone(){
  return get_option('timezone_string');

}
endif;

//WordPressで設定されているメールアドレス取得する
if ( !function_exists( 'get_wordpress_admin_email' ) ):
function get_wordpress_admin_email(){
  return get_option('admin_email');
}
endif;

//固定表示されている投稿IDを取得する
if ( !function_exists( 'get_sticky_post_ids' ) ):
function get_sticky_post_ids(){
  return get_option('sticky_posts');
}
endif;

//"を\"にエスケープする
if ( !function_exists( 'get_double_quotation_escape' ) ):
function get_double_quotation_escape($str){
  return str_replace('"', '\"', $str);
}
endif;

//.htaccessへの書き込み関数
if ( !function_exists( 'add_code_to_htaccess' ) ):
function add_code_to_htaccess($resoce_file, $begin, $end, $reg){

  //$resoce_file = 'browser-cache.conf';
  //設定ファイルが存在しない場合
  if (!file_exists($resoce_file)) {
    return ;
  }

  ob_start();
  require_once($resoce_file);
  $code = ob_get_clean();
  $new_code = $begin.PHP_EOL.
                trim($code).PHP_EOL.
              $end;

  //.htaccessファイルが存在する場合
  if (file_exists(get_abs_htaccess_file())) {
    //書き込む前にバックアップファイルを用意する
    $htaccess_backup_file = get_abs_htaccess_file().'.'.THEME_NAME;
    if (copy(get_abs_htaccess_file(), $htaccess_backup_file)) {
      if ($current_htaccess = @wp_filesystem_get_contents(get_abs_htaccess_file())) {

        $res = preg_match($reg, $current_htaccess, $m);

        //テーマファイル用のブラウザキャッシュコードが書き込まれている場合
        if ($res && isset($m[0])) {

        } else {//書き込まれていない場合
          //.htaccessにブラウザキャッシュの書き込みがなかった場合には単に追記する
          $last_htaccess = rtrim($current_htaccess).PHP_EOL.
                                $new_code;
          //ブラウザキャッシュを.htaccessファイルに書き込む
          wp_filesystem_put_contents(
            get_abs_htaccess_file(),
            $last_htaccess,
            0644
          );
        }
      }//wp_filesystem_get_contents
    }//copy
  } else {//.htaccessが存在しない場合
    //.htaccessファイルがない場合は、新しく生成したブラウザキャッシュが最終.htaccess書き込みファイルになる
    $last_htaccess = $new_code;
    //ブラウザキャッシュを.htaccessファイルに書き込む
    wp_filesystem_put_contents(
      get_abs_htaccess_file(),
      $last_htaccess,
      0644
    );
  }//file_exists(get_abs_htaccess_file())
}
endif;

//.htaccessのコード削除関数
if ( !function_exists( 'remove_code_from_htacccess' ) ):
function remove_code_from_htacccess($reg){
  //.htaccessファイルが存在しているとき
  if (file_exists(get_abs_htaccess_file())) {
    if ($current_htaccess = @wp_filesystem_get_contents(get_abs_htaccess_file())) {
      $res = preg_match($reg, $current_htaccess, $m);
      //書き込まれたブラウザキャッシュが見つかった場合
      if ($res && $m[0]) {
        //正規表現にマッチした.htaccessに書き込まれている現在のブラウザキャッシュを取得
        $current_code = $m[0];
        //正規表現で見つかったブラウザキャッシュコードを正規表現で削除
        $last_htaccess = str_replace($current_code, '', $current_htaccess);
        //_v($last_htaccess);
        //ブラウザキャッシュを削除したコードを.htaccessファイルに書き込む
        wp_filesystem_put_contents(
          get_abs_htaccess_file(),
          $last_htaccess,
          0644
        );
      }//$res && $[0]
    }
  }//file_exists(get_abs_htaccess_file())
}
endif;

//テキスト内にtarget="_blank"が含まれるか
if ( !function_exists( 'includes_target_blalk' ) ):
function includes_target_blalk($target){
  return includes_string($target, ' target="_blank"');
}
endif;

//XMLのエスケープ
if ( !function_exists( 'xmlspecialchars' ) ):
function xmlspecialchars($string){
  return preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string);
}
endif;

//カンマテキストを配列にする
if ( !function_exists( 'comma_text_to_array' ) ):
function comma_text_to_array($comma_text){
  $comma_text = str_replace(' ', '', $comma_text);
  if (empty($comma_text)) {
    $array = array();
  } else {
    $array = explode(',', $comma_text);
  }
  return $array;
}
endif;

//エディターカラーパレット用のキーカラー
if ( !function_exists( 'get_editor_key_color' ) ):
function get_editor_key_color(){
  $site_key_color = get_site_key_color();
  if (!empty($site_key_color)) {
    return $site_key_color;
  } else {
    return DEFAULT_EDITOR_KEY_COLOR;
  }
}
endif;

//httpコンテンツの取得
if ( !function_exists( 'get_http_content' ) ):
function get_http_content($url){
  try {
    $ch = curl_init();
    curl_setopt_array($ch, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT => 1.5,
    ));
    $body = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if (CURLE_OK !== $errno) {
      throw new RuntimeException($error, $errno);
    }
    return $body;
  } catch (Exception $e) {
    return false;
    //echo $e->getMessage();
  }
}
endif;

//本文を読むのにかかる時間
if ( !function_exists( 'get_time_to_content_read' ) ):
function get_time_to_content_read($content){
  $count = mb_strlen(strip_tags($content));
  if ($count == 0) {
    return 0;
  }
  $minutes = floor($count / 600) + 1;
  return $minutes;
}
endif;

//内部URLからタグオブジェクトを取得する
if ( !function_exists( 'url_to_tag_object' ) ):
function url_to_tag_object($url){
  $tag_slug = str_replace(TAG_BASE_URL, '', get_query_removed_url($url));
  $tag_slug = preg_replace('{/.+}', '', $tag_slug);
  $tags = get_tags(array('slug' => $tag_slug));
  if (isset($tags[0])) {
    $tag = $tags[0];
    return $tag;
  }
}
endif;

//有効なショートコードアイテムを保持しているか
if ( !function_exists( 'has_valid_shortcode_item' ) ):
function has_valid_shortcode_item($shortcodes){
  $items = array_filter($shortcodes, function($v, $k) { return $v->visible === "1"; }, ARRAY_FILTER_USE_BOTH);
  return !empty($items);
}
endif;

//不要なショートコードを除外した文字列を返す
if ( !function_exists( 'get_shortcode_removed_content' ) ):
function get_shortcode_removed_content($content){
  $removed_content = $content;
  //$removed_content = str_replace('[ad]', '', $removed_content);
  $removed_content = preg_replace('/\[toc.*\]/', '', $removed_content);
  $removed_content = preg_replace('/\[amazon.*\]/', '', $removed_content);
  $removed_content = preg_replace('/\[rakuten.*\]/', '', $removed_content);
  $removed_content = preg_replace('/\[navi.*\]/', '', $removed_content);
  $removed_content = preg_replace('/\[new_list.*\]/', '', $removed_content);
  $removed_content = preg_replace('/\[popular_list.*\]/', '', $removed_content);
  $removed_content = preg_replace('/\[sitemap.*\]/', '', $removed_content);
  $removed_content = preg_replace('/\[author_box.*\]/', '', $removed_content);
  $removed_content = preg_replace('/\[rank.*\]/', '', $removed_content);
  $removed_content = preg_replace('/\[star.*\]/', '', $removed_content);
  $removed_content = preg_replace('/\[\[.*\]\]/', '', $removed_content);
  return $removed_content;
}
endif;

//テンプレートのタグ取得
if ( !function_exists( 'get_template_part_tag( $slug )' ) ):
function get_template_part_tag($slug){
  ob_start();
  get_template_part($slug);
  return ob_get_clean();
}
endif;

//モバイルメニューボタンタグの取得
if ( !function_exists( 'get_mobile_navi_button_tag' ) ):
function get_mobile_navi_button_tag(){
  return get_template_part_tag('tmp/mobile-navi-button');
}
endif;

//モバイルホームボタンタグの取得
if ( !function_exists( 'get_mobile_home_button_tag' ) ):
function get_mobile_home_button_tag(){
  return get_template_part_tag('tmp/mobile-home-button');
}
endif;

//モバイル検索ボタンタグの取得
if ( !function_exists( 'get_mobile_search_button_tag' ) ):
function get_mobile_search_button_tag(){
  return get_template_part_tag('tmp/mobile-search-button');
}
endif;

//モバイルトップボタンタグの取得
if ( !function_exists( 'get_mobile_top_button_tag' ) ):
function get_mobile_top_button_tag(){
  return get_template_part_tag('tmp/mobile-top-button');
}
endif;

//モバイルサイドバーボタンタグの取得
if ( !function_exists( 'get_mobile_sidebar_button_tag' ) ):
function get_mobile_sidebar_button_tag(){
  return get_template_part_tag('tmp/mobile-sidebar-button');
}
endif;

//モバイル目次ボタンタグの取得
if ( !function_exists( 'get_mobile_toc_button_tag' ) ):
function get_mobile_toc_button_tag(){
  return get_template_part_tag('tmp/mobile-toc-button');
}
endif;

//モバイルシェアボタンタグの取得
if ( !function_exists( 'get_mobile_share_button_tag' ) ):
function get_mobile_share_button_tag(){
  return get_template_part_tag('tmp/mobile-share-button');
}
endif;

//モバイルフォローボタンタグの取得
if ( !function_exists( 'get_mobile_follow_button_tag' ) ):
function get_mobile_follow_button_tag(){
  return get_template_part_tag('tmp/mobile-follow-button');
}
endif;

//モバイル前へボタンタグの取得
if ( !function_exists( 'get_mobile_prev_button_tag' ) ):
function get_mobile_prev_button_tag(){
  return get_template_part_tag('tmp/mobile-prev-button');
}
endif;

//モバイル次へボタンタグの取得
if ( !function_exists( 'get_mobile_next_button_tag' ) ):
function get_mobile_next_button_tag(){
  return get_template_part_tag('tmp/mobile-next-button');
}
endif;

//モバイルロゴボタンタグの取得
if ( !function_exists( 'get_mobile_logo_button_tag' ) ):
function get_mobile_logo_button_tag(){
  return get_template_part_tag('tmp/mobile-logo-button');
}
endif;

//確実にホームパスを取得するget_home_path関数
if ( !function_exists( 'get_abs_home_path' ) ):
function get_abs_home_path(){
  $site_url = get_site_url(null, '/');
  $home_url = get_home_url(null, '/');
  // _v($site_url);
  // _v($home_url);
  if ($site_url == $home_url) {
    return ABSPATH;
  } else {
    if (includes_string($site_url, $home_url)) {
      $dir = str_replace($home_url, '', $site_url);
      // _v($dir);
      // _v(preg_quote($dir, '/'));
      $home_path = preg_replace('/'.preg_quote($dir, '/').'$/', '', ABSPATH);
      return $home_path;
    } else {
      return ABSPATH;
    }
  }
}
endif;

//.htaccessファイルの取得
if ( !function_exists( 'get_abs_htaccess_file' ) ):
function get_abs_htaccess_file(){
  return get_abs_home_path().'.htaccess';
}
endif;
