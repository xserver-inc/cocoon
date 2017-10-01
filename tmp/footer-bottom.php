<?php //フッターの最下部のテンプレート ?>
<div class="footer-bottom cf">
  <div class="footer-bottom-logo">
    <?php genelate_the_site_logo_tag(false); ?>
  </div>

  <div class="footer-bottom-content">
     <?php get_template_part('tmp/navi-footer') ?>

    <div class="source-org copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>.</div>
  </div>

</div>