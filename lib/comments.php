<?php //コメント関連

//コメントリスト引数の取得
if ( !function_exists( 'get_wp_list_comments_args' ) ):
function get_wp_list_comments_args(){
  $args = array(
          'avatar_size' => 55,
          'type' => 'comment',
          'callback' => 'comment_custom_callback',
        );
  return $args;
}
endif;

//コメント出力に関するコールバック
if ( !function_exists( 'comment_custom_callback' ) ):
function comment_custom_callback($comment, $args, $depth) {
  if ( 'div' === $args['style'] ) {
    $tag       = 'div';
    $add_below = 'comment';
  } else {
    $tag       = 'li';
    $add_below = 'div-comment';
  }
  ?>
  <<?php echo $tag ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
  <?php if ( 'div' != $args['style'] ) : ?>
      <div id="div-comment-<?php comment_ID() ?>" class="comment-body">
  <?php endif; ?>
  <div class="comment-author vcard">
    <?php if ( $args['avatar_size'] != 0 ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
    <?php printf( __( '%s <span class="says">says:</span>' ),
                    sprintf( '<cite class="fn">%s</cite>', get_comment_author_link( $comment ) )
                ); ?>
  </div>
  <?php if ( $comment->comment_approved == '0' ) : ?>
     <em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></em>
      <br />
  <?php endif; ?>

  <div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>">
      <?php
      /* translators: 1: date, 2: time */
      printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)' ), '  ', '' );
      ?>
  </div>

  <div class="comment-content">
    <?php comment_text($comment, array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) )); ?>
  </div>

  <div class="reply">
      <?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
  </div>
  <?php if ( 'div' != $args['style'] ) : ?>
  </div>
  <?php endif; ?>
  <?php
}
endif;