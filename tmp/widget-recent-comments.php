<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

$comment   = $args['comment'] ?? null;
$format    = $args['format'] ?? get_site_date_format();
$str_count = $args['str_count'] ?? 100;

//コメントが取得できない場合は何も出力しない
if ( !$comment ) return;

$url = get_permalink($comment->comment_post_ID).'#comment-'.$comment->comment_ID;
$title = $comment->post_title;
$avatar = get_avatar( $comment, '42', null );
$author = get_comment_author($comment->comment_ID);
$date = get_comment_date($format, $comment->comment_ID);

$comment_content = strip_tags($comment->comment_content);
$comment_content = str_replace(array("\r\n", "\r", "\n"), '', $comment_content);
//指定文字数を超えるコメントは省略する
if (mb_strlen($comment_content, "UTF-8") > $str_count) {
  $comment_content = mb_substr($comment_content, 0, $str_count).'...';
}
?>
<a class="recent-comment-link a-wrap cf" href="<?php echo esc_url($url); ?>" title="<?php echo esc_attr(wp_strip_all_tags($title)); //HTMLタグを除去してツールチップにコードが表示されないようにする ?>">
  <div class="recent-comment cf">
    <div class="recent-comment-info cf">
      <figure class="recent-comment-avatar"><?php echo $avatar; ?></figure>
      <div class="recent-comment-author"><?php echo esc_html($author); ?></div>
      <div class="recent-comment-date"><?php echo esc_html($date); ?></div>
    </div>
    <div class="recent-comment-content"><?php echo esc_html($comment_content); ?></div>
    <div class="recent-comment-article"><span class="fa fa-link" aria-hidden="true"></span><?php echo esc_html($title); ?></div>
  </div>
</a>
