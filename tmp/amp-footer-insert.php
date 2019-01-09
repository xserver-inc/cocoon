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
