<div class="mobile-menu">
  <!-- メニューボタン -->
  <div class="navi-menu-button">
      <input id="navi-menu-input" type="checkbox" class="display-none">
      <label id="navi-menu-open" for="navi-menu-input"><span></span></label>
      <label class="display-none" id="navi-menu-close" for="navi-menu-input"></label>
      <div id="navi-menu-content">
        <label class="navi-close-button" for="navi-menu-input"></label>
        <?php //ヘッダーナビ
        wp_nav_menu(
          array (
            //カスタムメニュー名
            'theme_location' => 'navi-header',
            //ul 要素に適用するCSS クラス名
            'menu_class' => 'menu-drawer',
            //コンテナを表示しない
            'container' => false,
            //カスタムメニューを設定しない際に固定ページでメニューを作成しない
            'fallback_cb' => false,
          )
        ); ?>
        <label class="navi-close-button" for="navi-menu-input"></label>
      </div>
  </div>
</div onclose="">