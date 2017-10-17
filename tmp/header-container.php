<?php //ヘッダーエリア ?>
<div class="header-container">
  <div class="header-container-in<?php echo get_additional_header_container_classes(); ?>">
    <header id="header" class="header<?php echo get_additional_header_classes(); ?> cf" role="banner" itemscope itemtype="http://schema.org/WPHeader">

      <div id="header-in" class="header-in wrap cf">

        <?php //キャッチフレーズがヘッダー上部のとき
        if (is_tagline_position_header_top()) {
           get_template_part('tmp/header-tagline');
        } ?>

        <?php //ロゴタグの生成
        genelate_the_site_logo_tag(); ?>

        <?php //キャッチフレーズがヘッダー下部のとき
        if (is_tagline_position_header_bottom()) {
           get_template_part('tmp/header-tagline');
        } ?>

      </div>

    </header>

    <?php get_template_part('tmp/navi'); ?>
  </div><!-- /.header-container-in -->
</div><!-- /.header-container -->