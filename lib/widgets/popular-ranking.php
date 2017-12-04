<?php
///////////////////////////////////////////////////
//人気記事（Popular Posts）ウイジェットの追加
///////////////////////////////////////////////////
class PopularPostsCategoryWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'popular_ranking',
      __( '[S] 人気記事', 'simplicity2' ),
      array('description' => __( '人気記事リストを表示するSimplicityウィジェットです。（※要Wordpress Popular Postsプラグイン）', 'simplicity2' ))
    );
  }
  function widget($args, $instance) {
    extract( $args );

    //ウィジェットモード（全ての人気記事を表示するか、カテゴリ別に表示するか）
    $widget_mode = apply_filters( 'widget_mode', empty($instance['widget_mode']) ? "defailt" : $instance['widget_mode'], $instance );
    $title_popular = apply_filters( 'widget_title_popular', empty($instance['title_popular']) ? __( '人気記事', 'simplicity2' ) : $instance['title_popular'] );
    //表示数を取得
    $entry_count = apply_filters( 'widget_entry_count', empty($instance['entry_count']) ? 5 : absint($instance['entry_count']) );
    //表示タイプを取得
    $entry_type = apply_filters( 'widget_entry_count', empty($instance['entry_type']) ? "default" : $instance['entry_type'], $instance );
    //固定ページを含める
    $is_pages_include = apply_filters( 'widget_is_pages_include', empty($instance['is_pages_include']) ? "" : $instance['is_pages_include'], $instance );
    //閲覧数の表示
    $is_views_visible = apply_filters( 'widget_is_views_visible', empty($instance['is_views_visible']) ? "" : $instance['is_views_visible'], $instance );
    //集計期間
    $range = apply_filters( 'range', empty($instance['range']) ? "all" : $instance['range'], $instance );
    //集計期間の表示
    $range_visible = apply_filters( 'range_visible', empty($instance['range_visible']) ? "" : $instance['range_visible'], $instance );
    //ランキング順位の表示
    $is_ranking_visible = apply_filters( 'is_ranking_visible', empty($instance['is_ranking_visible']) ? "" : $instance['is_ranking_visible'], $instance );
    //除外ID
    $exclude_ids = apply_filters( 'exclude_ids', empty($instance['exclude_ids']) ? "" : $instance['exclude_ids'], $instance );
    //除外カテゴリID
    $exclude_category_ids = apply_filters( 'exclude_category_ids', empty($instance['exclude_category_ids']) ? "" : $instance['exclude_category_ids'], $instance );

    //後で使用するテンプレートファイルへの受け渡し
    //ウィジェットモード
    global $g_widget_mode;
    //表示数をグローバル変数に格納
    global $g_entry_count;
    //表示タイプをグローバル変数に格納
    global $g_entry_type;
    //ウィジェットモードが設定されてない場合はall（全て表示）にする
    if ( !$widget_mode ) $widget_mode = 'all';
    $g_widget_mode = $widget_mode;
    //表示数が設定されていない時は5にする
    if ( !$entry_count ) $entry_count = 5;
    $g_entry_count = $entry_count;
    //表示タイプのデフォルト設定
    if ( !$entry_type ) $entry_type = 'default';
    $g_entry_type = $entry_type;
    //固定ページを含めるかのデフォルト設定
    global $g_is_pages_include;
    $g_is_pages_include = $is_pages_include;
    //ページビュー表示に格納
    global $g_is_views_visible;
    $g_is_views_visible = $is_views_visible;
    global $g_range;
    $g_range = ($range ? $range : 'all');
    global $g_widget_item;
    $g_widget_item = 'PopularPostsCategoryWidgetItem';
    //除外ID
    global $g_exclude_ids;
    $g_exclude_ids = $exclude_ids;
    //除外カテゴリーID
    global $g_exclude_category_ids;
    $g_exclude_category_ids = $exclude_category_ids;
  ?>
    <?php if ( $widget_mode == 'all' || //モードがウィジェットモードが「すべての人気記事表示」の時
               is_single() || is_category() )://投稿ページとカテゴリーページのとき ?>
      <?php
      $before_widget = $args['before_widget'];
      if ($is_ranking_visible) {
        $before_widget = str_replace('widget_popular_ranking', 'widget_popular_ranking ranking_list', $before_widget);
      }
      echo $before_widget;
      echo $args['before_title'];
      if ($title_popular) {
        echo $title_popular;
      } else {
        if ( $widget_mode == 'all' ) {//全ての表示モードの時は
          echo __( '人気記事', 'simplicity2' );
        } else {
          echo __( 'カテゴリー別人気記事', 'simplicity2' );
        }
      }
      echo $args['after_title'];
      //PV順
      if ( $entry_type == 'default' ) {
        get_template_part('popular-posts-entries');
      } else {
        get_template_part('popular-posts-entries-large');
      }
      if ($range_visible) {
        echo get_range_tag($range);
      }
      echo $args['after_widget'];
    endif; //$widget_mode ?>
  <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['widget_mode'] = trim(strip_tags($new_instance['widget_mode']));
    $instance['title_popular'] = trim(strip_tags($new_instance['title_popular']));
    $instance['entry_count'] = strip_tags($new_instance['entry_count']);
    $instance['entry_type'] = strip_tags($new_instance['entry_type']);
    $instance['is_pages_include'] = strip_tags($new_instance['is_pages_include']);
    $instance['is_views_visible'] = $new_instance['is_views_visible'];
    $instance['range'] = $new_instance['range'];
    $instance['range_visible'] = $new_instance['range_visible'];
    $instance['is_ranking_visible'] = strip_tags($new_instance['is_ranking_visible']);
    $instance['exclude_ids'] = strip_tags($new_instance['exclude_ids']);
    $instance['exclude_category_ids'] = strip_tags($new_instance['exclude_category_ids']);
      return $instance;
  }
  function form($instance) {
    if(empty($instance)){
      $instance = array(
        'widget_mode' => null,
        'title_popular' => null,
        'entry_count' => null,
        'entry_type' => null,
        'is_pages_include' => null,
        'is_views_visible' => null,
        'range' => null,
        'range_visible' => null,
        'is_ranking_visible' => null,
        'exclude_ids' => null,
        'exclude_category_ids' => null,
      );
    }
    $widget_mode = esc_attr($instance['widget_mode']);
    $title_popular = esc_attr($instance['title_popular']);
    $entry_count = esc_attr($instance['entry_count']);
    $entry_type = esc_attr($instance['entry_type']);
    $is_pages_include = esc_attr($instance['is_pages_include']);
    $is_views_visible = esc_attr($instance['is_views_visible']);
    $range = esc_attr($instance['range']);
    $range_visible = esc_attr($instance['range_visible']);
    $is_ranking_visible = esc_attr($instance['is_ranking_visible']);
    $exclude_ids = esc_attr($instance['exclude_ids']);
    $exclude_category_ids = null;
    if (isset($instance['exclude_category_ids']))
      $exclude_category_ids = esc_attr($instance['exclude_category_ids']);
    ?>

    <?php //ウィジェットモード（全てか、カテゴリ別か） ?>
    <p>
      <label for="<?php echo $this->get_field_id('widget_mode'); ?>">
        <?php _e( '表示モード', 'simplicity2' ) ?>
      </label><br />
      <input class="widefat" id="<?php echo $this->get_field_id('widget_mode'); ?>" name="<?php echo $this->get_field_name('widget_mode'); ?>"  type="radio" value="all" <?php echo ( ($widget_mode == 'all' || !$widget_mode ) ? ' checked="checked"' : ""); ?> /><?php _e( '全ての人気記事（全ページで表示）', 'simplicity2' ) ?><br />
      <input class="widefat" id="<?php echo $this->get_field_id('widget_mode'); ?>" name="<?php echo $this->get_field_name('widget_mode'); ?>"  type="radio" value="category"<?php echo ($widget_mode == 'category' ? ' checked="checked"' : ""); ?> /><?php _e( 'カテゴリ別人気記事（投稿・カテゴリで表示）', 'simplicity2' ) ?><br />
    </p>
    <p>
       <label for="<?php echo $this->get_field_id('title_popular'); ?>">
        <?php _e( '人気記事のタイトル', 'simplicity2' ) ?>
       </label>
       <input class="widefat" id="<?php echo $this->get_field_id('title_popular'); ?>" name="<?php echo $this->get_field_name('title_popular'); ?>" type="text" value="<?php echo $title_popular; ?>" />
    </p>
    <?php //表示数入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('entry_count'); ?>">
        <?php _e( '表示数（半角数字、デフォルト：5）', 'simplicity2' ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('entry_count'); ?>" name="<?php echo $this->get_field_name('entry_count'); ?>" type="text" value="<?php echo $entry_count; ?>" />
    </p>
    <?php //表示タイプフォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('entry_type'); ?>">
        <?php _e( '表示タイプ', 'simplicity2' ) ?>
      </label><br />
      <input class="widefat" id="<?php echo $this->get_field_id('entry_type'); ?>" name="<?php echo $this->get_field_name('entry_type'); ?>"  type="radio" value="default" <?php echo ( ($entry_type == 'default' || !$entry_type ) ? ' checked="checked"' : ""); ?> /><?php _e( 'デフォルト', 'simplicity2' ) ?><br />
      <input class="widefat" id="<?php echo $this->get_field_id('entry_type'); ?>" name="<?php echo $this->get_field_name('entry_type'); ?>"  type="radio" value="large_thumb"<?php echo ($entry_type == 'large_thumb' ? ' checked="checked"' : ""); ?> /><?php _e( '大きなサムネイル', 'simplicity2' ) ?><br />
      <input class="widefat" id="<?php echo $this->get_field_id('entry_type'); ?>" name="<?php echo $this->get_field_name('entry_type'); ?>"  type="radio" value="large_thumb_on"<?php echo ($entry_type == 'large_thumb_on' ? ' checked="checked"' : ""); ?> /><?php _e( 'タイトルを重ねた大きなサムネイル', 'simplicity2' ) ?><br />
    </p>
    <?php //固定ページの表示 ?>
    <p>
      <label for="<?php echo $this->get_field_id('is_pages_include'); ?>">
        <?php _e( '固定ページの表示', 'simplicity2' ) ?>
      </label><br />
      <input class="widefat" id="<?php echo $this->get_field_id('is_pages_include'); ?>" name="<?php echo $this->get_field_name('is_pages_include'); ?>" type="checkbox" value="on"<?php echo ($is_pages_include ? ' checked="checked"' : ''); ?> /><?php _e( 'ランキングに固定ページを含める', 'simplicity2' ) ?>
    </p>
    <?php //集計単位の指定 ?>
    <p>
      <label for="<?php echo $this->get_field_id('range'); ?>">
        <?php _e( '集計単位', 'simplicity2' ) ?>
      </label><br />
      <input class="widefat" id="<?php echo $this->get_field_id('range'); ?>" name="<?php echo $this->get_field_name('range'); ?>" type="radio" value="daily"<?php echo ($range == 'daily' ? ' checked="checked"' : ''); ?> /><?php _e( '1日', 'simplicity2' ) ?><br />
      <input class="widefat" id="<?php echo $this->get_field_id('range'); ?>" name="<?php echo $this->get_field_name('range'); ?>" type="radio" value="weekly"<?php echo ($range == 'weekly' ? ' checked="checked"' : ''); ?> /><?php _e( '1週間', 'simplicity2' ) ?><br />
      <input class="widefat" id="<?php echo $this->get_field_id('range'); ?>" name="<?php echo $this->get_field_name('range'); ?>" type="radio" value="monthly"<?php echo ($range == 'monthly' ? ' checked="checked"' : ''); ?> /><?php _e( '1ヶ月', 'simplicity2' ) ?><br />
      <input class="widefat" id="<?php echo $this->get_field_id('range'); ?>" name="<?php echo $this->get_field_name('range'); ?>" type="radio" value="all"<?php echo ($range == null || $range == 'all' ? ' checked="checked"' : ''); ?> /><?php _e( '全期間', 'simplicity2' ) ?><br />
    </p>
    <?php //集計単位の表示 ?>
    <p>
      <label for="<?php echo $this->get_field_id('range_visible'); ?>">
        <?php _e( '集計期間の表示', 'simplicity2' ) ?>
      </label><br />
      <input class="widefat" id="<?php echo $this->get_field_id('range_visible'); ?>" name="<?php echo $this->get_field_name('range_visible'); ?>" type="checkbox" value="on"<?php echo ($range_visible ? ' checked="checked"' : ''); ?> /><?php _e( '集計単位の表示', 'simplicity2' ) ?>
    </p>
    <?php //閲覧数の表示 ?>
    <p>
      <label for="<?php echo $this->get_field_id('is_not_top_visible'); ?>">
        <?php _e( '閲覧数の表示', 'simplicity2' ) ?>
      </label><br />
      <input class="widefat" id="<?php echo $this->get_field_id('is_views_visible'); ?>" name="<?php echo $this->get_field_name('is_views_visible'); ?>" type="checkbox" value="on"<?php echo ($is_views_visible ? ' checked="checked"' : ''); ?> /><?php _e( '閲覧数の表示', 'simplicity2' ) ?>
    </p>
    <?php //ランキング順位の表示 ?>
    <p>
      <label for="<?php echo $this->get_field_id('is_ranking_visible'); ?>">
        <?php _e( 'ランキング順位の表示', 'simplicity2' ) ?>
      </label><br />
      <input class="widefat" id="<?php echo $this->get_field_id('is_ranking_visible'); ?>" name="<?php echo $this->get_field_name('is_ranking_visible'); ?>" type="checkbox" value="on"<?php echo ($is_ranking_visible ? ' checked="checked"' : ''); ?> /><?php _e( 'ランキング順位の表示', 'simplicity2' ) ?>
    </p>
    <?php //除外する投稿ID（カンマ区切りで指定） ?>
    <p>
       <label for="<?php echo $this->get_field_id('exclude_ids'); ?>">
        <?php _e( '除外する投稿ID（カンマ区切りで指定）', 'simplicity2' ) ?>
       </label>
       <input class="widefat" id="<?php echo $this->get_field_id('exclude_ids'); ?>" name="<?php echo $this->get_field_name('exclude_ids'); ?>" type="text" value="<?php echo $exclude_ids; ?>" />
    </p>
    <?php //除外するカテゴリID（カンマ区切りで指定） ?>
    <p>
       <label for="<?php echo $this->get_field_id('exclude_category_ids'); ?>">
        <?php _e( '除外するカテゴリID（カンマ区切りで指定）', 'simplicity2' ) ?>
       </label>
       <input class="widefat" id="<?php echo $this->get_field_id('exclude_category_ids'); ?>" name="<?php echo $this->get_field_name('exclude_category_ids'); ?>" type="text" value="<?php echo $exclude_category_ids; ?>" />
    </p>
    <?php
  }
}
//Wordpress Popular Postsが有効になっていない場合は表示しない
if ( is_wpp_enable() ):
  //add_action('widgets_init', create_function('', 'return register_widget("PopularPostsCategoryWidgetItem");'));
  add_action('widgets_init', function(){register_widget('PopularPostsCategoryWidgetItem');});
endif;