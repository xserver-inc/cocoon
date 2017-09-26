<?php //内部ブログカード関数

//内部URLからブログをカードタグの取得
if ( !function_exists( 'url_to_internal_blogcard_tag' ) ):
function url_to_internal_blogcard_tag($url){
  if ( !$url ) return;
  $url = strip_tags($url);//URL
  $id = url_to_postid( $url );//IDを取得（URLから投稿ID変換）
  if ( !$id ) return;//IDを取得できない場合はループを飛ばす

  //global $post;
  $post_data = get_post($id);
  setup_postdata($post_data);
  $exce = $post_data->post_excerpt;
  $no_image = get_site_screenshot_url($url);

  $title = $post_data->post_title;//タイトルの取得

  //メタディスクリプションの取得
  $snipet = get_the_meta_description();

  //ブログカードのサムネイルを右側に
  $thumbnail_class = null;
  // $thumbnail_class = ' internal-blogcard-thumbnail-left';
  // if ( is_blog_card_thumbnail_right() ) {
  //   $thumbnail_class = ' internal-blogcard-thumbnail-right';
  // }

  //新しいタブで開く場合
  $target = is_internal_blogcard_target_blank() ? ' target="_blank"' : '';

  //ファビコン
  $favicon_tag = '<span class="internal-blogcard-favicon"><img src="//www.google.com/s2/favicons?domain='.get_the_site_domain().'" class="internal-blogcard-favicon-img" alt="" width="16" height="16" /></span>';

  //サイトロゴ
  $site_logo_tag = '<span class="internal-blogcard-domain">'.get_the_site_domain().'</span>';
  $site_logo_tag = '<div class="internal-blogcard-site">'.$favicon_tag.$site_logo_tag.'</div>';


  $date = mysql2date('Y.m.d', $post_data->post_date);//投稿日の取得
  $date_tag = '<div class="internal-blogcard-date">'.$date.'</div>';

  //サムネイルの取得（要160×90のサムネイル設定）
  $thumbnail = get_the_post_thumbnail($id, 'thumb160', array('class' => 'internal-blogcard-thumb-image', 'alt' => ''));
  if ( !$thumbnail ) {//サムネイルが存在しない場合
    $thumbnail = '<img src="'.$no_image.'" alt="" class="internal-blogcard-thumb-image"'.get_noimage_sizes_attr($no_image).' />';
  }

  //取得した情報からブログカードのHTMLタグを作成
  $tag =
  '<a href="'.$url.'" class="internal-blogcard-wrap a-wrap cf"'.$target.'>'.
    '<div class="blogcard internal-blogcard'.$thumbnail_class.$wide_class.'">'.
      '<div class="internal-blogcard-thumbnail">'.$thumbnail.'</div>'.
      '<div class="internal-blogcard-content">'.
        '<div class="internal-blogcard-title">'.$title.'</div>'.
        '<div class="internal-blogcard-snipet">'.$snipet.'</div>'.
        '<div class="internal-blogcard-footer">'.
          $site_logo_tag.$hatebu_tag.$date_tag.
        '</div>'.
      '</div>'.
    '</div>'.
  '</a>';

  return $tag;
}
endif;

//本文中のURLをブログカードタグに変更する
if ( !function_exists( 'url_to_internal_blogcard' ) ):
function url_to_internal_blogcard($the_content) {
  if ( is_singular() ) {//投稿ページもしくは固定ページのとき
    //1行にURLのみが期待されている行（URL）を全て$mに取得

    $res = preg_match_all('/^(<p>)?(<br ? \/?>)?(<a.+?>)?https?:\/\/'.preg_quote(get_the_site_domain()).'\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+(<\/a>)?(<br ? \/?>)?(<\/p>)?/im', $the_content,$m);
    foreach ($m[0] as $match) {

      //マッチしたpタグが適正でないときはブログカード化しない
      if ( !is_p_tag_appropriate($match) ) {
        continue;
      }

      $url = strip_tags($match);//URL

      $tag = url_to_internal_blogcard_tag($url);

      if ( !$tag ) continue;//IDを取得できない場合はループを飛ばす

      //本文中のURLをブログカードタグで置換
      $the_content = preg_replace('{'.preg_quote($match).'}', $tag , $the_content, 1);
      wp_reset_postdata();

    }
  }
  return $the_content;//置換後のコンテンツを返す
}
endif;
if ( is_internal_blogcard_enable() ) {
  add_filter('the_content', 'url_to_internal_blogcard', 9999999);//本文表示をフック
  add_filter('widget_text', 'url_to_internal_blogcard', 9999999);//テキストウィジェットをフック
  add_filter('widget_text_pc_text', 'url_to_internal_blogcard', 9999999);
  add_filter('widget_classic_text', 'url_to_internal_blogcard', 9999999);
  add_filter('widget_text_mobile_text', 'url_to_internal_blogcard', 9999999);
}

//本文中のURLショートコードをブログカードタグに変更する
if ( !function_exists( 'url_shortcode_to_internal_blogcard' ) ):
function url_shortcode_to_internal_blogcard($the_content) {
  if ( is_singular() ) {//投稿ページもしくは固定ページのとき

    //1行にURLのみが期待されている行（URL）を全て$mに取得
    $res = preg_match_all('/(<p>)?(<br ? \/?>)?\[https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+\](<br ? \/?>)?(<\/p>)?/im', $the_content, $m);
    foreach ($m[0] as $match) {
    //マッチしたURL一つ一つをループしてカードを作成
      $url = strip_tags($match);//URL
      $url = preg_replace('/[\[\]]/', '', $url);//[と]の除去
      $url = str_replace('?', '%3F', $url);//?をエンコード

      //取得した内部URLからブログカードのHTMLタグを作成
      $tag = url_to_internal_blogcard_tag($url);//外部ブログカードタグに変換
      //URLをブログカードに変換
      if ( !$tag ) {//取得したURLが外部URLだった場合
        $tag = url_to_external_blog_card($url);//外部ブログカードタグに変換
      }

      if ( $tag ) {//内部・外部ブログカードどちらかでタグを作成できた場合
        //本文中のURLをブログカードタグで置換
        $the_content = preg_replace('{'.preg_quote($match).'}', $tag , $the_content, 1);
      }
    }
  }

  return $the_content;//置換後のコンテンツを返す
}
endif;
add_filter('the_content', 'url_shortcode_to_internal_blogcard' ,9999);
add_filter('widget_text', 'url_shortcode_to_internal_blogcard' ,9999);
add_filter('widget_text_pc_text', 'url_shortcode_to_internal_blogcard', 9999);
add_filter('widget_classic_text', 'url_shortcode_to_internal_blogcard', 9999);
add_filter('widget_text_mobile_text', 'url_shortcode_to_internal_blogcard', 9999);
add_filter('comment_text', 'url_shortcode_to_internal_blogcard', 9999);


//ブログカード置換用テキストにpタグが含まれているかどうか
if ( !function_exists( 'is_p_tag_appropriate' ) ):
function is_p_tag_appropriate($match){
  if (strpos($match,'p>') !== false){
    //pタグが含まれていた場合は開始タグと終了タグが揃っているかどうか
    if ( (strpos($match,'<p>') !== false) && (strpos($match,'</p>') !== false) ) {
      return true;
    }
    return false;
  }
  return true;
}
endif;
