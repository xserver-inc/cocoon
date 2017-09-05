<?php
///////////////////////////////////////////////////
//SNSフォローボタン
///////////////////////////////////////////////////
class SimplicitySocialFollowWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'sns_follow_buttons',
      __( '[S] SNSフォローボタン', 'simplicity2' ),
      array('description' => __( 'SNSサービスのフォローアイコンボタンを表示するSimplicityウィジェットです。', 'simplicity2' ))
    );
  }
  function widget($args, $instance) {
    extract( $args );
    $title_popular = apply_filters( 'widget_title_social_follow', $instance['title_social_follow'] );
    echo $args['before_widget'];
    echo $args['before_title'];
    if ($title_popular) {
      echo $title_popular;
    } else {
      echo __( 'SNSフォローボタン', 'simplicity2' );
    }
    echo $args['after_title'];
    get_template_part('sns-pages'); //SNSフォローボタン
    echo $args['after_widget']; ?>
  <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title_social_follow'] = trim(strip_tags($new_instance['title_social_follow']));
      return $instance;
  }
  function form($instance) {
    if(empty($instance)){
      $instance = array(
        'title_social_follow' => null,
      );
    }
    $title_social_follow = esc_attr($instance['title_social_follow']);
    ?>
    <p>
       <label for="<?php echo $this->get_field_id('title_social_follow'); ?>">
        <?php _e( 'SNSフォローボタンのタイトル', 'simplicity2' ) ?>
       </label>
       <input class="widefat" id="<?php echo $this->get_field_id('title_social_follow'); ?>" name="<?php echo $this->get_field_name('title_social_follow'); ?>" type="text" value="<?php echo $title_social_follow; ?>" />
    </p>
    <?php
  }
}
add_action('widgets_init', create_function('', 'return register_widget("SimplicitySocialFollowWidgetItem");'));