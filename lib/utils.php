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
    $id = $category->term_id;
    $classes = array(
      'entry-category',
      'cat-label-'.$id,
    );
    $classes = apply_filters( 'nolink_category_label_classes', $classes, $category );
    $implode_class = implode(' ', $classes);
    $categories .= '<span class="'.$implode_class.'">'.$category->cat_name.'</span>';
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
    $style = null;
    $categories .= '<a class="cat-link cat-link-'.$category->cat_ID.'" href="'.get_category_link( $category->cat_ID ).'"'.$style.'><span class="fa fa-folder cat-icon tax-icon" aria-hidden="true"></span>'.$category->cat_name.'</a>';
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
  }

  //メインカテゴリーが指定してある場合は該当カテゴリーを適用
  $category = isset($categories[0]) ? $categories[0] : null;
  $main_cat_id = get_the_page_main_category($id);
  if ($main_cat_id && in_category($main_cat_id, $id)) {
    $category = get_category($main_cat_id);
  }

  //カテゴリーラベル制御用のフック
  $category = apply_filters('get_the_nolink_category', $category, $categories);
  if (isset($category->cat_ID) && isset($category->cat_name)) {
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


//PR表記小の出力
if ( !function_exists( 'generate_small_pr_label_tag' ) ):
function generate_small_pr_label_tag(){
  if (apply_filters( 'is_small_pr_label_visible', true )) {
    $small_caption = get_pr_label_small_caption();
    if (empty($small_caption)) {
    $small_caption= PR_LABEL_SMALL_CAPTION;
    }
    echo '<span class="pr-label pr-label-s">'.$small_caption.'</span>'; //PR表記出力
  }
}
endif;


//PR表記（大）の出力
if ( !function_exists( 'generate_large_pr_label_tag' ) ):
function generate_large_pr_label_tag(){
  if (apply_filters( 'is_large_pr_label_visible', true )) {
    $large_caption = get_pr_label_large_caption();
    if (empty($large_caption)) {
      $large_caption = PR_LABEL_LARGE_CAPTION;
    }
    echo '<div class="pr-label pr-label-l">'.$large_caption.'</div>'; //PR表記出力
  }
}
endif;


//タグリンクの取得
if ( !function_exists( 'get_the_tag_links' ) ):
function get_the_tag_links(){
  $tags = null;
  $posttags = get_the_tags();
  if ( $posttags ) {
    foreach(get_the_tags() as $tag){
      $tags .= '<a class="tag-link tag-link-'.$tag->term_id.' border-element" href="'.get_tag_link( $tag->term_id ).'"><span class="fa fa-tag tag-icon tax-icon" aria-hidden="true"></span>'.$tag->name.'</a>';
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
if ( !function_exists( 'is_comment_open' ) ):
function is_comment_open(){
  global $post;
  if ( isset($post->comment_status) ) {
    return $post->comment_status == 'open';
  }
  return false;
}
endif;

//コメントが許可されているか（is_comment_openのエイリアス）
if ( !function_exists( 'is_comment_allow' ) ):
function is_comment_allow(){
  return is_comment_open();
}
endif;


//現在のカテゴリーをカンマ区切りテキストで取得する
if ( !function_exists( 'get_category_ids' ) ):
function get_category_ids(){
  if ( is_single() ) {//投稿ページでは全カテゴリー取得
    $categories = get_the_category();
    $category_IDs = array();
    foreach($categories as $category):
      array_push( $category_IDs, $category -> cat_ID);
    endforeach ;
    return $category_IDs;
  } elseif ( is_category() ) {//カテゴリーページではトップカテゴリーのみ取得
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
  if (isset($wrap_class)) {
    $wrap_class = ' '.trim($wrap_class).' ad-'.$format;
  }
  if ($label_visible) {
    $wrap_class .= ' ad-label-visible';
  } else {
    $wrap_class .= ' ad-label-invisible';
  }
  //$format変数をテンプレートファイルに渡す
  set_query_var('format', $format);
  //$wrap_class変数をテンプレートファイルに渡す
  set_query_var('wrap_class', $wrap_class);
  //$ad_code変数をテンプレートファイルに渡す
  set_query_var('ad_code', $ad_code);
  //広告テンプレートの呼び出し
  cocoon_template_part('tmp/ad');
}
endif;

//オプション付きのテンプレート呼び出し
if ( !function_exists( 'get_template_part_with_option' ) ):
function get_template_part_with_option($slug, $option = null){
  //$option変数をテンプレートファイルに渡す
  set_query_var('option', $option);
  //広告テンプレートの呼び出し
  cocoon_template_part($slug);
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

//スキン制御があるか確認する
if ( !function_exists( 'is_form_skin_option' ) ):
function is_form_skin_option($name, $value = 1){
  global $_FORM_SKIN_OPTIONS;
  $name = str_replace('[]', '', $name);
  if (isset($_FORM_SKIN_OPTIONS[$name]) && is_array($_FORM_SKIN_OPTIONS[$name])) {
    return true;
  }
  //スキンにより固定値がある場合は採用する
  if (isset($_FORM_SKIN_OPTIONS[$name])) {
    return true;
  }
}
endif;

//get_form_skin_option→is_form_skin_optionに関数変更しても互換関係を保つためのエイリアス
if ( !function_exists( 'get_form_skin_option' ) ):
function get_form_skin_option($name, $value = 1){
  return is_form_skin_option($name, $value);
}
endif;

//オプションの値をデータベースに保存する
if ( !function_exists( 'update_theme_option' ) ):
function update_theme_option($option_name){
  $opt_val = isset($_POST[$option_name]) ? $_POST[$option_name] : '';
  set_theme_mod($option_name, $opt_val);
}
endif;

//オプションの値をデータベースから取得する
if ( !function_exists( 'get_theme_option' ) ):
function get_theme_option($option_name, $default = null){
  //スキンにより固定値がある場合は採用する
  $skin_option = get_skin_option($option_name);
  if (!is_null($skin_option)) {
    if ($skin_option == '0') {
      $skin_option = 0;
    }
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
  if (is_child_theme() && file_exists(CHILD_THEME_KEYFRAMES_CSS_FILE)) {
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
  wp_enqueue_style( 'font-awesome-style', get_site_icon_font_url() );
  if (is_site_icon_font_font_awesome_5()) {
    wp_enqueue_style( 'font-awesome5-update-style', FONT_AWESOME_5_UPDATE_URL );
  }
}
endif;

//IcoMoonの読み込み
if ( !function_exists( 'wp_enqueue_style_icomoon' ) ):
function wp_enqueue_style_icomoon(){
  wp_enqueue_style( 'icomoon-style', FONT_ICOMOON_URL );
}
endif;

//jQueryコアURLの取得
if ( !function_exists( 'get_jquery_core_url' ) ):
function get_jquery_core_url($ver){
  $url = null;
  switch ($ver) {
    case '3':
      $url = 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js';
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
      $full_ver = '3.6.1';
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
      $url = 'https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/3.3.2/jquery-migrate.min.js';
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
    'is_google_font_lazy_load_enable' => is_google_font_lazy_load_enable(),
  ) );
  wp_localize_script( THEME_JS, $name, $value );

  // // TODO: ファイル読みこみ位置 もしくは HTML側に直接出力など よい方法を考慮
  // wp_enqueue_script( 'set-event-passive', SET_EVENT_PASSIVE_JS_URL, array( ), false, true );
}
endif;

//Lazy Loadの読み込み
//https://apoorv.pro/lozad.js/
//https://qiita.com/yukagil/items/369a9972fd45223677fd
//https://qiita.com/ryo_hisano/items/42f5980720bc832e6e09
if ( !function_exists( 'wp_enqueue_lazy_load' ) ):
function wp_enqueue_lazy_load(){
  if (is_lazy_load_enable() && !is_admin() && !is_login_page()) {
    wp_enqueue_script( 'lazy-load-js', get_template_directory_uri() . '/plugins/lozad.js-master/dist/lozad.min.js', array(), false, true );
    $data = 'const observer = lozad(".lozad", {rootMargin: "0px 500px 500px"});observer.observe();';
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
    $data = "
    (function($){
      var clipboard = new Clipboard('.copy-button');//clipboardで使う要素を指定
      clipboard.on('success', function(e) {
        $('.copy-info').fadeIn(500).delay(1000).fadeOut(500);

        e.clearSelection();
      });
    })(jQuery);
    ";
    wp_add_inline_script( 'clipboard-js', $data, 'after' ) ;
  }
}
endif;

//ソースコードのハイライト表示に必要なリソースの読み込み
if ( !function_exists( 'wp_enqueue_highlight_js' ) ):
function wp_enqueue_highlight_js(){
  //global $pagenow;
  if ( (is_code_highlight_enable() && is_singular()) || is_admin_php_page() ) {
    if (is_code_highlight_package_light()) {
      $file_name = 'highlight.min.js';
    } else {
      $file_name = 'highlight.all.min.js';
    }
    //ソースコードハイライト表示用のスタイル
    wp_enqueue_style( 'code-highlight-style',  get_highlight_js_css_url() );
    //ソースコードハイライト表示用のライブラリ
    $url = get_template_directory_uri() . '/plugins/highlight-js/'.$file_name;
    $url = apply_filters( 'code_highlight_js_url', $url );
    wp_enqueue_script( 'code-highlight-js', $url, array( 'jquery' ), false, true );
    if (is_admin_php_page()) {
      $selector = CODE_HIGHLIGHT_CSS_SELECTOR;
    } else {
      $selector = get_code_highlight_css_selector();
      if ($selector === '') {
        $selector = CODE_HIGHLIGHT_CSS_SELECTOR;
      }
    }

    $data = '
          (function($){
           $("'.$selector.'").each(function(i, block) {
            hljs.highlightBlock(block);
           });
          })(jQuery);
        ';
    wp_add_inline_script( 'code-highlight-js', $data, 'after' ) ;
  }
}
endif;


//Lightboxの読み込み
if ( !function_exists( 'wp_enqueue_lightbox' ) ):
function wp_enqueue_lightbox(){
  //_v(get_image_zoom_effect());
 if ( ((is_lightbox_effect_enable() && is_lightboxable_page()) || is_admin_php_page()) ) {
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
 if ( ((is_lity_effect_enable() && is_lightboxable_page()) || is_admin_php_page()) ) {
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
 if ( ((is_baguettebox_effect_enable() && is_lightboxable_page()) || is_admin_php_page()) ) {
    //baguetteboxスタイルの呼び出し
    wp_enqueue_style( 'baguettebox-style', get_template_directory_uri() . '/plugins/baguettebox/dist/baguetteBox.min.css' );
    //baguetteboxスクリプトの呼び出し
    wp_enqueue_script( 'baguettebox-js', get_template_directory_uri() . '/plugins/baguettebox/dist/baguetteBox.min.js', array( 'jquery' ), false, true  );
    $selector = '.entry-content';
    $data = '
          (function($){
           baguetteBox.run("'.$selector.'");
          })(jQuery);
        ';
    wp_add_inline_script( 'baguettebox-js', $data, 'after' ) ;
  }
}
endif;


//Spotlightの読み込み
if ( !function_exists( 'wp_enqueue_spotlight' ) ):
function wp_enqueue_spotlight(){
 if ( ((is_spotlight_effect_enable() && is_lightboxable_page()) || is_admin_php_page()) ) {
    //spotlightスクリプトの呼び出し
    wp_enqueue_script( 'spotlight-js', get_template_directory_uri() . '/plugins/spotlight-master/dist/spotlight.bundle.js', array(), false, true  );
  }
}
endif;


//clingifyの読み込み
if ( !function_exists( 'wp_enqueue_clingify' ) ):
function wp_enqueue_clingify(){
  //グローバルナビ追従が有効な時
  if ( is_header_fixed() ) {
    //clingifyスタイルの呼び出し
    wp_enqueue_style( 'clingify-style', get_template_directory_uri() . '/plugins/clingify/clingify.css' );
    //clingifyスクリプトの呼び出し
    wp_enqueue_script( 'clingify-js', get_template_directory_uri() . '/plugins/clingify/jquery.clingify.min.js', array( 'jquery' ), false, true  );
    if (is_header_fixed()) {
      $selector = '.header-container';
      $detached_classes = get_additional_header_container_classes();
      $options = null;
      if (is_header_layout_type_center_logo()) {
        $options = '
        detached : function() {
          $(".header-container-in").removeClass().addClass("header-container-in'.$detached_classes.'");
          adjustHeader();
        },
        locked : function() {
          $(".header-container-in").removeClass().addClass("header-container-in hlt-top-menu wrap");
          adjustHeader();
        },
        ';
      }
      $data = '
              (function($){
                function adjustHeader(){
                  $(".js-clingify-placeholder").height($("#header-container").height());
                }

                $("'.$selector.'").clingify({
                  extraClass: "fixed-header-wrapper",
                  breakpointWidth: 834,
                  throttle: 500,
                  '.$options.'
                });
              })(jQuery);
            ';
      wp_add_inline_script( 'clingify-js', $data, 'after' );
    }
  }
}
endif;


//Slickの読み込み
if ( !function_exists( 'wp_enqueue_slick' ) ):
function wp_enqueue_slick(){
  if (is_carousel_visible()) {
    wp_enqueue_style( 'slick-theme-style', get_template_directory_uri() . '/plugins/slick/slick-theme.css' );
    //Slickスクリプトの呼び出し
    wp_enqueue_script( 'slick-js', get_template_directory_uri() . '/plugins/slick/slick.min.js', array( 'jquery' ), false, true  );
    $autoplay = null;
    if (is_carousel_autoplay_enable()) {
      $autoplay = 'autoplay: true,';
    }
    $data = '
              (function($){
                $(".carousel-content").slick({
                  dots: true,'.
                  $autoplay.
                  'autoplaySpeed: '.strval(intval(get_carousel_autoplay_interval())*1000).',
                  infinite: true,
                  slidesToShow: 6,
                  slidesToScroll: 6,
                  respondTo: "slider",
                  responsive: [
                      {
                        breakpoint: 1241,
                        settings: {
                          slidesToShow: 5,
                          slidesToScroll: 5
                        }
                      },
                      {
                        breakpoint: 1024,
                        settings: {
                          slidesToShow: 4,
                          slidesToScroll: 4
                        }
                      },
                      {
                        breakpoint: 835,
                        settings: {
                          slidesToShow: 3,
                          slidesToScroll: 3
                        }
                      },
                      {
                        breakpoint: 481,
                        settings: {
                          slidesToShow: 2,
                          slidesToScroll: 2
                        }
                      }
                    ]
                });

              })(jQuery);
            ';
    wp_add_inline_script( 'slick-js', $data, 'after' ) ;
  }

}
endif;

//Swiper
if ( !function_exists( 'wp_enqueue_swiper' ) ):
function wp_enqueue_swiper(){
  wp_enqueue_style( 'swiper-style', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css');
}
endif;

//SlickNav
if ( !function_exists( 'wp_enqueue_slicknav' ) ):
function wp_enqueue_slicknav(){
  if (is_slicknav_visible() || is_admin_php_page()) {
    //wp_enqueue_style( 'slicknav-style', get_template_directory_uri() . '/plugins/slicknav/slicknav.css' );
    //SlickNavスクリプトの呼び出し
    wp_enqueue_script( 'slicknav-js', get_template_directory_uri() . '/plugins/slicknav/jquery.slicknav.min.js', array( 'jquery' ), false, true  );
    $data = '
              (function($){
                $(".menu-header").slicknav({
                  label: "'.apply_filters('wp_enqueue_slicknav_label', 'MENU').'",
                  parentTag: "div",
                  allowParentLinks: true,
                });
              })(jQuery);
            ';
    wp_add_inline_script( 'slicknav-js', $data, 'after' ) ;

  }
}
endif;


//ScrollHint
if ( !function_exists( 'wp_enqueue_scrollhint' ) ):
function wp_enqueue_scrollhint(){
  if (is_responsive_table_enable() && (is_singular() || (is_category() && !is_paged())|| (is_tag() && !is_paged()))) {
    //ScrollHintスタイルの呼び出し
    wp_enqueue_style( 'scrollhint-style', get_template_directory_uri() . '/plugins/scroll-hint-master/css/scroll-hint.css' );
    //ScrollHintスクリプトの呼び出し
    wp_enqueue_script( 'scrollhint-js', get_template_directory_uri() . '/plugins/scroll-hint-master/js/scroll-hint.min.js', array( 'jquery' ), false, true  );
    $data = '
          (function($){
            new ScrollHint(".scrollable-table", {
              suggestiveShadow: true,
              i18n: {
                scrollable: "'.__( 'スクロールできます', THEME_NAME ).'"
              }
            });
          })(jQuery);
        ';
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
      $data = '
              (function($){
                var elements = $(".sidebar-scroll");
                Stickyfill.add(elements);
              })(jQuery);
            ';
      wp_add_inline_script( 'stickyfill-js', $data, 'after' );
    }

    if (is_scrollable_main_enable() && ($is_ie || $is_edge_version_under_16)) {
      $data = '
              (function($){
                var elements = $(".main-scroll");
                Stickyfill.add(elements);
              })(jQuery);
            ';
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
        $(".ect-tile-card").masonry({
          itemSelector: ".entry-card-wrap",
          isAnimated: false
        });
      ';
      $admin_code = null;
      if (1 || is_admin()) {
        $admin_code = '
          $(function(){
            setInterval(function(){'.
              $common_code
            .'},100);
          });
        ';
      }
      //実行コードの記入
      $code = '
        (function($){'.
          $common_code.
          $admin_code
        .'})(jQuery);
            ';
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


//Google Fontsの読み込み（Googleフォント以外のサイトフォント含む）
if ( !function_exists( 'wp_enqueue_google_fonts' ) ):
function wp_enqueue_google_fonts(){
  if (!is_site_font_family_local() && !is_google_font_lazy_load_enable()) {
    wp_enqueue_style( 'site-font-'.get_site_font_source(), get_site_font_source_url() );
  }
  if (!is_site_font_family_local() && is_google_font_lazy_load_enable() && !get_site_font_family_pretendard()) {
    $code = "window.WebFontConfig = {
      google: { families: ['".get_site_font_source_family().get_site_font_source_weight()."'] },
      active: function() {
        sessionStorage.fonts = true;
      }
    };

    (function() {
      var wf = document.createElement('script');
      wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js';
      wf.type = 'text/javascript';
      wf.async = 'true';
      var s = document.getElementsByTagName('script')[0];
      s.parentNode.insertBefore(wf, s);
    })();";
    wp_add_inline_script( THEME_JS, $code, 'after' );
  }
}
endif;


//設定変更CSSを読み込む
if ( !function_exists( 'wp_add_css_custome_to_inline_style' ) ):
function wp_add_css_custome_to_inline_style(){
  ob_start();//バッファリング
  cocoon_template_part('tmp/css-custom');
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
  if ($count == 1) {
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
  $mtime = get_the_modified_time('Ymd', $post_id);
  $ptime = get_the_time('Ymd', $post_id);
  if ($ptime > $mtime) {
    return get_the_time($format, $post_id);
  } elseif ($ptime === $mtime) {
    return null;
  } else {
    return get_the_modified_time($format, $post_id);
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
  //URLにサイトアドレスが含まれている場合
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
  if (includes_string($url, home_url())) {
    return true;
  } else {
    return false;
  }
}
endif;

//WordPressインストールフォルダが含まれているか
if ( !function_exists( 'includes_abspath' ) ):
function includes_abspath($local){
  //パスにサイトアドレスが含まれている場合
  if (includes_string($local, ABSPATH)) {
    return true;
  } else {
    return false;
  }
}
endif;

//site_urlに対するフォルダパスが含まれているか
if ( !function_exists( 'includes_site_path' ) ):
function includes_site_path($local){
  return includes_abspath($local);
}
endif;

//ホームパスが含まれているか
if ( !function_exists( 'includes_home_path' ) ):
function includes_home_path($local){
  //URLにホームアドレスが含まれていない場合
  if (!includes_string($local, get_abs_home_path())) {
    return false;
  } else {
    return true;
  }
}
endif;

//内部URLをローカルパスに変更（サイトURLを置換）
if ( !function_exists( 'url_to_local' ) ):
function url_to_local($url){
  //URLにサイトアドレスが含まれていない場合
  if (!includes_site_url($url)) {
    return false;
  }

  $path = str_replace(site_url('/'), ABSPATH, $url);
  $path = str_replace('//', '/', $path);
  $path = str_replace('\\', '/', $path);

  return $path;
}
endif;

//ローカルパスを内部URLに変更（サイトパス[ABSPATH：インストールパス]を置間）
if ( !function_exists( 'local_to_url' ) ):
function local_to_url($local){
  //パスにサイトアドレスが含まれていない場合
  if (!includes_site_path($local)) {
    return false;
  }
  $url = str_replace(ABSPATH, site_url('/'), $local);
  $url = str_replace('\\', '/', $url);

  return $url;
}
endif;

//ローカルパスを内部ホームURLに変更
if ( !function_exists( 'local_to_home_url' ) ):
function local_to_home_url($local){
  //パスにサイトアドレスが含まれていない場合
  if (!includes_home_path($local)) {
    return false;
  }
  $url = str_replace(get_abs_home_path(), home_url('/'), $local);
  $url = str_replace('\\', '/', $url);

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

//生成アイキャッチディレクトリ
if ( !function_exists( 'get_theme_featured_images_path' ) ):
function get_theme_featured_images_path(){
  $dir = WP_CONTENT_DIR.'/uploads/'.THEME_NAME.'-featured-images/';
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
  return get_abs_home_path().THEME_NAME.'-manifest.json';
}
endif;

//PWAのマニフェストファイルへのURL
if ( !function_exists( 'get_theme_pwa_manifest_json_url' ) ):
function get_theme_pwa_manifest_json_url(){
  return local_to_home_url(get_theme_pwa_manifest_json_file());
}
endif;

//PWAのサービスワーカーへのパス
if ( !function_exists( 'get_theme_pwa_service_worker_js_file' ) ):
function get_theme_pwa_service_worker_js_file(){
  return get_abs_home_path().THEME_NAME.'-service-worker.js';
}
endif;

//PWAのサービスワーカーへのパス
if ( !function_exists( 'get_theme_pwa_service_worker_js_url' ) ):
function get_theme_pwa_service_worker_js_url(){
  return local_to_home_url(get_theme_pwa_service_worker_js_file());
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

//テーマカスタマイザーCSSファイルの書き出し
if ( !function_exists( 'put_theme_css_cache_file' ) ):
function put_theme_css_cache_file(){
  ob_start();
  cocoon_template_part('tmp/css-custom');
  $custum_css = ob_get_clean();
  if ($custum_css) {
    $custum_css_file = get_theme_css_cache_file();
    //エディター用CSSファイルの書き出し
    wp_filesystem_put_contents($custum_css_file, $custum_css);
  }
}
endif;

//テーマのカスタムCSSファイルURL
if ( !function_exists( 'get_theme_css_cache_file_url' ) ):
function get_theme_css_cache_file_url(){
  $url = local_to_url(get_theme_css_cache_file());
  return $url;
}
endif;

//ビジュアルエディターのカスタムカラーパレットCSSファイル
if ( !function_exists( 'get_visual_color_palette_css_cache_file' ) ):
function get_visual_color_palette_css_cache_file(){
  $file = get_theme_css_cache_path().'visual-color-palette.css';
  return $file;
}
endif;

//ビジュアルエディターのカスタムカラーパレットCSSファイルURL
if ( !function_exists( 'get_visual_color_palette_css_cache_url' ) ):
function get_visual_color_palette_css_cache_url(){
  $url = local_to_url(get_visual_color_palette_css_cache_file());
  return $url;
}
endif;

//ブロックエディターのカスタムカラーパレットCSSファイル
if ( !function_exists( 'get_block_color_palette_css_cache_file' ) ):
function get_block_color_palette_css_cache_file(){
  $file = get_theme_css_cache_path().'block-color-palette.css';
  return $file;
}
endif;

//ブロックエディターのカスタムカラーパレットCSSファイルURL
if ( !function_exists( 'get_block_color_palette_css_cache_url' ) ):
function get_block_color_palette_css_cache_url(){
  $url = local_to_url(get_block_color_palette_css_cache_file());
  return $url;
}
endif;

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

//エディターページか
if ( !function_exists( 'is_screen_editor_page' ) ):
function is_screen_editor_page(){
  // 管理画面内でのみ実行
  if (is_admin()) {
    // 現在の画面オブジェクトを取得
    $current_screen = get_current_screen();

    // 画面オブジェクトが取得できた場合
    if ($current_screen) {
      // エディター画面である場合に true を返す
      if (($current_screen->base === 'post') || ($current_screen->base === 'site-editor')) {
          return true;
      }
    }
  }
  // エディター画面ではない場合は false を返す
  return false;
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

//テーマ設定テンプレートページか
if ( !function_exists( 'is_admin_theme_func_text_php_page' ) ):
function is_admin_theme_func_text_php_page(){
  global $pagenow;
  $is_func_text = isset($_GET['page']) && $_GET['page'] == 'theme-func-text';
  return $pagenow == 'admin.php' && $is_func_text;
}
endif;

//Jetpackの統計ページか
if ( !function_exists( 'is_admin_jetpack_stats_page' ) ):
function is_admin_jetpack_stats_page(){
  global $pagenow;
  $is_stats_text = isset($_GET['page']) && $_GET['page'] == 'stats';
  return $pagenow == 'admin.php' && $is_stats_text;
}
endif;

//Cocoon設定ページか
if ( !function_exists( 'is_admin_cocoon_settings_page' ) ):
function is_admin_cocoon_settings_page(){
  global $pagenow;
  $is_cocoon_settings = isset($_GET['page']) && $_GET['page'] == 'theme-settings';
  return $pagenow == 'admin.php' && $is_cocoon_settings;
}
endif;

//カテゴリ・タグ・カスタム分類編集ページか
if ( !function_exists( 'is_admin_term_php_page' ) ):
function is_admin_term_php_page(){
  global $pagenow;
  return $pagenow == 'term.php';
}
endif;

//投稿・新規作成ページかどうか
if ( !function_exists( 'is_admin_post_page' ) ):
function is_admin_post_page(){
  return is_admin_post_new_php_page() || is_admin_post_php_page() || is_admin_theme_func_text_php_page() || is_admin_term_php_page();
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
  if (empty($url)) return;
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
  return function_exists('is_wpforo_page');
}
endif;

//wpforoのページかどうか
if ( !function_exists( 'is_wpforo_plugin_page' ) ):
function is_wpforo_plugin_page($url = ''){
  if (is_wpforo_exist() && !is_admin()) {
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

  $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
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

//サイトフォントソースフォント名の取得
if ( !function_exists( 'get_site_font_source_family' ) ):
function get_site_font_source_family(){
  switch (get_site_font_family()) {
    case 'noto_sans_jp':
      $font_source_family = 'Noto+Sans+JP';
      break;
    case 'noto_serif_jp':
      $font_source_family = 'Noto+Serif+JP';
      break;
    case 'mplus_1p':
      $font_source_family = 'M+PLUS+1p';
      break;
    case 'rounded_mplus_1c':
      $font_source_family = 'M+PLUS+Rounded+1c';
      break;
    case 'kosugi':
      $font_source_family = 'Kosugi';
      break;
    case 'kosugi_maru':
      $font_source_family = 'Kosugi+Maru';
      break;
    case 'sawarabi_gothic':
      $font_source_family = 'Sawarabi+Gothic';
      break;
    case 'sawarabi_mincho':
      $font_source_family = 'Sawarabi+Mincho';
      break;
    case 'noto_sans_korean':
      $font_source_family = 'Noto+Sans+KR';
      break;
    default:
    $font_source_family = null;
      break;
  }
  return $font_source_family;
}
endif;

//サイトフォントソースフォントウェイト（太さ）の取得
if ( !function_exists( 'get_site_font_source_weight' ) ):
function get_site_font_source_weight(){
  switch (get_site_font_family()) {
    case 'noto_sans_jp':
      $font_source_weight = ':wght@100..900';
      break;
    case 'noto_serif_jp':
      $font_source_weight = ':wght@200..900';
      break;
    case 'mplus_1p':
      $font_source_weight = ':wght@100;300;400;500;700;800;900';
      break;
    case 'rounded_mplus_1c':
      $font_source_weight = ':wght@100;300;400;500;700;800;900';
      break;
    case 'kosugi':
      $font_source_weight = '';
      break;
    case 'kosugi_maru':
      $font_source_weight = '';
      break;
    case 'sawarabi_gothic':
      $font_source_weight = '';
      break;
    case 'sawarabi_mincho':
      $font_source_weight = '';
      break;
    case 'noto_sans_korean':
      $font_source_weight = ':wght@100..900';
      break;
    default:
    $font_source_weight = null;
      break;
  }
  return $font_source_weight;
}
endif;


//サイトフォントソースコードURLの取得
if ( !function_exists( 'get_site_font_source_url' ) ):
function get_site_font_source_url(){
  $url = 'https://fonts.googleapis.com/css2?family='.get_site_font_source_family().get_site_font_source_weight().'&display=swap';
  //Pretendardフォント
  if (get_site_font_family_pretendard()) {
    $url = 'https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css';
  }
  return $url;
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

//配列のサニタイズ
if ( !function_exists( 'sanitize_array' ) ):
function sanitize_array($array){
  if (is_array($array)) {
    return $array;
  } else {
    return array();
  }
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
    if (!is_dir($file)) {
      return file_get_contents($file);
    }
  } else {//外部URL
    return @file_get_contents($file, false, stream_context_create($options));
  }
}
endif;
if ( !function_exists( 'get_file_contents' ) ):
function get_file_contents($file){
  return wp_filesystem_get_contents($file);
}
endif;

if (!defined('FS_CHMOD_FILE')) {
  define( 'FS_CHMOD_FILE', ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );
}
//ファイル内容の出力
if ( !function_exists( 'wp_filesystem_put_contents' ) ):
function wp_filesystem_put_contents($new_file, $file_data, $chmod = 0 ){
  return file_put_contents($new_file, $file_data, $chmod);
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
  $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';// ユーザエージェントを取得
  if((strpos($ua, 'Windows Live Writer') !== false)
      || (strpos($ua, 'Open Live Writer') !== false)
    ){
    return true;
  }
  return false;
}
endif;

//ユーザーエージェントがボットかどうか
if ( !function_exists( 'is_useragent_robot' ) ):
function is_useragent_robot(){
  if (!isset($_SERVER['HTTP_USER_AGENT'])) {
    return false;
  }
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
  if ($value === '' || is_null($value)) {
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

//指定されたURLはWordPressホームURLかどうか
if ( !function_exists( 'is_home_url' ) ):
function is_home_url($url){
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

//リクエストURIが/app/public/index.phpか（再利用ブロック対策）
if ( !function_exists( 'is_server_request_uri_app_public_index_php' ) ):
function is_server_request_uri_app_public_index_php(){
  $res = false;
  if (isset($_SERVER['REQUEST_URI'])) {
    $res = $_SERVER['REQUEST_URI'] == '/app/public/index.php';
  }
  return $res;
}
endif;

//service-worker.js除外
if ( !function_exists( 'includes_service_worker_js_in_http_referer' ) ):
function includes_service_worker_js_in_http_referer(){
  $res = false;
  if (isset($_SERVER['HTTP_REFERER'])) {
    $res = includes_string($_SERVER['HTTP_REFERER'], 'service-worker.js');
  }
  return $res;
}
endif;

//cron除外
if ( !function_exists( 'includes_wp_cron_php_in_request_uri' ) ):
function includes_wp_cron_php_in_request_uri(){
  $res = false;
  if (isset($_SERVER['REQUEST_URI'])) {
    $res = includes_string($_SERVER['REQUEST_URI'], '/wp-cron.php');
  }
  return $res;
}
endif;

//管理画面除外（再利用ブロック対策）
if ( !function_exists( 'includes_wp_admin_in_request_uri' ) ):
function includes_wp_admin_in_request_uri(){
  $res = false;
  if (isset($_SERVER['REQUEST_URI'])) {
    $res = includes_string($_SERVER['REQUEST_URI'], '/wp-admin/');
  }
  return $res;
}
endif;

//現在のページでサイドバーが表示されるか
if ( !function_exists( 'is_the_page_sidebar_visible' ) ):
function is_the_page_sidebar_visible(){
  //サイドバー表示設定
  $is_sidebar_visible = true;
  //var_dump(get_sidebar_display_type());
  switch (get_sidebar_display_type()) {
    case 'no_display_all':
      $is_sidebar_visible = false;
      break;
    case 'no_display_front_page':
      if (is_front_top_page()) {
        $is_sidebar_visible = false;
      }
      break;
    case 'no_display_index_pages':
      if (!is_singular()) {
        $is_sidebar_visible = false;
      }
      break;
    case 'no_display_pages':
      if (is_page()) {
        $is_sidebar_visible = false;
      }
      break;
    case 'no_display_singles':
      if (is_single()) {
        $is_sidebar_visible = false;
      }
      break;
    case 'no_display_404_pages':
      if (is_404()) {
        $is_sidebar_visible = false;
      }
      break;
    default:

      break;
  }

  //投稿管理画面で「1カラム」が選択されている場合
  if (is_singular() && is_singular_page_type_column1()) {
    $is_sidebar_visible = false;
  }

  //投稿管理画面で「本文のみ」が選択されている場合
  if (is_singular() && is_singular_page_type_content_only()) {
    $is_sidebar_visible = false;
  }

  //投稿管理画面で「狭い」が選択されている場合
  if (is_singular() && is_singular_page_type_narrow()) {
    $is_sidebar_visible = false;
  }

  //投稿管理画面で「広い」が選択されている場合
  if (is_singular() && is_singular_page_type_wide()) {
    $is_sidebar_visible = false;
  }

  //サイドバーにウィジェットが入っていない場合
  if (!is_active_sidebar( 'sidebar' ) && !is_active_sidebar( 'sidebar-scroll' )) {
    $is_sidebar_visible = false;
  }

  return apply_filters('is_the_page_sidebar_visible', $is_sidebar_visible);
}
endif;

//インデックスページでサイドバーが表示されるか
if ( !function_exists( 'is_index_page_sidebar_visible' ) ):
function is_index_page_sidebar_visible(){
  $is_sidebar_visible = true;

  switch (get_sidebar_display_type()) {
    case 'no_display_all':
      $is_sidebar_visible = false;
      break;
    case 'no_display_index_pages':
      if (!is_singular()) {
        $is_sidebar_visible = false;
      }
      break;
  }

  //サイドバーにウィジェットが入っていない場合
  if (!is_active_sidebar( 'sidebar' )) {
    $is_sidebar_visible = false;
  }
  return apply_filters('is_index_page_sidebar_visible', $is_sidebar_visible);
}
endif;

//フロントトップページかどうか
if ( !function_exists( 'is_front_top_page' ) ):
function is_front_top_page(){
  return is_front_page() && !is_paged() && !isset($_GET['cat']);
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
  $url = get_site_icon_url();
  if (!$url) {
    $url = get_template_directory_uri().'/images/site-icon32x32.png';
  }
  return apply_filters('get_site_favicon_url', $url);
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
  if ($value) {
    $value = strip_tags($value);
    $value = esc_html(trim($value));
    $value = str_replace('"', '', $value);
    $value = str_replace("'", '', $value);
    $value = str_replace('[', '', $value);
    $value = str_replace(']', '', $value);
  }
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
    $to = date_i18n('U');
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
  $from = new DateTime(date('Y-m-d', $from));
  $to = new DateTime('today');
  $diff = $from->diff($to);
  $year = $diff->format('%y'.$unit);
  return $year;
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
  //テキストのみ
  $not_url_reg = '!(https?://[-_.!~*\'()a-zA-Z0-9;/?:\@&=+\$,%#]+)';
  if ($is_p) {
    $pattern = '{^<p>'.$not_url_reg.'</p>}im';
    $append = '<p>$1</p>';
  } else {
    $pattern = '{^'.$not_url_reg.'}im';
    $append = '$1';
  }
  $the_content = preg_replace($pattern, $append, $the_content);

  //URLリンク
  $not_url_reg = '!(<a.+>https?://[-_.!~*\'()a-zA-Z0-9;/?:\@&=+\$,%#]+</a>)';
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

//投稿・固定ページのSNSシェア画像の取得（シェア画像優先）
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
    if (isset($image[0])) {
      $sns_image_url = $image[0];
    }
  } else if ( preg_match( $searchPattern, $content, $image ) && !is_archive() && is_auto_post_thumbnail_enable()) {//投稿にアイキャッチは無いが画像がある場合の処理
    if (isset($image[2])) {
      $sns_image_url = $image[2];
    }
  } else if ( $no_image_url = get_no_image_url() ){//NO IMAGEが設定されている場合
    $sns_image_url = $no_image_url;
  } else if ( $ogp_home_image_url = get_ogp_home_image_url() ){//ホームイメージが設定されている場合
    $sns_image_url = $ogp_home_image_url;
  } else {
    $sns_image_url = NO_IMAGE_LARGE;
  }
  return apply_filters('get_singular_sns_share_image_url', $sns_image_url);
}
endif;

//投稿・固定ページのアイキャッチ画像の取得（アイキャッチ優先）
if ( !function_exists( 'get_singular_eyecatch_image_url' ) ):
function get_singular_eyecatch_image_url(){
  //本文を取得
  global $post;
  $content = '';
  if ( isset( $post->post_content ) ){
    $content = $post->post_content;
  }
  $eyecatch_image_url = NO_IMAGE_LARGE;
  //投稿にイメージがあるか調べるための正規表現
  $searchPattern = '/<img.*?src=(["\'])(.+?)\1.*?>/i';
  if (has_post_thumbnail()){//投稿にサムネイルがある場合の処理
    $image_id = get_post_thumbnail_id();
    $image = wp_get_attachment_image_src( $image_id, 'full');
    if (isset($image[0])) {
      $eyecatch_image_url = $image[0];
    }
  } else if ($singular_sns_image_url = get_singular_sns_image_url()) {
    $eyecatch_image_url = $singular_sns_image_url;
  } else if ( preg_match( $searchPattern, $content, $image ) && !is_archive() && is_auto_post_thumbnail_enable()) {//投稿にサムネイルは無いが画像がある場合の処理
    if (isset($image[2])) {
      $eyecatch_image_url = $image[2];
    }
  } else if ( $no_image_url = get_no_image_url() ){//NO IMAGEが設定されている場合
    $eyecatch_image_url = $no_image_url;
  } else if ( $ogp_home_image_url = get_ogp_home_image_url() ){//ホームイメージが設定されている場合
    $eyecatch_image_url = $ogp_home_image_url;
  }
  return apply_filters('get_singular_eyecatch_image_url', $eyecatch_image_url);
}
endif;

//wpForo URLが含まれている場合
if ( !function_exists( 'includes_wpforo_url' ) ):
function includes_wpforo_url($url){
  if (is_wpforo_exist()) {
    if (isset(WPF()->url)) {
      return includes_string($url, WPF()->url);
    }
  }
  return false;
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
    $color = $site_key_color;
  } else {
    $color = DEFAULT_EDITOR_KEY_COLOR;
  }
  return apply_filters('get_editor_key_color', $color);
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

//内部URLからカテゴリーオブジェクトを取得する
if ( !function_exists( 'url_to_category_object' ) ):
function url_to_category_object($url){
  //タグのベースURLの正規表現
  $category_base = get_option('category_base');
  $category_base = $category_base ? preg_quote($category_base, '/') : 'category';
  $quoteed_category_base = preg_quote($category_base);
  $reg = '{/'.$quoteed_category_base.'/([^/]+)|/\?cat\=([^&]+)}';
  preg_match($reg, $url, $m);
  $slug = null;
  if (isset($m[1]) && $m[1]) {
    $slug = $m[1];
    //カテゴリーの取得
    if ($slug) {
      $category = get_category_by_slug($slug);
      if ($category) {
        return $category;
      }
    }
  } elseif (isset($m[2]) && $m[2]) {
    $cat_id = $m[2];
    $category = get_category($cat_id);
    if ($category) {
      return $category;
    }
  }
}
endif;

//内部URLからタグオブジェクトを取得する
if ( !function_exists( 'url_to_tag_object' ) ):
function url_to_tag_object($url){
  //タグのベースURLの正規表現
  $tag_base = get_option('tag_base');
  $tag_base = $tag_base ? preg_quote($tag_base, '/') : 'tag';
  $quoteed_tag_base = preg_quote($tag_base);
  $reg = '{/'.$quoteed_tag_base.'/([^/]+)|/\?'.$quoteed_tag_base.'\=([^&]+)}';
  preg_match($reg, $url, $m);
  $slug = null;
  if (isset($m[1]) && $m[1]) {
    $slug = $m[1];
  } elseif (isset($m[2]) && $m[2]) {
    $slug = $m[2];
  }
  //タグの取得
  if ($slug) {
    $tag = get_term_by('slug', $slug, 'post_tag');
    if ($tag) {
      return $tag;
    }
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
  $removed_content = preg_replace('/\[author_box.*\]/', '', $removed_content);
  $removed_content = preg_replace('/\[rank.*\]/', '', $removed_content);
  $removed_content = preg_replace('/\[star.*\]/', '', $removed_content);
  $removed_content = preg_replace('/\[\[.*\]\]/', '', $removed_content);
  $removed_content = apply_filters('get_shortcode_removed_content', $removed_content);
  return $removed_content;
}
endif;

//get_template_partに関するフック付加
if ( !function_exists( 'cocoon_template_part' ) ):
function cocoon_template_part($slug){
  ob_start();
  get_template_part($slug);
  $content = ob_get_clean();

  // 読み込み前発火
  if (has_filter("cocoon_part_before__{$slug}")) {
    do_action("cocoon_part_before__{$slug}");
  }

  // 書き換え
  if (has_filter("cocoon_part__{$slug}")) {
    $content = apply_filters("cocoon_part__{$slug}" ,$content);
  }
  echo $content;

  // 読み込み後発火
  if (has_filter("cocoon_part_after__{$slug}")) {
    do_action("cocoon_part_after__{$slug}");
  }
}
endif;

//テンプレートのタグ取得
if ( !function_exists( 'get_template_part_tag' ) ):
function get_template_part_tag($slug){
  ob_start();
  cocoon_template_part($slug);
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

//コメントボタンタグの取得
if ( !function_exists( 'get_mobile_comments_button_tag' ) ):
function get_mobile_comments_button_tag(){
  return get_template_part_tag('tmp/mobile-comments-button');
}
endif;

//確実にホームパスを取得するget_home_path関数
if ( !function_exists( 'get_abs_home_path' ) ):
function get_abs_home_path(){
  $site_url = get_site_url(null, '/');
  $home_url = get_home_url(null, '/');

  if ($site_url == $home_url) {
    return ABSPATH;
  } else {
    if (includes_string($site_url, $home_url)) {
      $dir = str_replace($home_url, '', $site_url);

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

//ブログカードコンテンツ
if ( !function_exists( 'fix_blogcard_content' ) ):
function fix_blogcard_content($the_content){
  $the_content = str_replace('</p><p>', "</p>\n<p>", $the_content);
  $the_content = preg_replace('{<p> +}', '<p>', $the_content);
  $the_content = preg_replace('{ +</p>}', '</p>', $the_content);
  return $the_content;
}
endif;


//タームのメタデーターが存在しているか
if ( !function_exists( 'term_metadata_exists' ) ):
function term_metadata_exists($term_id, $meta_key){
  return metadata_exists('term', $term_id, $meta_key);
}
endif;

//スキン制御変数をクリアしてバックアップする
if ( !function_exists( 'clear_global_skin_theme_options' ) ):
function clear_global_skin_theme_options(){
  global $_THEME_OPTIONS, $_FORM_SKIN_OPTIONS;
  $_FORM_SKIN_OPTIONS = $_THEME_OPTIONS;
  $_THEME_OPTIONS = array();
}
endif;

//スキン制御変数を元に戻す
if ( !function_exists( 'restore_global_skin_theme_options' ) ):
function restore_global_skin_theme_options(){
  global $_THEME_OPTIONS, $_FORM_SKIN_OPTIONS;
  $_THEME_OPTIONS = $_FORM_SKIN_OPTIONS;
}
endif;

//拡張エディターカラーのスタイル生成
if ( !function_exists( 'generate_block_editor_color_style' ) ):
function generate_block_editor_color_style($class, $name, $color_code, $is_all = false){?>
<?php if($class = 'bb-'.$name || $is_all): ?>
.blank-box.bb-<?php echo $name; ?> {
  border-color: <?php echo $color_code; ?>;
}

.blank-box.bb-tab.bb-<?php echo $name; ?>::before {
  background-color: <?php echo $color_code; ?>;
}
<?php endif; ?>

<?php if($class = 'btn-wrap-'.$name || $is_all): ?>
.btn-<?php echo $name; ?>, .btn-wrap.btn-wrap-<?php echo $name; ?> > a {
  background-color: <?php echo $color_code; ?>;
}
<?php endif; ?>

<?php if($class = 'mc-'.$name || $is_all): ?>
.mc-<?php echo $name; ?> {
  background-color: <?php echo $color_code; ?>;
  color: #fff;
  border: none;
}
.mc-<?php echo $name; ?>.micro-bottom::after {
  border-bottom-color: <?php echo $color_code; ?>;
  border-top-color: transparent;
}
.mc-<?php echo $name; ?>::before {
  border-top-color: transparent;
  border-bottom-color: transparent;
}
.mc-<?php echo $name; ?>::after {
  border-top-color: <?php echo $color_code; ?>;
}
<?php endif; ?>

<?php if($class = 'cb-'.$name || $is_all): ?>
.cb-<?php echo $name; ?>.caption-box {
  border-color: <?php echo $color_code; ?>;
}
.cb-<?php echo $name; ?> .caption-box-label {
  background-color: <?php echo $color_code; ?>;
  color: #fff;
}
<?php endif; ?>

<?php if($class = 'tcb-'.$name || $is_all): ?>
.tcb-<?php echo $name; ?> .tab-caption-box-label {
  background-color: <?php echo $color_code; ?>;
  color: #fff;
}
.tcb-<?php echo $name; ?> .tab-caption-box-content {
  border-color: <?php echo $color_code; ?>;
}
<?php endif; ?>

<?php if($class = 'lb-'.$name || $is_all): ?>
.lb-<?php echo $name; ?> .label-box-content {
  border-color: <?php echo $color_code; ?>;
}
<?php endif; ?>

<?php if($class = 'tb-'.$name || $is_all): ?>
.tb-<?php echo $name; ?> .toggle-button {
  border: 2px solid <?php echo $color_code; ?>;
  background: <?php echo $color_code; ?>;
  color: #fff;
}
.tb-<?php echo $name; ?> .toggle-button::before {
  color: #ccc;
}
.tb-<?php echo $name; ?> .toggle-checkbox:checked ~ .toggle-content {
  border-color: <?php echo $color_code; ?>;
}
<?php endif; ?>

<?php if($class = 'llc-'.$name || $is_all): ?>
.iic-<?php echo $name; ?> li::before {
  color: <?php echo $color_code; ?>;
}
<?php endif; ?>

<?php if($class = 'has-'.$name.'-color' || $is_all): ?>
div .has-<?php echo $name; ?>-color {
  color: <?php echo $color_code; ?>;
}
<?php endif; ?>

<?php if($class = 'has-'.$name.'-background-color' || $is_all): ?>
div .has-<?php echo $name; ?>-background-color {
  background-color: <?php echo $color_code; ?>;
}
<?php endif; ?>
<?php
}
endif;

//拡張エディターカラーのスタイル取得
if ( !function_exists( 'get_block_editor_color_style' ) ):
function get_block_editor_color_style($class, $name, $color_code, $is_all = false){
  ob_start();
  generate_block_editor_color_style($class, $name, $color_code, $is_all);
  return ob_get_clean();
}
endif;

//タブレットをモバイルとしないモバイル判定関数
if ( !function_exists( 'is_mobile' ) ):
//スマートフォン表示分岐
function is_mobile(){
  if (!isset($_SERVER['HTTP_USER_AGENT'])) {
    return false;
  }
  $useragents = array(
    'iPhone', // iPhone
    'iPod', // iPod touch
    'Android.*Mobile', // 1.5+ Android *** Only mobile
    'Windows.*Phone', // *** Windows Phone
    'dream', // Pre 1.5 Android
    'CUPCAKE', // 1.5+ Android
    'blackberry9500', // Storm
    'blackberry9530', // Storm
    'blackberry9520', // Storm v2
    'blackberry9550', // Storm v2
    'blackberry9800', // Torch
    'webOS', // Palm Pre Experimental
    'incognito', // Other iPhone browser
    'webmate' ,// Other iPhone browser
    'Mobile.*Firefox', // Firefox OS
    'Opera Mini', // Opera Mini Browser
    'BB10', // BlackBerry 10
  );
  $pattern = '/'.implode('|', $useragents).'/i';
  return preg_match($pattern, $_SERVER['HTTP_USER_AGENT']);
}
endif;

//iOSかどうかを判定する
//https://net-viz.info/archives/409/
function is_ios() {
  if (!isset($_SERVER['HTTP_USER_AGENT'])) {
    return false;
  }
  $is_ipad = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
  global $is_iphone;
  if ($is_iphone || $is_ipad) {
    return true;
  }
}

//Font Awesome5変換
if ( !function_exists( 'change_fa' ) ):
function change_fa($buffer){
  if (
    is_site_icon_font_font_awesome_5()
    && preg_match_all('/<([a-z]+ [^>]*?class=")((fa fa-[a-z\-]+)[^"]*?)("[^>]*?)>/i', $buffer, $m)
  ) {
    $fa4_alls = $m[0];
    $befores = $m[1];
    $classes = $m[2];
    $fa4_classes = $m[3];
    $afters = $m[4];
    $list = get_font_awesome_exchange_list();
    $i = 0;
    foreach ($fa4_classes as $fa4_class) {
      $fa5_class = str_replace('fa ', 'fas ', $fa4_class);
      foreach ($list as $ex) {
        $fa4 = $ex[0];
        $fa5 = $ex[1];
        //_v($fa4.'============'.$fa4_class);
        if ($fa4 == $fa4_class) {
          //_v($fa4.'============'.$fa4_class);
          $fa5_class = $fa5;
          continue;
        }
      }
      $fa4_all_tag = $fa4_alls[$i];
      $fa5_all_tag = str_replace($fa4_class, $fa5_class, $fa4_all_tag);
      $buffer = str_replace($fa4_all_tag, $fa5_all_tag, $buffer);
      $i++;
    }
  } elseif (
    is_site_icon_font_font_awesome_4() &&
    preg_match_all('/<([a-z]+ [^>]*?class=")((fa[srb] fa-[a-z\-]+)[^"]*?)("[^>]*?)>/i', $buffer, $m)
  ){
    //_v($m);
    $fa5_alls = $m[0];
    $befores = $m[1];
    $classes = $m[2];
    $fa5_classes = $m[3];
    //_v($fa5_classes);
    $afters = $m[4];
    $list = get_font_awesome_exchange_list();
    $i = 0;
    foreach ($fa5_classes as $fa5_class) {
      //_v($i);
      $fa4_class = str_replace('fas ', 'fa ', $fa5_class);
      foreach ($list as $ex) {
        $fa5 = $ex[1];
        $fa4 = $ex[0];
        //_v($fa5.'============'.$fa5_class);
        if ($fa5 == $fa5_class) {
          //_v($fa5.'============'.$fa5_class);
          $fa4_class = $fa4;
          continue;
        }
      }
      $fa5_all_tag = $fa5_alls[$i];
      $fa4_all_tag = str_replace($fa5_class, $fa4_class, $fa5_all_tag);
      $buffer = str_replace($fa5_all_tag, $fa4_all_tag, $buffer);
      $i++;
    }
  }
  return $buffer;
}
endif;

//WordPress「表示設定」の「1ページに表示する最大投稿数」を取得する
if ( !function_exists( 'get_option_posts_per_page' ) ):
function get_option_posts_per_page(){
  return intval(get_option('posts_per_page'));
}
endif;

//カテゴリーIDからカテゴリー名を取得
if ( !function_exists( 'get_category_name_by_id' ) ):
function get_category_name_by_id($id){
  //カテゴリIDからカテゴリ情報取得
  $category = get_category($id);
  if (isset($category->cat_name)) {
    //カテゴリー名表示
    return $category->cat_name;
  }
}
endif;

//target=_blankの場合はrel=noopenerを取得
if ( !function_exists( 'get_rel_by_target' ) ):
function get_rel_by_target($target){
  if ($target == '_blank') {
    return ' rel="noopener"';
  }
}
endif;

//存在するカテゴリIDかどうか
if ( !function_exists( 'is_category_exist' ) ):
function is_category_exist($cat_id){
  return is_numeric($cat_id) && get_category($cat_id);
}
endif;

//管理ページが投稿か
if ( !function_exists( 'is_admin_single' ) ):
function is_admin_single(){
  global $post_type;
  return $post_type !== 'page';
}
endif;

//管理ページが固定ページか
if ( !function_exists( 'is_admin_page' ) ):
function is_admin_page(){
  global $post_type;
  return $post_type === 'page';
}
endif;

//URLがAmazonかどうか
if ( !function_exists( 'is_amazon_site_page' ) ):
function is_amazon_site_page($URI){
  return includes_string($URI, '//amzn.to/') || includes_string($URI, '//www.amazon.co');
}
endif;

//URLが楽天かどうか
if ( !function_exists( 'is_rakuten_site_page' ) ):
function is_rakuten_site_page($URI){
  return includes_string($URI, '//a.r10.to/') || preg_match('{//.+\.rakuten\.co\.jp/}', $URI);
}
endif;

//投稿の個別noindex idをすべて取得する
if ( !function_exists( 'get_postmeta_value_enable_post_ids' ) ):
function get_postmeta_value_enable_post_ids($meta_key){
  global $wpdb;
  $res = $wpdb->get_results("SELECT DISTINCT GROUP_CONCAT(post_id) AS ids FROM {$wpdb->prefix}postmeta WHERE (meta_key = '{$meta_key}') AND (meta_value = 1)");
  $result = (isset($res[0]) && $res[0]->ids) ? explode(',', $res[0]->ids) : array();
  return $result;
}
endif;

//カテゴリーの個別noindex idをすべて取得する
if ( !function_exists( 'get_termmeta_value_enable_ids' ) ):
function get_termmeta_value_enable_ids($meta_key){
  global $wpdb;
  $res = $wpdb->get_results("SELECT DISTINCT GROUP_CONCAT(term_id) AS ids FROM {$wpdb->prefix}termmeta WHERE (meta_key = '{$meta_key}') AND (meta_value = 1)");
  $ids = (isset($res[0]) && $res[0]->ids) ? explode(',', $res[0]->ids) : array();
  $result = array();
  if ($meta_key === 'the_category_noindex') {
    foreach ($ids as $id) {
      if (get_category($id)) {
        array_push($result, $id);
      }
    }
  }
  if ($meta_key === 'the_tag_noindex') {
    foreach ($ids as $id) {
      if (get_tag($id)) {
        array_push($result, $id);
      }
    }
  }
  return $result;
}
endif;

//WordPressバージョンが5.5以上かどうか
if ( !function_exists( 'is_wp_5_5_or_over' ) ):
function is_wp_5_5_or_over(){
  return get_bloginfo('version') >= '5.5';
}
endif;

//WordPressバージョンが5.8以上かどうか
if ( !function_exists( 'is_wp_5_8_or_over' ) ):
function is_wp_5_8_or_over(){
  return get_bloginfo('version') >= '5.8';
}
endif;

//WordPressバージョンが6.1以上かどうか
if ( !function_exists( 'is_wp_6_1_or_over' ) ):
function is_wp_6_1_or_over(){
  return get_bloginfo('version') >= '6.1';
}
endif;

//WordPressバージョンが6.5以上かどうか
if ( !function_exists( 'is_wp_6_5_or_over' ) ):
function is_wp_6_5_or_over(){
  return get_bloginfo('version') >= '6.5';
}
endif;

//WordPress5.5からのLazy Loadが有効な環境かどうか
if ( !function_exists( 'is_wp_lazy_load_valid' ) ):
function is_wp_lazy_load_valid(){
  global $is_safari;
  return is_wp_5_5_or_over() && !$is_safari;
}
endif;

//エディターでページタイプでスタイルを変更する用のclassを出力
if ( !function_exists( 'get_editor_page_type_class' ) ):
function get_editor_page_type_class(){
  $page_type = '';
  if (is_singular_page_type_wide()) {
    $page_type = ' page-type-wide';
  } elseif (is_singular_page_type_full_wide()) {
    $page_type = ' page-type-full-wide';
  }
  return $page_type;
}
endif;

if ( !function_exists( 'use_gutenberg_editor' ) ):
function use_gutenberg_editor(){
  $current_screen = get_current_screen();
  return ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) || ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() );
}
endif;

//ピクセル数を数字にする
if ( !function_exists( 'px_to_int' ) ):
function px_to_int($px){
  return intval(str_replace('px', '', $px));
}
endif;

// //デバッグ用の関数
// if ( !function_exists( '_v' ) ):
// function _v($v){
//   do_action( 'qm/debug', $v );
// }
// endif;

//著者プロフィールページのURLを取得
if ( !function_exists( 'get_the_auther_profile_page_url' ) ):
function get_the_auther_profile_page_url(){
  if (is_post_author_visible() && get_the_author()) {
    $author_id = get_the_author_meta( 'ID' );
    $profile_page_url = get_the_author_profile_page_url($author_id);
    if ($profile_page_url) {
      $url = $profile_page_url;
    } else {
      $url = get_author_posts_url( $author_id );
    }
  } else {
    $url = home_url();
  }
  return $url;
}
endif;

//著者プロフィール名前を取得
if ( !function_exists( 'get_the_auther_profile_name' ) ):
function get_the_auther_profile_name(){
  if (is_post_author_visible() && get_the_author()) {
    $name = get_the_author();
  } else {
    $name = get_bloginfo('name');
  }
  return $name;
}
endif;

//フロントページタイプの取得
if ( !function_exists( 'get_front_page_type_class' ) ):
function get_front_page_type_class(){
  return 'front-page-type-'.str_replace('_', '-', get_front_page_type());;
}
endif;

//RESTリクエストかどうか
if ( !function_exists( 'is_rest' ) ):
function is_rest() {
  return ( defined( 'REST_REQUEST' ) && REST_REQUEST );
}
endif;

//HTML内のAタグをSPANタグに変換
if ( !function_exists( 'replace_a_tags_to_span_tags' ) ):
function replace_a_tags_to_span_tags( $html ) {
  $html = str_replace('<a ', '<span ', $html);
  $html = str_replace('</a>', '</span>', $html);
  return $html;
}
endif;

//ブロックエディター画面かどうか
if ( !function_exists( 'is_block_editor_page' ) ):
function is_block_editor_page() {
  return is_admin() && has_blocks();
}
endif;

//クラシックエディター画面かどうか
if ( !function_exists( 'is_classic_editor' ) ):
function is_classic_editor() {
  // クラシックエディターが設定されているかどうかを確認
  if (class_exists('Classic_Editor') || !is_gutenberg_editor_enable() || is_classicpress()) {
      return true;
  }

  return false;
}
endif;

//HTMLで使用するヘックスカラーが暗い色かどうか（）
if ( !function_exists( 'is_dark_hexcolor' ) ):
function is_dark_hexcolor($hexcolor) {
  if (!$hexcolor || (strlen($hexcolor) !== 7)) {
    return false;
  }
  // カラーコードから"#"を削除
  $hexcolor = ltrim($hexcolor, '#');

  // 16進数のカラーコードを10進数のRGB値に変換
  $r = hexdec(substr($hexcolor, 0, 2));
  $g = hexdec(substr($hexcolor, 2, 2));
  $b = hexdec(substr($hexcolor, 4, 2));

  // 平均輝度を計算
  $luminance = ($r + $g + $b) / 3;

  // しきい値（この値は調整可能、0から255の範囲）
  // $threshold = 128; //半分
  $threshold = 162;

  // 輝度がしきい値より低いかどうかを返す
  return $luminance < $threshold;
}
endif;

//使用言語が韓国語かどうか
if ( !function_exists( 'is_wp_language_korean' ) ):
function is_wp_language_korean() {
  // WordPressの現在の言語を取得
  $current_language = get_locale();

  // 言語が韓国語（`ko_KR`）であるかどうかを判別
  if ($current_language === 'ko_KR') {
      return true;
  } else {
      return false;
  }
}
endif;

//文章内にtocショートコードが使われているか
if ( !function_exists( 'is_toc_shortcode_includes' ) ):
function is_toc_shortcode_includes($content) {
  return preg_match(TOC_SHORTCODE_REG, $content, $m);
}
endif;

//カレントページのURLを取得する
if ( !function_exists( 'get_current_page_url' ) ):
function get_current_page_url() {
  global $wp;

  return home_url(add_query_arg(array(), $wp->request));
}
endif;


////////////////////////////////////////////////////
// 以下子テーマカスタマイズ時にエラーがないようにするためのエイリアス
////////////////////////////////////////////////////
//barba.jsスクリプトの読み込み
if ( !function_exists( 'wp_enqueue_script_barba_js' ) ):
function wp_enqueue_script_barba_js(){}
endif;
//barba.jsのネームスペース
if ( !function_exists( 'get_barba_name_space' ) ):
function get_barba_name_space(){}
endif;
//文字列内のスクリプトをbarba.js用に取り出して出力する
if ( !function_exists( 'generate_baruba_js_scripts' ) ):
function generate_baruba_js_scripts($tag){}
endif;
//ユニバーサルアナリティクスID
define('OP_GOOGLE_ANALYTICS_TRACKING_ID', 'google_analytics_tracking_id');
if ( !function_exists( 'get_google_analytics_tracking_id' ) ):
function get_google_analytics_tracking_id(){}
endif;
//Google Analyticsのスクリプト
define('OP_GOOGLE_ANALYTICS_SCRIPT', 'google_analytics_script');
if ( !function_exists( 'get_google_analytics_script' ) ):
function get_google_analytics_script(){}
endif;
if ( !function_exists( 'is_google_analytics_script_gtag_js' ) ):
function is_google_analytics_script_gtag_js(){}
endif;