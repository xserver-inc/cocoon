<?php //リストの投稿が見つからなかったら
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
//見つからない時のメッセージ
$message =  __( '投稿が見つかりませんでした。', THEME_NAME );
$message = apply_filters('posts_not_found_message', $message);
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="posts-not-found">
  <h2>NOT FOUND</h2>
  <p><?php echo $message; ?></p>
</div>
