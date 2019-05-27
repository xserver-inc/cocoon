<?php //APIのIDなど
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//AmazonアクセスキーID
define('OP_AMAZON_API_ACCESS_KEY_ID', 'amazon_api_access_key_id');
if ( !function_exists( 'get_amazon_api_access_key_id' ) ):
function get_amazon_api_access_key_id(){
  return get_theme_option(OP_AMAZON_API_ACCESS_KEY_ID);
}
endif;

//Amazonシークレットキー
define('OP_AMAZON_API_SECRET_KEY', 'amazon_api_secret_key');
if ( !function_exists( 'get_amazon_api_secret_key' ) ):
function get_amazon_api_secret_key(){
  return get_theme_option(OP_AMAZON_API_SECRET_KEY);
}
endif;

//AmazonトラッキングID
define('OP_AMAZON_ASSOCIATE_TRACKING_ID', 'amazon_associate_tracking_id');
if ( !function_exists( 'get_amazon_associate_tracking_id' ) ):
function get_amazon_associate_tracking_id(){
  return get_theme_option(OP_AMAZON_ASSOCIATE_TRACKING_ID);
}
endif;

//Amazon検索ボタンを表示する
define('OP_AMAZON_SEARCH_BUTTON_VISIBLE', 'amazon_search_button_visible');
if ( !function_exists( 'is_amazon_search_button_visible' ) ):
function is_amazon_search_button_visible(){
  return get_theme_option(OP_AMAZON_SEARCH_BUTTON_VISIBLE, 1);
}
endif;

//見本画像の表示
define('OP_AMAZON_ITEM_CATALOG_IMAGE_VISIBLE', 'amazon_item_catalog_image_visible');
if ( !function_exists( 'is_amazon_item_catalog_image_visible' ) ):
function is_amazon_item_catalog_image_visible(){
  return get_theme_option(OP_AMAZON_ITEM_CATALOG_IMAGE_VISIBLE, 1);
}
endif;

//Amazon商品リンク価格表示
define('OP_AMAZON_ITEM_PRICE_VISIBLE', 'amazon_item_price_visible');
if ( !function_exists( 'is_amazon_item_price_visible' ) ):
function is_amazon_item_price_visible(){
  return get_theme_option(OP_AMAZON_ITEM_PRICE_VISIBLE);
}
endif;

//Amazon商品リンク在庫価格表示
define('OP_AMAZON_ITEM_STOCK_PRICE_VISIBLE', 'amazon_item_stock_price_visible');
if ( !function_exists( 'is_amazon_item_stock_price_visible' ) ):
function is_amazon_item_stock_price_visible(){
  return get_theme_option(OP_AMAZON_ITEM_STOCK_PRICE_VISIBLE, 1);
}
endif;

//Amazon商品レビュー表示
define('OP_AMAZON_ITEM_CUSTOMER_REVIEWS_VISIBLE', 'amazon_item_customer_reviews_visible');
if ( !function_exists( 'is_amazon_item_customer_reviews_visible' ) ):
function is_amazon_item_customer_reviews_visible(){
  return get_theme_option(OP_AMAZON_ITEM_CUSTOMER_REVIEWS_VISIBLE, 1);
}
endif;

//Amazon商品レビュー文字
define('OP_AMAZON_ITEM_CUSTOMER_REVIEWS_TEXT', 'amazon_item_customer_reviews_text');
if ( !function_exists( 'get_amazon_item_customer_reviews_text' ) ):
function get_amazon_item_customer_reviews_text(){
  return stripslashes_deep(get_theme_option(OP_AMAZON_ITEM_CUSTOMER_REVIEWS_TEXT, __( 'Amazonの商品レビュー・口コミを見る', THEME_NAME )));
}
endif;

//Amazonロゴ表示
define('OP_AMAZON_ITEM_LOGO_VISIBLE', 'amazon_item_logo_visible');
if ( !function_exists( 'is_amazon_item_logo_visible' ) ):
function is_amazon_item_logo_visible(){
  return get_theme_option(OP_AMAZON_ITEM_LOGO_VISIBLE, 1);
}
endif;

//Amazon検索ボタン文字
define('OP_AMAZON_SEARCH_BUTTON_TEXT', 'amazon_search_button_text');
if ( !function_exists( 'get_amazon_search_button_text' ) ):
function get_amazon_search_button_text(){
  return stripslashes_deep(get_theme_option(OP_AMAZON_SEARCH_BUTTON_TEXT, __( 'Amazon', THEME_NAME )));
}
endif;

//Amazon検索ページを詳細ページにする
define('OP_AMAZON_BUTTON_SEARCH_TO_DETAIL', 'amazon_button_search_to_detail');
if ( !function_exists( 'is_amazon_button_search_to_detail' ) ):
function is_amazon_button_search_to_detail(){
  return get_theme_option(OP_AMAZON_BUTTON_SEARCH_TO_DETAIL);
}
endif;

//楽天アプリケーションID
define('OP_RAKUTEN_APPLICATION_ID', 'rakuten_application_id');
if ( !function_exists( 'get_rakuten_application_id' ) ):
function get_rakuten_application_id(){
  return get_theme_option(OP_RAKUTEN_APPLICATION_ID);
}
endif;

//楽天アフィリエイトID
define('OP_RAKUTEN_AFFILIATE_ID', 'rakuten_affiliate_id');
if ( !function_exists( 'get_rakuten_affiliate_id' ) ):
function get_rakuten_affiliate_id(){
  return get_theme_option(OP_RAKUTEN_AFFILIATE_ID);
}
endif;

//楽天商品検索APIの並び順
define('OP_GET_RAKUTEN_API_SORT', 'get_rakuten_api_sort');
if ( !function_exists( 'get_rakuten_api_sort' ) ):
function get_rakuten_api_sort(){
  return get_theme_option(OP_GET_RAKUTEN_API_SORT, '-affiliateRate');
}
endif;

//楽天商品リンク価格表示
define('OP_RAKUTEN_ITEM_PRICE_VISIBLE', 'rakuten_item_price_visible');
if ( !function_exists( 'is_rakuten_item_price_visible' ) ):
function is_rakuten_item_price_visible(){
  return get_theme_option(OP_RAKUTEN_ITEM_PRICE_VISIBLE);
}
endif;

//楽天ロゴ表示
define('OP_RAKUTEN_ITEM_LOGO_VISIBLE', 'rakuten_item_logo_visible');
if ( !function_exists( 'is_rakuten_item_logo_visible' ) ):
function is_rakuten_item_logo_visible(){
  return get_theme_option(OP_RAKUTEN_ITEM_LOGO_VISIBLE, 1);
}
endif;

//楽天検索ボタンを表示する
define('OP_RAKUTEN_SEARCH_BUTTON_VISIBLE', 'rakuten_search_button_visible');
if ( !function_exists( 'is_rakuten_search_button_visible' ) ):
function is_rakuten_search_button_visible(){
  return get_theme_option(OP_RAKUTEN_SEARCH_BUTTON_VISIBLE, 1);
}
endif;

//楽天検索ボタン文字
define('OP_RAKUTEN_SEARCH_BUTTON_TEXT', 'rakuten_search_button_text');
if ( !function_exists( 'get_rakuten_search_button_text' ) ):
function get_rakuten_search_button_text(){
  return stripslashes_deep(get_theme_option(OP_RAKUTEN_SEARCH_BUTTON_TEXT, __( '楽天', THEME_NAME )));
}
endif;

//楽天検索ページを詳細ページにする
define('OP_RAKUTEN_BUTTON_SEARCH_TO_DETAIL', 'rakuten_button_search_to_detail');
if ( !function_exists( 'is_rakuten_button_search_to_detail' ) ):
function is_rakuten_button_search_to_detail(){
  return get_theme_option(OP_RAKUTEN_BUTTON_SEARCH_TO_DETAIL);
}
endif;

//Yahoo!バリューコマースSID
define('OP_YAHOO_VALUECOMMERCE_SID', 'yahoo_valuecommerce_sid');
if ( !function_exists( 'get_yahoo_valuecommerce_sid' ) ):
function get_yahoo_valuecommerce_sid(){
  return get_theme_option(OP_YAHOO_VALUECOMMERCE_SID);
}
endif;

//Yahoo!バリューコマースPID
define('OP_YAHOO_VALUECOMMERCE_PID', 'yahoo_valuecommerce_pid');
if ( !function_exists( 'get_yahoo_valuecommerce_pid' ) ):
function get_yahoo_valuecommerce_pid(){
  return get_theme_option(OP_YAHOO_VALUECOMMERCE_PID);
}
endif;

//Yahoo!検索ボタンを表示する
define('OP_YAHOO_SEARCH_BUTTON_VISIBLE', 'yahoo_search_button_visible');
if ( !function_exists( 'is_yahoo_search_button_visible' ) ):
function is_yahoo_search_button_visible(){
  return get_theme_option(OP_YAHOO_SEARCH_BUTTON_VISIBLE, 1);
}
endif;

//Yahoo!ショッピング検索ボタン文字
define('OP_YAHOO_SEARCH_BUTTON_TEXT', 'yahoo_search_button_text');
if ( !function_exists( 'get_yahoo_search_button_text' ) ):
function get_yahoo_search_button_text(){
  return stripslashes_deep(get_theme_option(OP_YAHOO_SEARCH_BUTTON_TEXT, __( 'Yahoo!ショッピング', THEME_NAME )));
}
endif;

//もしもアフィリエイトリンクを有効にする
define('OP_MOSHIMO_AFFILIATE_LINK_ENABLE', 'moshimo_affiliate_link_enable');
if ( !function_exists( 'is_moshimo_affiliate_link_enable' ) ):
function is_moshimo_affiliate_link_enable(){
  return get_theme_option(OP_MOSHIMO_AFFILIATE_LINK_ENABLE);
}
endif;

//もしもアフィリエイトのAmazon ID
define('OP_MOSHIMO_AMAZON_ID', 'moshimo_amazon_id');
if ( !function_exists( 'get_moshimo_amazon_id' ) ):
function get_moshimo_amazon_id(){
  return get_theme_option(OP_MOSHIMO_AMAZON_ID);
}
endif;

//もしもアフィリエイトの楽天ID
define('OP_MOSHIMO_RAKUTEN_ID', 'moshimo_rakuten_id');
if ( !function_exists( 'get_moshimo_rakuten_id' ) ):
function get_moshimo_rakuten_id(){
  return get_theme_option(OP_MOSHIMO_RAKUTEN_ID);
}
endif;

//もしもアフィリエイトのYahoo!ショッピングID
define('OP_MOSHIMO_YAHOO_ID', 'moshimo_yahoo_id');
if ( !function_exists( 'get_moshimo_yahoo_id' ) ):
function get_moshimo_yahoo_id(){
  return get_theme_option(OP_MOSHIMO_YAHOO_ID);
}
endif;

//APIキャッシュの保存期間
define('OP_API_CACHE_RETENTION_PERIOD', 'api_cache_retention_period');
if ( !function_exists( 'get_api_cache_retention_period' ) ):
function get_api_cache_retention_period(){
  return get_theme_option(OP_API_CACHE_RETENTION_PERIOD, 180);
}
endif;

//APIエラーメールを送信する
define('OP_API_ERROR_MAIL_ENABLE', 'api_error_mail_enable');
if ( !function_exists( 'is_api_error_mail_enable' ) ):
function is_api_error_mail_enable(){
  return get_theme_option(OP_API_ERROR_MAIL_ENABLE, 1);
}
endif;
