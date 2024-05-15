<?php
/**
 * Cocoon設定のカスタマイズ
 */
class Skin_Silk_Functions {
  //インスタンス保持
  public static $instance = false;

  //サイトアイコンフォント
  private $fa = '';

  //スキンカラー初期値
  const KEY_COLOR  = '#e57373';
  const TEXT_COLOR = '#ffffff';

  //ブロックスタイル一覧
  const BLOCK_STYLES = [
    [
      'name'       => 'core/image',
      'properties' => [
        'name'  => 'shadow',
        'label' => '影'
      ]
    ],
    [
      'name'       => 'core/group',
      'properties' => [
        'name'  => 'panel',
        'label' => 'パネル'
      ]
    ],
    [
      'name'       => 'core/group',
      'properties' => [
        'name'  => 'compare',
        'label' => '比較表（アイコンリスト）'
      ]
    ],
    [
      'name'       => 'core/group',
      'properties' => [
        'name'  => 'toggle-accordion',
        'label' => 'アコーディオン（トグルボックス）'
      ]
    ],
    [
      'name'       => 'core/columns',
      'properties' => [
        'name'  => 'mobile-columns',
        'label' => 'モバイル'
      ]
    ],
    [
      'name'       => 'core/separator',
      'properties' => [
        'name'  => 'cut-line',
        'label' => '切り取り線'
      ]
    ],
    [
      'name'       => 'core/table',
      'properties' => [
        'name'  => 'center-cell',
        'label' => '中央寄せ'
      ]
    ],
    [
      'name'       => 'core/table',
      'properties' => [
        'name'  => 'horizon',
        'label' => '水平'
      ]
    ],
    [
      'name'       => 'core/table',
      'properties' => [
        'name'  => 'color-head',
        'label' => 'キーカラー'
      ]
    ],
    [
      'name'       => 'core/list',
      'properties' => [
        'name'  => 'link',
        'label' => 'リンク'
      ]
    ],
    [
      'name'       => 'cocoon-blocks/blogcard',
      'properties' => [
        'name'  => 'normal-card',
        'label' => '横長'
      ]
    ],
    [
      'name'       => 'cocoon-blocks/blogcard',
      'properties' => [
        'name'  => 'columns-card',
        'label' => 'カラム'
      ]
    ],
    [
      'name'       => 'cocoon-blocks/blogcard',
      'properties' => [
        'name'  => 'text',
        'label' => 'テキスト'
      ]
    ],
    [
      'name'       => 'cocoon-blocks/toggle-box-1',
      'properties' => [
        'name'  => 'faq',
        'label' => 'よくある質問'
      ]
    ],
    [
      'name'       => 'cocoon-blocks/iconlist-box',
      'properties' => [
        'name'  => 'no-icon',
        'label' => 'アイコンなし'
      ]
    ]
  ];

  //カラーパレット
  const PALETTE = [
    'red'           => '#ef5350',
    'pink'          => '#f48fb1',
    'purple'        => '#ce93d8',
    'deep'          => '#9575cd',
    'indigo'        => '#5c6bc0',
    'blue'          => '#42a5f5',
    'light-blue'    => '#29b6f6',
    'cyan'          => '#00acc1',
    'teal'          => '#009688',
    'green'         => '#4caf50',
    'light-green'   => '#8bc34a',
    'lime'          => '#c0ca33',
    'yellow'        => '#ffd600',
    'amber'         => '#ffc107',
    'orange'        => '#ffa726',
    'deep-orange'   => '#ff7043',
    'brown'         => '#8d6e63',
    'grey'          => '#90a4ae',
    'black'         => '#424242',
    'watery-blue'   => '#e3f2fd',
    'watery-yellow' => '#fff8e1',
    'watery-red'    => '#ffebee',
    'watery-green'  => '#e8f5e9'
  ];

  //パターン名
  const PATTERNS = [
    'comparison-table',
    'fullwide-columns',
    'linklist-box'
  ];

  //隠しフィールド名
  const HIDDEN = 'silk_submit_hidden';

  //初期値・フック一覧
  private function __construct() {
    //ダークカラー定義
    if (!defined('SILK_DARK')) {
      define('SILK_DARK', ['rgba(255, 255, 255, 0.2)', 'rgba(255, 255, 255, 0.8)']);
    }

    //ダークモード設定
    if (!defined('SILK_SWITCH')) {
      define('SILK_SWITCH', false);
    }

    //見出しナンバリング
    if (!defined('SILK_COUNTER')) {
      define('SILK_COUNTER', true);
    }

    //縦型ブログカード
    if (!defined('SILK_BLOGCARD')) {
      define('SILK_BLOGCARD', true);
    }

    //カラー・スタイル関連のフック
    add_action('after_setup_theme', [$this, 'setup_skin']);
    add_filter('get_editor_key_color', [$this, 'editor_color']);
    add_filter('block_editor_color_palette_colors', [$this, 'palette_colors']);
    add_action('get_template_part_tmp/css-custom', [$this, 'css_custom']);
    add_action('cocoon_settings_after_save', [$this, 'delete_options']);
    add_action('get_header', [$this, 'icon_settings']);
    add_filter('menu_description_walker', [$this, 'navi_header']);
    add_filter('wp_nav_menu_args', [$this, 'navi_footer']);
    add_filter('wp_tag_cloud', [$this, 'tag_cloud'], 11);
    add_filter('the_author_box_name', [$this, 'author_box']);

    //ブログカード
    add_filter('get_blogcard_thumbnail_size', [$this, 'blogcard_size']);
    add_filter('external_blogcard_amazon_image_width', [$this, 'blogcard_width']);
    add_filter('external_blogcard_image_width', [$this, 'blogcard_width']);
    add_filter('external_blogcard_amazon_image_height', [$this, 'blogcard_height']);
    add_filter('external_blogcard_image_height', [$this, 'blogcard_height']);

    //ブロックカスタマイズ
    add_action('enqueue_block_editor_assets', [$this, 'editor_assets'], 11);
    add_action('admin_menu', [$this, 'reusable_menu']);
    add_filter('image_size_names_choose', [$this, 'image_size']);
    add_action('init', [$this, 'block_pattern']);
    add_filter('render_block_core/heading', [$this, 'blocks_heading'], 10, 2);
    add_filter('render_block_core/group', [$this, 'blocks_group'], 10, 2);
    add_filter('render_block_cocoon-blocks/toggle-box-1', [$this, 'blocks_toggle'], 10, 2);
    add_filter('render_block_cocoon-blocks/faq', [$this, 'blocks_faq'], 10, 2);
    add_filter('cocoon_json_ld_faq_visible', [$this, 'cocoon_faq']);
    add_action('wp_footer', [$this, 'footer_script']);

    //AMP
    add_filter('amp_skin_css', [$this, 'amp_css']);
    add_action('get_template_part_tmp/amp-header', [$this, 'amp_skin']);

    //スキン設定
    add_action('admin_menu', [$this, 'option_setting'], 11);

    //コードコピー
    add_action('get_template_part_tmp/footer-custom-field', [$this, 'clipboard_js']);

    //ダークモード
    add_filter('get_template_part_tmp/button-go-to-top', [$this, 'dark_mode']);
    add_action('wp_enqueue_scripts', [$this, 'js_cookie']);
    add_filter('body_class', [$this, 'cookie_class'], 20);
  }

  //インスタンス生成
  public static function instance() {
    if (!self::$instance) {
      self::$instance = new self;
    }
    return self::$instance;
  }

  //テーマ読み込み後
  public function setup_skin() {
    //サイトアイコンフォント設定
    $this->fa = $this->fa_class();

    if (is_gutenberg_editor_enable()) {
      //ブロックスタイル
      $blockstyles = apply_filters('silk_block_styles', self::BLOCK_STYLES);
      foreach ($blockstyles as $blockstyle) {
        register_block_style($blockstyle['name'], $blockstyle['properties']);
      }

      //ブログカードスタイル削除
      if (!SILK_BLOGCARD) {
        unregister_block_style('cocoon-blocks/blogcard', 'normal-card');
        unregister_block_style('cocoon-blocks/blogcard', 'columns-card');
        unregister_block_style('cocoon-blocks/blogcard', 'text');
      }
    }

    //公式ブロックパターン削除
    remove_theme_support('core-block-patterns');
  }

  //アイコンバージョン
  private function fa_class() {
    return is_site_icon_font_font_awesome_4() ? 'fa': 'fas';
  }

  //エディタ用キーカラー
  public function editor_color($color) {
    return get_theme_mod(OP_SITE_KEY_COLOR) ?: self::KEY_COLOR;
  }

  //エディター用CSS
  public function palette_colors($colors) {
    foreach (self::PALETTE as $slug => $color) {
      $key = array_search($slug, array_column($colors, 'slug'));
      if ($key !== false) {
        $colors[$key]['color'] = $color;
      }
    }
    return $colors;
  }

  //カスタムCSS
  public function css_custom() {
    $color     = get_site_key_color() ?: self::KEY_COLOR;
    $textcolor = get_site_key_text_color() ?: self::TEXT_COLOR;

    //サイトキーカラー
    echo '.tagline,
    #index-tab-1:checked ~ .index-tab-buttons .index-tab-button[for="index-tab-1"],
    #index-tab-2:checked ~ .index-tab-buttons .index-tab-button[for="index-tab-2"],
    #index-tab-3:checked ~ .index-tab-buttons .index-tab-button[for="index-tab-3"],
    #index-tab-4:checked ~ .index-tab-buttons .index-tab-button[for="index-tab-4"],
    .cat-label,
    .pagination .current,
    .article h2 span::after,
    blockquote p:first-of-type::before,
    .blogcard-label,
    .timeline-item::before,
    ol.toc-list > li::before,
    .sns-share-message,
    .author-widget-name,
    .go-to-top-button,
    .wp-calendar-table   [id$="today"],
    .mobile-footer-menu-buttons,
    .is-style-color-head th {
      background: '.$color.';
    }

    .slick-dots li button:before,
    .slick-dots li.slick-active button:before,
    .archive-title span,
    .article h4 > span::before,
    .article h5,
    .sidebar h2,
    .sidebar h3,
    ul.toc-list > li::before,
    .is-style-normal-card .blogcard-label,
    .search-form div.sbtn::after,
    .search-submit,
    .pager-post-navi a .iconfont,
    .pager-post-navi a.prev-next-home,
    .menu-drawer > li > a::before {
      color: '.$color.';
    }

    #index-tab-1:checked ~ .index-tab-buttons .index-tab-button[for="index-tab-1"],
    #index-tab-2:checked ~ .index-tab-buttons .index-tab-button[for="index-tab-2"],
    #index-tab-3:checked ~ .index-tab-buttons .index-tab-button[for="index-tab-3"],
    #index-tab-4:checked ~ .index-tab-buttons .index-tab-button[for="index-tab-4"],
    .pagination .current,
    .article h3,
    .sidebar h2,
    .sidebar h3,
    .toc,
    .author-widget-name:after {
      border-color: '.$color.';
    }';

    //サイトキーテキストカラー
    echo '.tagline,
    .cat-label,
    .pagination .current,
    blockquote p:first-of-type::before,
    .blogcard-label,
    ol.toc-list > li::before,
    .sns-share-message,
    .author-widget-name,
    .go-to-top-button,
    .go-to-top-button:hover,
    .wp-calendar-table   [id$="today"],
    .mobile-footer-menu-buttons .menu-icon,
    .mobile-footer-menu-buttons .menu-caption,
    .is-style-color-head th {
      color: '.$textcolor.';
    }';

    //カラーパレット追加
    $color_palette = get_cocoon_editor_color_palette_colors();
    foreach ($color_palette as $editor_color) {
      echo '.body .has-'.$editor_color['slug'].'-border-color .label-box-label,
      .wp-block-cover-image.has-'.$editor_color['slug'].'-background-color.has-background-dim,
      .wp-block-cover.has-'.$editor_color['slug'].'-background-color.has-background-dim,
      .has-'.$editor_color['slug'].'-background-color hr.is-style-cut-line::after,
      .has-'.$editor_color['slug'].'-background-color .iconlist-title {
        background-color: '.$editor_color['color'].';
      }

      .is-style-outline .wp-block-button__link.has-'.$editor_color['slug'].'-color,
      .wp-block-button__link.is-style-outline.has-'.$editor_color['slug'].'-color {
        color: '.$editor_color['color'].';
      }

      .has-'.$editor_color['slug'].'-background-color .sbs-stn .speech-balloon::after,
      .has-'.$editor_color['slug'].'-background-color .sbs-line .speech-balloon::after {
        border-right-color: '.$editor_color['color'].';
      }

      .has-'.$editor_color['slug'].'-background-color .sbs-stn.sbp-r .speech-balloon::after {
        border-left-color: '.$editor_color['color'].';
      }

      .has-'.$editor_color['slug'].'-background-color .recent-comment-content::after {
        border-bottom-color: '.$editor_color['color'].';
      }

      .has-'.$editor_color['slug'].'-background-color .marker,
      .has-'.$editor_color['slug'].'-background-color .marker-under,
      .has-'.$editor_color['slug'].'-background-color .marker-red,
      .has-'.$editor_color['slug'].'-background-color .marker-under-red,
      .has-'.$editor_color['slug'].'-background-color .marker-blue,
      .has-'.$editor_color['slug'].'-background-color .marker-under-blue {
        text-shadow: 1px 1px 2px '.$editor_color['color'].';
      }';
    }

    //ダークカラー対応
    $dark_white = SILK_DARK[0];
    $dark_black = SILK_DARK[1];
    echo '.a-wrap,
    .a-wrap:hover,
    .index-tab-buttons .index-tab-button,
    .page-numbers:not(.current):not(.dots):hover,
    .author-box,
    .toggle-button,
    .toggle-checkbox:checked ~ .toggle-content,
    div.search-form,
    .is-style-panel,
    .toc,
    .box-menu {
      background: '.$dark_white.';
    }

    .search-edit,
    input[type="text"],
    input[type="password"],
    input[type="date"],
    input[type="datetime"],
    input[type="email"],
    input[type="number"],
    input[type="search"],
    input[type="tel"],
    input[type="time"],
    input[type="url"],
    textarea,
    select {
      background: '.$dark_black.';
    }';

    //文字色
    $site_color = get_site_text_color() ?: '#484848';
    echo '.carousel .slick-arrow:before,
    .rating-number,
    ul.is-style-link li a::before,
    ol.is-style-link li a::before {
      color: '.$site_color.';
    }';

    //背景色
    $site_background = get_site_background_color() ?: 'var(--cocoon-custom-background-color, #fff)';
    echo 'hr.is-style-cut-line::after,
    .iconlist-title {
      background: '.$site_background.';
    }

    .info-list-item-content-link {
      --cocoon-black-color: '.$site_color.';
    }

    .speech-wrap,
    .sbs-line.sbp-r{
      --cocoon-custom-background-color: '.$site_background.';
      --cocoon-custom-text-color: inherit;
    }

    .body .speech-balloon::after {
      border-right-color: '.$site_background.';
    }
    .sbp-r .speech-balloon::after {
      border-left-color: '.$site_background.';
    }
    .sbs-line.sbp-r .speech-balloon::after {
      border-left-color: '.$site_background.';
    }

    .micro-balloon{
      --cocoon-custom-background-color: '.$site_background.';
      --cocoon-custom-text-color: inherit;
    }
    .micro-balloon:after {
      border-top-color: '.$site_background.';
    }
    .micro-bottom.micro-balloon:after {
      border-bottom-color: '.$site_background.';
    }

    /* カスタム色対応 */
    .speech-wrap,
    .toggle-box,
    .timeline-box,
    .iconlist-box,
    .faq-wrap,
    .caption-box,
    .tab-caption-box,
    .label-box,
    .micro-balloon,
    .micro-text{
      --cocoon-custom-text-color: '.$site_color.';
    }

    .sbs-line.sbp-r .speech-balloon:not(.has-background) {
      background-color: '.$site_background.';
    }

    .is-style-clip-box {
      --cocoon-white-color: '.$site_background.';
      background-color: var(--cocoon-white-color);
    }

    [class^="is-style-balloon-"], [class*=" is-style-balloon-"] {
      --cocoon-white-color: '.$site_background.';
      background: var(--cocoon-white-color);
      border: 1px solid var(--cocoon-box-border-color);
    }

    .timeline-title,
    .tab-caption-box-label,
    .caption-box-label{
      color: #484848;
    }

    .recent-comment-content::after {
      border-bottom-color: '.$site_background.';
    }

    .marker,
    .marker-under,
    .marker-red,
    .marker-under-red,
    .marker-blue,
    .marker-under-blue {
      text-shadow: 1px 1px 2px '.$site_background.';
    }';

    //スライドインサイドバー
    echo '.sidebar-menu-content {
      color: '.$site_color.';
      background: '.$site_background.';
    }';

    //ダークモード設定
    if (SILK_SWITCH) {
      echo 'body,
      .carousel .slick-arrow:before,
      .rating-number,
      ul.is-style-link li a::before,
      ol.is-style-link li a::before,
      hr.is-style-cut-line::after,
      .iconlist-title,
      .speech-balloon::after,
      .sbp-r .speech-balloon::after,
      .recent-comment-content::after,
      .marker,
      .marker-under,
      .marker-red,
      .marker-under-red,
      .marker-blue,
      .marker-under-blue,
      .sidebar-menu-content {
        transition: all 0.3s ease-out;
      }

      body.silk-darkmode {
        color: '.$site_background.';
      }

      body.public-page.silk-darkmode {
        background-color: '.$site_color.';
      }

      .silk-darkmode .carousel .slick-arrow:before,
      .silk-darkmode .rating-number,
      .silk-darkmode ul.is-style-link li a::before,
      .silk-darkmode ol.is-style-link li a::before {
        color: '.$site_background.';
      }

      .silk-darkmode hr.is-style-cut-line::after,
      .silk-darkmode .iconlist-title {
        background: '.$site_color.';
      }

      .silk-darkmode .speech-balloon::after {
        border-right-color: '.$site_color.';
      }

      .silk-darkmode .sbp-r .speech-balloon::after {
        border-left-color: '.$site_color.';
      }

      .silk-darkmode .recent-comment-content::after {
        border-bottom-color: '.$site_color.';
      }

      .silk-darkmode .marker,
      .silk-darkmode .marker-under,
      .silk-darkmode .marker-red,
      .silk-darkmode .marker-under-red,
      .silk-darkmode .marker-blue,
      .silk-darkmode .marker-under-blue {
        text-shadow: 1px 1px 2px '.$site_color.';
      }

      .silk-darkmode .sidebar-menu-content {
        color: '.$site_background.';
        background: '.$site_color.';
      }

      .silk-darkmode-button {
        position: fixed;
        left: 10px;
        bottom: 10px;
        line-height: 1;
        cursor: pointer;
      }

      .silk-darkmode-button i {
        display: block;
        font-size: 2em;
      }

      .silk-darkmode .toggle-box{
        --cocoon-custom-background-color: transparent;
      }

      .silk-darkmode .has-box-style,
      .silk-darkmode .has-border{
        --cocoon-box-border-color: #ddd;
      }

      .silk-darkmode .is-style-border-thin-and-thick,
      .silk-darkmode .is-style-border-radius-s-thin-and-thick,
      .silk-darkmode .is-style-border-radius-l-thin-and-thick {
        border-color: rgba(255, 255, 255, 0.1);
      }

      .silk-darkmode .is-style-light-background-box{
        background-color: rgba(255, 255, 255, 0.1);
      }

      .silk-darkmode .is-style-stripe-box {
        background-image: repeating-linear-gradient(-45deg, #333 0, #333 3px, transparent 3px, transparent 6px);
      }

      .silk-darkmode .is-style-checkered-box {
        background-image: linear-gradient(90deg, rgba(232, 238, 236, 0.1) 50%, transparent 50%), linear-gradient(rgba(234, 236, 238, 0.1) 50%, transparent 50%);
      }

      .silk-darkmode .is-style-stitch-box{
        background-color: rgba(255, 255, 255, 0.2);
        box-shadow: 0px 0px 0px 10px rgba(255, 255, 255, 0.2);
      }

      .silk-darkmode .is-style-border-top-box {
        box-shadow: 0 3px 5px rgba(255, 255, 255, 0.22);
      }

      .silk-darkmode .is-style-border-left-box {
        box-shadow: 0px 2px 3px rgba(255, 255, 255, 0.33);
      }';
    }

    //リンク色
    $link_color = get_site_link_color() ?: '#1967d2';
    echo 'a:hover,
    .comment-btn,
    .comment-btn:hover,
    .is-style-text .a-wrap,
    .is-style-text .a-wrap:hover {
      color: '.$link_color.';
    }

    input[type="submit"] {
      background: '.$link_color.';
    }';

    //モバイルボタン
    $mobile_background = get_header_background_color() ?: get_header_container_background_color() ?: '#fff';
    $mobile_color      = get_header_text_color() ?: get_header_container_text_color() ?: '#484848';
    echo '.mobile-header-menu-buttons {
      color: '.$mobile_color.';
      background: '.$mobile_background.';
    }';

    //スライドインメニュー
    $menu_background = get_global_navi_background_color() ?: get_header_container_background_color() ?: '#fff';
    $menu_color      = get_global_navi_text_color() ?: get_header_container_text_color() ?: '#484848';
    echo '.navi-menu-content,
    .menu-drawer a,
    .menu-drawer a:hover {
      color: '.$menu_color.';
      background: '.$menu_background.';
    }';

    //ボックスメニュー
    echo '.box-menus .box-menu:hover {
      box-shadow: inset 2px 2px 0 0 '.$color.', 2px 2px 0 0 '.$color.', 2px 0 0 0 '.$color.', 0 2px 0 0 '.$color.';
    }

    .box-menus .box-menu-icon {
      color: '.$color.';
    }';

    //アイコン
    if (is_site_icon_font_font_awesome_4()) {
      $font   = 'font-family: FontAwesome;';
      $weight = '';
    } else {
      $font   = 'font-family: "Font Awesome 5 Free";';
      $weight = 'font-weight: 900;';
    }
    echo '.article h4 > span::before,
    blockquote p:first-of-type::before,
    ul.is-style-link li a::before,
    ol.is-style-link li a::before,
    .widget_recent_entries ul li a::before,
    .widget_categories ul li a::before,
    .widget_archive ul li a::before,
    .widget_pages ul li a::before,
    .widget_meta ul li a::before,
    .widget_rss ul li a::before,
    .widget_nav_menu ul li a::before,
    .wp-block-group ul li a::before,
    .comment-btn::before,
    .menu-drawer a::before,
    .is-style-faq .toggle-button::after {
      '.$font.$weight.'
    }';

    //タグライン
    if (is_tagline_visible()) {
      echo '.header-container {
        padding-top: 20px;
      }

      .header-container.fixed-header {
        padding-top: 0;
        border-top: 3px solid '.$color.';
      }';
    }

    //見出しカウント
    if (SILK_COUNTER) {
      echo '.entry-content {
        counter-reset: h2;
      }

      .entry-content h2 > span::before {
        content: counter(h2, decimal) ". ";
        counter-increment: h2;
      }';
    }

    //縦型ブログカード
    if (!SILK_BLOGCARD) {
      echo '.blogcard-wrap {
        max-width: none;
      }

      .blogcard {
        padding: 0.6em;
      }

      .blogcard-label {
        top: -1em;
        right: auto;
        left: 1em;
        padding: 0 0.6em;
        font-size: 13px;
        font-weight: normal;
        line-height: 1.6;
        border-radius: 3px;
      }

      .blogcard-thumbnail {
        float: left;
        width: 160px;
        margin-top: 3px;
      }

      .blogcard-thumb-image {
        border-radius: 0;
      }

      .blogcard-content {
        padding: 0;
        margin-left: 170px;
        max-height: 140px;
        min-height: 100px;
      }

      .blogcard-title {
        margin-bottom: 0.4em;
      }

      .blogcard-footer {
        padding: 0.3em 0 0;
        font-size: 16px;
        opacity: 1;
      }

      .blogcard-site {
        display: flex;
      }

      .blogcard-favicon img {
        vertical-align: inherit;
      }

      .ib-right .blogcard-thumbnail,
      .eb-right .blogcard-thumbnail {
        float: right;
        margin-left: 0.6em;
      }

      .ib-right .blogcard-content,
      .eb-right .blogcard-content {
        margin-right: 170px;
      }';
    }

    //コピーボタン
    $group_margin = get_entry_content_margin_hight();
    if ($this->is_highlight()) {
      global $_MOBILE_COPY_BUTTON;
      $_MOBILE_COPY_BUTTON = true;

      echo '.article pre.wp-block-code {
        margin-bottom: 0;
      }

      .article .code-wrap {
        margin-bottom: '.$group_margin.'em;
      }

      .code-wrap {
        position: relative;
      }

      .code-wrap .code-copy {
        transition: all 0.3s ease-out;
      }

      .code-wrap:hover .code-copy {
        opacity: 1;
      }

      .code-copy {
        position: absolute;
        top: 0.5em;
        right: 1.25em;
        padding: 0.2em 1.5em;
        color: '.$textcolor.';
        background: '.$color.';
        font-size: 0.8em;
        border: 0;
        border-radius: 2px;
        opacity: 0;
        outline: none;
        box-shadow: 0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
        z-index: 2;
        cursor: pointer;
      }';
    }

    //よくある質問
    $faq_margin = (string)((float)$group_margin - 0.5);
    echo '.toggle-wrap.is-style-faq + .toggle-wrap.is-style-faq {
      margin-top: -'.$faq_margin.'em;
    }';

    //幅広
    $group_padding = get_main_column_padding() ?: '29';
    echo '.entry-content .alignwide:not(.wp-block-table) {
      margin-left: -'.$group_padding.'px;
      margin-right: -'.$group_padding.'px;
    }';

    //全幅
    if (!is_admin() && !is_the_page_sidebar_visible()) {
      $group_width  = is_clumns_changed() ? get_site_wrap_width() : '1256';

      echo 'html {
        overflow-x: hidden;
      }

      .content,
      .footer {
        margin-top: 0;
      }

      .alignfull {
        position: relative;
        left: calc(50% - 50vw);
        right: calc(50% - 50vw);
        max-width: 100vw;
        width: 100vw;
      }

      .main figure.wp-block-table.alignfull {
        max-width: 100vw;
        width: 100vw;
      }

      .wp-block-cover.alignfull {
        width: 100vw;
      }

      .alignfull > .wp-block-group__inner-container {
        width: '.$group_width.'px;
        padding: '.$group_margin.'em '.$group_padding.'px;
        margin: 0 auto;
      }

      @media screen and (max-width: 1260px) {
        .alignfull > .wp-block-group__inner-container {
          width: auto;
        }
      }

      @media screen and (max-width: 834px) {
        .alignfull > .wp-block-group__inner-container {
          padding: '.$group_margin.'em 24px;
        }
      }';
    }

    //翻訳
    echo '.date-tags .post-update::before {
      content: "'.__('更新日', THEME_NAME).' :";
    }

    .date-tags .post-date::before {
      content: "'.__('投稿日', THEME_NAME).' :";
    }';

    global $_THEME_OPTIONS;
    $_THEME_OPTIONS[OP_SITE_KEY_COLOR] = $_THEME_OPTIONS[OP_SITE_KEY_TEXT_COLOR] = '';
  }

  //設定のみリセット
  public function delete_options() {
    global $_THEME_OPTIONS;
    unset($_THEME_OPTIONS[OP_SITE_KEY_COLOR], $_THEME_OPTIONS[OP_SITE_KEY_TEXT_COLOR]);
  }

  //FontAwesome用アイコン設定
  public function icon_settings() {
    global $_THEME_OPTIONS;

    //目次タイトル
    $_THEME_OPTIONS[OP_TOC_TITLE] = '<i class="'.$this->fa.' fa-list-ul"></i>'.get_toc_title();

    //シェアボタン
    if ($message = get_sns_bottom_share_message()) {
      $_THEME_OPTIONS[OP_SNS_BOTTOM_SHARE_MESSAGE] = '<i class="'.$this->fa.' fa-share"></i>'.$message;
    }

    //通知
    if ($notice = get_notice_area_message()) {
      $_THEME_OPTIONS[OP_NOTICE_AREA_MESSAGE] = '<span class="notice-message"><i class="'.$this->fa.' fa-arrow-circle-right"></i>'.$notice.'</span>';
    }
  }

  //ダークモード
  public function dark_mode() {
    if (SILK_SWITCH && !is_amp()) {
      echo '<div class="silk-darkmode-button"><i class="fa fa-adjust" aria-hidden="true"></i></div>';
    }
  }

  //Cookie
  public function js_cookie() {
    if (SILK_SWITCH && !is_amp()) {
      wp_enqueue_script('js-cookie', '//cdn.jsdelivr.net/npm/js-cookie@rc/dist/js.cookie.min.js', [], false, true);

      $data = '(function ($) {
        $(".silk-darkmode-button").click(function (event) {
          event.preventDefault();
          $("body").toggleClass("silk-darkmode");

          if (Cookies.get("silk-darkmode") === undefined) {
            Cookies.set("silk-darkmode", 1);
          } else {
            Cookies.remove("silk-darkmode");
          }
        });
      })(jQuery);';
      wp_add_inline_script('js-cookie', minify_js($data));
    }
  }

  //Cookie判定
  public function cookie_class($classes) {
    if (SILK_SWITCH && !is_amp() && array_key_exists('silk-darkmode', $_COOKIE)) {
      $classes[] = 'silk-darkmode';
    }
    return $classes;
  }

  //ヘッダーメニュー
  public function navi_header($output) {
    return str_replace(['fa-angle-down', 'fa-angle-right'], ['fa-caret-down', 'fa-caret-right'], $output);
  }

  //フッターナビメニュー
  public function navi_footer($args) {
    if ($args['theme_location'] === NAV_MENU_FOOTER) {
      $args['link_before'] = '<i class="'.$this->fa.' fa-caret-right"></i>';
    }
    return $args;
  }

  //タグクラウド
  public function tag_cloud($output) {
    return str_replace('fa-tag', 'fa-hashtag', $output);
  }

  //プロフィールボックス
  public function author_box($name) {
    return '<i class="'.$this->fa.' fa-at"></i>'.$name;
  }

  //ブログカードサムネイル
  public function blogcard_size($size) {
    return THUMB320;
  }

  public function blogcard_width($width) {
    return THUMB320WIDTH;
  }

  public function blogcard_height($height) {
    return THUMB320HEIGHT;
  }

  //エディター用JS・CSS
  public function editor_assets() {
    $file = str_ireplace('style.css', 'gutenberg.js', get_skin_url());
    $path = url_to_local($file);

    if ($file && file_exists($path)) {
      wp_enqueue_script(
        'silk-gutenberg',
        $file,
        ['wp-rich-text']
      );

      wp_add_inline_style(
        THEME_NAME.'-gutenberg',
        '.blogcard-url-search .components-popover__content {
          padding: 10px;
        }
        .blogcard-url-search .components-base-control__field {
          margin-bottom: 0;
        }'
      );
    }
  }

  //再利用ブロックメニュー
  public function reusable_menu() {
    add_menu_page('再利用ブロック', '再利用ブロック', 'manage_options', 'edit.php?post_type=wp_block', '', 'dashicons-controls-repeat', 22);
    add_submenu_page('edit.php?post_type=wp_block', '再利用ブロック一覧', '再利用ブロック一覧', 'manage_options', 'edit.php?post_type=wp_block');
    add_submenu_page('edit.php?post_type=wp_block', '新規追加', '新規追加', 'manage_options', 'post-new.php?post_type=wp_block');
  }

  //画像サイズ追加
  public function image_size($names) {
    foreach (wp_get_additional_image_sizes() as $name => $size) {
      $names[$name] = $name.' ('.strval($size['width']).'x'.strval($size['height']).')';
    }
    return $names;
  }

  //ブロックパターン
  public function block_pattern() {
    if (function_exists('register_block_pattern')) {
      foreach (self::PATTERNS as $pattern) {
        register_block_pattern(
          'silk/'.$pattern,
          require __DIR__.'/patterns/'.$pattern.'.php'
        );
      }
    }

    if (function_exists('register_block_pattern_category')) {
      register_block_pattern_category('silk', ['label' => 'Cocoonスキン「SILK」']);
    }
  }

  //見出しブロック
  public function blocks_heading($content, $block) {
    $level   = array_key_exists('level', $block['attrs']) ? 'h'.strval($block['attrs']['level']) : 'h2';
    $content = preg_replace('/<'.$level.'(.*?)>(.*?)<\/'.$level.'>/', '<'.$level.'$1><span>$2</span></'.$level.'>', $content);
    return $content;
  }

  //グループブロック
  public function blocks_group($content, $block) {
    if (array_key_exists('className', $block['attrs'])) {
      //比較表整形
      $content = $this->group_replace($content, $block, 'is-style-compare', 'cocoon-blocks/iconlist-box');

      //アコーディオン整形
      $content = $this->group_replace($content, $block, 'is-style-toggle-accordion', 'cocoon-blocks/toggle-box-1');
    }

    return $content;
  }

  //グループ整形
  private function group_replace($content, $block, $style, $blockname) {
    if (strpos($block['attrs']['className'], $style) !== false) {
      foreach ($block['innerBlocks'] as $innerblock) {
        if ($innerblock['blockName'] !== $blockname) {
          $content = str_replace(render_block($innerblock), '', $content);
        }
      }
    }

    return $content;
  }

  //トグルボックス
  public function blocks_toggle($content, $block) {
    if (array_key_exists('className', $block['attrs']) && strpos($block['attrs']['className'], 'is-style-faq') !== false) {
      add_filter('silk_faq_entity', function ($faq) use ($content, $block) {
        return $this->add_faq($content, $block, $faq, 'content');
      });
    }

    return $content;
  }

  //FAQブロック
  public function blocks_faq($content, $block) {
    add_filter('silk_faq_entity', function ($faq) use ($content, $block) {
      return $this->add_faq($content, $block, $faq, 'question');
    });

    return $content;
  }

  //FAQ追加
  private function add_faq($content, $block, $faq, $question) {
    $name = array_key_exists($question, $block['attrs']) ? strip_tags($block['attrs'][$question]) : '';
    $answer = preg_replace('{^'.$block['innerContent'][0].'(.+?)'.$block['innerContent'][count($block['innerContent']) - 1].'$}s', '$1', $content);
    $text = strip_tags(str_replace(["\n", "\r"], '', $answer), '<h1><h2><h3><h4><h5><h6><br><ol><ul><li><a><p><div><b><strong><i><em>');

    $faq[count($faq)] = [
      '@type'          => 'Question',
      'name'           => $name,
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text'  => $text
      ]
    ];

    return $faq;
  }

  //FAQスクリプト削除
  public function cocoon_faq($bool) {
    return false;
  }

  //フッタースクリプト
  public function footer_script() {
    $faq = apply_filters('silk_faq_entity', []);

    if (!empty($faq)) {
      $entity = [];

      foreach ($faq as $key => $value) {
        $entity[] = $value;
      }

      echo '<script type="application/ld+json">'.json_encode([
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => $entity
      ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT).'</script>';
    }
  }

  //AMP対応
  public function amp_css($css) {
    return '';
  }

  //AMP設定
  public function amp_skin() {
    if (!is_amp_skin_style_enable()) {
      remove_action('get_template_part_tmp/css-custom', [$this, 'css_custom']);
      remove_filter('wp_nav_menu_args', [$this, 'navi_footer']);
      remove_filter('wp_tag_cloud', [$this, 'tag_cloud'], 11);
      remove_filter('the_author_box_name', [$this, 'author_box']);
      remove_filter('get_blogcard_thumbnail_size', [$this, 'blogcard_size']);
      remove_filter('external_blogcard_amazon_image_width', [$this, 'blogcard_width']);
      remove_filter('external_blogcard_image_width', [$this, 'blogcard_width']);
      remove_filter('external_blogcard_amazon_image_height', [$this, 'blogcard_height']);
      remove_filter('external_blogcard_image_height', [$this, 'blogcard_height']);
    }
  }

  //設定追加
  public function option_setting() {
    $hook = get_plugin_page_hook('theme-backup', THEME_SETTINGS_PAFE);
    if (!is_null($hook)) {
      add_action($hook, [$this, 'option_field']);
    }
  }

  //設定項目
  public function option_field() {
    if (isset($_POST[self::HIDDEN]) && wp_verify_nonce($_POST[self::HIDDEN], 'skin-option') && $_FILES['options']['name'] != '') {
      if ($_FILES['options']['type'] === 'application/json') {
        $this->update_option($_FILES);
        echo '<div class="updated"><p><strong>設定を追加しました。</strong></p></div>';
      } else {
        echo '<div class="error"><p><strong>JSONファイルを選択してください。</strong></p></div>';
      }
    } ?>

    <div class="wrap admin-settings">
      <div class="metabox-holder">
        <div id="skin-option" class="postbox">
          <h2 class="hndle">オプション設定</h2>
          <div class="inside">
            <p>スキンのオプション設定を追加します。Cocoon設定が変更されるので、事前にバックアップファイルを取得してください。</p>
            <table class="form-table">
              <tbody>
                <tr>
                  <th scope="row">
                    <?php generate_label_tag('', 'オプション'); ?>
                  </th>
                  <td>
                    <form enctype="multipart/form-data" action="" method="POST">
                      <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
                      JSONファイルをアップロード:
                      <input name="options" type="file" accept="application/json" /><br>
                      <input type="submit" class="button" value="設定の追加" />
                      <input type="hidden" name="<?php echo self::HIDDEN; ?>" value="<?php echo wp_create_nonce('skin-option'); ?>">
                      <?php generate_tips_tag('スキンのオプション設定が書かれたJSONファイルを選択し、「設定の追加」ボタンを押してください。JSONファイルの作成方法はファイル名を除き、スキン制御に従います。'.get_help_page_tag('https://wp-cocoon.com/option-json/')); ?>
                      <p><span class="fa fa-arrow-right" aria-hidden="true"></span> <a href="https://dateqa.com/cocoon/#silk" target="_blank" rel="noopener">設定ファイルをダウンロードする</a></p>
                      <p><span class="fa fa-arrow-right" aria-hidden="true"></span> <a href="https://dateqa.com/cocoon/#option" target="_blank" rel="noopener">オプションの詳しい設定方法を見る</a></p>
                    </form>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  <?php
  }

  //オプション追加
  private function update_option($files) {
    if (is_user_administrator()) {
      $path = get_theme_cache_path().'/'.basename($files['options']['name']);

      if (move_uploaded_file($files['options']['tmp_name'], $path)) {
        if ($json = wp_filesystem_get_contents($path)) {
          $options = json_decode($json, true);

          if (!is_null($options)) {
            $mods = get_theme_mods();

            foreach ($options as $name => $value) {
              if (isset($mods[$name])) {
                $mods[$name] = $value;
              }
            }

            update_option("theme_mods_".get_option('stylesheet'), $mods);
          }

          wp_filesystem_delete($path);
        }
      }
    }
  }

  //clipboard.js
  public function clipboard_js() {
    if ($this->is_highlight()) { ?>
      <?php //https環境ではブラウザのクリップボードAPIを利用する
      if (is_ssl()): ?>
        <script>
          (function ($) {
            $(".wp-block-code").wrap('<div class="code-wrap"></div>').before('<button class="code-copy"><i class="<?php echo $this->fa; ?> fa-copy"></i></button>');

            const selector = '.code-copy';//clipboardで使う要素を指定
            $(selector).click(function(event){
              //クリック動作をキャンセル
              event.preventDefault();

              navigator.clipboard.writeText($(this).parent().text()).then(
                () => {
                  const info = $(".copy-info").text();
                  $(".copy-info").text("コードをコピーしました").fadeIn(500).delay(1000).fadeOut(500, function() {
                    $(".copy-info").text(info);
                  });
                });
            });

          })(jQuery);
        </script>
      <?php else: // httpの際 ?>
        <script>
			  (function ($) {
				  $(".wp-block-code").wrap('<div class="code-wrap"></div>').before('<button class="code-copy"><i class="<?php echo $this->fa; ?> fa-copy"></i></button>');
				  const clip = new Clipboard(".code-copy", {
					  target: function (trigger) {
						  return trigger.nextElementSibling;
					  },
				  });
				  clip.on("success", function(event) {
            const info = $(".copy-info").text();
					  $(".copy-info").text("コードをコピーしました").fadeIn(500).delay(1000).fadeOut(500, function() {
              $(".copy-info").text(info);
            });
					  event.clearSelection();
				  });
			  })(jQuery);
		  </script>
      <?php endif; ?>

    <?php }
  }

  //ハイライト表示
  private function is_highlight() {
    return is_code_highlight_enable() && is_singular() && !is_amp();
  }
}

Skin_Silk_Functions::instance();
