<div class="mobile-menu-buttons">
  <!-- メニューボタン -->
  <div class="navi-menu-button menu-button">
    <input id="navi-menu-input" type="checkbox" class="display-none">
    <label id="navi-menu-open" class="menu-open" for="navi-menu-input">
    <div class="navi-menu-icon menu-icon"></div>
    <div class="navi-menu-caption menu-caption"><?php _e( 'メニュー', THEME_NAME ) ?></div>
    </label>
    <label class="display-none" id="navi-menu-close" for="navi-menu-input"></label>
    <div id="navi-menu-content" class="navi-menu-content menu-content">
      <label class="navi-menu-close-button menu-close-button" for="navi-menu-input"></label>
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
      <!-- <label class="navi-menu-close-button menu-close-button" for="navi-menu-input"></label> -->
    </div>
  </div>

  <!-- ホームボタン -->
  <div class="home-menu-button menu-button">
    <a href="<?php echo site_url(); ?>">
      <div class="home-menu-icon menu-icon"></div>
      <div class="home-menu-caption menu-caption"><?php _e( 'ホーム', THEME_NAME ) ?></div>
    </a>
  </div>

  <!-- 検索ボタン -->
  <div class="search-menu-button menu-button">
    <a href="<?php echo site_url(); ?>">
      <div class="search-menu-icon menu-icon"></div>
      <div class="search-menu-caption menu-caption"><?php _e( '検索', THEME_NAME ) ?></div>
    </a>
  </div>

  <?php if (!is_amp()): ?>
  <!-- トップボタン -->
  <div class="top-menu-button menu-button">
    <div class="top-menu-icon menu-icon"></div>
    <div class="top-menu-caption menu-caption"><?php _e( 'トップ', THEME_NAME ) ?></div>
  </div>
  <?php endif ?>

  <!-- サイドバーボタン -->
  <div class="sidebar-menu-button menu-button">
    <input id="sidebar-menu-input" type="checkbox" class="display-none">
    <label id="sidebar-menu-open" class="menu-open" for="sidebar-menu-input">
    <div class="sidebar-menu-icon menu-icon"></div>
    <div class="sidebar-menu-caption menu-caption"><?php _e( 'サイドバー', THEME_NAME ) ?></div>
    </label>
    <label class="display-none" id="sidebar-menu-close" for="sidebar-menu-input"></label>
    <div id="sidebar-menu-content" class="sidebar-menu-content menu-content">
      <label class="sidebar-menu-close-button menu-close-button" for="sidebar-menu-input"></label>
      <?php //サイドバー
      get_template_part('sidebar'); ?>
      <!-- <label class="sidebar-menu-close-button menu-close-button" for="sidebar-menu-input"></label> -->
    </div>
  </div>

</div onclose="">