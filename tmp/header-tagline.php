<?php //キャッチフレーズ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<?php if (is_tagline_visible()): ?>
<div class="tagline" itemprop="alternativeHeadline"><?php bloginfo('description') ?></div>
<?php endif ?>
