<?php
///////////////////////////////////////////////////
//SNSフォローボタン
///////////////////////////////////////////////////
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('widgets_init', function(){register_widget('SocialFollowWidgetItem');});
if ( !class_exists( 'SocialFollowWidgetItem' ) ):
class SocialFollowWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'sns_follow_buttons',
      WIDGET_NAME_PREFIX.__( 'SNSフォローボタン', THEME_NAME ),
      array('description' => __( 'SNSサービスのフォローアイコンボタンを表示するウィジェットです。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );
  }
  function widget($args, $instance) {
    extract( $args );
    $title_popular = apply_filters( 'widget_title_social_follow', $instance['title_social_follow'] );
    $title_popular = apply_filters( 'widget_title', $title_popular, $instance, $this->id_base );
    echo $args['before_widget'];
    if (!is_null($title_popular)) {
      echo $args['before_title'];
      if ($title_popular) {
        echo $title_popular;
      } else {
        echo __( 'SNSフォローボタン', THEME_NAME );
      }
      echo $args['after_title'];
    }

    get_template_part_with_option('tmp/sns-follow-buttons', SF_WIDGET); //SNSフォローボタン
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
        <?php _e( 'SNSフォローボタンのタイトル', THEME_NAME ) ?>
       </label>
       <input class="widefat" id="<?php echo $this->get_field_id('title_social_follow'); ?>" name="<?php echo $this->get_field_name('title_social_follow'); ?>" type="text" value="<?php echo $title_social_follow; ?>" />
    </p>
    <?php
  }
}
endif;
