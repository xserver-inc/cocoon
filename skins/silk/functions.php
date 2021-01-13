<?php

namespace SKIN\SILK;

/**
 * Cocoon設定のカスタマイズ
 */
class Functions {

  //インスタンス保持
  public static $instance = false;

  //サイトアイコンフォント
  private $fa = '';

  //スキンカラー初期値
  const KEY_COLOR  = '#757575';
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
    'black'         => '#616161',
    'watery-blue'   => '#e3f2fd',
    'watery-yellow' => '#fff8e1',
    'watery-red'    => '#ffebee',
    'watery-green'  => '#e8f5e9'
  ];

  //初期値・フック一覧
  private function __construct() {
    //ダークカラー定義
    if (!defined('SILK_DARK')) {
      define('SILK_DARK', ['rgba(255, 255, 255, 0.2)', 'rgba(255, 255, 255, 0.8)']);
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
    add_action('enqueue_block_editor_assets', [$this, 'editor_assets']);
    add_filter('render_block', [$this, 'custom_blocks'], 10, 2);

    //AMP
    add_filter('amp_skin_css', [$this, 'amp_css']);
    add_action('get_template_part_tmp/amp-header', [$this, 'amp_skin']);
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

    //ブロックスタイル
    foreach (self::BLOCK_STYLES as $blockstyle) {
      register_block_style($blockstyle['name'], $blockstyle['properties']);
    }
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
    #wp-calendar #today,
    .mobile-footer-menu-buttons,
    .is-style-color-head th {
      background: '.$color.';
    }

    .slick-dots li button:before,
    .slick-dots li.slick-active button:before,
    .archive-title span,
    .article h4 > span::before,
    .article h5,
    .sidebar h3,
    ul.toc-list > li::before,
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
    #wp-calendar #today,
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
    $site_background = get_site_background_color() ?: '#fff';
    echo 'hr.is-style-cut-line::after,
    .iconlist-title {
      background: '.$site_background.';
    }
    
    .speech-balloon::after {
      border-right-color: '.$site_background.';
    }
    
    .sbp-r .speech-balloon::after {
      border-left-color: '.$site_background.';
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

    //リンク色
    $link_color = get_site_link_color() ?: '#1967d2';
    echo 'a:hover,
    .comment-btn,
    .comment-btn:hover {
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

    //スライドインサイドバー
    echo '.sidebar-menu-content {
      color: '.$site_color.';
      background: '.$site_background.';
    }';

    //ボックスメニュー
    if ($color !== self::KEY_COLOR) {
      echo '.box-menus .box-menu:hover {
        box-shadow: inset 2px 2px 0 0 '.$color.', 2px 2px 0 0 '.$color.', 2px 0 0 0 '.$color.', 0 2px 0 0 '.$color.';
      }
      
      .box-menus .box-menu-icon {
        color: '.$color.';
      }';
    }

    //アイコン
    if ($this->fa === 'fa') {
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
    .comment-btn::before,
    .menu-drawer a::before {
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

    //幅広
    $group_padding = get_main_column_padding() ?: '29';
    echo '.entry-content .alignwide:not(.wp-block-table) {
      margin-left: -'.$group_padding.'px;
      margin-right: -'.$group_padding.'px;
    }';

    //全幅
    if (!is_admin() && !is_the_page_sidebar_visible()) {
      $group_width  = is_clumns_changed() ? get_site_wrap_width() : '1256';
      $group_margin = get_entry_content_margin_hight();

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
          padding: '.$group_margin.'em 16px;
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

  //エディター用JS
  public function editor_assets() {
    wp_enqueue_script(
      'silk-gutenberg-js',
      plugins_url('gutenberg.js', __FILE__),
      ['wp-rich-text']
    );
  }

  //ブロックコンテンツ
  public function custom_blocks($content, $block) {
    //見出し
    if ($block['blockName'] === 'core/heading') {
      $level   = array_key_exists('level', $block['attrs']) ? 'h'.strval($block['attrs']['level']) : 'h2';
      $content = preg_replace('/<'.$level.'(.*?)>(.*?)<\/'.$level.'>/', '<'.$level.'$1><span>$2</span></'.$level.'>', $content);
    }

    //グループ
    if ($this->class_exists($block, 'core/group')) {
      //比較表整形
      $content = $this->group_replace($content, $block, 'is-style-compare', 'cocoon-blocks/iconlist-box');

      //アコーディオン整形
      $content = $this->group_replace($content, $block, 'is-style-toggle-accordion', 'cocoon-blocks/toggle-box-1');
    }

    return $content;
  }

  //クラス有無
  private function class_exists($block, $name) {
    return $block['blockName'] === $name && array_key_exists('className', $block['attrs']);
  }

  //スタイル判定
  private function is_style($block, $style) {
    return strpos($block['attrs']['className'], $style) !== false;
  }

  //グループ整形
  private function group_replace($content, $block, $style, $blockname) {
    if ($this->is_style($block, $style)) {
      foreach ($block['innerBlocks'] as $innerblock) {
        if ($innerblock['blockName'] !== $blockname) {
          $content = str_replace(render_block($innerblock), '', $content);
        }
      }
    }
    return $content;
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
}

Functions::instance();
