<?php
///////////////////////////////////////////////////
//新着エントリーウイジェットの追加
///////////////////////////////////////////////////
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('widgets_init', function(){register_widget('RelatedEntryWidgetItem');});
if ( !class_exists( 'RelatedEntryWidgetItem' ) ):
class RelatedEntryWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'related_entries',
      WIDGET_NAME_PREFIX.__( '関連記事', THEME_NAME ),
      array('description' => __( '関連記事リストをサムネイルつきで表示するウィジェットです。投稿ページのみ表示されます。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );//ウイジェット名
  }
  function widget($args, $instance) {
    extract( $args );
    //タイトル名を取得
    $title = apply_filters( 'widget_title_related', empty($instance['title']) ? '' : $instance['title'] );
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    //表示数を取得
    $entry_count = apply_filters( 'widget_entry_count', empty($instance['entry_count']) ? EC_DEFAULT : $instance['entry_count'] );
    $entry_type = apply_filters( 'widget_entry_type', empty($instance['entry_type']) ? ET_DEFAULT : $instance['entry_type'] );
    //除外カテゴリーIDを取得
    $exclude_cat_ids = empty($instance['exclude_cat_ids']) ? array() : $instance['exclude_cat_ids'];
    $exclude_cat_ids = apply_filters( 'widget_exclude_cat_ids', $exclude_cat_ids, $instance, $this->id_base );

    //現在のカテゴリを取得
    $categories = array();
    $categories = get_category_ids();//カテゴリ配列の取得
    //除外カテゴリ配列のサニタイズ
    if (empty($exclude_cat_ids)) {
      $exclude_cat_ids = array();
    } else {
      if (!is_array($exclude_cat_ids)) {
        $exclude_cat_ids = explode(',', $exclude_cat_ids);
      }
    }
    //_v($exclude_cat_ids);

    //classにwidgetと一意となるクラス名を追加する
    if ( is_single() && get_category_ids() ):
      echo $args['before_widget'];
      if ($title !== null) {
        echo $args['before_title'];
        if ($title) {
          echo $title;//タイトルが設定されている場合は使用する
        } else {
          _e( '関連記事', THEME_NAME );
        }
        echo $args['after_title'];
      }
      //_v($exclude_cat_ids);

      //引数配列のセット
      $atts = array(
        'entry_count' => $entry_count,
        'cat_ids' => $categories,
        'entry_type' => $entry_type,
        'include_children' => 0,
        'post_type' => 'post',
        'taxonomy' => 'category',
        'random' => 1,
        'exclude_cat_ids' => $exclude_cat_ids,
      );
      //関連記事リストの作成
      generate_widget_entries_tag($atts);
      //generate_widget_entries_tag($entry_count, $entry_type, $categories, 0, 'post', 'category', 1);

      echo $args['after_widget']; ?>
    <?php endif; ?>
  <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['entry_count'] = strip_tags($new_instance['entry_count']);
    $instance['entry_type'] = strip_tags($new_instance['entry_type']);
    if (isset($new_instance['exclude_cat_ids'])){
      $instance['exclude_cat_ids'] = $new_instance['exclude_cat_ids'];
    } else {
      $instance['exclude_cat_ids'] = array();
    }
      return $instance;
  }
  function form($instance) {
    if(empty($instance)){
      $instance = array(
        'title'   => '',
        'entry_count' => EC_DEFAULT,
        'entry_type'  => ET_DEFAULT,
        'exclude_cat_ids' => array(),
      );
    }
    $title   = '';
    $entry_count = EC_DEFAULT;
    $entry_type  = ET_DEFAULT;
    if (isset($instance['title']))
      $title = esc_attr($instance['title']);
    if (isset($instance['entry_count']))
      $entry_count = esc_attr($instance['entry_count']);
    if (isset($instance['entry_type']))
      $entry_type = esc_attr($instance['entry_type']);

    $exclude_cat_ids = isset($instance['exclude_cat_ids']) ? $instance['exclude_cat_ids'] : array();
    ?>
    <?php //タイトル入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>">
        <?php _e( '関連記事のタイトル', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
    <?php //表示数入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('entry_count'); ?>">
        <?php _e( '表示数（半角数字）', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('entry_count'); ?>" name="<?php echo $this->get_field_name('entry_count'); ?>" type="number" value="<?php echo $entry_count ? $entry_count : EC_DEFAULT; ?>" />
    </p>
    <?php //表示タイプフォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('entry_type'); ?>">
        <?php _e( '表示タイプ', THEME_NAME ) ?>
      </label><br />
      <input class="widefat" id="<?php echo $this->get_field_id('entry_type'); ?>" name="<?php echo $this->get_field_name('entry_type'); ?>"  type="radio" value="<?php echo ET_DEFAULT; ?>" <?php echo ( ($entry_type == ET_DEFAULT || !$entry_type ) ? ' checked="checked"' : ""); ?> /><?php _e( 'デフォルト', THEME_NAME ) ?><br />
      <input class="widefat" id="<?php echo $this->get_field_id('entry_type'); ?>" name="<?php echo $this->get_field_name('entry_type'); ?>"  type="radio" value="<?php echo ET_LARGE_THUMB; ?>"<?php echo ($entry_type == ET_LARGE_THUMB ? ' checked="checked"' : ""); ?> /><?php _e( '大きなサムネイル', THEME_NAME ) ?><br />
      <input class="widefat" id="<?php echo $this->get_field_id('entry_type'); ?>" name="<?php echo $this->get_field_name('entry_type'); ?>"  type="radio" value="<?php echo ET_LARGE_THUMB_ON; ?>"<?php echo ($entry_type == ET_LARGE_THUMB_ON ? ' checked="checked"' : ""); ?> /><?php _e( 'タイトルを重ねた大きなサムネイル', THEME_NAME ) ?><br />
    </p>
    <?php //除外カテゴリーID ?>
      <label>
        <?php _e( '除外カテゴリーID（除外するものを選択してください）', THEME_NAME ) ?>
      </label>
      <?php echo generate_hierarchical_category_check_list(0, $this->get_field_name('exclude_cat_ids'), $exclude_cat_ids); ?>
    <?php
  }
}
endif;
