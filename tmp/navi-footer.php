<nav id="navi-footer" class="navi-footer">
  <div id="navi-footer-in" class="navi-footer-in">
    <?php wp_nav_menu(
      array (
        //カスタムメニュー名
        'theme_location' => 'navi-footer',
        //ul 要素に適用するCSS クラス名
        'menu_class' => 'menu-footer',
        //コンテナを表示しない
        'container' => false,
        //カスタムメニューを設定しない際に固定ページでメニューを作成しない
        'fallback_cb' => false,
        //出力されるulに対してidやclassを表示しない
        //'items_wrap' => '<ul>%3$s</ul>',
        //メニューの深さ
        'depth' => 1,
      )
    ); ?>
  </div>
</nav>