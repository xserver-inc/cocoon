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
    $depth = !empty($instance['depth']) ? $instance['depth'] : 6;
    $depth = apply_filters( 'toc_widget_depth', $depth, $instance, $this->id_base );

    if ( is_singular() ){
      $harray = array();
      $the_content = get_toc_expanded_content();
      $html = get_toc_tag($the_content, $harray, true, $depth);

      //目次が出力されている場合
      if ($html) {
        echo $args['before_widget'];
        if (!is_null($title)) {
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
    if (isset($new_instance['depth']))
      $instance['depth'] = strip_tags($new_instance['depth']);
    return $instance;
  }
  function form($instance) {
    if(empty($instance)){//notice回避
      $instance = array(
        'title' => null,
        'depth' => null,
      );
    }
    $title = esc_attr(!empty($instance['title']) ? $instance['title'] : '');
    $depth = esc_attr(!empty($instance['depth']) ? $instance['depth'] : 6);
    ?>
    <?php //タイトル入力フォーム ?>
    <p>
      <?php
      generate_label_tag($this->get_field_id('title'), __('タイトル', THEME_NAME) );
      generate_textbox_tag($this->get_field_name('title'), $title, '');
       ?>
    </p>
    <?php //深さ入力フォーム ?>
    <p>
      <?php
      generate_label_tag($this->get_field_id('depth'), __('目次表示の深さ', THEME_NAME) );
      echo '<br>';
      $options = array(
        '2' => __( 'H2見出しまで', THEME_NAME ),
        '3' => __( 'H3見出しまで', THEME_NAME ),
        '4' => __( 'H4見出しまで', THEME_NAME ),
        '5' => __( 'H5見出しまで', THEME_NAME ),
        '0' => __( 'H6見出しまで（デフォルト）', THEME_NAME ),
      );
      generate_selectbox_tag($this->get_field_name('depth'), $options, $depth);
      generate_tips_tag(__( 'Cocoon設定「目次」タブの「目次表示の深さ」で表示されていないものは表示できません。', THEME_NAME ).'<br>'.__( '例：Cocoon設定で「h2見出しまで」と設定されている場合、このウィジェットで「h3見出し」以降を表示することはできません。', THEME_NAME ));
       ?>
    </p>
    <?php
  }
}
endif;
