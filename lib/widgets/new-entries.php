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
    $widget_mode = apply_filters( 'cocoon_new_widget_mode', empty($instance['widget_mode']) ? WM_DEFAULT : $instance['widget_mode'] );
    //タイトル名を取得
    $title_new = apply_filters( 'cocoon_new_widget_title_new', empty($instance['title_new']) ? '' : $instance['title_new'] );
    $title_new = apply_filters( 'cocoon_new_widget_title', $title_new, $instance, $this->id_base );
    //表示数を取得
    $entry_count = apply_filters( 'cocoon_new_widget_entry_count', empty($instance['entry_count']) ? EC_DEFAULT : $instance['entry_count'] );
    //$is_top_visible = apply_filters( 'cocoon_new_widget_is_top_visible', empty($instance['is_top_visible']) ? true : $instance['is_top_visible'] );
    $entry_type = apply_filters( 'cocoon_new_widget_entry_type', empty($instance['entry_type']) ? ET_DEFAULT : $instance['entry_type'] );
    $is_sticky_visible = apply_filters( 'cocoon_new_widget_is_sticky_visible', empty($instance['is_sticky_visible']) ? 0 : 1 );

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
      if ($title_new !== null) {
        echo $args['before_title'];
        if ($title_new) {
          echo $title_new;//タイトルが設定されている場合は使用する
        } else {
          if ( $widget_mode == WM_DEFAULT ) {//全ての表示モードの時は
            _e( '新着記事', THEME_NAME );
          } else {
            _e( 'カテゴリー別新着記事', THEME_NAME );
          }
          //echo '新着記事';
        }
        echo $args['after_title'];
      }


      //get_template_part('tmp/new-entries');
      //引数配列のセット
      $atts = array(
        'entry_count' => $entry_count,
        'cat_ids' => $categories,
        'entry_type' => $entry_type,
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
    $instance['widget_mode'] = strip_tags($new_instance['widget_mode']);
    $instance['title_new'] = strip_tags($new_instance['title_new']);
    $instance['entry_count'] = strip_tags($new_instance['entry_count']);
    $instance['entry_type'] = strip_tags($new_instance['entry_type']);
    $instance['is_sticky_visible'] = strip_tags($new_instance['is_sticky_visible']);
      return $instance;
  }
  function form($instance) {
    if(empty($instance)){
      $instance = array(
        'widget_mode' => WM_DEFAULT,
        'title_new'   => '',
        'entry_count' => EC_DEFAULT,
        'entry_type'  => ET_DEFAULT,
        'is_sticky_visible'  => 1,
      );
    }
    $widget_mode = WM_DEFAULT;
    $title_new   = '';
    $entry_count = EC_DEFAULT;
    $entry_type  = ET_DEFAULT;
    $is_sticky_visible  = 1;
    if (isset($instance['widget_mode']))
      $widget_mode = esc_attr($instance['widget_mode']);
    if (isset($instance['title_new']))
      $title_new = esc_attr($instance['title_new']);
    if (isset($instance['entry_count']))
      $entry_count = esc_attr($instance['entry_count']);
    if (isset($instance['entry_type']))
      $entry_type = esc_attr($instance['entry_type']);
    $is_sticky_visible = empty($instance['is_sticky_visible']) ? 0 : 1;
    ?>
    <?php //ウィジェットモード（全てか、カテゴリ別か） ?>
    <p>
      <label for="<?php echo $this->get_field_id('widget_mode'); ?>">
        <?php _e( '表示モード', THEME_NAME ) ?>
      </label><br />
      <input class="widefat" id="<?php echo $this->get_field_id('widget_mode'); ?>" name="<?php echo $this->get_field_name('widget_mode'); ?>"  type="radio" value="all" <?php echo ( ($widget_mode == WM_DEFAULT || !$widget_mode ) ? ' checked="checked"' : ""); ?> /><?php _e( '全ての新着記事（全ページで表示）', THEME_NAME ) ?><br />
      <input class="widefat" id="<?php echo $this->get_field_id('widget_mode'); ?>" name="<?php echo $this->get_field_name('widget_mode'); ?>"  type="radio" value="category"<?php echo ($widget_mode == 'category' ? ' checked="checked"' : ""); ?> /><?php _e( 'カテゴリ別新着記事（投稿・カテゴリで表示）', THEME_NAME ) ?><br />
    </p>
    <?php //タイトル入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('title_new'); ?>">
        <?php _e( '新着記事のタイトル', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('title_new'); ?>" name="<?php echo $this->get_field_name('title_new'); ?>" type="text" value="<?php echo $title_new; ?>" />
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
    <?php //固定表示記事を表示する ?>
    <p>
      <?php
        generate_label_tag($this->get_field_id('is_sticky_visible'), __('固定表示', THEME_NAME) );
        echo '<br>';
        generate_checkbox_tag($this->get_field_name('is_sticky_visible') , $is_sticky_visible, __( '「固定表示」記事を表示する', THEME_NAME ));
      ?>
    </p>
    <?php
  }
}
endif;
