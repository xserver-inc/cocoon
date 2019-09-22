<?php
///////////////////////////////////////////////////
//おすすめカードウイジェットの追加
///////////////////////////////////////////////////
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('widgets_init', function(){register_widget('RecommendedCardWidgetItem');});
if ( !class_exists( 'RecommendedCardWidgetItem' ) ):
class RecommendedCardWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'recommended_cards',
      WIDGET_NAME_PREFIX.__( 'おすすめカード', THEME_NAME ),
      array('description' => __( 'おすすめカード一覧を表示するウィジェットです。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );//ウイジェット名
  }
  function widget($args, $instance) {
    extract( $args );
    //メニュー名
    $name = apply_filters( 'recommended_cards_widget_name', empty($instance['name']) ? '' : $instance['name'] );
    //タイトル名を取得
    $title = apply_filters( 'recommended_cards_widget_title', empty($instance['title']) ? '' : $instance['title'] );
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    //表示タイプ
    $entry_type = apply_filters( 'recommended_cards_widget_entry_type', empty($instance['entry_type']) ? ET_DEFAULT : $instance['entry_type'] );


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
    );
    //リストの作成
    echo get_navi_card_list_tag($atts);

    echo $args['after_widget']; ?>
  <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['name'] = strip_tags($new_instance['name']);
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['entry_type'] = strip_tags($new_instance['entry_type']);
    return $instance;
  }
  function form($instance) {
    if(empty($instance)){
      $instance = array(
        'name'   => '',
        'title'   => '',
        'entry_type'  => ET_DEFAULT,
      );
    }
    $name   = '';
    $title   = '';
    $entry_type  = ET_DEFAULT;
    if (isset($instance['name']))
      $name = esc_attr($instance['name']);
    if (isset($instance['title']))
      $title = esc_attr($instance['title']);
    if (isset($instance['entry_type']))
      $entry_type = esc_attr($instance['entry_type']);

    ?>
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
    <?php //タイトル入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>">
        <?php _e( 'タイトル', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
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
