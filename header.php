<!doctype html>
<html <?php language_attributes(); ?>>

<head>
<?php //Google Search Consoleのサイト認証IDの表示
if ( get_google_search_console_id() ): ?>
<!-- Google Search Console -->
<meta name="google-site-verification" content="<?php echo get_google_search_console_id() ?>" />
<!-- /Google Search Console -->
<?php endif;//ウェブマスターツールID終了 ?>
<meta charset="utf-8">

<?php // force Internet Explorer to use the latest rendering engine available ?>
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<?php // mobile meta (hooray!) ?>
<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<meta name="viewport" content="width=device-width, initial-scale=1"/>

<?php wp_head(); ?>

<?php //ヘッドタグ内挿入用のユーザー用テンプレート
get_template_part('tmp-user/head-insert'); ?>

</head>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">

<?php //アクセス解析ヘッダータグの取得
get_template_part('tmp/header-analytics'); ?>

<div id="container" class="container<?php echo get_additional_container_classes(); ?> cf">
  <?php //サイトヘッダー
  get_template_part('tmp/header-container'); ?>

    <div id="content" class="content cf">

      <div id="content-in" class="content-in wrap cf">

          <?php //メインカラム手前に挿入するユーザー用テンプレート
          get_template_part('tmp-user/main-before'); ?>

          <main id="main" class="main<?php echo get_additional_main_classes(); ?>" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">