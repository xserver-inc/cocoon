<?php //SEO関係の関数

//Wordpress4.1からのタイトル自動作成
//https://www.nxworld.net/wordpress/wp-custom-title-tag.html
add_action( 'after_setup_theme', 'setup_theme_custum' );
if ( !function_exists( 'setup_theme_custum' ) ):
function setup_theme_custum() {
  add_theme_support( 'title-tag' );
}
endif;

//Wordpress4.4以上でのタイトルセパレーターの設定
add_filter( 'document_title_separator', 'title_separator_custom' );
if ( !function_exists( 'title_separator_custom' ) ):
function title_separator_custom( $sep ){
    $sep = get_title_separator_caption();
    return $sep;
}
endif;


//Wordpress4.4以上でのタイトルカスタマイズ
add_filter( 'document_title_parts', 'title_parts_custom' );
if ( !function_exists( 'title_parts_custom' ) ):
function title_parts_custom( $title ){
  $site_name = trim( get_bloginfo('name') );
  $title['tagline'] = '';

  if(is_front_page()){ //フロントページ
    $title['title'] = $site_name;
    $title['site'] = '';

    if ( is_tagline_to_front_page_title() )://キャッチフレーズを追加する場合
      $title['tagline'] = trim( get_bloginfo('description') );
    endif;
  } elseif (is_singular()) { //投稿・固定ページ
    $title['title'] = trim( get_the_title() );
    //SEO向けのタイトルが設定されているとき
    if (get_the_page_seo_title()) {
      $title['title'] = get_the_page_seo_title();
    }
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
  } elseif (is_category()) {
    $cat_id = get_query_var('cat');
    $cat_name = $title['title'];
    if ($cat_id && get_category_title($cat_id)) {
      $cat_name = get_category_title($cat_id);
    }
    $title['site'] = '';
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
  } elseif (is_404()) {
    $title['title'] = get_404_page_title();
  };

  return $title;
}
endif;


//noindexページの判別関数
if ( !function_exists( 'is_noindex_page' ) ):
function is_noindex_page(){
  return (is_archive() && !is_tag() && !is_category() && !is_tax()) || //アーカイブページはインデックスに含めない
  ( is_tag() && is_tag_page_noindex() ) || //タグページをインデックスしたい場合はこの行を削除
  ( is_category() && is_paged() && is_paged_category_page_noindex() )  || //ページの2ページ目以降はインデックスに含めない（似たような内容の薄いコンテンツの除外）
  (is_attachment() && is_attachment_page_noindex()) || //添付ファイルページも含めない
  is_search() || //検索結果ページはインデックスに含めない
  is_404(); //404ページはインデックスに含めない
}
endif;


//noindexページを出力する
add_action( 'wp_head', 'the_noindex_follow_tag' );
if ( !function_exists( 'the_noindex_follow_tag' ) ):
function the_noindex_follow_tag(){
  $tag = null;
  if (is_noindex_page()) {
    $tag .= '<meta name="robots" content="noindex,follow">'.PHP_EOL;
  } elseif (is_singular()) {
    if ( is_the_page_noindex() && is_the_page_nofollow()) {
      $tag = '<meta name="robots" content="noindex,nofollow">'.PHP_EOL;
    } elseif ( is_the_page_noindex() ) {
      $tag = '<meta name="robots" content="noindex">'.PHP_EOL;
    } elseif ( is_the_page_nofollow() ) {
      $tag = '<meta name="robots" content="nofollow">'.PHP_EOL;
    }
  }
  if ($tag) {
    //var_dump($tag);
    $tag = '<!-- '.THEME_NAME_CAMEL.' noindex nofollow -->'.PHP_EOL.$tag;
    echo $tag;
  }
}
endif;

////ページネーションと分割ページ（マルチページ）タグを出力
if ( is_prev_next_enable() ) {
  //デフォルトのrel="next"/"prev"を消す
  remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
  //分割ページのみnext/prevを表示
  add_action( 'wp_head', 'the_prev_next_link_tag' );
}
if ( !function_exists( 'the_prev_next_link_tag' ) ):
function the_prev_next_link_tag() {
  //1ページを複数に分けた分割ページ
  if(is_single() || is_page()) {
    global $wp_query;
    global $multipage;
    //$multipage = check_multi_page();
    if($multipage) {
      $prev = generate_multipage_url('prev');
      $next = generate_multipage_url('next');
      if($prev) {
        echo '<!-- '.THEME_NAME_CAMEL.' prev -->'.PHP_EOL;
        echo '<link rel="prev" href="'.$prev.'" />'.PHP_EOL;
      }
      if($next) {
        echo '<!-- '.THEME_NAME_CAMEL.' next -->'.PHP_EOL;
        echo '<link rel="next" href="'.$next.'" />'.PHP_EOL;
      }
    }
  } else{
    //トップページやカテゴリページなどの分割ページの設定
    global $paged;
    if ( get_previous_posts_link() ){
      echo '<!-- '.THEME_NAME_CAMEL.' prev -->'.PHP_EOL;
      echo '<link rel="prev" href="'.get_pagenum_link( $paged - 1 ).'" />'.PHP_EOL;
    }
    if ( get_next_posts_link() ){
      echo '<!-- '.THEME_NAME_CAMEL.' next -->'.PHP_EOL;
      echo '<link rel="next" href="'.get_pagenum_link( $paged + 1 ).'" />'.PHP_EOL;
    }
  }
}
endif;

//分割ページ（マルチページ）URLの取得
//参考ページ：
//http://seophp.net/wordpress-fix-rel-prev-and-rel-next-without-plugin/
if ( !function_exists( 'generate_multipage_url' ) ):
function generate_multipage_url($rel='prev') {
  global $post;
  global $multipage;
  global $page;
  global $numpages;
  $url = '';
  //$multipage = check_multi_page();
  if($multipage) {
    //$numpages = $multipage[0];
    //$page = $multipage[1] == 0 ? 1 : $multipage[1];
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


//canonical URLの生成
if ( !function_exists( 'generate_canonical_url' ) ):
function generate_canonical_url(){
  global $paged;
  global $page;

  //canonicalの疑問点
  //アーカイブはnoindexにしているけどcanonicalタグは必要か？
  //タグページはnoindexにしているけどcanonicalタグは必要か？
  //404ページはAll in One SEO Packはcanonicalタグを出力していないようだけど必要か？
  $canonical_url = null;
  if (is_home()) {
    $canonical_url = home_url('/');
  } elseif (is_category()) {
    //$canonical_url = get_category_link(get_query_var('cat'));
    $canonical_url = get_query_removed_requested_url();
  } elseif (is_tag()) {
    // $postTag = get_the_tags();
    // $canonical_url = get_tag_link( $postTag[0]->term_id );
    $canonical_url = get_query_removed_requested_url();
  } elseif (is_singular()) {
    $canonical_url = get_permalink();
  // } elseif(is_404()) {
  //   $canonical_url =  home_url().'/404/';
  }

  if ($canonical_url && ( $paged >= 2 || $page >= 2)) {
    $canonical_url = get_query_removed_requested_url();
    // if (is_singular()) {
    //   $url = get_permalink();
    //   //最後が/でない場合
    //   $url = trailingslashit($url);
    //   $canonical_url = $url.max( $paged, $page ).'/';
    // } else {
    //   $url = $canonical_url;
    //   //最後が/でない場合
    //   $url = trailingslashit($url);
    //   $canonical_url = $url.'page/'.max( $paged, $page ).'/';
    // }
  }

  return $canonical_url;
}
endif;

//canonicalタグの取得
//取得条件；http://bazubu.com/seo101/how-to-use-canonical
if (is_canonical_tag_enable()) {
  //デフォルトのcanonicalタグ削除
  remove_action('wp_head', 'rel_canonical');
  //分割ページのみnext/prevを表示
  add_action( 'wp_head', 'generate_canonical_tag' );
}
if ( !function_exists( 'generate_canonical_tag' ) ):
function generate_canonical_tag(){
  $canonical_url = generate_canonical_url();
  // var_dump($canonical_url);
  if ( $canonical_url && !is_wpforo_plugin_page() ) {
    echo '<!-- '.THEME_NAME_CAMEL.' canonical -->'.PHP_EOL;
    echo '<link rel="canonical" href="'.$canonical_url.'">'.PHP_EOL;
  }
}
endif;


//カテゴリーメタディスクリプション用の説明文を取得
if ( !function_exists( 'get_category_meta_description' ) ):
function get_category_meta_description($category = null){
  //カテゴリー設定ページのディスクリプションを取得
  $cat_desc = trim( strip_tags( get_category_description() ) );
  if ( $cat_desc ) {//ディスクリプションが設定されている場合
    return htmlspecialchars($cat_desc);
  }

  //カテゴリ説明文を取得
  $cat_desc = trim( strip_tags( category_description() ) );
  if ( $cat_desc ) {//カテゴリ設定に説明がある場合はそれを返す
    return htmlspecialchars($cat_desc);
  }

  //カテゴリ本文から抜粋文を作成
  $cat_desc = trim( strip_tags( get_content_excerpt(get_category_content(), 160) ) );
  if ( $cat_desc ) {//カテゴリ設定に説明がある場合はそれを返す
    return htmlspecialchars($cat_desc);
  }

  //カテゴリ名から作成
  if ($category) {
    $cat_name = $category->name;
  } else {
    $cat_name = single_cat_title('', false);
  }

  $cat_desc = sprintf( __( '「%s」の記事一覧です。', THEME_NAME ), $cat_name );
  return htmlspecialchars($cat_desc);
}
endif;


//カテゴリーメタディスクリプション用の説明文を取得
if ( !function_exists( 'get_category_meta_keywords' ) ):
function get_category_meta_keywords(){
  if ($keywords = get_category_keywords()) {
    return $keywords;
  } else {
    return single_cat_title('', false);
  }
}
endif;


//投稿・固定ページのメタキーワードの取得
if ( !function_exists( 'get_the_meta_keywords' ) ):
function get_the_meta_keywords(){
  global $post;
  //var_dump(get_the_page_meta_keywords());
  $keywords =  get_the_page_meta_keywords();
  if (!$keywords) {
    $categories = get_the_category($post->ID);
    $category_names = array();
    foreach($categories as $category):
      array_push( $category_names, $category -> cat_name);
    endforeach ;
    $keywords = implode($category_names, ',');
  }
  return $keywords;
}
endif;

//メタディスクリプション文の取得
if ( !function_exists( 'get_meta_description_text' ) ):
function get_meta_description_text(){
  $description = null;

  if (is_front_page() && get_front_page_meta_description()) {
    $description = get_front_page_meta_description();
  } elseif (is_singular() && is_meta_description_to_singular()) {
    $description = get_the_meta_description();
  } elseif (is_category() && is_meta_description_to_category()) {
    $description = get_category_meta_description();
  }
  return apply_filters('meta_description_text', $description);
}
endif;

//メタディスクリプションタグを出力する
add_action( 'wp_head', 'generate_meta_description_tag' );
if ( !function_exists( 'generate_meta_description_tag' ) ):
function generate_meta_description_tag() {
  $description = get_meta_description_text();

  if ($description && !is_wpforo_plugin_page()) {
    echo '<!-- '.THEME_NAME_CAMEL.' meta description -->'.PHP_EOL;
    //var_dump('<meta name="description" content="'.$description.'">');
    echo '<meta name="description" content="'.$description.'">'.PHP_EOL;
  }
}
endif;

//メタキーワードテキストの取得
if ( !function_exists( 'get_meta_keywords_text' ) ):
function get_meta_keywords_text(){
  $keywords = null;
  //var_dump(get_the_meta_keywords());
  if (is_front_page() && get_front_page_meta_keywords()) {
    $keywords = get_front_page_meta_keywords();
  } elseif (is_singular() && is_meta_keywords_to_singular()) {
    $keywords = get_the_meta_keywords();
  } elseif (is_category() && is_meta_keywords_to_category()) {
    $keywords = get_category_meta_keywords();
  }
  return apply_filters('meta_keywords_text', $keywords);
}
endif;


//メタキーワードタグを出力する
add_action( 'wp_head', 'generate_meta_keywords_tag' );
if ( !function_exists( 'generate_meta_keywords_tag' ) ):
function generate_meta_keywords_tag() {
  $keywords = get_meta_keywords_text();

  if ($keywords && !is_wpforo_plugin_page()) {
    echo '<!-- '.THEME_NAME_CAMEL.' meta keywords -->'.PHP_EOL;
    //var_dump('<meta name="keywords" content="'.$keywords.'">');
    echo '<meta name="keywords" content="'.$keywords.'">'.PHP_EOL;
  }
}
endif;


//タグメタディスクリプション用の説明文を取得
if ( !function_exists( 'get_tag_meta_description' ) ):
function get_tag_meta_description(){
  $tag_desc = trim( strip_tags( tag_description() ) );
  if ( $tag_desc ) {//タグ設定に説明がある場合はそれを返す
    return htmlspecialchars($tag_desc);
  }
  $tag_desc = sprintf( __( '「%s」の記事一覧です。', 'simplicity2' ), single_cat_title('', false) );
  return htmlspecialchars($tag_desc);
}
endif;


//json-ldタグを出力する
add_action( 'wp_head', 'the_json_ld_tag' );
if ( !function_exists( 'the_json_ld_tag' ) ):
function the_json_ld_tag() {
  if (is_singular()) {
    echo '<!-- '.THEME_NAME_CAMEL.' JSON-LD -->'.PHP_EOL;
    get_template_part('tmp/json-ld');
  }
}
endif;


//サイト概要の取得
if ( !function_exists( 'get_the_meta_description' ) ):
function get_the_meta_description(){
  global $post;

  //抜粋を取得
  $desc = trim(strip_tags( $post->post_excerpt ));

  //投稿・固定ページにメタディスクリプションが設定してあれば取得
  if (get_the_page_meta_description()) {
    $desc = get_the_page_meta_description();
  }

  if ( !$desc ) {//投稿で抜粋が設定されていない場合は、110文字の冒頭の抽出分
    $desc = strip_shortcodes(get_the_snipet( $post->post_content, 150 ));
    $desc = mb_substr(str_replace(array("\r\n", "\r", "\n"), '', strip_tags($desc)), 0, 120);

  }
  $desc = htmlspecialchars($desc);
  return $desc;
}
endif;


//本文抜粋を取得する関数
if ( !function_exists( 'get_the_snipet' ) ):
function get_the_snipet($content, $length = 70) {
  global $post;

  //抜粋（投稿編集画面）の取得
  $description = $post->post_excerpt;

  //SEO設定のディスクリプション取得
  if (!$description) {
    $description = get_the_page_meta_description($post->ID);
  }

  //SEO設定のディスクリプションがない場合は「All in One SEO Packの値」を取得
  if (!$description) {
    $description = get_the_all_in_one_seo_pack_meta_description();
  }

  //SEO設定のディスクリプションがない場合は「抜粋」を取得
  if (!$description) {
    $description = htmlspecialchars(get_content_excerpt($content, $length));
  }
  return $description;
}
endif;

//本文抜粋を取得する関数
//使用方法：http://nelog.jp/get_the_snipet
if ( !function_exists( 'get_the_all_in_one_seo_pack_meta_description' ) ):
function get_the_all_in_one_seo_pack_meta_description() {
  global $post;
  if (class_exists( 'All_in_One_SEO_Pack' )) {
    $aioseop_description = get_post_meta($post->ID, '_aioseop_description', true);
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