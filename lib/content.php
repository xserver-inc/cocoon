<?php //本文に関する処理

global $_THE_CONTENT_SCRIPTS;

//本文内のスクリプトをまとめて取得
add_filter('the_content', 'get_the_content_all_scripts', 9999);
if ( !function_exists( 'get_the_content_all_scripts' ) ):
function get_the_content_all_scripts($the_content) {
  global $_THE_CONTENT_SCRIPTS;
  if (preg_match_all('{<script(?!.*src=).*?>(.+?)</script>}is', $the_content, $m)) {
    $all_index = 0;
    $js_index = 1;
    $i = 0;
    foreach ($m[$all_index] as $script) {
      $js_code = $m[$js_index][$i];
      //本文中のスクリプトをまとめる
      $_THE_CONTENT_SCRIPTS .= $js_code;
      //まとめたスクリプトは取り除く
      $the_content = str_replace($script, '', $the_content);

      $i++;
    }
    $_THE_CONTENT_SCRIPTS = minify_js($_THE_CONTENT_SCRIPTS);
    //_v($_THE_CONTENT_SCRIPTS);
  }
  return $the_content;
}
endif;