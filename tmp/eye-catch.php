<?php //投稿・固定ページのアイキャッチ
//アイキャッチがない場合は非表示クラスを追加
$display_none = has_post_thumbnail() ? '' : ' display-none'; ?>
<figure class="eye-catch<?php echo $display_none; ?>" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
  <?php
  if (has_post_thumbnail()) {
    //アイキャッチの表示
    the_post_thumbnail();
    // アイキャッチ画像のIDを取得
    $thumbnail_id = get_post_thumbnail_id();
    // mediumサイズの画像内容を取得（引数にmediumをセット）
    $eye_img = wp_get_attachment_image_src( $thumbnail_id , 'post-thumbnail' );
    $url = $eye_img[0];
    $width = $eye_img[1];
    $height = $eye_img[2];
  } else {
    $url = get_ogp_home_image_url();
    $size = get_image_width_and_height($home_image_url);
    $width = isset($size['width']) ? $size['width'] : 800;
    $height = isset($size['height']) ? $size['height'] : 600;
    echo ' <img src="'.$url.'" width="'.$width.'" height="'.$height.'" alt="">';
  }
  ?>
  <meta itemprop="url" content="<?php echo $url; ?>">
  <meta itemprop="width" content="<?php echo $width; ?>">
  <meta itemprop="height" content="<?php echo $height; ?>">
  <?php the_nolink_category(); //カテゴリラベルの取得 ?>
</figure>