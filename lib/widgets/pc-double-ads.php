<?php
///////////////////////////////////////////////////
//パソコン用ダブルレクタングル広告ウィジェットの追加
///////////////////////////////////////////////////
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('widgets_init', function(){register_widget('PcDoubleAdsWidgetItem');});
if ( !class_exists( 'PcDoubleAdsWidgetItem' ) ):
class PcDoubleAdsWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'pc_double_ads', //ウィジェット名
      WIDGET_NAME_PREFIX.__( 'PC用ダブルレクタングル広告', THEME_NAME ),
      array('description' => __( 'パソコンのみで表示されるダブルレクタングル広告ウィジェットです。834pxより大きな画面で表示されます。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );
  }
  function widget($args, $instance) {
    extract( $args );
    $ad1 = apply_filters( 'widget_pc_double_ad1_text', $instance['ad1_text'] );
    $ad2 = apply_filters( 'widget_pc_double_ad2_text', $instance['ad2_text'] );

    //classにwidgetと一意となるクラス名を追加する
    if ( !is_404() && //PCかつ404ページでないとき
          is_ads_visible()  ):
      echo $args['before_widget']; ?>
      <div class="ad-area ad-widget ad-dabble-rectangle">
        <div class="ad-label" data-nosnippet><?php echo get_ad_label_caption() ?></div>
        <div class="ad-wrap">
          <div class="ad-left ad-pc ad-responsive"><?php echo $ad1;?></div>
          <div class="ad-right ad-pc ad-responsive"><?php echo $ad2;?></div>
        </div>
      </div>
      <?php
      echo $args['after_widget'];
    endif //is_mobile ?>
  <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['ad1_text'] = $new_instance['ad1_text'];
    $instance['ad2_text'] = $new_instance['ad2_text'];
    return $instance;
  }
  function form($instance) {
    if(empty($instance)){//notice回避
      $instance = array(
        'ad1_text' => null,
        'ad2_text' => null,
      );
    }
    $ad1 = esc_attr($instance['ad1_text']);
    $ad2 = esc_attr($instance['ad2_text']);
?>
<?php //広告入力フォーム ?>
<p>
  <label for="<?php echo $this->get_field_id('ad1_text'); ?>">
    <?php _e( '広告タグ（左）', THEME_NAME ) ?>
  </label>
  <textarea class="widefat" id="<?php echo $this->get_field_id('ad1_text'); ?>" name="<?php echo $this->get_field_name('ad1_text'); ?>" cols="20" rows="16"><?php echo $ad1; ?></textarea>
</p>
<?php //広告入力フォーム ?>
<p>
  <label for="<?php echo $this->get_field_id('ad2_text'); ?>">
    <?php _e( '広告タグ（右）', THEME_NAME ) ?>
  </label>
  <textarea class="widefat" id="<?php echo $this->get_field_id('ad2_text'); ?>" name="<?php echo $this->get_field_name('ad2_text'); ?>" cols="20" rows="16"><?php echo $ad2; ?></textarea>
</p>
<?php
  }
}
endif;
