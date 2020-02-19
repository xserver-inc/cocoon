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
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover"/>

<?php //ヘッドタグ内挿入用のアクセス解析用テンプレート
get_template_part('tmp/head-analytics'); ?>
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
<?php //自動アドセンス
get_template_part('tmp/ad-auto-adsense'); ?>
<?php //WordPressが出力するヘッダー情報
wp_head(); ?>

<!-- google analytics, Googleタグマネージャー -->
<link rel='preconnect dns-prefetch' href="//www.googletagmanager.com">
<link rel='preconnect dns-prefetch' href="//www.google-analytics.com">
<!-- Google AdSense -->
<link rel="preconnect dns-prefetch" href="//pagead2.googlesyndication.com">
<link rel="preconnect dns-prefetch" href="//googleads.g.doubleclick.net">
<link rel="preconnect dns-prefetch" href="//tpc.googlesyndication.com">
<link rel="preconnect dns-prefetch" href="//ad.doubleclick.net">
<link rel="preconnect dns-prefetch" href="//www.gstatic.com">
<!-- 各種サービス -->
<link rel="preconnect dns-prefetch" href="//cse.google.com">
<link rel="preconnect dns-prefetch" href="//fonts.gstatic.com">
<link rel="preconnect dns-prefetch" href="//fonts.googleapis.com">
<link rel="preconnect dns-prefetch" href="//cms.quantserve.com">
<link rel="preconnect dns-prefetch" href="//secure.gravatar.com">
<link rel="preconnect dns-prefetch" href="//cdn.syndication.twimg.com">
<link rel="preconnect dns-prefetch" href="//cdn.jsdelivr.net">
<!-- ASP -->
<link rel='preconnect dns-prefetch' href="//images-fe.ssl-images-amazon.com">
<link rel='preconnect dns-prefetch' href="//m.media-amazon.com">
<link rel='preconnect dns-prefetch' href="//completion.amazon.com">
<link rel="preconnect dns-prefetch" href="//i.moshimo.com">
<link rel="preconnect dns-prefetch" href="//aml.valuecommerce.com">
<link rel="preconnect dns-prefetch" href="//dalc.valuecommerce.com">
<link rel="preconnect dns-prefetch" href="//dalb.valuecommerce.com">

<!-- Preload -->
<link rel="preload" as="font" type="font/woff" href="<?php echo FONT_ICOMOON_WOFF_URL; ?>" crossorigin>
<link rel="preload" as="font" type="font/ttf" href="<?php echo FONT_ICOMOON_TTF_URL; ?>" crossorigin>
<?php if (is_site_icon_font_font_awesome_4()): ?>
<link rel="preload" as="font" type="font/woff2" href="<?php echo FONT_AWESOME_4_WOFF2_URL; ?>" crossorigin>
<?php else: ?>
<link rel="preload" as="font" type="font/woff2" href="<?php echo FONT_AWESOME_5_BRANDS_WOFF2_URL; ?>" crossorigin>
<link rel="preload" as="font" type="font/woff2" href="<?php echo FONT_AWESOME_5_REGULAR_WOFF2_URL; ?>" crossorigin>
<link rel="preload" as="font" type="font/woff2" href="<?php echo FONT_AWESOME_5_SOLID_WOFF2_URL; ?>" crossorigin>
<?php endif; ?>

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
