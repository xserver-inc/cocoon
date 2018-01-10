<?php if ( is_comment_allow() || have_comments() ): ?>
<!-- comment area -->
<div id="comment-area" class="comment-area<?php echo get_additional_comment_area_classes(); ?>">
  <section class="comment-list">
    <h2 id="comment-title" class="comment-title">
      <?php echo get_comment_heading(); ?>
      <?php if (get_comment_sub_heading()): ?>
        <span class="comment-sub-heading sub-caption"><?php echo get_comment_sub_heading(); ?></span>
      <?php endif ?>
    </h2>

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
    'title_reply'  => get_comment_form_heading(),
    'label_submit' => get_comment_submit_label(),
  );
  echo '<aside class="comment-form">';
  if (!is_amp()) {
    //通常ページ
    comment_form($args);
  } else {
    //AMPページ?>
    <h3 id="reply-title" class="comment-reply-title"><?php echo get_comment_form_heading(); ?></h3>
    <a class="amp-comment-btn" href="<?php echo get_permalink().'#respond'; ?>"><?php _e( 'コメントを書き込む', THEME_NAME ) ?></a>
    <?php
  }


  echo '</aside>';

  ?>
</div><!-- /.comment area -->
<?php endif ?>

