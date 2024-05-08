<?php
///////////////////////////////////////////////////
//パソコン用テキストウィジェットの追加
///////////////////////////////////////////////////
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('widgets_init', function(){register_widget('PcTextWidgetItem');});
if ( !class_exists( 'PcTextWidgetItem' ) ):
class PcTextWidgetItem extends WP_Widget {
  function __construct() {
     parent::__construct(
      'pc_text',
      WIDGET_NAME_PREFIX.__( 'テキスト（PC用）', THEME_NAME ),//ウィジェット名
      array('description' => __( 'パソコンのみで表示されるテキストウィジェットです。834pxより大きな画面で表示されます。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );
  }
  function widget($args, $instance) {
    extract( $args );
    //タイトル名を取得
    $title = apply_filters( 'widget_title_pc_text', empty($instance['title_pc_text']) ? "" : $instance['title_pc_text'] );
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    $widget_text = isset( $instance['text_pc_text'] ) ? $instance['text_pc_text'] : '';
    $text = apply_filters( 'widget_text_pc_text', $widget_text, $instance, $this );
    $filter = !empty( $instance['filter'] );
    if ($filter) {
      $text = wpautop($text);
    }

    if ( !is_404() ): //パソコン表示かつ404ページでないとき
      echo $args['before_widget'];
      if ($title) {
        echo $args['before_title'].$title.$args['after_title'];//タイトルが設定されている場合は使用する
      } ?>
      <div class="text-pc">
        <?php echo $text; ?>
      </div>
      <?php echo $args['after_widget'];
    endif //!is_404 ?>
    <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    if (isset($new_instance['title_pc_text']))
      $instance['title_pc_text'] = strip_tags($new_instance['title_pc_text']);
    if (isset($new_instance['title_pc_text']))
      $instance['text_pc_text'] = $new_instance['text_pc_text'];
    $instance['filter'] = !empty( $new_instance['filter'] );
    return $instance;
  }
  function form($instance) {
    if(empty($instance)){//notice回避
      $instance = array(
        'title_pc_text' => null,
        'text_pc_text' => null,
        'filter' => null,
      );
    }
    $title = esc_attr(!empty($instance['title_pc_text']) ? $instance['title_pc_text'] : '');
    $text = esc_attr(!empty($instance['text_pc_text']) ? $instance['text_pc_text'] : '');
    $filter = esc_attr(!empty($instance['filter']) ? $instance['filter'] : '');
    ?>
    <?php //タイトル入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('title_pc_text'); ?>">
        <?php _e( 'タイトル', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('title_pc_text'); ?>" name="<?php echo $this->get_field_name('title_pc_text'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
    <?php //テキスト入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('text_pc_text'); ?>">
        <?php _e( 'テキスト', THEME_NAME ) ?>
      </label>
      <textarea class="widefat" id="<?php echo $this->get_field_id('text_pc_text'); ?>" name="<?php echo $this->get_field_name('text_pc_text'); ?>" cols="20" rows="16"><?php echo $text; ?></textarea>
      <input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox"<?php checked( $filter );//_v($filter) ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e( '自動的に段落を追加する', THEME_NAME ) ?></label>
    </p>
    <?php
  }
}
endif;
