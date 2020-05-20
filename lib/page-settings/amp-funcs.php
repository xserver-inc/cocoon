<?php //AMP設定用関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//AMPの有効化
define('OP_AMP_ENABLE', 'amp_enable');
if ( !function_exists( 'is_amp_enable' ) ):
function is_amp_enable(){
  return get_theme_option(OP_AMP_ENABLE);
}
endif;

//AMPロゴ
define('OP_AMP_LOGO_IMAGE_URL', 'amp_logo_image_url');
if ( !function_exists( 'get_amp_logo_image_url' ) ):
function get_amp_logo_image_url(){
  return get_theme_option(OP_AMP_LOGO_IMAGE_URL, get_template_directory_uri().'/images/no-amp-logo.png');
}
endif;

//AMP画像の拡大効果
define('OP_AMP_IMAGE_ZOOM_EFFECT', 'amp_image_zoom_effect');
if ( !function_exists( 'get_amp_image_zoom_effect' ) ):
function get_amp_image_zoom_effect(){
  return get_theme_option(OP_AMP_IMAGE_ZOOM_EFFECT, 'amp-lightbox-gallery');
}
endif;
if ( !function_exists( 'is_amp_image_zoom_effect_none' ) ):
function is_amp_image_zoom_effect_none(){
  return get_amp_image_zoom_effect() == 'none';
}
endif;
if ( !function_exists( 'is_amp_image_zoom_effect_lightbox' ) ):
function is_amp_image_zoom_effect_lightbox(){
  return get_amp_image_zoom_effect() == 'amp-image-lightbox';
}
endif;
if ( !function_exists( 'is_amp_image_zoom_effect_gallery' ) ):
function is_amp_image_zoom_effect_gallery(){
  return get_amp_image_zoom_effect() == 'amp-lightbox-gallery';
}
endif;

//AMPバリデーションツール
define('AVT_AMP_TEST', 'https://search.google.com/test/amp');
define('AVT_THE_AMP_VALIDATOR', 'https://validator.ampproject.org/#');
define('AVT_THE_AMP_BENCH', 'https://ampbench.appspot.com/');

//AAMPバリデーションツール
define('OP_AMP_VALIDATOR', 'amp_validator');
if ( !function_exists( 'get_amp_validator' ) ):
function get_amp_validator(){
  return get_theme_option(OP_AMP_VALIDATOR, AVT_AMP_TEST);
}
endif;

//インラインスタイルを取り除く
define('OP_AMP_REMOVAL_INLINE_STYLE_ENABLE', 'amp_removal_inline_style_enable');
if ( !function_exists( 'is_amp_removal_inline_style_enable' ) ):
function is_amp_removal_inline_style_enable(){
  return get_theme_option(OP_AMP_REMOVAL_INLINE_STYLE_ENABLE, 1);
}
endif;

//インラインスタイル
define('OP_AMP_INLINE_STYLE_ENABLE', 'amp_inline_style_enable');
if ( !function_exists( 'is_amp_inline_style_enable' ) ):
function is_amp_inline_style_enable(){
  return get_theme_option(OP_AMP_INLINE_STYLE_ENABLE);
}
endif;

//スキンスタイルを有効
define('OP_AMP_SKIN_STYLE_ENABLE', 'amp_skin_style_enable');
if ( !function_exists( 'is_amp_skin_style_enable' ) ):
function is_amp_skin_style_enable(){
  return get_theme_option(OP_AMP_SKIN_STYLE_ENABLE, 1);
}
endif;

//子テーマスタイルを有効
define('OP_AMP_CHILD_THEME_STYLE_ENABLE', 'amp_child_theme_style_enable');
if ( !function_exists( 'is_amp_child_theme_style_enable' ) ):
function is_amp_child_theme_style_enable(){
  return get_theme_option(OP_AMP_CHILD_THEME_STYLE_ENABLE, 1);
}
endif;

//AMP除外カテゴリ
define('OP_AMP_EXCLUDE_CATEGORY_IDS', 'amp_exclude_category_ids');
if ( !function_exists( 'get_amp_exclude_category_ids' ) ):
function get_amp_exclude_category_ids(){
  return get_theme_option(OP_AMP_EXCLUDE_CATEGORY_IDS, array());
}
endif;


