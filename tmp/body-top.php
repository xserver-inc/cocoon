<?php //ボディータグ上部
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div id="container" class="container<?php echo get_additional_container_classes(); ?> cf">

  <?php //body要素の直後に何かを挿入する際
  if ( function_exists( 'wp_body_open' ) ) {
      wp_body_open();
  } else {
      do_action( 'wp_body_open' );
  }?>

  <?php //ユーザーカスタマイズ用
  cocoon_template_part('tmp-user/body-top-insert'); ?>

  <?php //サイトヘッダー
  cocoon_template_part('tmp/header-container'); ?>

  <?php //通知エリア
  cocoon_template_part('tmp/notice'); ?>

  <?php //アピールエリア
  cocoon_template_part('tmp/appeal'); ?>

  <?php //おすすめカード
  cocoon_template_part('tmp/recommended-cards'); ?>

  <?php //カルーセル
  cocoon_template_part('tmp/carousel'); ?>

  <?php
  ////////////////////////////
  //コンテンツ上部ウィジェット
  ////////////////////////////
  if ( is_active_sidebar( 'content-top' ) ) : ?>
  <div id="content-top" class="content-top wwa">
    <div id="content-top-in" class="content-top-in wrap">
      <?php dynamic_sidebar( 'content-top' ); ?>
    </div>
  </div>
  <?php endif; ?>

  <?php //投稿パンくずリストがメイン手前の場合
  if ((is_single() || is_category()) && is_single_breadcrumbs_position_main_before()){
    cocoon_template_part('tmp/breadcrumbs');
  } ?>

  <?php //固定ページパンくずリストがメイン手前の場合
  if (is_page() && is_page_breadcrumbs_position_main_before()){
    cocoon_template_part('tmp/breadcrumbs-page');
  } ?>

  <?php //メインカラム手前に挿入するユーザー用テンプレート
  cocoon_template_part('tmp-user/main-before'); ?>

  <div id="content" class="content cf">

    <div id="content-in" class="content-in wrap">

        <main id="main" class="main<?php echo get_additional_main_classes(); ?>" itemscope itemtype="https://schema.org/Blog">

        <?php
        //PRラベル（小）の表示
        if (is_small_pr_labels_visible()) {
          generate_small_pr_label_tag(); //PR表記出力
        } ?>
