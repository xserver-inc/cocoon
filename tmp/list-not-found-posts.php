<?php //リストの投稿が見つからなかったら
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="posts-not-found">
  <h2>NOT FOUND</h2>
  <p><?php echo __( '投稿が見つかりませんでした。', THEME_NAME );//見つからない時のメッセージ ?></p>
</div>
