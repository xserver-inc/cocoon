<?php //その他設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//AmazonアクセスキーID
update_theme_option(OP_AMAZON_API_ACCESS_KEY_ID);

//Amazonシークレットキー
update_theme_option(OP_AMAZON_API_SECRET_KEY);

//AmazonトラッキングID
update_theme_option(OP_AMAZON_ASSOCIATE_TRACKING_ID);

//Amazon検索ボタンを表示する
update_theme_option(OP_AMAZON_SEARCH_BUTTON_VISIBLE);

//見本画像の表示
update_theme_option(OP_AMAZON_ITEM_CATALOG_IMAGE_VISIBLE);

//Amazon商品リンク価格表示
update_theme_option(OP_AMAZON_ITEM_PRICE_VISIBLE);


//Amazon商品リンクで表示する価格
update_theme_option(OP_AMAZON_ITEM_PRICE_TYPE);

// //Amazon商品リンク在庫価格表示
// update_theme_option(OP_AMAZON_ITEM_STOCK_PRICE_VISIBLE);

// //Amazon商品リンクの最安価格を表示する
// update_theme_option(OP_AMAZON_ITEM_LOWEST_PRICE_VISIBLE);

//Amazon商品リンク説明文表示
update_theme_option(OP_AMAZON_ITEM_DESCRIPTION_VISIBLE);

//Amazon商品レビュー表示
update_theme_option(OP_AMAZON_ITEM_CUSTOMER_REVIEWS_VISIBLE);

//Amazon商品レビュー文字
update_theme_option(OP_AMAZON_ITEM_CUSTOMER_REVIEWS_TEXT);

//Amazonロゴ表示
update_theme_option(OP_AMAZON_ITEM_LOGO_VISIBLE);

//Amazon検索ボタン文字
update_theme_option(OP_AMAZON_SEARCH_BUTTON_TEXT);

//楽天アプリケーションID
update_theme_option(OP_RAKUTEN_APPLICATION_ID);

//楽天アフィリエイトID
update_theme_option(OP_RAKUTEN_AFFILIATE_ID);

//楽天商品検索APIの並び順
update_theme_option(OP_GET_RAKUTEN_API_SORT);

//楽天商品リンク価格表示
update_theme_option(OP_RAKUTEN_ITEM_PRICE_VISIBLE);

//楽天ロゴ表示
update_theme_option(OP_RAKUTEN_ITEM_LOGO_VISIBLE);

//楽天検索ボタンを表示する
update_theme_option(OP_RAKUTEN_SEARCH_BUTTON_VISIBLE);

//楽天検索ボタン文字
update_theme_option(OP_RAKUTEN_SEARCH_BUTTON_TEXT);

//楽天検索ページを詳細ページにする
update_theme_option(OP_RAKUTEN_BUTTON_SEARCH_TO_DETAIL);

//Amazon検索ページを詳細ページにする
update_theme_option(OP_AMAZON_BUTTON_SEARCH_TO_DETAIL);

//Yahoo!バリューコマースSID
update_theme_option(OP_YAHOO_VALUECOMMERCE_SID);

//Yahoo!バリューコマースPID
update_theme_option(OP_YAHOO_VALUECOMMERCE_PID);

//Yahoo!検索ボタンを表示する
update_theme_option(OP_YAHOO_SEARCH_BUTTON_VISIBLE);

//Yahoo!検索ボタンテキスト
update_theme_option(OP_YAHOO_SEARCH_BUTTON_TEXT);

//DMMアフィリエイトID
update_theme_option(OP_DMM_AFFILIATE_ID);

//DMM検索ボタンを表示する
update_theme_option(OP_DMM_SEARCH_BUTTON_VISIBLE);

//DMM検索ボタン文字
update_theme_option(OP_DMM_SEARCH_BUTTON_TEXT);

//もしもアフィリエイトリンクを有効にする
update_theme_option(OP_MOSHIMO_AFFILIATE_LINK_ENABLE);

//もしもアフィリエイトのAmazon ID
update_theme_option(OP_MOSHIMO_AMAZON_ID);

//もしもアフィリエイトの楽天ID
update_theme_option(OP_MOSHIMO_RAKUTEN_ID);

//もしもアフィリエイトのYahoo!ショッピングID
update_theme_option(OP_MOSHIMO_YAHOO_ID);

//APIキャッシュの保存期間
update_theme_option(OP_API_CACHE_RETENTION_PERIOD);


//APIエラーメールを送信する
update_theme_option(OP_API_ERROR_MAIL_ENABLE);
