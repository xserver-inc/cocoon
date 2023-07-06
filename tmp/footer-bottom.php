<?php //フッターの最下部のテンプレート
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="footer-bottom<?php echo get_additional_footer_bottom_classes(); ?> cf">
  <div class="footer-bottom-logo">
    <?php generate_the_site_logo_tag(false); ?>
  </div>

  <div class="footer-bottom-content">
     <?php cocoon_template_part('tmp/navi-footer') ?>

    <div class="source-org copyright"><?php echo get_the_site_credit(); ?></div>
  </div>

</div>
