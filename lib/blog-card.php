<?php //ブログカード関係の関数

//はてな oEmbed対応
wp_oembed_add_provider('https://*', 'https://hatenablog.com/oembed');
//oembed無効
add_filter( 'embed_oembed_discover', '__return_false' );
//Embeds
remove_action( 'parse_query', 'wp_oembed_parse_query' );
remove_action( 'wp_head', 'wp_oembed_remove_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_remove_host_js' );
//本文中のURLが内部リンクの場合にWordpressがoembedをしてしまうのを解除(WP4.5.3向けの対策)
remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result' );

//内部URLからブログをカードタグの取得
if ( !function_exists( 'url_to_blog_card_tag' ) ):
function url_to_blog_card_tag($url){
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
  $date = null;
  if (is_blog_card_date_type_post_date()) {
    $date = mysql2date('Y-m-d H:i', $post_data->post_date);//投稿日の取得
  } else {
    $date = mysql2date('Y-m-d H:i', $post_data->post_modified);//更新日の取得
  }

  $excerpt = get_content_excerpt($post_data->post_content, get_excerpt_length());
  //抜粋の取得
  if ( is_wordpress_excerpt() && $exce ) {//Wordpress固有の抜粋のとき
    $excerpt = $exce;
  }

  //メタディスクリプションが設定してある場合はメタディスクリプションを抜粋表示
  $meta_description = get_post_meta($id, 'meta_description', true);
  if ( is_wordpress_excerpt() && $meta_description ) {
    $excerpt = $meta_description;
  }

  //ブログカードのサムネイルを右側に
  $thumbnail_class = ' blog-card-thumbnail-left';
  if ( is_blog_card_thumbnail_right() ) {
    $thumbnail_class = ' blog-card-thumbnail-right';
  }

  //新しいタブで開く場合
  $target = is_blog_card_target_blank() ? ' target="_blank"' : '';

  //ブログカードの幅を広げる
  $wide_class = null;
  if ( is_blog_card_width_auto() ) {
    $wide_class = ' blog-card-wide';
  }
  $hatena_wh = null;
  if ( is_amp() ) {
    $hatena_wh = ' width="48" height="13"';
  }

  //$hatebu_url = preg_replace('/^https?:\/\//i', '', $url);
  //はてブを表示する場合
  $hatebu_tag = is_blog_card_hatena_visible() && !is_amp() ? '<div class="blog-card-hatebu"><a href="//b.hatena.ne.jp/entry/'.$url.'"'.$target.' rel="nofollow"><img src="//b.hatena.ne.jp/entry/image/'.$url.'" alt=""'.$hatena_wh.' /></a></div>' : '';

  //ファビコン
  $favicon_tag = '';
  if ( is_favicon_enable() && get_the_favicon_url() ) {//ファビコンが有効か確認

    //GoogleファビコンAPIを利用する
    ////www.google.com/s2/favicons?domain=nelog.jp
    $favicon_tag = '<span class="blog-card-favicon"><img src="//www.google.com/s2/favicons?domain='.get_this_site_domain().'" class="blog-card-favicon-img" alt="" width="16" height="16" /></span>';
  }

  //サイトロゴ
  if ( is_blog_card_site_logo_visible() ) {
    if ( is_blog_card_site_logo_link_enable() ) {
      $site_logo_tag = '<a href="'.home_url().'"'.$target.'>'.get_this_site_domain().'</a>';
    } else {
      $site_logo_tag = get_this_site_domain();
    }
    $site_logo_tag = '<div class="blog-card-site">'.$favicon_tag.$site_logo_tag.'</div>';
  }


  $date_tag = '';
  if ( !is_blog_card_date_type_none() ) {//日付を表示するとき
    $date_tag = '<div class="blog-card-date">'.$date.'</div>';
  }
  //サムネイルの取得（要100×100のサムネイル設定）
  $thumbnail = get_the_post_thumbnail($id, 'thumb100', array('class' => 'blog-card-thumb-image', 'alt' => ''));
  if ( !$thumbnail ) {//サムネイルが存在しない場合
    $thumbnail = '<img src="'.$no_image.'" alt="" class="blog-card-thumb-image"'.get_noimage_sizes_attr($no_image).' />';
    //$thumbnail = '<img src="'.get_template_directory_uri().'/images/no-image.png" alt="'.$title.'" class="blog-card-thumb-image"'.get_noimage_sizes_attr().' />';
  }
  //取得した情報からブログカードのHTMLタグを作成
  $tag = '<div class="blog-card internal-blog-card'.$thumbnail_class.$wide_class.' cf"><div class="blog-card-thumbnail"><a href="'.$url.'" class="blog-card-thumbnail-link"'.$target.'>'.$thumbnail.'</a></div><div class="blog-card-content"><div class="blog-card-title"><a href="'.$url.'" class="blog-card-title-link"'.$target.'>'.$title.'</a></div><div class="blog-card-excerpt">'.$excerpt.'</div></div><div class="blog-card-footer">'.$site_logo_tag.$hatebu_tag.$date_tag.'</div></div>';

  if ( is_wraped_entry_card() ) {

    $wide_hover_card_class = null;
    if (is_blog_card_width_auto()) {
      $wide_hover_card_class = ' hover-blog-card-wide';
    }    //エントリーカードをカード化する場合はaタグを削除して全体をa.hover-cardで囲む
    $tag = wrap_entry_card($tag, $url, $target, false, ' hover-blog-card hover-internal-blog-card'.$wide_hover_card_class);
  }
  return $tag;
}
endif;

//本文中のURLをブログカードタグに変更する
if ( !function_exists( 'url_to_blog_card' ) ):
function url_to_blog_card($the_content) {
  if ( true /*is_singular()*/ ) {//投稿ページもしくは固定ページのとき（この条件分岐は変更）
    //1行にURLのみが期待されている行（URL）を全て$mに取得

    $res = preg_match_all('/^(<p>)?(<br ? \/?>)?(<a.+?>)?https?:\/\/'.preg_quote(get_this_site_domain()).'\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+(<\/a>)?(<br ? \/?>)?(<\/p>)?/im', $the_content,$m);
    /*$res = preg_match_all('{^(<p>)?(<a.+?>)?'.preg_quote(site_url()).'/?[-_.!~*\'()a-zA-Z0-9;/?:\@&=+\$,%#]+(</a>)?(</p>)?(<br ?/?>)?}im', $the_content,$m);*/    //マッチしたURL一つ一つをループしてカードを作成
    //var_dump($res);
    foreach ($m[0] as $match) {

      //マッチしたpタグが適正でないときはブログカード化しない
      if ( !is_p_tag_appropriate($match) ) {
        continue;
      }

      // //ブログカード置換用テキストにpタグが含まれているかどうか
      // if (strpos($match,'p>') !== false){
      //   //pタグが含まれていた場合は開始タグと終了タグが揃っているかどうか
      //   if ( !((strpos($match,'<p>') !== false) && (strpos($match,'</p>') !== false)) ) {
      //     continue;
      //   }
      // }

      $url = strip_tags($match);//URL

      $tag = url_to_blog_card_tag($url);

      if ( !$tag ) continue;//IDを取得できない場合はループを飛ばす

      //本文中のURLをブログカードタグで置換
      $the_content = preg_replace('{'.preg_quote($match).'}', $tag , $the_content, 1);
      wp_reset_postdata();

    }
  }
  return $the_content;//置換後のコンテンツを返す
}
endif;
if ( is_blog_card_enable() ) {
  add_filter('the_content', 'url_to_blog_card', 9999999);//本文表示をフック
  add_filter('widget_text', 'url_to_blog_card', 9999999);//テキストウィジェットをフック
  add_filter('widget_text_pc_text', 'url_to_blog_card', 9999999);
  add_filter('widget_classic_text', 'url_to_blog_card', 9999999);
  add_filter('widget_text_mobile_text', 'url_to_blog_card', 9999999);
  if (is_blog_card_comment_internal_enable())
    add_filter('comment_text', 'url_to_blog_card', 9999999);//コメントをフック
}

//本文中のURLショートコードをブログカードタグに変更する
if ( !function_exists( 'url_shortcode_to_blog_card' ) ):
function url_shortcode_to_blog_card($the_content) {
  if ( true /*is_singular()*/ ) {//投稿ページもしくは固定ページのとき
    //1行にURLのみが期待されている行（URL）を全て$mに取得
    $res = preg_match_all('/(<p>)?(<br ? \/?>)?\[https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+\](<br ? \/?>)?(<\/p>)?/im', $the_content, $m);
    foreach ($m[0] as $match) {
    //マッチしたURL一つ一つをループしてカードを作成
      $url = strip_tags($match);//URL
      $url = preg_replace('/[\[\]]/', '', $url);//[と]の除去
      $url = str_replace('?', '%3F', $url);//?をエンコード
      // $match = str_replace('?', '%3F', $match);//?をエンコード

      //取得した内部URLからブログカードのHTMLタグを作成
      $tag = url_to_blog_card_tag($url);//外部ブログカードタグに変換
      //URLをブログカードに変換
      if ( !$tag ) {//取得したURLが外部URLだった場合
        $tag = url_to_external_blog_card($url);//外部ブログカードタグに変換
      }
      // echo('<pre>');
      // var_dump(htmlspecialchars('{'.preg_quote($match).'}'));
      // var_dump(htmlspecialchars($tag));
      // echo('</pre>');

      if ( $tag ) {//内部・外部ブログカードどちらかでタグを作成できた場合
        //本文中のURLをブログカードタグで置換
        $the_content = preg_replace('{'.preg_quote($match).'}', $tag , $the_content, 1);
      }
    }
  }

  // echo('<pre>');
  // var_dump(htmlspecialchars($the_content));
  // echo('</pre>');
  return $the_content;//置換後のコンテンツを返す
}
endif;
add_filter('the_content', 'url_shortcode_to_blog_card' ,99999999);//本文表示をフック
add_filter('widget_text', 'url_shortcode_to_blog_card' ,99999999);//テキストウィジェットをフック
add_filter('widget_text_pc_text', 'url_shortcode_to_blog_card', 99999999);
add_filter('widget_classic_text', 'url_shortcode_to_blog_card', 99999999);
add_filter('widget_text_mobile_text', 'url_shortcode_to_blog_card', 99999999);
add_filter('comment_text', 'url_shortcode_to_blog_card', 99999999);//コメントをフック

//外部URLからブログをカードタグの取得
if ( !function_exists( 'url_to_external_blog_card_tag' ) ):
function url_to_external_blog_card_tag($url){
  $url = strip_tags($url);//URL

  //サイトの内部リンクは処理しない場合
  if ( strpos( $url, get_this_site_domain() ) ) {
    $id = url_to_postid( $url );//IDを取得（URLから投稿ID変換
    if ( $id ) {//IDを取得できる場合はループを飛ばす
      return;
    }//IDが取得できない場合は外部リンクとして処理する
  }

  $tag = '';


  //ブログカードの幅を広げる
  $wide_class = null;
  if ( is_blog_card_external_width_auto() ) {
    $wide_class = ' blog-card-wide';
  }

  if ( is_blog_card_external_default() || is_amp() ) {
    //Simplicity独自プローブカード（キャッシュ）の利用
    $tag = url_to_external_ogp_blog_card_tag($url);
  } elseif ( is_blog_card_external_hatena() ) {
    //取得した情報からはてなブログカードのHTMLタグを作成
    $tag = '<'.'iframe '.'class="blog-card external-blog-card-hatena'.$wide_class.' cf" src="//hatenablog-parts.com/embed?url='.$url.'"></'.'iframe'.'>';
  } elseif ( is_blog_card_external_embedly() ) {
    //取得した情報からEmbedlyブログカードのHTMLタグを作成
    $tag = '<a class="embedly-card" href="'.$url.'">'.$url.'</a><script async src="//cdn.embedly.com/widgets/platform.js" charset="UTF-8"></script>';
  }

  return $tag;
}
endif;

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

//本文中の外部URLをはてなブログカードタグに変更する
if ( !function_exists( 'url_to_external_blog_card' ) ):
function url_to_external_blog_card($the_content) {
  if ( true /*is_singular()*/ ) {//投稿ページもしくは固定ページのとき
    //1行にURLのみが期待されている行（URL）を全て$mに取得
    $res = preg_match_all('/^(<p>)?(<br ? \/?>)?(<a.+?>)?https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+(<\/a>)?(<br ? \/?>)?(<\/p>)?/im', $the_content,$m);
    //var_dump(htmlentities($the_content));
    //マッチしたURL一つ一つをループしてカードを作成
    foreach ($m[0] as $match) {

      //マッチしたpタグが適正でないときはブログカード化しない
      if ( !is_p_tag_appropriate($match) ) {
        continue;
      }

      // //ブログカード置換用テキストにpタグが含まれているかどうか
      // if (strpos($match,'p>') !== false){
      //   //pタグが含まれていた場合は開始タグと終了タグが揃っているかどうか
      //   if ( !((strpos($match,'<p>') !== false) && (strpos($match,'</p>') !== false)) ) {
      //     continue;
      //   }
      // }

      $url = strip_tags($match);//URL
      //var_dump(htmlentities($match));

      $tag = url_to_external_blog_card_tag($url);
      //$tag = $tag.htmlspecialchars($tag);

      if ( !$tag ) continue;

      // echo('<pre>');
      // var_dump(htmlentities($tag));
      // echo('</pre>');

      //brタグの除却
      //$tag = preg_replace('/<br>/i', '', $tag);

      //本文中のURLをブログカードタグで置換
      $the_content = preg_replace('{'.preg_quote($match).'}', $tag , $the_content, 1);
      //$the_content = htmlspecialchars($the_content);
    }
  }
  return $the_content;//置換後のコンテンツを返す
}
endif;
if ( is_blog_card_external_enable() ) {//外部リンクブログカードが有効のとき
  add_filter('the_content','url_to_external_blog_card', 9999999);//本文表示をフック
  add_filter('widget_text', 'url_to_external_blog_card', 9999999);//テキストウィジェットをフック
  add_filter('widget_text_pc_text', 'url_to_external_blog_card', 9999999);
  add_filter('widget_classic_text', 'url_to_external_blog_card', 9999999);
  add_filter('widget_text_mobile_text', 'url_to_external_blog_card', 9999999);
  if (is_blog_card_comment_external_enable())
      add_filter('comment_text', 'url_to_external_blog_card', 9999999);//コメントをフック
}

//Simplicityキャッシュディレクトリ
function get_simplicity_cache_dir(){
  return WP_CONTENT_DIR.'/uploads/simplicity-cache';
}

//外部サイトからブログカードサムネイルを取得する
if ( !function_exists( 'fetch_card_image' ) ):
function fetch_card_image($image){
  if ( WP_Filesystem() ) {//WP_Filesystemの初期化
    global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し
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
    //var_dump($temp_ext);
    if ( $temp_ext ) {
      $ext = $temp_ext;
    }
    //キャッシュディレクトリ
    $dir = get_simplicity_cache_dir();
    //$dir = ABSPATH.'wp-content/uploads/simplicity-temp-314159265';
    //ディレクトリがないときには作成する
    if ( !file_exists($dir) ) {
      mkdir($dir, 0777);
    }
    //ローカル画像ファイルパス
    $new_file = $dir.'/'.md5($image).'.'.$ext;
    //$new_file = $dir.$filename;
    //$wp_filesystemオブジェクトのメソッドとしてファイルを取得する
    $file_data = @$wp_filesystem->get_contents($image);

    if ( $file_data ) {
      $wp_filesystem->put_contents($new_file, $file_data);
      //画像編集オブジェクトの作成
      $image_editor = wp_get_image_editor($new_file);
      if ( !is_wp_error($image_editor) ) {
        $image_editor->resize(100, 100, true);
        $image_editor->save( $new_file );
        return str_replace(WP_CONTENT_DIR, content_url(), $new_file);
        //$file_data = @$wp_filesystem->get_contents($new_file);
        // if ( !$file_data ) {
        //   return null;
        // }
      }
      $wp_filesystem->delete($new_file);

      // $image = base64_ encode($file_data);
      // //$wp_filesystem->delete($new_file);
      // remove_directory($dir);
      // return 'data:image/'.$ext.';base64,'.$image;
    }
  }
}
endif;

//外部サイトから直接OGP情報を取得してブログカードにする
if ( !function_exists( 'url_to_external_ogp_blog_card_tag' ) ):
function url_to_external_ogp_blog_card_tag($url){
  if ( !$url ) return;
  $url = strip_tags($url);//URL
  if (preg_match('/.+(\.mp3|\.midi|\.mp4|\.mpeg|\.mpg|\.jpg|\.jpeg|\.png|\.gif|\.svg|\.pdf)$/i', $url, $m)) {
    return;
  }
  $url_hash = 'sp_bcc_'.md5( $url );
  $error_title = $url;//'This page is error.';
  $title = $error_title;
  $error_image = get_site_screenshot_url($url);
  //$error_image = get_template_directory_uri() . '/images/no-image.png';
  $image = $error_image;
  $excerpt = '';
  $error_rel_nofollow = ' rel="nofollow"';
  //画像編集作業用ディレクトリ
  //$dir = ABSPATH.'wp-content/uploads/excard-images-314159265/';

  require_once('open-graph.php');
  //ブログカードキャッシュ更新モード、もしくはログインユーザー以外のときはキャッシュの取得
  if ( !(is_blog_card_external_cache_refresh_mode() && is_user_logged_in()) ) {
    //保存したキャッシュを取得
    $ogp = get_transient( $url_hash );
  }

  if ( empty($ogp) ) {
    $ogp = OpenGraph::fetch( $url );
    if ( $ogp == false ) {
      $ogp = 'error';
    } else {
      //キャッシュ画像の取得
      $res = fetch_card_image($ogp->image);
      // echo('<pre>');
      // var_dump($res);
      // echo('</pre>');

      if ( $res ) {
        $ogp->image = $res;
      }

      if ( isset( $ogp->title ) )
        $title = $ogp->title;//タイトルの取得

      if ( isset( $ogp->description ) )
        $excerpt = $ogp->description;//ディスクリプションの取得

      if ( isset( $ogp->image ) )
        $image = $ogp->image;////画像URLの取得

      $error_rel_nofollow = null;
    }

    set_transient( $url_hash, $ogp,
                   60 * 60 * 24 * get_blog_card_external_cache_days() );

  } elseif ( $ogp == 'error' ) {
    //前回取得したとき404ページだったら何も出力しない
    // $title = $error_title;
    // $excerpt = '';
    // $image = get_template_directory_uri() . '/images/no-image.png';
  } else {
    if ( isset( $ogp->title ) )
      $title = $ogp->title;//タイトルの取得

    if ( isset( $ogp->description ) )
      $excerpt = $ogp->description;//ディスクリプションの取得

    if ( isset( $ogp->image ) )
      $image = $ogp->image;//画像URLの取得

    $error_rel_nofollow = null;
  }
  //var_dump($image);

  //ドメイン名を取得
  $domain = get_domain_name(isset($ogp->url) ? punycode_decode($ogp->url) : punycode_decode($url));


  //og:imageが相対パスのとき
  if(!$image || (strpos($image, '//') === false) || ((is_ssl() || is_amp()) && (strpos($image, 'https:') === false))){    // //OGPのURL情報があるか
    //相対パスの時はエラー用の画像を表示
    $image = $error_image;
  }

  $excerpt = get_content_excerpt( $excerpt, 160 );

  //ブログカードのサムネイルを右側に
  $thumbnail_class = ' blog-card-thumbnail-left';
  if ( is_blog_card_external_thumbnail_right() ) {
    $thumbnail_class = ' blog-card-thumbnail-right';
  }

  //新しいタブで開く場合
  $target = is_blog_card_external_target_blank() ? ' target="_blank"' : '';

  //コメント内でブログカード呼び出しが行われた際はnofollowをつける
  global $comment; //コメント内以外で$commentを呼び出すとnullになる
  $nofollow = $comment || $error_rel_nofollow ? ' rel="nofollow"' : null;
  // echo('<pre>');
  // var_dump($nofollow);
  // echo('</pre>');

  //ブログカードの幅を広げる
  $wide_class = null;
  if ( is_blog_card_external_width_auto() ) {
    $wide_class = ' blog-card-wide';
  }
  $hatena_wh = null;
  if ( is_amp() ) {
    $hatena_wh = ' width="48" height="13"';
  }

  //はてブを表示する場合
  $hatebu_tag = is_blog_card_external_hatena_visible() && !is_amp() ? '<div class="blog-card-hatebu"><a href="//b.hatena.ne.jp/entry/'.$url.'"'.$target.' rel="nofollow"><img src="//b.hatena.ne.jp/entry/image/'.$url.'" alt=""'.$hatena_wh.' /></a></div>' : '';

  //GoogleファビコンAPIを利用する
  ////www.google.com/s2/favicons?domain=nelog.jp
  $favicon_tag = '<span class="blog-card-favicon"><img src="//www.google.com/s2/favicons?domain='.$domain.'" class="blog-card-favicon-img" alt="" width="16" height="16" /></span>';

  //サイトロゴ
  if ( is_blog_card_external_site_logo_visible() ) {
    if ( is_blog_card_external_site_logo_link_enable() ) {
      $site_logo_tag = '<a href="//'.$domain.'"'.$target.$nofollow.'>'.$domain.'</a>';
    } else {
      $site_logo_tag = $domain;
    }
    $site_logo_tag = '<div class="blog-card-site">'.$favicon_tag.$site_logo_tag.'</div>';
  }

  //サムネイルを取得できた場合
  if ( $image ) {
    $thumbnail = '<img src="'.$image.'" alt="" class="blog-card-thumb-image" height="100" width="100" />';
  }
  //取得した情報からブログカードのHTMLタグを作成
  $tag = '<div class="blog-card external-blog-card'.$thumbnail_class.$wide_class.' cf"><div class="blog-card-thumbnail"><a href="'.$url.'" class="blog-card-thumbnail-link"'.$target.$nofollow.'>'.$thumbnail.'</a></div><div class="blog-card-content"><div class="blog-card-title"><a href="'.$url.'" class="blog-card-title-link"'.$target.$nofollow.'>'.$title.'</a></div><div class="blog-card-excerpt">'.$excerpt.'</div></div><div class="blog-card-footer">'.$site_logo_tag.$hatebu_tag.'</div></div>';
  if ( is_wraped_entry_card() ) {
    $wide_hover_card_class = null;
    if (is_blog_card_external_width_auto()) {
      $wide_hover_card_class = ' hover-blog-card-wide';
    }
    //エントリーカードをカード化する場合はaタグを削除して全体をa.hover-cardで囲む
    $tag = wrap_entry_card($tag, $url, $target, $nofollow, ' hover-blog-card hover-external-blog-card'.$wide_hover_card_class);
    //$tag = '<a class="abc"><object><div>aaa</div></object></a>';
  }
  return $tag;
}
endif;

//transientキャッシュの削除
function delete_blog_card_cache_transients(){
  global $wpdb;
  $wpdb->query("DELETE FROM $wpdb->options WHERE (`option_name` LIKE '%_transient_sp_bcc_%') OR (`option_name` LIKE '%_transient_timeout_sp_bcc_%')");
}

//テーマを変更時にブログカードのキャッシュを削除
function delete_blog_card_cache() {
  delete_blog_card_cache_transients();
  remove_directory(get_simplicity_cache_dir());
}
add_action('switch_theme', 'delete_blog_card_cache');