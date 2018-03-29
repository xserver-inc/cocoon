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