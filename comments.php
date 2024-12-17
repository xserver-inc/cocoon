<?php //コメントエリア
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( is_comment_open() || have_comments() ):
  $comment_heading = get_comment_heading();
  if (empty($comment_heading)) {
    $comment_heading = COMMENT_HEADING;
  } ?>
<!-- comment area -->
<div id="comment-area" class="comment-area<?php echo get_additional_comment_area_classes(); ?>">
  <section class="comment-list">
    <h2 id="comments" class="comment-title">
      <?php echo $comment_heading; ?>
      <?php if (get_comment_sub_heading()): ?>
        <span class="comment-sub-heading sub-caption"><?php echo get_comment_sub_heading(); ?></span>
      <?php endif ?>
    </h2>

    <?php
    if(have_comments()): // コメントがあったら
    ?>
        <ol class="commets-list">
        <?php
        $args = get_wp_list_comments_args();
        wp_list_comments($args); //コメント一覧を表示 ?>
        </ol>

        <?php
        if (get_comment_pages_count() > 1): ?>
        <div class="comment-page-link">
        <?php
          $args = array(
            'prev_text' => '<span class="screen-reader-text">'.__( '前へ', THEME_NAME ).'</span><span class="fa fa-angle-left" aria-hidden="true"></span>',
            'next_text' => '<span class="screen-reader-text">'.__( '次へ', THEME_NAME ).'</span><span class="fa fa-angle-right" aria-hidden="true"></span>',
          );
          paginate_comments_links($args); //コメントが多い場合、ページャーを表示
        ?>
        </div>
        <?php endif; ?>

    <?php
    endif; ?>
  </section>
  <?php

  ///////////////////////////////////////////
  // ここからコメントフォーム
  ///////////////////////////////////////////
  // メールアドレスが公開されることはありません。
  $req = get_option( 'require_name_email' );
  $required_text = sprintf( ' ' . __( 'Required fields are marked %s' ), '<span class="required">*</span>' );
  //コメント案内メッセージ
  $comment_info_msg = get_comment_information_message();
  $comment_info_msg_tag = null;
  if ($comment_info_msg) {
    $comment_info_msg_tag = '<div class="comment-information-messag">'.do_shortcode(wpautop($comment_info_msg)).'</div>';
  }
  //コメントフォームの引数
  $post_id = get_the_ID();
  $user = wp_get_current_user();
  $user_identity = $user->exists() ? $user->display_name : '';
  $comment_form_heading = get_comment_form_heading();
  if (empty($comment_form_heading)) {
    $comment_form_heading = COMMENT_FORM_HEADING;
  }
  $comment_submit_label = get_comment_submit_label();
  if (empty($comment_submit_label)) {
    $comment_submit_label = COMMENT_SUBMIT_LABEL;
  }
  $args = array(
    'title_reply'  => $comment_form_heading,
    'label_submit' => $comment_submit_label,
    'logged_in_as' => '<p class="logged-in-as">' . sprintf(
      /* translators: 1: edit user link, 2: accessibility text, 3: user name, 4: logout URL */
      __( 'Logged in as %1$s. <a href="%2$s">Edit your profile</a>. <a href="%3$s">Log out?</a>' ),
      $user_identity,
      get_edit_user_link(),
      wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ), $post_id ) )
    ) . '</p>'.$comment_info_msg_tag,
    'comment_notes_before' => '<p class="comment-notes"><span id="email-notes">' . __( 'Your email address will not be published.' ) . '</span>'. ( $req ? $required_text : '' ) . '</p>'.$comment_info_msg_tag,
  );

  if (is_comment_open()) {
     echo '<aside class="comment-form">';
    if (!is_amp()) {
      if (is_comment_form_display_type_toggle_button()) {?>
        <button type="button" id="comment-reply-btn" class="comment-btn key-btn"><?php _e( 'コメントを書き込む', THEME_NAME ) ?></button>
      <?php }
      //通常ページ
      comment_form($args);
    } else {
      //AMPページ?>
      <h3 id="reply-title" class="comment-reply-title"><?php echo $comment_form_heading; ?></h3>
      <a class="comment-btn" href="<?php echo get_permalink().'#comment-area'; ?>"><?php _e( 'コメントを書き込む', THEME_NAME ) ?></a>
      <?php
    }
      echo '</aside>';
  }
  ?>
</div><!-- /.comment area -->
<?php endif ?>

