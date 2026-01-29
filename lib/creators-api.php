<?php //Creators API
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

// Creators APIの認証情報が有効かどうか
if ( !function_exists( 'is_amazon_creators_api_credentials_available' ) ):
function is_amazon_creators_api_credentials_available(){
  // 認証情報IDとシークレットの両方がある場合のみ有効
  return (bool)(get_amazon_creators_api_credential_id() && get_amazon_creators_api_secret());
}
endif;

// Creators APIのOAuth2トークンエンドポイントを取得
if ( !function_exists( 'amazon_creators_api_get_token_endpoint' ) ):
function amazon_creators_api_get_token_endpoint($version){
  switch ($version) {
    case '2.1':
      return 'https://creatorsapi.auth.us-east-1.amazoncognito.com/oauth2/token';
    case '2.2':
      return 'https://creatorsapi.auth.eu-south-2.amazoncognito.com/oauth2/token';
    case '2.3':
      return 'https://creatorsapi.auth.us-west-2.amazoncognito.com/oauth2/token';
    default:
      return '';
  }
}
endif;

// Creators APIのOAuth2アクセストークンを取得
if ( !function_exists( 'amazon_creators_api_get_access_token' ) ):
function amazon_creators_api_get_access_token($credential_id, $credential_secret, $version){
  if (!function_exists('curl_init')) {
    return array(
      'error' => amazon_creators_api_error_json(
        'CreatorsApiCurlMissing',
        __( 'Creators APIを利用するにはPHPのcURL拡張が必要です。', THEME_NAME )
      ),
    );
  }

  $transient_key = 'amazon_creators_api_token_'.md5($credential_id.'|'.$credential_secret.'|'.$version);
  $cached = get_transient($transient_key);
  if ($cached) {
    return array('token' => $cached);
  }

  $token_endpoint = amazon_creators_api_get_token_endpoint($version);
  if (!$token_endpoint) {
    return array(
      'error' => amazon_creators_api_error_json(
        'CreatorsApiInvalidVersion',
        __( 'Creators APIの認証バージョンが正しくありません。', THEME_NAME )
      ),
    );
  }

  $post_fields = http_build_query(array(
    'grant_type' => 'client_credentials',
    'client_id' => $credential_id,
    'client_secret' => $credential_secret,
    'scope' => 'creatorsapi/default',
  ));

  $ch = curl_init($token_endpoint);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, (int)apply_filters('amazon_creators_api_connect_timeout', 10));
  curl_setopt($ch, CURLOPT_TIMEOUT, (int)apply_filters('amazon_creators_api_timeout', 20));

  $response = curl_exec($ch);
  $curl_errno = curl_errno($ch);
  $curl_error = curl_error($ch);
  $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($curl_errno) {
    amazon_creators_api_debug_log('token curl error: '.$curl_error);
    return array(
      'error' => amazon_creators_api_error_json('CreatorsApiTokenCurlError', $curl_error),
    );
  }

  if (!$response) {
    return array(
      'error' => amazon_creators_api_error_json('CreatorsApiTokenEmpty', __( 'Creators APIのトークン取得に失敗しました。', THEME_NAME )),
    );
  }

  $data = json_decode($response, true);
  if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    return array(
      'error' => amazon_creators_api_error_json('CreatorsApiTokenJsonDecodeError', __( 'Creators APIのトークンレスポンスを解析できませんでした。', THEME_NAME )),
    );
  }

  if ($http_code >= 400) {
    $message = isset($data['error_description']) ? $data['error_description'] : (isset($data['error']) ? $data['error'] : 'token error');
    return array(
      'error' => amazon_creators_api_error_json('CreatorsApiTokenHttpError', $message),
    );
  }

  if (empty($data['access_token'])) {
    return array(
      'error' => amazon_creators_api_error_json('CreatorsApiTokenMissing', __( 'Creators APIのアクセストークンが取得できませんでした。', THEME_NAME )),
    );
  }

  $expires_in = isset($data['expires_in']) ? (int)$data['expires_in'] : 3600;
  $expiration = max(60, $expires_in - 30);
  set_transient($transient_key, $data['access_token'], $expiration);

  return array('token' => $data['access_token']);
}
endif;

// デバッグのON/OFFを切り替えるためのフラグを取得
if ( !function_exists( 'amazon_creators_api_debug_enabled' ) ):
function amazon_creators_api_debug_enabled(){
  return (bool)apply_filters('amazon_creators_api_debug', false);
}
endif;

// Creators APIのデバッグログを書き出す
if ( !function_exists( 'amazon_creators_api_debug_log' ) ):
function amazon_creators_api_debug_log($message){
  if (!amazon_creators_api_debug_enabled()) {
    return;
  }
  // デバッグログの保存先を決めて、必要ならフォルダを作る
  $log_file = apply_filters('amazon_creators_api_debug_log_file', get_theme_resources_path().'creators_api_debug.log');
  $log_dir = dirname($log_file);
  if (!is_dir($log_dir)) {
    wp_mkdir_p($log_dir);
  }
  $timestamp = date_i18n('Y-m-d H:i:s');
  error_log('[CreatorsAPI] '.$timestamp.' '.$message.PHP_EOL, 3, $log_file);
}
endif;

// Creators APIのエラーをJSON形式に変換
if ( !function_exists( 'amazon_creators_api_error_json' ) ):
function amazon_creators_api_error_json($code, $message){
  // 既存UIで扱えるようにPA-API互換のエラー形式に寄せる
  $error = (object)array(
    'Code' => (string)$code,
    'Message' => (string)$message,
  );
  // エラーをJSON形式に変換
  return wp_json_encode((object)array('Errors' => array($error)));
}
endif;

// Creators APIのエラーを正規化
if ( !function_exists( 'amazon_creators_api_normalize_errors' ) ):
function amazon_creators_api_normalize_errors($errors){
  // Creators APIのエラー配列をPA-API形式に正規化
  if (!is_array($errors)) {
    return null;
  }
  // エラーを1個ずつPA-API形式にする
  $normalized = array();
  foreach ($errors as $error) {
    $code = is_object($error) && isset($error->code) ? $error->code : 'CreatorsApiError';
    $message = is_object($error) && isset($error->message) ? $error->message : '';
    $normalized[] = (object)array(
      'Code' => $code,
      'Message' => $message,
    );
  }
  if (empty($normalized)) {
    return null;
  }
  // まとめたエラーをJSONにする
  return wp_json_encode((object)array('Errors' => $normalized));
}
endif;

// Creators APIのキー名をPA-APIの表記に寄せる
if ( !function_exists( 'amazon_creators_api_key_to_paapi' ) ):
function amazon_creators_api_key_to_paapi($key){
  switch ($key) {
    case 'displayValue':
      return 'DisplayValue';
    case 'displayValues':
      return 'DisplayValues';
    case 'displayAmount':
      return 'DisplayAmount';
    case 'url':
      return 'URL';
    case 'hiRes':
      return 'HiRes';
    default:
      return ucfirst($key);
  }
}
endif;

// Creators APIのオブジェクト/配列のキー名を再帰的にPA-API形式へ変換
if ( !function_exists( 'amazon_creators_api_convert_object_keys' ) ):
function amazon_creators_api_convert_object_keys($value){
  // オブジェクト/配列のキー名を再帰的にPA-API形式へ変換
  if (is_array($value)) {
    $result = array();
    foreach ($value as $item) {
      $result[] = amazon_creators_api_convert_object_keys($item);
    }
    return $result;
  }
  // オブジェクトでなければそのまま返す
  if (!is_object($value)) {
    return $value;
  }
  // オブジェクトのキー名を置き換える
  $obj = new stdClass();
  foreach ($value as $key => $val) {
    $pa_key = amazon_creators_api_key_to_paapi($key);
    $obj->{$pa_key} = amazon_creators_api_convert_object_keys($val);
  }
  return $obj;
}
endif;

// Creators APIのoffersV2をPA-APIのOffers構造に変換
if ( !function_exists( 'amazon_creators_api_convert_offers' ) ):
function amazon_creators_api_convert_offers($offers_v2){
  // offersV2をPA-APIのOffers構造に変換
  if (!is_object($offers_v2) || !isset($offers_v2->listings) || !is_array($offers_v2->listings)) {
    return null;
  }
  // 先頭のlistingを使う
  $listing = isset($offers_v2->listings[0]) ? $offers_v2->listings[0] : null;
  if (!$listing || !is_object($listing)) {
    return null;
  }
  // 価格情報を取得
  $price = isset($listing->price) && is_object($listing->price) ? $listing->price : null;
  // 価格の通貨/表示額を取得
  $price_money = ($price && isset($price->money) && is_object($price->money)) ? $price->money : null;
  // 基準価格の情報を取得
  $saving_basis = ($price && isset($price->savingBasis) && is_object($price->savingBasis)) ? $price->savingBasis : null;
  // 基準価格の通貨/表示額を取得
  $saving_money = ($saving_basis && isset($saving_basis->money) && is_object($saving_basis->money)) ? $saving_basis->money : null;
  // 在庫や発送状況の情報を取得
  $availability = isset($listing->availability) && is_object($listing->availability) ? $listing->availability : null;
  // 商品状態の情報を取得
  $condition = isset($listing->condition) && is_object($listing->condition) ? $listing->condition : null;
  // ポイント情報を取得
  $loyalty = isset($listing->loyaltyPoints) && is_object($listing->loyaltyPoints) ? $listing->loyaltyPoints : null;

  // Listings用のオブジェクトを作る
  $listing_obj = new stdClass();
  if ($price_money && isset($price_money->displayAmount)) {
    // 販売価格の表示用金額を入れる
    $listing_obj->Price = (object)array('DisplayAmount' => $price_money->displayAmount);
  }
  if ($saving_money && isset($saving_money->displayAmount)) {
    // 参考価格の表示用金額を入れる
    $listing_obj->SavingBasis = (object)array('DisplayAmount' => $saving_money->displayAmount);
  }
  if ($availability) {
    // 在庫・購入可能数などの情報を入れる
    $listing_obj->Availability = (object)array(
      'Message' => isset($availability->message) ? $availability->message : null,
      'Type' => isset($availability->type) ? $availability->type : null,
      'MaxOrderQuantity' => isset($availability->maxOrderQuantity) ? $availability->maxOrderQuantity : null,
      'MinOrderQuantity' => isset($availability->minOrderQuantity) ? $availability->minOrderQuantity : null,
    );
  }
  if ($condition) {
    // 商品状態の情報を入れる
    $listing_obj->Condition = (object)array(
      'Value' => isset($condition->value) ? $condition->value : null,
      'SubCondition' => isset($condition->subCondition) ? $condition->subCondition : null,
      'ConditionNote' => isset($condition->conditionNote) ? $condition->conditionNote : null,
    );
  }
  if ($loyalty && isset($loyalty->points)) {
    $listing_obj->LoyaltyPoints = (object)array('Points' => $loyalty->points);
  }
  // 出品者情報があれば変換して入れる
  if (isset($listing->merchantInfo) && is_object($listing->merchantInfo)) {
    $listing_obj->MerchantInfo = amazon_creators_api_convert_object_keys($listing->merchantInfo);
  }

  // Summariesに最低/最高価格を入れる
  $summaries = new stdClass();
  if ($price_money && isset($price_money->displayAmount)) {
    // 最高価格の表示用金額を入れる
    $summaries->HighestPrice = (object)array('DisplayAmount' => $price_money->displayAmount);
    // 最低価格の表示用金額を入れる
    $summaries->LowestPrice = (object)array('DisplayAmount' => $price_money->displayAmount);
  }

  // Offers全体を組み立てる
  $offers = new stdClass();
  // Listings（出品情報）を入れる
  $offers->Listings = array($listing_obj);
  // Summaries（価格のまとめ）を入れる
  $offers->Summaries = array($summaries);
  return $offers;
}
endif;

// Creators APIのitemをPA-API互換のItem構造へ変換
if ( !function_exists( 'amazon_creators_api_convert_item_to_paapi' ) ):
function amazon_creators_api_convert_item_to_paapi($item){
  // itemをPA-API互換のItem構造へ変換
  // 期待する形でなければ終了
  if (!is_object($item)) {
    return null;
  }
  // 変換後の入れ物を作る
  $pa_item = new stdClass();
  // 商品ページのURLを入れる
  if (isset($item->detailPageURL)) {
    $pa_item->DetailPageURL = $item->detailPageURL;
  }
  // 親ASINを入れる
  if (isset($item->parentASIN)) {
    $pa_item->ParentASIN = $item->parentASIN;
  }
  // 各情報をPA-APIの形に変換して入れる
  if (isset($item->browseNodeInfo)) {
    $pa_item->BrowseNodeInfo = amazon_creators_api_convert_object_keys($item->browseNodeInfo);
  }
  if (isset($item->customerReviews)) {
    $pa_item->CustomerReviews = amazon_creators_api_convert_object_keys($item->customerReviews);
  }
  if (isset($item->images)) {
    $pa_item->Images = amazon_creators_api_convert_object_keys($item->images);
  }
  if (isset($item->itemInfo)) {
    $pa_item->ItemInfo = amazon_creators_api_convert_object_keys($item->itemInfo);
  }
  // offersV2をOffersへ変換する
  if (isset($item->offersV2)) {
    $pa_item->Offers = amazon_creators_api_convert_offers($item->offersV2);
  }
  return $pa_item;
}
endif;

// Creators APIのレスポンス全体をPA-API互換に整形
if ( !function_exists( 'amazon_creators_api_convert_response_to_paapi' ) ):
function amazon_creators_api_convert_response_to_paapi($json){
  // Creators APIのレスポンス全体をPA-API互換に整形
  // itemsResult.items がないと変換できない
  if (!is_object($json) || !isset($json->itemsResult) || !isset($json->itemsResult->items) || !is_array($json->itemsResult->items)) {
    return null;
  }
  // itemを1つずつ変換する
  $items = array();
  foreach ($json->itemsResult->items as $item) {
    $pa_item = amazon_creators_api_convert_item_to_paapi($item);
    if ($pa_item) {
      $items[] = $pa_item;
    }
  }
  // PA-API形式のItemsResultを作る
  $pa = new stdClass();
  $pa->ItemsResult = (object)array('Items' => $items);
  return $pa;
}
endif;

// Creators APIのレスポンスがエラーを持つかどうか
if ( !function_exists( 'is_creators_api_json_error' ) ):
function is_creators_api_json_error($json){
  // Creators APIは"errors"配下にエラーを持つ
  if (is_null($json) || !is_object($json)) {
    return false;
  }
  return property_exists($json, 'errors');
}
endif;

// Creators APIのレスポンスが商品情報を持つかどうか
if ( !function_exists( 'is_creators_api_json_item_exist' ) ):
function is_creators_api_json_item_exist($json){
  // itemsResult.items が存在するかを確認
  if (is_null($json) || !is_object($json)) {
    return false;
  }
  if (isset($json->{'itemsResult'})) {
    $itemsResult = $json->{'itemsResult'};
    if (is_object($itemsResult)) {
      return property_exists($itemsResult, 'items');
    }
  }
  return false;
}
endif;

// Creators APIの商品情報を取得
if ( !function_exists( 'get_amazon_creators_itemlookup_json' ) ):
function get_amazon_creators_itemlookup_json($asin, $tracking_id = null){
  // 空白を取り除く
  $asin = trim($asin);
  if (empty($asin)) {
    return false;
  }

  // デバッグ開始
  amazon_creators_api_debug_log('start asin='.$asin);

  // 認証情報が不足している場合は処理しない
  if (!is_amazon_creators_api_credentials_available()) {
    amazon_creators_api_debug_log('missing credentials');
    return false;
  }

  // 追跡IDの有無でキャッシュキーを変える
  $tracking_id = trim($tracking_id);
  $tid = null;
  if ($tracking_id) {
    $tid = '+'.$tracking_id;
  }

  // PA-APIと同じトランジェントキャッシュを流用
  $transient_id = get_amazon_api_transient_id($asin.$tid);
  $transient_bk_id = get_amazon_api_transient_bk_id($asin.$tid);
  $json_cache = get_transient( $transient_id );
  if ($json_cache && DEBUG_CACHE_ENABLE) {
    $cache_json = json_decode($json_cache);
    if (json_last_error() === JSON_ERROR_NONE && $cache_json) {
      // エラーキャッシュは維持する
      if (function_exists('is_paapi_json_error') && is_paapi_json_error($cache_json)) {
        return $json_cache;
      }
      // 商品情報が揃っているキャッシュのみ返す
      $ItemsResult = isset($cache_json->{'ItemsResult'}) ? $cache_json->{'ItemsResult'} : null;
      if ($ItemsResult && isset($ItemsResult->{'Items'}) && is_array($ItemsResult->{'Items'}) && !empty($ItemsResult->{'Items'}[0])) {
        return $json_cache;
      }
    }
    delete_transient($transient_id);
    delete_transient($transient_bk_id);
  }

  // 認証情報とトラッキングIDを取得
  $credential_id = trim(get_amazon_creators_api_credential_id());
  $credential_secret = trim(get_amazon_creators_api_secret());
  $partnerTag = trim(get_amazon_associate_tracking_id($tracking_id));

  // 既定は日本向け(FE)・JPマーケット。必要ならフィルタで変更
  $version = apply_filters('amazon_creators_api_version', '2.3');
  $marketplace = apply_filters('amazon_creators_api_marketplace', AMAZON_DOMAIN);

  // デバッグ用に送信情報の状態を記録
  amazon_creators_api_debug_log('request marketplace='.$marketplace.' version='.$version.' partnerTag='.($partnerTag ? 'set' : 'empty'));

  // 既存のPA-API利用に近いリソースを要求
  $resources = array(
    'browseNodeInfo.browseNodes',
    'browseNodeInfo.browseNodes.ancestor',
    'browseNodeInfo.browseNodes.salesRank',
    'browseNodeInfo.websiteSalesRank',
    'customerReviews.count',
    'customerReviews.starRating',
    'images.primary.small',
    'images.primary.medium',
    'images.primary.large',
    'images.variants.small',
    'images.variants.medium',
    'images.variants.large',
    'itemInfo.byLineInfo',
    'itemInfo.contentInfo',
    'itemInfo.contentRating',
    'itemInfo.classifications',
    'itemInfo.externalIds',
    'itemInfo.features',
    'itemInfo.manufactureInfo',
    'itemInfo.productInfo',
    'itemInfo.technicalInfo',
    'itemInfo.title',
    'itemInfo.tradeInInfo',
    'parentASIN',
    'offersV2.listings.availability',
    'offersV2.listings.condition',
    'offersV2.listings.dealDetails',
    'offersV2.listings.isBuyBoxWinner',
    'offersV2.listings.loyaltyPoints',
    'offersV2.listings.merchantInfo',
    'offersV2.listings.price',
    'offersV2.listings.type',
  );
  $resources = apply_filters('amazon_creators_api_get_items_resources', $resources);

  // キャッシュ保持はPA-APIと同じく24時間
  $days = 1;

  // OAuth2トークンを取得
  $token_result = amazon_creators_api_get_access_token($credential_id, $credential_secret, $version);
  if (isset($token_result['error'])) {
    return $token_result['error'];
  }
  $access_token = isset($token_result['token']) ? $token_result['token'] : '';
  if (!$access_token) {
    return amazon_creators_api_error_json('CreatorsApiTokenMissing', __( 'Creators APIのアクセストークンが取得できませんでした。', THEME_NAME ));
  }

  // リクエストの本文を作成
  $request_body = array(
    'partnerTag' => $partnerTag,
    'itemIds' => array($asin),
    'resources' => $resources,
  );
  $request_body = apply_filters('amazon_creators_api_get_items_payload', $request_body, $asin, $partnerTag);
  $request_json = wp_json_encode($request_body);

  $host = apply_filters('amazon_creators_api_host', 'https://creatorsapi.amazon');
  $endpoint = $host.'/catalog/v1/getItems';

  $headers = array(
    'Authorization: Bearer '.$access_token.', Version '.$version,
    'Content-Type: application/json',
    'Accept: application/json',
    'x-marketplace: '.$marketplace,
    'User-Agent: cocoon-creatorsapi-curl',
  );

  $ch = curl_init($endpoint);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $request_json);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, (int)apply_filters('amazon_creators_api_connect_timeout', 10));
  curl_setopt($ch, CURLOPT_TIMEOUT, (int)apply_filters('amazon_creators_api_timeout', 20));

  $res = curl_exec($ch);
  $curl_errno = curl_errno($ch);
  $curl_error = curl_error($ch);
  $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($curl_errno) {
    amazon_creators_api_debug_log('api curl error: '.$curl_error);
    return amazon_creators_api_error_json('CreatorsApiCurlError', $curl_error);
  }

  if ($http_code >= 400) {
    amazon_creators_api_debug_log('api http error: '.$http_code);
    if (!$res) {
      return amazon_creators_api_error_json('CreatorsApiHttpError', 'HTTP '.$http_code);
    }
    $error_json = json_decode($res, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($error_json)) {
      return amazon_creators_api_error_json('CreatorsApiHttpError', 'HTTP '.$http_code);
    }
  }

  amazon_creators_api_debug_log('response received');

  // 空のレスポンスなら失敗扱い
  if (!$res) {
    return false;
  }

  // JSONを解析する
  $json = json_decode($res);
  if (json_last_error() !== JSON_ERROR_NONE) {
    // JSONとして解析できない場合はエラーを返す
    amazon_creators_api_debug_log('json decode error');
    return amazon_creators_api_error_json('CreatorsApiJsonDecodeError', __( 'Creators APIのレスポンスを解析できませんでした。', THEME_NAME ));
  }

  if ($json) {
    // Creators APIのerrorsをPA-API互換のErrorsに変換
    if (isset($json->errors)) {
      amazon_creators_api_debug_log('errors in response');
      $normalized_errors = amazon_creators_api_normalize_errors($json->errors);
      if ($normalized_errors) {
        return $normalized_errors;
      }
    }

    // Creators APIのレスポンスをPA-API互換に変換して表示ロジックに合わせる
    $paapi_json = amazon_creators_api_convert_response_to_paapi($json);
    if ($paapi_json) {
      $json = $paapi_json;
      $res = wp_json_encode($json);
    }

    // PA-API互換のErrorsがあればエラー扱いにする
    if (function_exists('is_paapi_json_error') && is_paapi_json_error($json)) {
      $json_cache = get_transient( $transient_bk_id );
      if ($json_cache && DEBUG_CACHE_ENABLE) {
        return $json_cache;
      }
      return $res;
    }

    // Creators APIのエラーならキャッシュを返すか、そのまま返す
    if (is_creators_api_json_error($json)) {
      $json_cache = get_transient( $transient_bk_id );
      if ($json_cache && DEBUG_CACHE_ENABLE) {
        return $json_cache;
      }
      return $res;
    }

    // 取得失敗は既存のAmazonエラーログに記録
    if (!is_paapi_json_item_exist($json) && !is_creators_api_json_item_exist($json)) {
      amazon_creators_api_debug_log('items not found in response');
      error_log_to_amazon_product($asin, AMAZON_ASIN_ERROR_MESSAGE);
    }
  }

  // キャッシュが有効なら保存する
  if (DEBUG_CACHE_ENABLE) {
    // キャッシュ更新時刻を挿入して可視化
    $count = 1;
    $res = str_replace(
      array(',"browseNodeInfo":{', ',"BrowseNodeInfo":{'),
      array(',"date":"'.date_i18n( 'Y/m/d H:i').'","browseNodeInfo":{', ',"date":"'.date_i18n( 'Y/m/d H:i').'","BrowseNodeInfo":{'),
      $res,
      $count
    );
    $expiration = DAY_IN_SECONDS * $days + (rand(0, 60) * 60);
    set_transient($transient_id, $res, $expiration);
    set_transient($transient_bk_id, $res, $expiration * 2);
  }

  return $res;
}
endif;
