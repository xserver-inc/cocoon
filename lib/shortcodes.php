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
  ), $atts));
  $categories = array();
  //var_dump($cats);
  if ($cats && $cats != 'all') {
    $categories = explode(',', $cats);
  }
  ob_start();
  generate_new_entries_tag($count, $type, $categories, $children, $post_type);
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
    curl_setopt_array($ch, [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
    ]);
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

//Amazon商品リンク作成
add_shortcode('amazon', 'generate_amazon_product_link');
if ( !function_exists( 'generate_amazon_product_link' ) ):
function generate_amazon_product_link($atts){
  extract( shortcode_atts( array(
    'asin' => null,
    //'isbn ' => null,
    'kw' => null,
  ), $atts ) );

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
  //キャッシュ更新間隔
  $period = intval(get_api_cache_retention_period());

  //アクセスキーもしくはシークレットキーがない場合
  if (empty($access_key_id) || empty($secret_access_key)) {
    $error_message = __( 'Amazon APIのアクセスキーもしくはシークレットキーが設定されていません。「Cocoon設定」の「API」タブから入力してください。', THEME_NAME );
    return wrap_amazon_item_box($error_message);
  }

  // //ASIN
  // $asin = 'B013PUTPHK';
  // $asin = 'B0186FESEE';
  //ASINがない場合
  if (empty($asin)) {
    $error_message = __( 'Amazon商品リンクショートコード内にASINが入力されていません。', THEME_NAME );
    return wrap_amazon_item_box($error_message);
  }

  //キャッシュの存在
  $transient_id = TRANSIENT_AMAZON_API_PREFIX.$asin;
  $tag_cache = get_transient( $transient_id );
  if ($tag_cache) {
    //_v($tag_cache);
    return $tag_cache;
  }
  // $transient_bk_id = TRANSIENT_BACKUP_AMAZON_API_PREFIX.$asin;
  // $tag_cache = get_transient( $transient_bk_id );
  // if ($tag_cache) {
  //   return $tag_cache;
  // }

  ///////////////////////////////////////
  // アソシエイトAPI設定
  ///////////////////////////////////////
  //アソシエートURLの作成
  $base_url = 'https://'.__( 'www.amazon.co.jp', THEME_NAME ).'/exec/obidos/ASIN';
  $associate_url = $base_url.'/'.$asin.'/';
  if (!empty($associate_tracking_id)) {
    $associate_url .= $associate_tracking_id.'/';
  }
  $associate_url = esc_url($associate_url);

  // //APIエンドポイントURL
  // $endpoint = 'https://ecs.amazonaws.jp/onca/xml';

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

  if ($res) {
    // xml取得
    $xml = simplexml_load_string($res);
    //var_dump($xml->Error);
    if (isset($xml->Error)) {
      //バックアップキャッシュの確認
      $transient_bk_id = TRANSIENT_BACKUP_AMAZON_API_PREFIX.$asin;
      $tag_cache = get_transient( $transient_bk_id );
      if ($tag_cache) {
        return $tag_cache;
      }
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

    // if (!property_exists($xml->Items, 'Item')) {
    //   $error_message = __( '商品を取得できませんでした。存在しないASINを指定している可能性があります。', THEME_NAME );
    //   return wrap_amazon_item_box($error_message);
    // }

    if (property_exists($xml->Items, 'Item')) {
      $item = $xml->Items->Item;

      //var_dump($xml);

      //var_dump($xml->Items->Errors);
      // _v($item);
      $ASIN = esc_html($item->ASIN);
      $DetailPageURL = esc_url($item->DetailPageURL);

      $SmallImage = $item->SmallImage;
      $MediumImage = $item->MediumImage;
      $MediumImageUrl = esc_url($MediumImage->URL);
      $MediumImageWidth = esc_html($MediumImage->Width);
      $MediumImageHeight = esc_html($MediumImage->Height);
      $LargeImage = $item->LargeImage;

      $ItemAttributes = $item->ItemAttributes;

      $Title = $ItemAttributes->Title;
      $TitleAttr = esc_attr($Title);
      $TitleHtml = esc_html($Title);

      $ProductGroup = esc_html($ItemAttributes->ProductGroup);
      $ProductGroupClass = strtolower($ProductGroup);
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
      if (trim($kw)) {
        //楽天ボタンの取得
        $rakuten_btn_tag = null;
        if ($rakuten_affiliate_id) {
          $rakuten_btn_tag =
            '<div class="shoplinkrakuten">'.
              '<a href="">'.__( '楽天市場', THEME_NAME ).'</a>'.
            '</div>';
        }
        //Yahoo!ボタンの取得
        $yahoo_tag = null;
        if ($sid && $pid) {
          $yahoo_tag =
            '<div class="shoplinkyahoo">'.
              '<a href="">'.__( 'Yahoo!ショッピング', THEME_NAME ).'</a>'.
            '</div>';
        }
        //ボタンコンテナ
        $buttons_tag =
          '<div class="amazon-item-buttons">'.
            '<div class="shoplinkamazon">'.
              '<a href="">'.__( 'Amazon', THEME_NAME ).'</a>'.
            '</div>'.
            $rakuten_btn_tag.
            $yahoo_tag.
          '</div>';
      }

      //_v($item);
      $tag =
        '<div class="amazon-item-box no-icon '.$ProductGroupClass.' cf">'.
          '<figure class="amazon-item-thumb">'.
            '<a href="'.$associate_url.'" class="amazon-item-thumb-link" target="_blank" title="'.$TitleAttr.'">'.
              '<img src="'.$MediumImageUrl.'" alt="'.$TitleAttr.'" width="'.$MediumImageWidth.'" height="'.$MediumImageHeight.'" class="amazon-item-thumb-image">'.
            '</a>'.
          '</figure>'.
          '<div class="amazon-item-content">'.
            '<div class="amazon-item-title">'.
              '<a href="'.$associate_url.'" class="amazon-item-title-link" target="_blank" title="'.$TitleAttr.'">'.
                 $TitleHtml.
              '</a>'.
            '</div>'.
            '<div class="amazon-item-snippet">'.
              '<div class="amazon-item-maker">'.
                $maker.
              '</div>'.
              $buttons_tag.
            '</div>'.
          '</div>'.
        '</div>';
    } else {
      $error_message = __( '商品を取得できませんでした。存在しないASINを指定している可能性があります。', THEME_NAME );
      $tag = wrap_amazon_item_box($error_message);
    }

    //キャッシュ更新間隔（randで次回の同時読み込みを防ぐ）
    $expiration = 60 * 60 * 24 * $period + (rand(0, 60) * 60);
    //Amazon APIキャッシュの保存
    set_transient($transient_id, $tag, $expiration);
    //Amazon APIバックアップキャッシュの保存
    set_transient($transient_bk_id, $tag, $expiration * 2);

    return $tag;
  }

}
endif;

