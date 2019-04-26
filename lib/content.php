<?php //本文に関する処理
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

global $_THE_CONTENT_SCRIPTS;

//本文内のスクリプトをまとめて取得
add_filter('the_content', 'get_the_content_all_scripts', 9999);
if ( !function_exists( 'get_the_content_all_scripts' ) ):
function get_the_content_all_scripts($the_content) {
  global $_THE_CONTENT_SCRIPTS;

  if (is_footer_javascript_enable() &&
      apply_filters('get_the_content_all_scripts', true) &&
      preg_match_all('{<script.*?>(.*?)</script>}is', $the_content, $m)) {
    $all_index = 0;
    $js_index = 1;
    $i = 0;
    foreach ($m[$all_index] as $script) {
      $is_exclude =
        ////Buddypressページでは除外
        is_buddypress_page() ||
        //WordPressのプレイリストなど
        includes_string($script, 'type="application/json"') ||
        //Amazonアソシエイト
        includes_string($script, 'amzn_assoc') ||
        includes_string($script, 'amazon-adsystem.co') ||
        //Googleトレンド埋め込み
        includes_string($script, 'ssl.gstatic.com') ||
        includes_string($script, 'trends.google.co') ||
        //もしもモーションウィジェット
        includes_string($script, 'MafRakutenWidgetParam') ||
        includes_string($script, '/rakuten/widget.js') ||
        includes_string($script, 'MoshimoAffiliate');
        $is_exclude = apply_filters('cocoon_exclude_script_movement_from_content', $is_exclude, $script);
      //除外設定
      if ($is_exclude) {
        continue;
      }
      $js_code = $m[$js_index][$i];
      if (trim($js_code)) {
        //本文中のスクリプトをまとめる
        $_THE_CONTENT_SCRIPTS .= $js_code;
        //まとめたスクリプトは取り除く
        $the_content = str_replace($script, '', $the_content);
      }

      $i++;
    }
    $_THE_CONTENT_SCRIPTS = minify_js($_THE_CONTENT_SCRIPTS);
    //_v($_THE_CONTENT_SCRIPTS);
  }
  return $the_content;
}
endif;
