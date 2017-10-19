<?php
//外部のphpからWordpress のAPIを扱う
require_once('../../../../../wp-load.php');
require_once('../_defins.php');
//管理者権限を持っているログインユーザーかどうか
if (is_user_logged_in() && current_user_can('manage_options')) {
  global $wpdb;
  $option_name = 'theme_mods_'.THEME_NAME;
  $res = $wpdb->get_row("
    SELECT * FROM {$wpdb->options}
    WHERE option_name = '{$option_name}';
  ");
  $text_file   = THEME_NAME.'_settings.txt';
  if ($res && $res->option_value) {
  $text_buffer = $res->option_value;
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $text_file);
    header('Content-Length:' . strlen($text_buffer));
    header('Pragma: no-cache');
    header('Cache-Control: no-cache');
    echo $text_buffer;
  }
}
exit;

?>