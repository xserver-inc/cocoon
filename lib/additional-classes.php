<?php //スタイリング用の追加クラス関数

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

if ( !function_exists( 'replace_value_to_class' ) ):
function replace_value_to_class($value){
  return str_replace('_', '-', $value);;
}
endif;


//bodyクラスの追加関数
add_filter('body_class', 'body_class_additional');
if ( !function_exists( 'body_class_additional' ) ):
function body_class_additional($classes) {
  global $post;

  //管理画面との差別用
  $classes[] = 'public-page';
  //body
  $classes[] .= 'page-body';

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

      break;
  }

  //投稿管理画面で「1カラム」が選択されている場合
  if (is_singular() && is_singular_page_type_column1()) {
    $add_no_sidebar = true;
    $classes[] = 'column1';
  }

  //投稿管理画面で「本文のみ」が選択されている場合
  if (is_singular() && is_singular_page_type_content_only()) {
    $add_no_sidebar = true;
    $classes[] = 'content-only';
  }

  //投稿管理画面で「狭い」が選択されている場合
  if (is_singular() && is_singular_page_type_narrow()) {
    $add_no_sidebar = true;
    $classes[] = 'column-narrow';
  }

  //投稿管理画面で「広い」が選択されている場合
  if (is_singular() && is_singular_page_type_wide()) {
    $add_no_sidebar = true;
    $classes[] = 'column-wide';
  }

  //投稿管理画面で「デフォルト」以外が選択されている場合
  if (!is_singular_page_type_default()) {
    $classes[] = replace_value_to_class(get_singular_page_type());
  }

  //サイドバーにウィジェットが入っていない場合
  if (!is_active_sidebar( 'sidebar' )) {
    $add_no_sidebar = true;
  }

  //サイドバー非表示のフラグが立っている場合はクラスを追加
  if ($add_no_sidebar) {
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
      if (!(is_front_page() && !is_paged())) {
        $add_no_appeal_area = true;
      }
      break;
    //投稿・固定ページで表示しない
    case 'not_singular':
      if (is_singular()) {
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
      if (!(is_front_page() && !is_paged())) {
        $add_no_carousel = true;
      }
      break;
    //投稿・固定ページで表示しない
    case 'not_singular':
      if (is_singular()) {
        $add_no_carousel = true;
      }
      break;
  }

  //Live Writer用クラス
  if (is_user_agent_live_writer()) {
    $classes[] = 'live-writer';
  }

  //モバイルボタンタイプ
  $classes[] = 'mblt-'.replace_value_to_class(get_mobile_button_layout_type());

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

  //カルーセル表示のフラグが立っている場合はクラスを追加
  if ($add_no_carousel) {
    $classes[] = 'no-carousel';
  }

  //モバイルボタンはスライドインタイプか
  if (is_mobile_button_layout_type_slide_in()) {
    $classes[] = 'mobile-button-slide-in';
  }

  //サイトのサムネイルを表示するか
  if (!is_all_thumbnail_visible()) {
    $classes[] = 'no-thumbnail';
  }

  //投稿・固定ページの投稿日を表示するか
  if (is_singular() && !is_post_date_visible()) {
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

  //スマホ環境でスニペットを表示するか
  if (!is_smartphone_entry_card_snippet_visible()) {
    $classes[] = 'no-smartphone-snippet';
  }

  //Pinterestボタンを表示するか
  if (is_pinterest_share_button_visible() && is_singular()) {
    $classes[] = 'show-pinterest-button';
  }

  return $classes;
}//body_class_additional
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
function get_additional_new_entriy_cards_classes($entry_type, $option = null){
  $classes = null;
  if ($entry_type != ET_DEFAULT) {
    $classes .= ' not-default';
    if ($entry_type == ET_LARGE_THUMB) {
      $classes .= ' large-thumb';
    } else if ($entry_type == ET_LARGE_THUMB_ON) {
      $classes .= ' large-thumb-on';
    }
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;

//エントリーカードの追加関数
if ( !function_exists( 'get_additional_popular_entriy_cards_classes' ) ):
function get_additional_popular_entriy_cards_classes($entry_type, $ranking_visible, $option = null){
  // global $_ENTRY_TYPE;
  // global $_RANKING_VISIBLE;

  $classes = null;
  if ($entry_type != ET_DEFAULT) {
    $classes .= ' not-default';
    if ($entry_type == ET_LARGE_THUMB) {
      $classes .= ' large-thumb';
    } else if ($entry_type == ET_LARGE_THUMB_ON) {
      $classes .= ' large-thumb-on';
    }
  }

  if ($ranking_visible) {
    $classes .= ' ranking-visible';
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

  //フォロー数表示
  if (is_sns_follow_buttons_count_visible()) {
    $classes .= ' fbc-show';
  } else {
    $classes .= ' fbc-hide';
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
    $classes .= ' ba-fixed';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
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
  return $classes;
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

  //フッターナビメニューをテキスト幅に
  if (is_footer_navi_menu_text_width_enable()) {
    $classes .= ' fnm-text-width';
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
  $classes .= ' related-'.replace_value_to_class(get_related_entry_type());
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
  $classes .= ' post-navi-'.replace_value_to_class(get_post_navi_type());
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
  return $classes;
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
  return $classes;
}
endif;


//エントリーカードのclass追加関数
if ( !function_exists( 'get_additional_entry_card_classes' ) ):
function get_additional_entry_card_classes($option = null){
  $classes = null;

  $classes .= ' ect-'.replace_value_to_class(get_entry_card_type());
  switch (get_entry_card_type()) {
    case 'vertical_card_2':
    case 'vertical_card_3':
      $classes .= ' ect-vertical-card';
      break;
  }

  //エントリーカードに枠線を付ける
  if (is_entry_card_border_visible()) {
    $classes .= ' entry-card-border';
  }


  //スマートフォンでエントリーカードを1カラムに
  if (!is_entry_card_type_entry_card() && is_smartphone_entry_card_1_column()) {
    $classes .= ' sp-entry-card-1-column';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;


//目次のclass追加関数
if ( !function_exists( 'get_additional_toc_classes' ) ):
function get_additional_toc_classes($option = null){
  $classes = null;

  $classes .= ' tnt-'.replace_value_to_class(get_toc_number_type());
  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
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
  return $classes;
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

