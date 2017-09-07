<?php //広告設定をデータベースに保存

//オプションの値をデータベースに保存する
if ( !function_exists( 'update_theme_option' ) ):
function update_theme_option($option_name){
  $opt_val = isset($_POST[$option_name]) ? $_POST[$option_name] : null;
  update_option($option_name, $opt_val);
}
endif;

//広告を全て表示するか
define('OP_ALL_ADS_VISIBLE', 'all_ads_visible');
if ( !function_exists( 'is_all_ads_visible' ) ):
function is_all_ads_visible(){
  return get_option(OP_ALL_ADS_VISIBLE);
}
endif;

//広告コード
define('OP_AD_CODE', 'ad_code');
if ( !function_exists( 'get_ad_code' ) ):
function get_ad_code(){
  return stripslashes_deep(get_option(OP_AD_CODE));
}
endif;

//広告ラベル
define('OP_AD_LABEL', 'ad_label');
if ( !function_exists( 'get_ad_label' ) ):
function get_ad_label(){
  $opt_val = get_option(OP_AD_LABEL);
  return $opt_val ? $opt_val : __( 'スポンサーリンク', THEME_NAME ) ;
}
endif;

//広告除外記事ID
define('OP_AD_EXCLUDE_POST_IDS', 'ad_exclude_post_ids');
if ( !function_exists( 'get_ad_exclude_post_ids' ) ):
function get_ad_exclude_post_ids(){
  return get_option(OP_AD_EXCLUDE_POST_IDS);
}
endif;

//広告除外カテゴリーID
define('OP_AD_EXCLUDE_CATEGORY_IDS', 'ad_exclude_category_ids');
if ( !function_exists( 'get_ad_exclude_category_ids' ) ):
function get_ad_exclude_category_ids(){
  return get_option(OP_AD_EXCLUDE_CATEGORY_IDS);
}
endif;

