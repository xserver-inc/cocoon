<?php
///////////////////////////////////////////////////
//モバイル用広告ウイジェットの追加
///////////////////////////////////////////////////
class AdWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'common_ad',
      WIDGET_NAME_PREFIX.__( '広告', THEME_NAME ), //ウイジェット名
      array('description' => __( 'パソコンとモバイル端末両方に表示される広告ウィジェットです。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );
  }
  function widget($args, $instance) {
    extract( $args );
    $ad = apply_filters( 'widget_ad_text', isset($instance['ad_text']) ? $instance['ad_text'] : '' );
    $format = apply_filters( 'widget_ad_format', isset($instance['ad_format']) ? $instance['ad_format'] : 'none' );

    if ( !is_404() && //404ページでないとき
         is_all_ads_visible() //広告表示がオンのとき
       ):
       echo $args['before_widget'];
       get_template_part_with_ad_format($format, 'common-ad-widget', 1/*ラベルの表示*/, $ad);
       ?>
       <?php //以前のHTMLタグは使用しない
       if (0): ?>
        <div class="ad-area ad-widget">
          <div class="ad-label"><?php echo get_ad_label() ?></div>
          <div class="ad-responsive ad-common"><?php echo $ad; ?></div>
        </div>
       <?php endif ?>

      <?php echo $args['after_widget'];
    endif //!is_404 ?>
  <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['ad_text'] = $new_instance['ad_text'];
    $instance['ad_format'] = $new_instance['ad_format'];
    return $instance;
  }
  function form($instance) {
    if(empty($instance)){//notice回避
      $instance = array(
        'ad_text' => null,
        'ad_format' => 'none',
      );
    }
    $ad = esc_attr(!empty($instance['ad_text']) ? $instance['ad_text'] : '');
    $format = esc_attr(!empty($instance['ad_format']) ? $instance['ad_format'] : 'none');
?>
<?php //広告入力フォーム ?>
<p>
  <label for="<?php echo $this->get_field_id('ad_text'); ?>">
    <?php _e( '広告タグ', THEME_NAME ) ?>
  </label>
  <textarea class="widefat" id="<?php echo $this->get_field_id('ad_text'); ?>" name="<?php echo $this->get_field_name('ad_text'); ?>" cols="20" rows="16" placeholder="<?php _e( 'AdSenseの場合はレスポンシブタグを挿入してください。未記入の場合は、設定項目に指定したAdSenseコードが利用されます。', THEME_NAME ) ?>"><?php echo $ad; ?></textarea>
</p>
<?php //広告表示フォーマット ?>
<p>
  <?php
    //_V($format);
    generate_label_tag($this->get_field_id('ad_format'), __('広告フォーマット（AdSenseコード入力時のみ有効）', THEME_NAME) );
    global $_MOBILE_WIDGET_DATA_AD_FORMATS;
    $options = $_MOBILE_WIDGET_DATA_AD_FORMATS;
    generate_selectbox_tag($this->get_field_name('ad_format'), $options, $format);
   ?>
</p>
<?php
  }
}
add_action('widgets_init', function(){register_widget('AdWidgetItem');});