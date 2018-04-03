<?php //その他設定に必要な定数や関数

//簡単SSL対応
define('OP_EASY_SSL_ENABLE', 'easy_ssl_enable');
if ( !function_exists( 'is_easy_ssl_enable' ) ):
function is_easy_ssl_enable(){
  return get_theme_option(OP_EASY_SSL_ENABLE);
}
endif;

//ファイルシステム認証を有効にする
define('OP_REQUEST_FILESYSTEM_CREDENTIALS_ENABLE', 'request_filesystem_credentials_enable');
if ( !function_exists( 'is_request_filesystem_credentials_enable' ) ):
function is_request_filesystem_credentials_enable(){
  return get_theme_option(OP_REQUEST_FILESYSTEM_CREDENTIALS_ENABLE);
}
endif;

//Simplicityから設定情報の移行
define('OP_MIGRATE_FROM_SIMPLICITY', 'migrate_from_simplicity');
if ( !function_exists( 'is_migrate_from_simplicity' ) ):
function is_migrate_from_simplicity(){
  return get_theme_option(OP_MIGRATE_FROM_SIMPLICITY);
}
endif;