<?php //OGP設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//Facebook OGPを有効
update_theme_option(OP_FACEBOOK_OGP_ENABLE);

//Facebook App-ID
update_theme_option(OP_FACEBOOK_APP_ID);

//Twitterカードを有効
update_theme_option(OP_TWITTER_CARD_ENABLE);

//Twitterカードタイプ
update_theme_option(OP_TWITTER_CARD_TYPE);

//ホームイメージ
update_theme_option(OP_OGP_HOME_IMAGE_URL);
