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

    <?php // icons & favicons (for more: http://www.jonathantneal.com/blog/understand-the-favicon/) ?>
    <link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-touch-icon.png">
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">

    <?php // wordpress head functions ?>
    <?php wp_head(); ?>
    <?php // end of wordpress head ?>

  </head>

  <body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">

    <div id="container" class="container cf">
      <div class="header-container">
        <header id="header" class="header cf" role="banner" itemscope itemtype="http://schema.org/WPHeader">

          <div id="header-in" class="header-in wrap cf">
            <div class="header-top"><div class="tagline"><?php bloginfo('description') ?></div></div>

            <?php // to use a image just replace the bloginfo('name') with your img src and remove the surrounding <p> ?>
            <div id="logo" class="logo" itemscope itemtype="http://schema.org/Organization"><a href="<?php echo home_url(); ?>" class="site-name"><?php bloginfo('name'); ?></a></div>

            <?php // if you'd like to use the site description you can un-comment it below ?>
            <?php // bloginfo('description'); ?>

          </div>

        </header>

        <?php get_template_part('tmp/navi') ?>
      </div><!-- /.header-container -->

      <div id="content" class="content cf">

        <div id="content-in" class="content-in wrap cf">

            <main id="main" class="main<?php echo get_additional_main_classes(); ?>" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">