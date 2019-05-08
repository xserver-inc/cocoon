<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
<?php //ヘッドタグ内挿入用のアクセス解析用テンプレート
get_template_part('tmp/head-analytics'); ?>
<meta charset="utf-8">
<?php //AMPの案内タグを出力
if ( has_amp_page() ): ?>
<link rel="amphtml" href="<?php echo get_amp_permalink(); ?>">
<?php endif ?>
<?php //Google Search Consoleのサイト認証IDの表示
if ( get_google_search_console_id() ): ?>
<!-- Google Search Console -->
<meta name="google-site-verification" content="<?php echo get_google_search_console_id() ?>" />
<!-- /Google Search Console -->
<?php endif;//Google Search Console終了 ?>
<?php //Google Tag Manager
if (is_analytics() && $tracking_id = get_google_tag_manager_tracking_id()): ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo $tracking_id; ?>');</script>
<!-- End Google Tag Manager -->
<?php endif //Google Tag Manager終了 ?>

<?php // force Internet Explorer to use the latest rendering engine available ?>
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<?php // mobile meta (hooray!) ?>
<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover"/>

<?php //自動アドセンス
get_template_part('tmp/ad-auto-adsense'); ?>


<?php wp_head(); ?>

<?php //カスタムフィールドの挿入（カスタムフィールド名：head_custom
get_template_part('tmp/head-custom-field'); ?>

<?php //headで読み込む必要があるJavaScript
get_template_part('tmp/head-javascript'); ?>

<?php //PWAスクリプト
get_template_part('tmp/head-pwa'); ?>

<?php //ヘッドタグ内挿入用のユーザー用テンプレート
get_template_part('tmp-user/head-insert'); ?>
</head>

<body <?php body_class(); ?> itemscope itemtype="https://schema.org/WebPage">

<?php //body要素の直後に何かを挿入する際
if ( function_exists( 'wp_body_open' ) ) {
    wp_body_open();
} else {
    do_action( 'wp_body_open' );
}?>

<?php //body最初に挿入するアクセス解析ヘッダータグの取得
get_template_part('tmp/body-top-analytics'); ?>

<?php //ユーザーカスタマイズ用
get_template_part('tmp-user/body-top-insert'); ?>

<?php //サイトヘッダーからコンテンツまでbodyタグ最初のHTML
get_template_part('tmp/body-top'); ?>
