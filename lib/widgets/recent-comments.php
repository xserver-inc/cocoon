<?php
///////////////////////////////////////////////////
//最近のコメントウイジェット
///////////////////////////////////////////////////
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('widgets_init', function(){register_widget('RecentCommentsWidgetItem');});
if ( !class_exists( 'RecentCommentsWidgetItem' ) ):
class RecentCommentsWidgetItem extends WP_Widget {
  function __construct() {
     parent::__construct(
      'recent_comments',
      WIDGET_NAME_PREFIX.__( '最近のコメント', THEME_NAME ),//ウイジェット名
      array('description' => __( '最近投稿されたコメントを表示するウィジェットです。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );
  }
  function widget($args, $instance) {
    extract( $args );

    //タイトル名を取得
    $title = apply_filters( 'widget_recent_comment_title', empty($instance['title']) ? __( '最近のコメント', THEME_NAME ) : $instance['title'] );
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    //コメント表示数
    $count = apply_filters( 'widget_recent_comment_count', empty($instance['count']) ? 5 : absint( $instance['count'] ) );
    //コメント文字数
    $str_count = apply_filters( 'widget_recent_comment_str_count', empty($instance['str_count']) ? 100 : absint( $instance['str_count'] ) );
    //管理者除外するか
    $author_not_in = apply_filters( 'widget_recent_comment_author_not_in', empty($instance['author_not_in']) ? false : true );

     ?>
      <?php //classにwidgetと一意となるクラス名を追加する ?>
      <?php echo $args['before_widget']; ?>
        <?php
        if (!is_null($title)) {
          echo $args['before_title'];
          if ($title) {
            echo $title;//タイトルが設定されている場合は使用する
          } else {
            echo __( '最近のコメント', THEME_NAME );
          }
          echo $args['after_title'];
        }

        ?>
          <?php
          $comments_args = array(
            'author__not_in' => $author_not_in ? 1 : 0, // 管理者は除外
            'number' => $count, // 取得するコメント数
            'status' => 'approve', // 承認済みコメントのみ取得
            'type' => 'comment' // 取得タイプを指定。トラックバックとピンバックは除外
          );
          //クエリの取得
          $comments_query = new WP_Comment_Query;
          $comments = $comments_query->query( $comments_args );
          //コメントループ
          if ( $comments ) {
            foreach ( $comments as $comment ) {
              //var_dump($comment);
              $url = get_permalink($comment->comment_post_ID).'#comment-'.$comment->comment_ID;
              $title = $comment->post_title;
              $avatar = get_avatar( $comment, '42', null );
              $author = get_comment_author($comment->comment_ID);
              $date = get_comment_date( get_site_date_format(), $comment->comment_ID);
              $comment_content = strip_tags($comment->comment_content);
              if(mb_strlen($comment_content,"UTF-8") > $str_count) {
                $comment_content = mb_substr($comment_content, 0, $str_count).'...';
              }?>
                <div class="recent-comments cf">
                  <a class="recent-comment-link a-wrap cf" href="<?php echo $url; ?>" title="<?php echo esc_attr($title); ?>">
                    <div class="recent-comment cf">
                      <div class="recent-comment-info cf">
                        <figure class="recent-comment-avatar">
                          <?php echo $avatar; ?>
                        </figure>
                        <div class="recent-comment-author">
                          <?php echo $author; ?>
                        </div>
                        <div class="recent-comment-date">
                          <?php echo $date; ?>
                        </div>
                      </div>
                      <div class="recent-comment-content">
                        <?php echo $comment_content; ?>
                      </div>
                      <div class="recent-comment-article"><span class="fa fa-link" aria-hidden="true"></span> <?php echo $title; ?>
                      </div>
                    </div><!-- /.recent-comment -->
                  </a>
                </div><!-- /.recent-comments -->
                <?php
              // } else {
              //   echo $my_pre_comment_content;
              // };
              // echo '</div>';

              // echo '</dd>';
            }
          } else { ?>
            <p><?php _e( 'コメントなし', THEME_NAME ) ?></p>
          <?php
          }
          ?>
      <?php echo $args['after_widget']; ?>
    <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] =
      isset($new_instance['title']) ? strip_tags($new_instance['title']) : null;
    $instance['count'] =
      isset($new_instance['count']) ? $new_instance['count'] : null;
    $instance['str_count'] =
      isset($new_instance['str_count']) ? $new_instance['str_count'] : null;
    $instance['author_not_in'] =
      isset($new_instance['author_not_in']) ? $new_instance['author_not_in'] : null;
    return $instance;
  }
  function form($instance) {
    if(empty($instance)){//notice回避
      $instance = array(
        'title' => null,
        'count' => 5,
        'str_count' => 100,
        'author_not_in' => false,
      );
    }
    $title = esc_attr($instance['title']);
    $count = esc_attr($instance['count']);
    $str_count = esc_attr($instance['str_count']);
    $author_not_in = esc_attr($instance['author_not_in']);
    ?>
    <?php //タイトル入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>">
      <?php _e( 'タイトル', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
    <?php //表示するコメント数 ?>
    <p>
      <label for="<?php echo $this->get_field_id('count'); ?>">
        <?php _e( '表示するコメント数', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="number" min="3" max="30" value="<?php echo $count; ?>" />
    </p>
    <?php //コメント文字数 ?>
    <p>
      <label for="<?php echo $this->get_field_id('str_count'); ?>">
        <?php _e( 'コメント文字数', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('str_count'); ?>" name="<?php echo $this->get_field_name('str_count'); ?>" type="number" min="30" value="<?php echo $str_count; ?>" />
    </p>
    <?php //管理者の除外 ?>
    <p>
      <label for="<?php echo $this->get_field_id('author_not_in'); ?>">
        <?php _e( '管理者の除外', THEME_NAME ) ?>
      </label><br />
      <input class="widefat" id="<?php echo $this->get_field_id('author_not_in'); ?>" name="<?php echo $this->get_field_name('author_not_in'); ?>" type="checkbox" value="on"<?php echo ($author_not_in ? ' checked="checked"' : ''); ?> /><?php _e( '管理者のコメントを表示しない', THEME_NAME ) ?>
    </p>
    <?php
  }
}
endif;
