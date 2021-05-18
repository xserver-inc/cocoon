<?php //画像設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//アイキャッチの表示
define('OP_EYECATCH_VISIBLE', 'eyecatch_visible');
if ( !function_exists( 'is_eyecatch_visible' ) ):
function is_eyecatch_visible(){
  return get_theme_option(OP_EYECATCH_VISIBLE, 1);
}
endif;

//アイキャッチラベルの表示
define('OP_EYECATCH_LABEL_VISIBLE', 'eyecatch_label_visible');
if ( !function_exists( 'is_eyecatch_label_visible' ) ):
function is_eyecatch_label_visible(){
  return get_theme_option(OP_EYECATCH_LABEL_VISIBLE, 1);
}
endif;

//アイキャッチの中央寄せ
define('OP_EYECATCH_CENTER_ENABLE', 'eyecatch_center_enable');
if ( !function_exists( 'is_eyecatch_center_enable' ) ):
function is_eyecatch_center_enable(){
  return get_theme_option(OP_EYECATCH_CENTER_ENABLE);
}
endif;

//アイキャッチをカラム幅に引き伸ばす
define('OP_EYECATCH_WIDTH_100_PERCENT_ENABLE', 'eyecatch_width_100_percent_enable');
if ( !function_exists( 'is_eyecatch_width_100_percent_enable' ) ):
function is_eyecatch_width_100_percent_enable(){
  return get_theme_option(OP_EYECATCH_WIDTH_100_PERCENT_ENABLE);
}
endif;

//アイキャッチキャプションを表示する
define('OP_EYECATCH_CAPTION_VISIBLE', 'eyecatch_caption_visible');
if ( !function_exists( 'is_eyecatch_caption_visible' ) ):
function is_eyecatch_caption_visible(){
  return get_theme_option(OP_EYECATCH_CAPTION_VISIBLE, 1);
}
endif;

//Auto Post Thumbnail
define('OP_AUTO_POST_THUMBNAIL_ENABLE', 'auto_post_thumbnail_enable');
if ( !function_exists( 'is_auto_post_thumbnail_enable' ) ):
function is_auto_post_thumbnail_enable(){
  return get_theme_option(OP_AUTO_POST_THUMBNAIL_ENABLE);
}
endif;

//画像の枠線効果
define('OP_IMAGE_WRAP_EFFECT', 'image_wrap_effect');
if ( !function_exists( 'get_image_wrap_effect' ) ):
function get_image_wrap_effect(){
  return get_theme_option(OP_IMAGE_WRAP_EFFECT, 'none');
}
endif;

//画像の拡大効果
define('OP_IMAGE_ZOOM_EFFECT', 'image_zoom_effect');
if ( !function_exists( 'get_image_zoom_effect' ) ):
function get_image_zoom_effect(){
  return get_theme_option(OP_IMAGE_ZOOM_EFFECT, 'baguettebox');
}
endif;

//Lightboxが有効
if ( !function_exists( 'is_lightbox_effect_enable' ) ):
function is_lightbox_effect_enable(){
  return get_image_zoom_effect() == 'lightbox';
}
endif;
//lityが有効
if ( !function_exists( 'is_lity_effect_enable' ) ):
function is_lity_effect_enable(){
  return get_image_zoom_effect() == 'lity';
}
endif;
//baguetteboxが有効
if ( !function_exists( 'is_baguettebox_effect_enable' ) ):
function is_baguettebox_effect_enable(){
  return get_image_zoom_effect() == 'baguettebox';
}
endif;
//Spotlightが有効
if ( !function_exists( 'is_spotlight_effect_enable' ) ):
function is_spotlight_effect_enable(){
  return get_image_zoom_effect() == 'spotlight';
}
endif;

//本文中画像の中央寄せ
define('OP_CONTENT_IMAGE_CENTER_ENABLE', 'content_image_center_enable');
if ( !function_exists( 'is_content_image_center_enable' ) ):
function is_content_image_center_enable(){
  return get_theme_option(OP_CONTENT_IMAGE_CENTER_ENABLE);
}
endif;

//サムネイル画像タイプ
define('OP_THUMBNAIL_IMAGE_TYPE', 'thumbnail_image_type');
if ( !function_exists( 'get_thumbnail_image_type' ) ):
function get_thumbnail_image_type(){
  return get_theme_option(OP_THUMBNAIL_IMAGE_TYPE, 'wide');
}
endif;
//アスペクト比の取得
if ( !function_exists( 'get_thumbnail_aspect_ratio' ) ):
function get_thumbnail_aspect_ratio(){
  switch (get_thumbnail_image_type()) {
    case 'golden_ratio':
      $ratio = 1/((1 + sqrt(5)) / 2);
      break;
    case 'silver_ratio':
      $ratio = 1/sqrt(2);
      break;
      case 'postcard':
        $ratio = 2/3;
        break;
    case 'standard':
      $ratio = 3/4;
      break;
    case 'square':
      $ratio = 1;
      break;
    default:
      $ratio = 9/16;
      break;
  }
  return $ratio;
}
endif;
//Retinaディスプレイサイズの取得
if ( !function_exists( 'get_retina_image_size' ) ):
function get_retina_image_size($size){
  return intval($size) * 2;
}
endif;
//サムネイルの横の画像サイズ
if ( !function_exists( 'get_thumbnail_width' ) ):
function get_thumbnail_width($width){
  if (is_retina_thumbnail_enable()) {
    $width = get_retina_image_size($width);
  }
  return $width;
}
endif;
//サムネイルの縦の画像サイズ
if ( !function_exists( 'get_thumbnail_height' ) ):
function get_thumbnail_height($width){
  $height = round($width * get_thumbnail_aspect_ratio());
  return $height;
}
endif;
//正方形サムネイルの横の画像サイズ
if ( !function_exists( 'get_square_thumbnail_width' ) ):
function get_square_thumbnail_width($width){
  if (is_retina_thumbnail_enable()) {
    $width = get_retina_image_size($width);
  }
  return $width;
}
endif;
//正方形サムネイルの縦の画像サイズ
if ( !function_exists( 'get_square_thumbnail_height' ) ):
function get_square_thumbnail_height($width){
  $height = $width;
  return $height;
}
endif;

//Retinaディスプレイ
define('OP_RETINA_THUMBNAIL_ENABLE', 'retina_thumbnail_enable');
if ( !function_exists( 'is_retina_thumbnail_enable' ) ):
function is_retina_thumbnail_enable(){
  return get_theme_option(OP_RETINA_THUMBNAIL_ENABLE);
}
endif;

//NO IMAGE画像
define('OP_NO_IMAGE_URL', 'no_image_url');
if ( !function_exists( 'get_no_image_url' ) ):
function get_no_image_url(){
  return get_theme_option(OP_NO_IMAGE_URL);
}
endif;
if ( !function_exists( 'get_no_image_file' ) ):
function get_no_image_file(){
  return url_to_local(get_no_image_url());
}
endif;
if ( !function_exists( 'get_image_sized_url' ) ):
function get_image_sized_url($url, $w, $h){
  $ext = get_extention($url);
  $sized_url = str_replace('.'.$ext, '-'.$w.'x'.$h.'.'.$ext, $url);
  return $sized_url;
}
endif;
if ( !function_exists( 'get_no_image_large_url' ) ):
function get_no_image_large_url($id = null){
  if ($no_image_url = get_no_image_url()) {
    $res = $no_image_url;
  } else {
    $res = NO_IMAGE_LARGE;
  }
  return apply_filters('get_sized_no_image_url', $res, null, null, $id);
}
endif;
if ( !function_exists( 'get_no_image_320x180_url' ) ):
function get_no_image_320x180_url($id = null){
  if ($no_image_url = get_no_image_url()) {
    $res = get_image_sized_url($no_image_url, THUMB320WIDTH, THUMB320HEIGHT);
  } else {
    $res = NO_IMAGE_320;
  }
  return apply_filters('get_sized_no_image_url', $res, THUMB320WIDTH, THUMB320HEIGHT, $id);
}
endif;
if ( !function_exists( 'get_no_image_320x180_file' ) ):
function get_no_image_320x180_file(){
  return url_to_local(get_no_image_320x180_url());
}
endif;
if ( !function_exists( 'get_no_image_160x90_url' ) ):
function get_no_image_160x90_url($id = null){
  if ($no_image_url = get_no_image_url()) {
    $res = get_image_sized_url(get_no_image_url(), THUMB160WIDTH, THUMB160HEIGHT);
  } else {
    $res = NO_IMAGE_160;
  }
  return apply_filters('get_sized_no_image_url', $res, THUMB160WIDTH, THUMB160HEIGHT, $id);
}
endif;
if ( !function_exists( 'get_no_image_160x90_file' ) ):
function get_no_image_160x90_file(){
  return url_to_local(get_no_image_160x90_url());
}
endif;
if ( !function_exists( 'get_no_image_120x68_url' ) ):
function get_no_image_120x68_url($id = null){
  if ($no_image_url = get_no_image_url()) {
    $res = get_image_sized_url($no_image_url, THUMB120WIDTH, THUMB120HEIGHT);
  } else {
    $res = NO_IMAGE_120;
  }
  return apply_filters('get_sized_no_image_url', $res, THUMB120WIDTH, THUMB120HEIGHT, $id);
}
endif;
if ( !function_exists( 'get_no_image_120x68_file' ) ):
function get_no_image_120x68_file(){
  return url_to_local(get_no_image_120x68_url());
}
endif;
if ( !function_exists( 'get_no_image_150x150_url' ) ):
function get_no_image_150x150_url($id = null){
  if ($no_image_url = get_no_image_url()) {
    $res = get_image_sized_url($no_image_url, THUMB150WIDTH, THUMB150HEIGHT);
  } else {
    $res = NO_IMAGE_150;
  }
  return apply_filters('get_sized_no_image_url', $res, THUMB150WIDTH, THUMB150HEIGHT, $id);
}
endif;
if ( !function_exists( 'get_no_image_150x150_file' ) ):
function get_no_image_150x150_file(){
  return url_to_local(get_no_image_150x150_url());
}
endif;

//オリジナルサムネイルタグの取得
if ( !function_exists( 'get_original_image_tag' ) ):
function get_original_image_tag($image_url, $width, $height, $class, $alt = null){
  $html = '<img src="'.esc_url($image_url).'" alt="'.esc_attr($alt).'" class="'.esc_attr($class).'" width="'.esc_attr($width).'" height="'.esc_attr($height).'" />';
  $html = convert_all_lazy_load_tag($html);
  return $html;
}
endif;

//アイキャッチ画像が存在しない場合はカテゴリーの画像を利用
//画像サイズが存在しない場合は、[large]画像を使用
add_filter('get_sized_no_image_url', 'get_categorized_no_image_url', 10, 4);
if ( !function_exists( 'get_categorized_no_image_url' ) ):
function get_categorized_no_image_url($url, $width = null, $height = null, $id = null){
  if (!$id) {
    $id = get_the_ID();
  }
  //サイズ指定がある場合はカテゴリーURLを取得
  if ($width && $height) {
    $cat_url = null;
    $cat = get_the_category($id);

    // //カテゴリー画像取得
    if ($cat && isset($cat[0])) {
      $cat_url = get_the_category_eye_catch_url($cat[0]->cat_ID);
      if ($cat_url) {
        $url = get_image_sized_url($cat_url, $width, $height);
      }
    }

    //メインカテゴリーが設定してある場合
    // _v('id='.$id);
    $main_cat_id = get_the_page_main_category($id);
    // _v('mid='.$main_cat_id);
    if ($main_cat_id && in_category($main_cat_id, $id)) {
      $cat_url = get_the_category_eye_catch_url($main_cat_id);
      if ($cat_url) {
        $url = get_image_sized_url($cat_url, $width, $height);
      } else {
        switch ($width) {
          case 120:
            $url = NO_IMAGE_120;
            break;
          case 150:
            $url = NO_IMAGE_150;
            break;
          case 160:
            $url = NO_IMAGE_160;
            break;
          case 320:
            $url = NO_IMAGE_320;
            break;
          default:
            $url = NO_IMAGE_LARGE;
            break;
        }
      }
    }

  }
  return $url;
}
endif;
