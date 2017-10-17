<?php //スタイリング用の追加クラス関数

//フォントファミリークラスの取得
if ( !function_exists( 'get_site_font_family_class' ) ):
function get_site_font_family_class(){
  return 'ff-'.str_replace('_', '-', get_site_font_family());
}
endif;

//フォントサイズクラスの取得
if ( !function_exists( 'get_site_font_size_class' ) ):
function get_site_font_size_class(){
  return 'fz-'.get_site_font_size();
}
endif;


//bodyクラスの追加関数
add_filter('body_class', 'body_class_additional');
if ( !function_exists( 'body_class_additional' ) ):
function body_class_additional($classes) {
  global $post;

  //管理画面との差別用
  $classes[] = 'public-page';

  //カテゴリ入りクラスの追加
  if ( is_single() ) {
    foreach((get_the_category($post->ID)) as $category)
      $classes[] = 'categoryid-'.$category->cat_ID;
  }

  //フォントファミリー
  $classes[] = get_site_font_family_class();

  //フォントサイズ
  $classes[] = get_site_font_size_class();

  //サイドバー表示設定
  $add_no_sidebar = false;
  //var_dump(get_sidebar_display_type());
  switch (get_sidebar_display_type()) {
    case 'no_display_all':
      $add_no_sidebar = true;
      break;
    case 'no_display_front_page':
      if (is_front_page() && !is_home()) {
        $add_no_sidebar = true;
      }
      break;
    case 'no_display_index_pages':
      if (!is_singular()) {
        $add_no_sidebar = true;
      }
      break;
    case 'no_display_pages':
      if (is_page()) {
        $add_no_sidebar = true;
      }
      break;
    case 'no_display_singles':
      if (is_single()) {
        $add_no_sidebar = true;
      }
      break;
    default:
      //サイドバーにウィジェットが入っていない場合
      if (!is_active_sidebar( 'sidebar' )) {
        $add_no_sidebar = true;
      }
      break;
  }

  //vサイドバー非表示のフラグが立っている場合はクラスを追加
  if ($add_no_sidebar) {
    $classes[] = 'no-sidebar';
  }

  //サイドバー追従領域にウィジェットが入っていない場合
  if (!is_scrollable_sidebar_enable()) {
    $classes[] = 'no-scrollable-sidebar';
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

  return $classes;
}
endif;


//メインカラムの追加関数
if ( !function_exists( 'get_additional_main_classes' ) ):
function get_additional_main_classes($option = null){
  $classes = null;
  // //サイドバーにウィジェットが入っていない場合
  // if (!is_active_sidebar( 'sidebar' )) {
  //   $classes .= ' no-sidebar';
  // }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;}
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
    default:

      break;
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;

//エントリーカードの追加関数
if ( !function_exists( 'get_additional_new_entriy_cards_classes' ) ):
function get_additional_new_entriy_cards_classes($option = null){
  global $g_entry_type;
  $classes = null;
  if ($g_entry_type != ET_DEFAULT) {
    $classes .= ' not-default';
    if ($g_entry_type == ET_LARGE_THUMB) {
      $classes .= ' large-thumb';
    } else if ($g_entry_type == ET_LARGE_THUMB_ON) {
      $classes .= ' large-thumb-on';
    }
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;

//SNSシェアボタンの追加関数
if ( !function_exists( 'get_additional_sns_share_button_classes' ) ):
function get_additional_sns_share_button_classes($option = null){
  $classes = null;
  //カラム数
  if ($option == SS_TOP) {
    $value = get_sns_top_share_column_count();
  } else {
    $value = get_sns_share_column_count();
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
    $value = get_sns_share_logo_caption_position();
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
    $value = get_sns_share_button_color();
  }
  switch ($value) {
    case 'brand_color':
      $classes .= ' bc-brand-color';
      break;
    case 'brand_color_white':
      $classes .= ' bc-brand-color-white';
      break;
    default:

      break;
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;

//SNSフォローボタンのclass追加関数
if ( !function_exists( 'get_additional_sns_follow_button_classes' ) ):
function get_additional_sns_follow_button_classes($option = null){
  $classes = null;

  //ボタンカラー
  switch (get_sns_follow_button_color()) {
    case 'brand_color':
      $classes .= ' bc-brand-color';
      break;
    case 'brand_color_white':
      $classes .= ' bc-brand-color-white';
      break;
    default:

      break;
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
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
  return $classes;
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
  return $classes;
}
endif;


//ヘッダーのclass追加関数
if ( !function_exists( 'get_additional_header_classes' ) ):
function get_additional_header_classes($option = null){
  $classes = null;

  //ヘッダーを固定にする場合
  if (is_header_background_attachment_fixed()) {
    $classes .= ' hba-fixed';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;


//斎藤サイト全体コンテナのclass追加関数
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
  return $classes;
}
endif;


//ヘッダーコンテナのclass追加関数
if ( !function_exists( 'get_additional_header_container_classes' ) ):
function get_additional_header_container_classes($option = null){
  $classes = null;

  switch (get_header_layout_type()) {
    case 'top_menu':
      $classes .= ' hlt-top-menu wrap';
      break;
    case 'top_menu_small':
      $classes .= ' hlt-top-menu hlt-tm-small wrap';
      break;
    default://'center_logo'デフォルト
      $classes .= ' hlt-center-logo';
      break;
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
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

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;


//フッターボトムのclass追加関数
if ( !function_exists( 'get_additional_related_entries_classes' ) ):
function get_additional_related_entries_classes($option = null){
  $classes = null;
  switch (get_related_entry_type()) {
    case 'vartical_card_3':
    case 'vartical_card_4':
      $classes .= ' related-vartical-card';
      break;
  }
  $classes .= ' related-'.str_replace('_', '-', get_related_entry_type());
  if (is_related_entry_border_visible()) {
    $classes .= ' related-entry-border';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;


//フッターボトムのclass追加関数
if ( !function_exists( 'get_additional_post_navi_classes' ) ):
function get_additional_post_navi_classes($option = null){
  $classes = null;
  // switch (get_post_navi_type()) {
  //   case 'vartical_card_3':
  //   case 'vartical_card_4':
  //     $classes .= ' related-vartical-card';
  //     break;
  // }
  $classes .= ' post-navi-'.str_replace('_', '-', get_post_navi_type());
  if (is_post_navi_border_visible()) {
    $classes .= ' post-navi-border';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;


//フッターボトムのclass追加関数
if ( !function_exists( 'get_additional_comment_area_classes' ) ):
function get_additional_comment_area_classes($option = null){
  $classes = null;

  if (!is_comment_website_visible()) {
    $classes .= ' website-hide';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;


//投稿パンくずリストのclass追加関数
if ( !function_exists( 'get_additional_single_breadcrumbs_classes' ) ):
function get_additional_single_breadcrumbs_classes($option = null){
  $classes = null;

  $classes .= ' sbp-'.str_replace('_', '-', get_single_breadcrumbs_position());

  switch (get_single_breadcrumbs_position()) {
    case 'main_before':
    case 'footer_before':
      $classes .= ' wrap';
      break;
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;




