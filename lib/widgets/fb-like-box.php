<?php
///////////////////////////////////////////////////
//プロフィールウィジェットの追加
///////////////////////////////////////////////////
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( !is_amp()) {
  add_action('widgets_init', function(){register_widget('FBLikeBoxWidgetItem');});
}
if ( !class_exists( 'FBLikeBoxWidgetItem' ) ):
class FBLikeBoxWidgetItem extends WP_Widget {
  function __construct() {
     parent::__construct(
      'fb_like_box',
      WIDGET_NAME_PREFIX.__( 'Facebookボックス', THEME_NAME ),//ウィジェット名
      array('description' => __( '「この記事が気に入ったらフォローしよう」ウィジェットです。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );
  }
  function widget($args, $instance) {
    extract( $args );
    //タイトル名を取得
    $title = !empty($instance['title']) ? $instance['title'] : '';
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    $message = !empty( $instance['message'] ) ? $instance['message'] : __( 'この記事が気に入ったら<br>フォローしよう', THEME_NAME );
    $sub_message = !empty( $instance['sub_message'] ) ? $instance['sub_message'] : __( '最新情報をお届けします。', THEME_NAME );

    $facebook_url = !empty( $instance['facebook_url'] ) ? $instance['facebook_url'] : get_the_author_facebook_url();

    $twitter_id = isset( $instance['twitter_id'] ) ? $instance['twitter_id'] : '';
    if ($twitter_id === '0') {
      $twitter_id = null;
    }
    if ($twitter_id === '') {
      $twitter_id = get_the_author_twitter_id();
    }

    $line_id = isset( $instance['line_id'] ) ? $instance['line_id'] : '';
    if ($line_id === '0') {
      $line_id = null;
    }
    if ($line_id === '') {
      $line_id = get_the_author_line_id();
    }

    if ( is_singular() && $facebook_url ){ //投稿・固定ページのトップ表示
      echo $args['before_widget'];
      if ($title) {
        echo $args['before_title'].$title.$args['after_title'];//タイトルが設定されている場合は使用する
      }

      set_query_var('_MESSAGE', $message);
      set_query_var('_SUB_MESSAGE', $sub_message);
      set_query_var('_FACEBOOK_URL', $facebook_url);
      set_query_var('_TWITTER_ID', $twitter_id);
      set_query_var('_LINE_ID', $line_id);
      cocoon_template_part('tmp/fb-like-box');
      echo $args['after_widget'];
    }//is_singular
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags(!empty($new_instance['title']) ? $new_instance['title'] : '');
    $instance['message'] = !empty( $new_instance['message'] ) ? $new_instance['message'] : '';
    $instance['sub_message'] = !empty( $new_instance['sub_message'] ) ? $new_instance['sub_message'] : '';
    $instance['facebook_url'] = strip_tags(!empty($new_instance['facebook_url']) ? $new_instance['facebook_url'] : '');
    $instance['twitter_id'] = strip_tags(($new_instance['twitter_id'] !== '0') ? $new_instance['twitter_id'] : 0);
    $instance['line_id'] = strip_tags(($new_instance['line_id'] !== '0') ? $new_instance['line_id'] : 0);
      return $instance;
  }
  function form($instance) {
    if(empty($instance)){//notice回避
      $instance = array(
        'title' => null,
        'message' => null,
        'sub_message' => null,
        'facebook_url' => null,
        'twitter_id' => null,
        'line_id' => null,
      );
    }

    $title = esc_attr(!empty($instance['title']) ? $instance['title'] : null);
    $message = esc_attr(!empty($instance['message']) ? $instance['message'] : null);
    $sub_message = esc_attr(!empty($instance['sub_message']) ? $instance['sub_message'] : null);
    $facebook_url = esc_attr(!empty($instance['facebook_url']) ? $instance['facebook_url'] : null);
    $twitter_id = esc_attr(($instance['twitter_id'] != '0') ? $instance['twitter_id'] : 0);
    $line_id = esc_attr(($instance['line_id'] != '0') ? $instance['line_id'] : 0);
    ?>
    <?php //タイトル入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>">
        <?php _e( 'タイトル', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
    <?php //メッセージ ?>
    <p>
      <label for="<?php echo $this->get_field_id('message'); ?>">
        <?php _e( 'メッセージ', THEME_NAME ) ?>
      </label>
      <?php if (0): ?>
      <input class="widefat" id="<?php echo $this->get_field_id('message'); ?>" name="<?php echo $this->get_field_name('message'); ?>" type="text" value="<?php echo $message; ?>" placeholder="<?php _e( 'この記事が気に入ったら<br>フォローしよう', THEME_NAME ) ?>" />
      <?php endif ?>
      <textarea class="widefat" id="<?php echo $this->get_field_id('message'); ?>" name="<?php echo $this->get_field_name('message'); ?>" cols="20" rows="4" placeholder="<?php _e( 'この記事が気に入ったら<br>フォローしよう', THEME_NAME ) ?>"><?php echo $message; ?></textarea>
    </p>
    <?php //サブメッセージ ?>
    <p>
      <label for="<?php echo $this->get_field_id('sub_message'); ?>">
        <?php _e( 'サブメッセージ', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('sub_message'); ?>" name="<?php echo $this->get_field_name('sub_message'); ?>" type="text" value="<?php echo $sub_message; ?>" placeholder="<?php _e( '最新情報をお届けします。', THEME_NAME ) ?>" />
    </p>
    <?php //FacebookページURL ?>
    <p>
      <label for="<?php echo $this->get_field_id('facebook_url'); ?>">
        <?php _e( 'FacebookページURL', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('facebook_url'); ?>" name="<?php echo $this->get_field_name('facebook_url'); ?>" type="text" value="<?php echo $facebook_url; ?>" placeholder="<?php _e( 'https://www.facebook.com/XXXXXXX', THEME_NAME ) ?>" /><br>
      <?php _e( '未入力だとプロフィールページで設定されているものを利用。', THEME_NAME ) ?>
    </p>
    <?php //Twitter ID ?>
    <p>
      <label for="<?php echo $this->get_field_id('twitter_id'); ?>">
        <?php _e( 'Twitter ID（＠は不要です）', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('twitter_id'); ?>" name="<?php echo $this->get_field_name('twitter_id'); ?>" type="text" value="<?php echo $twitter_id; ?>" placeholder="<?php _e( 'XXXXXXX', THEME_NAME ) ?>" /><br>
      <?php _e( '同上。※「0」を入力で非表示。', THEME_NAME ) ?>
    </p>
    <?php //LINE ID ?>
    <p>
      <label for="<?php echo $this->get_field_id('line_id'); ?>">
        <?php _e( 'LINE ID（＠は不要です）', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('line_id'); ?>" name="<?php echo $this->get_field_name('line_id'); ?>" type="text" value="<?php echo $line_id; ?>" placeholder="<?php _e( 'XXXXXXX', THEME_NAME ) ?>" /><br>
      <?php _e( '同上。※「0」を入力で非表示。', THEME_NAME ) ?>
    </p>
    <?php
  }
}
endif;
