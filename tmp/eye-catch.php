<?php //投稿・固定ページのアイキャッチ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;
//アイキャッチがない場合は非表示クラスを追加
$display_none = (is_eyecatch_visible() && has_post_thumbnail()) ? null : ' display-none'; ?>
<div class="eye-catch-wrap<?php echo $display_none; ?>">
<figure class="eye-catch" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
  <?php
  if (has_post_thumbnail()) {
    // アイキャッチ画像のIDを取得
    $thumbnail_id = get_post_thumbnail_id();
    // fullサイズの画像内容を取得（引数にfullをセット）
    $eye_img = wp_get_attachment_image_src( $thumbnail_id , 'full' );
    $url = $eye_img[0];
    $width = $eye_img[1];
    $height = $eye_img[2];
    $size = $width.'x'.$height.' size-'.$width.'x'.$height;
    $attr = array(
      'class' => "attachment-$size eye-catch-image",
    );
    //アイキャッチの表示
    if ($width && $height) {
      the_post_thumbnail(array($width, $height), $attr);
    } else {
      the_post_thumbnail('full', $attr);
    }

  } else {
    $url = get_singular_eyecatch_image_url();
    $size = get_image_width_and_height($url);
    $width = isset($size['width']) ? $size['width'] : 800;
    $height = isset($size['height']) ? $size['height'] : 600;
    echo ' <img src="'.$url.'" width="'.$width.'" height="'.$height.'" alt="">';
  }
  ?>
  <meta itemprop="url" content="<?php echo $url; ?>">
  <meta itemprop="width" content="<?php echo $width; ?>">
  <meta itemprop="height" content="<?php echo $height; ?>">
  <?php //アイキャッチラベルの表示
  if (is_eyecatch_label_visible()) {
    the_nolink_category(null, apply_filters('is_eyecatch_category_label_visible', true)); //カテゴリラベルの取得
  }

  //アイキャッチにキャプションが設定されているとき
  if (is_eyecatch_caption_visible() && isset(get_post( get_post_thumbnail_id() )->post_excerpt)) {
    $eye_catch_caption = get_post( get_post_thumbnail_id() )->post_excerpt;
    if( $eye_catch_caption ) {
        echo '<figcaption class="eye-catch-caption">' . $eye_catch_caption . '</figcaption>';
    }
  }
  ?>
</figure>
</div>
