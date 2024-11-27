<?php //SNSシェア設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//トップシェアボタンの設定保存
require_once abspath(__FILE__).'sns-share-posts-top.php';
//ボトムシェアボタンの設定保存
require_once abspath(__FILE__).'sns-share-posts-bottom.php';

//ツイートにメンションを含める
update_theme_option(OP_TWITTER_ID_INCLUDE);
// //ツイート後にフォローを促す
// update_theme_option(OP_TWITTER_RELATED_FOLLOW_ENABLE);
//ツイートに含めるハッシュタグ
update_theme_option(OP_TWITTER_HASH_TAG);

//Facebookのアクセストークン
update_theme_option(OP_FACEBOOK_ACCESS_TOKEN);

//写真をPinterestで共有する
update_theme_option(OP_PINTEREST_SHARE_PIN_VISIBLE);

//SNSシェア数キャッシュ有効
update_theme_option(OP_SNS_SHARE_COUNT_CACHE_ENABLE);
//SNSシェア数キャッシュ取得間隔
update_theme_option(OP_SNS_SHARE_COUNT_CACHE_INTERVAL);
//別スキームのSNSシェア数を取得するか
update_theme_option(OP_ANOTHER_SCHEME_SNS_SHARE_COUNT);
