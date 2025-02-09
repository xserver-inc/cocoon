<?php //スタイリング用の追加クラス関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//フォントファミリークラスの取得
if ( !function_exists( 'get_site_font_family_class' ) ):
function get_site_font_family_class(){
  return 'ff-'.replace_value_to_class(get_site_font_family());
}
endif;

//フォントサイズクラスの取得
if ( !function_exists( 'get_site_font_size_class' ) ):
function get_site_font_size_class(){
  return 'fz-'.get_site_font_size();
}
endif;

//フォントウエイトクラスの取得
if ( !function_exists( 'get_site_font_weight_class' ) ):
function get_site_font_weight_class(){
  return 'fw-'.get_site_font_weight();
}
endif;

if ( !function_exists( 'replace_value_to_class' ) ):
function replace_value_to_class($value){
  return strtolower(str_replace('_', '-', $value));
}
endif;


//bodyクラスの追加関数
add_filter('body_class', 'body_class_additional');
if ( !function_exists( 'body_class_additional' ) ):
function body_class_additional($classes) {
  global $post;

  //全体的なCSS優先度対策用
  $classes[] = 'body';

  //フロントページかどうか
  if (is_front_top_page()) {
    $classes[] = 'front-top-page';
  }

  //管理画面との差別用
  $classes[] = 'public-page';
  //body
  $classes[] = 'page-body';

  //カテゴリー・タグクラスの追加
  if ( is_single() ) {
    //カテゴリークラスの追加
    if (isset($post->ID)) {
      $categories = get_the_category($post->ID);
      if (is_array($categories)) {
        foreach($categories as $category)
          $classes[] = 'categoryid-'.$category->cat_ID;
      }
    }

    //タグクラスの追加
    if (isset($post->ID)) {
      $tags = get_the_tags($post->ID);
      if (is_array($tags)) {
        foreach($tags as $tag)
          $classes[] = 'tagid-'.$tag->term_id;
      }
    }

  }

  //フォントファミリー
  $classes[] = get_site_font_family_class();

  //フォントサイズ
  $classes[] = get_site_font_size_class();

  //フォントウエイト
  $classes[] = get_site_font_weight_class();

  //投稿管理画面で「1カラム」が選択されている場合
  if (is_singular() && is_singular_page_type_column1()) {
    //$add_no_sidebar = true;
    $classes[] = 'column1';
  }

  //投稿管理画面で「本文のみ」が選択されている場合
  if (is_singular() && is_singular_page_type_content_only()) {
    //$add_no_sidebar = true;
    $classes[] = 'content-only';
  }

  //投稿管理画面で「狭い」が選択されている場合
  if (is_singular() && is_singular_page_type_narrow()) {
    //$add_no_sidebar = true;
    $classes[] = 'column-narrow';
  }

  //投稿管理画面で「広い」が選択されている場合
  if (is_singular() && is_singular_page_type_wide()) {
    $classes[] = 'column-wide';
  }

  //投稿管理画面で「フルワイド」が選択されている場合
  if (is_singular() && is_singular_page_type_full_wide()) {
    $classes[] = 'column-full-wide';
  }

  //ヘッダーが「センターロゴ」か「トップメニュー」か
  if (is_header_layout_type_top()) {
    $classes[] = 'hlt-top-menu-wrap';
  } else {
    $classes[] = 'hlt-center-logo-wrap';
  }

  //エントリーカードタイプ
  $classes[] = 'ect-'.replace_value_to_class(get_entry_card_type()).'-wrap';

  $classes[] = 'rect-'.replace_value_to_class(get_related_entry_type()).'-wrap';

  //投稿管理画面で「デフォルト」以外が選択されている場合
  if (!is_singular_page_type_default()) {
    $classes[] = replace_value_to_class(get_singular_page_type());
  }

  //サイドバー非表示のフラグが立っている場合はクラスを追加
  if (!is_the_page_sidebar_visible()) {
    $classes[] = 'no-sidebar';
  }

  //サイドバー追従領域のウィジェット状態
  if (is_scrollable_sidebar_enable()) {
    $classes[] = 'scrollable-sidebar';
  } else {
    $classes[] = 'no-scrollable-sidebar';
  }

  //メイン追従領域のウィジェット状態
  if (is_scrollable_main_enable()) {
    $classes[] = 'scrollable-main';
  } else {
    $classes[] = 'no-scrollable-main';
  }

  //サイドバーの位置
  switch (get_sidebar_position()) {
    case 'sidebar_left':
      $classes[] = 'sidebar-left';
      break;
    default: //sidebar_right
      $classes[] = 'sidebar-right';
      break;
  }

  //アピールエリア表示設定
  $add_no_appeal_area = false;
  switch (get_appeal_area_display_type()) {
    //フロントページ以外では表示しない
    case 'front_page_only':
      if (!(is_front_top_page())) {
        $add_no_appeal_area = true;
      }
      break;
    //投稿・固定ページで表示しない
    case 'not_singular':
      if (is_singular()) {
        $add_no_appeal_area = true;
      }
      break;
    //投稿・固定ページのみで表示
    case 'singular_only':
      if (!is_singular()) {
        $add_no_appeal_area = true;
      }
      break;
    //投稿ページで表示
    case 'single_only':
      if (!is_single()) {
        $add_no_appeal_area = true;
      }
      break;
    //固定ページで表示
    case 'page_only':
      if (!is_page()) {
        $add_no_appeal_area = true;
      }
      break;
  }

  //アピールエリア表示のフラグが立っている場合はクラスを追加
  if ($add_no_appeal_area) {
    $classes[] = 'no-appeal-area';
  }


  //カルーセル表示設定
  $add_no_carousel = false;
  switch (get_carousel_display_type()) {
    //フロントページ以外では表示しない
    case 'front_page_only':
      if (!is_front_top_page()) {
        $add_no_carousel = true;
      }
      break;
    //投稿・固定ページで表示しない
    case 'not_singular':
      if (is_singular()) {
        $add_no_carousel = true;
      }
      break;
    //投稿・固定ページのみで表示
    case 'singular_only':
      if (!is_singular()) {
        $add_no_carousel = true;
      }
      break;
    //投稿ページで表示
    case 'single_only':
      if (!is_single()) {
        $add_no_carousel = true;
      }
      break;
    //固定ページで表示
    case 'page_only':
      if (!is_page()) {
        $add_no_carousel = true;
      }
      break;
  }
  //_v($add_no_carousel);
  //カルーセル表示のフラグが立っている場合はクラスを追加
  if ($add_no_carousel) {
    $classes[] = 'no-carousel';
  }

  //Live Writer用クラス
  if (is_user_agent_live_writer()) {
    $classes[] = 'live-writer';
  }

  //モバイルボタンタイプ
  $classes[] = 'mblt-'.replace_value_to_class(get_mobile_button_layout_type());
  //モバイルボタンでスクロール動作するか
  if (!is_fixed_mobile_buttons_enable()) {
    $classes[] = 'scrollable-mobile-buttons';
  }

  //管理者クラス
  $author_id = get_the_author_meta( 'ID' );
  //var_dump($author_id);
  $auther_class = 'author-admin';
  if (!is_author_administrator()) {
    $auther_class = 'author-guest';
  }

  // elseif ($author_id == 0) {
  //   $auther_class = 'author-guest';
  // }
  $classes[] = $auther_class;

  //モバイルボタンはスライドインタイプか
  if (is_mobile_button_layout_type_footer_mobile_buttons()) {
    $classes[] = 'mobile-button-fmb';
  }

  //スライドインボタン表示時にサイドバーを表示するか
  if (is_mobile_button_layout_type_mobile_buttons() && !is_slide_in_content_bottom_sidebar_visible()) {
    $classes[] = 'no-mobile-sidebar';
  }

  //サイトのサムネイルを表示するか
  if (!is_all_thumbnail_visible()) {
    $classes[] = 'no-thumbnail';
  }

  //投稿・固定ページの投稿日を表示するか
  if (is_singular() && !is_post_date_visible() && (get_update_time() || !is_post_update_visible())) {
    $classes[] = 'no-post-date';
  }

  //投稿・固定ページの更新日を表示するか
  if (is_singular() && !is_post_update_visible()) {
    $classes[] = 'no-post-update';
  }

  //投稿・固定ページの投稿者を表示するか
  if (is_singular() && !is_post_author_visible()) {
    $classes[] = 'no-post-author';
  }

  //スマートフォン環境でスニペットを表示するか
  if (!is_smartphone_entry_card_snippet_visible()) {
    $classes[] = 'no-sp-snippet';
  }

  //スマートフォン環境でスニペットを表示するか
  if (!is_smartphone_related_entry_card_snippet_visible()) {
    $classes[] = 'no-sp-snippet-related';
  }

  //Pinterestボタンを表示するか
  if (is_pinterest_share_pin_visible() && is_singular()) {
    $classes[] = 'show-pinterest-button';
  }

  //ヘッダーモバイルボタン表示時にサイトロゴを表示しない場合
  if (!is_mobile_header_logo_visible() && is_mobile_button_layout_type_mobile_buttons_top() && !is_amp()) {
    $classes[] = 'no-mobile-header-logo';
  }

  //利用アイコンフォントクラス
  $classes[] = get_site_icon_font_class();

  //ヘッダーを固定する場合
  if (is_header_fixed()) {
    $classes[] = 'is-header-fixed';
  }

  //サムネイルアスペクト比
  $classes[] = 'thumb-'.replace_value_to_class(get_thumbnail_image_type());

  //ページ設定でタイトルを非表示としたとき
  if (!is_page_title_visible()) {
    $classes[] = 'no-page-title';
  }

  //ClassicPressの場合
  if (is_classicpress()) {
    $classes[] = 'classicpress';
  }

  return apply_filters('body_class_additional', $classes);
}//body_class_additional
endif;


//メインカラムの追加関数
if ( !function_exists( 'get_additional_main_classes' ) ):
function get_additional_main_classes($option = null){
  $classes = null;

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_main_classes', $classes);
}
endif;

//メインカラムの追加関数
if ( !function_exists( 'get_additional_entry_content_classes' ) ):
function get_additional_entry_content_classes($option = null){
  $classes = null;
  //画像の枠線エフェクトが設定されている場合
  switch (get_image_wrap_effect()) {
    case 'border':
      $classes .= ' iwe-border';
      break;
    case 'border_bold':
      $classes .= ' iwe-border-bold';
      break;
    case 'shadow':
      $classes .= ' iwe-shadow';
      break;
      case 'shadow_paper':
        $classes .= ' iwe-shadow-paper';
        break;
    default:

      break;
  }

  //行番号を表示する
  if (is_code_row_number_enable()) {
    $classes .= ' is-code-row-number-enable';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_entry_content_classes', $classes);
}
endif;

//エントリーカードの追加関数
if ( !function_exists( 'get_additional_widget_entry_cards_classes' ) ):
function get_additional_widget_entry_cards_classes($atts){
  extract(shortcode_atts(array(
    'type' => 0,
    'bold' => 0,
    'arrow' => 0,
    'ranking_visible' => 0,
    'pv_visible' => 0,
    'class' => null,
    'horizontal' => 0,
  ), $atts));
  $classes = null;

  if (includes_string($type, ET_LARGE_THUMB) && $type) {
    $classes .= ' card-large-image';
    if ($type == ET_LARGE_THUMB) {
      $classes .= ' large-thumb';
    } else if ($type == ET_LARGE_THUMB_ON) {
      $classes .= ' large-thumb-on';
    }
  } else {
    if ($type == ET_BORDER_PARTITION || $type == 1) {
      $classes .= ' border-partition';
    } else if ($type == ET_BORDER_SQUARE || $type == 2) {
      $classes .= ' border-square';
    }
  }

  if ($bold) {
    $classes .= ' card-title-bold';
  }

  if ($arrow && !is_widget_entry_card_large_image_use($type)) {
    $classes .= ' card-arrow';
  }

  if ($ranking_visible) {
    $classes .= ' ranking-visible';
  }

  if ($class) {
    $classes .= ' '.trim($class);
  }

  if ($horizontal) {
    $classes .= ' is-list-horizontal swiper';
  }
  return apply_filters('get_additional_widget_entry_cards_classes', $classes);
}
endif;

//SNSシェアボタンの追加関数
if ( !function_exists( 'get_additional_sns_share_button_classes' ) ):
function get_additional_sns_share_button_classes($option = null){
  $classes = null;
  if ($option == SS_MOBILE) {
    //_v($option);
    $classes .= ' ss-col-3 bc-brand-color sbc-hide '.SS_MOBILE;
    return apply_filters('get_additional_sns_share_button_classes', $classes);
  }

  //カラム数
  if ($option == SS_TOP) {
    $value = get_sns_top_share_column_count();
  } else {
    $value = get_sns_bottom_share_column_count();
  }
  switch ($value) {
    case 1:
      $classes .= ' ss-col-1';
      break;
    case 2:
      $classes .= ' ss-col-2';
      break;
    case 3:
      $classes .= ' ss-col-3';
      break;
    case 4:
      $classes .= ' ss-col-4';
      break;
    case 5:
      $classes .= ' ss-col-5';
      break;
    default:
      $classes .= ' ss-col-6';
      break;
  }

  //ロゴとキャプションの配置
  if ($option == SS_TOP) {
    $value = get_sns_top_share_logo_caption_position();
  } else {
    $value = get_sns_bottom_share_logo_caption_position();
  }
  switch ($value) {
    case 'high_and_low_lc':
      $classes .= ' ss-high-and-low-lc';
      break;
    case 'high_and_low_cl':
      $classes .= ' ss-high-and-low-cl';
      break;
    default:

      break;
  }

  //ボタンカラー
  if ($option == SS_TOP) {
    $value = get_sns_top_share_button_color();
  } else {
    $value = get_sns_bottom_share_button_color();
  }
  switch ($value) {
    case 'brand_color':
      $classes .= ' bc-brand-color';
      break;
    case 'brand_color_white':
      $classes .= ' bc-brand-color-white';
      break;
    default:
      $classes .= ' bc-monochrome';
      break;
  }

  //ボタンカラー
  if ($option == SS_TOP) {
    $value = is_sns_top_share_buttons_count_visible();
  } else {
    $value = is_sns_bottom_share_buttons_count_visible();
  }
  if ($value) {
    $classes .= ' sbc-show';
  } else {
    $classes .= ' sbc-hide';
  }


  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_sns_share_button_classes', $classes);
}
endif;

//SNSフォローボタンのclass追加関数
if ( !function_exists( 'get_additional_sns_follow_button_classes' ) ):
function get_additional_sns_follow_button_classes($option = null){
  $classes = null;

  if ($option == SF_MOBILE) {
    $classes .= ' bc-brand-color fbc-hide '.SF_MOBILE;
    return apply_filters('get_additional_sns_follow_button_classes', $classes);
  }

  //ボタンカラー
  switch (get_sns_follow_button_color()) {
    case 'brand_color':
      $classes .= ' bc-brand-color';
      break;
    case 'brand_color_white':
      $classes .= ' bc-brand-color-white';
      break;
    default:
      $classes .= ' bc-monochrome';
      break;
  }

  //フォロー数表示
  if (is_sns_follow_buttons_count_visible()) {
    $classes .= ' fbc-show';
  } else {
    $classes .= ' fbc-hide';
  }


  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_sns_follow_button_classes', $classes);
}
endif;

//内部ブログカードのclass追加関数
if ( !function_exists( 'get_additional_internal_blogcard_classes' ) ):
function get_additional_internal_blogcard_classes($option = null){
  $classes = null;

  //ボタンカラー
  switch (get_internal_blogcard_thumbnail_style()) {
    case 'right':
      $classes .= ' ib-right';
      break;
    default: //left
      $classes .= ' ib-left';
      break;
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_internal_blogcard_classes', $classes);
}
endif;

//外部ブログカードのclass追加関数
if ( !function_exists( 'get_additional_external_blogcard_classes' ) ):
function get_additional_external_blogcard_classes($option = null){
  $classes = null;

  //ボタンカラー
  switch (get_external_blogcard_thumbnail_style()) {
    case 'right':
      $classes .= ' eb-right';
      break;
    default: //left
      $classes .= ' eb-left';
      break;
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_external_blogcard_classes', $classes);
}
endif;


//ヘッダーのclass追加関数
if ( !function_exists( 'get_additional_header_classes' ) ):
function get_additional_header_classes($option = null){
  $classes = null;

  //ヘッダーを固定にする場合
  if (is_header_background_attachment_fixed()) {
    $classes .= ' ba-fixed';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_header_classes', $classes);
}
endif;


//サイト全体コンテナのclass追加関数
if ( !function_exists( 'get_additional_container_classes' ) ):
function get_additional_container_classes($option = null){
  $classes = null;

  //サイト幅を揃える
  if (is_align_site_width()) {
    $classes .= ' wrap';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_container_classes', $classes);
}
endif;


//ヘッダーコンテナのclass追加関数
if ( !function_exists( 'get_additional_header_container_classes' ) ):
function get_additional_header_container_classes($option = null){
  $classes = null;

  switch (get_header_layout_type()) {
    case 'center_logo_top_menu':
      $classes .= ' hlt-center-logo-top-menu';
      break;
    case 'center_logo_slim':
      $classes .= ' hlt-center-logo cl-slim';
      break;
    case 'center_logo_slim_top_menu':
      $classes .= ' hlt-center-logo-top-menu cl-slim';
      break;
    case 'top_menu':
      $classes .= ' hlt-top-menu wrap';
      break;
    case 'top_menu_right':
      $classes .= ' hlt-top-menu hlt-tm-right wrap';
      break;
    case 'top_menu_small':
      $classes .= ' hlt-top-menu hlt-tm-small wrap';
      break;
    case 'top_menu_small_right':
      $classes .= ' hlt-top-menu hlt-tm-right hlt-tm-small wrap';
      break;
    default://'center_logo'デフォルト
      $classes .= ' hlt-center-logo';
      break;
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_header_container_classes', $classes);
}
endif;


//フッターボトムのclass追加関数
if ( !function_exists( 'get_additional_footer_bottom_classes' ) ):
function get_additional_footer_bottom_classes($option = null){
  $classes = null;

  switch (get_footer_display_type()) {
    case 'left_and_right':
      $classes .= ' fdt-left-and-right';
      break;
    case 'up_and_down':
      $classes .= ' fdt-up-and-down';
      break;
    default://デフォルト
      $classes .= ' fdt-logo';
      break;
  }

  //フッターナビメニューをテキスト幅に
  if (is_footer_navi_menu_text_width_enable()) {
    $classes .= ' fnm-text-width';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_footer_bottom_classes', $classes);
}
endif;

//関連記事のclass追加関数
if ( !function_exists( 'get_additional_related_entries_classes' ) ):
function get_additional_related_entries_classes($option = null){
  $classes = null;
  switch (get_related_entry_type()) {
    case 'vertical_card_3':
    case 'vertical_card_4':
      $classes .= ' rect-vertical-card';
      break;
  }
  $classes .= ' rect-'.replace_value_to_class(get_related_entry_type());
  if (is_related_entry_border_visible()) {
    $classes .= ' recb-entry-border';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_related_entries_classes', $classes);
}
endif;


//ページナビのclass追加関数
if ( !function_exists( 'get_additional_post_navi_classes' ) ):
function get_additional_post_navi_classes($option = null){
  $classes = null;
  // switch (get_post_navi_type()) {
  //   case 'vertical_card_3':
  //   case 'vertical_card_4':
  //     $classes .= ' related-vertical-card';
  //     break;
  // }
  $classes .= ' post-navi-'.replace_value_to_class(get_post_navi_type());
  if (is_post_navi_border_visible()) {
    $classes .= ' post-navi-border';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_post_navi_classes', $classes);
}
endif;


//コメントエリアのclass追加関数
if ( !function_exists( 'get_additional_comment_area_classes' ) ):
function get_additional_comment_area_classes($option = null){
  $classes = null;

  if (!is_comment_website_visible()) {
    $classes .= ' website-hide';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_comment_area_classes', $classes);
}
endif;


//投稿パンくずリストのclass追加関数
if ( !function_exists( 'get_additional_single_breadcrumbs_classes' ) ):
function get_additional_single_breadcrumbs_classes($option = null){
  $classes = null;

  $classes .= ' sbp-'.replace_value_to_class(get_single_breadcrumbs_position());

  switch (get_single_breadcrumbs_position()) {
    case 'main_before':
    case 'footer_before':
      $classes .= ' wrap';
      break;
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_single_breadcrumbs_classes', $classes);
}
endif;


//固定ページパンくずリストのclass追加関数
if ( !function_exists( 'get_additional_page_breadcrumbs_classes' ) ):
function get_additional_page_breadcrumbs_classes($option = null){
  $classes = null;

  $classes .= ' pbp-'.replace_value_to_class(get_page_breadcrumbs_position());

  switch (get_page_breadcrumbs_position()) {
    case 'main_before':
    case 'footer_before':
      $classes .= ' wrap';
      break;
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_page_breadcrumbs_classes', $classes);
}
endif;


//エントリーカードのclass追加関数
if ( !function_exists( 'get_additional_entry_card_classes' ) ):
function get_additional_entry_card_classes($option = null){
  // //検索ページではクラス指定しない
  // if (is_search()) return;

  $classes = null;

  $classes .= ' ect-'.replace_value_to_class(get_entry_card_type());
  switch (get_entry_card_type()) {
    case 'vertical_card_2':
    case 'vertical_card_3':
    case 'tile_card_2':
    case 'tile_card_3':
      $classes .= ' ect-vertical-card';
      break;
  }
  switch (get_entry_card_type()) {
    case 'tile_card_2':
    case 'tile_card_3':
      $classes .= ' ect-tile-card';
      break;
  }
  if (is_entry_card_type_2_columns()) {
    $classes .= ' ect-2-columns';
  }
  if (is_entry_card_type_3_columns()) {
    $classes .= ' ect-3-columns';
  }

  //エントリーカードに枠線を付ける
  if (is_entry_card_border_visible()) {
    $classes .= ' ecb-entry-border';
  }

  //フロントページタイプ
  if (is_front_page_type_index()) {
    $classes .= ' '.get_front_page_type_class();
  }

  //スマートフォンでエントリーカードを1カラムに
  if (!is_entry_card_type_entry_card() && is_smartphone_entry_card_1_column()) {
    $classes .= ' sp-entry-card-1-column';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_entry_card_classes', $classes);
}
endif;


//目次のclass追加関数
if ( !function_exists( 'get_additional_toc_classes' ) ):
function get_additional_toc_classes($option = null){
  $classes = null;

  $classes .= ' tnt-'.replace_value_to_class(get_toc_number_type());

  if (is_toc_position_center()) {
    $classes .= ' toc-center';
  }

  $classes .= ' tnt-'.get_toc_number_type();

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_toc_classes', $classes);
}
endif;


//アピールエリアのclass追加関数
if ( !function_exists( 'get_additional_appeal_area_classes' ) ):
function get_additional_appeal_area_classes($option = null){
  $classes = null;

  $classes .= ' adt-'.replace_value_to_class(get_appeal_area_display_type());


  //背景画像の固定
  if (is_appeal_area_background_attachment_fixed()) {
    $classes .= ' ba-fixed';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_appeal_area_classes', $classes);
}
endif;


//カルーセルのclass追加関数
if ( !function_exists( 'get_additional_carousel_area_classes' ) ):
function get_additional_carousel_area_classes($option = null){
  $classes = null;


  //背景画像の固定
  if (is_carousel_card_border_visible()) {
    $classes .= ' ccb-carousel-border';
  }

  //スマートフォン表示
  if (!is_carousel_smartphone_visible()) {
    $classes .= ' sp-display-none';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return apply_filters('get_additional_carousel_area_classes', $classes);
}
endif;


//アピールエリアのclass追加関数
if ( !function_exists( 'get_additional_categories_tags_area_classes' ) ):
function get_additional_categories_tags_area_classes($option = null){
  $classes = null;

  $classes .= ' ctdt-'.replace_value_to_class(get_category_tag_display_type());

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;

//管理パネルエリアのclass追加関数
if ( !function_exists( 'get_additional_admin_panel_area_classes' ) ):
function get_additional_admin_panel_area_classes($option = null){
  $classes = null;

  $classes .= ' apdt-'.replace_value_to_class(get_admin_panel_display_type());

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;

//ポストクラスのフィルタ
add_filter( 'post_class', 'filter_post_class', 10, 3 );
if ( !function_exists( 'filter_post_class' ) ):
function filter_post_class( $classes, $class, $post_id ) {
  $i = 1;
  foreach ($classes as $value) {
    $classes[$i] = preg_replace('/^(category|tag)-.+/', '$0-post', $value);
    // if(preg_match('/^tag-/', $value)){

    //     //削除実行
    //     unset($classes[$i]);
    // }
    $i++;
  }
  // //削除実行
  // $classes = array_diff($classes, array('tag-link'));
  // //indexを詰める
  // $classes = array_values($classes);
  return $classes;
};
endif;

//おすすめカードのclass追加関数
if ( !function_exists( 'get_additional_recommend_cards_classes' ) ):
function get_additional_recommend_cards_classes($style = null, $is_margin = null, $option = null){
  $classes = null;

  $style = $style ? $style : get_recommended_cards_style();
  $is_margin = !is_null($is_margin) ? $is_margin : is_recommended_cards_margin_enable();

  switch ($style) {
    case 'image_only':
      $classes .= ' rcs-image-only';
      break;
    case RC_DEFAULT:
      $classes .= ' rcs-center-white-title rcs-center-title';
      break;
    case 'center_label_title':
      $classes .= ' rcs-center-label-title rcs-center-title';
      break;
    case ET_LARGE_THUMB_ON:
      $classes .= ' rcs-large-thumb-on';
      break;
  }

  if ($is_margin) {
    $classes .= ' rcs-card-margin';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;
