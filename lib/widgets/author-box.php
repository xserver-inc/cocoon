<?php
///////////////////////////////////////////////////
//プロフィールウイジェットの追加
///////////////////////////////////////////////////
class AuthorBoxWidgetItem extends WP_Widget {
  function __construct() {
     parent::__construct(
      'author_box',
      WIDGET_NAME_PREFIX.__( 'プロフィール', THEME_NAME ),//ウイジェット名
      array('description' => __( '記事を書いた著者のプロフィール情報を表示するウィジェットです。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );
  }
  function widget($args, $instance) {
    extract( $args );
    //タイトル名を取得
    $title = isset($instance['title']) ? $instance['title'] : '';
    $widget_name = isset( $instance['widget_name'] ) ? $instance['widget_name'] : '';

    echo $args['before_widget'];
    if ($title) {
      echo $args['before_title'].$title.$args['after_title'];//タイトルが設定されている場合は使用する
    }
    //set_query_var('_WIDGET_NAME', $widget_name);
    //get_template_part('tmp/author-box');
    generate_author_box_tag($widget_name);
    echo $args['after_widget'];
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags(isset($new_instance['title']) ? $new_instance['title'] : '');
    $instance['widget_name'] = isset($new_instance['widget_name']) ? $new_instance['widget_name'] : '';
      return $instance;
  }
  function form($instance) {
    if(empty($instance)){//notice回避
      $instance = array(
        'title' => null,
        'widget_name' => null,
      );
    }
    $title = esc_attr($instance['title']);
    $widget_name = esc_attr($instance['widget_name']);
    ?>
    <?php //タイトル入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>">
        <?php _e( 'タイトル', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
    <?php //ウィジェット名 ?>
    <p>
      <label for="<?php echo $this->get_field_id('widget_name'); ?>">
        <?php _e( 'ウィジェット名', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('widget_name'); ?>" name="<?php echo $this->get_field_name('widget_name'); ?>" type="text" value="<?php echo $widget_name; ?>" placeholder="<?php _e( '例：この記事を書いた人', THEME_NAME ) ?>" />
    </p>
    <?php //プロフィールページへの誘導 ?>
    <p>
      <?php _e( '※「プロフィール情報」や、「フォローボタン」はプロフィールページにて変更してください。', THEME_NAME ) ?><br>
      <a href="profile.php" target="_blank"><?php _e( 'あなたのプロフィール', THEME_NAME ) ?></a>
    </p>
    <?php
  }
}
//add_action('widgets_init', create_function('', 'return register_widget("AuthorBoxWidgetItem");'));
add_action('widgets_init', function(){register_widget('AuthorBoxWidgetItem');});
