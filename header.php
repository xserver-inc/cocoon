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
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="referrer" content="<?php echo apply_filters('cocoon_meta_referrer_content', get_meta_referrer_content()); ?>">
<meta name="format-detection" content="telephone=no">

<?php //ヘッドタグ内挿入用のアクセス解析用テンプレート
cocoon_template_part('tmp/head-analytics'); ?>
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
<?php //preconnect dns-prefetch
$domains = list_text_to_array(get_pre_acquisition_list());
if ($domains) {
  echo '<!-- preconnect dns-prefetch -->'.PHP_EOL;
}
foreach ($domains as $domain): ?>
<link rel="preconnect dns-prefetch" href="//<?php echo $domain; ?>">
<?php endforeach; ?>

<!-- Preload -->
<link rel="preload" as="font" type="font/woff" href="<?php echo FONT_ICOMOON_WOFF_URL; ?>" crossorigin>
<?php if (is_site_icon_font_font_awesome_4()): ?>
<link rel="preload" as="font" type="font/woff2" href="<?php echo FONT_AWESOME_4_WOFF2_URL; ?>" crossorigin>
<?php else: ?>
<link rel="preload" as="font" type="font/woff2" href="<?php echo FONT_AWESOME_5_BRANDS_WOFF2_URL; ?>" crossorigin>
<link rel="preload" as="font" type="font/woff2" href="<?php echo FONT_AWESOME_5_REGULAR_WOFF2_URL; ?>" crossorigin>
<link rel="preload" as="font" type="font/woff2" href="<?php echo FONT_AWESOME_5_SOLID_WOFF2_URL; ?>" crossorigin>
<?php endif; ?>
<?php //WordPressが出力するヘッダー情報
wp_head();
?>

<?php //カスタムフィールドの挿入（カスタムフィールド名：head_custom
cocoon_template_part('tmp/head-custom-field'); ?>

<?php //headで読み込む必要があるJavaScript
cocoon_template_part('tmp/head-javascript'); ?>

<?php //PWAスクリプト
cocoon_template_part('tmp/head-pwa'); ?>

<?php //ヘッドタグ内挿入用のユーザー用テンプレート
cocoon_template_part('tmp-user/head-insert'); ?>
</head>

<body <?php body_class(); ?> itemscope itemtype="https://schema.org/WebPage">

<?php //body最初に挿入するアクセス解析ヘッダータグの取得
  cocoon_template_part('tmp/body-top-analytics'); ?>

<?php //サイトヘッダーからコンテンツまでbodyタグ最初のHTML
cocoon_template_part('tmp/body-top'); ?>
