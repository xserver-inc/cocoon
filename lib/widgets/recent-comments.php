<?php
///////////////////////////////////////////////////
//最近のコメントウイジェット
///////////////////////////////////////////////////
class RecentCommentsWidgetItem extends WP_Widget {
  function __construct() {
     parent::__construct(
      'recent_comments',
      __( '[S] 最近のコメント', 'simplicity2' ),//ウイジェット名
      array('description' => __( '最近投稿されたコメントを表示するウィジェットです。', 'simplicity2' ))
    );
  }
  function widget($args, $instance) {
    extract( $args );

    //タイトル名を取得
    $title = apply_filters( 'widget_recent_comment_title', empty($instance['title']) ? __( '最近のコメント', 'simplicity2' ) : $instance['title'] );
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
        echo $args['before_title'];
        if ($title) {
          echo $title;//タイトルが設定されている場合は使用する
        } else {
          echo __( '最近のコメント', 'simplicity2' );
        }
        echo $args['after_title'];
        ?>
        <dl class="recent-comments">
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
              $url = get_permalink($comment->comment_post_ID);
              echo '<dt>';
              echo get_avatar( $comment, '38', null );
              echo '</dt>';

              echo '<dd>';
              echo '<div class="recent-comment-author">';
              comment_author($comment->comment_ID);
              echo '</div>';

              echo '<div class="recent-comment-date">';
              echo comment_date( get_theme_text_date_format(), $comment->comment_ID);
              echo '</div>';

              echo '<div class="recent-comment-title">';
              echo '<a href="'.get_permalink($comment->comment_post_ID).'#comment-'.$comment->comment_ID.'">'.$comment->post_title.'</a>';
              echo '</div>';

              echo '<div class="recent-comment-content"><span class="fa fa-comment-o"></span>&nbsp;';
              $my_pre_comment_content = strip_tags($comment->comment_content);
              if(mb_strlen($my_pre_comment_content,"UTF-8") > $str_count) {
                $my_comment_content = mb_substr($my_pre_comment_content, 0, $str_count) ; echo $my_comment_content. '...' ;
              } else {
                echo $my_pre_comment_content;
              };
              echo '</div>';

              echo '</dd>';
            }
          } else {
            echo __( 'コメントなし', 'simplicity2' );
          }
          ?>
        </dl>
      <?php echo $args['after_widget']; ?>
    <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['count'] = $new_instance['count'];
    $instance['str_count'] = $new_instance['str_count'];
    $instance['author_not_in'] = $new_instance['author_not_in'];
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
      <?php _e( 'タイトル', 'simplicity2' ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
    <?php //表示するコメント数 ?>
    <p>
      <label for="<?php echo $this->get_field_id('count'); ?>">
        <?php _e( '表示するコメント数', 'simplicity2' ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="number" min="3" max="30" value="<?php echo $count; ?>" />
    </p>
    <?php //コメント文字数 ?>
    <p>
      <label for="<?php echo $this->get_field_id('str_count'); ?>">
        <?php _e( 'コメント文字数', 'simplicity2' ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('str_count'); ?>" name="<?php echo $this->get_field_name('str_count'); ?>" type="number" min="30" value="<?php echo $str_count; ?>" />
    </p>
    <?php //管理者の除外 ?>
    <p>
      <label for="<?php echo $this->get_field_id('author_not_in'); ?>">
        <?php _e( '管理者の除外', 'simplicity2' ) ?>
      </label><br />
      <input class="widefat" id="<?php echo $this->get_field_id('author_not_in'); ?>" name="<?php echo $this->get_field_name('author_not_in'); ?>" type="checkbox" value="on"<?php echo ($author_not_in ? ' checked="checked"' : ''); ?> /><?php _e( '管理者のコメントを表示しない', 'simplicity2' ) ?>
    </p>
    <?php
  }
}
add_action('widgets_init', create_function('', 'return register_widget("RecentCommentsWidgetItem");'));
