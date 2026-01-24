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

// Creators API SDKを読み込む
if ( !function_exists( 'amazon_creators_api_autoload' ) ):
function amazon_creators_api_autoload(){
  // テーマ内に配置されたCreators API SDKを読み込む
  $autoload_path = apply_filters('amazon_creators_api_autoload_path', __DIR__.'/../creatorsapi-php-sdk/vendor/autoload.php');
  if (!$autoload_path || !file_exists($autoload_path)) {
    return false;
  }
  require_once $autoload_path;
  return class_exists('Amazon\\CreatorsAPI\\v1\\Configuration');
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
  $log_file = apply_filters('amazon_creators_api_debug_log_file', get_theme_resources_path().'creators_api_debug.log');
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
  // 値段や基準価格の情報を拾う
  $price = isset($listing->price) && is_object($listing->price) ? $listing->price : null;
  $price_money = ($price && isset($price->money) && is_object($price->money)) ? $price->money : null;
  $saving_basis = ($price && isset($price->savingBasis) && is_object($price->savingBasis)) ? $price->savingBasis : null;
  $saving_money = ($saving_basis && isset($saving_basis->money) && is_object($saving_basis->money)) ? $saving_basis->money : null;

  // Listings用のオブジェクトを作る
  $listing_obj = new stdClass();
  if ($price_money && isset($price_money->displayAmount)) {
    $listing_obj->Price = (object)array('DisplayAmount' => $price_money->displayAmount);
  }
  if ($saving_money && isset($saving_money->displayAmount)) {
    $listing_obj->SavingBasis = (object)array('DisplayAmount' => $saving_money->displayAmount);
  }
  // 出品者情報があれば変換して入れる
  if (isset($listing->merchantInfo) && is_object($listing->merchantInfo)) {
    $listing_obj->MerchantInfo = amazon_creators_api_convert_object_keys($listing->merchantInfo);
  }

  // Summariesに最低/最高価格を入れる
  $summaries = new stdClass();
  if ($price_money && isset($price_money->displayAmount)) {
    $summaries->HighestPrice = (object)array('DisplayAmount' => $price_money->displayAmount);
    $summaries->LowestPrice = (object)array('DisplayAmount' => $price_money->displayAmount);
  }

  // Offers全体を組み立てる
  $offers = new stdClass();
  $offers->Listings = array($listing_obj);
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

  // 認証情報やSDKが不足している場合は処理しない
  if (!is_amazon_creators_api_credentials_available()) {
    amazon_creators_api_debug_log('missing credentials');
    return false;
  }

  if (!amazon_creators_api_autoload()) {
    amazon_creators_api_debug_log('autoload failed');
    return amazon_creators_api_error_json(
      'CreatorsApiSdkMissing',
      __( 'Creators API SDKが見つかりません。テーマ直下のcreatorsapi-php-sdk/vendor/autoload.phpが読み込めることを確認してください。', THEME_NAME )
    );
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
    return $json_cache;
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
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::BROWSE_NODE_INFO_BROWSE_NODES,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::BROWSE_NODE_INFO_BROWSE_NODES_ANCESTOR,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::BROWSE_NODE_INFO_BROWSE_NODES_SALES_RANK,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::BROWSE_NODE_INFO_WEBSITE_SALES_RANK,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::CUSTOMER_REVIEWS_COUNT,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::CUSTOMER_REVIEWS_STAR_RATING,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::IMAGES_PRIMARY_SMALL,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::IMAGES_PRIMARY_MEDIUM,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::IMAGES_PRIMARY_LARGE,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::IMAGES_VARIANTS_SMALL,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::IMAGES_VARIANTS_MEDIUM,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::IMAGES_VARIANTS_LARGE,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::ITEM_INFO_BY_LINE_INFO,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::ITEM_INFO_CONTENT_INFO,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::ITEM_INFO_CONTENT_RATING,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::ITEM_INFO_CLASSIFICATIONS,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::ITEM_INFO_EXTERNAL_IDS,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::ITEM_INFO_FEATURES,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::ITEM_INFO_MANUFACTURE_INFO,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::ITEM_INFO_PRODUCT_INFO,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::ITEM_INFO_TECHNICAL_INFO,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::ITEM_INFO_TITLE,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::ITEM_INFO_TRADE_IN_INFO,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::PARENT_ASIN,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::OFFERS_V2_LISTINGS_AVAILABILITY,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::OFFERS_V2_LISTINGS_CONDITION,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::OFFERS_V2_LISTINGS_DEAL_DETAILS,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::OFFERS_V2_LISTINGS_IS_BUY_BOX_WINNER,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::OFFERS_V2_LISTINGS_LOYALTY_POINTS,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::OFFERS_V2_LISTINGS_MERCHANT_INFO,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::OFFERS_V2_LISTINGS_PRICE,
    \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource::OFFERS_V2_LISTINGS_TYPE,
  );
  $resources = apply_filters('amazon_creators_api_get_items_resources', $resources);

  // キャッシュ保持はPA-APIと同じく24時間
  $days = 1;

  try {
    // SDKを使ってGetItemsを実行
    $config = new \Amazon\CreatorsAPI\v1\Configuration();
    $config->setCredentialId($credential_id);
    $config->setCredentialSecret($credential_secret);
    $config->setVersion($version);

    $apiInstance = new \Amazon\CreatorsAPI\v1\com\amazon\creators\api\DefaultApi(null, $config);
    $getItemsRequest = new \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsRequestContent();
    $getItemsRequest->setPartnerTag($partnerTag);
    $getItemsRequest->setItemIds(array($asin));
    $getItemsRequest->setResources($resources);

    // APIを呼び出す
    $response = $apiInstance->getItems($marketplace, $getItemsRequest);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    amazon_creators_api_debug_log('response received');
  } catch (\Amazon\CreatorsAPI\v1\ApiException $e) {
    $res_body = $e->getResponseBody();
    // 可能な限りレスポンス内容を保持して管理画面に表示
    if (is_string($res_body)) {
      $res = $res_body;
    } else {
      $res = json_encode($res_body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    if (!$res) {
      amazon_creators_api_debug_log('api exception: '.$e->getMessage());
      return amazon_creators_api_error_json('CreatorsApiException', $e->getMessage());
    }
  } catch (Exception $e) {
    // 予期しない例外は統一フォーマットで返す
    amazon_creators_api_debug_log('exception: '.$e->getMessage());
    return amazon_creators_api_error_json('CreatorsApiException', $e->getMessage());
  }

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

    // PA-APIのエラーハンドリングに合わせる
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
    $res = str_replace(',"browseNodeInfo":{', ',"date":"'.date_i18n( 'Y/m/d H:i').'","browseNodeInfo":{', $res, $count);
    $expiration = DAY_IN_SECONDS * $days + (rand(0, 60) * 60);
    set_transient($transient_id, $res, $expiration);
    set_transient($transient_bk_id, $res, $expiration * 2);
  }

  return $res;
}
endif;
