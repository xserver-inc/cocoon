<?php //AMP設定用関数

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

//AMP除外カテゴリ
define('OP_AMP_EXCLUDE_CATEGORY_IDS', 'amp_exclude_category_ids');
if ( !function_exists( 'get_amp_exclude_category_ids' ) ):
function get_amp_exclude_category_ids(){
  return get_theme_option(OP_AMP_EXCLUDE_CATEGORY_IDS, array());
}
endif;


