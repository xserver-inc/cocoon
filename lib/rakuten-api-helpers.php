<?php //楽天API共通ヘルパー関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 *
 * 楽天商品検索APIの共通処理を一元化するヘルパー関数群。
 * ブロック (block-rakuten-product-link.php)、ショートコード (shortcodes-rakuten.php)、
 * Cron (block-rakuten-product-link-cron.php) で同一ロジックを共有する。
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////////
// APIエンドポイントと認証クエリを構築する
// アクセスキーの有無で新旧APIを自動切り替え
///////////////////////////////////////////
if ( !function_exists( 'cocoon_rakuten_api_build_endpoint' ) ):
function cocoon_rakuten_api_build_endpoint($app_id, $access_key = '') {
  if (empty($access_key)) {
    // アクセスキー未入力時は旧APIへフォールバック
    $api_base   = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/' . COCOON_RAKUTEN_API_LEGACY_VERSION;
    $auth_query = '?applicationId=' . urlencode($app_id);
  } else {
    // アクセスキー入力時は新API（OpenAPI）を利用
    $api_base   = 'https://openapi.rakuten.co.jp/ichibams/api/IchibaItem/Search/' . COCOON_RAKUTEN_API_VERSION;
    $auth_query = '?applicationId=' . urlencode($app_id) . '&accessKey=' . urlencode($access_key);
  }
  return array(
    'base'       => $api_base,
    'auth_query' => $auth_query,
  );
}
endif;

///////////////////////////////////////////
// wp_remote_get 用のHTTPリクエスト引数を構築する
// 新APIで必須となった Referer / Origin ヘッダーを付与
///////////////////////////////////////////
if ( !function_exists( 'cocoon_rakuten_api_get_request_args' ) ):
function cocoon_rakuten_api_get_request_args() {
  $args = array(
    'sslverify' => true,
    'headers'   => array(
      'Referer' => home_url('/'),
      'Origin'  => home_url(),
    ),
  );
  // フィルターフックでカスタマイズ可能にする
  return apply_filters('wp_remote_get_rakuten_args', $args);
}
endif;

///////////////////////////////////////////
// APIレスポンスからエラーメッセージを抽出する
// 新API (errors->errorMessage) と旧API (error_description) の両形式に対応
///////////////////////////////////////////
if ( !function_exists( 'cocoon_rakuten_api_extract_error' ) ):
function cocoon_rakuten_api_extract_error($response) {
  // WP_Errorが渡された場合のガード
  if ( is_wp_error( $response ) ) {
    return $response->get_error_message();
  }

  $ebody = isset($response['body']) ? json_decode($response['body']) : null;
  // 新API形式: {"errors": {"errorCode": 403, "errorMessage": "..."}}
  // 旧API形式: {"error": "...", "error_description": "..."}
  $error_desc = '';
  if (isset($ebody->errors->errorMessage)) {
    $error_desc = $ebody->errors->errorMessage;
  } elseif (isset($ebody->error_description)) {
    $error_desc = $ebody->error_description;
  }
  return $error_desc;
}
endif;
