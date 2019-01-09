<?php //コメント関連
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//コメントリスト引数の取得
if ( !function_exists( 'get_wp_list_comments_args' ) ):
function get_wp_list_comments_args(){
  if (is_comment_display_type_default()) {
    $args = array(
      'avatar_size' => 55,
      'callback' => 'comment_custom_callback',
    );
  } else {
    $args = array(
      'callback' => 'simple_thread_comment_custom_callback',
    );
  }

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
      <div id="div-comment-<?php comment_ID() ?>" class="comment-body article">
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

//コメント用の日付フォーマット
if ( !function_exists( ' simple_thread_comment_date_format' ) ):
function simple_thread_comment_date_format(){
  return __( 'Y/m/d(D)', THEME_NAME );
}
endif;

//コメント用の時間フォーマット
if ( !function_exists( ' simple_thread_comment_time_format' ) ):
function simple_thread_comment_time_format(){
  return __( 'H:i:s', THEME_NAME );
}
endif;

//シンプルスレッド用カスタマイズコード
if ( !function_exists( 'simple_thread_comment_custom_callback' ) ):
function simple_thread_comment_custom_callback($comment, $args, $depth) {
  $GLOBALS['comment'] = $comment;
  if ( 'div' === $args['style'] ) {
    $add_below = 'comment';
  } else {
    $add_below = 'div-comment';
  } ?>
  <li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
    <div id="st-comment-<?php comment_ID(); ?>" class="st-comment-body article">

      <div class="st-comment-meta st-commentmetadata">
        <span class="st-comment-author vcard">
          <?php echo get_avatar( $comment, 48 );//アバター画像 ?>
          <?php printf(__('<span class="comment-author-label">名前:</span><cite class="fn comment-author">%s<span class="admin"></span></a></cite><span class="comment-author-separator"> :</span>'), get_comment_author_link()); //投稿者の設定 ?>
        </span>
        <span class="st-comment-datetime"><?php _e( '投稿日：', THEME_NAME ) ?><?php printf(__('%1$s at %2$s'), get_comment_date(simple_thread_comment_date_format()),  get_comment_time(simple_thread_comment_time_format())); //投稿日の設定 ?></span>
        <span class="st-comment-id">
        ID：<?php //IDっぽい文字列の表示（あくまでIDっぽいものです。）
          $ip01 = get_comment_author_IP(); //書き込んだユーザーのIPアドレスを取得
          $ip02 = get_comment_date('jn'); //今日の日付
          $ip03 = ip2long($ip01); //IPアドレスの数値化
          $ip04 = ($ip02) * ($ip03); //ip02とip03を掛け合わせる
          echo mb_substr(base64_encode($ip04), 2, 9); //base64でエンコード、頭から9文字まで出力
        ?>
        </span>
        <span class="st-comment-edit"><?php edit_comment_link(__('Edit'),'  ',''); //編集リンク ?></span>
      </div>
      <?php if ($comment->comment_approved == '0') : ?>
          <em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.') ?></em>
      <?php endif; ?>
      <div class="st-comment-content">
        <?php comment_text(); //コメント本文 ?>
      </div>

      <div class="reply">
          <?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
      </div>

    </div>
  </li>
<?php
}
endif;
