<?php //投稿日と更新日
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

$display_none = is_seo_date_type_none() && !is_amp() ? ' display-none' : null; ?>
<div class="date-tags<?php echo $display_none; ?>">

  <?php //投稿日と更新日タグの取得
  echo get_the_date_tags(); ?>

  <?php if (is_amp() && is_user_administrator()): ?>
    <span class="amp-back">
    <span class="fa fa-bolt" aria-hidden="true"></span> <a href="<?php the_permalink() ?>"><?php _e( '通常ページへ', THEME_NAME ) ?></a>
    </span>

  <?php endif ?>
</div>
