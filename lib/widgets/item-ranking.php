<?php
///////////////////////////////////////////////////
//アイテムランキングウィジェットの追加
///////////////////////////////////////////////////
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('widgets_init', function(){register_widget('ItemRankingWidgetItem');});
if ( !class_exists( 'ItemRankingWidgetItem' ) ):
class ItemRankingWidgetItem extends WP_Widget {
  function __construct() {
     parent::__construct(
      'item_ranking',
      WIDGET_NAME_PREFIX.__( 'ランキング', THEME_NAME ),//ウィジェット名
      array('description' => __( '商品・サービスのランキングを表示するウィジェットです。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );
  }
  function widget($args, $instance) {
    extract( $args );
    //タイトル名を取得
    $title = !empty($instance['title']) ? $instance['title'] : '';
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    $ranking_id = !empty($instance['ranking_id']) ? $instance['ranking_id'] : null;

    if ( !is_404() ): //パソコン表示かつ404ページでないとき
      echo $args['before_widget'];
      if ($title) {
        echo $args['before_title'].$title.$args['after_title'];//タイトルが設定されている場合は使用する
      } ?>
      <div class="item-ranking-box">
        <?php generate_item_ranking_tag($ranking_id); ?>
      </div>
      <?php echo $args['after_widget'];
    endif //!is_404 ?>
    <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    if (isset($new_instance['title']))
      $instance['title'] = strip_tags($new_instance['title']);
    if (isset($new_instance['ranking_id']))
      $instance['ranking_id'] = strip_tags($new_instance['ranking_id']);
    return $instance;
  }
  function form($instance) {
    if(empty($instance)){//notice回避
      $instance = array(
        'title' => null,
        'ranking_id' => null,
      );
    }
    $title = esc_attr(!empty($instance['title']) ? $instance['title'] : '');
    $ranking_id = !empty($instance['ranking_id']) ? $instance['ranking_id'] : null;
    ?>
    <?php //タイトル入力フォーム ?>
    <p>
      <?php
      generate_label_tag($this->get_field_id('title'), __('タイトル', THEME_NAME) );
      generate_textbox_tag($this->get_field_name('title'), $title, '');
       ?>
    </p>
    <?php //ランキングリスト ?>
    <p>
      <?php
      generate_label_tag($this->get_field_id('ranking_id'), __('ランキング', THEME_NAME) );
      $records = get_item_rankings(null, 'title');
      if ($records) {
        $options = array();
        foreach($records as $record){
          // //非表示の場合は跳ばす
          // if (!$record->visible) {
          //   continue;
          // }
          $options[$record->id] = $record->title;
        }
        generate_selectbox_tag($this->get_field_name('ranking_id'), $options, $ranking_id);
        generate_tips_tag(__( 'ランキングを選択してください。', THEME_NAME ));
      } else {?>
        <p><?php _e( 'ランキングが設定されていません。', THEME_NAME ) ?></p>
        <p><?php _e( '設定画面からランキングを設定してください→', THEME_NAME ) ?><a href="admin.php?page=theme-ranking"><?php _e( 'ランキング設定', THEME_NAME ) ?></a></p>
      <?php
      }

       ?>
    </p>
    <?php
  }
}
endif;
