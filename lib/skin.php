<?php //スキンに
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

$php_file = url_to_local(get_skin_php_url());
if (file_exists($php_file)) {
  require_once $php_file;
}