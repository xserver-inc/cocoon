<?php //メインカラム追従領域
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

if ( is_active_sidebar( 'main-scroll' ) ) : ?>
<div id="main-scroll" class="main-scroll">
  <?php dynamic_sidebar( 'main-scroll' ); ?>
</div>
<?php endif; ?>
