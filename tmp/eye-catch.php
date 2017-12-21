<?php //投稿・固定ページのアイキャッチ
if ( has_post_thumbnail() ): // サムネイルを持っているとき ?>
<figure class="eye-catch">
  <?php
    //アイキャッチの表示
    the_post_thumbnail();
  ?>
  <?php the_nolink_category(); //カテゴリラベルの取得 ?>
</figure>
<?php endif; ?>