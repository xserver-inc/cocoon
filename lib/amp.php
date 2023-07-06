<?php //AMP
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//AMP判別関数
if ( !function_exists( 'is_amp' ) ):
function is_amp(){
  //bbPressがインストールされていて、トピックの時は除外
  if (is_plugin_fourm_page()) {
    return false;
  }

  //AMPチェック
  $is_amp = false;
  if ( empty($_GET['amp']) ) {
    return false;
  }

  // ampのパラメーターが1かつ
  // かつsingularページのみ$is_ampをtrueにする
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
  $category_ids = get_amp_exclude_category_ids();
  //return 1;
  return is_singular() &&
    is_amp_enable() &&
    is_the_page_amp_enable() &&
    !in_category( $category_ids ) && //除外カテゴリーではAMPページを生成しない
    //プラグインが生成するフォーラムページでない場合
    !is_plugin_fourm_page();
}
endif;

//AMP用にコンテンツを変換する
if ( !function_exists( 'convert_content_for_amp' ) ):
function convert_content_for_amp($the_content){
  if ( !is_amp() ) {
    return $the_content;
  }

  //iframe用のplaceholderタグ（amp-iframeの呼び出し位置エラー対策）
  $amp_placeholder = '<amp-img layout="fill" src="'.get_template_directory_uri().'/images/transparence.png'.'" placeholder></amp-img>';


  // バリューコマースのバナー変換
  $pattern = '/<script language="javascript" src="(https?)?\/\/ad\.jp\.ap\.valuecommerce\.com\/servlet\/jsbanner.+?<a.+?ck\.jp\.ap\.valuecommerce\.com.+?sid=(\d+).+?pid=(\d+).+?<\/a><\/noscript>/';

  $append =
    '<amp-ad width="300" height="250" type="valuecommerce" data-sid="$2" data-pid="$3"></amp-ad>';

  $the_content = preg_replace($pattern, $append, $the_content);

  //noscriptタグの削除
  $the_content = preg_replace('/<noscript>/i', '', $the_content);
  $the_content = preg_replace('/<\/noscript>/i', '', $the_content);

  //fontタグの削除
  $the_content = preg_replace('/<font[^>]*?>/i', '', $the_content);
  $the_content = preg_replace('/<\/font>/i', '', $the_content);

  //colタグのwidth属性削除
  $the_content = preg_replace('/<col(.*?) width="[^"]*?"(.*?)>/i', '<col$1$2>', $the_content);
  $the_content = preg_replace("/<col(.*?) width='[^']*?'(.*?)>/i", '<col$1$2>', $the_content);

  //Amazon商品リンクのhttp URLをhttpsへ
  $the_content = str_replace(
    'http://rcm-jp.amazon.co.jp/',
    'https://rcm-fe.amazon-adsystem.com/', $the_content);
  $the_content = str_replace(
    '"//rcm-fe.amazon-adsystem.com/',
    '"https://rcm-fe.amazon-adsystem.com/', $the_content);
  $the_content = str_replace(
    "'//rcm-fe.amazon-adsystem.com/",
    "'https://rcm-fe.amazon-adsystem.com/", $the_content);
  //Amazon商品画像のURLをhttpsへ
  $the_content = str_replace(
    'http://ecx.images-amazon.com',
    'https://images-fe.ssl-images-amazon.com', $the_content);
  //楽天商品画像のURLをhttpsへ
  $the_content = str_replace(
    'http://thumbnail.image.rakuten.co.jp',
    'https://thumbnail.image.rakuten.co.jp', $the_content);

  //レントラックス
  $the_content = str_replace(
    'http://www.rentracks.jp/adx/p.gifx',
    'https://www.rentracks.jp/adx/p.gifx', $the_content);
  $the_content = str_replace(
    'http://www.image-rentracks.com/',
    'https://www.image-rentracks.com/', $the_content);

  //YouTube iframeのsrc属性のhttp URLをhttpsへ
  $the_content = str_replace('http://www.youtube.com/', 'https://www.youtube.com/', $the_content);
  //JetpackがYouTubeのURLに余計なクエリを付け加えるのを取り除く
  //$the_content = preg_replace('{(https://www.youtube.com/embed/[^?"\']+)[^"\']*}i', '$1', $the_content);

  //Googleカスタム検索ボックス
  $the_content = preg_replace('{</?gcse:searchbox-only>}', '', $the_content);
  $the_content = preg_replace('{</?gcse:search>}', '', $the_content);

  //C2A0文字コード（UTF-8の半角スペース）を通常の半角スペースに置換
  $the_content = str_replace('\xc2\xa0', ' ', $the_content);

  //インラインスタイルを除去する場合
  if (!is_amp_inline_style_enable()) {
    //style属性を取り除く
    $the_content = preg_replace('/ +?style=["][^"]*?["]/i', '', $the_content);
    $the_content = preg_replace('/ +?style=[\'][^\']*?[\']/i', '', $the_content);
  } else {
    //style属性にzoomが入っていれば取り除く
    $pattern = '/style="(.*?)zoom:.+?;(.*?)"/i';
    $append = 'style="$1$2"';
    $the_content = preg_replace($pattern, $append, $the_content);
    //style属性に-webkitが入っていれば取り除く
    $pattern = '/style="(.*?)-webkit-.+?:.+?;(.*?)"/i';
    $append = 'style="$1$2"';
    $the_content = preg_replace($pattern, $append, $the_content);
    //style属性に_displayが入っていれば取り除く
    $pattern = '/style="(.*?)_display:.+?;(.*?)"/i';
    $append = 'style="$1$2"';
    $the_content = preg_replace($pattern, $append, $the_content);
    //style属性に!importantが入っていれば取り除く
    $pattern = '/style="(.*?)!important(.*?)"/i';
    $append = 'style="$1$2"';
    $the_content = preg_replace($pattern, $append, $the_content);
  }

  //target属性を取り除く
  $the_content = preg_replace('/ +?target=["](?!.*_blank).*?["]/i', '', $the_content);
  $the_content = preg_replace('/ +?target=[\'](?!.*_blank).*?[\']/i', '', $the_content);

  //rel属性を取り除く
  $the_content = preg_replace('/ +?rel=["][^"]*?["]/i', '', $the_content);
  $the_content = preg_replace('/ +?rel=[\'][^\']*?[\']/i', '', $the_content);

  //onclick属性を取り除く
  $the_content = preg_replace('/ +?onclick=["][^"]*?["]/i', '', $the_content);
  $the_content = preg_replace('/ +?onclick=[\'][^\']*?[\']/i', '', $the_content);

  //onload属性を取り除く
  $the_content = preg_replace('/ +?onload=["][^"]*?["]/i', '', $the_content);
  $the_content = preg_replace('/ +?onload=[\'][^\']*?[\']/i', '', $the_content);

  //marginwidth属性を取り除く
  $the_content = preg_replace('/ +?marginwidth=["][^"]*?["]/i', '', $the_content);
  $the_content = preg_replace('/ +?marginwidth=[\'][^\']*?[\']/i', '', $the_content);

  //marginheight属性を取り除く
  $the_content = preg_replace('/ +?marginheight=["][^"]*?["]/i', '', $the_content);
  $the_content = preg_replace('/ +?marginheight=[\'][^\']*?[\']/i', '', $the_content);

  //contenteditable属性を取り除く
  $the_content = preg_replace('/ +?contenteditable=["][^"]*?["]/i', '', $the_content);
  $the_content = preg_replace('/ +?contenteditable=[\'][^\']*?[\']/i', '', $the_content);

  //sandbox属性を取り除く
  $the_content = preg_replace('/ +?sandbox=["][^"]*?["]/i', '', $the_content);
  $the_content = preg_replace('/ +?sandbox=[\'][^\']*?[\']/i', '', $the_content);

  //security属性を取り除く
  $the_content = preg_replace('/ +?security=["][^"]*?["]/i', '', $the_content);
  $the_content = preg_replace('/ +?security=[\'][^\']*?[\']/i', '', $the_content);

  //decoding属性を取り除く
  $the_content = preg_replace('/ +?decoding=["][^"]*?["]/i', '', $the_content);
  $the_content = preg_replace('/ +?decoding=[\'][^\']*?[\']/i', '', $the_content);

  //loading属性を取り除く
  $the_content = preg_replace('/ +?loading=["][^"]*?["]/i', '', $the_content);
  $the_content = preg_replace('/ +?loading=[\'][^\']*?[\']/i', '', $the_content);

  //xml:lang属性を取り除く
  $the_content = preg_replace('/ +?xml:lang=["][^"]*?["]/i', '', $the_content);
  $the_content = preg_replace('/ +?xml:lang=[\'][^\']*?[\']/i', '', $the_content);

  // //type属性を取り除く
  $the_content = str_replace(' type="text/html"', '', $the_content);

  //loading="lazy"属性の削除
  $the_content = str_replace(' loading="lazy"', '', $the_content);

  //YouTubeプレイヤーのtype属性を取り除く
  $the_content = str_replace(" class='youtube-player' type='text/html'", " class='youtube-player'", $the_content);
  $the_content = str_replace(' class="youtube-player" type="text/html"', ' class="youtube-player"', $the_content);

  //FONTタグを取り除く
  $the_content = preg_replace('/<font[^>]+?>/i', '', $the_content);
  $the_content = preg_replace('/<\/font>/i', '', $the_content);

  //formタグを取り除く
  $the_content = preg_replace('{<form(?!.*class="amp-form).+?</form>}is', '', $the_content);

  //AMPフォームの場合はtarget="_top"を加える
  $the_content = str_replace('<form class="amp-form search-box"', '<form class="amp-form search-box" target="_top"', $the_content);

  //formタグを完全に取り除く
  $pattern = '{<form action="[^"]+?" method="get">(.*?</form>)?}is';
  $append = '';
  $the_content = preg_replace($pattern, $append, $the_content);

  //ドロップダンスカテゴリーの削除
  $pattern = '{<aside class="widget.+?categories-dropdown.+?</aside>}is';
  $append = '';
  $the_content = preg_replace($pattern, $append, $the_content);

  //selectタグを取り除く
  $the_content = preg_replace('{<select.+?</select>}is', '', $the_content);

  //アプリーチの画像対応
  $the_content = preg_replace('/<img([^>]+?src="[^"]+?(mzstatic\.com|phobos\.apple\.com|ggpht\.com)[^"]+?[^>\/]+alt="Appreach"[^>\/]+)\/?>/is', '<amp-img$1 width="75" height="75" sizes="(max-width: 75px) 100vw, 75px"></amp-img>', $the_content);
  $the_content = preg_replace('/<img([^>]+?src="[^"]+?nabettu\.github\.io[^"]+?[^>\/]+)\/?>/is', '<amp-img$1 width="120" height="36" sizes="(max-width: 120px) 100vw, 120px"></amp-img>', $the_content);

  //数式変換
  if (is_formula_enable() && is_math_shortcode_exist()) {

    if (preg_match_all('#<p[^>]*?>[\s\S]+?</p>#', $the_content, $m)) {
      $paragraphs = $m[0];
      foreach ($paragraphs as $paragraph) {
        $old_p = $paragraph;
        $new_p = preg_replace('/\\\\\\(.+?\\\\\\)/', '<amp-mathml layout="container" inline data-formula="$0"></amp-mathml>', $old_p);
        $new_p = preg_replace('/\\\\\\[[\s\S]+?\\\\\\]/', '<amp-mathml layout="container" data-formula="$0"></amp-mathml>', $new_p);
        $the_content = str_replace($old_p, $new_p, $the_content);
      }
    }

  }

  //imgタグをamp-imgタグに変更する
  $res = preg_match_all('/<img(.+?)\/?>/is', $the_content, $m);

  if ($res) {//画像タグがある場合
    foreach ($m[0] as $match) {
      //変数の初期化
      $src_attr = null;
      $url = null;
      $id_attr = null;
      $class_attr = null;
      $width_attr = null;
      $width_value = null;
      $height_attr = null;
      $height_value = null;
      $alt_attr = null;
      $alt_value = null;
      $title_attr = null;
      $title_value = null;
      $sizes_attr = null;

      //src属性の取得（画像URLの取得）
      $src_res = preg_match('/src=["\']([^"\']+?)["\']/is', $match, $srcs);
      if ($src_res) {
        $src_attr = ' '.$srcs[0];//src属性を作成
        $url = $srcs[1];//srcの値（URL）を取得する
      }

      //id属性の取得
      $id_res = preg_match('/id=["\']([^"\']*?)["\']/is', $match, $ids);
      if ($id_res) {
        $id_attr = ' '.$ids[0];//id属性を作成
      }

      //class属性の取得
      $class_res = preg_match('/class=["\']([^"\']*?)["\']/is', $match, $classes);
      if ($class_res) {
        $class_attr = ' '.$classes[0];//id属性を作成
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

      //widthとheight属性のないものは画像から情報取得
      if ($url && (empty($width_value) || empty($height_value))) {
        $size = get_image_width_and_height($url);
        if ($size) {
          $width_value = $size['width'];
          $width_attr = ' width="'.$width_value.'"';//width属性を作成
          $height_value = $size['height'];
          $height_attr = ' height="'.$height_value.'"';//height属性を作成
        } else {
          //外部サイトにある画像の場合
          if (
            strpos($url,'//images-fe.ssl-images-amazon.com') !== false ||
            strpos($url,'//thumbnail.image.rakuten.co.jp') !== false ||
            strpos($url,'//item.shopping.c.yimg.jp') !== false
          ) {
            //Amazon・楽天・Yahoo!ショッピング商品画像にwidthとheightの属性がない場合
            $width_value = get_amp_product_image_width();
            $width_attr = ' width="'.$width_value.'"';//width属性を作成
            $height_value = get_amp_product_image_height();
            $height_attr = ' height="'.$height_value.'"';//height属性を作成
          } else {
            $width_value = get_amp_default_image_width();
            $width_attr = ' width="'.$width_value.'"';//width属性を作成
            $height_value = get_amp_default_image_height();
            $height_attr = ' height="'.$height_value.'"';//height属性を作成
          }
        }
      }

      //sizes属性の作成（きれいなレスポンシブ化のために）
      if ($width_value) {
        $sizes_attr = ' sizes="(max-width: '.$width_value.'px) 100vw, '.$width_value.'px"';
      }

      //amp-imgタグの作成
      $tag = '<amp-img'.$src_attr.$id_attr.$class_attr.$width_attr.$height_attr.$alt_attr.$title_attr.$sizes_attr.'></amp-img>';

      //imgタグをamp-imgタグに置換
      $the_content = preg_replace('{'.preg_quote($match).'}', $tag , $the_content, 1);
    }
  }

  //画像タグをAMP用に置換
  $the_content = preg_replace('/<img(.+?)\/?>/is', '<amp-img$1></amp-img>', $the_content);
  /* AMP画像置換修正候補
  $the_content = preg_replace('{<img([^>]+?)/?>}is', '<amp-img$1></amp-img>', $the_content);
  */

  // Twitterをamp-twitterに置換する（埋め込みコード）
  /*
  $pattern = '{<blockquote class="twitter-tweet".*?>.+?<a.+?href="https://twitter.com/.*?/status/([^\?"]+).*?">.+?</blockquote>}is';
  */
  $pattern = '{<blockquote class="twitter-tweet"[^>]*?>.+?<a[^>]+?href="https://twitter\.com/[^/]*?/status/([^\?"]+)[^>]*?">.+?</blockquote>}is';
  $append = '<p><amp-twitter width=592 height=472 layout="responsive" data-tweetid="$1"></amp-twitter></p>';
  $the_content = preg_replace($pattern, $append, $the_content);

  // JetpackによるFacebook埋め込みをamp-facebookに置換する（埋め込みコード）
  $pattern = '{<fb:post href="([^"]+?)"></fb:post>}is';
  $append = '<amp-facebook width=324 height=438 layout="responsive" data-href="$1"></amp-facebook>';
  $the_content = preg_replace($pattern, $append, $the_content);

  // vineをamp-vineに置換する（サービス終了したけど一応）
  $pattern = '{<iframe[^>]+?src="https://vine.co/v/([^/]+?)/embed/simple"[^>]+?></iframe>}is';
  $append = '<amp-vine data-vineid="$1" width="592" height="592" layout="responsive"></amp-vine>';
  $the_content = preg_replace($pattern, $append, $the_content);

  // Instagramをamp-instagramに置換する
  //$pattern = '{<blockquote class="instagram-media".+?"https://www.instagram.com/p/(.+?)/.*?".+?</blockquote>}is';
  $pattern = '{<blockquote class="instagram-media"[^>]+?"https://www.instagram.com/p/([^/]+?)/[^"]*?".+?</blockquote>}is';
  $append = '<amp-instagram layout="responsive" data-shortcode="$1" width="592" height="592" ></amp-instagram>';
  $the_content = preg_replace($pattern, $append, $the_content);

  // audioをamp-audioに置換する
  $pattern = '{<audio .+?src="([^"]+?)".+?</audio>}is';
  $append = '<amp-audio src="$1"></amp-audio>';
  $the_content = preg_replace($pattern, $append, $the_content);

  // videoをamp-videoに置換する
  $pattern = '<video';
  $append = '<amp-video layout="responsive"';
  $the_content = str_replace($pattern, $append, $the_content);
  $pattern = '</video>';
  $append = '</amp-video>';
  $the_content = str_replace($pattern, $append, $the_content);


  // YouTubeを置換する（埋め込みコード）
  $pattern = '{<iframe[^>]+?src="https?://www\.youtube\.com/embed/([^\?"]+)[^"]*?"[^>]*?></iframe>}is';
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


  //はてなブログカードiframe用の処理追加
  $pattern = '{<iframe[^>]+?src="(https?://hatenablog-parts\.com/embed.+?)"[^>]+?></iframe>}is';
  $append = '<amp-iframe src="$1" width="500" height="190"></amp-iframe>';
  $the_content = preg_replace($pattern, $append, $the_content);

  //Amazon商品紹介iframeのAMP化
  $pattern = '{<iframe[^>]+?src="(.+?rcm-fe\.amazon-adsystem\.com[^"]+?)"[^>]+?(width="(\d+)")?.(height="(\d+)")?.+?</iframe>}is';
  if (preg_match_all($pattern, $the_content, $m)) {
    $all_idx = 0;
    $url_idx = 1;
    $width_attr_idx = 2;
    $width_idx = 3;
    $height_attr_idx = 4;
    $height_idx = 5;

    if ($m[0]) {
      $i = 0;
      foreach ($m[$all_idx] as $key => $iframe_raw) {
        $url = $m[$url_idx][$i];
        $width = 120;
        $height = 240;
        if (isset($m[$width_idx]) && isset($m[$height_idx])) {
          if (!empty($m[$width_idx][$i]) && !empty($m[$height_idx][$i])) {
            $width = $m[$width_idx][$i];
            $height = $m[$height_idx][$i];
          }
        }
        $iframe_new = '<amp-iframe sandbox="allow-scripts allow-same-origin allow-popups" src="'.$url.'" width="'.$width.'" height="'.$height.'">'.$amp_placeholder.'</amp-iframe>';

        $the_content = str_replace($iframe_raw, $iframe_new, $the_content);
        $i++;
      }
    }
  }

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

  //カエレバのstyle属性を取り除く
  $pattern = '{<div class="(kaerebalink-box|booklink-box|tomarebalink-box)".+?<div class="booklink-footer"}is';
  if (preg_match_all($pattern, $the_content, $m)) {
    if (isset($m[0])) {
      foreach ($m[0] as $match) {
        $kaereba_tag = $match;
        $replaced_kaereba_tag = preg_replace('/ +?style=["][^"]*?["]/i', '', $kaereba_tag);
        $the_content = str_replace($kaereba_tag, $replaced_kaereba_tag, $the_content);
      }

    }
  }

  //スクリプトを除去する
  $pattern = '{(<p>)<script[^>]+?></script>(</p>)/}i';
  $append = '';
  $the_content = preg_replace($pattern, $append, $the_content);
  $pattern = '/<script(?!.*type="application\/json").+?<\/script>/is';
  $append = '';
  $the_content = preg_replace($pattern, $append, $the_content);

  //スタイルを除去する
  $pattern = '{<style.+?</style>}is';
  $append = '';
  $the_content = preg_replace($pattern, $append, $the_content);
  //@keyframesスタイルを</body>手前に記入
  $amp_keyframes_tag = get_style_amp_keyframes_tag();
  $pattern = '</body>';
  $append = $amp_keyframes_tag."\n".$pattern;
  $the_content = str_replace($pattern, $append, $the_content);

  //空のamp-imgタグは削除
  $pattern = '{<amp-img></amp-img>}i';
  $append = '';
  $the_content = preg_replace($pattern, $append, $the_content);

  //空のpタグは削除
  $pattern = '{<p></p>}i';
  $append = '';
  $the_content = preg_replace($pattern, $append, $the_content);

  //画像拡大効果spotlightクラスの削除
  $pattern = '<a class="spotlight" ';
  $append = '<a ';
  $the_content = str_replace($pattern, $append, $the_content);

  switch (get_amp_image_zoom_effect()) {
    case 'amp-image-lightbox':
      //amp-img を amp-image-lightbox 用に置換
      $pattern     = '{<a ([a-z]+?="[^"]+?" )?href="[^"]+?/wp-content/uploads[^"]+?"><amp-img([^>]+?)></amp-img></a>}i';
      // $append      = '<amp-img class="amp-lightbox amp-image-lightbox" on="tap:amp-lightbox" role="button" tabindex="0"$1></amp-img>';

      if (preg_match_all($pattern, $the_content, $m)) {
        $all_idx = 0;
        $etc_idx = 2;
        $i = 0;
        foreach ($m[$all_idx] as $tag) {
          $all_tag = $tag;
          $etc_tag = $m[$etc_idx][$i];
          if (preg_match('/class="(.+?)"/i', $etc_tag, $n)) {
            $etc_tag = preg_replace('/class=".+?"/i', '', $etc_tag );
            $the_content = str_replace($all_tag,
            '<p><amp-img class="amp-lightbox amp-image-lightbox '.$n[1].'" on="tap:amp-lightbox" role="button" tabindex="0"'.$etc_tag.'></amp-img></p>',
              $the_content);
          } else {
            $the_content = str_replace($all_tag,
              '<p><amp-img class="amp-lightbox amp-image-lightbox" on="tap:amp-lightbox" role="button" tabindex="0"'.$etc_tag.'></amp-img></p>',
              $the_content);
          }
          $i++;
        }
      }
      break;
    case 'amp-lightbox-gallery':
      // amp-img を amp-lightbox-gallery 用に置換
      $pattern     = '{<a ([a-z]+?="[^"]+?" )?href="[^"]+?/wp-content/uploads[^"]+?"><amp-img([^>]+?)></amp-img></a>}i';
      if (preg_match_all($pattern, $the_content, $m)) {
        $all_idx = 0;
        $etc_idx = 2;
        $i = 0;
        foreach ($m[$all_idx] as $tag) {
          $all_tag = $tag;
          $etc_tag = $m[$etc_idx][$i];
          if (preg_match('/class="(.+?)"/i', $etc_tag, $n)) {
            $etc_tag = preg_replace('/class=".+?"/i', '', $etc_tag );
            $the_content = str_replace($all_tag,
              '<p><amp-img class="amp-lightbox amp-lightbox-gallery '.$n[1].'" lightbox'.$etc_tag.'></amp-img></p>',
              $the_content);
          } else {
            $the_content = str_replace($all_tag,
              '<p><amp-img class="amp-lightbox amp-lightbox-gallery" lightbox'.$etc_tag.'></amp-img></p>',
              $the_content);
          }
          $i++;
        }
      }
      break;
  }

  return apply_filters('convert_content_for_amp', $the_content);
}//convert_content_for_amp
endif;

//テンプレートの中身をAMP化する
if ( !function_exists( 'get_template_part_amp' ) ):
function get_template_part_amp($template_name){
  ob_start();//バッファリング
  cocoon_template_part($template_name);//テンプレートの呼び出し
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

    preg_match('/data-ad-client="(ca-pub-[^"]+?)"/i', $ad300, $m);
    if (empty($m[1])) return;
    $data_ad_client = $m[1];
    if (!$data_ad_client) return;
    preg_match('/data-ad-slot="([^"]+?)"/i', $ad300, $m);
    $data_ad_slot = $m[1];
    if (!$data_ad_slot) return;
    $adsense_code = '<amp-ad width="300" height="250" type="adsense" data-ad-client="'.$data_ad_client.'" data-ad-slot="'.$data_ad_slot.'"></amp-ad>';
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
    cocoon_template_part('ad-amp');//広告貼り付け用に作成したテンプレート
    $ad_template = ob_get_clean();
    $h2result = get_h2_included_in_body( $the_content );//本文にH2タグが含まれていれば取得
    if ( $h2result ) {//H2見出しが本文中にある場合のみ
      //最初のH2の手前に広告を挿入（最初のH2を置換）
      $count = 1;
      $the_content = preg_replace(H2_REG, $ad_template.PHP_EOL.PHP_EOL.$h2result, $the_content, $count);
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

//AMPページではCrayon Syntax Highlighterを表示しない
add_action( 'wp_loaded','remove_crayon_syntax_highlighter' );
if ( !function_exists( 'remove_crayon_syntax_highlighter' ) ):
function remove_crayon_syntax_highlighter() {
  //is_ampではダメ
  if (isset($_GET['amp']) && $_GET['amp'] === '1') {
    //Crayon Syntax HighlighterはAMPページで適用しない
    remove_filter('the_posts', 'CrayonWP::the_posts', 100);
  }
}
endif;

if ( !function_exists( 'get_the_singular_content' ) ):
function get_the_singular_content(){
  $all_content = null;

  ob_start();//バッファリング
  cocoon_template_part('tmp/body-top');//bodyタグ直下から本文まで
  $body_top_content = ob_get_clean();

  ob_start();//バッファリング
  if (is_single()) {
    cocoon_template_part('tmp/single-contents');
  } else {
    cocoon_template_part('tmp/page-contents');
  }
  $body_content = ob_get_clean();

  ob_start();//バッファリング
  dynamic_sidebar( 'sidebar' );
  $sidebar_content = ob_get_clean();

  ob_start();//バッファリング
  dynamic_sidebar( 'sidebar-scroll' );
  $sidebar_scroll_content = ob_get_clean();

  ob_start();//バッファリング
  dynamic_sidebar('footer-left');
  dynamic_sidebar('footer-center');
  dynamic_sidebar('footer-right');
  dynamic_sidebar('footer-mobile');
  $footer_content = ob_get_clean();

  ob_start();//バッファリング
  cocoon_template_part('tmp/amp-footer-insert');
  $footer_insert = ob_get_clean();

  //モバイルメニューボタン
  //モバイルフッターボタンのみ
  if (is_mobile_button_layout_type_footer_mobile_buttons()) {
    ob_start();
    cocoon_template_part('tmp/mobile-footer-menu-buttons');
    $mobile_menu_buttons = ob_get_clean();
  } elseif //モバイルヘッダーボタンのみ
  (is_mobile_button_layout_type_header_mobile_buttons()) {
    ob_start();
    cocoon_template_part('tmp/mobile-header-menu-buttons');
    $mobile_menu_buttons = ob_get_clean();
  } else {//ヘッダーとフッター双方のモバイルボタン
    ob_start();
    cocoon_template_part('tmp/mobile-header-menu-buttons');
    cocoon_template_part('tmp/mobile-footer-menu-buttons');
    $mobile_menu_buttons = ob_get_clean();
  }

  $all_content = $body_top_content.$body_content.$sidebar_content.$sidebar_scroll_content.$footer_content.$footer_insert;

  return $all_content;
}
endif;

//<style amp-custom>タグの作成
if ( !function_exists( 'generate_style_amp_custom_tag' ) ):
function generate_style_amp_custom_tag(){?>
<style amp-custom><?php

  $css_all = '';
  //AMPスタイルの取得（SCSSで出力したAMP用のCSS）
  $css_url = get_template_directory_uri().'/amp.css';
  $css = css_url_to_css_minify_code($css_url);
  if ($css !== false) {
    $css_all .= apply_filters( 'amp_parent_css', $css );
  }

  ///////////////////////////////////////////
  //IcoMoonのスタイル
  ///////////////////////////////////////////
  $css_icomoom = css_url_to_css_minify_code(get_template_directory_uri().'/webfonts/icomoon/style.css');
  if ($css_icomoom !== false) {
    $css_all .= apply_filters( 'amp_icomoon_css', $css_icomoom );
  }

  ///////////////////////////////////////////
  //Font Awesome5のスタイル
  ///////////////////////////////////////////
  if (is_site_icon_font_font_awesome_5()) {
    $css_fa5 = css_url_to_css_minify_code(get_template_directory_uri().'/css/fontawesome5.css');
    if ($css_fa5 !== false) {
      $css_all .= apply_filters( 'amp_font_awesome_5_css', $css_fa5 );
    }
  }

  ///////////////////////////////////////////
  //スキンのスタイル
  ///////////////////////////////////////////
  if ( ($skin_url = get_skin_url()) && is_amp_skin_style_enable() ) {//設定されたスキンがある場合
    //通常のスキンスタイル
    $skin_css = css_url_to_css_minify_code($skin_url);
    if ($skin_css !== false) {
      $css_all .= apply_filters( 'amp_skin_css', $skin_css );
    }

    //AMPのスキンスタイル
    $amp_css_url = str_replace('style.css', 'amp.css', $skin_url);
    $amp_css = css_url_to_css_minify_code($amp_css_url);
    if ($amp_css !== false) {
      $css_all .= apply_filters( 'amp_skin_amp_css', $amp_css );
    }
  }

  ///////////////////////////////////////////
  //カスタマイザーのスタイル
  ///////////////////////////////////////////
  ob_start();//バッファリング
  cocoon_template_part('tmp/css-custom');//カスタムテンプレートの呼び出し
  $css_custom = ob_get_clean();
  $css_all .= apply_filters( 'amp_custum_css', $css_custom );

  ///////////////////////////////////////////
  //子テーマのスタイル
  ///////////////////////////////////////////
  if ( is_child_theme() && is_amp_child_theme_style_enable() ) {
    //通常のスキンスタイル
    $css_child_url = CHILD_THEME_STYLE_CSS_URL;
    $child_css = css_url_to_css_minify_code($css_child_url);
    if ($child_css !== false) {
      $css_all .= apply_filters( 'amp_child_css', $child_css );
    }

    //AMPのスキンスタイル
    $css_child_url = get_stylesheet_directory_uri().'/amp.css';
    $child_amp_css = css_url_to_css_minify_code($css_child_url);
    if ($child_amp_css !== false) {
      $css_all .= apply_filters( 'amp_child_amp_css', $child_amp_css );
    }
  }
  ///////////////////////////////////////////
  //カスタマイザー「追加CSS」のスタイル
  ///////////////////////////////////////////
  if ($wp_custom_css = wp_get_custom_css()){
    $css_all .= apply_filters( 'amp_wp_custom_css', $wp_custom_css );
  }

  ///////////////////////////////////////////
  //投稿・固定ページに記入されているカスタムCSS
  ///////////////////////////////////////////
  if ($custom_css = get_custom_css_code()) {
    $css_all .= apply_filters( 'amp_singular_custom_css', $custom_css );
  }

  //!importantの除去
  $css_all = preg_replace('/!important/i', '', $css_all);

  //CSSの縮小化
  $css_all = minify_css($css_all);

  //全てのCSSの出力
  echo apply_filters( 'amp_all_css', $css_all );
  //}?></style>
<?php
}
endif;

//<style amp-keyframes>タグの取得
if ( !function_exists( 'get_style_amp_keyframes_tag' ) ):
function get_style_amp_keyframes_tag(){
  $css_all = '';
  //AMPスタイルの取得（SCSSで出力したAMP用のCSS）
  $keyframes_css_url = get_template_directory_uri().'/keyframes.css';
  $css = css_url_to_css_minify_code($keyframes_css_url);
  if ($css !== false) {
    $css_all .= apply_filters( 'amp_parent_keyframes_css', $css );
  }

  ///////////////////////////////////////////
  //スキンのスタイル
  ///////////////////////////////////////////
  if ( ($skin_url = get_skin_url()) && is_amp_skin_style_enable() ) {//設定されたスキンがある場合
    //通常のスキンスタイル
    $skin_keyframes_url = str_replace('style.css', 'keyframes.css', $skin_url);
    $skin_keyframes_css = css_url_to_css_minify_code($skin_keyframes_url);
    if ($skin_keyframes_css !== false) {
      $css_all .= apply_filters( 'amp_skin_keyframes_css', $skin_keyframes_css );
    }
  }

  ///////////////////////////////////////////
  //子テーマのスタイル
  ///////////////////////////////////////////
  if ( is_child_theme() && is_amp_child_theme_style_enable() ) {
    //通常のスキンスタイル
    $css_child_keyframes_url = CHILD_THEME_KEYFRAMES_CSS_URL;
    $child_keyframes_css = css_url_to_css_minify_code($css_child_keyframes_url);
    if ($child_keyframes_css !== false) {
      $css_all .= apply_filters( 'amp_child_keyframes_css', $child_keyframes_css );
    }
  }

  //!importantの除去
  $css_all = preg_replace('/!important/i', '', $css_all);

  //CSSの縮小化
  $css_all = minify_css($css_all);
  $css_all = apply_filters( 'amp_all_keyframes_css', $css_all );

  $tag = '<style amp-keyframes>'.$css_all.'</style>';
  //全てのCSSの出力
  return $tag;
}
endif;

//<style amp-keyframes>タグの出力
if ( !function_exists( 'generate_style_amp_keyframes_tag' ) ):
function generate_style_amp_keyframes_tag(){
  echo get_style_amp_keyframes_tag();
}
endif;


if ( !function_exists( 'get_cleaned_css_selector' ) ):
function get_cleaned_css_selector($selector){
  //class用のドットを取り除く
  $selector = str_replace('.', ' ', $selector);
  //ID用のシャープを取り除く
  $selector = str_replace('#', ' ', $selector);
  //>をスペースに変換
  $selector = str_replace('>', ' ', $selector);
  //~をスペースに変換
  $selector = str_replace('~', ' ', $selector);
  ///////////////////////////////////////
  // 擬似要素
  ///////////////////////////////////////
  //:beforeを取り除く
  $selector = str_replace(':before', '', $selector);
  //:afterを取り除く
  $selector = str_replace(':after', '', $selector);

  ///////////////////////////////////////
  // 疑似クラスなどを取り除く
  ///////////////////////////////////////
  $classes = array('input\[type="checkbox"\]',':nth-last-of-type\(.*?\)',':nth-last-child\(.*?\)',':nth-of-type\(.*?\)',':nth-child\(.*?\)',':active',':checked',':default',':disabled',':empty',':enabled',':first-child',':first-of-type',':fullscreen',':indeterminate',':in-range',':invalid',':last-child',':last-of-type',':only-child',':only-of-type',':optional',':out-of-range',':read-only',':read-write',':required',':right',':root',':scope',':target',':valid',':visited',':first',':any',':focus',':hover',':left',':link');
  foreach ($classes as $class) {
    $selector = preg_replace('/'.$class.'/', '', $selector);
  }

  //:を取り除く
  $selector = str_replace(':', '', $selector);
  //連続した半角スペースを1つに置換
  $selector = str_replace('  ', ' ', $selector);
  return $selector;
}
endif;

//CSSセレクターセットが本文内に存在するか（厳密な判別ではない、結構大ざっぱ）
if ( !function_exists( 'is_comma_splited_selector_exists_in_body_tag' ) ):
function is_comma_splited_selector_exists_in_body_tag($comma_splited_selector, $body_tag){
  //amp-imgが含まれるCSSセレクタは除外しない
  if (strpos($comma_splited_selector, 'amp-img') !== false) {
    return true;
  }

  $comma_splited_selector = get_cleaned_css_selector($comma_splited_selector);
  $space_splited_selectors = explode(' ', $comma_splited_selector);

  foreach ($space_splited_selectors as $selector) {
    //調べるまでもなく最初から存在するとわかっているセレクターは次に飛ばす（多少なりとも処理時間の短縮）
    $elements = array('html', 'body', 'div', 'span', 'a', 'aside', 'section', 'figure', 'main', 'header', 'footer', 'sidebar', 'article', 'ul', 'ol', 'li', 'p', 'h1', 'h2', 'h3');
    if (in_array($selector, $elements)) {
      continue;
    }

    //本文内にセレクタータグが存在しない場合
    if ($selector && strpos($body_tag, $selector) === false) {
      return false;
    }
  }
  return true;
}
endif;

//AMP用のCSSから不要なCSSコードを取り除く（なるべくAMPの50KB制限に引っかからないようにサイズ節約）
if ( !function_exists( 'get_dieted_amp_css_tag' ) ):
function get_dieted_amp_css_tag($style_amp_custom_tag, $body_tag){
  $css = $style_amp_custom_tag;
  if (preg_match_all('/\}([\.#\-a-zA-Z0-9\s>,:]+?)\{/i', $css, $m)
      && isset($m[1])) {
    $selectors = $m[1];
    //重複は統一
    $selectors = array_unique($selectors);
    //_v($selectors);
    $delete_target_selectors = array();
    //セレクター判別用の清掃
    foreach ($selectors as $selector) {
      //カンマで区切られたCSS配列を分割
      $comma_splited_selectors = explode(',', $selector);
      $comma_splited_selectors = array_unique($comma_splited_selectors);

      foreach ($comma_splited_selectors as $comma_splited_selector) {
        //置換用のターゲットCSSセレクタの保存
        $delete_target_selector = $comma_splited_selector;
        if (!is_comma_splited_selector_exists_in_body_tag($comma_splited_selector, $body_tag)) {
          $delete_target_selectors[] = $delete_target_selector;
        }
      }
    }

    //削除候補のCSSセレクタを置換で削除
    foreach ($delete_target_selectors as $delete_target_selector) {
      $css = preg_replace('/\}'.preg_quote($delete_target_selector, '/').',/i', '}', $css);
      $css = preg_replace('/\}'.preg_quote($delete_target_selector, '/').'{/i', '}{', $css);
    }
    //カッコ{css codf}のみになっているCSSを削除
    $css = preg_replace('/(\{.+?\})(\{.+?\})+/i', '$1', $css);

    //余計なメディアクエリを削除
    //$css = preg_replace('/@media screen and \(max-width:\d+px\)\{\}/i', '', $css);

  }
  return $css;
}
endif;

//AMP化コールバックの開始
add_action( 'wp_loaded','wp_loaded_ampfy_html', 1 );
if ( !function_exists( 'wp_loaded_ampfy_html' ) ):
function wp_loaded_ampfy_html() {
  ob_start('html_ampfy_call_back');
}
endif;

//AMP化処理
if ( !function_exists( 'html_ampfy_call_back' ) ):
function html_ampfy_call_back( $html ) {
  if (is_admin()) {
    return $html;
  }

  if (!is_amp()) {
    return $html;
  }

  global $post;
  //キャッシュの存在
  $transient_id = TRANSIENT_AMP_PREFIX.$post->ID;
  $transient_file = get_theme_amp_cache_path().$transient_id;
  $file_path_cache = get_transient( $transient_id );
  if ($file_path_cache && DEBUG_CACHE_ENABLE && !is_user_administrator()) {
    if (file_exists($transient_file)) {
      $html_cache = wp_filesystem_get_contents($transient_file);
      if ($html_cache) {
        return $html_cache;
      }
    }
  }

  $head_tag = null;
  $body_tag = null;
  $style_amp_custom_tag = null;
  //ヘッダータグの取得
  if (preg_match('{<!doctype html>.+</head>}is', $html, $m)) {
    if (isset($m[0])) {
      $head_tag = $m[0];
    }
  }

  //ボディータグの取得
  if (preg_match('{<body .+</html>}is', $html, $m)) {
    if (isset($m[0])) {
      $body_tag = $m[0];
    }
  }

  //AMP用CSSスタイルの取得
  if (preg_match('{<style amp-custom>.+</style>}is', $head_tag, $m)) {
    if (isset($m[0])) {
      $default_style_amp_custom_tag = $m[0];

      //不要なCSSを削除してサイズ削減
      $dieted_style_amp_custom_tag = get_dieted_amp_css_tag($default_style_amp_custom_tag, $body_tag);

      //ヘッダーの<style amp-custom>をサイズ削減したものに入れ替える
      $head_tag = str_replace($default_style_amp_custom_tag, $dieted_style_amp_custom_tag, $head_tag);
    }
  }

  if ($head_tag && $body_tag) {
    //bodyタグ内をAMP化
    $body_tag = convert_content_for_amp($body_tag);
    //AMP用headタグ編集用のフック
    $head_tag = apply_filters('amp_html_head_tag', $head_tag);
    //AMP用bodyタグ編集用のフック
    $body_tag = apply_filters('amp_html_body_tag', $body_tag);
    $all_tag = $head_tag . $body_tag;

    //AMPキャッシュの保存
    $is_include_body = includes_string($all_tag, '</body>');
    if ($is_include_body && DEBUG_CACHE_ENABLE && !is_user_administrator()) {
      set_transient($transient_id, $transient_file, DAY_IN_SECONDS * 1);
      wp_filesystem_put_contents($transient_file, $all_tag);
    }

    //AMP用全てのHTMLタグ編集用のフック
    return $body_tag = apply_filters('amp_html_all_tag', $all_tag);
  }

  return $html;
}
endif;

//AMPのデフォルト画像幅
if ( !function_exists( 'get_amp_default_image_width' ) ):
function get_amp_default_image_width(){
  return 300;
}
endif;

//AMPのデフォルト画像高さ
if ( !function_exists( 'get_amp_default_image_height' ) ):
function get_amp_default_image_height(){
  return 300;
}
endif;

//AMPのデフォルト商品画像幅
if ( !function_exists( 'get_amp_product_image_width' ) ):
function get_amp_product_image_width(){
  return 75;
}
endif;

//AMPのデフォルト商品画像高さ
if ( !function_exists( 'get_amp_product_image_height' ) ):
function get_amp_product_image_height(){
  return 75;
}
endif;

//AMPのiframeはLazy Loadしない
add_filter( 'wp_lazy_loading_enabled', 'disable_post_content_iframe_lazy_loading_for_amp', 10, 3 );
if ( !function_exists( 'disable_post_content_iframe_lazy_loading_for_amp' ) ):
function disable_post_content_iframe_lazy_loading_for_amp( $default, $tag_name, $context ) {
  if ( is_amp() && $tag_name === 'iframe' ) {
      return false;
  }
  return $default;
}
endif;

