<?php //ヘッダーエリア ?>
<div class="header-container">
  <header id="header" class="header cf" role="banner" itemscope itemtype="http://schema.org/WPHeader">

    <div id="header-in" class="header-in wrap cf">
      <div class="header-top"><div class="tagline"><?php bloginfo('description') ?></div></div>
      <?php //サイト名の見出し
      $tag = 'div';
      if (!is_singular()) {
        $tag = 'h1';
      }
       ?>
      <<?php echo $tag; ?> id="logo" class="logo" itemscope itemtype="http://schema.org/Organization"><a href="<?php echo home_url(); ?>" class="site-name"><?php bloginfo('name'); ?></a></<?php echo $tag; ?>>

    </div>

  </header>

  <?php get_template_part('tmp/navi'); ?>
</div><!-- /.header-container -->