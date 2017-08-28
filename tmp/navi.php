<!-- Navigation -->
<nav id="navi" class="navi cf" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
	<div id="navi-in" class="navi-in wrap cf">
    <?php wp_nav_menu(
      array (
        'theme_location' => 'header-navi',
        // 'menu_class' => '',
        // 'menu_id' => '',
        'container' => false,
        // 'container_class' => 'menu',
        'fallback_cb' => false,
        'items_wrap' => '<ul>%3$s</ul>',
      )
    ); ?>
  </div><!-- /#navi-in -->
</nav>
<!-- /Navigation -->