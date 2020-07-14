<?php
//JSON-LDに関する記述
//https://developers.google.com/search/docs/data-types/articles
//https://schema.org/NewsArticle

/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

$author = (get_the_author_meta('display_name') ? get_the_author_meta('display_name') : get_bloginfo('name'));
 ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "mainEntityOfPage":{
    "@type":"WebPage",
    "@id":"<?php echo esc_attr(get_the_permalink()); ?>"<?php  // パーマリンクを取得 ?>

  },
  "headline": "<?php echo esc_attr(get_the_title());?>",<?php // ページタイトルを取得 ?>

  "image": {
    "@type": "ImageObject",
<?php
// アイキャッチ画像URLを取得
$image_url = get_singular_eyecatch_image_url();
$image_file = url_to_local($image_url);
if ($image_url && file_exists($image_file)) {
  $image_url = $image_url;
  $size = get_image_width_and_height($image_url);
  $width = $size ? $size['width'] : 800;
  $height = $size ? $size['height'] : 800;
  //サムネイルの幅が小さすぎる場合は仕様（696px以上）に合わせる
  if ($width < 696) {
    $height = round($height * (696/$width));
    $width = 696;
  }
} else {
  if (!$image_url) {
    $image_url = NO_IMAGE_LARGE;
  }
  $width = 800;
  $height = 451;
} ?>
    "url": "<?php echo esc_url($image_url);?>",
    "width": <?php echo $width; ?>,
    "height": <?php echo $height; ?>

  },
  "datePublished": "<?php echo esc_attr(get_seo_post_time()); ?>",<?php  // 記事投稿時間（分岐しているのbbPressのトピック対策） ?>

  "dateModified": "<?php echo esc_attr(get_seo_update_time()); ?>",<?php  // 記事更新時間 ?>

  "author": {
    "@type": "Person",
    "name": "<?php echo esc_attr($author); ?>"<?php // 投稿者ニックネーム ?>

  },
  "publisher": {
    "@type": "Organization",
    "name": "<?php echo esc_attr(get_bloginfo( 'name' )); ?>",<?php // サイト名 ?>

<?php //後で修正
$image_url = get_amp_logo_image_url();
$image_file = url_to_local($image_url);
if ($image_url && file_exists($image_file)) {//ロゴ画像がある場合
  $size = get_image_width_and_height($image_url);
  $width = $size ? $size['width'] : 600;
  $height = $size ? $size['height'] : 60;
  // //ロゴの幅が大きすぎる場合は仕様の範囲内（600px）にする
  // if ($width > 600) {
  //   $height = round($height * (600/$width));
  //   $width = 600;
  // }
  // //ロゴの高さが大きすぎる場合は仕様の範囲内（60px）にする
  // if ($height > 60) {
  //   $width = round($width * (60/$height));
  //   $height = 60;
  // }
  $sizes = calc_publisher_image_sizes($width, $height);
  $width = $sizes ? $sizes['width'] : 600;
  $height = $sizes ? $sizes['height'] : 60;
} else {//ロゴ画像がない場合
  $image_url = get_template_directory_uri().'/images/no-amp-logo.png';
  $width = 206;
  $height = 60;
}?>
    "logo": {
      "@type": "ImageObject",
      "url": "<?php echo esc_url($image_url); ?>",<?php  // ロゴ画像 ?>

      "width": <?php echo $width; ?>,
      "height": <?php echo $height; ?>

    }
  },
  "description": "<?php
    $description = get_meta_description_text();
    $description = str_replace('\\', '', $description);
    echo esc_attr($description);
  ?>…"<?php  // 抜粋 ?>

}
</script>
