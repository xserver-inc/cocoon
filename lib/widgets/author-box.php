<?php
///////////////////////////////////////////////////
//プロフィールウイジェットの追加
///////////////////////////////////////////////////
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('widgets_init', function(){register_widget('AuthorBoxWidgetItem');});
if ( !class_exists( 'AuthorBoxWidgetItem' ) ):
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
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    $label = isset( $instance['label'] ) ? $instance['label'] : '';
    $is_image_circle = !empty( $instance['is_image_circle'] );

    echo $args['before_widget'];
    if ($title) {
      echo $args['before_title'].$title.$args['after_title'];//タイトルが設定されている場合は使用する
    }
    //set_query_var('_WIDGET_NAME', $label);
    //get_template_part('tmp/author-box');
    // if (!is_bbpress_page()) {
    // }
    generate_author_box_tag(null, $label, $is_image_circle);
    echo $args['after_widget'];
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags(isset($new_instance['title']) ? $new_instance['title'] : '');
    $instance['label'] = isset($new_instance['label']) ? $new_instance['label'] : '';
    $instance['is_image_circle'] = !empty($new_instance['is_image_circle']);
    //_v($instance);
      return $instance;
  }
  function form($instance) {
    if(empty($instance)){//notice回避
      $instance = array(
        'title' => null,
        'label' => null,
        'is_image_circle' => 0,
      );
    }
    $title = null;
    $label = null;
    if (isset($instance['title']))
      $title = esc_attr($instance['title']);
    if (isset($instance['label']))
      $label = esc_attr($instance['label']);

    $is_image_circle = !empty($instance['is_image_circle']);

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
      <label for="<?php echo $this->get_field_id('label'); ?>">
        <?php _e( '肩書きラベル', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('label'); ?>" name="<?php echo $this->get_field_name('label'); ?>" type="text" value="<?php echo $label; ?>" placeholder="<?php _e( '例：この記事を書いた人', THEME_NAME ) ?>" />
    </p>
    <?php //画像を円形にする ?>
    <p>
      <?php
      generate_checkbox_tag( $this->get_field_name('is_image_circle'), $is_image_circle, __( '画像を円形にする', THEME_NAME ));
      ?>
    </p>
    <?php //プロフィールページへの誘導 ?>
    <p>
      <?php _e( '※「プロフィール画像・情報」や、「フォローボタン」はプロフィールページにて変更してください。', THEME_NAME ) ?><br>
      <a href="profile.php" target="_blank" rel="noopener"><?php _e( 'あなたのプロフィール', THEME_NAME ) ?></a>
    </p>
    <?php
  }
}
endif;
