<?php //寄付設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//寄付コードの取得
define('OP_PRIVILEGE_ACTIVATION_CODE', 'privilege_activation_code');
if ( !function_exists( 'get_privilege_activation_code' ) ):
function get_privilege_activation_code(){
  return get_theme_option(OP_PRIVILEGE_ACTIVATION_CODE);
}
endif;

function is_privilege_activation_code_available(){
  return is_standard_privilege_activation_code_available() || is_premium_privilege_activation_code_available() || is_moderator_privilege_activation_code_available() || is_trial_privilege_activation_code_available();
}

function is_standard_privilege_activation_code_available(){
  $code = trim(get_privilege_activation_code());
  return md5($code) === '7081aec2ac3fa8669a70df50335a4be8';
}

function is_premium_privilege_activation_code_available(){
  $code = trim(get_privilege_activation_code());
  return md5($code) === '5518f3a52e446b036749548114b1278e';
}

function is_moderator_privilege_activation_code_available(){
  $code = trim(get_privilege_activation_code());
  return md5($code) === '0cd7eedf451181910507e526dc485138';
}

function is_trial_privilege_activation_code_available(){
  $code = trim(get_privilege_activation_code());
  return md5($code) === 'e82a4cd24a36907aa0c171dcaa5aa8cc';
}

