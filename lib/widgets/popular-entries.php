<?php
///////////////////////////////////////////////////
//人気エントリーウイジェットの追加
///////////////////////////////////////////////////
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (is_access_count_enable()) {
  add_action('widgets_init', function(){register_widget('PopularEntryWidgetItem');});
}
if ( !class_exists( 'PopularEntryWidgetItem' ) ):
class PopularEntryWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'popular_entries',
      WIDGET_NAME_PREFIX.__( '人気記事', THEME_NAME ),
      array('description' => __( '人気記事リストをサムネイルつきで表示するウィジェットです。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );//ウイジェット名
  }
  function widget($args, $instance) {
    extract( $args );
    //ウィジェットモード（全ての人気記事を表示するか、カテゴリ別に表示するか）
    $widget_mode = apply_filters( 'widget_mode', empty($instance['widget_mode']) ? WM_DEFAULT : $instance['widget_mode'] );
    //タイトル名を取得
    $title = empty($instance['title']) ? '' : $instance['title'];
    $title = apply_filters( 'popular_entries_widget_title', $title, $instance, $this->id_base );
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    //表示数を取得
    $entry_count = apply_filters( 'popular_entries_widget_entry_count', empty($instance['entry_count']) ? EC_DEFAULT : $instance['entry_count'] );
    //表示タイプ
    $entry_type = apply_filters( 'popular_entries_widget_entry_type', empty($instance['entry_type']) ? ET_DEFAULT : $instance['entry_type'] );
    //集計期間
    $count_days = apply_filters( 'popular_entries_widget_count_days', empty($instance['count_days']) ? PCD_DEFAULT : $instance['count_days'] );
    //ランキング表示
    $ranking_visible = apply_filters( 'popular_entries_widget_ranking_visible', empty($instance['ranking_visible']) ? 0 : $instance['ranking_visible'] );
    //PV表示
    $pv_visible = apply_filters( 'popular_entries_widget_pv_visible', empty($instance['pv_visible']) ? 0 : $instance['pv_visible'] );
    //タイトルを太字に
    $is_bold = apply_filters( 'popular_entries_widget_is_bold', empty($instance['is_bold']) ? 0 : $instance['is_bold'] );
    //矢印表示
    $is_arrow_visible = apply_filters( 'popular_entries_widget_is_arrow_visible', empty($instance['is_arrow_visible']) ? 0 : $instance['is_arrow_visible'] );
    //除外投稿IDを取得
    $exclude_post_ids = empty($instance['exclude_post_ids']) ? '' : $instance['exclude_post_ids'];
    $exclude_post_ids = apply_filters( 'popular_entries_widget_exclude_post_ids', $exclude_post_ids, $instance, $this->id_base );
    //除外カテゴリーIDを取得
    $exclude_cat_ids = empty($instance['exclude_cat_ids']) ? array() : $instance['exclude_cat_ids'];
    $exclude_cat_ids = apply_filters( 'popular_entries_widget_exclude_cat_ids', $exclude_cat_ids, $instance, $this->id_base );

    $cat_ids = array();
    if ($widget_mode == 'category') {
      $cat_ids = get_category_ids();//カテゴリ配列の取得
      //_v($cat_ids);
    }
    //除外投稿ID配列のサニタイズ
    $exclude_post_ids = comma_text_to_array($exclude_post_ids);
    // $exclude_post_ids = str_replace(' ', '', $exclude_post_ids);
    // if (empty($exclude_post_ids)) {
    //   $exclude_post_ids = array();
    // } else {
    //   $exclude_post_ids = explode(',', $exclude_post_ids);
    // }

    //除外カテゴリ配列のサニタイズ
    //$exclude_cat_ids = comma_text_to_array($exclude_cat_ids);
    if (empty($exclude_cat_ids)) {
      $exclude_cat_ids = array();
    } else {
      if (!is_array($exclude_cat_ids)) {
        $exclude_cat_ids = explode(',', $exclude_cat_ids);
      }
    }

    //classにwidgetと一意となるクラス名を追加する
    if ( //「表示モード」が「全ての人気記事」のとき
               ($widget_mode == WM_DEFAULT) ||
               //「表示モード」が「カテゴリ別人気記事」のとき
               ( ($widget_mode == 'category') && get_category_ids() ) ):
      echo $args['before_widget'];
      if (!is_null($title)) {
        echo $args['before_title'];
        if ($title) {
          echo $title;//タイトルが設定されている場合は使用する
        } else {
          if ( $widget_mode == WM_DEFAULT ) {//全ての表示モードの時は
            _e( '人気記事', THEME_NAME );
          } else {
            _e( 'カテゴリー別人気記事', THEME_NAME );
          }
        }
        echo $args['after_title'];
      }


      //get_template_part('tmp/popular-entries');
      $atts = array(
        'days' => $count_days,
        'entry_count' => $entry_count,
        'entry_type' => $entry_type,
        'ranking_visible' => $ranking_visible,
        'pv_visible' => $pv_visible,
        'bold' => $is_bold,
        'arrow' => $is_arrow_visible,
        'cat_ids' => $cat_ids,
        'exclude_post_ids' => $exclude_post_ids,
        'exclude_cat_ids' => $exclude_cat_ids,
      );
      generate_popular_entries_tag($atts);
      //_v($atts);
      //generate_popular_entries_tag($count_days, $entry_count, $entry_type, $ranking_visible, $pv_visible, $cat_ids, $exclude_post_ids, $exclude_cat_ids);

      echo $args['after_widget']; ?>
    <?php endif; ?>
  <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    if (isset($new_instance['widget_mode']))
      $instance['widget_mode'] = strip_tags($new_instance['widget_mode']);
    if (isset($new_instance['title']))
      $instance['title'] = strip_tags($new_instance['title']);
    if (isset($new_instance['entry_count']))
      $instance['entry_count'] = strip_tags($new_instance['entry_count']);
    if (isset($new_instance['entry_type']))
      $instance['entry_type'] = strip_tags($new_instance['entry_type']);
    if (isset($new_instance['count_days']))
      $instance['count_days'] = strip_tags($new_instance['count_days']);

    $instance['ranking_visible'] = !empty($new_instance['ranking_visible']) ? 1 : 0;
    $instance['pv_visible'] = !empty($new_instance['pv_visible']) ? 1 : 0;
    $instance['is_bold'] = !empty($new_instance['is_bold']) ? 1 : 0;
    $instance['is_arrow_visible'] = !empty($new_instance['is_arrow_visible']) ? 1 : 0;

    if (isset($new_instance['exclude_post_ids']))
      $instance['exclude_post_ids'] = strip_tags($new_instance['exclude_post_ids']);
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
        'widget_mode' => WM_DEFAULT,
        'title' => '',
        'entry_count' => EC_DEFAULT,
        'entry_type' => ET_DEFAULT,
        'count_days' => PCD_DEFAULT,
        'ranking_visible' => 0,
        'pv_visible' => 0,
        'is_bold' => 0,
        'is_arrow_visible' => 0,
        'exclude_post_ids' => '',
        'exclude_cat_ids' => array(),
      );
    }
    $widget_mode = isset($instance['widget_mode']) ? esc_attr($instance['widget_mode']) : WM_DEFAULT;
    $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
    $entry_count = isset($instance['entry_count']) ? esc_attr($instance['entry_count']) : EC_DEFAULT;
    $entry_type = isset($instance['entry_type']) ? esc_attr($instance['entry_type']) : ET_DEFAULT;
    $count_days = isset($instance['count_days']) ? esc_attr($instance['count_days']) : PCD_DEFAULT;
    $ranking_visible = !empty($instance['ranking_visible']) ? 1 : 0;
    $pv_visible = !empty($instance['pv_visible']) ? 1 : 0;
    $is_bold = !empty($instance['is_bold']) ? 1 : 0;
    $is_arrow_visible = !empty($instance['is_arrow_visible']) ? 1 : 0;
    $exclude_post_ids = isset($instance['exclude_post_ids']) ? esc_attr($instance['exclude_post_ids']) : '';
    $exclude_cat_ids = isset($instance['exclude_cat_ids']) ? $instance['exclude_cat_ids'] : array();
    //_v($exclude_cat_ids);
    //var_dump($instance);
    ?>
    <?php //ウィジェットモード（全てか、カテゴリ別か） ?>
    <p>
      <label for="<?php echo $this->get_field_id('widget_mode'); ?>">
        <?php _e( '表示モード', THEME_NAME ) ?>
      </label><br />
      <input class="widefat" id="<?php echo $this->get_field_id('widget_mode'); ?>" name="<?php echo $this->get_field_name('widget_mode'); ?>"  type="radio" value="all" <?php echo ( ($widget_mode == WM_DEFAULT || !$widget_mode ) ? ' checked="checked"' : ""); ?> /><?php _e( '全ての人気記事（全ページで表示）', THEME_NAME ) ?><br />
      <input class="widefat" id="<?php echo $this->get_field_id('widget_mode'); ?>" name="<?php echo $this->get_field_name('widget_mode'); ?>"  type="radio" value="category"<?php echo ($widget_mode == 'category' ? ' checked="checked"' : ""); ?> /><?php _e( 'カテゴリ別人気記事（投稿・カテゴリで表示）', THEME_NAME ) ?><br />
    </p>
    <?php //タイトル入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>">
        <?php _e( '人気記事のタイトル', THEME_NAME ) ?>
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
    <?php //表示タイプ ?>
    <p>
      <?php
      generate_label_tag($this->get_field_id('entry_type'), __('表示タイプ', THEME_NAME) );
      echo '<br>';
      $options = get_widget_entry_type_options();
      generate_radiobox_tag($this->get_field_name('entry_type'), $options, $entry_type);
      ?>
    </p>
    <?php //集計期間 ?>
    <p>
      <?php
        generate_label_tag($this->get_field_id('count_days'), __('集計期間', THEME_NAME) );

        $options = array(
          '1' => __( '本日', THEME_NAME ),
          '7' => __( '7日間', THEME_NAME ),
          '30' => __( '30日間', THEME_NAME ),
          '365' => __( '1年間', THEME_NAME ),
          'all' => __( '全期間', THEME_NAME ),
        );
        generate_selectbox_tag($this->get_field_name('count_days'), $options, $count_days);
       ?>
    </p>
    <?php //ランキング表示 ?>
    <p>
      <?php
        generate_checkbox_tag($this->get_field_name('ranking_visible') , $ranking_visible, __( 'ランキング表示', THEME_NAME ));
       ?>
    </p>
    <?php //PV表示 ?>
    <p>
      <?php
        generate_checkbox_tag($this->get_field_name('pv_visible') , $pv_visible, __( 'PV表示', THEME_NAME ));
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
    <?php //除外投稿ID ?>
    <p>
      <label for="<?php echo $this->get_field_id('exclude_post_ids'); ?>">
        <?php _e( '除外投稿ID（カンマ区切りでIDを入力してください）', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('exclude_post_ids'); ?>" name="<?php echo $this->get_field_name('exclude_post_ids'); ?>" type="text" value="<?php echo $exclude_post_ids; ?>" />
    </p>
    <p>
    <?php //除外カテゴリーID ?>
      <label>
        <?php _e( '除外カテゴリーID（除外するものを選択してください）', THEME_NAME ) ?>
      </label>
      <?php echo generate_hierarchical_category_check_list(0, $this->get_field_name('exclude_cat_ids'), $exclude_cat_ids); ?>
    </p>
    <?php
  }
}
endif;
