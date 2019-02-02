<?php
///////////////////////////////////////////////////
//アイテムランキングウイジェットの追加
///////////////////////////////////////////////////
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('widgets_init', function(){register_widget('TOCWidgetItem');});
if ( !class_exists( 'TOCWidgetItem' ) ):
class TOCWidgetItem extends WP_Widget {
  function __construct() {
     parent::__construct(
      'toc',
      WIDGET_NAME_PREFIX.__( '目次', THEME_NAME ),//ウイジェット名
      array('description' => __( '目次リンクを表示するだけのウィジェットです。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );
  }
  function widget($args, $instance) {
    extract( $args );
    //タイトル名を取得
    $title = !empty($instance['title']) ? $instance['title'] : '';
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    $title = apply_filters( 'toc_widget_title', $title, $instance, $this->id_base );

    if ( is_singular() ){
      //global $_TOC_WIDGET_OR_SHORTCODE_USE;

      $harray = array();
      $html = get_toc_tag(get_the_content(), $harray, true);

      //目次が出力されている場合
      if ($html) {
        echo $args['before_widget'];
        if ($title !== null) {
          if (empty($title)) {
            $title = __( '目次', THEME_NAME );
          }
        }
        if ($title) {
          echo $args['before_title'].$title.$args['after_title'];//タイトルが設定されている場合は使用する
        } ?>
        <div class="toc-widget-box">
          <?php echo $html; ?>
        </div>
        <?php echo $args['after_widget'];
      }

     } //is_singular ?>
    <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    if (isset($new_instance['title']))
      $instance['title'] = strip_tags($new_instance['title']);
    return $instance;
  }
  function form($instance) {
    if(empty($instance)){//notice回避
      $instance = array(
        'title' => null,
      );
    }
    $title = esc_attr(!empty($instance['title']) ? $instance['title'] : '');
    ?>
    <?php //タイトル入力フォーム ?>
    <p>
      <?php
      generate_label_tag($this->get_field_id('title'), __('タイトル', THEME_NAME) );
      generate_textbox_tag($this->get_field_name('title'), $title, '');
       ?>
    </p>
    <?php
  }
}
endif;
