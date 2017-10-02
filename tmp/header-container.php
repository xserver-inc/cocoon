<?php //ヘッダーエリア ?>
<div class="header-container">
  <div class="header-container-in<?php echo get_additional_header_container_classes(); ?>">
    <header id="header" class="header<?php echo get_additional_header_classes(); ?> cf" role="banner" itemscope itemtype="http://schema.org/WPHeader">

      <div id="header-in" class="header-in wrap cf">
        <div class="header-top"><div class="tagline"><?php bloginfo('description') ?></div></div>

        <?php //ロゴタグの生成
        genelate_the_site_logo_tag(); ?>

      </div>

    </header>

    <?php get_template_part('tmp/navi'); ?>
  </div><!-- /.header-container-in -->
</div><!-- /.header-container -->