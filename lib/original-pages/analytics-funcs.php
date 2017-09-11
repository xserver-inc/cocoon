<?php //アクセス解析設定に必要な定数や関数

//Google AnalyticsのトラッキングID
define('OP_GOOGLE_ANALYTICS_TRACKING_ID', 'google_analytics_tracking_id');
if ( !function_exists( 'get_google_analytics_tracking_id' ) ):
function get_google_analytics_tracking_id(){
  return get_option(OP_GOOGLE_ANALYTICS_TRACKING_ID);
}
endif;

//Google Search ConsoleのID
define('OP_GOOGLE_SEARCH_CONSOLE_ID', 'google_search_console_id');
if ( !function_exists( 'get_google_search_console_id' ) ):
function get_google_search_console_id(){
  return get_option(OP_GOOGLE_SEARCH_CONSOLE_ID);
}
endif;

//PtengineのID
define('OP_PTENGINE_TRACKING_ID', 'ptengine_tracking_id');
if ( !function_exists( 'get_ptengine_tracking_id' ) ):
function get_ptengine_tracking_id(){
  return get_option(OP_PTENGINE_TRACKING_ID);
}
endif;
