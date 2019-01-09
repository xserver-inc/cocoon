<?php //SNSフォロー設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//本文下フォローボタンの表示
update_theme_option(OP_SNS_FOLLOW_BUTTONS_VISIBLE);
//SNSフォローメッセージ
update_theme_option(OP_SNS_FOLLOW_MESSAGE);
//feedlyフォローボタンの表示
update_theme_option(OP_FEEDLY_FOLLOW_BUTTON_VISIBLE);
//RSSフォローボタンの表示
update_theme_option(OP_RSS_FOLLOW_BUTTON_VISIBLE);
//ボタンカラー
update_theme_option(OP_SNS_FOLLOW_BUTTON_COLOR);
//デフォルトユーザー
update_theme_option(OP_SNS_DEFAULT_FOLLOW_USER);
//本文下のフォローボタンシェア数の表示
update_theme_option(OP_SNS_FOLLOW_BUTTONS_COUNT_VISIBLE);
//feedlyの購読者数入力
update_theme_option(OP_SNS_FEEDLY_FOLLOW_COUNT);

//SNSフォロー数キャッシュ有効
update_theme_option(OP_SNS_FOLLOW_COUNT_CACHE_ENABLE);
//SNSフォロー数キャッシュ取得間隔
update_theme_option(OP_SNS_FOLLOW_COUNT_CACHE_INTERVAL);
//別スキームのSNSフォロー数を取得するか
update_theme_option(OP_ANOTHER_SCHEME_SNS_FOLLOW_COUNT);
