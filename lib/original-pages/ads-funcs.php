<?php //広告設定に必要な定数や関数

//オプションの値をデータベースに保存する
if ( !function_exists( 'update_theme_option' ) ):
function update_theme_option($option_name){
  $opt_val = isset($_POST[$option_name]) ? $_POST[$option_name] : null;
  update_option($option_name, $opt_val);
}
endif;

//チェックボックスのチェックを付けるか
if ( !function_exists( 'the_checkbox_checked' ) ):
function the_checkbox_checked($value){
  if ($value) {
    echo ' checked="checked"';
  }
}
endif;



//広告を全て表示するか
define('OP_ALL_ADS_VISIBLE', 'all_ads_visible');
if ( !function_exists( 'is_all_ads_visible' ) ):
function is_all_ads_visible(){
  return get_option(OP_ALL_ADS_VISIBLE, 1);
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
  return get_option(OP_AD_LABEL, __( 'スポンサーリンク', THEME_NAME ));
}
endif;

//インデックストップの広告表示
define('OP_AD_POS_INDEX_TOP_VISIBLE', 'ad_pos_index_top_visible');
if ( !function_exists( 'is_ad_pos_index_top_visible' ) ):
function is_ad_pos_index_top_visible(){
  return get_option(OP_AD_POS_INDEX_TOP_VISIBLE, 1);
}
endif;

//インデックスミドルの広告表示
define('OP_AD_POS_INDEX_MIDDLE_VISIBLE', 'ad_pos_index_middle_visible');
if ( !function_exists( 'is_ad_pos_index_middle_visible' ) ):
function is_ad_pos_index_middle_visible(){
  return get_option(OP_AD_POS_INDEX_MIDDLE_VISIBLE);
}
endif;

//インデックスボトムの広告表示
define('OP_AD_POS_INDEX_BOTTOM_VISIBLE', 'ad_pos_index_bottom_visible');
if ( !function_exists( 'is_ad_pos_index_bottom_visible' ) ):
function is_ad_pos_index_bottom_visible(){
  return get_option(OP_AD_POS_INDEX_BOTTOM_VISIBLE);
}
endif;

//インデックスサイドバー上の広告表示
define('OP_AD_POS_INDEX_SIDEBAR_TOP_VISIBLE', 'ad_pos_index_sidebar_top_visible');
if ( !function_exists( 'is_ad_pos_index_sidebar_top_visible' ) ):
function is_ad_pos_index_sidebar_top_visible(){
  return get_option(OP_AD_POS_INDEX_SIDEBAR_TOP_VISIBLE, 1);
}
endif;

//インデックスサイドバー下の広告表示
define('OP_AD_POS_INDEX_SIDEBAR_BOTTOM_VISIBLE', 'ad_pos_index_sidebar_bottom_visible');
if ( !function_exists( 'is_ad_pos_index_sidebar_bottom_visible' ) ):
function is_ad_pos_index_sidebar_bottom_visible(){
  return get_option(OP_AD_POS_INDEX_SIDEBAR_BOTTOM_VISIBLE);
}
endif;

//投稿・固定ページタイトル上の広告表示
define('OP_AD_POS_ABOVE_TITLE_VISIBLE', 'ad_pos_above_title_visible');
if ( !function_exists( 'is_ad_pos_above_title_visible' ) ):
function is_ad_pos_above_title_visible(){
  return get_option(OP_AD_POS_ABOVE_TITLE_VISIBLE);
}
endif;

//投稿・固定ページ本文上の広告表示
define('OP_AD_POS_CONTENT_TOP_VISIBLE', 'ad_pos_content_top_visible');
if ( !function_exists( 'is_ad_pos_content_top_visible' ) ):
function is_ad_pos_CONTENT_TOP_visible(){
  return get_option(OP_AD_POS_CONTENT_TOP_VISIBLE);
}
endif;

//投稿・固定ページ本文中の広告表示
define('OP_AD_POS_CONTENT_MIDDLE_VISIBLE', 'ad_pos_content_middle_visible');
if ( !function_exists( 'is_ad_pos_content_middle_visible' ) ):
function is_ad_pos_content_middle_visible(){
  return get_option(OP_AD_POS_CONTENT_MIDDLE_VISIBLE);
}
endif;

//投稿・固定ページ本文下の広告表示
define('OP_AD_POS_CONTENT_BOTTOM_VISIBLE', 'ad_pos_content_bottom_visible');
if ( !function_exists( 'is_ad_pos_content_bottom_visible' ) ):
function is_ad_pos_content_bottom_visible(){
  return get_option(OP_AD_POS_CONTENT_BOTTOM_VISIBLE, 1);
}
endif;

//投稿・固定ページSNSボタン上の広告表示
define('OP_AD_POS_ABOVE_SNS_BUTTONS_VISIBLE', 'ad_pos_above_sns_buttons_visible');
if ( !function_exists( 'is_ad_pos_above_sns_buttons_visible' ) ):
function is_ad_pos_above_sns_buttons_visible(){
  return get_option(OP_AD_POS_ABOVE_SNS_BUTTONS_VISIBLE);
}
endif;

//投稿・固定ページSNSボタン下の広告表示
define('OP_AD_POS_BELOW_SNS_BUTTONS_VISIBLE', 'ad_pos_below_sns_buttons_visible');
if ( !function_exists( 'is_ad_pos_below_sns_buttons_visible' ) ):
function is_ad_pos_below_sns_buttons_visible(){
  return get_option(OP_AD_POS_BELOW_SNS_BUTTONS_VISIBLE);
}
endif;

//投稿関連記事下の広告表示
define('OP_AD_POS_BELOW_RELATED_POSTS_VISIBLE', 'ad_pos_below_related_posts_visible');
if ( !function_exists( 'is_ad_pos_below_related_posts_visible' ) ):
function is_ad_pos_below_related_posts_visible(){
  return get_option(OP_AD_POS_BELOW_RELATED_POSTS_VISIBLE, 1);
}
endif;

// //投稿・固定ページサイドバートップの広告表示
// define('OP_AD_POS_SIDEBAR_TOP_VISIBLE', 'ad_pos_sidebar_top_visible');
// if ( !function_exists( 'is_ad_pos_sidebar_top_visible' ) ):
// function is_ad_pos_sidebar_top_visible(){
//   return get_option(OP_AD_POS_SIDEBAR_TOP_VISIBLE, 1);
// }
// endif;

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

