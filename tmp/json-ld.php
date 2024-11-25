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
$description = get_ogp_description_text();
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
  //画像サイズが取得できない場合
  if (($width === 0) || ($height === 0)) {
    //アイキャッチ画像のIDを取得
    $post_thumbnail_id = get_post_thumbnail_id();

    if ($post_thumbnail_id){
      //アイキャッチ画像のメタデータを取得
      $metadata = wp_get_attachment_metadata($post_thumbnail_id);

      if ($metadata){
        //幅と高さを取得
        if (isset($metadata['width']) && isset($metadata['height'])){
          $width = $metadata['width'];
          $height = $metadata['height'];
        }
      }
    }

  }
  //サムネイルの幅が小さすぎる場合は仕様（696px以上）に合わせる
  if (($width > 0) && ($width < 696)) {
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
    "name": "<?php echo esc_attr(get_the_auther_profile_name()); ?>"<?php // 投稿者ニックネーム ?>,
    "url": "<?php echo esc_url(get_the_auther_profile_page_url()); ?>"<?php // 投稿者URL ?>

  },
  "description": "<?php
    $description = $description;
    $description = str_replace('\\', '', $description);
    echo esc_attr($description);
  ?>…"<?php  // 抜粋 ?>

}
</script>
