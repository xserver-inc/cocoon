<?php //検索フォーム
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (!is_amp() || !is_ssl()): ?>
<form class="search-box input-box" method="get" action="<?php echo home_url('/'); ?>">
<?php else: ?>
<form class="amp-form search-box" method="get" action="<?php echo home_url('/'); ?>">
<?php endif ?>
  <input type="text" placeholder="<?php _e( 'サイト内を検索', THEME_NAME ) ?>" name="s" class="search-edit" aria-label="input">
  <button type="submit" class="search-submit" aria-label="button"><span class="fa fa-search" aria-hidden="true"></span></button>
</form>
