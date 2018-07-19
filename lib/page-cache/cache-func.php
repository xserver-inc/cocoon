<?php //バックアップ関係の関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

//SNSカウントキャシュの削除
if ( !function_exists( 'delete_sns_count_caches' ) ):
function delete_sns_count_caches(){
  if (is_user_administrator()) {
    $res =  delete_db_cache_records(TRANSIENT_SHARE_PREFIX);
    if ($res) {
      return delete_db_cache_records(TRANSIENT_FOLLOW_PREFIX);
    }
  }
}
endif;

//人気記事ウィジェットキャシュの削除
if ( !function_exists( 'delete_popular_entries_caches' ) ):
function delete_popular_entries_caches(){
  if (is_user_administrator()) {
    return delete_db_cache_records(TRANSIENT_POPULAR_PREFIX);
  }
}
endif;

//ブログカードキャシュの削除
if ( !function_exists( 'delete_blogcard_caches' ) ):
function delete_blogcard_caches(){
  if (is_user_administrator()) {
    return delete_db_cache_records(TRANSIENT_BLOGCARD_PREFIX);
  }
}
endif;

//AmazonAPIキャシュの削除
if ( !function_exists( 'delete_amazon_api_caches' ) ):
function delete_amazon_api_caches(){
  if (is_user_administrator()) {
    return delete_db_cache_records(TRANSIENT_AMAZON_API_PREFIX);
  }
}
endif;

//全てのキャッシュを削除
if ( !function_exists( 'delete_all_theme_caches' ) ):
function delete_all_theme_caches(){
  delete_sns_count_caches();
  delete_popular_entries_caches();
  delete_blogcard_caches();
  delete_amazon_api_caches();
}
endif;