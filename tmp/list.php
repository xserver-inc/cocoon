<?php //一覧
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

////////////////////////////
//アーカイブのタイトル
////////////////////////////
if ( is_category() ){
  ////////////////////////////
  //カテゴリーページのパンくずリスト
  ////////////////////////////
  if (is_single_breadcrumbs_position_main_top()) {
    cocoon_template_part('tmp/breadcrumbs');
  }

  ////////////////////////////
  //カテゴリーページのコンテンツ
  ////////////////////////////
  if (!is_paged()) {
    cocoon_template_part('tmp/category-content');
  } else {
    cocoon_template_part('tmp/list-title');
  }
} elseif ( (is_tag() || is_tax()) && !is_paged() ) {
  cocoon_template_part('tmp/tag-content');
} elseif (!is_home()) {
  //それ以外
  cocoon_template_part('tmp/list-title');
}

////////////////////////////
//インデックストップ広告
////////////////////////////
if (is_ad_pos_index_top_visible() && is_all_adsenses_visible()){
  //レスポンシブ広告
  get_template_part_with_ad_format(get_ad_pos_index_top_format(), 'ad-index-top', is_ad_pos_index_top_label_visible());
};

////////////////////////////
//インデックスリストトップウィジェット
////////////////////////////
if ( is_active_sidebar( 'index-top' ) ){
  dynamic_sidebar( 'index-top' );
};

////////////////////////////
// トップシェアボタン
////////////////////////////
//SNSトップシェアボタンの表示
if (is_sns_top_share_buttons_visible() &&
  //フロントページトップシェアボタンの表示
  (is_front_page() && !is_paged() && is_sns_front_page_top_share_buttons_visible())
){
  get_template_part_with_option('tmp/sns-share-buttons', SS_TOP);
} ?>

<?php
  if (is_front_top_page() && is_front_page_type_tab_index()) {
    cocoon_template_part('tmp/list-tab-index');
  } elseif (is_front_top_page() && is_front_page_type_category()) {
    cocoon_template_part('tmp/list-category');
  } elseif ((is_front_top_page() && is_front_page_type_category_2_columns()) || is_front_top_page() && is_front_page_type_category_3_columns()) {
    cocoon_template_part('tmp/list-category-columns');
  } else {
    cocoon_template_part('tmp/list-index');
  }
?>

<?php
////////////////////////////
//インデックスボトム広告
////////////////////////////
if (is_ad_pos_index_bottom_visible() && is_all_adsenses_visible()){
  //レスポンシブ広告のフォーマットにrectangleを指定する
  get_template_part_with_ad_format(get_ad_pos_index_bottom_format(), 'ad-index-bottom', is_ad_pos_index_bottom_label_visible());
};

////////////////////////////
//インデックスリストボトムウィジェット
////////////////////////////
if ( is_active_sidebar( 'index-bottom' ) ){
  dynamic_sidebar( 'index-bottom' );
};

////////////////////////////
//フロントページボトムシェアボタン
////////////////////////////
//SNSボトムシェアボタンの表示
if (is_sns_bottom_share_buttons_visible() && !is_paged() &&
  (
  //フロントページボトムシェアボタンの表示
  (is_front_page() && is_sns_front_page_bottom_share_buttons_visible()) ||
  //カテゴリーページトップシェアボタンの表示
  (is_category() && is_sns_category_bottom_share_buttons_visible()) ||
  //タグページトップシェアボタンの表示
  (is_tag() && is_sns_tag_bottom_share_buttons_visible())
  )

){
  get_template_part_with_option('tmp/sns-share-buttons', SS_BOTTOM);
}

////////////////////////////
//フロントページフォローボタン
////////////////////////////
//SNSフォローボタンの表示
if (is_sns_follow_buttons_visible() && !is_paged() &&
  (
    //フロントページフォローボタンの表示
    (is_front_page() && is_sns_front_page_follow_buttons_visible()) ||
    //カテゴリーページボトムフォローボタンの表示
    (is_category() && is_sns_category_follow_buttons_visible()) ||
    //タグページボトムフォローボタンの表示
    (is_tag() && is_sns_tag_follow_buttons_visible())
  )

){
  get_template_part_with_option('tmp/sns-follow-buttons', SF_BOTTOM);
}

////////////////////////////
//ページネーション
////////////////////////////
if (is_front_page_type_index() || !is_front_top_page()) {
  cocoon_template_part('tmp/pagination');
}

////////////////////////////
//カテゴリーページのパンくずリスト
////////////////////////////
if (is_category() && is_single_breadcrumbs_position_main_bottom()){
  cocoon_template_part('tmp/breadcrumbs');
}

////////////////////////////
//メインカラム追従領域
////////////////////////////
cocoon_template_part('tmp/main-scroll'); ?>
