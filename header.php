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

    <?php // drop Google Analytics Here ?>
    <?php // end analytics ?>

  </head>

  <body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">

    <div id="container">

      <header id="header" class="header" role="banner" itemscope itemtype="http://schema.org/WPHeader">

        <div id="header-in" class="header-in wrap cf">
          <div class="header-top"><span class="tagline"><?php bloginfo('description') ?></span></div>

          <?php // to use a image just replace the bloginfo('name') with your img src and remove the surrounding <p> ?>
          <div id="logo" class="logo" itemscope itemtype="http://schema.org/Organization"><a href="<?php echo home_url(); ?>" class="site-name"><?php bloginfo('name'); ?></a></div>

          <?php // if you'd like to use the site description you can un-comment it below ?>
          <?php // bloginfo('description'); ?>


          <nav id="navi" class="navi" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
            <?php wp_nav_menu(array(
                       'container' => false,                           // remove nav container
                       'container_class' => 'menu cf',                 // class of container (should you choose to use it)
                       'menu' => __( 'The Main Menu', 'bonestheme' ),  // nav name
                       'menu_class' => 'nav top-nav cf',               // adding custom nav class
                       'theme_location' => 'main-nav',                 // where it's located in the theme
                       'before' => '',                                 // before the menu
                             'after' => '',                                  // after the menu
                             'link_before' => '',                            // before each link
                             'link_after' => '',                             // after each link
                             'depth' => 0,                                   // limit the depth of the nav
                       'fallback_cb' => ''                             // fallback function (if there is one)
            )); ?>

          </nav>

        </div>

      </header>

      <div id="content">

        <div id="content-in" class="content-in wrap cf">

            <main id="main" class="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">