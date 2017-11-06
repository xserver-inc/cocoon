<?php
///////////////////////////////////////////////////
//モバイル用広告ウイジェットの追加
///////////////////////////////////////////////////
class MobileAdWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'mobile_ad',
      WIDGET_NAME_PREFIX.__( 'モバイル用広告', THEME_NAME ), //ウイジェット名
      array('description' => __( 'モバイルのみで表示される広告ウィジェットです。768px以下で表示されます。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );
  }
  function widget($args, $instance) {
    extract( $args );
    $ad = apply_filters( 'widget_mobile_ad_text', $instance['ad_text'] );

    if ( !is_404() && //404ページでないとき
         is_all_ads_visible() //広告表示がオンのとき
       ):
       echo $args['before_widget']; ?>
        <div class="ad-area ad-widget">
          <div class="ad-label"><?php echo get_ad_label() ?></div>
          <div class="ad-responsive ad-mobile"><?php echo $ad; ?></div>
        </div>
      <?php echo $args['after_widget'];
    endif //!is_404 ?>
  <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['ad_text'] = $new_instance['ad_text'];
    return $instance;
  }
  function form($instance) {
    if(empty($instance)){//notice回避
      $instance = array(
        'ad_text' => null,
      );
    }
    $ad = esc_attr($instance['ad_text']);
?>
<?php //広告入力フォーム ?>
<p>
  <label for="<?php echo $this->get_field_id('ad_text'); ?>">
    <?php _e( '広告タグ', THEME_NAME ) ?>
  </label>
  <textarea class="widefat" id="<?php echo $this->get_field_id('ad_text'); ?>" name="<?php echo $this->get_field_name('ad_text'); ?>" cols="20" rows="16"><?php echo $ad; ?></textarea>
</p>
<?php
  }
}
add_action('widgets_init', create_function('', 'return register_widget("MobileAdWidgetItem");'));