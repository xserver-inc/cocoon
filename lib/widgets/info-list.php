<?php
///////////////////////////////////////////////////
//新着情報ウィジェットの追加
///////////////////////////////////////////////////
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('widgets_init', function(){register_widget('InfoListWidgetItem');});
if ( !class_exists( 'InfoListWidgetItem' ) ):
class InfoListWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'info_list',
      WIDGET_NAME_PREFIX.__( '新着情報', THEME_NAME ),
      array('description' => __( '最近書かれた記事のタイトルリストを表示します。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );//ウィジェット名
  }
  function widget($args, $instance) {
    extract( $args );
    //ウィジェットモード（全ての新着記事を表示するか、カテゴリー別に表示するか）
    $widget_mode = apply_filters( 'info_list_widget_mode', empty($instance['widget_mode']) ? WM_DEFAULT : $instance['widget_mode'] );
    //タイトル名を取得
    $title = apply_filters( 'info_list_widget_title', empty($instance['title']) ? '' : $instance['title'] );
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    //キャプションを取得
    $caption = apply_filters( 'info_list_widget_caption', empty($instance['caption']) ? '' : $instance['caption'] );
    //表示数を取得
    $count = apply_filters( 'info_list_widget_count', empty($instance['count']) ? EC_DEFAULT : $instance['count'] );
    //水平表示
    $is_frame = apply_filters( 'info_list_widget_is_frame', empty($instance['is_frame']) ? 0 : 1 );
    $is_divider = apply_filters( 'info_list_widget_is_divider', empty($instance['is_divider']) ? 0 : 1 );
    //更新日順
    $modified = apply_filters( 'info_list_widget_modified', empty($instance['modified']) ? 0 : 1 );
    //カテゴリーID
    $cat_ids = empty($instance['cat_ids']) ? array() : $instance['cat_ids'];
    $cat_ids = apply_filters( 'info_list_widget_cat_ids', $cat_ids, $instance, $this->id_base );
    //カテゴリー配列のサニタイズ
    $cats = 'all';
    if (is_array($cat_ids)) {
      $cats = implode(',', $cat_ids);
    }

    echo $args['before_widget'];

    if ($title) {
      echo $args['before_title'];
      echo $title;//タイトルが設定されている場合は使用する
      echo $args['after_title'];
    }

    //引数配列のセット
    $atts = array(
      'caption' => $caption,
      'count' => $count,
      'frame' => $is_frame,
      'divider' => $is_divider,
      'modified' => $modified,
      'cats' => $cats,
    );
    //新着記事リストの作成
    generate_info_list_tag($atts);

    echo $args['after_widget']; ?>
  <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    if (isset($new_instance['title']))
      $instance['title'] = strip_tags($new_instance['title']);
    if (isset($new_instance['caption']))
      $instance['caption'] = strip_tags($new_instance['caption']);
    if (isset($new_instance['count']))
      $instance['count'] = strip_tags($new_instance['count']);

    $instance['is_frame'] = !empty($new_instance['is_frame']) ? 1 : 0;
    $instance['is_divider'] = !empty($new_instance['is_divider']) ? 1 : 0;
    $instance['modified'] = !empty($new_instance['modified']) ? 1 : 0;

    if (isset($new_instance['cat_ids'])){
      $instance['cat_ids'] = $new_instance['cat_ids'];
    } else {
      $instance['cat_ids'] = array();
    }

    return $instance;
  }
  function form($instance) {
    if(empty($instance)){
      $instance = array(
        'title'   => __( '新着情報', THEME_NAME ),
        'caption'   => '',
        'count' => EC_DEFAULT,
        'is_frame'  => 1,
        'is_divider'  => 1,
        'modified'  => 0,
        'cat_ids' => array(),
      );
    }
    $title   = __( '新着情報', THEME_NAME );
    $caption = '';
    $count = EC_DEFAULT;
    if (isset($instance['title']))
      $title = esc_attr($instance['title']);
    if (isset($instance['caption']))
      $caption = esc_attr($instance['caption']);
    if (isset($instance['count']))
      $count = esc_attr($instance['count']);
    $is_frame = empty($instance['is_frame']) ? 0 : 1;
    $is_divider = empty($instance['is_divider']) ? 0 : 1;
    $modified = empty($instance['modified']) ? 0 : 1;
    $cat_ids = isset($instance['cat_ids']) ? $instance['cat_ids'] : array();
    ?>
    <?php //タイトル入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>">
        <?php _e( '新着情報のタイトル', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
    <?php //キャプション入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('caption'); ?>">
        <?php _e( 'キャプション（枠線内表示）', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('caption'); ?>" name="<?php echo $this->get_field_name('caption'); ?>" type="text" value="<?php echo $caption; ?>" />
    </p>
    <?php //表示数入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('count'); ?>">
        <?php _e( '表示数（半角数字）', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="number" value="<?php echo $count ? $count : EC_DEFAULT; ?>" min="1" />
    </p>
    <?php //枠線を表示する ?>
    <p>
      <?php
        generate_checkbox_tag($this->get_field_name('is_frame') , $is_frame, __( '枠線を表示する', THEME_NAME ));
      ?>
    </p>
    <?php //仕切り線を表示する ?>
    <p>
      <?php
        generate_checkbox_tag($this->get_field_name('is_divider') , $is_divider, __( '仕切り線を表示する', THEME_NAME ));
      ?>
    </p>
    <?php //更新日順に並び替える ?>
    <p>
      <?php
        generate_checkbox_tag($this->get_field_name('modified') , $modified, __( '更新日順に並び替える', THEME_NAME ));
      ?>
    </p>
    <?php //表示カテゴリー ?>
    <p>
      <label>
        <?php _e( '表示カテゴリー', THEME_NAME ) ?>
        <?php _e( '（※未選択の場合は全て表示）', THEME_NAME ) ?>
      </label>
      <?php echo generate_hierarchical_category_check_list(0, $this->get_field_name('cat_ids'), $cat_ids); ?>
    </p>
    <?php
  }
}
endif;
