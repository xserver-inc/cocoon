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
    $sep = get_title_separator_caption();
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

    if ( is_tagline_to_front_page_title() )://キャッチフレーズを追加する場合
      $title['tagline'] = trim( get_bloginfo('description') );
    endif;
  elseif(is_singular()): //投稿・固定ページ
    $title['title'] = trim( get_the_title() );
    //SEO向けのタイトルが設定されているとき
    // if (get_seo_title_singular_page()) {
    //   $title['title'] = get_seo_title_singular_page();
    // }
    $title['site'] = '';
    switch (get_singular_page_title_format()) {
      case 'pagetitle_sitename':
        $title['site'] = $site_name;
        break;
      case 'sitename_pagetitle':
        $title['title'] = $site_name;
        $title['site'] = trim( get_the_title() );
        break;
    }
    // if ( is_site_name_to_singular_title() )://サイト名を追加する場合
    //   $title['site'] = $site_name;
    // endif;
  elseif (is_category()):
    $cat_name = $title['title'];
    $title['site'] = '';
    switch (get_category_page_title_format()) {
      case 'category_sitename':
        $title['site'] = $site_name;
        break;
      case 'sitename_category':
        $title['title'] = $site_name;
        $title['site'] = $cat_name;
        break;
    }

  endif;

  return $title;
}
endif;
add_filter( 'document_title_parts', 'title_parts_custom' );

//noindexページの判別関数
if ( !function_exists( 'is_noindex_page' ) ):
function is_noindex_page(){
  return (is_archive() && !is_category()) || //アーカイブページはインデックスに含めない
  is_tag() || //タグページをインデックスしたい場合はこの行を削除
  ( is_paged() && is_paged_category_page_noindex() )  || //ページの2ページ目以降はインデックスに含めない（似たような内容の薄いコンテンツの除外）
  is_search() || //検索結果ページはインデックスに含めない
  is_404() || //404ページはインデックスに含めない
  is_attachment(); //添付ファイルページも含めない
}
endif;