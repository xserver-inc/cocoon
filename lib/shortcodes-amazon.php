<?php //Amazon商品リンク
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;


if ( !class_exists( 'CocoonAwsV4' ) ):
class CocoonAwsV4 {

  private $accessKey = null;
  private $secretKey = null;
  private $path = null;
  private $regionName = null;
  private $serviceName = null;
  private $httpMethodName = null;
  private $queryParametes = array ();
  private $awsHeaders = array ();
  private $payload = "";

  private $HMACAlgorithm = "AWS4-HMAC-SHA256";
  private $aws4Request = "aws4_request";
  private $strSignedHeader = null;
  private $xAmzDate = null;
  private $currentDate = null;

  public function __construct($accessKey, $secretKey) {
      $this->accessKey = $accessKey;
      $this->secretKey = $secretKey;
      $this->xAmzDate = $this->getTimeStamp ();
      $this->currentDate = $this->getDate ();
  }

  function setPath($path) {
      $this->path = $path;
  }

  function setServiceName($serviceName) {
      $this->serviceName = $serviceName;
  }

  function setRegionName($regionName) {
      $this->regionName = $regionName;
  }

  function setPayload($payload) {
      $this->payload = $payload;
  }

  function setRequestMethod($method) {
      $this->httpMethodName = $method;
  }

  function addHeader($headerName, $headerValue) {
      $this->awsHeaders [$headerName] = $headerValue;
  }

  private function prepareCanonicalRequest() {
      $canonicalURL = "";
      $canonicalURL .= $this->httpMethodName . "\n";
      $canonicalURL .= $this->path . "\n" . "\n";
      $signedHeaders = '';
      foreach ( $this->awsHeaders as $key => $value ) {
          $signedHeaders .= $key . ";";
          $canonicalURL .= $key . ":" . $value . "\n";
      }
      $canonicalURL .= "\n";
      $this->strSignedHeader = substr ( $signedHeaders, 0, - 1 );
      $canonicalURL .= $this->strSignedHeader . "\n";
      $canonicalURL .= $this->generateHex ( $this->payload );
      return $canonicalURL;
  }

  private function prepareStringToSign($canonicalURL) {
      $stringToSign = '';
      $stringToSign .= $this->HMACAlgorithm . "\n";
      $stringToSign .= $this->xAmzDate . "\n";
      $stringToSign .= $this->currentDate . "/" . $this->regionName . "/" . $this->serviceName . "/" . $this->aws4Request . "\n";
      $stringToSign .= $this->generateHex ( $canonicalURL );
      return $stringToSign;
  }

  private function calculateSignature($stringToSign) {
      $signatureKey = $this->getSignatureKey ( $this->secretKey, $this->currentDate, $this->regionName, $this->serviceName );
      $signature = hash_hmac ( "sha256", $stringToSign, $signatureKey, true );
      $strHexSignature = strtolower ( bin2hex ( $signature ) );
      return $strHexSignature;
  }

  public function getHeaders() {
      $this->awsHeaders ['x-amz-date'] = $this->xAmzDate;
      ksort ( $this->awsHeaders );

      // Step 1: CREATE A CANONICAL REQUEST
      $canonicalURL = $this->prepareCanonicalRequest ();

      // Step 2: CREATE THE STRING TO SIGN
      $stringToSign = $this->prepareStringToSign ( $canonicalURL );

      // Step 3: CALCULATE THE SIGNATURE
      $signature = $this->calculateSignature ( $stringToSign );

      // Step 4: CALCULATE AUTHORIZATION HEADER
      if ($signature) {
          $this->awsHeaders ['Authorization'] = $this->buildAuthorizationString ( $signature );
          return $this->awsHeaders;
      }
  }

  private function buildAuthorizationString($strSignature) {
      return $this->HMACAlgorithm . " " . "Credential=" . $this->accessKey . "/" . $this->getDate () . "/" . $this->regionName . "/" . $this->serviceName . "/" . $this->aws4Request . "," . "SignedHeaders=" . $this->strSignedHeader . "," . "Signature=" . $strSignature;
  }

  private function generateHex($data) {
      return strtolower ( bin2hex ( hash ( "sha256", $data, true ) ) );
  }

  private function getSignatureKey($key, $date, $regionName, $serviceName) {
      $kSecret = "AWS4" . $key;
      $kDate = hash_hmac ( "sha256", $date, $kSecret, true );
      $kRegion = hash_hmac ( "sha256", $regionName, $kDate, true );
      $kService = hash_hmac ( "sha256", $serviceName, $kRegion, true );
      $kSigning = hash_hmac ( "sha256", $this->aws4Request, $kService, true );

      return $kSigning;
  }

  private function getTimeStamp() {
      return gmdate ( "Ymd\THis\Z" );
  }

  private function getDate() {
      return gmdate ( "Ymd" );
  }
}
endif;





//JSONがエラーを出力しているか
if ( !function_exists( 'is_paapi_json_error' ) ):
function is_paapi_json_error($json){
  return property_exists($json, 'Errors');
}
endif;

//PA-APIの返り値のJSONにアイテムが存在するか
if ( !function_exists( 'is_paapi_json_item_exist' ) ):
function is_paapi_json_item_exist($json){
  return property_exists($json->{'ItemsResult'}, 'Items');
}
endif;


//Amazon APIから情報の取得
if ( !function_exists( 'get_amazon_itemlookup_json' ) ):
function get_amazon_itemlookup_json($asin){
  $asin = trim($asin);

  //キャッシュの存在
  $transient_id = get_amazon_api_transient_id($asin);
  $transient_bk_id = get_amazon_api_transient_bk_id($asin);
  $json_cache = get_transient( $transient_id );
  // $json = json_decode( $json_cache );
  // $json_error_code    = isset($json->{'Errors'}[0]->{'Code'}) ? $json->{'Errors'}[0]->{'Code'} : null;
  //_v($json_cache);
  if ($json_cache /* && ($json_error_code != 'TooManyRequests')*/ && DEBUG_CACHE_ENABLE) {
    //_v($json_cache);
    return $json_cache;
  }

  $serviceName = 'ProductAdvertisingAPI';
  $region = __( 'us-west-2', THEME_NAME );
  $region = apply_filters('amazon_webservices_region', $region);

  //アクセスキー
  $accessKey = trim(get_amazon_api_access_key_id());
  //シークレットキー
  $secretKey = trim(get_amazon_api_secret_key());
  //アソシエイトタグ
  $partnerTag = trim(get_amazon_associate_tracking_id());
  //キャッシュ更新間隔（日）
  $days = intval(get_api_cache_retention_period());
  //_v($access_key_id);

  $payload = '{'
    .' "ItemIds": ['
    .'  "'.$asin.'"'
    .' ],'
    .' "Resources": ['
    .'  "BrowseNodeInfo.BrowseNodes",'
    .'  "BrowseNodeInfo.BrowseNodes.Ancestor",'
    .'  "BrowseNodeInfo.BrowseNodes.SalesRank",'
    .'  "BrowseNodeInfo.WebsiteSalesRank",'
    .'  "CustomerReviews.Count",'
    .'  "CustomerReviews.StarRating",'
    .'  "Images.Primary.Small",'
    .'  "Images.Primary.Medium",'
    .'  "Images.Primary.Large",'
    .'  "Images.Variants.Small",'
    .'  "Images.Variants.Medium",'
    .'  "Images.Variants.Large",'
    .'  "ItemInfo.ByLineInfo",'
    .'  "ItemInfo.ContentInfo",'
    .'  "ItemInfo.ContentRating",'
    .'  "ItemInfo.Classifications",'
    .'  "ItemInfo.ExternalIds",'
    .'  "ItemInfo.Features",'
    .'  "ItemInfo.ManufactureInfo",'
    .'  "ItemInfo.ProductInfo",'
    .'  "ItemInfo.TechnicalInfo",'
    .'  "ItemInfo.Title",'
    .'  "ItemInfo.TradeInInfo",'
    .'  "Offers.Listings.Availability.MaxOrderQuantity",'
    .'  "Offers.Listings.Availability.Message",'
    .'  "Offers.Listings.Availability.MinOrderQuantity",'
    .'  "Offers.Listings.Availability.Type",'
    .'  "Offers.Listings.Condition",'
    .'  "Offers.Listings.Condition.SubCondition",'
    .'  "Offers.Listings.DeliveryInfo.IsAmazonFulfilled",'
    .'  "Offers.Listings.DeliveryInfo.IsFreeShippingEligible",'
    .'  "Offers.Listings.DeliveryInfo.IsPrimeEligible",'
    .'  "Offers.Listings.DeliveryInfo.ShippingCharges",'
    .'  "Offers.Listings.IsBuyBoxWinner",'
    .'  "Offers.Listings.LoyaltyPoints.Points",'
    .'  "Offers.Listings.MerchantInfo",'
    .'  "Offers.Listings.Price",'
    .'  "Offers.Listings.ProgramEligibility.IsPrimeExclusive",'
    .'  "Offers.Listings.ProgramEligibility.IsPrimePantry",'
    .'  "Offers.Listings.Promotions",'
    .'  "Offers.Listings.SavingBasis",'
    .'  "Offers.Summaries.HighestPrice",'
    .'  "Offers.Summaries.LowestPrice",'
    .'  "Offers.Summaries.OfferCount",'
    .'  "ParentASIN",'
    .'  "RentalOffers.Listings.Availability.MaxOrderQuantity",'
    .'  "RentalOffers.Listings.Availability.Message",'
    .'  "RentalOffers.Listings.Availability.MinOrderQuantity",'
    .'  "RentalOffers.Listings.Availability.Type",'
    .'  "RentalOffers.Listings.BasePrice",'
    .'  "RentalOffers.Listings.Condition",'
    .'  "RentalOffers.Listings.Condition.SubCondition",'
    .'  "RentalOffers.Listings.DeliveryInfo.IsAmazonFulfilled",'
    .'  "RentalOffers.Listings.DeliveryInfo.IsFreeShippingEligible",'
    .'  "RentalOffers.Listings.DeliveryInfo.IsPrimeEligible",'
    .'  "RentalOffers.Listings.DeliveryInfo.ShippingCharges",'
    .'  "RentalOffers.Listings.MerchantInfo"'
    .' ],'
    .' "PartnerTag": "'.$partnerTag.'",'
    .' "PartnerType": "Associates",'
    .' "Marketplace": "'.AMAZON_DOMAIN.'"'
    .'}';
  $host = __( 'webservices.amazon.co.jp', THEME_NAME );
  $host = apply_filters('amazon_webservices_host', $host);
  $uriPath = '/paapi5/getitems';
  $awsv4 = new CocoonAwsV4 ($accessKey, $secretKey);
  $awsv4->setRegionName($region);
  $awsv4->setServiceName($serviceName);
  $awsv4->setPath ($uriPath);
  $awsv4->setPayload ($payload);
  $awsv4->setRequestMethod ("POST");
  $awsv4->addHeader ('content-encoding', 'amz-1.0');
  $awsv4->addHeader ('content-type', 'application/json; charset=utf-8');
  $awsv4->addHeader ('host', $host);
  $awsv4->addHeader ('x-amz-target', 'com.amazon.paapi5.v1.ProductAdvertisingAPIv1.GetItems');
  $headers = $awsv4->getHeaders ();
  $headerString = "";
  foreach ( $headers as $key => $value ) {
    $headerString .= $key . ': ' . $value . "\r\n";
  }
  $params = array (
    'http' => array (
      'header' => $headerString,
      'method' => 'POST',
      'content' => $payload,
      'ignore_errors' => true,
    )
  );
  $stream = stream_context_create( $params );

  $fp = @fopen ( 'https://'.$host.$uriPath, 'rb', false, $stream );

  if (!$fp) {
    //throw new Exception ( "Exception Occured" );
    return false;
  }
  $res = @stream_get_contents( $fp );
  if ($res === false) {
    //throw new Exception ( "Exception Occured" );
    return false;
  }

  //503エラーの場合はfalseを返す
  if (includes_string($res, 'Website Temporarily Unavailable')) {
    return false;
  }

  //_v($res);
  if ($res) {
    //JSON取得
    $json = json_decode( $res );
    //_v($json);
    //_v(is_paapi_json_item_exist($json));
    //_v(is_paapi_json_error($json));

    if ($json) {
      //エラーだった場合
      if (is_paapi_json_error($json)) {
        //バックアップキャッシュの確認
        $json_cache = get_transient( $transient_bk_id );
        if ($json_cache && DEBUG_CACHE_ENABLE) {
          return $json_cache;
        }
        return $res;
      }

      //取得できなかった商品のログ出力
      if (!is_paapi_json_item_exist($json)) {
        error_log_to_amazon_product($asin, AMAZON_ASIN_ERROR_MESSAGE);
      }
    }

    if (DEBUG_CACHE_ENABLE) {
      //一応、XML取得時のタイムスタンプを保存しておく
      $count = 1;
      $res = str_replace(',"BrowseNodeInfo":{', ',"date":"'.date_i18n( 'Y/m/d H:i').'","BrowseNodeInfo":{', $res, $count);
      //_v($res);
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
    'text_only' => 0,
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
  ), $atts, 'amazon' ) );

  $asin = sanitize_shortcode_value($asin);

  //ASINが取得できない場合はID
  if (empty($asin)) {
    $asin = sanitize_shortcode_value($id);
  }
  //キーワード
  $keyword = sanitize_shortcode_value($kw);

  //説明文
  $description = $desc;

  //カタログ
  if (!is_null($catalog)) {
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

  //商品情報の取得
  $res = get_amazon_itemlookup_json($asin);

  if ($res === false) {//503エラーの場合
    return get_amazon_admin_error_message_tag($associate_url, __( '503エラー。このエラーは、PA-APIのアクセス制限を超えた場合や、メンテナンス中などにより、リクエストに応答できない場合に出力されるエラーコードです。サーバーの「php.ini設定」の「allow_url_fopen」項目が「ON」になっているかを確認してください。このエラーが頻出する場合は「API」設定項目にある「キャッシュの保存期間」を長めに設定することをおすすめします。', THEME_NAME ));
  }

  //_v($res);
  if ($res) {
    // xml取得
    $json = json_decode( $res );

    if (is_paapi_json_error($json)) {

      $json_error_code    = $json->{'Errors'}[0]->{'Code'};
      $json_error_message = $json->{'Errors'}[0]->{'Message'};

      $admin_message = __( 'アイテムを取得できませんでした。', THEME_NAME ).'<br>';
      $admin_message .= '<pre class="nohighlight"><b>'.$json_error_code.'</b><br>'.preg_replace('/AWS Access Key ID: .+?\. /', '', $json_error_message).'</pre>';
      $admin_message .= '<span class="red">'.__( 'このエラーメッセージは"サイト管理者のみ"に表示されています。', THEME_NAME ).'</span>';

      //キャッシュ名の取得
      $transient_id = get_amazon_api_transient_id($asin);
      $json_cache = get_transient( $transient_id );
      //キャッシュがないときのみログ・メールする
      if (!$json_cache) {
        //メールの送信
        $msg = 'アイテムを取得できませんでした。'.PHP_EOL.
          $json_error_code.PHP_EOL.
          $json_error_message.PHP_EOL;
        error_log_to_amazon_product($asin, $msg);

        //リクエスト過多エラーの場合はキャッシュを保存しない
        if ($json_error_code != 'TooManyRequests') {
          //エラーの場合は一日だけキャッシュ
          $expiration = DAY_IN_SECONDS;
          //Amazon APIキャッシュの保存
          set_transient($transient_id, $res, $expiration);
        }
      }

      return get_amazon_admin_error_message_tag($associate_url, $admin_message);
    }

    //var_dump($item);
    ///////////////////////////////////////////
    // キャッシュ削除リンク
    ///////////////////////////////////////////
    $cache_delete_tag = get_cache_delete_tag('amazon', $asin);

    if (!is_paapi_json_item_exist($json)) {
      return get_amazon_admin_error_message_tag($associate_url, AMAZON_ASIN_ERROR_MESSAGE, $cache_delete_tag, $asin);
    }

    if (is_paapi_json_item_exist($json)) {
      $item = $json->{'ItemsResult'}->{'Items'}[0];
      //_v($item);

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


      //イメージセットを取得する
      $Images = $item->{'Images'};
      $ImageItem = $Images->{'Primary'};
      //メイン画像以外の画像
      $Variants = isset($Images->{'Variants'}) ? $Images->{'Variants'} : array();

      //画像インデックスが設定されている場合
      if (!is_null($image_index) && $Variants) {
        //インデックスを整数型にする
        $image_index = intval($image_index);

        //有効なインデックスの場合
        if (!empty($Variants[$image_index])) {
          //インデックスが有効な場合は画像アイテムを入れ替える
          $ImageItem = $Variants[$image_index];
        }
      }
      //_v($ImageItem);

      //$Primary = $ImageItem->{'Primary'};
      $SmallImage = $ImageItem->{'Small'};
      $SmallImageUrl = $SmallImage->URL;
      $SmallImageWidth = $SmallImage->Width;
      $SmallImageHeight = $SmallImage->Height;
      $MediumImage = $ImageItem->{'Medium'};
      $MediumImageUrl = $MediumImage->URL;
      $MediumImageWidth = $MediumImage->Width;
      $MediumImageHeight = $MediumImage->Height;
      $LargeImage = $ImageItem->{'Large'};
      $LargeImageUrl = $LargeImage->URL;
      $LargeImageWidth = $LargeImage->Width;
      $LargeImageHeight = $LargeImage->Height;
      // _v($SmallImage);
      // _v($MediumImage);
      // _v($LargeImage);

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

      $ItemInfo = isset($item->{'ItemInfo'}) ? $item->{'ItemInfo'} : null;
      //_v( $ItemInfo);

      if ($ItemInfo) {
        //説明文
        if (is_null($description)) {
          if (is_amazon_item_description_visible()) {
            $Features = isset($ItemInfo->{'Features'}) ? $ItemInfo->{'Features'} : null;
            $description = isset($Features->{'DisplayValues'}[0]) ? $Features->{'DisplayValues'}[0] : null;
          }
        }
      }


      ///////////////////////////////////////////
      // 商品リンク出力用の変数設定
      ///////////////////////////////////////////
      if ($title) {
        $Title = $title;
      } else {
        $Title = $ItemInfo->{'Title'}->{'DisplayValue'};
      }
      //_v($Title);
      $TitleAttr = esc_attr($Title);
      $TitleHtml = esc_html($Title);

      //商品グレープ
      $Classifications = $ItemInfo->{'Classifications'};
      $ProductGroup = esc_html($Classifications->{'ProductGroup'}->{'DisplayValue'});
      $ProductGroupClass = strtolower($ProductGroup);
      $ProductGroupClass = str_replace(' ', '-', $ProductGroupClass);
      //_v($ProductGroup);

      $ByLineInfo = $ItemInfo->{'ByLineInfo'};
      $Publisher = esc_html(isset($ByLineInfo->{'Publisher'}->{'DisplayValue'}) ? $ByLineInfo->{'Publisher'}->{'DisplayValue'} : null);
      $Manufacturer = esc_html(isset($ByLineInfo->{'Manufacturer'}->{'DisplayValue'}) ? $ByLineInfo->{'Manufacturer'}->{'DisplayValue'} : null);
      $Brand = esc_html(isset($ByLineInfo->{'Brand'}->{'DisplayValue'}) ? $ByLineInfo->{'Brand'}->{'DisplayValue'} : null);
      $Binding = esc_html(isset($ByLineInfo->{'Binding'}->{'DisplayValue'}) ? $ByLineInfo->{'Binding'}->{'DisplayValue'} : null);
      $Author = esc_html(isset($ByLineInfo->{'Author'}->{'DisplayValue'}) ? $ByLineInfo->{'Author'}->{'DisplayValue'} : null);
      $Artist = esc_html(isset($ByLineInfo->{'Artist'}->{'DisplayValue'}) ? $ByLineInfo->{'Artist'}->{'DisplayValue'} : null);
      $Actor = esc_html(isset($ByLineInfo->{'Actor'}->{'DisplayValue'}) ? $ByLineInfo->{'Actor'}->{'DisplayValue'} : null);
      $Creator = esc_html(isset($ByLineInfo->{'Creator'}->{'DisplayValue'}) ? $ByLineInfo->{'Creator'}->{'DisplayValue'} : null);
      $Director = esc_html(isset($ByLineInfo->{'Director'}->{'DisplayValue'}) ? $ByLineInfo->{'Director'}->{'DisplayValue'} : null);
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
      } elseif ($Brand) {
        $maker = $Brand;
      } elseif ($Manufacturer) {
        $maker = $Manufacturer;
      } else {
        $maker = $Binding;
      }
      //_v($maker);

      $Offers = $item->{'Offers'};
      $HighestPrice = isset($Offers->{'Summaries'}[0]->{'HighestPrice'}->{'DisplayAmount'}) ? $Offers->{'Summaries'}[0]->{'HighestPrice'}->{'DisplayAmount'} : null;
      $LowestPrice = isset($Offers->{'Summaries'}[0]->{'LowestPrice'}->{'DisplayAmount'}) ? $Offers->{'Summaries'}[0]->{'LowestPrice'}->{'DisplayAmount'} : null;

      $SavingBasisPrice = isset($Offers->{'Listings'}[0]->{'SavingBasis'}->{'DisplayAmount'}) ? $Offers->{'Listings'}[0]->{'SavingBasis'}->{'DisplayAmount'} : null;
      $Price = isset($Offers->{'Listings'}[0]->{'Price'}->{'DisplayAmount'}) ? $Offers->{'Listings'}[0]->{'Price'}->{'DisplayAmount'} : null;

      //$ListPrice = $item->ItemAttributes->ListPrice;
      //_v($FormattedPrice);

      ///////////////////////////////////////////
      // デフォルト価格取得
      ///////////////////////////////////////////
      if ($SavingBasisPrice) {
        $FormattedPrice = $SavingBasisPrice;
      } else {
        if ($Price) {
          $FormattedPrice = $Price;
        } elseif ($LowestPrice) {
          $FormattedPrice = $LowestPrice;
        } else {
          $FormattedPrice = $HighestPrice;
        }
      }

      ///////////////////////////////////////////
      // Amazon価格タイプに合わせる
      ///////////////////////////////////////////
      switch (get_amazon_item_price_type()) {
        case 'price':
          $FormattedPrice = $Price ? $Price : $FormattedPrice;
          break;
        case 'lowest_price':
          $FormattedPrice = $LowestPrice ? $LowestPrice : $FormattedPrice;
          break;
        case 'highest_price':
          $FormattedPrice = $HighestPrice ? $HighestPrice : $FormattedPrice;
          break;
      }
      // if (is_amazon_item_lowest_price_visible()) {
      //   $FormattedPrice = $LowestPrice ? $LowestPrice : $FormattedPrice;
      // }



      //$associate_url = esc_url($base_url.$ASIN.'/'.$associate_tracking_id.'/');

      ///////////////////////////////////////////
      // 値段表記
      ///////////////////////////////////////////
      $item_price_tag = null;
      //JSONから時間情報を取得（無い場合は現時間）
      $acquired_date = isset($item->{'date'}) ? $item->{'date'} : date_i18n( 'Y/m/d H:i');
      if ((is_amazon_item_price_visible() || $price === '1')
            && $FormattedPrice
            && $price !== '0'
          ) {
        $item_price_tag = get_item_price_tag($FormattedPrice, $acquired_date);
      }
      ///////////////////////////////////////////
      // レビュー
      ///////////////////////////////////////////
      $review_tag = null;
      //_v($review);
      if ((is_amazon_item_customer_reviews_visible() || $review === '1')
          && $associate_tracking_id
          && $review !== '0') {
        $review_url = $review_url = get_amazon_review_url($asin, $associate_tracking_id);
        //_v($review_url);
        $review_tag =
          '<div class="amazon-item-review product-item-review item-review">'.
            '<span class="fa fa-comments-o" aria-hidden="true"></span> <a class="amazon-item-review-link  product-item-review-link item-review-link" href="'.esc_url($review_url).'" target="_blank" rel="nofollow noopener">'.
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
      if ((!is_amazon_item_logo_visible() && is_null($logo)) || (!$logo && !is_null($logo) )) {
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
        (is_amazon_item_catalog_image_visible() && is_null($samples)) ||
        (!is_null($samples) && $samples);

      $image_l_tag = null;
      if ($is_catalog_image_visible && ($size != 'l') && $LargeImageUrl) {
        $image_l_tag =
          '<div class="amazon-item-thumb-l product-item-thumb-l image-content">'.
            '<img src="'.esc_url($LargeImageUrl).'" alt="" width="'.esc_attr($LargeImageWidth).'" height="'.esc_attr($LargeImageHeight).'">'.
          '</div>';
      }
      $swatchimages_tag = null;

      if ($Images && !$image_only && $is_catalog_image_visible) {

        $tmp_tag = null;
        for ($i=0; $i < count($Variants)-1; $i++) {
          $display_none_class = null;
          if (($size != 'l') && ($i >= 3)) {
            $display_none_class .= ' sp-display-none';
          }
          if (($size == 's') && ($i >= 3) || ($size == 'm') && ($i >= 5)) {
            $display_none_class .= ' display-none';
          }

          $Variant = $Variants[$i];
          //SwatchImage
          $SwatchImage = $Variant->{'Small'};
          $SwatchImageURL = $SwatchImage->URL;
          $SwatchImageWidth = $SwatchImage->Width;
          $SwatchImageHeight = $SwatchImage->Height;

          //LargeImage
          $LargeImage = $Variant->{'Large'};
          $LargeImageURL = $LargeImage->URL;
          $LargeImageWidth = $LargeImage->Width;
          $LargeImageHeight = $LargeImage->Height;

          $tmp_tag .=
            '<div class="image-thumb swatch-image-thumb si-thumb'.esc_attr($display_none_class).'">'.
              '<img src="'.esc_url($SwatchImageURL).'" alt="" width="'.esc_attr($SwatchImageWidth).'" height="'.esc_attr($SwatchImageHeight).'">'.
              '<div class="image-content">'.
              '<img src="'.esc_url($LargeImageURL).'" alt="" width="'.esc_attr($LargeImageWidth).'" height="'.esc_attr($LargeImageHeight).'">'.
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
      // Amazonテキストリンク
      ///////////////////////////////////////////
      $text_link_tag =
        '<a href="'.esc_url($associate_url).'" class="amazon-item-title-link product-item-title-link" target="_blank" title="'.esc_attr($TitleAttr).'" rel="nofollow noopener">'.
        $TitleHtml.
        $moshimo_amazon_impression_tag.
        '</a>';
      if ($text_only) {
        return $text_link_tag;
      }

      ///////////////////////////////////////////
      // 商品リンクタグの生成
      ///////////////////////////////////////////
      $tag =
        '<div class="amazon-item-box product-item-box no-icon '.$size_class.$border_class.$logo_class.' '.esc_attr($ProductGroupClass).' '.esc_attr($asin).' cf">'.
          $image_figure_tag.
          '<div class="amazon-item-content product-item-content cf">'.
            '<div class="amazon-item-title product-item-title">'.
            $text_link_tag.
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
    } else {
      $tag = get_amazon_admin_error_message_tag($associate_url, AMAZON_ASIN_ERROR_MESSAGE, $cache_delete_tag, $asin);
    }

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
function get_amazon_admin_error_message_tag($url, $message, $cache_delete_tag = null, $asin = null){
  $error_message = get_amazon_error_product_link($url);
  if (is_user_administrator()) {
    $asin_msg = null;
    if ($asin) {
      $asin_msg = '(ASIN:'.$asin.')';
    }
    $error_message .= '<br><br>'.get_admin_errormessage_box_tag($message.$asin_msg);
  }
  return wrap_product_item_box($error_message, 'amazon', $cache_delete_tag);
}
endif;
