<?php
if (!defined('ABSPATH')) exit;


global $_THEME_OPTIONS;
global $_MOBILE_COPY_BUTTON;
global $_HVN_EYECATCH;
global $_HVN_NOTICE;

$skin_url = get_skin_url();
// ふわっと追加
if (strpos($skin_url, 'raku-add-fadein') !== false) {
  $skin_url = get_theme_option('raku_base_skin_url');
}

$skin_url = str_replace('style.css', '', $skin_url);
$skin_name = basename($skin_url);

define('HVN_SKIN_URL', $skin_url);
define('HVN_SKIN', "/skins/{$skin_name}/");
define('HVN_COUNT', 10);


//******************************************************************************
//  関数定義
//******************************************************************************
$path = url_to_local(HVN_SKIN_URL) . 'lib/*.php';
$files = glob($path);
foreach ($files as $file) {
  require_once($file);
}


//******************************************************************************
//  初期値
//******************************************************************************
define('HVN_MAIN_COLOR', '#40210f');
define('HVN_TEXT_COLOR', '#ffffff');
define('HVN_BODY_COLOR', '#f5f4f1');

define('HVN_GAP' , 30);
define('HVN_MAIN_WIDTH', 770);
define('HVN_SIDE_WIDTH', 370);

$_MOBILE_COPY_BUTTON = true;
$_THEME_OPTIONS = array(
// 全体
  'site_key_color' => '',
  'site_key_text_color' => '',
  'site_font_family' => 'meiryo',
  'site_font_size' => '16px',
  'site_text_color' => '',
  'mobile_site_font_size' => '16px',
  'site_font_weight' => 400,
  'site_icon_font' => 'font_awesome_5',
  'site_background_color' => '',
  'site_background_image_url' => '',
  'align_site_width' => 0,
  'site_link_color' => '#1e88e5',
  'site_selection_color' =>'',
  'site_selection_background_color' => '',
  'sidebar_position' => 'sidebar_right',
  'sidebar_display_type' => 'display_all',
  'all_thumbnail_visible' => 1,
  'site_date_format' => 'Y-m-d',

// ヘッダー
  'header_layout_type' => 'center_logo_slim',
  'header_fixed' => 0,
  'header_area_height' => 0,
  'mobile_header_area_height' => 0,
  'tagline_position' => 'none',
  'header_background_image_url' => '',
  'header_container_background_color' => '',
  'header_container_text_color' => '',
  'global_navi_menu_text_width_enable' => 1,
  'global_navi_sub_menu_width' => 240,

// 広告
  'pr_label_category_page_visible' => 0,
  'pr_label_tag_page_visible' => 0,
  'pr_label_small_visible' => 0,
  'pr_label_large_visible' => 1,
  'pr_label_small_caption' => '',

// カラム
  'main_column_padding' => 10,
  'main_column_border_width' => 0,
  'sidebar_border_width' => 0,

// インデックス
  'index_new_entry_card_count' => 4,
  'index_category_entry_card_count' => 4,
  'index_sort_orderby' => '',
  'entry_card_border_visible' => 0,
  'entry_card_excerpt_max_length' => 120,
  'entry_card_excerpt_more' => '...',
  'entry_card_snippet_visible' => 0,
  'smartphone_entry_card_snippet_visible' => 0,
  'entry_card_post_date_visible' => 0,
  'entry_card_post_update_visible' => 1,
  'entry_card_post_author_visible' => 0,

// 投稿
  'category_tag_display_type' => 'tag_only',
  'category_tag_display_position' => 'content_bottom',
  'related_entries_visible' => 1,
  'related_association_type' => 'category',
  'related_entry_heading' => __('関連記事', THEME_NAME),
  'related_entry_sub_heading' => '',
  'related_entry_type' => 'mini_card',
  'related_entry_count' => 4,
  'related_entry_border_visible' => 0,
  'related_excerpt_max_length' => 120,
  'related_entry_card_snippet_visible' => 0,
  'smartphone_related_entry_card_snippet_visible' => 0,
  'related_entry_card_post_date_visible' => 0,
  'related_entry_card_post_update_visible' => 0,
  'related_entry_card_post_author_visible' => 0,
  'post_navi_visible' => 1,
  'post_navi_type' => 'square',
  'post_navi_position' => 'under_related',
  'post_navi_border_visible' => 0,
  'single_breadcrumbs_position' => 'main_top',
  'single_breadcrumbs_include_post' => 0,

// 固定ページ
  'page_comment_visible' => 0,
  'page_breadcrumbs_position' => 'main_top',
  'page_breadcrumbs_include_post' => 0,

// 本文
  'entry_content_line_hight' => 1.8,
  'entry_content_margin_hight' => 1.8,
  'external_link_open_type' => 'blank',
  'internal_link_open_type' => 'default',
  'internal_link_icon_visible' => 0,
  'post_date_visible' => 1,
  'post_update_visible' => 1,
  'post_author_visible' => 0,

// 目次
  'toc_toggle_switch_enable' => 0,
  'toc_display_count' => 2,
  'toc_depth' => 3,
  'toc_number_type' => 'none',
  'toc_position_center' => 0,
  'toc_heading_inner_html_tag_enable' => 0,
  'category_toc_visible' => 0,
  'tag_toc_visible' => 0,

// SNSシェア
  'sns_top_share_buttons_visible' => 0,
  'sns_bottom_share_buttons_visible' => 1,
  'sns_bottom_share_button_color' => 'brand_color_white',
  'sns_bottom_share_column_count' => 1,
  'sns_bottom_share_logo_caption_position' => 'high_and_low_lc',
  'sns_bottom_share_buttons_count_visible' => 0,
  'sns_front_page_bottom_share_buttons_visible' => 0,
  'sns_single_bottom_share_buttons_visible' => 1,
  'sns_page_bottom_share_buttons_visible' => 1,
  'sns_category_bottom_share_buttons_visible' => 0,
  'sns_tag_bottom_share_buttons_visible' => 0,

// SNS フォロー
  'sns_front_page_follow_buttons_visible' => 0,
  'sns_single_follow_buttons_visible' => 1,
  'sns_page_follow_buttons_visible' => 1,
  'sns_category_follow_buttons_visible' => 0,
  'sns_tag_follow_buttons_visible' => 0,
  'sns_follow_button_color' => 'brand_color_white',
  'sns_follow_buttons_count_visible' => 0,

// 画像
  'eyecatch_center_enable' => 1,
  'eyecatch_width_100_percent_enable' => 1,
  'eyecatch_caption_visible' => 0,
  'image_wrap_effect' => 'none',

// ブログカード
  'internal_blogcard_enable' => 1,
  'comment_internal_blogcard_enable' => 0,
  'internal_blogcard_thumbnail_style' => 'left',
  'internal_blogcard_date_type' => 'none',
  'internal_blogcard_target_blank' => 0,
  'external_blogcard_enable' => 1,
  'comment_external_blogcard_enable' => 0,
  'external_blogcard_thumbnail_style' => 'left',
  'external_blogcard_target_blank' => 1,

// アピールエリア
  'appeal_area_button_background_color' => '',

// コード
  'code_highlight_enable' => 1,
  'code_row_number_enable' => 0,
  'code_highlight_package' => 'light',
  'code_highlight_style' => 'tomorrow-night-bright',
  'code_highlight_css_selector' => '.entry-content pre',

// コメント
  'comment_display_type' => 'default',
  'comment_sub_heading' => '',
  'comment_information_message' => '',
  'comment_form_display_type' => 'toggle_button',

// おすすめカード
  'recommended_cards_margin_enable' => 1,
  'recommended_cards_area_both_sides_margin_enable' => 1,

// カルーセル
  'carousel_display_type' => 'none',

// フッター
  'footer_text_color' => '',
  'footer_display_type' => 'up_and_down',
  'footer_navi_menu_text_width_enable' => 1,
  'copyright_name' => '',
  'credit_notation' => 'simple',

// ボタン
  'go_to_top_button_visible' => 1,
  'go_to_top_button_icon_font' => 'fa-chevron-up',
  'go_to_top_button_image_url' => '',

// モバイル
  'mobile_button_layout_type' => 'footer_mobile_buttons',
  'fixed_mobile_buttons_enable' => 0,
  'mobile_header_logo_visible' => 1,
  'slide_in_content_bottom_sidebar_visible' => 0,

// 管理者画面
  'admin_list_memo_visible' => 1,

// ウィジェット
  'exclude_widget_classes' => [
    'WP_Widget_Media_Audio',
    'WP_Widget_Media_Image',
    'WP_Widget_Media_Gallery',
    'WP_Widget_Meta',
    'WP_Widget_Recent_Posts',
    'WP_Widget_Recent_Comments',
    'WP_Widget_Block',
    'RelatedEntryWidgetItem',
    'PcTextWidgetItem',
    'PcAdWidgetItem',
    'MobileTextWidgetItem',
    'MobileAdWidgetItem',
    'AdWidgetItem',
    'FBLikeBoxWidgetItem',
    'FBLikeBallooneWidgetItem',
    'RecommendedCardWidgetItem',
],

// ウィジェットエリア
  'exclude_widget_area_ids' => [
//    'sidebar',
//    'sidebar-scroll',
    'main-scroll',

    'above-single-content-title',
    'below-single-content-title',
//    'single-content-top',
    'single-content-middle',
    'single-content-bottom',
//    'above-single-sns-buttons',
//    'below-single-sns-buttons',
    'above-single-related-entries',
    'below-single-related-entries',
    'above-single-comment-aria',
    'below-single-comment-form',

    'above-page-content-title',
    'below-page-content-title',
//    'page-content-top',
    'page-content-middle',
    'page-content-bottom',
//    'above-page-sns-buttons',
//    'below-page-sns-buttons',

//  'index-top',
    'index-middle',
//  'index-bottom',

//  'footer-left',
//  'footer-center',
//  'footer-right',

    'footer-mobile',
    '404-page'
  ],

// エディター
  'visual_editor_style_enable' => 1,
);
