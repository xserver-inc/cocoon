<?php //ウイジェットエリア用の関数

/////////////////////////////////////
// ウィジェットエリアの指定
/////////////////////////////////////

register_sidebars(1,
  array(
  'name' => __( 'サイドバー', THEME_NAME ),
  'id' => 'sidebar',
  'description' => __( 'サイドバーのウィジットエリアです。', THEME_NAME ),
  'before_widget' => '<aside id="%1$s" class="widget %2$s">',
  'after_widget' => '</aside>',
  'before_title'  => '<h3 class="widget-title sidebar_widget-title">',
  'after_title'   => '</h3>',
));

register_sidebars(1,
  array(
  'name' => __( 'サイドバースクロール追従領域', THEME_NAME ),
  'id' => 'sidebar-scroll',
  'description' => __( 'サイドバーで下にスクロールすると追いかけてくるエリアです。※モバイルでは表示されません。（ここにGoogle AdSenseを貼るのはポリシー違反です。）', THEME_NAME ),
  'before_widget' => '<aside id="%1$s" class="widget %2$s">',
  'after_widget' => '</aside>',
  'before_title' => '<h3 class="widget-title sidebar_widget-title">',
  'after_title' => '</h3>',
));

register_sidebars(1,
  array(
  'name' => __( 'メンインカラムスクロール追従領域', THEME_NAME ),
  'id' => 'man-scroll',
  'description' => __( 'メインカラムで下にスクロールすると追いかけてくるエリアです。サイドバーの方が長い場合に追従してきます。※モバイルでは表示されません。（ここにGoogle AdSenseを貼るのはポリシー違反です。）', THEME_NAME ),
  'before_widget' => '<aside id="%1$s" class="widget %2$s">',
  'after_widget' => '</aside>',
  'before_title' => '<div class="widget-omain-scroll main-widget-label">',
  'after_title' => '</h3>',
));

register_sidebars(1,
  array(
  'name' => __( '投稿パンくずリスト上', THEME_NAME ),
  'id' => 'widget-over-breadcrumbs',
  'description' => __( '投稿のパンくずリスト上に表示されるウイジェット。設定しないと表示されません。', THEME_NAME ),
  'before_widget' => '<div id="%1$s" class="widget-over-breadcrumbs %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<div class="widget-over-breadcrumbs-title main-widget-label">',
  'after_title' => '</div>',
));

register_sidebars(1,
  array(
  'name' => __( '投稿タイトル上', THEME_NAME ),
  'id' => 'widget-over-articletitle',
  'description' => __( '投稿タイトル上に表示されるウイジェット。設定しないと表示されません。', THEME_NAME ),
  'before_widget' => '<div id="%1$s" class="widget-over-articletitle %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<div class="widget-over-article-title main-widget-label">',
  'after_title' => '</div>',
));

register_sidebars(1,
  array(
  'name' => __( '投稿本文上', THEME_NAME ),
  'id' => 'widget-over-article',
  'description' => __( '投稿本文上に表示されるウイジェット。設定しないと表示されません。', THEME_NAME ),
  'before_widget' => '<div id="%1$s" class="widget-over-article %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<div class="widget-over-article-title main-widget-label">',
  'after_title' => '</div>',
));

register_sidebars(1,
  array(
  'name' => __( '投稿本文中', THEME_NAME ),
  'id' => 'widget-in-article',
  'description' => __( '投稿本文中に表示されるウイジェット。文中最初のH2タグの手前に表示されます。広告が表示されている場合は、広告の下に表示されます。設定しないと表示されません。', THEME_NAME ),
  'before_widget' => '<div id="%1$s" class="widget-in-article %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<div class="widget-in-article-title main-widget-label">',
  'after_title' => '</div>',
));

register_sidebars(1,
  array(
  'name' => __( '投稿本文下', THEME_NAME ),
  'id' => 'widget-under-article',
  'description' => __( '投稿本文下に表示されるウイジェット。設定しないと表示されません。', THEME_NAME ),
  'before_widget' => '<div id="%1$s" class="widget-under-article %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<div class="widget-under-article-title main-widget-label">',
  'after_title' => '</div>',
));

register_sidebars(1,
  array(
  'name' => __( '投稿SNSボタン上', THEME_NAME ),
  'id' => 'widget-over-sns-buttons',
  'description' => __( '投稿のメインカラムの一番下となるSNSボタンの上に表示されるウイジェット。広告を表示している場合は、広告の下になります。設定しないと表示されません。', THEME_NAME ),
  'before_widget' => '<div id="%1$s" class="widget-over-sns-buttons %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<div class="widget-over-sns-buttons-title main-widget-label">',
  'after_title' => '</div>',
));

register_sidebars(1,
  array(
  'name' => __( '投稿SNSボタン下', THEME_NAME ),
  'id' => 'widget-under-sns-buttons',
  'description' => __( '投稿のメインカラムの一番下となるSNSボタンの下に表示されるウイジェット。設定しないと表示されません。', THEME_NAME ),
  'before_widget' => '<div id="%1$s" class="widget-under-sns-buttons %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<div class="widget-under-sns-buttons-title main-widget-label">',
  'after_title' => '</div>',
));

register_sidebars(1,
  array(
  'name' => __( '投稿関連記事下', THEME_NAME ),
  'id' => 'widget-under-related-entries',
  'description' => __( '関連記事の下（広告を表示している場合はその下）に表示されるウイジェット。設定しないと表示されません。', THEME_NAME ),
  'before_widget' => '<div id="%1$s" class="widget-under-related-entries %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<h2 class="widget-under-related-entries-title main-widget-label">',
  'after_title' => '</h2>',
));

register_sidebars(1,
  array(
  'name' => __( '固定ページ本文上', THEME_NAME ),
  'id' => 'widget-over-page-article',
  'description' => __( '固定ページ本文上に表示されるウイジェット。設定しないと表示されません。', THEME_NAME ),
  'before_widget' => '<div id="%1$s" class="widget-over-page-article %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<div class="widget-over-page-article-title main-widget-label">',
  'after_title' => '</div>',
));

register_sidebars(1,
  array(
  'name' => __( '固定ページ本文中', THEME_NAME ),
  'id' => 'widget-in-page-article',
  'description' => __( '固定ページ本文中に表示されるウイジェット。文中最初のH2タグの手前に表示されます。広告が表示されている場合は、広告の下に表示されます。設定しないと表示されません。', THEME_NAME ),
  'before_widget' => '<div id="%1$s" class="widget-in-page-article %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<div class="widget-in-page-article-title main-widget-label">',
  'after_title' => '</div>',
));

register_sidebars(1,
  array(
  'name' => __( '固定ページ本文下', THEME_NAME ),
  'id' => 'widget-under-page-article',
  'description' => __( '固定ページ本文下に表示されるウイジェット。設定しないと表示されません。', THEME_NAME ),
  'before_widget' => '<div id="%1$s" class="widget-under-page-article %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<div class="widget-under-page-article-title main-widget-label">',
  'after_title' => '</div>',
));

register_sidebars(1,
  array(
  'name' => __( '固定ページSNSボタン上', THEME_NAME ),
  'id' => 'widget-over-page-sns-buttons',
  'description' => __( '固定ページのメインカラムの一番下となるSNSボタンの上に表示されるウイジェット。広告を表示している場合は、広告の下になります。設定しないと表示されません。', THEME_NAME ),
  'before_widget' => '<div id="%1$s" class="widget-over-page-sns-buttons %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<div class="widget-over-page-sns-buttons-title main-widget-label">',
  'after_title' => '</div>',
));

register_sidebars(1,
  array(
  'name' => __( '固定ページSNSボタン下', THEME_NAME ),
  'id' => 'widget-under-page-sns-buttons',
  'description' => __( '固定ページのメインカラムの一番下となるSNSボタンの下に表示されるウイジェット。設定しないと表示されません。', THEME_NAME ),
  'before_widget' => '<div id="%1$s" class="widget-under-page-sns-buttons %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<div class="widget-under-page-sns-buttons-title main-widget-label">',
  'after_title' => '</div>',
));

register_sidebars(1,
  array(
  'name' => __( 'インデックスリストトップ', THEME_NAME ),
  'id' => 'widget-index-top',
  'description' => __( 'インデックスリストのトップに表示されるウイジェット。広告が表示されているときは広告の下に表示されます。設定しないと表示されません。', THEME_NAME ),
  'before_widget' => '<aside id="%1$s" class="widget-index-top %2$s">',
  'after_widget' => '</aside>',
  'before_title' => '<div class="widget-index-top-title main-widget-label">',
  'after_title' => '</div>',
));

register_sidebars(1,
  array(
  'name' => __( 'インデックスリストミドル', THEME_NAME ),
  'id' => 'widget-index-middle',
  'description' => __( 'インデックスリストの3つ目下に表示されるウイジェット。「一覧リストのスタイル」が「サムネイルカード」の時のみの機能です。広告が表示されているときは広告の下に表示されます。設定しないと表示されません。', THEME_NAME ),
  'before_widget' => '<aside id="%1$s" class="widget-index-middle %2$s">',
  'after_widget' => '</aside>',
  'before_title' => '<div class="widget-index-middle-title main-widget-label">',
  'after_title' => '</div>',
));

register_sidebars(1,
  array(
  'name' => __( 'インデックスリストボトム', THEME_NAME ),
  'id' => 'widget-index-bottom',
  'description' => __( 'インデックスリストのボトムに表示されるウイジェット。広告が表示されているときは広告の下に表示されます。設定しないと表示されません。', THEME_NAME ),
  'before_widget' => '<aside id="%1$s" class="widget-index-bottom %2$s">',
  'after_widget' => '</aside>',
  'before_title' => '<div class="widget-index-bottom-title main-widget-label">',
  'after_title' => '</div>',
));

register_sidebars(1,
  array(
  'name' => __( 'フッター左', THEME_NAME ),
  'id' => 'footer-left',
  'description' => __( 'フッター左側のウィジットエリアです。', THEME_NAME ),
  'before_widget' => '<aside id="%1$s" class="widget %2$s">',
  'after_widget' => '</aside>',
  'before_title' => '<h3 class="footer_widget_title">',
  'after_title' => '</h3>',
));

register_sidebars(1,
  array(
  'id' => 'footer-center',
  'name' => __( 'フッター中', THEME_NAME ),
  'description' => __( 'フッター中間のウィジットエリアです。', THEME_NAME ),
  'before_widget' => '<aside id="%1$s" class="widget %2$s">',
  'after_widget' => '</aside>',
  'before_title' => '<h3 class="footer_widget_title">',
  'after_title' => '</h3>',
));

register_sidebars(1,
  array(
  'name' => __( 'フッター右', THEME_NAME ),
  'id' => 'footer-right',
  'description' => __( 'フッター右側フッター中のウィジットエリアです。', THEME_NAME ),
  'before_widget' => '<aside id="%1$s" class="widget %2$s">',
  'after_widget' => '</aside>',
  'before_title' => '<h3 class="footer_widget_title">',
  'after_title' => '</h3>',
));

register_sidebars(1,
  array(
  'name' => __( '404ページ', THEME_NAME ),
  'id' => '404-page',
  'description' => __( '404ページをカスタマイズするためのウィジットエリアです。', THEME_NAME ),
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<div class="404_widget_title">',
  'after_title' => '</div>',
));
