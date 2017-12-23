<?php //カラム設定に必要な定数や関数

//404ページメッセージ
define('OP_404_PAGE_MESSAGE', '404_page_message');
if ( !function_exists( 'get_404_page_message' ) ):
function get_404_page_message(){
  return stripslashes_deep(get_theme_option(OP_404_PAGE_MESSAGE, __( 'お探しのページは見つかりませんでした。', THEME_NAME )));
}
endif;