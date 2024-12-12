<?php
///////////////////////////////////////////////////
//おすすめカードウィジェットの追加
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
    );//ウィジェット名
  }
  function widget($args, $instance) {
    extract( $args );
    //メニュー名
    $name = apply_filters( 'recommended_cards_widget_name', empty($instance['name']) ? '' : $instance['name'] );
    //タイトル名を取得
    $title = apply_filters( 'recommended_cards_widget_title', empty($instance['title']) ? '' : $instance['title'] );
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    //表示スタイル
    $style = apply_filters( 'recommended_cards_widget_style', empty($instance['style']) ? RC_DEFAULT : $instance['style'] );
    //余白
    $is_margin = apply_filters( 'recommended_cards_widget_is_margin', empty($instance['is_margin']) ? 0 : 1 );

    if ($name) {
      echo $args['before_widget'];
      if ($title) {
        echo $args['before_title'];
        echo $title;//タイトルが設定されている場合は使用する
        echo $args['after_title'];
      }

      //引数配列のセット
      $atts = array(
        'name' => $name,
        'style' => $style,
        'margin' => $is_margin,
      );
      //おすすめカードの作成
      echo get_recommend_cards_tag($atts);

      echo $args['after_widget'];
    }
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['name'] = strip_tags($new_instance['name']);
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['style'] = strip_tags($new_instance['style']);
    $instance['is_margin'] = isset($new_instance['is_margin']) ? 1 : 0;
    return $instance;
  }
  function form($instance) {
    if(empty($instance)){
      $instance = array(
        'name'   => '',
        'title'   => '',
        'style'  => RC_DEFAULT,
        'is_margin'  => 0,
      );
    }
    $name   = '';
    $title   = '';
    $style  = RC_DEFAULT;
    if (isset($instance['name']))
      $name = esc_attr($instance['name']);
    if (isset($instance['title']))
      $title = esc_attr($instance['title']);
    if (isset($instance['style']))
      $style = esc_attr($instance['style']);
    $is_margin = empty($instance['is_margin']) ? 0 : 1;
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
    <?php //表示スタイル ?>
    <p>
      <?php
      generate_label_tag($this->get_field_id('style'), __('表示スタイル', THEME_NAME) );
      echo '<br>';
      $options = get_widget_style_options();
      generate_radiobox_tag($this->get_field_name('style'), $options, $style);
      ?>
    </p>
    <?php //おすすめカード毎に余白を設ける ?>
    <p>
      <?php
        generate_checkbox_tag($this->get_field_name('is_margin') , $is_margin, __( 'おすすめカード毎に余白を設ける', THEME_NAME ));
      ?>
    </p>
    <p><?php echo get_help_page_tag('https://wp-cocoon.com/recommended-cards-widget/'); ?></p>
    <?php
  }
}
endif;
