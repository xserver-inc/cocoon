<?php
///////////////////////////////////////////////////
//ボックスメニューウィジェットの追加
///////////////////////////////////////////////////
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('widgets_init', function(){register_widget('BoxMenuWidgetItem');});
if ( !class_exists( 'BoxMenuWidgetItem' ) ):
class BoxMenuWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'box_menu',
      WIDGET_NAME_PREFIX.__( 'ボックスメニュー', THEME_NAME ),
      array('description' => __( 'アイコンつきメニューを表示するウィジェットです。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );//ウィジェット名
  }
  function widget($args, $instance) {
    extract( $args );
    //メニュー名
    $name = apply_filters( 'box_menu_widget_name', empty($instance['name']) ? '' : $instance['name'] );
    //タイトル名を取得
    $title = apply_filters( 'box_menu_widget_title', empty($instance['title']) ? '' : $instance['title'] );
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );


    echo $args['before_widget'];
    if ($title) {
      echo $args['before_title'];
      echo $title;//タイトルが設定されている場合は使用する
      echo $args['after_title'];
    }

    //引数配列のセット
    $atts = array(
      'name' => $name,
    );
    //リストの作成
    echo get_box_menu_tag($atts);

    echo $args['after_widget']; ?>
  <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['name'] = strip_tags($new_instance['name']);
    return $instance;
  }
  function form($instance) {
    if(empty($instance)){
      $instance = array(
        'title'   => '',
        'name'   => '',
      );
    }
    $title   = '';
    $name   = '';
    if (isset($instance['title']))
      $title = esc_attr($instance['title']);
    if (isset($instance['name']))
      $name = esc_attr($instance['name']);
    ?>
    <?php //タイトル入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>">
        <?php _e( 'タイトル', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
    <?php //メニュー名 ?>
    <p>
      <?php
      generate_label_tag($this->get_field_id('name'), __('メニュー名', THEME_NAME) );
      echo '<br>';
      $options = array();
      $menus = wp_get_nav_menus();
      foreach ($menus as $menu) {
        $menu_name = $menu->name;
        $options[$menu_name] = $menu_name;
      }
      generate_selectbox_tag($this->get_field_name('name'), $options, $name);
      ?>
    </p>
    <?php
  }
}
endif;
