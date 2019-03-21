<?php
///////////////////////////////////////////////////
//モバイル用テキストウイジェットの追加
///////////////////////////////////////////////////
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('widgets_init', function(){register_widget('MobileTextWidgetItem');});
if ( !class_exists( 'MobileTextWidgetItem' ) ):
class MobileTextWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'mobile_text',
      WIDGET_NAME_PREFIX.__( 'テキスト（モバイル用）', THEME_NAME ),
      array('description' => __( 'モバイルのみで表示されるテキストウィジェットです。834px以下で表示されます。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );//ウイジェット名
  }
  function widget($args, $instance) {
    extract( $args );
    //タイトル名を取得
    $title = apply_filters( 'widget_title_mobile_text', $instance['title_mobile_text'] );
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    $widget_text = isset( $instance['text_mobile_text'] ) ? $instance['text_mobile_text'] : '';
    $text = apply_filters( 'widget_text_mobile_text', $widget_text, $instance, $this );
    $filter = !empty( $instance['filter'] ) ? $instance['filter'] : 0;
    if ($filter) {
      $text = wpautop($text);
    }

    if ( !is_404() ): //404ページでないとき
      echo $args['before_widget'];
      if ($title) {//タイトルが設定されている場合は使用する
        echo $args['before_title'].$title.$args['after_title'];
      } ?>
      <div class="text-mobile">
        <?php echo $text; ?>
      </div>
    <?php echo $args['after_widget'];
    endif //!is_404 ?>
    <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    if (isset($new_instance['title_mobile_text']))
      $instance['title_mobile_text'] = strip_tags($new_instance['title_mobile_text']);
    if (isset($new_instance['text_mobile_text']))
      $instance['text_mobile_text'] = $new_instance['text_mobile_text'];
    $instance['filter'] = !empty( $new_instance['filter'] );

    return $instance;
  }
  function form($instance) {
    if(empty($instance)){//notice回避
      $instance = array(
        'title_mobile_text' => null,
        'text_mobile_text' => null,
        'filter' => null,
      );
    }
    $title = esc_attr(!empty($instance['title_mobile_text']) ? $instance['title_mobile_text'] : '');
    $text = esc_attr(!empty($instance['text_mobile_text']) ? $instance['text_mobile_text'] : '');
    $filter = esc_attr(!empty($instance['filter']) ? $instance['filter'] : 0);
    ?>
    <?php //タイトル入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('title_mobile_text'); ?>">
      <?php _e( 'タイトル', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('title_mobile_text'); ?>" name="<?php echo $this->get_field_name('title_mobile_text'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
    <?php //テキスト入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('text_mobile_text'); ?>">
      <?php _e( 'テキスト', THEME_NAME ) ?>
      </label>
      <textarea class="widefat" id="<?php echo $this->get_field_id('text_mobile_text'); ?>" name="<?php echo $this->get_field_name('text_mobile_text'); ?>" cols="20" rows="16"><?php echo $text; ?></textarea>
      <input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox"<?php checked( $filter );//_v($filter) ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e( '自動的に段落を追加する', THEME_NAME ) ?></label>
    </p>
    <?php
  }
}
endif;
