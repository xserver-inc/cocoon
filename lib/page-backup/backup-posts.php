<?php //リセットの実行
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

// 検証済みアップロードだけを復元処理へ渡す入口
$restore_success = restore_theme_settings_from_upload($_FILES['settings'] ?? null);
