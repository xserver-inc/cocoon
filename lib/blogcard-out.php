<?php //外部ブログカード関数

//外部URLからブログをカードタグの取得
if ( !function_exists( 'url_to_external_blog_card_tag' ) ):
function url_to_external_blog_card_tag($url){
  $url = strip_tags($url);//URL

  //サイトの内部リンクは処理しない場合
  if ( strpos( $url, get_the_site_domain() ) ) {
    $id = url_to_postid( $url );//IDを取得（URLから投稿ID変換
    if ( $id ) {//IDを取得できる場合はループを飛ばす
      return;
    }//IDが取得できない場合は外部リンクとして処理する
  }

  //独自プローブカード（キャッシュ）の利用
  $tag = url_to_external_ogp_blogcard_tag($url);

  return $tag;
}
endif;

//本文中の外部URLをはてなブログカードタグに変更する
if ( !function_exists( 'url_to_external_blog_card' ) ):
function url_to_external_blog_card($the_content) {
  //1行にURLのみが期待されている行（URL）を全て$mに取得
  $res = preg_match_all('/^(<p>)?(<a[^>]+?>)?https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+(<\/a>)?(?!.*<br *\/?>).*?(<\/p>)?/im', $the_content,$m);
  /*
  $res = preg_match_all('/^(<p>)?(<a[^>]+?>)?https?:\/\/'.preg_quote(get_the_site_domain()).'\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+(<\/a>)?(<\/p>)?/im', $the_content,$m);
  */
  //_v($the_content);

  //マッチしたURL一つ一つをループしてカードを作成
  foreach ($m[0] as $match) {

    //マッチしたpタグが適正でないときはブログカード化しない
    if ( !is_p_tag_appropriate($match) ) {
      continue;
    }

    $url = strip_tags($match);//URL
    //var_dump(htmlentities($match));

    $tag = url_to_external_blog_card_tag($url);
    //$tag = $tag.htmlspecialchars($tag);
    //_v($tag);

    if ( !$tag ) continue;

    //本文中のURLをブログカードタグで置換
    $the_content = preg_replace('{^'.preg_quote($match, '{}').'}im', $tag , $the_content, 1);
    //_v($the_content);
  }

  return $the_content;//置換後のコンテンツを返す
}
endif;
if ( is_external_blogcard_enable() ) {//外部リンクブログカードが有効のとき
  add_filter('the_content','url_to_external_blog_card', 11);//本文表示をフック
  add_filter('widget_text', 'url_to_external_blog_card', 11);//テキストウィジェットをフック
  add_filter('widget_text_pc_text', 'url_to_external_blog_card', 11);
  add_filter('widget_classic_text', 'url_to_external_blog_card', 11);
  add_filter('widget_text_mobile_text', 'url_to_external_blog_card', 11);
  add_filter('the_category_content', 'url_to_external_blog_card', 11);
}


//外部サイトからブログカードサムネイルを取得する
if ( !function_exists( 'fetch_card_image' ) ):
function fetch_card_image($image){
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
  $dir = get_theme_blog_card_cache_dir();

  //画像の読み込み
  if ( $file_data = @wp_filesystem_get_contents($image) ) {
  // if ( WP_Filesystem() ) {//WP_Filesystemの初期化
  //   global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し

    //ディレクトリがないときには作成する
    if ( !file_exists($dir) ) {
      mkdir($dir, 0777);
    }
    //ローカル画像ファイルパス
    $new_file = $dir.'/'.md5($image).'.'.$ext;

    // $file_data = @$wp_filesystem->get_contents($image);

    if ( $file_data ) {
      //$wp_filesystem->put_contents($new_file, $file_data);
      wp_filesystem_put_contents($new_file, $file_data);
      //画像編集オブジェクトの作成
      $image_editor = wp_get_image_editor($new_file);
      if ( !is_wp_error($image_editor) ) {
        $image_editor->resize(160, 90, true);
        $image_editor->save( $new_file );
        return str_replace(WP_CONTENT_DIR, content_url(), $new_file);
      }
      //$wp_filesystem->delete($new_file);
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
  $url_hash = 'bcc_'.md5( $url );
  $error_title = $url; //エラーの場合はURLを表示
  $title = $error_title;
  $error_image = get_site_screenshot_url($url);

  $image = $error_image;
  $snipet = '';
  $error_rel_nofollow = ' rel="nofollow"';

  require_once('open-graph.php');
  //ブログカードキャッシュ更新モード、もしくはログインユーザー以外のときはキャッシュの取得
  if ( !(is_external_blogcard_refresh_mode() && is_user_administrator()) ) {
    //保存したキャッシュを取得
    $ogp = get_transient( $url_hash );
  }

  if ( empty($ogp) ) {
    $ogp = OpenGraphGetter::fetch( $url );
    if ( $ogp == false ) {
      $ogp = 'error';
    } else {
      //キャッシュ画像の取得
      $res = fetch_card_image($ogp->image);

      if ( $res ) {
        $ogp->image = $res;
      }

      if ( isset( $ogp->title ) )
        $title = $ogp->title;//タイトルの取得

      if ( isset( $ogp->description ) )
        $snipet = $ogp->description;//ディスクリプションの取得

      if ( isset( $ogp->image ) )
        $image = $ogp->image;////画像URLの取得

      $error_rel_nofollow = null;
    }

    set_transient( $url_hash, $ogp,
                   60 * 60 * 24 * intval(get_external_blogcard_cache_retention_period()) );

  } elseif ( $ogp == 'error' ) {
    //前回取得したとき404ページだったら何も出力しない
  } else {
    if ( isset( $ogp->title ) )
      $title = $ogp->title;//タイトルの取得

    if ( isset( $ogp->description ) )
      $snipet = $ogp->description;//ディスクリプションの取得

    if ( isset( $ogp->image ) )
      $image = $ogp->image;//画像URLの取得

    $error_rel_nofollow = null;
  }
  //var_dump($image);

  //ドメイン名を取得
  $domain = get_domain_name(isset($ogp->url) ? punycode_decode($ogp->url) : punycode_decode($url));

  //og:imageが相対パスのとき
  if(!$image || (strpos($image, '//') === false) || (is_ssl() && (strpos($image, 'https:') === false))){    // //OGPのURL情報があるか
    //相対パスの時はエラー用の画像を表示
    $image = $error_image;
  }

  $snipet = get_content_excerpt( $snipet, 160 );

  // //ブログカードのサムネイルを右側に
  // $thumbnail_class = ' blogcard-thumbnail-left';
  // if ( is_blog_card_external_thumbnail_right() ) {
  //   $thumbnail_class = ' blogcard-thumbnail-right';
  // }

  //新しいタブで開く場合
  $target = is_external_blogcard_target_blank() ? ' target="_blank"' : '';

  //コメント内でブログカード呼び出しが行われた際はnofollowをつける
  global $comment; //コメント内以外で$commentを呼び出すとnullになる
  $nofollow = $comment || $error_rel_nofollow ? ' rel="nofollow"' : null;


  // //はてブを表示する場合
  // $hatebu_tag = is_blog_card_external_hatena_visible() && !is_amp() ? '<div class="blogcard-hatebu"><a href="//b.hatena.ne.jp/entry/'.$url.'"'.$target.' rel="nofollow"><img src="//b.hatena.ne.jp/entry/image/'.$url.'" alt=""'.$hatena_wh.' /></a></div>' : '';

  //GoogleファビコンAPIを利用する
  ////www.google.com/s2/favicons?domain=nelog.jp
  $favicon_tag = '<span class="blogcard-favicon"><img src="//www.google.com/s2/favicons?domain='.$domain.'" class="blogcard-favicon-image" alt="" width="16" height="16" /></span>';

  //サイトロゴ
  $site_logo_tag = '<div class="blogcard-domain external-blogcard-domain">'.$domain.'</div>';
  $site_logo_tag = '<div class="blogcard-site external-blogcard-site">'.$favicon_tag.$site_logo_tag.'</div>';

  //サムネイルを取得できた場合
  if ( $image ) {
    $thumbnail = '<img src="'.$image.'" alt="" class="blogcard-thumb-image external-blogcard-thumb-image" width="160" height="90" />';
  }

  //取得した情報からブログカードのHTMLタグを作成
  $tag =
  '<a href="'.$url.'" class="blogcard-wrap external-blogcard-wrap a-wrap cf"'.$target.$nofollow.'>'.
    '<div class="blogcard external-blogcard'.get_additional_external_blogcard_classes().' cf">'.
      '<figure class="blogcard-thumbnail external-blogcard-thumbnail">'.$thumbnail.'</figure>'.
      '<div class="blogcard-content external-blogcard-content">'.
        '<div class="blogcard-title external-blogcard-title">'.$title.'</div>'.
        '<div class="blogcard-snipet external-blogcard-snipet">'.$snipet.'</div>'.
      '</div>'.
      '<div class="blogcard-footer external-blogcard-footer cf">'.$site_logo_tag.'</div>'.
    '</div>'.
  '</a>';

  return $tag;
}
endif;
