<?php //Amazon商品リンク
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//Amazon APIから情報の取得
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
  $transient_id = get_amazon_api_transient_id($asin);
  $transient_bk_id = get_amazon_api_transient_bk_id($asin);
  $xml_cache = get_transient( $transient_id );
  //_v($xml_cache);
  if ($xml_cache && DEBUG_CACHE_ENABLE) {
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
    'ResponseGroup' => 'ItemAttributes,Images,Offers',
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
  // $xml_file = get_theme_logs_path().'amazon_last_xml_error.xml';
  // $res = wp_filesystem_get_contents($xml_file);

  //503エラーの場合はfalseを返す
  if (includes_string($res, 'Website Temporarily Unavailable')) {
    return false;
  }

  //_v($res);
  if ($res) {
    //xml取得
    $xml = simplexml_load_string($res);

    if ($xml) {
      //取得できなかった商品のログ出力
      if (!property_exists($xml->Items, 'Item')) {
        error_log_to_amazon_product($asin, AMAZON_ASIN_ERROR_MESSAGE);
      }
      if (property_exists($xml->Error, 'Code')) {
        //バックアップキャッシュの確認
        $xml_cache = get_transient( $transient_bk_id );
        if ($xml_cache && DEBUG_CACHE_ENABLE) {
          return $xml_cache;
        }
        return $res;
      }
    }

    if (DEBUG_CACHE_ENABLE) {
      //キャッシュ更新間隔（randで次回の同時読み込みを防ぐ）
      $expiration = DAY_IN_SECONDS * $days + (rand(0, 60) * 60);
      //Amazon APIキャッシュの保存
      set_transient($transient_id, $res, $expiration);
      //Amazon APIバックアップキャッシュの保存
      set_transient($transient_bk_id, $res, $expiration * 2);
    }

    return $res;
  }
  return false;
}
endif;

//Amazon商品リンク作成
if (!shortcode_exists('amazon')) {
  add_shortcode('amazon', 'amazon_product_link_shortcode');
}
if ( !function_exists( 'amazon_product_link_shortcode' ) ):
function amazon_product_link_shortcode($atts){
  extract( shortcode_atts( array(
    'asin' => null,
    'id' => null,
    //'search ' => null,
    'kw' => null,
    'title' => null,
    'desc' => null,
    'price' => null,
    'review' => null,
    'size' => 'm',
    'amazon' => 1,
    'rakuten' => 1,
    'yahoo' => 1,
    'border' => 1,
    'logo' => null,
    'image_only' => 0,
    'image_index' => null,
    'samples' => null,
    'catalog' => null,
    'btn1_url' => null,
    'btn1_text' => __( '詳細ページ', THEME_NAME ),
    'btn1_tag' => null,
    'btn2_url' => null,
    'btn2_text' => __( '詳細ページ', THEME_NAME ),
    'btn2_tag' => null,
    'btn3_url' => null,
    'btn3_text' => __( '詳細ページ', THEME_NAME ),
    'btn3_tag' => null,
  ), $atts ) );

  $asin = sanitize_shortcode_value($asin);

  //ASINが取得できない場合はID
  if (empty($asin)) {
    $asin = sanitize_shortcode_value($id);
  }
  //キーワード
  $keyword = sanitize_shortcode_value($kw);
  $description = $desc;
  if ($catalog !== null) {
    $samples = $catalog;
  }

  //アクセスキー
  $access_key_id = trim(get_amazon_api_access_key_id());
  //シークレットキー
  $secret_access_key = trim(get_amazon_api_secret_key());
  //トラッキングID
  $associate_tracking_id = trim(get_amazon_associate_tracking_id());
  //楽天アフィリエイトID
  $rakuten_affiliate_id = trim(get_rakuten_affiliate_id());
  //Yahoo!バリューコマースSID
  $sid = trim(get_yahoo_valuecommerce_sid());
  //Yahoo!バリューコマースPID
  $pid = trim(get_yahoo_valuecommerce_pid());

  //もしもID
  $moshimo_amazon_id  = trim(get_moshimo_amazon_id());
  $moshimo_rakuten_id = trim(get_moshimo_rakuten_id());
  $moshimo_yahoo_id   = trim(get_moshimo_yahoo_id());

  //アクセスキーもしくはシークレットキーがない場合
  if (empty($access_key_id) || empty($secret_access_key) || empty($associate_tracking_id)) {
    $error_message = __( 'Amazon APIのアクセスキーもしくはシークレットキーもしくはトラッキングIDが設定されていません。「Cocoon設定」の「API」タブから入力してください。', THEME_NAME );
    return wrap_product_item_box($error_message);
  }

  //ASINがない場合
  if (empty($asin)) {
    $error_message = __( 'Amazon商品リンクショートコード内にASINが入力されていません。', THEME_NAME );
    return wrap_product_item_box($error_message);
  }

  //アソシエイトurlの取得
  $associate_url = get_amazon_associate_url($asin, $associate_tracking_id);


  $res = get_amazon_itemlookup_xml($asin);
  if ($res === false) {//503エラーの場合
    return get_amazon_admin_error_message_tag($associate_url, __( '503エラー。このエラーは、PA-APIのアクセス制限を超えた場合や、メンテナンス中などにより、リクエストに応答できない場合に出力されるエラーコードです。このエラーが頻出する場合は「API」設定項目にある「キャッシュの保存期間」を長めに設定することをおすすめします。', THEME_NAME ));
  }

  //_v($res);
  if ($res) {
    // xml取得
    $xml = simplexml_load_string($res);

    if (property_exists($xml->Error, 'Code')) {
      $admin_message = __( 'アイテムを取得できませんでした。', THEME_NAME ).'<br>';
      $admin_message .= '<pre class="nohighlight"><b>'.$xml->Error->Code.'</b><br>'.preg_replace('/AWS Access Key ID: .+?\. /', '', $xml->Error->Message).'</pre>';
      $admin_message .= '<span class="red">'.__( 'このエラーメッセージは"サイト管理者のみ"に表示されています。', THEME_NAME ).'</span>';
      return get_amazon_admin_error_message_tag($associate_url, $admin_message);
    }

    //var_dump($item);
    ///////////////////////////////////////////
    // キャッシュ削除リンク
    ///////////////////////////////////////////
    $cache_delete_tag = get_cache_delete_tag('amazon', $asin);

    if (!property_exists($xml->Items, 'Item')) {
      return get_amazon_admin_error_message_tag($associate_url, AMAZON_ASIN_ERROR_MESSAGE, $cache_delete_tag);
    }

    if (property_exists($xml->Items, 'Item')) {
      $item = $xml->Items->Item;

      $ASIN = esc_html($item->ASIN);

      ///////////////////////////////////////
      // アマゾンURL
      ///////////////////////////////////////
      $moshimo_amazon_base_url = 'https://af.moshimo.com/af/c/click?a_id='.$moshimo_amazon_id.'&p_id=170&pc_id=185&pl_id=4062&url=';
      $DetailPageURL = esc_url($item->DetailPageURL);
      if ($DetailPageURL) {
        $associate_url = $DetailPageURL;
      }
      $moshimo_amazon_url = null;
      $moshimo_amazon_impression_tag = null;
      if ($moshimo_amazon_id && is_moshimo_affiliate_link_enable()) {
        $moshimo_amazon_url = $moshimo_amazon_base_url.urlencode(get_amazon_associate_url($asin));
        $associate_url = $moshimo_amazon_url;
        //インプレッションタグ
        $moshimo_amazon_impression_tag = get_moshimo_amazon_impression_tag();
      }

      //画像用のアイテムセット
      $ImageItem = $item;

      //イメージセットを取得する
      $ImageSets = $item->ImageSets;

      //画像インデックスが設定されている場合
      if ($image_index !== null && $ImageSets) {
        //インデックスを整数型にする
        $image_index = intval($image_index);

        //有効なインデックスの場合
        if (!empty($ImageSets->ImageSet[$image_index])) {
          //インデックスが有効な場合は画像アイテムを入れ替える
          $ImageItem = $ImageSets->ImageSet[$image_index];
        }
      }

      $SmallImage = $ImageItem->SmallImage;
      $SmallImageUrl = $SmallImage->URL;
      $SmallImageWidth = $SmallImage->Width;
      $SmallImageHeight = $SmallImage->Height;
      $MediumImage = $ImageItem->MediumImage;
      $MediumImageUrl = $MediumImage->URL;
      $MediumImageWidth = $MediumImage->Width;
      $MediumImageHeight = $MediumImage->Height;
      $LargeImage = $ImageItem->LargeImage;
      $LargeImageUrl = $LargeImage->URL;
      $LargeImageWidth = $LargeImage->Width;
      $LargeImageHeight = $LargeImage->Height;

      //サイズ設定
      $size = strtolower($size);
      switch ($size) {
        case 's':
          $size_class = 'pis-s';
          if ($SmallImageUrl) {
            $ImageUrl = $SmallImageUrl;
            $ImageWidth = $SmallImageWidth;
            $ImageHeight = $SmallImageHeight;
          } else {
            $ImageUrl = 'https://images-fe.ssl-images-amazon.com/images/G/09/nav2/dp/no-image-no-ciu._SL75_.gif';
            $ImageWidth = '75';
            $ImageHeight = '75';
          }
          break;
        case 'l':
          $size_class = 'pis-l';
          if ($LargeImageUrl) {
            $ImageUrl = $LargeImageUrl;
            $ImageWidth = $LargeImageWidth;
            $ImageHeight = $LargeImageHeight;
          } else {
            $ImageUrl = 'https://images-fe.ssl-images-amazon.com/images/G/09/nav2/dp/no-image-no-ciu._SL500_.gif';
            $ImageWidth = '500';
            $ImageHeight = '500';
          }
          break;
        default:
          $size_class = 'pis-m';
          if ($MediumImageUrl) {
            $ImageUrl = $MediumImageUrl;
            $ImageWidth = $MediumImageWidth;
            $ImageHeight = $MediumImageHeight;
          } else {
            $ImageUrl = 'https://images-fe.ssl-images-amazon.com/images/G/09/nav2/dp/no-image-no-ciu._SL160_.gif';
            $ImageWidth = '160';
            $ImageHeight = '160';
          }
          break;
      }

      $ItemAttributes = $item->ItemAttributes;

      ///////////////////////////////////////////
      // 商品リンク出力用の変数設定
      ///////////////////////////////////////////
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
      $Author = esc_html($ItemAttributes->Author);
      $Artist = esc_html($ItemAttributes->Artist);
      $Actor = esc_html($ItemAttributes->Actor);
      $Creator = esc_html($ItemAttributes->Creator);
      $Director = esc_html($ItemAttributes->Director);
      if ($Author) {
        $maker = $Author;
      } elseif ($Artist) {
        $maker = $Artist;
      } elseif ($Actor) {
        $maker = $Actor;
      } elseif ($Creator) {
        $maker = $Creator;
      } elseif ($Director) {
        $maker = $Director;
      } elseif ($Publisher) {
        $maker = $Publisher;
      } elseif ($Manufacturer) {
        $maker = $Manufacturer;
      } else {
        $maker = $Binding;
      }

      $ListPrice = $item->ItemAttributes->ListPrice;
      $Price = esc_html($ListPrice->Amount);
      $FormattedPrice = $ListPrice->FormattedPrice;

      ///////////////////////////////////////////
      // OfferSummary価格取得
      ///////////////////////////////////////////
      $OfferSummary = $item->OfferSummary;
      if ($OfferSummary) {
        $LowestNewPrice = $OfferSummary->LowestNewPrice->FormattedPrice;
        if ($LowestNewPrice) {
          $FormattedPrice = $LowestNewPrice;
        } else {
          $LowestUsedPrice = $OfferSummary->LowestUsedPrice->FormattedPrice;
          if ($LowestUsedPrice) {
            $FormattedPrice = $LowestUsedPrice;
          } else {
            $LowestCollectiblePrice = $OfferSummary->LowestCollectiblePrice->FormattedPrice;
            if ($LowestCollectiblePrice) {
              $FormattedPrice = $LowestCollectiblePrice;
            }
          }
        }
        //_v($OfferSummary);
      }

      ///////////////////////////////////////////
      // Amazon価格の取得
      ///////////////////////////////////////////
      if (is_amazon_item_stock_price_visible() && isset($item->Offers->Offer->OfferListing->Price->FormattedPrice)) {
        $FormattedPrice = $item->Offers->Offer->OfferListing->Price->FormattedPrice;
      }

      $FormattedPrice = esc_html($FormattedPrice);

      //$associate_url = esc_url($base_url.$ASIN.'/'.$associate_tracking_id.'/');

      ///////////////////////////////////////////
      // 値段表記
      ///////////////////////////////////////////
      $item_price_tag = null;
      //XMLのOperationRequesから時間情報を取得
      $unix_date = (string)$xml->OperationRequest->Arguments->Argument[6]->attributes()->Value;
      if ($unix_date) {
        $timestamp = strtotime(get_date_from_gmt($unix_date));
        $acquired_date = date_i18n( 'Y/m/d H:i', $timestamp );

        if ((is_amazon_item_price_visible() || $price === '1')
             && $FormattedPrice
             && $price !== '0'
           ) {
          $item_price_tag = get_item_price_tag($FormattedPrice, $acquired_date);
        }
      }
      ///////////////////////////////////////////
      // レビュー
      ///////////////////////////////////////////
      $review_tag = null;
      //_v($review);
      if ((is_amazon_item_customer_reviews_visible() || $review === '1')
          && isset($item->ItemLinks->ItemLink[2])
          && $review !== '0') {
        $review_url = $item->ItemLinks->ItemLink[2]->URL;
        //_v($review_url);
        $review_tag =
          '<div class="amazon-item-review product-item-review item-review">'.
            '<a class="amazon-item-review-link  product-item-review-link item-review-link" href="'.esc_url($review_url).'" target="_blank" rel="nofollow noopener">'.
              get_amazon_item_customer_reviews_text().
            '</a>'.
          '</div>';
          //_v($review_tag);
      }


      ///////////////////////////////////////////
      // 説明文タグ
      ///////////////////////////////////////////
      $description_tag = get_item_description_tag($description);

      ///////////////////////////////////////////
      // 検索ボタンの作成
      ///////////////////////////////////////////
      $args = array(
        'keyword' => $keyword,
        'associate_tracking_id' => $associate_tracking_id,
        'rakuten_affiliate_id' => $rakuten_affiliate_id,
        'sid' => $sid,
        'pid' => $pid,
        'moshimo_amazon_id' => $moshimo_amazon_id,
        'moshimo_rakuten_id' => $moshimo_rakuten_id,
        'moshimo_yahoo_id' => $moshimo_yahoo_id,
        'amazon' => $amazon,
        'rakuten' => $rakuten,
        'yahoo' => $yahoo,
        'amazon_page_url' => $associate_url,
        'rakuten_page_url' => null,
        'btn1_url' => $btn1_url,
        'btn1_text' => $btn1_text,
        'btn1_tag' => $btn1_tag,
        'btn2_url' => $btn2_url,
        'btn2_text' => $btn2_text,
        'btn2_tag' => $btn2_tag,
        'btn3_url' => $btn3_url,
        'btn3_text' => $btn3_text,
        'btn3_tag' => $btn3_tag,
      );
      $buttons_tag = get_search_buttons_tag($args);

      //枠線非表示
      $border_class = null;
      if (!$border) {
        $border_class = ' no-border';
      }

      //ロゴ非表示
      $logo_class = null;
      if ((!is_amazon_item_logo_visible() && $logo === null) || (!$logo && $logo !== null )) {
        $logo_class = ' no-after';
      }

      ///////////////////////////////////////////
      // 管理者情報タグ
      ///////////////////////////////////////////
      $product_item_admin_tag = get_product_item_admin_tag($cache_delete_tag);

      ///////////////////////////////////////////
      // イメージリンクタグ
      ///////////////////////////////////////////
      //テーマ設定もしくはcatalog=1で機能が有効な場合
      $is_catalog_image_visible =
        (is_amazon_item_catalog_image_visible() && $samples === null) ||
        ($samples !== null && $samples);

      $image_l_tag = null;
      if ($is_catalog_image_visible && ($size != 'l') && $LargeImageUrl) {
        $image_l_tag =
          '<div class="amazon-item-thumb-l product-item-thumb-l image-content">'.
            '<img src="'.esc_url($LargeImageUrl).'" alt="" width="'.esc_attr($LargeImageWidth).'" height="'.esc_attr($LargeImageHeight).'">'.
          '</div>';
      }
      $swatchimages_tag = null;

      if ($ImageSets && !$image_only && $is_catalog_image_visible) {
        $SwatchImages = $ImageSets->ImageSet;

        $tmp_tag = null;
        for ($i=0; $i < count($ImageSets->ImageSet)-1; $i++) {
          $display_none_class = null;
          if (($size != 'l') && ($i >= 3)) {
            $display_none_class .= ' sp-display-none';
          }
          if (($size == 's') && ($i >= 3) || ($size == 'm') && ($i >= 5)) {
            $display_none_class .= ' display-none';
          }

          $ImageSet = $ImageSets->ImageSet[$i];
          //SwatchImage
          $SwatchImage = $ImageSet->SwatchImage;
          $SwatchImageURL = $SwatchImage->URL;
          $SwatchImageWidth = $SwatchImage->Width;
          $SwatchImageHeight = $SwatchImage->Height;

          //LargeImage
          $LargeImage = $ImageSet->LargeImage;
          $LargeImageURL = $LargeImage->URL;
          $LargeImageWidth = $LargeImage->Width;
          $LargeImageHeight = $LargeImage->Height;

          $tmp_tag .=
            '<div class="image-thumb swatch-image-thumb si-thumb'.esc_attr($display_none_class).'">'.
              '<img src="'.esc_url($SwatchImageURL).'" alt="" widh="'.esc_attr($SwatchImageWidth).'" height="'.esc_attr($SwatchImageHeight).'">'.
              '<div class="image-content">'.
              '<img src="'.esc_url($LargeImageURL).'" alt="" widh="'.esc_attr($LargeImageWidth).'" height="'.esc_attr($LargeImageHeight).'">'.
              '</div>'.
            '</div>';
        }
        $swatchimages_tag = '<a href="'.esc_url($associate_url).'" class="swatchimages" target="_blank" rel="nofollow noopener">'.$tmp_tag.'</a>';
      }
      $image_only_class = null;
      if ($image_only) {
        $image_only_class = ' amazon-item-image-only product-item-image-only no-icon';
      }
      $image_link_tag = '<a href="'.esc_url($associate_url).'" class="amazon-item-thumb-link product-item-thumb-link image-thumb'.esc_attr($image_only_class).'" target="_blank" title="'.esc_attr($TitleAttr).'" rel="nofollow noopener">'.
              '<img src="'.esc_url($ImageUrl).'" alt="'.esc_attr($TitleAttr).'" width="'.esc_attr($ImageWidth).'" height="'.esc_attr($ImageHeight).'" class="amazon-item-thumb-image product-item-thumb-image">'.
              $moshimo_amazon_impression_tag.
              $image_l_tag.
            '</a>'.
            $swatchimages_tag;
      //画像のみ出力する場合
      if ($image_only) {
        return apply_filters('amazon_product_image_link_tag', $image_link_tag);
      }

      //画像ブロック
      $image_figure_tag =
        '<figure class="amazon-item-thumb product-item-thumb">'.
          $image_link_tag.
          //$image_l_tag.
        '</figure>';

      ///////////////////////////////////////////
      // 商品リンクタグの生成
      ///////////////////////////////////////////
      $tag =
        '<div class="amazon-item-box product-item-box no-icon '.$size_class.$border_class.$logo_class.' '.esc_attr($ProductGroupClass).' '.esc_attr($asin).' cf">'.
          $image_figure_tag.
          '<div class="amazon-item-content product-item-content cf">'.
            '<div class="amazon-item-title product-item-title">'.
              '<a href="'.esc_url($associate_url).'" class="amazon-item-title-link product-item-title-link" target="_blank" title="'.esc_attr($TitleAttr).'" rel="nofollow noopener">'.
                 $TitleHtml.
                 $moshimo_amazon_impression_tag.
              '</a>'.
            '</div>'.
            '<div class="amazon-item-snippet product-item-snippet">'.
              '<div class="amazon-item-maker product-item-maker">'.
                $maker.
              '</div>'.
              $item_price_tag.
              $description_tag.
              $review_tag.
            '</div>'.
            $buttons_tag.
          '</div>'.
          $product_item_admin_tag.
        '</div>';
    } else {//property_exists($xml->Items, 'Item')
      $tag = get_amazon_admin_error_message_tag($associate_url, AMAZON_ASIN_ERROR_MESSAGE, $cache_delete_tag);
    }//property_exists($xml->Items, 'Item')

    return apply_filters('amazon_product_link_tag', $tag);
  }

}
endif;

//PA-APIで商品情報を取得できなかった場合のエラーログ
if ( !function_exists( 'error_log_to_amazon_product' ) ):
function error_log_to_amazon_product($asin, $message = ''){
  //エラーログに出力
  $date = date_i18n("Y-m-d H:i:s");
  $msg = $date.','.
         $asin.','.
         get_the_permalink().
         PHP_EOL;
  error_log($msg, 3, get_theme_amazon_product_error_log_file());

  //メールで送信
  if (is_api_error_mail_enable()) {
    $subject = __( 'Amazon商品取得エラー', THEME_NAME );
    $mail_msg =
      __( 'Amazon商品リンクが取得できませんでした。', THEME_NAME ).PHP_EOL.
      PHP_EOL.
      'ASIN:'.$asin.PHP_EOL.
      'URL:'.get_the_permalink().PHP_EOL.
      'Message:'.$message.PHP_EOL.
      THEME_MAIL_AMAZON_PR.THEME_MAIL_CREDIT;
    wp_mail( get_wordpress_admin_email(), $subject, $mail_msg );
  }
}
endif;

//Amazonエラーの際に出力するリンクを
if ( !function_exists( 'get_amazon_error_product_link' ) ):
function get_amazon_error_product_link($url){
  return '<a href="'.esc_url($url).'" target="_blank" rel="nofollow noopener">'.__( 'Amazonで詳細を見る', THEME_NAME ).'</a>';
}
endif;

//AmazonのASINエラータグ取得
if ( !function_exists( 'get_amazon_admin_error_message_tag' ) ):
function get_amazon_admin_error_message_tag($url, $message, $cache_delete_tag = null){
  $error_message = get_amazon_error_product_link($url);
  if (is_user_administrator()) {
    $error_message .= '<br><br>'.get_admin_errormessage_box_tag($message);
  }
  return wrap_product_item_box($error_message, 'amazon', $cache_delete_tag);
}
endif;
