<?php //AMP Lightboxが有効な時
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if(is_amp_image_zoom_effect_lightbox()): ?>
  <amp-image-lightbox id="amp-lightbox" layout="nodisplay"></amp-image-lightbox>
<?php endif; ?>
<?php if (is_jetpack_exist() && is_analytics()): ?>
  <amp-pixel src="<?php echo esc_url( jetpack_amp_build_stats_pixel_url() ); ?>"></amp-pixel>
<?php endif; ?>
