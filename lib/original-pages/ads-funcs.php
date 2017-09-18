<?php //広告設定に必要な定数や関数

//広告を全て表示するか
define('OP_ALL_ADS_VISIBLE', 'all_ads_visible');
if ( !function_exists( 'is_all_ads_visible' ) ):
function is_all_ads_visible(){
  return get_option(OP_ALL_ADS_VISIBLE, 1);
}
endif;

//アドセンス広告を全て表示するか
define('OP_ALL_ADSENSES_VISIBLE', 'all_adsenses_visible');
if ( !function_exists( 'is_all_adsenses_visible' ) ):
function is_all_adsenses_visible(){
  return get_option(OP_ALL_ADSENSES_VISIBLE, 1);
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

//インデックストップの広告フォーマット
define('OP_AD_POS_INDEX_TOP_FORMAT', 'ad_pos_index_top_format');
if ( !function_exists( 'get_ad_pos_index_top_format' ) ):
function get_ad_pos_index_top_format(){
  return get_option(OP_AD_POS_INDEX_TOP_FORMAT, DATA_AD_FORMAT_HORIZONTAL);
}
endif;

//インデックスミドルの広告表示
define('OP_AD_POS_INDEX_MIDDLE_VISIBLE', 'ad_pos_index_middle_visible');
if ( !function_exists( 'is_ad_pos_index_middle_visible' ) ):
function is_ad_pos_index_middle_visible(){
  return get_option(OP_AD_POS_INDEX_MIDDLE_VISIBLE);
}
endif;

//インデックスミドルの広告フォーマット
define('OP_AD_POS_INDEX_MIDDLE_FORMAT', 'ad_pos_index_middle_format');
if ( !function_exists( 'get_ad_pos_index_middle_format' ) ):
function get_ad_pos_index_middle_format(){
  return get_option(OP_AD_POS_INDEX_MIDDLE_FORMAT, DATA_AD_FORMAT_RECTANGLE);
}
endif;

//インデックスボトムの広告表示
define('OP_AD_POS_INDEX_BOTTOM_VISIBLE', 'ad_pos_index_bottom_visible');
if ( !function_exists( 'is_ad_pos_index_bottom_visible' ) ):
function is_ad_pos_index_bottom_visible(){
  return get_option(OP_AD_POS_INDEX_BOTTOM_VISIBLE);
}
endif;

//インデックスボトムの広告フォーマット
define('OP_AD_POS_INDEX_BOTTOM_FORMAT', 'ad_pos_index_bottom_format');
if ( !function_exists( 'get_ad_pos_index_bottom_format' ) ):
function get_ad_pos_index_bottom_format(){
  return get_option(OP_AD_POS_INDEX_BOTTOM_FORMAT, DATA_AD_FORMAT_RECTANGLE);
}
endif;

//インデックスサイドバー上の広告表示
define('OP_AD_POS_SIDEBAR_TOP_VISIBLE', 'ad_pos_sidebar_top_visible');
if ( !function_exists( 'is_ad_pos_sidebar_top_visible' ) ):
function is_ad_pos_sidebar_top_visible(){
  return get_option(OP_AD_POS_SIDEBAR_TOP_VISIBLE, 1);
}
endif;

//インデックスサイドバー下の広告表示
define('OP_AD_POS_SIDEBAR_BOTTOM_VISIBLE', 'ad_pos_sidebar_bottom_visible');
if ( !function_exists( 'is_ad_pos_sidebar_bottom_visible' ) ):
function is_ad_pos_sidebar_bottom_visible(){
  return get_option(OP_AD_POS_SIDEBAR_BOTTOM_VISIBLE);
}
endif;

//投稿・固定ページタイトル上の広告表示
define('OP_AD_POS_ABOVE_TITLE_VISIBLE', 'ad_pos_above_title_visible');
if ( !function_exists( 'is_ad_pos_above_title_visible' ) ):
function is_ad_pos_above_title_visible(){
  return get_option(OP_AD_POS_ABOVE_TITLE_VISIBLE);
}
endif;

//投稿・固定ページタイトル上の広告フォーマット
define('OP_AD_POS_ABOVE_TITLE_FORMAT', 'ad_pos_above_title_format');
if ( !function_exists( 'get_ad_pos_above_title_format' ) ):
function get_ad_pos_above_title_format(){
  return get_option(OP_AD_POS_ABOVE_TITLE_FORMAT, DATA_AD_FORMAT_HORIZONTAL);
}
endif;

//投稿・固定ページタイトル下の広告表示
define('OP_AD_POS_BELOW_TITLE_VISIBLE', 'ad_pos_below_title_visible');
if ( !function_exists( 'is_ad_pos_below_title_visible' ) ):
function is_ad_pos_below_title_visible(){
  return get_option(OP_AD_POS_BELOW_TITLE_VISIBLE);
}
endif;

//投稿・固定ページタイトル下の広告フォーマット
define('OP_AD_POS_BELOW_TITLE_FORMAT', 'ad_pos_below_title_format');
if ( !function_exists( 'get_ad_pos_below_title_format' ) ):
function get_ad_pos_below_title_format(){
  return get_option(OP_AD_POS_BELOW_TITLE_FORMAT, DATA_AD_FORMAT_HORIZONTAL);
}
endif;

//投稿・固定ページ本文上の広告表示
define('OP_AD_POS_CONTENT_TOP_VISIBLE', 'ad_pos_content_top_visible');
if ( !function_exists( 'is_ad_pos_content_top_visible' ) ):
function is_ad_pos_content_top_visible(){
  return get_option(OP_AD_POS_CONTENT_TOP_VISIBLE);
}
endif;

//投稿・固定ページ本文上の広告フォーマット
define('OP_AD_POS_CONTENT_TOP_FORMAT', 'ad_pos_content_top_format');
if ( !function_exists( 'get_ad_pos_content_top_format' ) ):
function get_ad_pos_content_top_format(){
  return get_option(OP_AD_POS_CONTENT_TOP_FORMAT, DATA_AD_FORMAT_HORIZONTAL);
}
endif;

//投稿・固定ページ本文中の広告表示
define('OP_AD_POS_CONTENT_MIDDLE_VISIBLE', 'ad_pos_content_middle_visible');
if ( !function_exists( 'is_ad_pos_content_middle_visible' ) ):
function is_ad_pos_content_middle_visible(){
  return get_option(OP_AD_POS_CONTENT_MIDDLE_VISIBLE);
}
endif;

//投稿・固定ページ本文中の広告フォーマット
define('OP_AD_POS_CONTENT_MIDDLE_FORMAT', 'ad_pos_content_middle_format');
if ( !function_exists( 'get_ad_pos_content_middle_format' ) ):
function get_ad_pos_content_middle_format(){
  return get_option(OP_AD_POS_CONTENT_MIDDLE_FORMAT, DATA_AD_FORMAT_RECTANGLE);
}
endif;

//投稿・固定ページ本文中のH2見出し全てに広告表示
define('OP_AD_POS_ALL_CONTENT_MIDDLE_VISIBLE', 'ad_pos_all_content_middle_visible');
if ( !function_exists( 'is_ad_pos_all_content_middle_visible' ) ):
function is_ad_pos_all_content_middle_visible(){
  return get_option(OP_AD_POS_ALL_CONTENT_MIDDLE_VISIBLE);
}
endif;

//投稿・固定ページ本文下の広告表示
define('OP_AD_POS_CONTENT_BOTTOM_VISIBLE', 'ad_pos_content_bottom_visible');
if ( !function_exists( 'is_ad_pos_content_bottom_visible' ) ):
function is_ad_pos_content_bottom_visible(){
  return get_option(OP_AD_POS_CONTENT_BOTTOM_VISIBLE, 1);
}
endif;

//投稿・固定ページ本文下の広告フォーマット
define('OP_AD_POS_CONTENT_BOTTOM_FORMAT', 'ad_pos_content_bottom_format');
if ( !function_exists( 'get_ad_pos_content_bottom_format' ) ):
function get_ad_pos_content_bottom_format(){
  return get_option(OP_AD_POS_CONTENT_BOTTOM_FORMAT, DATA_AD_FORMAT_RECTANGLE);
}
endif;

//投稿・固定ページSNSボタン上の広告表示
define('OP_AD_POS_ABOVE_SNS_BUTTONS_VISIBLE', 'ad_pos_above_sns_buttons_visible');
if ( !function_exists( 'is_ad_pos_above_sns_buttons_visible' ) ):
function is_ad_pos_above_sns_buttons_visible(){
  return get_option(OP_AD_POS_ABOVE_SNS_BUTTONS_VISIBLE);
}
endif;

//投稿・固定ページSNSボタン上の広告フォーマット
define('OP_AD_POS_ABOVE_SNS_BUTTONS_FORMAT', 'ad_pos_above_sns_buttons_format');
if ( !function_exists( 'get_ad_pos_above_sns_buttons_format' ) ):
function get_ad_pos_above_sns_buttons_format(){
  return get_option(OP_AD_POS_ABOVE_SNS_BUTTONS_FORMAT, DATA_AD_FORMAT_RECTANGLE);
}
endif;

//投稿・固定ページSNSボタン下の広告表示
define('OP_AD_POS_BELOW_SNS_BUTTONS_VISIBLE', 'ad_pos_below_sns_buttons_visible');
if ( !function_exists( 'is_ad_pos_below_sns_buttons_visible' ) ):
function is_ad_pos_below_sns_buttons_visible(){
  return get_option(OP_AD_POS_BELOW_SNS_BUTTONS_VISIBLE);
}
endif;

//投稿・固定ページSNSボタン下の広告フォーマット
define('OP_AD_POS_BELOW_SNS_BUTTONS_FORMAT', 'ad_pos_below_sns_buttons_format');
if ( !function_exists( 'get_ad_pos_below_sns_buttons_format' ) ):
function get_ad_pos_below_sns_buttons_format(){
  return get_option(OP_AD_POS_BELOW_SNS_BUTTONS_FORMAT, DATA_AD_FORMAT_RECTANGLE);
}
endif;

//投稿関連記事下の広告表示
define('OP_AD_POS_BELOW_RELATED_POSTS_VISIBLE', 'ad_pos_below_related_posts_visible');
if ( !function_exists( 'is_ad_pos_below_related_posts_visible' ) ):
function is_ad_pos_below_related_posts_visible(){
  return get_option(OP_AD_POS_BELOW_RELATED_POSTS_VISIBLE, 1);
}
endif;

//投稿関連記事下の広告フォーマット
define('OP_AD_POS_BELOW_RELATED_POSTS_FORMAT', 'ad_pos_below_related_posts_format');
if ( !function_exists( 'get_ad_pos_below_related_posts_format' ) ):
function get_ad_pos_below_related_posts_format(){
  return get_option(OP_AD_POS_BELOW_RELATED_POSTS_FORMAT, DATA_AD_FORMAT_RECTANGLE);
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

//メインカラム広告の詳細設定フォーム
if ( !function_exists( 'echo_main_column_ad_detail_setting_forms' ) ):
function echo_main_column_ad_detail_setting_forms($name, $value, $body_ad_name = null, $body_ad_value = null){ ?>
 <span class="toggle">
  <span class="toggle-link"><?php _e( '詳細設定', THEME_NAME ) ?></span>
  <div class="toggle-content">
    <?php _e( 'フォーマット：', THEME_NAME ) ?><select name="<?php echo $name; ?>" >
      <option value="<?php echo DATA_AD_FORMAT_AUTO; ?>"<?php the_option_selected(DATA_AD_FORMAT_AUTO, $value) ?>><?php _e( 'オート', THEME_NAME ) ?></option>
      <option value="<?php echo DATA_AD_FORMAT_HORIZONTAL; ?>"<?php the_option_selected(DATA_AD_FORMAT_HORIZONTAL, $value) ?>><?php _e( 'バナー', THEME_NAME ) ?></option>
      <option value="<?php echo DATA_AD_FORMAT_RECTANGLE; ?>"<?php the_option_selected(DATA_AD_FORMAT_RECTANGLE, $value) ?>><?php _e( 'レスポンシブレクタングル', THEME_NAME ) ?></option>
      <option value="<?php echo AD_FORMAT_SINGLE_RECTANGLE; ?>"<?php the_option_selected(AD_FORMAT_SINGLE_RECTANGLE, $value) ?>><?php _e( 'シングルレクタングル', THEME_NAME ) ?></option>
      <option value="<?php echo AD_FORMAT_DABBLE_RECTANGLE; ?>"<?php the_option_selected(AD_FORMAT_DABBLE_RECTANGLE, $value) ?>><?php _e( 'ダブルレクタングル', THEME_NAME ) ?></option>
    </select>
   <?php //本文中広告用の設定
   if (isset($body_ad_name) && isset($body_ad_value)): ?>
      <p><input type="checkbox" name="<?php echo $body_ad_name; ?>" value="1"<?php the_checkbox_checked($body_ad_value); ?>><?php _e('全てのH2見出し手前に広告を挿入' ,THEME_NAME ); ?></p>
   <?php endif ?>
  </div>
</span>
<?php
}
endif;

