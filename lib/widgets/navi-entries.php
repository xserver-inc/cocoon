<?php
///////////////////////////////////////////////////
//ナビカードウイジェットの追加
///////////////////////////////////////////////////
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('widgets_init', function(){register_widget('NaviEntryWidgetItem');});
if ( !class_exists( 'NaviEntryWidgetItem' ) ):
class NaviEntryWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'navi_entries',
      WIDGET_NAME_PREFIX.__( 'ナビカード', THEME_NAME ),
      array('description' => __( 'ナビカードリストを表示するウィジェットです。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );//ウイジェット名
  }
  function widget($args, $instance) {
    extract( $args );
    //タイトル名を取得
    $title = apply_filters( 'navi_entries_widget_title', empty($instance['title']) ? '' : $instance['title'] );
    //メニュー名
    $name = apply_filters( 'navi_entries_widget_name', empty($instance['name']) ? '' : $instance['name'] );
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    //表示タイプ
    $entry_type = apply_filters( 'navi_entries_widget_entry_type', empty($instance['entry_type']) ? ET_DEFAULT : $instance['entry_type'] );
    //タイトルの太さ
    $is_bold = apply_filters( 'navi_entries_widget_is_bold', empty($instance['is_bold']) ? 0 : 1 );
    //矢印表示
    $is_arrow_visible = apply_filters( 'navi_entries_widget_is_arrow_visible', empty($instance['is_arrow_visible']) ? 0 : 1 );


    echo $args['before_widget'];
    if ($title) {
      echo $args['before_title'];
      if ($title) {
        echo $title;//タイトルが設定されている場合は使用する
      }
      echo $args['after_title'];
    }

    //引数配列のセット
    $atts = array(
      'name' => $name,
      'type' => $entry_type,
      'bold' => $is_bold,
      'arrow' => $is_arrow_visible,
    );
    // _v($atts);
    //リストの作成
    echo get_navi_card_list_tag($atts);

    echo $args['after_widget']; ?>
  <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['name'] = strip_tags($new_instance['name']);
    $instance['entry_type'] = strip_tags($new_instance['entry_type']);
    $instance['is_bold'] = isset($new_instance['is_bold']) ? 1 : 0;
    $instance['is_arrow_visible'] = isset($new_instance['is_arrow_visible']) ? 1 : 0;
    return $instance;
  }
  function form($instance) {
    if(empty($instance)){
      $instance = array(
        'title'   => '',
        'name'   => '',
        'entry_type'  => ET_DEFAULT,
        'is_bold'  => 0,
        'is_arrow_visible'  => 0,
      );
    }
    $title   = '';
    $name   = '';
    $entry_type  = ET_DEFAULT;
    if (isset($instance['title']))
      $title = esc_attr($instance['title']);
    if (isset($instance['name']))
      $name = esc_attr($instance['name']);
    if (isset($instance['entry_type']))
      $entry_type = esc_attr($instance['entry_type']);
    $is_bold = empty($instance['is_bold']) ? 0 : 1;
    $is_arrow_visible = empty($instance['is_arrow_visible']) ? 0 : 1;

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
      //_v($menus);
      foreach ($menus as $menu) {
        $menu_name = $menu->name;
        $options[$menu_name] = $menu_name;
      }
      generate_selectbox_tag($this->get_field_name('name'), $options, $name);
      ?>
    </p>
    <?php //表示タイプ ?>
    <p>
      <?php
      generate_label_tag($this->get_field_id('entry_type'), __('表示タイプ', THEME_NAME) );
      echo '<br>';
      $options = get_widget_entry_type_options();
      generate_radiobox_tag($this->get_field_name('entry_type'), $options, $entry_type);
      ?>
    </p>
    <?php //タイトルを太字にする ?>
    <p>
      <?php
        generate_checkbox_tag($this->get_field_name('is_bold') , $is_bold, __( 'タイトルを太字にする', THEME_NAME ));
      ?>
    </p>
    <?php //矢印表示 ?>
    <p>
      <?php
        generate_checkbox_tag($this->get_field_name('is_arrow_visible') , $is_arrow_visible, __( '矢印表示', THEME_NAME ));
      ?>
    </p>
    <p><?php echo get_help_page_tag('https://wp-cocoon.com/navi-card-widget/'); ?></p>
    <?php
  }
}
endif;
