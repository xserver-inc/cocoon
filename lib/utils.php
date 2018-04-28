<?php

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
function get_the_nolink_category(){
  $categories = get_the_category();
  //var_dump($categories);
  if ( isset($categories[0]) ) {
    $category = $categories[0];
    return '<span class="cat-label cat-label-'.$category->cat_ID.'">'.$category->cat_name.'</span>';
  }
}
endif;


//リンクのないカテゴリーの出力
if ( !function_exists( 'the_nolink_category' ) ):
function the_nolink_category(){
  echo get_the_nolink_category();
}

endif;


//タグリンクの取得
if ( !function_exists( 'get_the_tag_links' ) ):
function get_the_tag_links(){
  $tags = null;
  $posttags = get_the_tags();
  if ( $posttags ) {
    foreach(get_the_tags() as $tag){
      $tags .= '<a class="tag-link tag-link-'.$tag->term_id.'" href="'.get_tag_link( $tag->term_id ).'">'.$tag->name.'</a>';
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
  // switch ($format) {
  //   case DATA_AD_FORMAT_AUTO:
  //     $format = DATA_AD_FORMAT_AUTO;
  //     break;
  //   case DATA_AD_FORMAT_RECTANGLE:
  //     $format = DATA_AD_FORMAT_RECTANGLE;
  //     break;
  //   case DATA_AD_FORMAT_HORIZONTAL:
  //     $format = DATA_AD_FORMAT_HORIZONTAL;
  //     break;
  //   case DATA_AD_FORMAT_VERTICAL:
  //     $format = DATA_AD_FORMAT_VERTICAL;
  //     break;
  //   case DATA_AD_FORMAT_AUTORELAXED:
  //     $format = DATA_AD_FORMAT_AUTORELAXED;
  //     break;
  //   case DATA_AD_FORMAT_FLUID:
  //     $format = DATA_AD_FORMAT_FLUID;
  //     break;
  //   case DATA_AD_FORMAT_LINK:
  //     $format = DATA_AD_FORMAT_LINK;
  //     break;
  //   default:
  //     $format = DATA_AD_FORMAT_RECTANGLE;
  //     break;
  // }
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

//オプションの値をデータベースに保存する
if ( !function_exists( 'update_theme_option' ) ):
function update_theme_option($option_name){
  $opt_val = isset($_POST[$option_name]) ? $_POST[$option_name] : '';
  //update_option($option_name, $opt_val);
  set_theme_mod($option_name, $opt_val);
}
endif;

//オプションの値をデータベースから取得する
if ( !function_exists( 'get_theme_option' ) ):
function get_theme_option($option_name, $default = null){
  return get_theme_mod($option_name, $default);
}
endif;

//highlight-jsのCSS URLを取得
if ( !function_exists( 'get_highlight_js_css_url' ) ):
function get_highlight_js_css_url(){
  return get_template_directory_uri() . '/plugins/highlight-js/styles/'.get_code_highlight_style().'.css';
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
                      breakpoint: 1030,
                      settings: {
                        slidesToShow: 4,
                        slidesToScroll: 4
                      }
                    },
                    {
                      breakpoint: 768,
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


//投稿画面ポスト時の確認ダイアログ
if ( !function_exists( 'wp_enqueue_confirmation_before_publish' ) ):
function wp_enqueue_confirmation_before_publish(){
  if (is_confirmation_before_publish()) {
    $post_text = __( '公開', THEME_NAME );
    $confirm_text = __( '記事を公開してもよろしいですか？', THEME_NAME );
    $data = <<< EOM
window.onload = function() {
  var id = document.getElementById('publish');
  if (id.value.indexOf("$post_text", 0) != -1) {
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
  wp_add_inline_style( THEME_NAME.'-style', $css_custom );
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
function get_update_time($format = 'Y.m.d') {
  $mtime = get_the_modified_time('Ymd');
  $ptime = get_the_time('Ymd');
  if ($ptime > $mtime) {
    return get_the_time($format);
  } elseif ($ptime === $mtime) {
    return null;
  } else {
    return get_the_modified_time($format);
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
  if (strpos($url, home_url()) === false) {
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
  if (strpos($local, ABSPATH) === false) {
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
  if (!includes_site_url($url)) {
    return false;
  }

  $path = str_replace(site_url(), ABSPATH, $url);
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
  //URLにサイトアドレスが含まれていない場合
  if (!includes_abspath($local)) {
    return false;
  }
  $url = str_replace(ABSPATH, site_url().'/', $local);
  //$url = str_replace('//', '/', $url);
  $url = str_replace('\\', '/', $url);
  // _v($local);
  // _v(ABSPATH);
  // _v(site_url());
  // _v($url);

  return $url;
}
endif;


//テーマのリソースディレクトリ
if ( !function_exists( 'get_theme_resources_dir' ) ):
function get_theme_resources_dir(){
  $dir = WP_CONTENT_DIR.'/uploads/'.THEME_NAME.'-resources/';
  if (!file_exists($dir)) mkdir($dir, 0777, true);
  return $dir;
}
endif;

//テーマの汎用キャッシュディレクトリ
if ( !function_exists( 'get_theme_cache_dir' ) ):
function get_theme_cache_dir(){
  $dir = get_theme_resources_dir().'cache/';
  if (!file_exists($dir)) mkdir($dir, 0777, true);
  return $dir;
}
endif;

//テーマのブログカードキャッシュディレクトリ
if ( !function_exists( 'get_theme_blog_card_cache_dir' ) ):
function get_theme_blog_card_cache_dir(){
  $dir = get_theme_resources_dir().'blog-card-cache/';
  if (!file_exists($dir)) mkdir($dir, 0777, true);
  return $dir;
}
endif;

//テーマのCSSキャッシュディレクトリ
if ( !function_exists( 'get_theme_css_cache_dir' ) ):
function get_theme_css_cache_dir(){
  $dir = get_theme_resources_dir().'css-cache/';
  if (!file_exists($dir)) mkdir($dir, 0777, true);
  return $dir;
}
endif;

//テーマのカスタムCSSファイル
if ( !function_exists( 'get_theme_css_cache_file' ) ):
function get_theme_css_cache_file(){
  $file = get_theme_css_cache_dir().'css-custom.css';
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



//画像URLから幅と高さを取得する（同サーバー内ファイルURLのみ）
if ( !function_exists( 'get_image_width_and_height' ) ):
function get_image_width_and_height($image_url){
  //URLにサイトアドレスが含まれていない場合
  if (!includes_site_url($image_url)) {
    return false;
  }
  // $wp_upload_dir = wp_upload_dir();
  // $uploads_dir = $wp_upload_dir['basedir'];
  // $uploads_url = $wp_upload_dir['baseurl'];
  // $image_file = str_replace($uploads_url, $uploads_dir, $image_url);
  $image_file = url_to_local($image_url);
  //_v($image_file);
  if (file_exists($image_file)) {
    $imagesize = getimagesize($image_file);
    if ($imagesize) {
      $res = array();
      $res['width'] = $imagesize[0];
      $res['height'] = $imagesize[1];
      //_v($res);
      return $res;
    }
  }
}
endif;

//テーマ設定ページか
if ( !function_exists( 'is_admin_php_page' ) ):
function is_admin_php_page(){
  global $pagenow;
  return $pagenow == 'admin.php';
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
    if (bp_is_my_profile()
        || bp_is_user_profile()
        // || bp_is_forums_component()
        // || bp_is_user_forums_started()
        // || bp_is_user_forums_replied_to()
        // || bp_is_group_forum()
        // || bp_is_group_forum_topic()
        // || bp_is_group_forum_topic_edit()
        // || bp_is_user_forums()
        || (is_buddypress_exist() && is_bbpress_page())
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
      // || wpforo_topic()
      // || wpforo_forum()
      // || wpforo_post()
      // || is_wpforo_shortcode_page()
      // || is_wpforo_url()
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
  return current_time('Y-m-d');
}
endif;

//アクセステーブル用の現在の日時文字列（$days日前）
if ( !function_exists( 'get_current_db_date_before' ) ):
function get_current_db_date_before($days){
  return date('Y-m-d', strtotime(current_time('Y-m-d').' -'.$days.' day'));
}
endif;

//ユーザーが管理者か
if ( !function_exists( 'is_user_administrator' ) ):
function is_user_administrator(){
  return current_user_can( 'administrator' );
}
endif;

//ファイル内容の取得
if ( !function_exists( 'wp_filesystem_get_contents' ) ):
function wp_filesystem_get_contents($file){
  $creds = false;
  if (is_request_filesystem_credentials_enable())
    $creds = request_filesystem_credentials('', '', false, false, null);

  if (WP_Filesystem($creds)) {//WP_Filesystemの初期化
    global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し
    $contents = $wp_filesystem->get_contents($file);
    return $contents;
  }
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
  $creds = false;
  if (is_request_filesystem_credentials_enable())
    $creds = request_filesystem_credentials('', '', false, false, null);

  if (WP_Filesystem($creds)) {//WP_Filesystemの初期化
    global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し
    //$wp_filesystemオブジェクトのメソッドとしてファイルに書き込む
    $wp_filesystem->put_contents($new_file, $file_data, $chmod);
  }
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
    $wp_filesystem->delete($file);
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

//現在表示しているページのURL（クエリを取り除く）
if ( !function_exists( 'get_query_removed_requested_url' ) ):
function get_query_removed_requested_url(){
  $url = get_requested_url();
  $url = preg_replace('{\?.+$}', '', $url);
  $url = trailingslashit($url);
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