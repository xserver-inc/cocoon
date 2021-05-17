<?php //寄付設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//寄付コードの取得
define('OP_PRIVILEGE_ACTIVATION_CODE', 'get_privilege_activation_code');
if ( !function_exists( 'get_privilege_activation_code' ) ):
function get_privilege_activation_code(){
  return get_theme_option(OP_PRIVILEGE_ACTIVATION_CODE);
}
endif;

function is_privilege_activation_code_available(){
  $code = trim(get_privilege_activation_code());
  return md5($code) === '7081aec2ac3fa8669a70df50335a4be8';
}


