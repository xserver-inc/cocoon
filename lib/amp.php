<?php

//AMP判別関数
if ( !function_exists( 'is_amp' ) ):
function is_amp(){
  //bbPressがインストールされていて、トピックの時は除外
  if (function_exists('bbp_is_topic')) {
    if (bbp_is_topic()) {
      return false;
    }
  }
  //AMPチェック
  $is_amp = false;
  if ( empty($_GET['amp']) ) {
    return false;
  }

  // ampのパラメーターが1かつ
  // かつsingleページのみ$is_ampをtrueにする
  if(is_amp_enable() && //AMPがカスタマイザーの有効化されているか
     is_singular() &&
     $_GET['amp'] === '1' &&//URLにamp=1パラメータがあるとき
     has_amp_page()//AMPページが存在しているとき
    ){
    $is_amp = true;
  }
  return $is_amp;
}
endif;

//AMPページがある投稿ページか
if ( !function_exists( 'has_amp_page' ) ):
function has_amp_page(){
  $category_ids =get_noamp_category_ids();
  return is_singular() &&
    is_amp_enable() &&
    is_amp_page_enable() &&
    !in_category( $category_ids ) && //除外カテゴリではAMPページを生成しない
    (!function_exists('is_bbpress') || !is_bbpress());
}
endif;

//AMP用にコンテンツを変換する
if ( !function_exists( 'convert_content_for_amp' ) ):
function convert_content_for_amp($the_content){
  if ( !is_amp() ) {
    return $the_content;
  }



  //iframe用のplaceholderタグ（amp-iframeの呼び出し位置エラー対策）
  $amp_placeholder = '<amp-img layout="fill" src="'.get_template_directory_uri().'/images/transparence.png'.'" placeholder>';


  //noscriptタグの削除
  $the_content = preg_replace('/<noscript>/i', '', $the_content);
  $the_content = preg_replace('/<\/noscript>/i', '', $the_content);

  //fontタグの削除
  $the_content = preg_replace('/<font[^>]*?>/i', '', $the_content);
  $the_content = preg_replace('/<\/font>/i', '', $the_content);

  //Amazon商品リンクのhttp URLをhttpsへ
  $the_content = str_replace('http://rcm-jp.amazon.co.jp/', 'https://rcm-fe.amazon-adsystem.com/', $the_content);
  $the_content = str_replace('"//rcm-fe.amazon-adsystem.com/', '"https://rcm-fe.amazon-adsystem.com/', $the_content);
  $the_content = str_replace("'//rcm-fe.amazon-adsystem.com/", "'https://rcm-fe.amazon-adsystem.com/", $the_content);
  //Amazon商品画像のURLをhttpsへ
  $the_content = str_replace('http://ecx.images-amazon.com', 'https://images-fe.ssl-images-amazon.com', $the_content);
  //楽天商品画像のURLをhttpsへ
  $the_content = str_replace('http://thumbnail.image.rakuten.co.jp', 'https://thumbnail.image.rakuten.co.jp', $the_content);

  //Amazonデフォルトの埋め込みタグを置換する
  /*
  $pattern = '/<iframe([^>]+?)(src="https:\/\/rcm-fe.amazon-adsystem.com\/[^"]+?").*?><\/iframe>/is';
  $append = '<amp-iframe$1$2 width="120" height="240"frameborder="0">'.$amp_placeholder.'</amp-iframe>';
  */
  $pattern = '/<iframe([^>]+?)(src="https?:\/\/rcm-fe.amazon-adsystem.com\/[^"]+?t=([^&"]+)[^"]+?asins=([^&"]+)[^"]*?").*?><\/iframe>/is';
  $amazon_url = 'https://www.amazon.co.jp/exec/obidos/ASIN/$4/$3/ref=nosim/';
  $append = PHP_EOL.'<amp-iframe$1$2 width="120" height="240" frameborder="0">'.$amp_placeholder.'</amp-iframe><br><a href="'.$amazon_url.'" class="aa-link"></a>'.PHP_EOL;

  //YouTube iframeのsrc属性のhttp URLをhttpsへ
  $the_content = str_replace('http://www.youtube.com/', 'https://www.youtube.com/', $the_content);
  //JetpackがYouTubeのURLに余計なクエリを付け加えるのを取り除く
  //$the_content = preg_replace('{(https://www.youtube.com/embed/[^?"\']+)[^"\']*}i', '$1', $the_content);

  //$append = url_to_external_ogp_blog_card_tag($amazon_url);
  //$the_content = preg_replace($pattern, htmlspecialchars($append), $the_content);
  $the_content = preg_replace($pattern, $append, $the_content);
  //Amazon画像をブログカード化
  //$the_content = url_to_external_blog_card($the_content);


  //C2A0文字コード（UTF-8の半角スペース）を通常の半角スペースに置換
  $the_content = str_replace('\xc2\xa0', ' ', $the_content);

  //style属性を取り除く
  $the_content = preg_replace('/ *?style=["][^"]*?["]/i', '', $the_content);
  $the_content = preg_replace('/ *?style=[\'][^\']*?[\']/i', '', $the_content);

  //target属性を取り除く
  $the_content = preg_replace('/ *?target=["][^"]*?["]/i', '', $the_content);
  $the_content = preg_replace('/ *?target=[\'][^\']*?[\']/i', '', $the_content);

  //onclick属性を取り除く
  $the_content = preg_replace('/ *?onclick=["][^"]*?["]/i', '', $the_content);
  $the_content = preg_replace('/ *?onclick=[\'][^\']*?[\']/i', '', $the_content);

  //onload属性を取り除く
  $the_content = preg_replace('/ *?onload=["][^"]*?["]/i', '', $the_content);
  $the_content = preg_replace('/ *?onload=[\'][^\']*?[\']/i', '', $the_content);

  //marginwidth属性を取り除く
  $the_content = preg_replace('/ *?marginwidth=["][^"]*?["]/i', '', $the_content);
  $the_content = preg_replace('/ *?marginwidth=[\'][^\']*?[\']/i', '', $the_content);

  //marginheight属性を取り除く
  $the_content = preg_replace('/ *? marginheight=["][^"]*?["]/i', '', $the_content);
  $the_content = preg_replace('/ *? marginheight=[\'][^\']*?[\']/i', '', $the_content);

  //YouTubeプレイヤーのtype属性を取り除く
  $the_content = str_replace(" class='youtube-player' type='text/html'", " class='youtube-player'", $the_content);
  $the_content = str_replace(' class="youtube-player" type="text/html"', ' class="youtube-player"', $the_content);

  //単純に耐える属性を取り除いたらAMPエラーが出た
  //typeが不要なタグと必要なタグもあるみたい
  // //type属性を取り除く
  // $the_content = preg_replace('/ *? type=["][^"]*?["]/i', '', $the_content);
  // $the_content = preg_replace('/ *? type=[\'][^\']*?[\']/i', '', $the_content);

  //FONTタグを取り除く
  $the_content = preg_replace('/<font[^>]+?>/i', '', $the_content);
  $the_content = preg_replace('/<\/font>/i', '', $the_content);

  // //カエレバ・ヨメレバのAmazon商品画像にwidthとhightを追加する
  // $the_content = preg_replace('/ src="(http:)?\/\/ecx.images-amazon.com/i', ' width="75" height="75" sizes="(max-width: 75px) 100vw, 75px" src="http://ecx.images-amazon.com', $the_content);
  // //カエレバ・ヨメレバのAmazon商品画像にwidthとhightを追加する（SSL用）
  // $the_content = preg_replace('/ src="(https:)?\/\/images-fe.ssl-images-amazon.com/i', ' width="75" height="75" sizes="(max-width: 75px) 100vw, 75px" src="https://images-fe.ssl-images-amazon.com', $the_content);
  // //カエレバ・ヨメレバの楽天商品画像にwidthとhightを追加する
  // $the_content = preg_replace('/ src="(http:)?\/\/thumbnail.image.rakuten.co.jp/i', ' width="75" height="75" sizes="(max-width: 75px) 100vw, 75px" src="http://thumbnail.image.rakuten.co.jp', $the_content);
  // //カエレバ・ヨメレバのYahoo!ショッピング商品画像にwidthとhightを追加する
  // $the_content = preg_replace('/ src="(http:)?\/\/item.shopping.c.yimg.jp/i', ' width="75" height="75" sizes="(max-width: 75px) 100vw, 75px" src="http://item.shopping.c.yimg.jp', $the_content);

  //アプリーチの画像対応
  $the_content = preg_replace('/<img([^>]+?src="[^"]+?(mzstatic\.com|phobos\.apple\.com|googleusercontent\.com|ggpht\.com)[^"]+?[^>\/]+)\/?>/is', '<amp-img$1 width="75" height="75" sizes="(max-width: 75px) 100vw, 75px"></amp-img>', $the_content);
  $the_content = preg_replace('/<img([^>]+?src="[^"]+?nabettu\.github\.io[^"]+?[^>\/]+)\/?>/is', '<amp-img$1 width="120" height="36" sizes="(max-width: 120px) 100vw, 120px"></amp-img>', $the_content);

  //imgタグをamp-imgタグに変更する
  $res = preg_match_all('/<img(.+?)\/?>/is', $the_content, $m);
  //var_dump($res);
  //var_dump($m);
  if ($res) {//画像タグがある場合

    foreach ($m[0] as $match) {
      //変数の初期化
      $src_attr = null;
      $url = null;
      $width_attr = null;
      $width_value = null;
      $height_attr = null;
      $height_value = null;
      $alt_attr = null;
      $alt_value = null;
      $title_attr = null;
      $title_value = null;
      $sizes_attr = null;
      //var_dump(htmlspecialchars($match));

      //src属性の取得（画像URLの取得）
      $src_res = preg_match('/src=["\']([^"\']+?)["\']/is', $match, $srcs);
      if ($src_res) {
        $src_attr = ' '.$srcs[0];//src属性を作成
        $url = $srcs[1];//srcの値（URL）を取得する
      }

      //width属性の取得
      $width_res = preg_match('/width=["\']([^"\']*?)["\']/is', $match, $widths);
      if ($width_res) {
        $width_attr = ' '.$widths[0];//width属性を作成
        $width_value = $widths[1];//widthの値（幅）を取得する
      }

      //height属性の取得
      $height_res = preg_match('/height=["\']([^"\']*?)["\']/is', $match, $heights);
      if ($height_res) {
        $height_attr = ' '.$heights[0];//height属性を作成
        $height_value = $heights[1];//heightの値（高さ）を取得する
      }

      //alt属性の取得
      $alt_res = preg_match('/alt=["]([^"]*?)["]/is', $match, $alts);
      if (!$alt_res)
        $alt_res = preg_match("/alt=[']([^']*?)[']/is", $match, $alts);
      if ($alt_res) {
        $alt_attr = ' '.$alts[0];//alt属性を作成
        $alt_value = $alts[1];//altの値を取得する
      }

      //title属性の取得
      $title_res = preg_match('/title=["]([^"]*?)["]/is', $match, $titles);
      if (!$title_res)
        $title_res = preg_match("/title=[']([^']*?)[']/is", $match, $titles);
      if ($title_res) {
        $title_attr = ' '.$titles[0];//title属性を作成
        $title_value = $titles[1];//titleの値を取得する
      }

      $class_attr = null;
      //widthとheight属性のないものは画像から情報取得
      if ($url && (empty($width_value) || empty($height_value))) {
        $size = get_image_width_and_height($url);
        if ($size) {
          $class_attr = ' class="internal-content-img"';
          $width_value = $size['width'];
          $width_attr = ' width="'.$width_value.'"';//width属性を作成
          $height_value = $size['height'];
          $height_attr = ' height="'.$height_value.'"';//height属性を作成
        } else {
          //外部サイトにある画像の場合
          $class_attr = ' class="external-content-img"';
          //var_dump($url);
          if (
            strpos($url,'//images-fe.ssl-images-amazon.com') !== false ||
            strpos($url,'//thumbnail.image.rakuten.co.jp') !== false ||
            strpos($url,'//item.shopping.c.yimg.jp') !== false
          ) {
            //Amazon・楽天・Yahoo!ショッピング商品画像にwidthとheightの属性がない場合
            $width_value = 75;
            $width_attr = ' width="75"';//width属性を作成
            $height_value = 75;
            $height_attr = ' height="75"';//height属性を作成
          } else {
            $width_value = 300;
            $width_attr = ' width="300"';//width属性を作成
            $height_value = 300;
            $height_attr = ' height="300"';//height属性を作成
          }

        }
      }

      //sizes属性の作成（きれいなレスポンシブ化のために）
      if ($width_value) {
        $sizes_attr = ' sizes="(max-width: '.$width_value.'px) 100vw, '.$width_value.'px"';
      }

      //amp-imgタグの作成
      $tag = '<amp-img'.$src_attr.$width_attr.$height_attr.$alt_attr.$title_attr.$sizes_attr.$class_attr.'></amp-img>';
      // echo('<pre>');
      // var_dump($srcs);
      // var_dump(htmlspecialchars($tag));
      // var_dump($widths);
      // var_dump($heights);
      // var_dump($alts);
      // var_dump($titles);
      // echo('</pre>');

      //imgタグをamp-imgタグに置換
      $the_content = preg_replace('{'.preg_quote($match).'}', $tag , $the_content, 1);
    }
  }


  //画像タグをAMP用に置換
  $the_content = preg_replace('/<img(.+?)\/?>/is', '<amp-img$1></amp-img>', $the_content);

  // Twitterをamp-twitterに置換する（埋め込みコード）
  $pattern = '/<blockquote class="twitter-tweet".*?>.+?<a href="https:\/\/twitter.com\/.*?\/status\/([^\?"]+).*?">.+?<\/blockquote>/is';
  $append = '<p><amp-twitter width=592 height=472 layout="responsive" data-tweetid="$1"></amp-twitter></p>';
  $the_content = preg_replace($pattern, $append, $the_content);

  // JetpackによるFacebook埋め込みをamp-facebookに置換する（埋め込みコード）
  $pattern = '/<fb:post href="([^"]+?)"><\/fb:post>/is';
  $append = '<amp-facebook width=324 height=438 layout="responsive" data-href="$1"></amp-facebook>';
  $the_content = preg_replace($pattern, $append, $the_content);

  // vineをamp-vineに置換する
  $pattern = '/<iframe[^>]+?src="https:\/\/vine.co\/v\/(.+?)\/embed\/simple".+?><\/iframe>/is';
  $append = '<p><amp-vine data-vineid="$1" width="592" height="592" layout="responsive"></amp-vine></p>';
  $the_content = preg_replace($pattern, $append, $the_content);

  // Instagramをamp-instagramに置換する
  $pattern = '/<blockquote class="instagram-media".+?"https:\/\/www.instagram.com\/p\/(.+?)\/".+?<\/blockquote>/is';
  $append = '<p><amp-instagram layout="responsive" data-shortcode="$1" width="592" height="592" ></amp-instagram></p>';
  $the_content = preg_replace($pattern, $append, $the_content);

  // //YouTubeのURL埋め込み時にiframeのsrc属性のURLに余計なクエリが入るのを除去（力技;）
  // $the_content = preg_replace('/\??(((?<!service)version=\d*)|(&|&|&)rel=\d*|(&|&|&)fs=\d*|(&|&|&)autohide=\d*|(&|&|&)showsearch=\d*|(&|&|&)showinfo=\d*|(&|&|&)iv_load_policy=\d*|(&|&|&)wmode=transparent)+/is', '', $the_content);
  // YouTubeを置換する（埋め込みコード）
  $pattern = '/<iframe[^>]+?src="https?:\/\/www.youtube.com\/embed\/([^\?"]+).*?".*?><\/iframe>/is';
  $append = '<amp-youtube layout="responsive" data-videoid="$1" width="800" height="450"></amp-youtube>';
  $the_content = preg_replace($pattern, $append, $the_content);

  // Facebookを置換する（埋め込みコード）
  $pattern = '/<div class="fb-video" data-allowfullscreen="true" data-href="([^"]+?)"><\/div>/is';
  $append = '<amp-facebook layout="responsive" data-href="$1" width="500" height="450"></amp-facebook>';
  $the_content = preg_replace($pattern, $append, $the_content);

  //iframe埋め込み対策
  $the_content = preg_replace('/ +allowTransparency(=["\'][^"\']*?["\'])?/i', '', $the_content);
  $the_content = preg_replace('/ +allowFullScreen(=["\'][^"\']*?["\'])?/i', '', $the_content);
  $the_content = preg_replace('/ +webkitAllowFullScreen(=["\'][^"\']*?["\'])?/i', '', $the_content);
  $the_content = preg_replace('/ +mozallowfullscreen(=["\'][^"\']*?["\'])?/i', '', $the_content);

  //タイトルつきiframeでhttpを呼び出している場合は通常リンクに修正
  $pattern = '/<iframe[^>]+?src="(http:\/\/[^"]+?)"[^>]+?title="([^"]+?)"[^>]+?><\/iframe>/is';
  $append = '<a href="$1">$2</a>';
  $the_content = preg_replace($pattern, $append, $the_content);
  $pattern = '/<iframe[^>]+?title="([^"]+?)[^>]+?src="(http:\/\/[^"]+?)""[^>]+?><\/iframe>/is';
  $append = '<a href="$1">$2</a>';
  $the_content = preg_replace($pattern, $append, $the_content);
  //iframeでhttpを呼び出している場合は通常リンクに修正
  $pattern = '/<iframe[^>]+?src="(http:\/\/[^"]+?)"[^>]+?><\/iframe>/is';
  $append = '<a href="$1">$1</a>';
  $the_content = preg_replace($pattern, $append, $the_content);

  //iframeをamp-iframeに置換する
  $pattern = '/<iframe/i';
  $append = '<amp-iframe layout="responsive" sandbox="allow-scripts allow-same-origin allow-popups"';
  $the_content = preg_replace($pattern, $append, $the_content);
  $pattern = '/<\/iframe>/i';
  $append = $amp_placeholder.'</amp-iframe>';
  $the_content = preg_replace($pattern, $append, $the_content);

  //スクリプトを除去する
  $pattern = '/<p><script.+?<\/script><\/p>/i';
  $append = '';
  $the_content = preg_replace($pattern, $append, $the_content);
  $pattern = '/<script.+?<\/script>/is';
  $append = '';
  $the_content = preg_replace($pattern, $append, $the_content);

  //空のamp-imgタグは削除
  $pattern = '{<amp-img></amp-img>}i';
  $append = '';
  $the_content = preg_replace($pattern, $append, $the_content);


  //空のpタグは削除
  $pattern = '{<p></p>}i';
  $append = '';
  $the_content = preg_replace($pattern, $append, $the_content);

  // echo('<pre>');
  // var_dump(htmlspecialchars($the_content));
  // echo('</pre>');

  return $the_content;
}
endif;
add_filter('the_content','convert_content_for_amp', 999999999);

//テンプレートの中身をAMP化する
if ( !function_exists( 'get_template_part_amp' ) ):
function get_template_part_amp($template_name){
  ob_start();//バッファリング
  get_template_part($template_name);//テンプレートの呼び出し
  $template = ob_get_clean();//テンプレート内容を変数に代入
  $template = convert_content_for_amp($template);
  echo $template;
}
endif;

//AMP用のAdSenseコードを取得する
if ( !function_exists( 'generate_amp_adsense_code' ) ):
function generate_amp_adsense_code(){
  $adsense_code = null;
  if ( get_amp_adsense_code() || is_active_sidebar( 'adsense-300' ) ) {
    $ad300 = get_amp_adsense_code();
    ob_start();
    dynamic_sidebar('adsense-300');
    $ad300 .= ob_get_clean();
    //var_dump(htmlspecialchars($ad300));
    preg_match('/data-ad-client="(ca-pub-[^"]+?)"/i', $ad300, $m);
    if (empty($m[1])) return;
    $data_ad_client = $m[1];
    if (!$data_ad_client) return;
    preg_match('/data-ad-slot="([^"]+?)"/i', $ad300, $m);
    $data_ad_slot = $m[1];
    if (!$data_ad_slot) return;
    $adsense_code = '<amp-ad width="300" height="250" type="adsense" data-ad-client="'.$data_ad_client.'" data-ad-slot="'.$data_ad_slot.'"></amp-ad>';
    //var_dump(htmlspecialchars($adsense_code));
  }
  return $adsense_code;
}
endif;

//最初のH2の手前に広告を挿入（最初のH2を置換）
if ( !function_exists( 'add_ads_before_1st_h2_in_amp' ) ):
function add_ads_before_1st_h2_in_amp($the_content) {
  if ( is_amp() ) {//AMPの時のみ有効
    //広告（AdSense）タグを記入
    ob_start();//バッファリング
    get_template_part('ad-amp');//広告貼り付け用に作成したテンプレート
    $ad_template = ob_get_clean();
    $h2result = get_h2_included_in_body( $the_content );//本文にH2タグが含まれていれば取得
    if ( $h2result ) {//H2見出しが本文中にある場合のみ
      //最初のH2の手前に広告を挿入（最初のH2を置換）
      $count = 1;
      $the_content = preg_replace(H2_REG, $ad_template.$h2result, $the_content, $count);
    }
  }
  return $the_content;
}
endif;
add_filter('the_content','add_ads_before_1st_h2_in_amp');

//AMP用のURLを取得する
if ( !function_exists( 'get_amp_permalink' ) ):
function get_amp_permalink(){
  $permalink = get_permalink();
  //URLの中に?が存在しているか
  if (strpos($permalink,'?') !== false) {
    $amp_permalink = $permalink.'&amp;amp=1';
  } else {
    $amp_permalink = $permalink.'?amp=1';
  }
  return $amp_permalink;
}
endif;

//画像URLから幅と高さを取得する（同サーバー内ファイルURLのみ）
function get_image_width_and_height($image_url){
  //URLにサイトアドレスが含まれていない場合
  if (!includes_site_url($image_url)) {
    return false;
  }
  $wp_upload_dir = wp_upload_dir();
  $uploads_dir = $wp_upload_dir['basedir'];
  $uploads_url = $wp_upload_dir['baseurl'];
  $image_file = str_replace($uploads_url, $uploads_dir, $image_url);
  $imagesize = getimagesize($image_file);
  if ($imagesize) {
    $res = array();
    $res['width'] = $imagesize[0];
    $res['height'] = $imagesize[1];
    return $res;
  }
}

//AMPページではCrayon Syntax Highlighterを表示しない
if ( !function_exists( 'remove_crayon_syntax_highlighter' ) ):
function remove_crayon_syntax_highlighter() {
  if (isset($_GET['amp']) && $_GET['amp'] === '1') {
    //Crayon Syntax HighlighterはAMPページで適用しない
    remove_filter('the_posts', 'CrayonWP::the_posts', 100);
  }
}
endif;
add_action( 'wp_loaded','remove_crayon_syntax_highlighter' );

// //AMPページでFont AwesomeやGoogle Fontsを利用する
// if ( !function_exists( 'add_simplicity_amp_fonts' ) ):
// function add_simplicity_amp_fonts($data) {
//   $data['font_urls'] = array(
//       'FontAwesome' => 'https://max'.'cdn.boot'.'strapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
//   );
//   if (!is_site_font_default()) {
//     $data['font_urls'] += array(get_site_font_source() => get_site_font_source_url());
//   }
//   return $data;
// }
// endif;
// add_action( 'amp_post_template_data', 'add_simplicity_amp_fonts');