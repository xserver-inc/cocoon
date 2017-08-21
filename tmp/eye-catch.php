<?php //投稿・固定ページのアイキャッチ
if ( has_post_thumbnail() ): // サムネイルを持っているとき ?>
<figure class="eye-catch">
  <?php
    //アイキャッチの表示
    the_post_thumbnail();
    // //アイキャッチのキャプション機能が有効のとき
    // if( is_eye_catch_caption_visible() &&
    //   //アイキャッチにキャプションが設定されているとき
    //   get_post( get_post_thumbnail_id() )->post_excerpt ) {
    //     echo '<figcaption class="eye-catch-caption">' . get_post( get_post_thumbnail_id() )->post_excerpt . '</figcaption>';
    // }
  ?>
</figure>
<?php endif; ?>