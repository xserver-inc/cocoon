<?php //外部ブログカード関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//外部URLからブログをカードタグの取得
if ( !function_exists( 'url_to_external_blog_card_tag' ) ):
function url_to_external_blog_card_tag($url){
  $url = strip_tags($url);//URL

  //サイトの内部リンクは処理しない場合（※wpForoページは外部リンクとして処理する）
  if ( includes_home_url($url) && !includes_wpforo_url($url) ) {
    $id = url_to_postid( $url );//IDを取得（URLから投稿ID変換
    if ( $id && get_post_status($id) ) {//IDを取得できる場合はループを飛ばす
      return;
    }//IDが取得できない場合は外部リンクとして処理する
  }

  //独自ブログカード（キャッシュ）の利用
  $tag = url_to_external_ogp_blogcard_tag($url);

  return $tag;
}
endif;

//本文中の外部URLをはてなブログカードタグに変更する
if ( !function_exists( 'url_to_external_blog_card' ) ):
function url_to_external_blog_card($the_content) {
  // return $the_content;
  // //ブロックエディターのブログカード用の本文整形
  // $the_content = fix_blogcard_content($the_content);
  //1行にURLのみが期待されている行（URL）を全て$mに取得
  $res = preg_match_all('/^(<p[^>]*?>)?(<a[^>]+?>)?https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+(<\/a>)?(?!.*<br *\/?>).*?(<\/p>)?/im', $the_content,$m);

  //マッチしたURL一つ一つをループしてカードを作成
  foreach ($m[0] as $match) {

    //マッチしたpタグが適正でないときはブログカード化しない
    if ( !is_p_tag_appropriate($match) ) {
      continue;
    }

    $url = strip_tags($match);//URL

    //ブログカード化しないURLで除外
    $exclude_urls = apply_filters('exclusion_external_blog_card_urls', array());
    foreach ($exclude_urls as $exclude_url) {
      if (includes_string($url, $exclude_url)) {
        return $match;
      }
    }

    $tag = url_to_external_blog_card_tag($url);

    if ( !$tag ) continue;

    //本文中のURLをブログカードタグで置換
    $the_content = preg_replace('{^'.preg_quote($match, '{}').'}im', "\n".$tag , $the_content, 1);
  }
  //ブログカード無効化の解除
  $the_content = cancel_blog_card_deactivation($the_content);
  $the_content = cancel_blog_card_deactivation($the_content, false);

  return $the_content;//置換後のコンテンツを返す
}
endif;
if ( is_external_blogcard_enable() ) {//外部リンクブログカードが有効のとき
  add_filter('the_content','url_to_external_blog_card', 11);//本文表示をフック
  add_filter('widget_text', 'url_to_external_blog_card', 11);//テキストウィジェットをフック
  add_filter('widget_text_pc_text', 'url_to_external_blog_card', 11);
  //add_filter('widget_classic_text', 'url_to_external_blog_card', 11);
  add_filter('widget_text_mobile_text', 'url_to_external_blog_card', 11);
  add_filter('the_category_tag_content', 'url_to_external_blog_card', 11);
  //コメント内ブログカード
  if (is_comment_external_blogcard_enable()) {
    add_filter('comment_text', 'url_to_external_blog_card', 11);
  }
}


//外部サイトからブログカードサムネイルを取得する
if ( !function_exists( 'fetch_card_image' ) ):
function fetch_card_image($image, $url = null){
  //var_dump($image);
  //URLの？以降のクエリを削除
  $image = preg_replace('/\?.*$/i', '', $image);
  $filename = substr($image, (strrpos($image, '/'))+1);
  $allow_exts = array('png', 'jpg', 'jpeg', 'gif' );

  //拡張子取得
  $ext = 'png';
  $temp_ext = get_extention($filename);
  if ( !in_array($temp_ext, $allow_exts) ) {
    return ;
  }

  if ( $temp_ext ) {
    $ext = $temp_ext;
  }

  //キャッシュディレクトリ
  $dir = get_theme_blog_card_cache_path();
  //画像の読み込み
  if ( $file_data = @wp_filesystem_get_contents($image, true) ) {

    //ディレクトリがないときには作成する
    if ( !file_exists($dir) ) {
      mkdir($dir, 0777);
    }
    //ローカル画像ファイルパス
    $new_file = $dir.md5($image).'.'.$ext;
    // var_dump($new_file);

    if ( $file_data ) {
      wp_filesystem_put_contents($new_file, $file_data);
      //画像編集オブジェクトの作成
      $image_editor = wp_get_image_editor($new_file);
      if ( !is_wp_error($image_editor) ){
        if (is_amazon_site_page($url)) {
          $width = apply_filters('external_blogcard_amazon_image_width',THUMB160WIDTH );
          $height = apply_filters('external_blogcard_amazon_image_height',THUMB160WIDTH );
          $image_editor->resize($width, $height, true);
        } else {
          $width = apply_filters('external_blogcard_image_width',THUMB160WIDTH );
          $height = apply_filters('external_blogcard_image_height',THUMB160HEIGHT );
          $image_editor->resize($width, $height, true);
        }

        $image_editor->save( $new_file );
        return str_replace(WP_CONTENT_DIR, content_url(), $new_file);
      }
      wp_filesystem_delete($new_file);
    }
  }
}
endif;

//外部サイトから直接OGP情報を取得してブログカードにする
if ( !function_exists( 'url_to_external_ogp_blogcard_tag' ) ):
function url_to_external_ogp_blogcard_tag($url){
  if ( !$url ) return;
  $url = strip_tags($url);//URL
  if (preg_match('/.+(\.mp3|\.midi|\.mp4|\.mpeg|\.mpg|\.jpg|\.jpeg|\.png|\.gif|\.svg|\.pdf)$/i', $url, $m)) {
    return;
  }
  $url = ampersand_urldecode($url);
  $params = get_url_params($url);
  $user_title = !empty($params['title']) ? $params['title'] : null;
  $user_snippet = !empty($params['snippet']) ? $params['snippet'] : null;
  //$url = add_query_arg(array('title' => null, 'snippet' => null), $url);
  //_v($url);

  $url_hash = TRANSIENT_BLOGCARD_PREFIX.md5( $url );
  $error_title = $url; //エラーの場合はURLを表示
  $title = $error_title;
  $error_image = get_site_screenshot_url($url);

  $image = $error_image;
  $snippet = '';
  $error_rel_nofollow = ' rel="nofollow"';



  require_once abspath(__FILE__).'open-graph.php';
  //ブログカードキャッシュ更新モード、もしくはログインユーザー以外のときはキャッシュの取得
  if ( !(is_external_blogcard_refresh_mode() && is_user_administrator()) ) {
    //保存したキャッシュを取得
    $ogp = get_transient( $url_hash );
  }

  if ( empty($ogp) ) {
    $ogp = OpenGraphGetter::fetch( $url );
    // _v($ogp);
    if ( $ogp == false ) {
      $ogp = 'error';
    } else {

      if (isset($ogp->image) && empty($ogp->image)) {
        //キャッシュ画像の取得
        $res = fetch_card_image($ogp->image, $url);

        if ( $res ) {
          $ogp->image = $res;
        }
      }

      if ( isset( $ogp->title ) && $ogp->title )
        $title = $ogp->title;//タイトルの取得

      if ( isset( $ogp->description ) && $ogp->description )
        $snippet = $ogp->description;//ディスクリプションの取得

      if ( isset( $ogp->image ) && $ogp->image )
        $image = $ogp->image;////画像URLの取得

      $error_rel_nofollow = null;
    }

    set_transient( $url_hash, $ogp,
                   DAY_IN_SECONDS * intval(get_external_blogcard_cache_retention_period()) );

  } elseif ( $ogp == 'error' ) {
    //前回取得したとき404ページだったら何も出力しない
  } else {
    if ( isset( $ogp->title ) && $ogp->title )
      $title = $ogp->title;//タイトルの取得

    if ( isset( $ogp->description ) && $ogp->description )
      $snippet = $ogp->description;//ディスクリプションの取得

    if ( isset( $ogp->image ) && $ogp->image )
      $image = $ogp->image;//画像URLの取得

    $error_rel_nofollow = null;
  }
  //var_dump($image);

  //ドメイン名を取得（OGP情報のURLが正しいかのチェック）
  $durl = punycode_decode($url);
  if (isset($ogp->url) && preg_match(URL_REG, $ogp->url)) {
    $durl = punycode_decode($ogp->url);
  }
  $domain = get_domain_name($durl);

  //og:imageが相対パスのとき
  if(!$image || (strpos($image, '//') === false) || (is_ssl() && (strpos($image, 'https:') === false))){    // //OGPのURL情報があるか
    //相対パスの時はエラー用の画像を表示
    $image = $error_image;
  }
  $title = strip_tags($title);
  if ($user_title) {
    $title = $user_title;
  }
  //タイトルのフック
  $title = apply_filters('cocoon_blogcard_title',$title);
  $title = apply_filters('cocoon_external_blogcard_title',$title);


  $image = strip_tags($image);

  $snippet = get_content_excerpt($snippet, get_entry_card_excerpt_max_length());
  $snippet = strip_tags($snippet);
  if ($user_snippet) {
    $snippet = $user_snippet;
  }
  $snippet = apply_filters( 'cocoon_blogcard_snippet', $snippet );
  $snippet = apply_filters( 'cocoon_external_blogcard_snippet', $snippet );

  //新しいタブで開く場合
  $target = is_external_blogcard_target_blank() ? ' target="_blank"' : '';

  $rel = '';
  if (is_external_blogcard_target_blank()) {
    $rel = ' rel="noopener"';
  }
  //コメント内でブログカード呼び出しが行われた際はnofollowをつける
  global $comment; //コメント内以外で$commentを呼び出すとnullになる
  if (is_external_blogcard_target_blank() && $comment) {
    $rel = ' rel="nofollow noopener"';
  }

  //GoogleファビコンAPIを利用する
  ////www.google.com/s2/favicons?domain=nelog.jp
  $favicon_tag = '<div class="blogcard-favicon external-blogcard-favicon">'.
    get_original_image_tag('https://www.google.com/s2/favicons?domain='.$durl, 16, 16, 'blogcard-favicon-image external-blogcard-favicon-image').
  '</div>';

  //サイトロゴ
  $site_logo_tag = '<div class="blogcard-domain external-blogcard-domain">'.$domain.'</div>';
  $site_logo_tag = '<div class="blogcard-site external-blogcard-site">'.$favicon_tag.$site_logo_tag.'</div>';

  //サムネイルを取得できた場合
  $image = apply_filters('get_external_blogcard_thumbnail_url', $image);
  if ( $image ) {
    $thumbnail = get_original_image_tag($image, THUMB160WIDTH, THUMB160HEIGHT, 'blogcard-thumb-image external-blogcard-thumb-image');
  }

  //取得した情報からブログカードのHTMLタグを作成
  $tag =
  '<a href="'.esc_url($url).'" title="'.esc_attr($title).'" class="blogcard-wrap external-blogcard-wrap a-wrap cf"'.$target.$rel.'>'.
    '<div class="blogcard external-blogcard'.get_additional_external_blogcard_classes().' cf">'.
      '<div class="blogcard-label external-blogcard-label">'.
        '<span class="fa"></span>'.
      '</div>'.
      '<figure class="blogcard-thumbnail external-blogcard-thumbnail">'.$thumbnail.'</figure>'.
      '<div class="blogcard-content external-blogcard-content">'.
        '<div class="blogcard-title external-blogcard-title">'.$title.'</div>'.
        '<div class="blogcard-snippet external-blogcard-snippet">'.$snippet.'</div>'.
      '</div>'.
      '<div class="blogcard-footer external-blogcard-footer cf">'.$site_logo_tag.'</div>'.
    '</div>'.
  '</a>';

  return $tag;
}
endif;
