<?php //SEO関係の関数


//Wordpress4.1からのタイトル自動作成
//https://www.nxworld.net/wordpress/wp-custom-title-tag.html
if ( !function_exists( 'setup_theme_custum' ) ):
function setup_theme_custum() {
  add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'setup_theme_custum' );
endif;

//Wordpress4.4以上でのタイトルセパレーターの設定
if ( !function_exists( 'title_separator_custom' ) ):
function title_separator_custom( $sep ){
    $sep = ' | ';
    return $sep;
}
endif;
add_filter( 'document_title_separator', 'title_separator_custom' );


//Wordpress4.4以上でのタイトルカスタマイズ
if ( !function_exists( 'title_parts_custom' ) ):
function title_parts_custom( $title ){
  $site_name = trim( get_bloginfo('name') );
  $title['tagline'] = '';

  if(is_front_page()): //フロントページ
    $title['title'] = $site_name;
    $title['site'] = '';
    // if ( is_catch_phrase_to_frontpage_title() )://キャッチフレーズを追加する場合
    //   $title['tagline'] = trim( get_bloginfo('description') );
    // endif;
  elseif(is_singular()): //投稿・固定ページ
    $title['title'] = trim( get_the_title() );
    //SEO向けのタイトルが設定されているとき
    // if (get_seo_title_singular_page()) {
    //   $title['title'] = get_seo_title_singular_page();
    // }
    $title['site'] = '';
    // if ( is_site_name_to_singular_title() )://サイト名を追加する場合
    //   $title['site'] = $site_name;
    // endif;
  endif;

  return $title;
}
endif;
add_filter( 'document_title_parts', 'title_parts_custom' );

// //タイトル自動作成をフックして変更したい部分を変更する
// if ( !function_exists( 'wp_title_custom' ) ):
// function wp_title_custom( $title ) {
//   global $paged, $page;

//   if ( is_feed() ) {
//     return $title;
//   }

//   $site_name = trim( get_bloginfo('name') );
//   if(is_front_page()):
//     $title = $site_name;
//     // if ( is_catch_phrase_to_frontpage_title() )://キャッチフレーズを追加する場合
//     //    $title = $title. ' | ' . trim( get_bloginfo('description') );
//     // endif;
//   elseif(is_singular()):
//     $title = trim( get_the_title() );

//     // //SEO向けのタイトルが設定されているとき
//     // if (get_seo_title_singular_page()) {
//     //   $title = get_seo_title_singular_page();
//     // }
//     // if ( is_site_name_to_singular_title() )://サイト名を追加する場合
//     //    $title = $title. ' | ' . $site_name;
//     // endif;
//   endif;

//   return $title;
// }
// endif;
// //Wordpress4.4未満
// add_filter( 'wp_title', 'wp_title_custom');