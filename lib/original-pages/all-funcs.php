<?php //全体設定に必要な定数や関数

//サイトキーカラー
define('OP_SITE_KEY_COLOR', 'site_key_color');
if ( !function_exists( 'get_site_key_color' ) ):
function get_site_key_color(){
  return get_option(OP_SITE_KEY_COLOR);
}
endif;

//サイトキーテキストカラー
define('OP_SITE_KEY_TEXT_COLOR', 'site_key_text_color');
if ( !function_exists( 'get_site_key_text_color' ) ):
function get_site_key_text_color(){
  return get_option(OP_SITE_KEY_TEXT_COLOR);
}
endif;

//フォント
define('OP_SITE_FONT_FAMILY', 'site_font_family');
if ( !function_exists( 'get_site_font_family' ) ):
function get_site_font_family(){
  return get_option(OP_SITE_FONT_FAMILY, 'yu_gothic');
}
endif;
if ( !function_exists( 'is_site_font_family_local' ) ):
function is_site_font_family_local(){
  switch (get_site_font_family()) {
    case 'yu_gothic':
    case 'meiryo':
    case 'ms_pgothic':
      return true;
      break;
  }
}
endif;

//フォントサイズ
define('OP_SITE_FONT_SIZE', 'site_font_size');
if ( !function_exists( 'get_site_font_size' ) ):
function get_site_font_size(){
  return get_option(OP_SITE_FONT_SIZE, '18px');
}
endif;

//サイト背景色
define('OP_SITE_BACKGROUND_COLOR', 'site_background_color');
if ( !function_exists( 'get_site_background_color' ) ):
function get_site_background_color(){
  return get_option(OP_SITE_BACKGROUND_COLOR);
}
endif;

//サイト背景画像
define('OP_SITE_BACKGROUND_IMAGE_URL', 'site_background_image_url');
if ( !function_exists( 'get_site_background_image_url' ) ):
function get_site_background_image_url(){
  return get_option(OP_SITE_BACKGROUND_IMAGE_URL);
}
endif;

//サイトリンク色
define('OP_SITE_LINK_COLOR', 'site_link_color');
if ( !function_exists( 'get_site_link_color' ) ):
function get_site_link_color(){
  return get_option(OP_SITE_LINK_COLOR);
}
endif;

//サイト幅を揃える
define('OP_ALIGN_SITE_WIDTH', 'align_site_width');
if ( !function_exists( 'is_align_site_width' ) ):
function is_align_site_width(){
  return get_option(OP_ALIGN_SITE_WIDTH);
}
endif;

//サイドバーの表示タイプ
define('OP_SIDEBAR_POSITION', 'sidebar_position');
if ( !function_exists( 'get_sidebar_position' ) ):
function get_sidebar_position(){
  return get_option(OP_SIDEBAR_POSITION, 'sidebar_right');
}
endif;

//サイドバーの表示状態の設定
define('OP_SIDEBAR_DISPLAY_TYPE', 'sidebar_display_type');
if ( !function_exists( 'get_sidebar_display_type' ) ):
function get_sidebar_display_type(){
  return get_option(OP_SIDEBAR_DISPLAY_TYPE, 'display_all');
}
endif;

//サイトアイコン
define('OP_SITE_ICON_URL', 'site_icon_url');
//Wordpressデフォルトのget_site_icon_url関数とかぶるため名前変更
if ( !function_exists( 'get_site_icon_url2' ) ):
function get_site_icon_url2(){
  return get_option(OP_SITE_ICON_URL, get_default_site_icon_url());
}
endif;
if ( !function_exists( 'get_default_site_icon_url' ) ):
function get_default_site_icon_url(){
  return get_template_directory_uri().'/images/site-icon.png';
}
endif;

//noindexページを出力する
add_action( 'wp_head', 'the_site_icon_tag' );
add_action( 'admin_print_styles', 'the_site_icon_tag' );
if ( !function_exists( 'the_site_icon_tag' ) ):
function the_site_icon_tag(){
  $tag = null;
  if (get_site_icon_url2()) {
    $tag .= '<link rel="shortcut icon" href="'.get_site_icon_url2().'">'.PHP_EOL;
    $tag .= '<link rel="apple-touch-icon" href="'.get_site_icon_url2().'">'.PHP_EOL;
  } elseif (is_singular()) {
    $tag = '<link rel="shortcut icon" href="'.get_default_site_icon_url().'">'.PHP_EOL;
    $tag = '<link rel="apple-touch-icon" href="'.get_default_site_icon_url().'">'.PHP_EOL;
  }
  if ($tag) {
    $tag = '<!-- '.THEME_NAME_CAMEL.' site icon -->'.PHP_EOL.$tag;
    echo $tag;
  }
}
endif;



