<?php
///////////////////////////////////////////////////
//CTAウイジェットの追加
///////////////////////////////////////////////////
class CTABoxWidgetItem extends WP_Widget {
  function __construct() {
     parent::__construct(
      'cta_box',
      WIDGET_NAME_PREFIX.__( 'CTAボックス', THEME_NAME ),//ウイジェット名
      array('description' => __( 'コール・トゥ・アクションで訪問者にとってもらいたい行動を促すウィジェットです。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );
  }
  function widget($args, $instance) {
    extract( $args );
    //タイトル名を取得
    $title = !empty($instance['title']) ? $instance['title'] : '';
    $heading = !empty($instance['heading']) ? $instance['heading'] : '';
    $image_url = !empty($instance['image_url']) ? $instance['image_url'] : '';
    $message = !empty( $instance['message'] ) ? $instance['message'] : '';
    $layout = !empty( $instance['layout'] ) ? $instance['layout'] : '';
    $button_text = !empty( $instance['button_text'] ) ? $instance['button_text'] : '';
    $button_url = !empty( $instance['button_url'] ) ? $instance['button_url'] : '';
    $button_color_class = !empty( $instance['button_color_class'] ) ? $instance['button_color_class'] : 'btn-red';


    echo $args['before_widget'];
    if ($title) {
      echo $args['before_title'].$title.$args['after_title'];//タイトルが設定されている場合は使用する
    }

    set_query_var('_HEADING', $heading);
    set_query_var('_IMAGE_URL', $image_url);
    set_query_var('_MESSAGE', $message);
    set_query_var('_LAYOUT', $layout);
    set_query_var('_BUTTON_TEXT', $button_text);
    set_query_var('_BUTTON_URL', $button_url);
    set_query_var('_BUTTON_COLOR_CLASS', $button_color_class);

    get_template_part('tmp/cta-box');

    echo $args['after_widget'];
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags(!empty($new_instance['title']) ? $new_instance['title'] : '');
    $instance['heading'] = strip_tags(!empty($new_instance['heading']) ? $new_instance['heading'] : '');
    $instance['image_url'] = strip_tags(!empty( $new_instance['image_url'] ) ? $new_instance['image_url'] : '');
    $instance['message'] = !empty( $new_instance['message'] ) ? $new_instance['message'] : '';
    $instance['layout'] = strip_tags(!empty( $new_instance['layout'] ) ? $new_instance['layout'] : '');
    $instance['button_text'] = strip_tags(!empty($new_instance['button_text']) ? $new_instance['button_text'] : '');
    $instance['button_url'] = strip_tags(!empty($new_instance['button_url']) ? $new_instance['button_url'] : '');
    $instance['button_color_class'] = strip_tags(!empty($new_instance['button_color_class']) ? $new_instance['button_color_class'] : '');
      return $instance;
  }
  function form($instance) {
    if(empty($instance)){//notice回避
      $instance = array(
        'title' => null,
        'heading' => null,
        'image_url' => null,
        'message' => null,
        'layout' => null,
        'button_text' => null,
        'button_url' => null,
        'button_color_class' => 'btn-red',
      );
    }
    $title = esc_attr(isset($instance['title']) ? $instance['title'] : null);
    $heading = esc_attr(isset($instance['heading']) ? $instance['heading'] : null);
    $image_url = esc_attr(isset($instance['image_url']) ? $instance['image_url'] : null);
    $message = esc_attr(isset($instance['message']) ? $instance['message'] : null);
    $layout = esc_attr(isset($instance['layout']) ? $instance['layout'] : null);
    $button_text = esc_attr(isset($instance['button_text']) ? $instance['button_text'] : null);
    $button_url = esc_attr(isset($instance['button_url']) ? $instance['button_url'] : null);
    $button_color_class = esc_attr(isset($instance['button_color_class']) ? $instance['button_color_class'] : null);
    ?>
    <?php //タイトル入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>">
        <?php _e( 'タイトル', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
    <?php //見出し ?>
    <p>
      <label for="<?php echo $this->get_field_id('heading'); ?>">
        <?php _e( 'CTA見出し', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('heading'); ?>" name="<?php echo $this->get_field_name('heading'); ?>" type="text" value="<?php echo $heading; ?>" />
    </p>
    <?php //画像 ?>
    <p>
      <label for="<?php echo $this->get_field_id('image_url'); ?>">
        <?php _e( 'CTA画像', THEME_NAME ) ?>
      </label><br>

      <?php
      genelate_upload_image_tag($this->get_field_name('image_url'),$image_url); ?>
    </p>
    <?php //メッセージ?>
    <p>
      <label for="<?php echo $this->get_field_id('message'); ?>">
        <?php _e( 'CTAメッセージ', THEME_NAME ) ?>
      </label>
      <textarea class="widefat" id="<?php echo $this->get_field_id('message'); ?>" name="<?php echo $this->get_field_name('message'); ?>" cols="20" rows="6"><?php echo $message; ?></textarea>
    </p>
    <?php //レイアウト?>
    <p>
      <label for="<?php echo $this->get_field_id('layout'); ?>">
        <?php _e( '画像とメッセージのレイアウト', THEME_NAME ) ?>
      </label><br>
      <?php
      $options = array(
        'top_and_bottom' => __( '画像・メッセージを上下に配置', THEME_NAME ),
        'left_and_right' => __( '画像・メッセージを左右に配置', THEME_NAME ),
        'right_and_left' => __( 'メッセージ・画像を左右に配置', THEME_NAME ),
      );
      genelate_selectbox_tag($this->get_field_name('layout'), $options, $layout);
      ?>
    </p>
    <?php //ボタンテキスト ?>
    <p>
      <label for="<?php echo $this->get_field_id('button_text'); ?>">
        <?php _e( 'CTAボタンテキスト', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('button_text'); ?>" name="<?php echo $this->get_field_name('button_text'); ?>" type="text" value="<?php echo $button_text; ?>" />
    </p>
    <?php //ボタンURL ?>
    <p>
      <label for="<?php echo $this->get_field_id('button_url'); ?>">
        <?php _e( 'CTAボタンURL', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('button_url'); ?>" name="<?php echo $this->get_field_name('button_url'); ?>" type="text" value="<?php echo $button_url; ?>" />
    </p>
    <?php //ボタン色 ?>
    <p>
      <label for="<?php echo $this->get_field_id('button_color_class'); ?>">
        <?php _e( 'CTAボタン色', THEME_NAME ) ?>
      </label><br>
      <?php
      $options = array(
        'btn-red' => __( '赤色', THEME_NAME ),
        'btn-pink' => __( 'ピンク', THEME_NAME ),
        'btn-purple' => __( '紫色', THEME_NAME ),
        'btn-deep' => __( '深紫', THEME_NAME ),
        'btn-indigo' => __( '紺色（（インディゴ）', THEME_NAME ),
        'btn-blue' => __( '青色', THEME_NAME ),
        'btn-light-blue' => __( '水色', THEME_NAME ),
        'btn-cyan' => __( '明るい青（シアン）', THEME_NAME ),
        'btn-teal' => __( '緑色がかった青（ティール）', THEME_NAME ),
        'btn-green' => __( '緑色', THEME_NAME ),
        'btn-light-green' => __( '明るい緑', THEME_NAME ),
        'btn-lime' => __( 'ライム', THEME_NAME ),
        'btn-yellow' => __( '黄色', THEME_NAME ),
        'btn-amber' => __( '琥珀色（アンバー）', THEME_NAME ),
        'btn-orange' => __( 'オレンジ', THEME_NAME ),
        'btn-deep-orange' => __( 'ビープオレンジ', THEME_NAME ),
        'btn-brown' => __( '茶色', THEME_NAME ),
        'btn-grey' => __( '藍色', THEME_NAME ),
      );
      genelate_selectbox_tag($this->get_field_name('button_color_class'), $options, $button_color_class);
      ?>
    </p>
    <?php
  }
}
add_action('widgets_init', create_function('', 'return register_widget("CTABoxWidgetItem");'));
