<?php get_header(); ?>

<?php
////////////////////////////
//一覧の繰り返し処理
////////////////////////////
if (have_posts()) : // WordPress ループ
  while (have_posts()) : the_post(); // 繰り返し処理開始
    get_template_part('tmp/entry-card');
  endwhile; // 繰り返し処理終了 ?>
  <div class="clear"></div>
<?php else : // ここから記事が見つからなかった場合の処理  ?>
    <div class="post">
      <h2>NOT FOUND</h2>
      <p><?php echo __( '投稿が見つかりませんでした。', THEME_NAME );//見つからない時のメッセージ ?></p>
    </div>
<?php
endif;
?>

<?php get_template_part('tmp/pagenation') ?>

<?php get_footer(); ?>