<!doctype html>
<html <?php language_attributes(); ?>>

<head>
<?php //body最初に挿入するアクセス解析ヘッダータグの取得
get_template_part('tmp/head-analytics'); ?>
<meta charset="utf-8">
<?php //Google Search Consoleのサイト認証IDの表示
if ( get_google_search_console_id() ): ?>
<!-- Google Search Console -->
<meta name="google-site-verification" content="<?php echo get_google_search_console_id() ?>" />
<!-- /Google Search Console -->
<?php endif;//Google Search Console終了 ?>
<?php //Google Tag Manager
if (!is_user_administrator() && get_google_tag_manager_tracking_id()): ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo get_google_tag_manager_tracking_id(); ?>2');</script>
<!-- End Google Tag Manager -->
<?php endif //Google Tag Manager終了 ?>

<?php // force Internet Explorer to use the latest rendering engine available ?>
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<?php // mobile meta (hooray!) ?>
<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<meta name="viewport" content="width=device-width, initial-scale=1"/>

<?php wp_head(); ?>

<?php //カスタムフィールドの挿入（カスタムフィールド名：head_custom
get_template_part('tmp/head-custom-field'); ?>

<?php //ヘッドタグ内挿入用のユーザー用テンプレート
get_template_part('tmp-user/head-insert'); ?>

</head>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">

<?php //body最初に挿入するアクセス解析ヘッダータグの取得
get_template_part('tmp/body-top-analytics'); ?>

<div id="container" class="container<?php echo get_additional_container_classes(); ?> cf">
  <?php //サイトヘッダー
  get_template_part('tmp/header-container'); ?>

  <?php //アピールエリア
  get_template_part('tmp/appeal'); ?>

  <?php //カルーセル
  get_template_part('tmp/carousel'); ?>

  <?php //投稿パンくずリストがメイン手前の場合
  if (is_single() && is_single_breadcrumbs_position_main_before()){
    get_template_part('tmp/breadcrumbs');
  } ?>

  <?php //固定ページパンくずリストがメイン手前の場合
  if (is_page() && is_page_breadcrumbs_position_main_before()){
    get_template_part('tmp/breadcrumbs-page');
  } ?>

  <?php //メインカラム手前に挿入するユーザー用テンプレート
  get_template_part('tmp-user/main-before'); ?>

  <div id="content" class="content cf">

    <div id="content-in" class="content-in wrap cf">

        <main id="main" class="main<?php echo get_additional_main_classes(); ?>" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">