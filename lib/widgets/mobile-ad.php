<?php
///////////////////////////////////////////////////
//モバイル用広告ウイジェットの追加
///////////////////////////////////////////////////
class MobileAdWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'mobile_ad',
      __( '[S] モバイル用広告', 'simplicity2' ), //ウイジェット名
      array('description' => __( 'モバイルのみで表示されるSimplicity用の広告ウィジェットです。（※アドセンスの場合は広告コードのみを記入してください。）', 'simplicity2' ))
    );
  }
  function widget($args, $instance) {
    extract( $args );
    $ad = apply_filters( 'widget_mobile_ad_text', $instance['ad_text'] );
    $margin_left_px = apply_filters( 'widget_margin_left_px', $instance['margin_left_px'] );
    $is_exclude_ads_enable = apply_filters( 'widget_is_exclude_ads_enable', $instance['is_exclude_ads_enable'] );
    if ( empty($margin_left_px) ) {
      $margin_left_px = 0;
    }

  ?>
  <?php //classにwidgetと一意となるクラス名を追加する ?>
  <?php
  $margin_left = null;
  $margin_left_tag = null;
  if ( $margin_left_px != 0 ) {
    $margin_left_tag = ' style="margin-left: '.$margin_left_px.'px;"';
  }
  if ( is_mobile() && !is_404() && //モバイルかつ404ページでないとき
       ( is_ads_visible() || !$is_exclude_ads_enable ) //広告表示がオンのとき
     ):
     echo $args['before_widget']; ?>
      <div class="ad-space ad-widget"<?php echo $margin_left_tag; ?>>
        <div class="ad-label"><?php echo get_ads_label() ?></div>
        <div class="ad-responsive ad-mobile adsense-300"><?php echo $ad; ?></div>
      </div>
    <?php echo $args['after_widget'];
  endif //is_mobile ?>
  <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['ad_text'] = $new_instance['ad_text'];
    $instance['margin_left_px']   = $new_instance['margin_left_px'];
    $instance['is_exclude_ads_enable'] = $new_instance['is_exclude_ads_enable'];
    return $instance;
  }
  function form($instance) {
    if(empty($instance)){//notice回避
      $instance = array(
        'ad_text' => null,
        'margin_left_px' => 0,
        'is_exclude_ads_enable' => true,
      );
    }
    $ad = esc_attr($instance['ad_text']);
    $margin_left_px = esc_attr($instance['margin_left_px']);
    $is_exclude_ads_enable = esc_attr($instance['is_exclude_ads_enable']);
?>
<?php //広告入力フォーム ?>
<p>
  <label for="<?php echo $this->get_field_id('ad_text'); ?>">
    <?php _e( '広告タグ', 'simplicity2' ) ?>
  </label>
  <textarea class="widefat" id="<?php echo $this->get_field_id('ad_text'); ?>" name="<?php echo $this->get_field_name('ad_text'); ?>" cols="20" rows="16"><?php echo $ad; ?></textarea>
</p>
<p>
  <label for="<?php echo $this->get_field_id('margin_left_px'); ?>">
    <?php _e( '左マージンのピクセル数（－指定で左に移動）', 'simplicity2' ) ?>
  </label>
  <?php if ( !$margin_left_px ){
    $margin_left_px = 0;
  } ?>
  <input class="widefat" id="<?php echo $this->get_field_id('margin_left_px'); ?>" name="<?php echo $this->get_field_name('margin_left_px'); ?>" type="number" value="<?php echo $margin_left_px; ?>" />
</p>
<?php //広告除外設定を適用するか ?>
<p>
  <label for="<?php echo $this->get_field_id('is_exclude_ads_enable'); ?>">
    <?php _e( '広告除外設定の適用', 'simplicity2' ) ?>
  </label><br />
  <input class="widefat" id="<?php echo $this->get_field_id('is_exclude_ads_enable'); ?>" name="<?php echo $this->get_field_name('is_exclude_ads_enable'); ?>" type="checkbox" value="on"<?php echo ($is_exclude_ads_enable ? ' checked="checked"' : ''); ?> /><?php _e( 'カスタマイザーの広告除外設定を適用する', 'simplicity2' ) ?>
</p>
<?php
  }
}
add_action('widgets_init', create_function('', 'return register_widget("MobileAdWidgetItem");'));