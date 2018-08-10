<?php //ショートコード
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

//プロフィールショートコード関数
add_shortcode('author_box', 'author_box_shortcode');
if ( !function_exists( 'author_box_shortcode' ) ):
function author_box_shortcode($atts) {
  extract(shortcode_atts(array(
    'label' => '',
  ), $atts));
  ob_start();
  generate_author_box_tag($label);
  $res = ob_get_clean();
  return $res;
}
endif;

//新着記事ショートコード関数
add_shortcode('new_list', 'new_entries_shortcode');
if ( !function_exists( 'new_entries_shortcode' ) ):
function new_entries_shortcode($atts) {
  extract(shortcode_atts(array(
    'count' => 5,
    'cats' => 'all',
    'type' => 'default',
    'children' => 0,
    'post_type' => 'post',
    'taxonomy' => 'category',
  ), $atts));
  $categories = array();
  //var_dump($cats);
  if ($cats && $cats != 'all') {
    $categories = explode(',', $cats);
  }
  ob_start();
  generate_new_entries_tag($count, $type, $categories, $children, $post_type, $taxonomy);
  $res = ob_get_clean();
  return $res;
}
endif;

//人気記事ショートコード関数
add_shortcode('popular_list', 'popular_entries_shortcode');
if ( !function_exists( 'popular_entries_shortcode' ) ):
function popular_entries_shortcode($atts) {
  extract(shortcode_atts(array(
    'days' => 'all',
    'count' => 5,
    'type' => 'default',
    'rank' => 0,
    'pv' => 0,
    'cats' => 'all',
  ), $atts));
  $categories = array();
  if ($cats && $cats != 'all') {
    $categories = explode(',', $cats);
  }
  ob_start();
  generate_popular_entries_tag($days, $count, $type, $rank, $pv, $categories);
  $res = ob_get_clean();
  return $res;
}
endif;

define('AFFI_SHORTCODE', 'affi');
//アフィリエイトタグショートコード関数
add_shortcode(AFFI_SHORTCODE, 'affiliate_tag_shortcode');
if ( !function_exists( 'affiliate_tag_shortcode' ) ):
function affiliate_tag_shortcode($atts) {
  extract(shortcode_atts(array(
    'id' => 0,
  ), $atts));
  if ($id) {
    if ($recode = get_affiliate_tag($id)) {

      global $post;
      $atag = $recode->text;

      //無限ループ要素の除去
      // $shortcode = get_affiliate_tag_shortcode($id);
      // $atag = str_replace($shortcode, '', $atag);
      $atag = preg_replace('{\['.AFFI_SHORTCODE.'[^\]]*?id=[\'"]?'.$id.'[\'"]?[^\]]*?\]}i', '', $atag);

      $post_id = null;
      if (isset($post->ID)) {
        $post_id = 'data-post-id="'.$post->ID.'" ';
      }
      //計測用の属性付与
      $atag = str_replace('<a ', '<a data-atag-id="'.$id.'" '.$post_id, $atag);

      return do_shortcode($atag);
    }
  }

}
endif;
//アフィリエイトショートコードの生成
if ( !function_exists( 'get_affiliate_tag_shortcode' ) ):
function get_affiliate_tag_shortcode($id) {
  return "[".AFFI_SHORTCODE." id={$id}]";
}
endif;

define('TEMPLATE_SHORTCODE', 'temp');
//関数テキストショートコード関数
add_shortcode(TEMPLATE_SHORTCODE, 'function_text_shortcode');
if ( !function_exists( 'function_text_shortcode' ) ):
function function_text_shortcode($atts) {
  extract(shortcode_atts(array(
    'id' => 0,
  ), $atts));
  if ($id) {
    if ($recode = get_function_text($id)) {
      //無限ループ要素の除去
      //$shortcode = get_function_text_shortcode($id);
      $template = preg_replace('{\['.TEMPLATE_SHORTCODE.'[^\]]*?id=[\'"]?'.$id.'[\'"]?[^\]]*?\]}i', '', $recode->text);

      return do_shortcode($template);
    }
  }
}
endif;

//テンプレートショートコードの取得
if ( !function_exists( 'get_function_text_shortcode' ) ):
function get_function_text_shortcode($id) {
  return "[".TEMPLATE_SHORTCODE." id={$id}]";
}
endif;

define('RANKING_SHORTCODE', 'rank');
//ランキングショートコード関数
add_shortcode(RANKING_SHORTCODE, 'item_ranking_shortcode');
if ( !function_exists( 'item_ranking_shortcode' ) ):
function item_ranking_shortcode($atts) {
  extract(shortcode_atts(array(
    'id' => 0,
  ), $atts));
  if ($id) {
    // //無限ループ回避
    // if ($recode->id == $id) return;

    ob_start();
    generate_item_ranking_tag($id);
    return ob_get_clean();
  }

}
endif;

//ショートコードの取得
if ( !function_exists( 'get_item_ranking_shortcode' ) ):
function get_item_ranking_shortcode($id) {
  return "[".RANKING_SHORTCODE." id={$id}]";
}
endif;

//ログインユーザーのみに表示するコンテンツ
add_shortcode('login_user_only', 'login_user_only_shortcode');
if ( !function_exists( 'login_user_only_shortcode' ) ):
function login_user_only_shortcode( $atts, $content = null ) {
  extract( shortcode_atts( array(
      'msg' => __( 'こちらのコンテンツはログインユーザーのみに表示されます。', THEME_NAME ),
  ), $atts ) );
  if (is_user_logged_in()) {
    return do_shortcode($content);
  } else {
    return '<div class="login-user-only">'.htmlspecialchars_decode($msg).'</div>';
  }
}
endif;

if ( !function_exists( 'get_http_content' ) ):
function get_http_content($url){
  try {
    $ch = curl_init();
    curl_setopt_array($ch, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
    ));
    $body = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if (CURLE_OK !== $errno) {
      throw new RuntimeException($error, $errno);
    }
    return $body;
  } catch (Exception $e) {
    return false;
    //echo $e->getMessage();
  }
}
endif;

//Amazon商品紹介リンクの外枠で囲む
if ( !function_exists( 'wrap_amazon_item_box' ) ):
function wrap_amazon_item_box($message){
  return '<div class="amazon-item-box no-icon amazon-item-error cf"><div>'.$message.'</div></div>';
}
endif;

// //Amazon商品リンクボタンを表示するか
// if ( !function_exists( 'is_amazon_box_buttons_visible' ) ):
// function is_amazon_box_buttons_visible(){
//   return trim()
// }
// endif;

//シンプルなアソシエイトURLの作成
if ( !function_exists( 'get_amazon_associate_url' ) ):
function get_amazon_associate_url($asin, $associate_tracking_id){
  $base_url = 'https://'.__( 'www.amazon.co.jp', THEME_NAME ).'/exec/obidos/ASIN';
  $associate_url = $base_url.'/'.$asin.'/';
  if (!empty($associate_tracking_id)) {
    $associate_url .= $associate_tracking_id.'/';
  }
  $associate_url = esc_url($associate_url);
  return $associate_url;
}
endif;

if ( !function_exists( 'get_asin_transient_id' ) ):
function get_asin_transient_id($asin){
  return TRANSIENT_AMAZON_API_PREFIX.$asin;
}
endif;

if ( !function_exists( 'get_asin_transient_bk_id' ) ):
function get_asin_transient_bk_id($asin){
  return TRANSIENT_BACKUP_AMAZON_API_PREFIX.$asin;
}
endif;

if ( !function_exists( 'get_amazon_itemlookup_xml' ) ):
function get_amazon_itemlookup_xml($asin){
  //アクセスキー
  $access_key_id = trim(get_amazon_api_access_key_id());
  //シークレットキー
  $secret_access_key = trim(get_amazon_api_secret_key());
  //アソシエイトタグ
  $associate_tracking_id = trim(get_amazon_associate_tracking_id());
  //キャッシュ更新間隔
  $days = intval(get_api_cache_retention_period());
  //_v($access_key_id);

  //キャッシュの存在
  $transient_id = get_asin_transient_id($asin);
  $transient_bk_id = get_asin_transient_bk_id($asin);
  $xml_cache = get_transient( $transient_id );
  //_v($xml_cache);
  if ($xml_cache) {
    //_v($xml_cache);
    return $xml_cache;
  }

  //APIエンドポイントURL
  $endpoint = 'https://ecs.amazonaws.jp/onca/xml';

  // パラメータ
  $params = array(
    //共通↓
    'Service' => 'AWSECommerceService',
    'AWSAccessKeyId' => $access_key_id,
    'AssociateTag' => $associate_tracking_id,
    //リクエストにより変更↓
    'Operation' => 'ItemLookup',
    'ItemId' => $asin,
    'ResponseGroup' => 'ItemAttributes,Images',
    //署名用タイムスタンプ
    'Timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
  );

  //パラメータと値のペアをバイト順？で並べかえ。
  ksort($params);


  // エンドポイントを指定します。
  $endpoint = __( 'webservices.amazon.co.jp', THEME_NAME );

  $uri = '/onca/xml';

  $pairs = array();

  // パラメータを key=value の形式に編集します。
  // 同時にURLエンコードを行います。
  foreach ($params as $key => $value) {
    array_push($pairs, rawurlencode($key)."=".rawurlencode($value));
  }

  // パラメータを&で連結します。
  $canonical_query_string = join("&", $pairs);

  // 署名に必要な文字列を先頭に追加します。
  $string_to_sign = "GET\n".$endpoint."\n".$uri."\n".$canonical_query_string;

  // RFC2104準拠のHMAC-SHA256ハッシュアルゴリズムの計算を行います。
  // これがSignatureの値になります。
  $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $secret_access_key, true));

  // Siginatureの値のURLエンコードを行い、リクエストの最後に追加します。
  $request_url = 'https://'.$endpoint.$uri.'?'.$canonical_query_string.'&Signature='.rawurlencode($signature);

  $res = get_http_content($request_url);
  //var_dump($res);

  //_v($res);
  if ($res) {
    //xml取得
    $xml = simplexml_load_string($res);
    if (property_exists($xml->Error, 'Code')) {
      //バックアップキャッシュの確認
      $xml_cache = get_transient( $transient_bk_id );
      if ($xml_cache) {
        return $xml_cache;
      }
      return $res;
    }
    //キャッシュ更新間隔（randで次回の同時読み込みを防ぐ）
    $expiration = 60 * 60 * 24 * $days + (rand(0, 60) * 60);
    //Amazon APIキャッシュの保存
    set_transient($transient_id, $res, $expiration);
    //Amazon APIバックアップキャッシュの保存
    set_transient($transient_bk_id, $res, $expiration * 2);

    return $res;
  }
  return false;
}
endif;

//Amazon商品リンク作成
add_shortcode('amazon', 'generate_amazon_product_link');
if ( !function_exists( 'generate_amazon_product_link' ) ):
function generate_amazon_product_link($atts){
  extract( shortcode_atts( array(
    'asin' => null,
    'id' => null,
    //'isbn ' => null,
    'kw' => null,
    'title' => null,
    'desc' => null,
    'size' => 'm',
    'amazon' => 1,
    'rakuten' => 1,
    'yahoo' => 1,
  ), $atts ) );

  $asin = esc_html(trim($asin));

  //ASINが取得できない場合はID
  if (empty($asin)) {
    $asin = $id;
  }

  //アクセスキー
  $access_key_id = trim(get_amazon_api_access_key_id());
  //シークレットキー
  $secret_access_key = trim(get_amazon_api_secret_key());
  //アソシエイトタグ
  $associate_tracking_id = trim(get_amazon_associate_tracking_id());
  //楽天アフィリエイトID
  $rakuten_affiliate_id = trim(get_rakuten_affiliate_id());
  //Yahoo!バリューコマースSID
  $sid = trim(get_yahoo_valuecommerce_sid());
  //Yahoo!バリューコマースPID
  $pid = trim(get_yahoo_valuecommerce_pid());
  // //キャッシュ更新間隔
  // $days = intval(get_api_cache_retention_period());
  //キーワード
  $kw = trim($kw);


  //アクセスキーもしくはシークレットキーがない場合
  if (empty($access_key_id) || empty($secret_access_key)) {
    $error_message = __( 'Amazon APIのアクセスキーもしくはシークレットキーが設定されていません。「Cocoon設定」の「API」タブから入力してください。', THEME_NAME );
    return wrap_amazon_item_box($error_message);
  }

  //ASINがない場合
  if (empty($asin)) {
    $error_message = __( 'Amazon商品リンクショートコード内にASINが入力されていません。', THEME_NAME );
    return wrap_amazon_item_box($error_message);
  }

  //アソシエイトurlの取得
  $associate_url = get_amazon_associate_url($asin, $associate_tracking_id);


  $res = get_amazon_itemlookup_xml($asin);
  if ($res) {
    // xml取得
    $xml = simplexml_load_string($res);
    //_v($xml);

    if (property_exists($xml->Error, 'Code')) {
      $error_message = '<a href="'.$associate_url.'" target="_blank">'.__( 'Amazonで詳細を見る', THEME_NAME ).'</a>';

      if (is_user_administrator()) {
        $admin_message = '<b>'.__( '管理者用エラーメッセージ', THEME_NAME ).'</b><br>';
        $admin_message .= __( 'アイテムを取得できませんでした。', THEME_NAME ).'<br>';
        $admin_message .= '<pre class="nohighlight"><b>'.$xml->Error->Code.'</b><br>'.preg_replace('/AWS Access Key ID: .+?\. /', '', $xml->Error->Message).'</pre>';
        $admin_message .= '<span class="red">'.__( 'このエラーメッセージは"サイト管理者のみ"に表示されています。少し時間おいてリロードしてください。それでも改善されない場合は、以下の不具合フォーラムにエラーメッセージとともにご連絡ください。', THEME_NAME ).'</span><br><a href="" target="_blank">'.__( '不具合報告フォーラム', THEME_NAME ).'</a>';
        $error_message .= '<br><br>'.get_message_box_tag($admin_message, 'warning-box fz-14px');
      }
      return wrap_amazon_item_box($error_message);
    }

    //var_dump($item);

    if (!property_exists($xml->Items, 'Item')) {
      $error_message = __( '商品を取得できませんでした。存在しないASINを指定している可能性があります。', THEME_NAME );
      return wrap_amazon_item_box($error_message);
    }

    if (property_exists($xml->Items, 'Item')) {
      $item = $xml->Items->Item;

      //var_dump($xml);

      //var_dump($xml->Items->Errors);
      // _v($item);
      $ASIN = esc_html($item->ASIN);
      $DetailPageURL = esc_url($item->DetailPageURL);
      if ($DetailPageURL) {
        $associate_url = $DetailPageURL;
      }

      $SmallImage = $item->SmallImage;
      $SmallImageUrl = esc_url($SmallImage->URL);
      $SmallImageWidth = esc_html($SmallImage->Width);
      $SmallImageHeight = esc_html($SmallImage->Height);
      $MediumImage = $item->MediumImage;
      $MediumImageUrl = esc_url($MediumImage->URL);
      $MediumImageWidth = esc_html($MediumImage->Width);
      $MediumImageHeight = esc_html($MediumImage->Height);
      $LargeImage = $item->LargeImage;
      $LargeImageUrl = esc_url($LargeImage->URL);
      $LargeImageWidth = esc_html($LargeImage->Width);
      $LargeImageHeight = esc_html($LargeImage->Height);

      //サイズ設定
      $size = strtolower($size);
      switch ($size) {
        case 's':
          $size_class = 'ais-s';
          $ImageUrl = $SmallImageUrl;
          $ImageWidth = $SmallImageWidth;
          $ImageHeight = $SmallImageHeight;
          break;
        case 'l':
          $size_class = 'ais-l';
          $ImageUrl = $LargeImageUrl;
          $ImageWidth = $LargeImageWidth;
          $ImageHeight = $LargeImageHeight;
          break;
        default:
          $size_class = 'ais-m';
          $ImageUrl = $MediumImageUrl;
          $ImageWidth = $MediumImageWidth;
          $ImageHeight = $MediumImageHeight;
          break;
      }


      $ItemAttributes = $item->ItemAttributes;

      if ($title) {
        $Title = $title;
      } else {
        $Title = $ItemAttributes->Title;
      }

      $TitleAttr = esc_attr($Title);
      $TitleHtml = esc_html($Title);

      $ProductGroup = esc_html($ItemAttributes->ProductGroup);
      $ProductGroupClass = strtolower($ProductGroup);
      $ProductGroupClass = str_replace(' ', '-', $ProductGroupClass);
      $Publisher = esc_html($ItemAttributes->Publisher);
      $Manufacturer = esc_html($ItemAttributes->Manufacturer);
      $Binding = esc_html($ItemAttributes->Binding);
      if ($Publisher) {
        $maker = $Publisher;
      } elseif ($Manufacturer) {
        $maker = $Manufacturer;
      } else {
        $maker = $Binding;
      }

      $ListPrice = $item->ListPrice;
      $FormattedPrice = esc_html($item->FormattedPrice);

      //$associate_url = esc_url($base_url.$ASIN.'/'.$associate_tracking_id.'/');

      $buttons_tag = null;
      if ($kw) {
        //Amazonボタンの取得
        $amazon_btn_tag = null;
        if (is_amazon_search_button_visible() && $amazon) {
          $amazon_url = 'https://'.__( 'www.amazon.co.jp', THEME_NAME ).'/gp/search?keywords='.urlencode($kw).'&tag='.$associate_tracking_id;
          $amazon_btn_tag =
            '<div class="shoplinkamazon">'.
              '<a href="'.$amazon_url.'" target="_blank" rel="nofollow">'.get_amazon_search_button_text().'</a>'.
            '</div>';
        }

        //楽天ボタンの取得
        $rakuten_btn_tag = null;
        if ($rakuten_affiliate_id && is_rakuten_search_button_visible() && $rakuten) {
          $rakuten_url = 'https://hb.afl.rakuten.co.jp/hgc/'.$rakuten_affiliate_id.'/?pc=https%3A%2F%2Fsearch.rakuten.co.jp%2Fsearch%2Fmall%2F'.urlencode($kw).'%2F-%2Ff.1-p.1-s.1-sf.0-st.A-v.2%3Fx%3D0%26scid%3Daf_ich_link_urltxt%26m%3Dhttp%3A%2F%2Fm.rakuten.co.jp%2F';
          $rakuten_btn_tag =
            '<div class="shoplinkrakuten">'.
              '<a href="'.$rakuten_url.'" target="_blank" rel="nofollow">'.get_rakuten_search_button_text().'</a>'.
            '</div>';
        }
        //Yahoo!ボタンの取得
        $yahoo_tag = null;
        if ($sid && $pid && is_yahoo_search_button_visible() && $yahoo) {
          $yahoo_url = 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid='.$sid.'&pid='.$pid.'&vc_url=http%3A%2F%2Fsearch.shopping.yahoo.co.jp%2Fsearch%3Fp%3D'.$kw;
          $yahoo_tag =
            '<div class="shoplinkyahoo">'.
              '<a href="'.$yahoo_url.'" target="_blank" rel="nofollow">'.get_yahoo_search_button_text().'</a>'.
            '</div>';
        }
        //ボタンコンテナ
        $buttons_tag =
          '<div class="amazon-item-buttons">'.
            $amazon_btn_tag.
            $rakuten_btn_tag.
            $yahoo_tag.
          '</div>';
      }

      $cache_del_tag = null;
      if (is_user_administrator()) {
        $cache_del_tag = '<a href="'.add_query_arg(array('page' => 'theme-cache', 'cache' => 'amazon_asin_cache', 'asin' => $asin), admin_url().'admin.php').'" class="asin-cache-del-link" target="_blank" rel="nofollow"'.ONCLICK_DELETE_CONFIRM.'>'.__( 'キャッシュ削除', THEME_NAME ).'</a>';
      }

      $desc_tag = null;
      if ($desc) {
        $desc_tag = '<div class="amazon-item-description">'.esc_html($desc).'</div>';
      }

      //_v($item);
      $tag =
        '<div class="amazon-item-box no-icon '.$size_class.' '.$ProductGroupClass.' '.$asin.' cf">'.
          '<figure class="amazon-item-thumb">'.
            '<a href="'.$associate_url.'" class="amazon-item-thumb-link" target="_blank" title="'.$TitleAttr.'" rel="nofollow">'.
              '<img src="'.$ImageUrl.'" alt="'.$TitleAttr.'" width="'.$ImageWidth.'" height="'.$ImageHeight.'" class="amazon-item-thumb-image">'.
            '</a>'.
          '</figure>'.
          '<div class="amazon-item-content">'.
            '<div class="amazon-item-title">'.
              '<a href="'.$associate_url.'" class="amazon-item-title-link" target="_blank" title="'.$TitleAttr.'" rel="nofollow">'.
                 $TitleHtml.
              '</a>'.
            '</div>'.
            '<div class="amazon-item-snippet">'.
              '<div class="amazon-item-maker">'.
                $maker.
              '</div>'.
              $desc_tag.
              $buttons_tag.
            '</div>'.
          '</div>'.
          $cache_del_tag.
        '</div>';
    } else {
      $error_message = __( '商品を取得できませんでした。存在しないASINを指定している可能性があります。', THEME_NAME );
      $tag = wrap_amazon_item_box($error_message);
    }

    return $tag;
  }

}
endif;

