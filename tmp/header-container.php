<?php //ヘッダーエリア ?>
<div class="header-container">
  <header id="header" class="header cf" role="banner" itemscope itemtype="http://schema.org/WPHeader">

    <div id="header-in" class="header-in wrap cf">
      <div class="header-top"><div class="tagline"><?php bloginfo('description') ?></div></div>

      <?php //ロゴタグの生成
      genelate_the_site_logo_tag(); ?>

    </div>

  </header>

  <?php get_template_part('tmp/navi'); ?>
</div><!-- /.header-container -->