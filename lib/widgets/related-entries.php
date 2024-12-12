<?php
///////////////////////////////////////////////////
//新着エントリーウィジェットの追加
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
    );//ウィジェット名
  }
  function widget($args, $instance) {
    extract( $args );
    //タイトル名を取得
    $title = apply_filters( 'related_entries_widget_title', empty($instance['title']) ? '' : $instance['title'] );
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    //表示数を取得
    $entry_count = apply_filters( 'related_entries_widget_entry_count', empty($instance['entry_count']) ? EC_DEFAULT : $instance['entry_count'] );
    //表示タイプ
    $entry_type = apply_filters( 'related_entries_widget_entry_type', empty($instance['entry_type']) ? ET_DEFAULT : $instance['entry_type'] );
    //関連付け
    $taxonomy = apply_filters( 'related_entries_widget_taxonomy', empty($instance['taxonomy']) ? 'category' : $instance['taxonomy'] );
    //水平表示
    $is_horizontal = apply_filters( 'new_entries_widget_is_horizontal', empty($instance['is_horizontal']) ? 0 : 1 );
    //タイトルを太字にする
    $is_bold = apply_filters( 'related_entries_widget_is_bold', empty($instance['is_bold']) ? 0 : $instance['is_bold'] );
    //カードに矢印を表示する
    $is_arrow_visible = apply_filters( 'related_entries_widget_is_arrow_visible', empty($instance['is_arrow_visible']) ? 0 : $instance['is_arrow_visible'] );
    //除外カテゴリーIDを取得
    $exclude_cat_ids = empty($instance['exclude_cat_ids']) ? array() : $instance['exclude_cat_ids'];
    $exclude_cat_ids = apply_filters( 'related_entries_widget_exclude_cat_ids', $exclude_cat_ids, $instance, $this->id_base );

    //現在のカテゴリーを取得
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
    //タグの設定
    $tags = array();
    if ($taxonomy == 'post_tag') {
      global $post;
      if (isset($post->ID)) {
        $tags = get_the_tag_ids($post->ID);
      }
      // _v($tags);
      // _v($post->ID);
    }
    //タグがない場合はカテゴリーを表示
    if (!$tags) {
      $taxonomy = 'category';
    }

    //classにwidgetと一意となるクラス名を追加する
    if ( is_single() && get_category_ids() ):
      echo $args['before_widget'];
      if ($title) {
        echo $args['before_title'];
        echo $title;//タイトルが設定されている場合は使用する
        echo $args['after_title'];
      }

      if ($taxonomy == 'category') {
        //カテゴリーの時はタグを空にする
        $tags = array();
      } else {
        //タグの時はカテゴリーを空にする
        $categories = array();
      }


      //引数配列のセット
      $atts = array(
        'entry_count' => $entry_count,
        'cat_ids' => $categories,
        'tag_ids' => $tags,
        'type' => $entry_type,
        'horizontal' => $is_horizontal,
        'bold' => $is_bold,
        'arrow' => $is_arrow_visible,
        'include_children' => 0,
        'post_type' => 'post',
        'taxonomy' => $taxonomy,
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
    if (isset($new_instance['title']))
      $instance['title'] = strip_tags($new_instance['title']);
    if (isset($new_instance['entry_count']))
      $instance['entry_count'] = strip_tags($new_instance['entry_count']);
    if (isset($new_instance['entry_type']))
      $instance['entry_type'] = strip_tags($new_instance['entry_type']);
    if (isset($new_instance['taxonomy']))
      $instance['taxonomy'] = strip_tags($new_instance['taxonomy']);

    $instance['is_horizontal'] = !empty($new_instance['is_horizontal']) ? 1 : 0;
    $instance['is_bold'] = !empty($new_instance['is_bold']) ? 1 : 0;
    $instance['is_arrow_visible'] = !empty($new_instance['is_arrow_visible']) ? 1 : 0;
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
        'title'   => __( '関連記事', THEME_NAME ),
        'entry_count' => EC_DEFAULT,
        'entry_type'  => ET_DEFAULT,
        'taxonomy'  => 'category',
        'is_horizontal'  => 0,
        'is_bold'  => 0,
        'is_arrow_visible'  => 0,
        'exclude_cat_ids' => array(),
      );
    }
    $title = __( '関連記事', THEME_NAME );
    $entry_count = EC_DEFAULT;
    $entry_type  = ET_DEFAULT;
    $taxonomy = 'category';
    if (isset($instance['title']))
      $title = esc_attr($instance['title']);
    if (isset($instance['entry_count']))
      $entry_count = esc_attr($instance['entry_count']);
    if (isset($instance['entry_type']))
      $entry_type = esc_attr($instance['entry_type']);
    if (isset($instance['taxonomy']))
      $taxonomy = esc_attr($instance['taxonomy']);
      $is_horizontal = empty($instance['is_horizontal']) ? 0 : 1;
    $is_bold = empty($instance['is_bold']) ? 0 : 1;
    $is_arrow_visible = empty($instance['is_arrow_visible']) ? 0 : 1;

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
      <input class="widefat" id="<?php echo $this->get_field_id('entry_count'); ?>" name="<?php echo $this->get_field_name('entry_count'); ?>" type="number" value="<?php echo $entry_count ? $entry_count : EC_DEFAULT; ?>" min="1" />
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
    <?php //関連付け ?>
    <p>
      <?php
      generate_label_tag($this->get_field_id('taxonomy'), __('関連付け', THEME_NAME) );
      echo '<br>';
      $options = array(
        'category' => 'カテゴリー',
        'post_tag' => 'タグ（無い場合はカテゴリー表示）',
      );
      generate_radiobox_tag($this->get_field_name('taxonomy'), $options, $taxonomy);
      ?>
    </p>
    <?php //横並び表示にする ?>
    <p>
      <?php
        generate_checkbox_tag($this->get_field_name('is_horizontal') , $is_horizontal, __( '横並び表示にする', THEME_NAME ));
        _e( '（「大きなサムネイル」との使用がお勧め）', THEME_NAME )
      ?>
    </p>
    <?php //タイトルを太字にする ?>
    <p>
      <?php
        generate_checkbox_tag($this->get_field_name('is_bold') , $is_bold, __( 'タイトルを太字にする', THEME_NAME ));
      ?>
    </p>
    <?php //カードに矢印を表示する ?>
    <p>
      <?php
        generate_checkbox_tag($this->get_field_name('is_arrow_visible') , $is_arrow_visible, __( 'カードに矢印を表示する', THEME_NAME ));
      ?>
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
