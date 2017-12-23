<?php //テーマ情報取得

if ( !function_exists( 'get_theme_info' ) ):
function get_theme_info($file){
  if (file_exists($file)) {
    // ob_start();
    // require_once($file);
    // $text = ob_get_clean();
    $text = get_file_contents($file);
    if ($text) {
      $info = array();
      if (preg_match('/Theme Name: *(.+)/i', $text, $m)) {
        $info['theme_name'] = $m[1];
      }
      if (preg_match('/Version: *(.+)/i', $text, $m)) {
        $info['version'] = $m[1];
      }

      return $info;
    }
  }
}
endif;