<?php //SEO関係の関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//WordPress4.1からのタイトル自動作成
//https://www.nxworld.net/wordpress/wp-custom-title-tag.html
add_action( 'after_setup_theme', 'setup_theme_custum' );
if ( !function_exists( 'setup_theme_custum' ) ):
function setup_theme_custum() {
  add_theme_support( 'title-tag' );
}
endif;

//WordPress4.4以上でのタイトルセパレーターの設定
add_filter( 'document_title_separator', 'title_separator_custom' );
if ( !function_exists( 'title_separator_custom' ) ):
function title_separator_custom( $sep ){
    $sep = get_title_separator_caption();
    return $sep;
}
endif;


//WordPress4.4以上でのタイトルカスタマイズ
add_filter( 'document_title_parts', 'title_parts_custom' );
if ( !function_exists( 'title_parts_custom' ) ):
function title_parts_custom( $title ){
  $site_name = trim( get_bloginfo('name') );
  $title['tagline'] = '';

  if(is_front_page()){ //フロントページ
    //自由形式タイトルの場合
    if (is_free_front_page_title()) {
      $title['title'] = esc_html( get_free_front_page_title() );
    } else {//自由形式でないとき
      $title['title'] = $site_name;
      $title['site'] = '';

      if ( is_tagline_to_front_page_title() )://キャッチフレーズを追加する場合
        $title['tagline'] = trim( get_bloginfo('description') );
      endif;
    }

  } elseif (is_singular()) { //投稿・固定ページ
    $title['title'] = trim( get_the_title() );
    //SEO向けのタイトルが設定されているとき
    if (get_the_page_seo_title()) {
      $title['title'] = get_the_page_seo_title();
    }
    $title['site'] = '';
    if ($simplified_site_name = get_simplified_site_name()) {
      $site_name = $simplified_site_name;
    }
    switch (get_singular_page_title_format()) {
      case 'pagetitle_sitename':
        $title['site'] = $site_name;
        break;
      case 'sitename_pagetitle':
        $title['title'] = $site_name;
        $title['site'] = trim( get_the_title() );
        break;
    }
  } elseif (is_category()) {
    $cat_id = get_query_var('cat');
    $cat_name = $title['title'];
    if ($cat_id && get_the_category_title($cat_id)) {
      $cat_name = get_the_category_title($cat_id);
    }
    $title['title'] = $cat_name;
    $title['site'] = '';
    if ($simplified_site_name = get_simplified_site_name()) {
      $site_name = $simplified_site_name;
    }
    switch (get_category_page_title_format()) {
      case 'category_sitename':
        $title['title'] = $cat_name;
        $title['site'] = $site_name;
        break;
      case 'sitename_category':
        $title['title'] = $site_name;
        $title['site'] = $cat_name;
        break;
    }
  } elseif (is_tag() || is_tax()) {
    $tag_id = get_queried_object_id();
    $tag_name = $title['title'];
    if ($tag_id && get_the_tag_title($tag_id)) {
      $tag_name = get_the_tag_title($tag_id);
    }
    $title['title'] = $tag_name;
    $title['site'] = '';
    if ($simplified_site_name = get_simplified_site_name()) {
      $site_name = $simplified_site_name;
    }
    switch (get_category_page_title_format()) {//※カテゴリーと共通？
      case 'category_sitename':
        $title['title'] = $tag_name;
        $title['site'] = $site_name;
        break;
      case 'sitename_category':
        $title['title'] = $site_name;
        $title['site'] = $tag_name;
        break;
    }
  } elseif (is_404()) {
    $title['title'] = get_404_page_title();
  };

  return apply_filters('title_parts_custom', $title);
}
endif;


//noindexページの判別関数
if ( !function_exists( 'is_noindex_page' ) ):
function is_noindex_page(){
  $is_noindex = (is_archive() && !is_category() && !is_tag() && !is_tax() && is_other_archive_page_noindex()) || //アーカイブページはインデックスに含めない
  ( is_category() && (is_category_page_noindex() || get_the_category_noindex()) )  || //カテゴリーページ
  ( is_category() && is_paged() && is_paged_category_page_noindex() )  || //カテゴリーページ（2ページ目以降）
  ( is_tax() && (is_tag_page_noindex() || get_the_tag_noindex()) ) || //タクソノミ
  ( is_tag() && (is_tag_page_noindex() || get_the_tag_noindex()) ) || //タグページ（2ページ目以降）
  ( is_tag() && is_paged() && is_paged_tag_page_noindex() ) || //タグページ（2ページ目以降）
  (is_attachment() && is_attachment_page_noindex()) || //添付ファイルページも含めない
  is_search() || //検索結果ページはインデックスに含めない
  is_404(); //404ページはインデックスに含めない

  return apply_filters('is_noindex_page', $is_noindex);
}
endif;


//noindexページを出力する
// add_action( 'wp_head', 'the_noindex_follow_tag' );
if ( !function_exists( 'the_noindex_follow_tag' ) ):
function the_noindex_follow_tag(){
  // $tag = null;
  // if (is_noindex_page()) {
  //   $tag .= '<meta name="robots" content="noindex,follow">'.PHP_EOL;
  // } elseif (is_singular()) {
  //   if ( is_the_page_noindex() && is_the_page_nofollow()) {
  //     $tag = '<meta name="robots" content="noindex,nofollow">'.PHP_EOL;
  //   } elseif ( is_the_page_noindex() ) {
  //     $tag = '<meta name="robots" content="noindex">'.PHP_EOL;
  //   } elseif ( is_the_page_nofollow() ) {
  //     $tag = '<meta name="robots" content="nofollow">'.PHP_EOL;
  //   }
  // }
  // if ($tag) {
  //   //var_dump($tag);
  //   $tag = '<!-- '.THEME_NAME_CAMEL.' noindex nofollow -->'.PHP_EOL.$tag;
  //   echo $tag;
  // }
}
endif;

// noindex, nofollow関係はwp_robotsフックに任せる
add_filter( 'wp_robots', 'wp_robots_tag_custom' );
if ( !function_exists( 'wp_robots_tag_custom' ) ):
function wp_robots_tag_custom( array $robots ) {
  // WordPress 設定 > 表示設定で「検索エンジンがサイトをインデックスしないようにする」が無効（デフォルト）の場合はテーマのnoindex設定をする
  if (get_option( 'blog_public' )) {
    if ( is_noindex_page() ) {
      $robots['noindex'] = true;
      $robots['follow'] = true;
      $robots['max-image-preview'] = false;
    } elseif (is_singular()) {
      if ( is_the_page_noindex() && is_the_page_nofollow()) {
        $robots['noindex'] = true;
        $robots['nofollow'] = true;
        $robots['max-image-preview'] = false;
      } elseif ( is_the_page_noindex() ) {
        $robots['noindex'] = true;
        $robots['follow'] = true;
        $robots['max-image-preview'] = false;
      } elseif ( is_the_page_nofollow() ) {
        $robots['nofollow'] = true;
      }
    }
  }

  return $robots;
}
endif;

//ページネーションと分割ページ（マルチページ）タグを出力
if ( is_prev_next_enable() ) {
  //デフォルトのrel="next"/"prev"を消す
  remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
  //分割ページのみnext/prevを表示
  add_action( 'wp_head', 'the_prev_next_link_tag' );
}
if ( !function_exists( 'the_prev_next_link_tag' ) ):
function the_prev_next_link_tag() {
  //1ページを複数に分けた分割ページ
  if(is_singular()) {
    global $multipage;
    if($multipage) {
      $prev = get_multipage_url('prev');
      $prev_url = user_trailingslashit($prev);
      $next = get_multipage_url('next');
      $next_url = user_trailingslashit($next);
      if($prev) {
        echo '<!-- '.THEME_NAME_CAMEL.' prev -->'.PHP_EOL;
        echo '<link rel="prev" href="'.esc_url($prev_url).'" />'.PHP_EOL;
      }
      if($next) {
        echo '<!-- '.THEME_NAME_CAMEL.' next -->'.PHP_EOL;
        echo '<link rel="next" href="'.esc_url($next_url).'" />'.PHP_EOL;
      }
    }
  } else if (is_search()){
    //トップページやカテゴリーページなどの分割ページの設定
    global $paged;
    $search_query = htmlspecialchars(get_search_query());
    if ( get_previous_posts_link() ){
      $page = $paged - 1;
      $url = user_trailingslashit(get_site_url()).'?s='.$search_query.'&amp;paged='.$page;
      echo '<!-- '.THEME_NAME_CAMEL.' prev -->'.PHP_EOL;
      echo '<link rel="prev" href="'.esc_url($url).'" />'.PHP_EOL;
    }
    if ( get_next_posts_link() ){
      $page = $paged + 1;
      $url = user_trailingslashit(get_site_url()).'?s='.$search_query.'&amp;paged='.$page;
      echo '<!-- '.THEME_NAME_CAMEL.' next -->'.PHP_EOL;
      echo '<link rel="next" href="'.esc_url($url).'" />'.PHP_EOL;
    }
  } else if (!is_404()){
    //トップページやカテゴリーページなどの分割ページの設定
    global $paged;
    if ( get_previous_posts_link() ){
      $url = user_trailingslashit(get_pagenum_link( $paged - 1 ));
      echo '<!-- '.THEME_NAME_CAMEL.' prev -->'.PHP_EOL;
      echo '<link rel="prev" href="'.esc_url($url).'" />'.PHP_EOL;
    }
    if ( get_next_posts_link() ){
      $url = user_trailingslashit(get_pagenum_link( $paged + 1 ));
      echo '<!-- '.THEME_NAME_CAMEL.' next -->'.PHP_EOL;
      echo '<link rel="next" href="'.esc_url($url).'" />'.PHP_EOL;
    }
  }
}
endif;

//分割ページ（マルチページ）URLの取得
//参考ページ：
//http://seophp.net/wordpress-fix-rel-prev-and-rel-next-without-plugin/
if ( !function_exists( 'get_multipage_url' ) ):
function get_multipage_url($rel='prev') {
  global $post;
  global $multipage;
  global $page;
  global $numpages;
  $url = '';
  if($multipage) {
    $i = 'prev' == $rel? $page - 1: $page + 1;
    if($i && $i > 0 && $i <= $numpages) {
      if(1 == $i) {
        $url = get_permalink();
      } else {
        if ('' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending'))) {
          $url = add_query_arg('page', $i, get_permalink());
        } else {
          $url = trailingslashit(get_permalink()).user_trailingslashit($i, 'single_paged');
        }
      }
    }
  }
  return $url;
}
endif;


//分割ページ（マルチページ）かチェックする
if ( !function_exists( 'get_the_post_has_multi_page' ) ):
function get_the_post_has_multi_page() {
  $num_pages    = substr_count(
      $GLOBALS['post']->post_content,
      '<!--nextpage-->'
  ) + 1;
  $current_page = get_query_var( 'page' );
  return array ( $num_pages, $current_page );
}
endif;

//ページ情報つきURLを返す
if ( !function_exists( 'get_paged_archive_url' ) ):
function get_paged_archive_url($url, $page_num = null){
  if (is_paged()) {
    global $paged;
    if (empty($page_num)) {
      $page_num = $paged;
    }
    $url = trailingslashit($url);
    $url = user_trailingslashit($url.'page/'.$page_num);
  }
  return $url;
}
endif;

//表示したページのベースURLを取得する
if ( !function_exists( 'get_page_base_url' ) ):
function get_page_base_url(){
  $url = null;
  if (is_home()) {
    $url = home_url();
  } elseif (is_category()) {
    $url = get_category_link(get_query_var('cat'));
  } elseif (is_tag()) {
    $postTag = get_the_tags();
    $url = get_tag_link( $postTag[0]->term_id );
  } elseif (is_singular()) {
    $url = get_permalink();
  } elseif(is_404()) {
    $url =  home_url().'/404/';
  } else {
    $url = get_query_removed_requested_url();
  }

  return $url;
}
endif;

//canonical URLの生成
if ( !function_exists( 'generate_canonical_url' ) ):
function generate_canonical_url(){
  global $paged;
  global $page;
  global $multipage;
  //canonicalの疑問点
  //アーカイブはnoindexにしているけどcanonicalタグは必要か？
  //タグページはnoindexにしているけどcanonicalタグは必要か？
  //404ページはAll in One SEO Packはcanonicalタグを出力していないようだけど必要か？
  $canonical_url = null;
  //投稿・固定ページの場合のcanonical URL
  $the_page_canonical_url = get_the_page_canonical_url();
  if (is_home() && is_paged()) {
    $canonical_url = get_query_removed_requested_url();
  } elseif (is_front_page()) {
    $canonical_url = home_url();
  } elseif (is_category()) {
    //カテゴリーページのみcocoon.dev/category/hoge/catname/page/2/といったURLの対策が必要
    $canonical_url = get_category_link(get_query_var('cat'));
    $canonical_url = get_paged_archive_url($canonical_url);
  } elseif (is_tag()) {
    $canonical_url = get_query_removed_requested_url();
  } elseif (is_singular() && !$multipage) {
    if ($the_page_canonical_url) {
      $canonical_url = $the_page_canonical_url;
    } else {
      $canonical_url = get_permalink();
    }
  } elseif (is_singular() && ($paged >= 2 || $page >= 2)) {
    if ($the_page_canonical_url) {
      $canonical_url = $the_page_canonical_url;
    } else {
      $canonical_url = get_query_removed_requested_url();
    }
  } elseif (is_singular()) {//この条件分岐は不要かも
    if ($the_page_canonical_url) {
      $canonical_url = $the_page_canonical_url;
    } else {
      $canonical_url = get_permalink();
    }
  } elseif(is_404()) {
    $canonical_url =  home_url().'/404/';
  } else {
    $canonical_url = get_query_removed_requested_url();
  }

  $canonical_url = user_trailingslashit($canonical_url);

  return apply_filters('generate_canonical_url', $canonical_url);
}
endif;

//canonicalタグの取得
//取得条件；http://bazubu.com/seo101/how-to-use-canonical
if (is_canonical_tag_enable()) {
  //デフォルトのcanonicalタグ削除
  remove_action('wp_head', 'rel_canonical');
  //canonicalタグを表示
  add_action( 'wp_head', 'generate_canonical_tag' );
}
if ( !function_exists( 'generate_canonical_tag' ) ):
function generate_canonical_tag(){
  $canonical_url = generate_canonical_url();
  // var_dump($canonical_url);
  if ( $canonical_url && !is_wpforo_plugin_page() ) {
    echo '<!-- '.THEME_NAME_CAMEL.' canonical -->'.PHP_EOL;
    echo '<link rel="canonical" href="'.esc_url($canonical_url).'">'.PHP_EOL;
  }
}
endif;


//カテゴリーメタディスクリプション用の説明文を取得
if ( !function_exists( 'get_category_meta_description' ) ):
function get_category_meta_description($category = null){
  if ( !$category && is_category() ) {
    $category = get_queried_object();
  }

  //カテゴリー名から作成
  if ( $category ) {
    $cat_name = $category->name;
  } else {
    $cat_name = single_cat_title('', false);
  }
  //デフォルトのカテゴリ名
  $desc = sprintf( __( '「%s」の記事一覧です。', THEME_NAME ), $cat_name );


  //カテゴリー本文から抜粋文を作成
  $tmp_desc = get_the_category_content(null, true);
  if ( $tmp_desc ) {
    $desc = get_content_excerpt( $tmp_desc, 120 );//get_content_excerptはデフォルトでも120文字だが明示的に記入
  }

  //カテゴリ説明文を取得
  $tmp_desc = category_description();
  if ( $tmp_desc ) {
    $desc = get_content_excerpt( $tmp_desc, 120 );
  }

  //カテゴリー設定ページのディスクリプションを取得
  $tmp_desc = get_the_category_meta_description();
  if ( $tmp_desc ) {
    $desc = get_content_excerpt( $tmp_desc, 1000 );//メタディスクリプションは基本全部出力するが最大1000文字まで
  }

  $desc = htmlspecialchars($desc);
  return apply_filters('get_category_meta_description', $desc);
}
endif;


//カテゴリーメタディスクリプション用の説明文を取得
if ( !function_exists( 'get_category_meta_keywords' ) ):
function get_category_meta_keywords(){
  if ($keywords = get_the_category_meta_keywords()) {
    $res = $keywords;
  } else {
    $res = single_cat_title('', false);
  }
  $res = htmlspecialchars($res);
  return apply_filters('get_category_meta_keywords', $res);
}
endif;


//投稿・固定ページのメタキーワードの取得
if ( !function_exists( 'get_the_meta_keywords' ) ):
function get_the_meta_keywords(){
  global $post;
  //var_dump(get_the_page_meta_keywords());
  $keywords =  get_the_page_meta_keywords();
  if (!$keywords) {
    //IDを取得
    $post_id = null;
    if (isset($post->ID)) {
      $post_id = $post->ID;
    }
    $categories = get_the_category($post_id);
    $category_names = array();
    foreach($categories as $category):
      array_push( $category_names, $category -> cat_name);
    endforeach ;
    $keywords = implode(',', $category_names);
  }
  $keywords = htmlspecialchars($keywords);
  return apply_filters('get_the_meta_keywords', $keywords);
}
endif;

//メタディスクリプション文の取得
if ( !function_exists( 'get_meta_description_text' ) ):
function get_meta_description_text(){
  //generate_meta_description_tag関数でメタディスクリプションが空のときメタディスクリプションを出力しないようにするため、デフォルトは空文字
  $description = '';
  if (is_front_top_page()) {
    if (is_meta_description_to_front_page()) {
      $description = get_front_page_meta_description();
      if (!$description) {
        $description = get_bloginfo('description');
      }
    }
  } elseif (is_singular() && is_meta_description_to_singular()) {
    //パスワードが必要な時は何も出力しない
    if (!post_password_required()) {
      $description = get_the_meta_description();
    }
  } elseif (is_category() && is_meta_description_to_category()) {
    $description = get_category_meta_description();
  } elseif ((is_tag() || is_tax()) && is_meta_description_to_category()) {
    $description = get_tag_meta_description();
  }
  if ($description) {
    $description = htmlspecialchars($description);
  }
  return apply_filters('get_meta_description_text', $description);
}
endif;

//OGPディスクリプション文の取得
if ( !function_exists( 'get_ogp_description_text' ) ):
function get_ogp_description_text(){
  $description = get_bloginfo('description');
  if (is_front_top_page()) {
    if (get_front_page_meta_description()) {
      $description = get_front_page_meta_description();
    } else {
      $description = get_bloginfo('description');
    }
  } elseif (is_singular()) {
    if (!post_password_required()) {
      $description = get_the_meta_description();
    }
  } elseif (is_category()) {
    $description = get_category_meta_description();
  } elseif ((is_tag() || is_tax())) {
    $description = get_tag_meta_description();
  }
  if ($description) {
    $description = htmlspecialchars($description);
  }
  return apply_filters('get_ogp_description_text', $description);
}
endif;

//メタディスクリプションタグを出力する
add_action( 'wp_head', 'generate_meta_description_tag' );
if ( !function_exists( 'generate_meta_description_tag' ) ):
function generate_meta_description_tag() {
  $description = get_meta_description_text();

  if ($description && !is_wpforo_plugin_page()) {
    echo '<!-- '.THEME_NAME_CAMEL.' meta description -->'.PHP_EOL;
    echo '<meta name="description" content="'.esc_attr($description).'">'.PHP_EOL;
  }
}
endif;

//メタキーワードテキストの取得
if ( !function_exists( 'get_meta_keywords_text' ) ):
function get_meta_keywords_text(){
  $keywords = null;
  if (is_front_page() && is_meta_keywords_to_front_page()) {
    $keywords = get_front_page_meta_keywords();
  } elseif (is_singular() && is_meta_keywords_to_singular()) {
    $keywords = get_the_meta_keywords();
  } elseif (is_category() && is_meta_keywords_to_category()) {
    $keywords = get_category_meta_keywords();
  } elseif (is_tag() && is_meta_keywords_to_category()) {//※カテゴリーページのメタタグ設定と共通？（※今後要検討）
    $keywords = get_tag_meta_keywords();
  }
  if ($keywords) {
    $keywords = htmlspecialchars($keywords);
  }
  return apply_filters('get_meta_keywords_text', $keywords);
}
endif;


//メタキーワードタグを出力する
add_action( 'wp_head', 'generate_meta_keywords_tag' );
if ( !function_exists( 'generate_meta_keywords_tag' ) ):
function generate_meta_keywords_tag() {
  $keywords = get_meta_keywords_text();

  if ($keywords && !is_wpforo_plugin_page()) {
    echo '<!-- '.THEME_NAME_CAMEL.' meta keywords -->'.PHP_EOL;
    echo '<meta name="keywords" content="'.esc_attr($keywords).'">'.PHP_EOL;
  }
}
endif;


//タグメタディスクリプション用の説明文を取得
if ( !function_exists( 'get_tag_meta_description' ) ):
function get_tag_meta_description($tag = null){
  //タグ名から作成
  if ($tag) {
    $tag_name = $tag->name;
  } else {
    $tag_name = single_tag_title('', false);
  }
  //デフォルトのタグ名
  $desc = sprintf( __( '「%s」の記事一覧です。', THEME_NAME ), $tag_name );

  //タグ本文から抜粋文を作成
  $tmp_desc = get_the_tag_content(null, true);
  if ( $tmp_desc ) {
    $desc = get_content_excerpt( $tmp_desc, 120 );//get_content_excerptはデフォルトでも120文字だが明示的に記入
  }

  //タグ説明文を取得
  $tmp_desc = tag_description();
  if ( $tmp_desc ) {
    $desc = get_content_excerpt( $tmp_desc, 120 );
  }

  //タグ設定ページのディスクリプションを取得
  $tmp_desc = get_the_tag_meta_description();
  if ( $tmp_desc ) {
    $desc = get_content_excerpt( $tmp_desc, 1000 );//メタディスクリプションは基本全部出力するが最大1000文字まで
  }

  $desc = htmlspecialchars($desc);
  return apply_filters('get_tag_meta_description', $desc);
}
endif;

//タグキーワード用のワードを取得
if ( !function_exists( 'get_tag_meta_keywords' ) ):
function get_tag_meta_keywords(){
  if ($keywords = get_the_tag_meta_keywords()) {
    $res = $keywords;
  } else {
    $res = single_tag_title('', false);
  }
  $res = htmlspecialchars($res);
  return apply_filters('get_tag_meta_keywords', $res);
}
endif;

//メタサムネイルを出力する
add_action( 'wp_head', 'generate_meta_thumbnail_tag' );
if ( !function_exists( 'generate_meta_thumbnail_tag' ) ):
function generate_meta_thumbnail_tag() {
  if (is_singular()) {
    $thumbnail_url = get_singular_eyecatch_image_url();

    if ($thumbnail_url && !is_wpforo_plugin_page()) {
      echo '<!-- '.THEME_NAME_CAMEL.' meta thumbnail -->'.PHP_EOL;
      echo '<meta name="thumbnail" content="'.esc_attr($thumbnail_url).'">'.PHP_EOL;
    }
  }
}
endif;

//json-ldタグを出力する
add_action( 'wp_head', 'the_json_ld_tag' );
if ( !function_exists( 'the_json_ld_tag' ) ):
function the_json_ld_tag() {
  if (is_singular() && is_json_ld_tag_enable()) {
    echo '<!-- '.THEME_NAME_CAMEL.' JSON-LD -->'.PHP_EOL;
    cocoon_template_part('tmp/json-ld');
    if (is_the_page_review_enable()) {
      echo '<!-- '.THEME_NAME_CAMEL.' Review JSON-LD -->'.PHP_EOL;
      cocoon_template_part('tmp/json-ld-review');
    }
  }
}
endif;


//サイト概要の取得
if ( !function_exists( 'get_the_meta_description' ) ):
function get_the_meta_description(){
  global $post;

  //内容を取得
  $tmp_content = '';
  if (isset($post->post_content)) {
    $tmp_content = $post->post_content;
  }

  //get_content_excerptはデフォルトでも120文字だが明示的に記入
  $desc = get_content_excerpt(get_the_snippet( $tmp_content, 120 ), 120);

  //抜粋を取得
  $tmp_desc = '';
  if (isset($post->post_excerpt)) {
    $tmp_desc = $post->post_excerpt;
  }

  if ( $tmp_desc ) {
    $desc = get_content_excerpt( $tmp_desc, 120 );
  }

  //投稿・固定ページにメタディスクリプションが設定してあれば取得
  $tmp_desc = get_the_page_meta_description();
  if ( $tmp_desc ) {
    $desc = get_content_excerpt( $tmp_desc, 1000 );//メタディスクリプションは基本全部出力するが最大1000文字まで
  }

  $desc = htmlspecialchars($desc);
  return apply_filters('get_the_meta_description', $desc);
}
endif;


//本文抜粋を取得する関数
if ( !function_exists( 'get_the_snippet' ) ):
function get_the_snippet($content, $length = 70, $post_id = null) {
  global $post;
  $tmp_post = $post;

  $description = null;

  //$post_idが指定されている場合は、その内容に変更
  if ($post_id) {
    $tmp_post = get_post($post_id);
  }

  //抜粋（投稿編集画面）の取得
  if ($tmp_post && isset($tmp_post->post_excerpt)) {
    $description = $tmp_post->post_excerpt;
  }

  //SEO設定のディスクリプション取得
  if (!$description) {
    if ($tmp_post && isset($tmp_post->ID)) {
      $description = get_the_page_meta_description($tmp_post->ID);
    }
  }

  //SEO設定のディスクリプションがない場合は「All in One SEO Packの値」を取得
  if (!$description) {
    if ($tmp_post && isset($tmp_post->ID)) {
      $description = get_the_all_in_one_seo_pack_meta_description($post_id);
    }
  }
  //改行を除去
  $description = $description ?? '';
  $description = str_replace(array("\r\n", "\r", "\n"), '', $description);

  //SEO設定のディスクリプションがない場合は「本文からの自動生成抜粋」を取得
  if (!$description) {
    $description = get_content_excerpt($content, $length);
    $description = str_replace('<', '&lt;', $description);
    $description = str_replace('>', '&gt;', $description);
  }
  return apply_filters( 'get_the_snippet', $description, $tmp_post );
}
endif;

//本文抜粋を取得する関数
//使用方法：http://nelog.jp/get_the_snippet
if ( !function_exists( 'get_the_all_in_one_seo_pack_meta_description' ) ):
function get_the_all_in_one_seo_pack_meta_description($id = null) {
  global $post;
  if (!$id && isset($post->ID)) {
    $id = $post->ID;
  }
  if (class_exists( 'All_in_One_SEO_Pack' )) {
    $aioseop_description = get_post_meta($id, '_aioseop_description', true);
    if ($aioseop_description) {
      return $aioseop_description;
    }
  }
}
endif;

//SEO的な投稿日取得
if ( !function_exists( 'get_seo_post_time' ) ):
function get_seo_post_time(){
  $update_time = get_update_time('c');
  if (is_seo_date_type_update_date_only() && $update_time) {
    $res = $update_time;
  } else {
    $res = get_the_time('c');
  }
  return $res;
}
endif;

//SEO的な更新日取得
if ( !function_exists( 'get_seo_update_time' ) ):
function get_seo_update_time(){
  $update_time = get_update_time('c');
  if (is_seo_date_type_post_date_only() || !$update_time) {
    $res = get_the_time('c');
  } else {
    $res = $update_time;
  }
  return $res;
}
endif;
