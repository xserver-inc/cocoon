<?php
///////////////////////////////////////////////////
//パソコン用ダブルレクタングル広告ウイジェットの追加
///////////////////////////////////////////////////
class PcDoubleAdsWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'pc_double_ads', //ウイジェット名
      __( '[S] PC用広告ダブルレクタングル', 'simplicity2' ),
      array('description' => __( 'パソコンのみで表示されるSimplicity用のダブルレクタングル広告ウィジェットです。（※アドセンスの場合は広告コードのみを記入してください。）', 'simplicity2' ))
    );
  }
  function widget($args, $instance) {
    extract( $args );
    $ad1 = apply_filters( 'widget_pc_double_ad1_text', $instance['ad1_text'] );
    $ad2 = apply_filters( 'widget_pc_double_ad2_text', $instance['ad2_text'] );
    $is_exclude_ads_enable = apply_filters( 'widget_is_exclude_ads_enable', $instance['is_exclude_ads_enable'] );
    if ( empty($margin_left_px) ) {
      $margin_left_px = 0;
    }

    //classにwidgetと一意となるクラス名を追加する
    if ( !is_mobile() && !is_404() && //PCかつ404ページでないとき
      ( is_ads_visible() || !$is_exclude_ads_enable )  ):
      echo $args['before_widget']; ?>
      <div class="ad-article-bottom ad-space ad-widget">
        <div class="ad-label"><?php echo get_ads_label() ?></div>
        <div class="ad-left ad-pc adsense-336"><?php echo $ad1;?></div>
        <div class="ad-right ad-pc adsense-336"><?php echo $ad2;?></div>
        <div class="clear"></div>
      </div>
      <?php echo $args['after_widget'];
    endif //is_mobile ?>
  <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['ad1_text'] = $new_instance['ad1_text'];
    $instance['ad2_text'] = $new_instance['ad2_text'];
    $instance['is_exclude_ads_enable'] = $new_instance['is_exclude_ads_enable'];
    return $instance;
  }
  function form($instance) {
    if(empty($instance)){//notice回避
      $instance = array(
        'ad1_text' => null,
        'ad2_text' => null,
        'is_exclude_ads_enable' => true,
      );
    }
    $ad1 = esc_attr($instance['ad1_text']);
    $ad2 = esc_attr($instance['ad2_text']);
    $is_exclude_ads_enable = esc_attr($instance['is_exclude_ads_enable']);
?>
<?php //広告入力フォーム ?>
<p>
  <label for="<?php echo $this->get_field_id('ad1_text'); ?>">
    <?php _e( '広告タグ（左）', 'simplicity2' ) ?>
  </label>
  <textarea class="widefat" id="<?php echo $this->get_field_id('ad1_text'); ?>" name="<?php echo $this->get_field_name('ad1_text'); ?>" cols="20" rows="16"><?php echo $ad1; ?></textarea>
</p>
<?php //広告入力フォーム ?>
<p>
  <label for="<?php echo $this->get_field_id('ad2_text'); ?>">
    <?php _e( '広告タグ（右）', 'simplicity2' ) ?>
  </label>
  <textarea class="widefat" id="<?php echo $this->get_field_id('ad2_text'); ?>" name="<?php echo $this->get_field_name('ad2_text'); ?>" cols="20" rows="16"><?php echo $ad2; ?></textarea>
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
add_action('widgets_init', create_function('', 'return register_widget("PcDoubleAdsWidgetItem");'));