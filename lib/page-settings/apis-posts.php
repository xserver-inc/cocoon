<?php //その他設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

//AmazonアクセスキーID
update_theme_option(OP_AMAZON_API_ACCESS_KEY_ID);

//Amazonシークレットキー
update_theme_option(OP_AMAZON_API_SECRET_KEY);

//AmazonトラッキングID
update_theme_option(OP_AMAZON_ASSOCIATE_TRACKING_ID);

//楽天アフィリエイトID
update_theme_option(OP_RAKUTEN_AFFILIATE_ID);

//APIキャッシュの保存期間
update_theme_option(OP_API_CACHE_RETENTION_PERIOD);