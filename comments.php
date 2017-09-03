<!-- comment area -->
<div id="comment-area" class="comment-area">
  <section class="comment-list">
    <h2 id="comment-title" class="comment-title"><?php _e( 'コメント', THEME_NAME ); ?></h2>

    <?php
    if(have_comments()): // コメントがあったら
    ?>
        <ol class="commets-list">
        <?php wp_list_comments('avatar_size=55'); //コメント一覧を表示 ?>
        </ol>

        <div class="comment-page-link">
            <?php paginate_comments_links(); //コメントが多い場合、ページャーを表示 ?>
        </div>
    <?php
    endif; ?>
  </section>
  <?php

  // ここからコメントフォーム
  $args = array(
    'title_reply'  => __( 'コメントをどうぞ', THEME_NAME ),
    'label_submit' => __( 'コメントを送信', THEME_NAME ),
  );
  echo '<aside class="comment-form">';
  comment_form($args);
  echo '</aside>';

  ?>
</div><!-- /.comment area -->
