<?php //フッターのアクセス解析
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (!is_amp()) : //AMPページでは除外 ?>
<?php //その他フッター様の解析コード
if (is_analytics() && $footer_tags = get_other_analytics_footer_tags()) {
  echo '<!-- Other Analytics -->'.PHP_EOL;
  echo $footer_tags.PHP_EOL;
  echo '<!-- /Other Analytics -->'.PHP_EOL;
}?>
<?php endif; //!is_amp() ?>
