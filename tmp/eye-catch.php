<?php //投稿・固定ページのアイキャッチ
if ( has_post_thumbnail() ): // サムネイルを持っているとき ?>
<figure class="eye-catch" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
  <?php
    //アイキャッチの表示
    the_post_thumbnail();
    // アイキャッチ画像のIDを取得
    $thumbnail_id = get_post_thumbnail_id();

    // mediumサイズの画像内容を取得（引数にmediumをセット）
    $eye_img = wp_get_attachment_image_src( $thumbnail_id , 'post-thumbnail' );
  ?>
  <meta itemprop="url" content="<?php echo $eye_img[0]; ?>">
  <meta itemprop="width" content="<?php echo $eye_img[1]; ?>">
  <meta itemprop="height" content="<?php echo $eye_img[2]; ?>">
  <?php the_nolink_category(); //カテゴリラベルの取得 ?>
</figure>
<?php endif; ?>