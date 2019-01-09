<?php //コメントエリア
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( is_comment_allow() || have_comments() ): ?>
<!-- comment area -->
<div id="comment-area" class="comment-area<?php echo get_additional_comment_area_classes(); ?>">
  <section class="comment-list">
    <h2 id="comments" class="comment-title">
      <?php echo get_comment_heading(); ?>
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

        <div class="comment-page-link">
          <?php paginate_comments_links(); //コメントが多い場合、ページャーを表示 ?>
        </div>
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
    $comment_info_msg_tag = '<div class="comment-information-messag">'.$comment_info_msg.'</div>';
  }
  //コメントフォームの引数
  $post_id = get_the_ID();
  $user = wp_get_current_user();
  $user_identity = $user->exists() ? $user->display_name : '';
  $args = array(
    'title_reply'  => get_comment_form_heading(),
    'label_submit' => get_comment_submit_label(),
    'logged_in_as' => '<p class="logged-in-as">' . sprintf(
      /* translators: 1: edit user link, 2: accessibility text, 3: user name, 4: logout URL */
      __( '<a href="%1$s" aria-label="%2$s">Logged in as %3$s</a>. <a href="%4$s">Log out?</a>' ),
      get_edit_user_link(),
      /* translators: %s: user name */
      esc_attr( sprintf( __( 'Logged in as %s. Edit your profile.' ), $user_identity ) ),
      $user_identity,
      wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ), $post_id ) )
    ) . '</p>'.$comment_info_msg_tag,
    'comment_notes_before' => '<p class="comment-notes"><span id="email-notes">' . __( 'Your email address will not be published.' ) . '</span>'. ( $req ? $required_text : '' ) . '</p>'.$comment_info_msg_tag,
  );
  echo '<aside class="comment-form">';
  if (!is_amp()) {
    if (is_comment_form_display_type_toggle_button()) {?>
      <button id="comment-reply-btn" class="comment-btn key-btn"><?php _e( 'コメントを書き込む', THEME_NAME ) ?></button>
    <?php }
    //通常ページ
    comment_form($args);
  } else {
    //AMPページ?>
    <h3 id="reply-title" class="comment-reply-title"><?php echo get_comment_form_heading(); ?></h3>
    <a class="comment-btn" href="<?php echo get_permalink().'#comment-area'; ?>"><?php _e( 'コメントを書き込む', THEME_NAME ) ?></a>
    <?php
  }


  echo '</aside>';

  ?>
</div><!-- /.comment area -->
<?php endif ?>

