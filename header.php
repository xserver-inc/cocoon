<!doctype html>
<html <?php language_attributes(); ?>>

  <head>
    <meta charset="utf-8">

    <?php // force Internet Explorer to use the latest rendering engine available ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <?php // mobile meta (hooray!) ?>
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <?php wp_head(); ?>

  </head>

  <body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">

    <?php //アクセス解析ヘッダータグの取得
    get_template_part('tmp/analytics-header'); ?>

    <div id="container" class="container cf">
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

      <div id="content" class="content cf">

        <div id="content-in" class="content-in wrap cf">

            <main id="main" class="main<?php echo get_additional_main_classes(); ?>" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">