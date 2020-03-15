<?php //広告設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//広告を全て表示するか
define('OP_ALL_ADS_VISIBLE', 'all_ads_visible');
if ( !function_exists( 'is_all_ads_visible' ) ):
function is_all_ads_visible(){
  return get_theme_option(OP_ALL_ADS_VISIBLE, 1);
}
endif;

//アドセンス広告を全て表示するか
define('OP_ALL_ADSENSES_VISIBLE', 'all_adsenses_visible');
if ( !function_exists( 'is_all_adsenses_visible' ) ):
function is_all_adsenses_visible(){
  return get_theme_option(OP_ALL_ADSENSES_VISIBLE, 1);
}
endif;

//広告コード
define('OP_AD_CODE', 'ad_code');
if ( !function_exists( 'get_ad_code' ) ):
function get_ad_code(){
  return stripslashes_deep(get_theme_option(OP_AD_CODE));
}
endif;

//広告リンクユニットコード
define('OP_AD_LINK_UNIT_CODE', 'ad_link_unit_code');
if ( !function_exists( 'get_ad_link_unit_code' ) ):
function get_ad_link_unit_code(){
  return stripslashes_deep(get_theme_option(OP_AD_LINK_UNIT_CODE));
}
endif;

//関連コンテンツユニットコード
define('OP_AD_RELATED_CONTENTS_UNIT_CODE', 'ad_related_contents_unit_code');
if ( !function_exists( 'get_ad_related_contents_unit_code' ) ):
function get_ad_related_contents_unit_code(){
  return stripslashes_deep(get_theme_option(OP_AD_RELATED_CONTENTS_UNIT_CODE));
}
endif;

//広告ラベル
define('OP_AD_LABEL_CAPTION', 'ad_label_caption');
if ( !function_exists( 'get_ad_label_caption' ) ):
function get_ad_label_caption(){
  return get_theme_option(OP_AD_LABEL_CAPTION, __( 'スポンサーリンク', THEME_NAME ));
}
endif;

//アドセンス表示方式
define('OP_ADSENSE_DISPLAY_METHOD', 'adsense_display_method');
if ( !function_exists( 'get_adsense_display_method' ) ):
function get_adsense_display_method(){
  $display_method = get_theme_option(OP_ADSENSE_DISPLAY_METHOD, 'by_myself');
  $display_method = $display_method ? $display_method : 'by_myself';
  return $display_method;
}
endif;

//アドセンス表示方式は「自動広告のみ」か
if ( !function_exists( 'is_adsense_display_method_by_auto' ) ):
function is_adsense_display_method_by_auto(){
  return get_adsense_display_method() == 'by_auto';
}
endif;
if ( !function_exists( 'is_auto_adsens_only_enable' ) ):
function is_auto_adsens_only_enable(){
  return is_adsense_display_method_by_auto();
}
endif;

//アドセンス表示方式は「自動とマニュアルの併用」か
if ( !function_exists( 'is_adsense_display_method_by_auto_and_myself' ) ):
function is_adsense_display_method_by_auto_and_myself(){
  return get_adsense_display_method() == 'by_auto_and_myself';
}
endif;

//アドセンス表示方式は「マニュアルのみ」か
if ( !function_exists( 'is_adsense_display_method_by_myself' ) ):
function is_adsense_display_method_by_myself(){
  return get_adsense_display_method() == 'by_myself';
}
endif;

//自動AdSenseコードを有効にする
if ( !function_exists( 'is_auto_adsense_enable' ) ):
function is_auto_adsense_enable(){
  return is_adsense_display_method_by_auto() || is_adsense_display_method_by_auto_and_myself();
}
endif;

//インデックストップの広告表示
define('OP_AD_POS_INDEX_TOP_VISIBLE', 'ad_pos_index_top_visible');
if ( !function_exists( 'is_ad_pos_index_top_visible' ) ):
function is_ad_pos_index_top_visible(){
  return get_theme_option(OP_AD_POS_INDEX_TOP_VISIBLE, 1);
}
endif;

//インデックストップの広告フォーマット
define('OP_AD_POS_INDEX_TOP_FORMAT', 'ad_pos_index_top_format');
if ( !function_exists( 'get_ad_pos_index_top_format' ) ):
function get_ad_pos_index_top_format(){
  return get_theme_option(OP_AD_POS_INDEX_TOP_FORMAT, DATA_AD_FORMAT_HORIZONTAL);
}
endif;

//インデックストップの広告ラベル表示
define('OP_AD_POS_INDEX_TOP_LABEL_VISIBLE', 'ad_pos_index_top_label_visible');
if ( !function_exists( 'is_ad_pos_index_top_label_visible' ) ):
function is_ad_pos_index_top_label_visible(){
  return get_theme_option(OP_AD_POS_INDEX_TOP_LABEL_VISIBLE);
}
endif;

//インデックスミドルの広告表示
define('OP_AD_POS_INDEX_MIDDLE_VISIBLE', 'ad_pos_index_middle_visible');
if ( !function_exists( 'is_ad_pos_index_middle_visible' ) ):
function is_ad_pos_index_middle_visible(){
  return get_theme_option(OP_AD_POS_INDEX_MIDDLE_VISIBLE);
}
endif;

//インデックスミドルの広告フォーマット
define('OP_AD_POS_INDEX_MIDDLE_FORMAT', 'ad_pos_index_middle_format');
if ( !function_exists( 'get_ad_pos_index_middle_format' ) ):
function get_ad_pos_index_middle_format(){
  return get_theme_option(OP_AD_POS_INDEX_MIDDLE_FORMAT, DATA_AD_FORMAT_RECTANGLE);
}
endif;

//インデックスミドルの広告ラベル表示
define('OP_AD_POS_INDEX_MIDDLE_LABEL_VISIBLE', 'ad_pos_index_middle_label_visible');
if ( !function_exists( 'is_ad_pos_index_middle_label_visible' ) ):
function is_ad_pos_index_middle_label_visible(){
  return get_theme_option(OP_AD_POS_INDEX_MIDDLE_LABEL_VISIBLE, 1);
}
endif;

//インデックスボトムの広告表示
define('OP_AD_POS_INDEX_BOTTOM_VISIBLE', 'ad_pos_index_bottom_visible');
if ( !function_exists( 'is_ad_pos_index_bottom_visible' ) ):
function is_ad_pos_index_bottom_visible(){
  return get_theme_option(OP_AD_POS_INDEX_BOTTOM_VISIBLE, 1);
}
endif;

//インデックスボトムの広告フォーマット
define('OP_AD_POS_INDEX_BOTTOM_FORMAT', 'ad_pos_index_bottom_format');
if ( !function_exists( 'get_ad_pos_index_bottom_format' ) ):
function get_ad_pos_index_bottom_format(){
  return get_theme_option(OP_AD_POS_INDEX_BOTTOM_FORMAT, DATA_AD_FORMAT_RECTANGLE);
}
endif;

//インデックスボトムの広告ラベル表示
define('OP_AD_POS_INDEX_BOTTOM_LABEL_VISIBLE', 'ad_pos_index_bottom_label_visible');
if ( !function_exists( 'is_ad_pos_index_bottom_label_visible' ) ):
function is_ad_pos_index_bottom_label_visible(){
  return get_theme_option(OP_AD_POS_INDEX_BOTTOM_LABEL_VISIBLE, 1);
}
endif;

//サイドバー上の広告表示
define('OP_AD_POS_SIDEBAR_TOP_VISIBLE', 'ad_pos_sidebar_top_visible');
if ( !function_exists( 'is_ad_pos_sidebar_top_visible' ) ):
function is_ad_pos_sidebar_top_visible(){
  return get_theme_option(OP_AD_POS_SIDEBAR_TOP_VISIBLE, 1);
}
endif;

//サイドバー上の広告フォーマット
define('OP_AD_POS_SIDEBAR_TOP_FORMAT', 'ad_pos_sidebar_top_format');
if ( !function_exists( 'get_ad_pos_sidebar_top_format' ) ):
function get_ad_pos_sidebar_top_format(){
  return get_theme_option(OP_AD_POS_SIDEBAR_TOP_FORMAT, 1);
}
endif;

//サイドバー上の広告ラベル表示
define('OP_AD_POS_SIDEBAR_TOP_LABEL_VISIBLE', 'ad_pos_sidebar_top_label_visible');
if ( !function_exists( 'is_ad_pos_sidebar_top_label_visible' ) ):
function is_ad_pos_sidebar_top_label_visible(){
  return get_theme_option(OP_AD_POS_SIDEBAR_TOP_LABEL_VISIBLE);
}
endif;

//サイドバー下の広告表示
define('OP_AD_POS_SIDEBAR_BOTTOM_VISIBLE', 'ad_pos_sidebar_bottom_visible');
if ( !function_exists( 'is_ad_pos_sidebar_bottom_visible' ) ):
function is_ad_pos_sidebar_bottom_visible(){
  return get_theme_option(OP_AD_POS_SIDEBAR_BOTTOM_VISIBLE);
}
endif;

//サイドバー下の広告フォーマット
define('OP_AD_POS_SIDEBAR_BOTTOM_FORMAT', 'ad_pos_sidebar_bottom_format');
if ( !function_exists( 'get_ad_pos_sidebar_bottom_format' ) ):
function get_ad_pos_sidebar_bottom_format(){
  return get_theme_option(OP_AD_POS_SIDEBAR_BOTTOM_FORMAT, 1);
}
endif;

//サイドバー下の広告ラベル表示
define('OP_AD_POS_SIDEBAR_BOTTOM_LABEL_VISIBLE', 'ad_pos_sidebar_bottom_label_visible');
if ( !function_exists( 'is_ad_pos_sidebar_bottom_label_visible' ) ):
function is_ad_pos_sidebar_bottom_label_visible(){
  return get_theme_option(OP_AD_POS_SIDEBAR_BOTTOM_LABEL_VISIBLE);
}
endif;

//投稿・固定ページタイトル上の広告表示
define('OP_AD_POS_ABOVE_TITLE_VISIBLE', 'ad_pos_above_title_visible');
if ( !function_exists( 'is_ad_pos_above_title_visible' ) ):
function is_ad_pos_above_title_visible(){
  return get_theme_option(OP_AD_POS_ABOVE_TITLE_VISIBLE);
}
endif;

//投稿・固定ページタイトル上の広告フォーマット
define('OP_AD_POS_ABOVE_TITLE_FORMAT', 'ad_pos_above_title_format');
if ( !function_exists( 'get_ad_pos_above_title_format' ) ):
function get_ad_pos_above_title_format(){
  return get_theme_option(OP_AD_POS_ABOVE_TITLE_FORMAT, DATA_AD_FORMAT_HORIZONTAL);
}
endif;

//投稿・固定ページタイトル上の広告ラベル表示
define('OP_AD_POS_ABOVE_TITLE_LABEL_VISIBLE', 'ad_pos_above_title_label_visible');
if ( !function_exists( 'is_ad_pos_above_title_label_visible' ) ):
function is_ad_pos_above_title_label_visible(){
  return get_theme_option(OP_AD_POS_ABOVE_TITLE_LABEL_VISIBLE);
}
endif;

//投稿・固定ページタイトル下の広告表示
define('OP_AD_POS_BELOW_TITLE_VISIBLE', 'ad_pos_below_title_visible');
if ( !function_exists( 'is_ad_pos_below_title_visible' ) ):
function is_ad_pos_below_title_visible(){
  return get_theme_option(OP_AD_POS_BELOW_TITLE_VISIBLE);
}
endif;

//投稿・固定ページタイトル下の広告フォーマット
define('OP_AD_POS_BELOW_TITLE_FORMAT', 'ad_pos_below_title_format');
if ( !function_exists( 'get_ad_pos_below_title_format' ) ):
function get_ad_pos_below_title_format(){
  return get_theme_option(OP_AD_POS_BELOW_TITLE_FORMAT, DATA_AD_FORMAT_HORIZONTAL);
}
endif;

//投稿・固定ページタイトル下の広告ラベル表示
define('OP_AD_POS_BELOW_TITLE_LABEL_VISIBLE', 'ad_pos_below_title_label_visible');
if ( !function_exists( 'is_ad_pos_below_title_label_visible' ) ):
function is_ad_pos_below_title_label_visible(){
  return get_theme_option(OP_AD_POS_BELOW_TITLE_LABEL_VISIBLE);
}
endif;

//投稿・固定ページ本文上の広告表示
define('OP_AD_POS_CONTENT_TOP_VISIBLE', 'ad_pos_content_top_visible');
if ( !function_exists( 'is_ad_pos_content_top_visible' ) ):
function is_ad_pos_content_top_visible(){
  return get_theme_option(OP_AD_POS_CONTENT_TOP_VISIBLE);
}
endif;

//投稿・固定ページ本文上の広告フォーマット
define('OP_AD_POS_CONTENT_TOP_FORMAT', 'ad_pos_content_top_format');
if ( !function_exists( 'get_ad_pos_content_top_format' ) ):
function get_ad_pos_content_top_format(){
  return get_theme_option(OP_AD_POS_CONTENT_TOP_FORMAT, DATA_AD_FORMAT_HORIZONTAL);
}
endif;

//投稿・固定ページ本文上の広告ラベル表示
define('OP_AD_POS_CONTENT_TOP_LABEL_VISIBLE', 'ad_pos_content_top_label_visible');
if ( !function_exists( 'is_ad_pos_content_top_label_visible' ) ):
function is_ad_pos_content_top_label_visible(){
  return get_theme_option(OP_AD_POS_CONTENT_TOP_LABEL_VISIBLE);
}
endif;

//投稿・固定ページ本文中の広告表示
define('OP_AD_POS_CONTENT_MIDDLE_VISIBLE', 'ad_pos_content_middle_visible');
if ( !function_exists( 'is_ad_pos_content_middle_visible' ) ):
function is_ad_pos_content_middle_visible(){
  return get_theme_option(OP_AD_POS_CONTENT_MIDDLE_VISIBLE);
}
endif;

//投稿・固定ページ本文中の広告フォーマット
define('OP_AD_POS_CONTENT_MIDDLE_FORMAT', 'ad_pos_content_middle_format');
if ( !function_exists( 'get_ad_pos_content_middle_format' ) ):
function get_ad_pos_content_middle_format(){
  return get_theme_option(OP_AD_POS_CONTENT_MIDDLE_FORMAT, DATA_AD_FORMAT_RECTANGLE);
}
endif;

//投稿・固定ページ本文中の広告ラベル表示
define('OP_AD_POS_CONTENT_MIDDLE_LABEL_VISIBLE', 'ad_pos_content_middle_label_visible');
if ( !function_exists( 'is_ad_pos_content_middle_label_visible' ) ):
function is_ad_pos_content_middle_label_visible(){
  return get_theme_option(OP_AD_POS_CONTENT_MIDDLE_LABEL_VISIBLE, 1);
}
endif;

//投稿・固定ページ本文中のH2見出し全てに広告表示
define('OP_AD_POS_ALL_CONTENT_MIDDLE_VISIBLE', 'ad_pos_all_content_middle_visible');
if ( !function_exists( 'is_ad_pos_all_content_middle_visible' ) ):
function is_ad_pos_all_content_middle_visible(){
  return get_theme_option(OP_AD_POS_ALL_CONTENT_MIDDLE_VISIBLE);
}
endif;

//投稿・固定ページ本文中の広表示数
define('OP_AD_POS_CONTENT_MIDDLE_COUNT', 'ad_pos_content_middle_count');
if ( !function_exists( 'get_ad_pos_content_middle_count' ) ):
function get_ad_pos_content_middle_count(){
  return get_theme_option(OP_AD_POS_CONTENT_MIDDLE_COUNT, -1);
}
endif;

//投稿・固定ページ本文下の広告表示
define('OP_AD_POS_CONTENT_BOTTOM_VISIBLE', 'ad_pos_content_bottom_visible');
if ( !function_exists( 'is_ad_pos_content_bottom_visible' ) ):
function is_ad_pos_content_bottom_visible(){
  return get_theme_option(OP_AD_POS_CONTENT_BOTTOM_VISIBLE, 1);
}
endif;

//投稿・固定ページ本文下の広告フォーマット
define('OP_AD_POS_CONTENT_BOTTOM_FORMAT', 'ad_pos_content_bottom_format');
if ( !function_exists( 'get_ad_pos_content_bottom_format' ) ):
function get_ad_pos_content_bottom_format(){
  return get_theme_option(OP_AD_POS_CONTENT_BOTTOM_FORMAT, DATA_AD_FORMAT_RECTANGLE);
}
endif;

//投稿・固定ページ本文下の広告ラベル表示
define('OP_AD_POS_CONTENT_BOTTOM_LABEL_VISIBLE', 'ad_pos_content_bottom_label_visible');
if ( !function_exists( 'is_ad_pos_content_bottom_label_visible' ) ):
function is_ad_pos_content_bottom_label_visible(){
  return get_theme_option(OP_AD_POS_CONTENT_BOTTOM_LABEL_VISIBLE, 1);
}
endif;

//投稿・固定ページSNSボタン上の広告表示
define('OP_AD_POS_ABOVE_SNS_BUTTONS_VISIBLE', 'ad_pos_above_sns_buttons_visible');
if ( !function_exists( 'is_ad_pos_above_sns_buttons_visible' ) ):
function is_ad_pos_above_sns_buttons_visible(){
  return get_theme_option(OP_AD_POS_ABOVE_SNS_BUTTONS_VISIBLE);
}
endif;

//投稿・固定ページSNSボタン上の広告フォーマット
define('OP_AD_POS_ABOVE_SNS_BUTTONS_FORMAT', 'ad_pos_above_sns_buttons_format');
if ( !function_exists( 'get_ad_pos_above_sns_buttons_format' ) ):
function get_ad_pos_above_sns_buttons_format(){
  return get_theme_option(OP_AD_POS_ABOVE_SNS_BUTTONS_FORMAT, DATA_AD_FORMAT_RECTANGLE);
}
endif;

//投稿・固定ページSNSボタン上の広告ラベル表示
define('OP_AD_POS_ABOVE_SNS_BUTTONS_LABEL_VISIBLE', 'ad_pos_above_sns_buttons_label_visible');
if ( !function_exists( 'is_ad_pos_above_sns_buttons_label_visible' ) ):
function is_ad_pos_above_sns_buttons_label_visible(){
  return get_theme_option(OP_AD_POS_ABOVE_SNS_BUTTONS_LABEL_VISIBLE, 1);
}
endif;

//投稿・固定ページSNSボタン下の広告表示
define('OP_AD_POS_BELOW_SNS_BUTTONS_VISIBLE', 'ad_pos_below_sns_buttons_visible');
if ( !function_exists( 'is_ad_pos_below_sns_buttons_visible' ) ):
function is_ad_pos_below_sns_buttons_visible(){
  return get_theme_option(OP_AD_POS_BELOW_SNS_BUTTONS_VISIBLE);
}
endif;

//投稿・固定ページSNSボタン下の広告フォーマット
define('OP_AD_POS_BELOW_SNS_BUTTONS_FORMAT', 'ad_pos_below_sns_buttons_format');
if ( !function_exists( 'get_ad_pos_below_sns_buttons_format' ) ):
function get_ad_pos_below_sns_buttons_format(){
  return get_theme_option(OP_AD_POS_BELOW_SNS_BUTTONS_FORMAT, DATA_AD_FORMAT_RECTANGLE);
}
endif;

//投稿・固定ページSNSボタン下の広告ラベル表示
define('OP_AD_POS_BELOW_SNS_BUTTONS_LABEL_VISIBLE', 'ad_pos_below_sns_buttons_label_visible');
if ( !function_exists( 'is_ad_pos_below_sns_buttons_label_visible' ) ):
function is_ad_pos_below_sns_buttons_label_visible(){
  return get_theme_option(OP_AD_POS_BELOW_SNS_BUTTONS_LABEL_VISIBLE, 1);
}
endif;

//投稿関連記事下の広告表示
define('OP_AD_POS_BELOW_RELATED_POSTS_VISIBLE', 'ad_pos_below_related_posts_visible');
if ( !function_exists( 'is_ad_pos_below_related_posts_visible' ) ):
function is_ad_pos_below_related_posts_visible(){
  return get_theme_option(OP_AD_POS_BELOW_RELATED_POSTS_VISIBLE, 1);
}
endif;

//投稿関連記事下の広告フォーマット
define('OP_AD_POS_BELOW_RELATED_POSTS_FORMAT', 'ad_pos_below_related_posts_format');
if ( !function_exists( 'get_ad_pos_below_related_posts_format' ) ):
function get_ad_pos_below_related_posts_format(){
  return get_theme_option(OP_AD_POS_BELOW_RELATED_POSTS_FORMAT, DATA_AD_FORMAT_RECTANGLE);
}
endif;

//投稿関連記事下の広告ラベル表示
define('OP_AD_POS_BELOW_RELATED_POSTS_LABEL_VISIBLE', 'ad_pos_below_related_posts_label_visible');
if ( !function_exists( 'is_ad_pos_below_related_posts_label_visible' ) ):
function is_ad_pos_below_related_posts_label_visible(){
  return get_theme_option(OP_AD_POS_BELOW_RELATED_POSTS_LABEL_VISIBLE, 1);
}
endif;

//[ad]ショートコードを有効
define('OP_AD_SHORTCODE_ENABLE', 'ad_shortcode_enable');
if ( !function_exists( 'is_ad_shortcode_enable' ) ):
function is_ad_shortcode_enable(){
  return get_theme_option(OP_AD_SHORTCODE_ENABLE, 1);
}
endif;

//[ad]ショートコード広告フォーマット
define('OP_AD_SHORTCODE_FORMAT', 'ad_shortcode_format');
if ( !function_exists( 'get_ad_shortcode_format' ) ):
function get_ad_shortcode_format(){
  return get_theme_option(OP_AD_SHORTCODE_FORMAT, DATA_AD_FORMAT_RECTANGLE);
}
endif;

//[ad]ショートコード広告ラベル表示
define('OP_AD_SHORTCODE_LABEL_VISIBLE', 'ad_shortcode_label_visible');
if ( !function_exists( 'is_ad_shortcode_label_visible' ) ):
function is_ad_shortcode_label_visible(){
  return get_theme_option(OP_AD_SHORTCODE_LABEL_VISIBLE, 1);
}
endif;

//LinkSwitch有効
define('OP_AD_LINKSWITCH_ENABLE', 'ad_linkswitch_enable');
if ( !function_exists( 'is_ad_linkswitch_enable' ) ):
function is_ad_linkswitch_enable(){
  return get_theme_option(OP_AD_LINKSWITCH_ENABLE);
}
endif;

//LinkSwitch ID
define('OP_AD_LINKSWITCH_ID', 'ad_linkswitch_id');
if ( !function_exists( 'get_ad_linkswitch_id' ) ):
function get_ad_linkswitch_id(){
  $linkswitch_id = get_theme_option(OP_AD_LINKSWITCH_ID);
  $linkswitch_id = trim($linkswitch_id);
  $linkswitch_id = strip_tags($linkswitch_id);
  return $linkswitch_id;
}
endif;
//LinkSwitchトータルとして有効か
if ( !function_exists( 'is_all_linkswitch_enable' ) ):
function is_all_linkswitch_enable(){
  return is_all_ads_visible() && is_ad_linkswitch_enable() && get_ad_linkswitch_id();
}
endif;

//広告除外記事ID
define('OP_AD_EXCLUDE_POST_IDS', 'ad_exclude_post_ids');
if ( !function_exists( 'get_ad_exclude_post_ids' ) ):
function get_ad_exclude_post_ids(){
  return get_theme_option(OP_AD_EXCLUDE_POST_IDS);
}
endif;

//広告除外カテゴリーID
define('OP_AD_EXCLUDE_CATEGORY_IDS', 'ad_exclude_category_ids');
if ( !function_exists( 'get_ad_exclude_category_ids' ) ):
function get_ad_exclude_category_ids(){
  return get_theme_option(OP_AD_EXCLUDE_CATEGORY_IDS, array());
}
endif;
