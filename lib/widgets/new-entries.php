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

add_action('widgets_init', function(){register_widget('NewEntryWidgetItem');});
if ( !class_exists( 'NewEntryWidgetItem' ) ):
class NewEntryWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'new_entries',
      WIDGET_NAME_PREFIX.__( '新着記事', THEME_NAME ),
      array('description' => __( '新着記事リストをサムネイルつきで表示するウィジェットです。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );//ウイジェット名
  }
  function widget($args, $instance) {
    extract( $args );
    //ウィジェットモード（全ての新着記事を表示するか、カテゴリ別に表示するか）
    $widget_mode = apply_filters( 'new_entries_widget_mode', empty($instance['widget_mode']) ? WM_DEFAULT : $instance['widget_mode'] );
    //タイトル名を取得
    $title = apply_filters( 'new_entries_widget_title', empty($instance['title']) ? '' : $instance['title'] );
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    //表示数を取得
    $entry_count = apply_filters( 'new_entries_widget_entry_count', empty($instance['entry_count']) ? EC_DEFAULT : $instance['entry_count'] );
    $entry_type = apply_filters( 'new_entries_widget_entry_type', empty($instance['entry_type']) ? ET_DEFAULT : $instance['entry_type'] );
    $is_bold = apply_filters( 'new_entries_widget_is_bold', empty($instance['is_bold']) ? 0 : 1 );
    $is_arrow_visible = apply_filters( 'new_entries_widget_is_arrow_visible', empty($instance['is_arrow_visible']) ? 0 : 1 );
    $is_sticky_visible = apply_filters( 'new_entries_widget_is_sticky_visible', empty($instance['is_sticky_visible']) ? 0 : 1 );

    //現在のカテゴリを取得
    $categories = array();
    if ($widget_mode == 'category') {
      $categories = get_category_ids();//カテゴリ配列の取得
    }


    //classにwidgetと一意となるクラス名を追加する
    if ( //「表示モード」が「全ての新着記事」のとき
              ($widget_mode == WM_DEFAULT) ||
              //「表示モード」が「カテゴリ別新着記事」のとき
              ( ($widget_mode == 'category') && get_category_ids() ) ):
      echo $args['before_widget'];
      if (!is_null($title)) {
        echo $args['before_title'];
        if ($title) {
          echo $title;//タイトルが設定されている場合は使用する
        } else {
          if ( $widget_mode == WM_DEFAULT ) {//全ての表示モードの時は
            echo apply_filters('new_entries_caption', __( '新着記事', THEME_NAME ));;
          } else {
            _e( 'カテゴリー別新着記事', THEME_NAME );
          }
        }
        echo $args['after_title'];
      }


      //get_template_part('tmp/new-entries');
      //引数配列のセット
      $atts = array(
        'entry_count' => $entry_count,
        'cat_ids' => $categories,
        'type' => $entry_type,
        'bold' => $is_bold,
        'arrow' => $is_arrow_visible,
        'sticky' => $is_sticky_visible,
      );
      //新着記事リストの作成
      generate_widget_entries_tag($atts);
      //generate_widget_entries_tag($entry_count, $entry_type, $categories);

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

    $instance['is_bold'] = !empty($new_instance['is_bold']) ? 1 : 0;
    $instance['is_arrow_visible'] = !empty($new_instance['is_arrow_visible']) ? 1 : 0;
    $instance['is_sticky_visible'] = !empty($new_instance['is_sticky_visible']) ? 1 : 0;
    return $instance;
  }
  function form($instance) {
    if(empty($instance)){
      $instance = array(
        'widget_mode' => WM_DEFAULT,
        'title'   => '',
        'entry_count' => EC_DEFAULT,
        'entry_type'  => ET_DEFAULT,
        'is_bold'  => 0,
        'is_arrow_visible'  => 0,
        'is_sticky_visible'  => 1,
      );
    }
    $widget_mode = WM_DEFAULT;
    $title   = '';
    $entry_count = EC_DEFAULT;
    $entry_type  = ET_DEFAULT;
    $is_sticky_visible  = 1;
    if (isset($instance['widget_mode']))
      $widget_mode = esc_attr($instance['widget_mode']);
    if (isset($instance['title']))
      $title = esc_attr($instance['title']);
    if (isset($instance['entry_count']))
      $entry_count = esc_attr($instance['entry_count']);
    if (isset($instance['entry_type']))
      $entry_type = esc_attr($instance['entry_type']);
    $is_bold = empty($instance['is_bold']) ? 0 : 1;
    $is_arrow_visible = empty($instance['is_arrow_visible']) ? 0 : 1;
    $is_sticky_visible = empty($instance['is_sticky_visible']) ? 0 : 1;
    ?>
    <?php //ウィジェットモード（全てか、カテゴリ別か） ?>
    <p>
      <?php
      generate_label_tag($this->get_field_id('widget_mode'), __('表示モード', THEME_NAME) );
      echo '<br>';
      $options = array(
        'all' => __( '全ての新着記事（全ページで表示）'),
        'category' => __( 'カテゴリ別新着記事（投稿・カテゴリで表示）'),
      );
      generate_radiobox_tag($this->get_field_name('widget_mode'), $options, $widget_mode);
      ?>
    </p>
    <?php //タイトル入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>">
        <?php _e( '新着記事のタイトル', THEME_NAME ) ?>
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
    <?php //固定表示記事を表示する ?>
    <p>
      <?php
        generate_checkbox_tag($this->get_field_name('is_sticky_visible') , $is_sticky_visible, __( '「固定表示」記事を表示', THEME_NAME ));
      ?>
    </p>
    <?php
  }
}
endif;
