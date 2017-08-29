<!-- Navigation -->
<nav id="navi" class="navi cf" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
	<div id="navi-in" class="navi-in wrap cf">
    <?php wp_nav_menu(
      array (
        //カスタムメニュー名
        'theme_location' => 'header-navi',
        //コンテナを表示しない
        'container' => false,
        //カスタムメニューを設定しない際に固定ページでメニューを作成しない
        'fallback_cb' => false,
        //出力されるulに対してidやclassを表示しない
        'items_wrap' => '<ul>%3$s</ul>',
      )
    ); ?>
  </div><!-- /#navi-in -->
</nav>
<!-- /Navigation -->