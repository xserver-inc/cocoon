<?php
///////////////////////////////////////
// ヘッダーのカスタムフィールドを挿入
// ヘッダーで呼び出すCSSやスクリプトなど
// カスタムフィールドに「head_custom」と入力することで使用。
///////////////////////////////////////
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( is_singular() ){//投稿・固定ページの場合
  global $post;
  if (isset($post->ID)) {
    $head_custom = get_post_meta($post->ID, 'head_custom', true);
    if ( $head_custom ) {
      echo replace_directory_uri($head_custom);
    }
  }
}
?>
