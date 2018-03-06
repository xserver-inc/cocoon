<?php
///////////////////////////////////////////////////
//人気エントリーウイジェットの追加
///////////////////////////////////////////////////
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
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    //表示数を取得
    $entry_count = apply_filters( 'widget_entry_count', empty($instance['entry_count']) ? EC_DEFAULT : $instance['entry_count'] );
    //表示タイプ
    $entry_type = apply_filters( 'widget_entry_type', empty($instance['entry_type']) ? ET_DEFAULT : $instance['entry_type'] );
    //集計期間
    $count_days = apply_filters( 'widget_count_days', empty($instance['count_days']) ? PCD_DEFAULT : $instance['count_days'] );
    //ランキング表示
    $ranking_visible = apply_filters( 'widget_ranking_visible', empty($instance['ranking_visible']) ? 0 : $instance['ranking_visible'] );
    //PV表示
    $pv_visible = apply_filters( 'widget_pv_visible', empty($instance['pv_visible']) ? 0 : $instance['pv_visible'] );

    $cat_ids = array();
    if ($widget_mode == 'category') {
      $cat_ids = get_category_ids();//カテゴリ配列の取得
    }
    //var_dump($cat_ids);
    // //表示数をグローバル変数に格納
    // //ウィジェットモード
    // global $_WIDGET_MODE;
    // //後で使用するテンプレートファイルへの受け渡し
    // global $_ENTRY_COUNT;
    // //集計期間をグローバル変数に格納
    // global $_COUNT_DAYS;
    // //ウィジェットモードが設定されてない場合はall（全て表示）にする
    // if ( !$widget_mode ) $widget_mode = WM_DEFAULT;
    // $_WIDGET_MODE = $widget_mode;
    // //表示数が設定されていない時は5にする
    // if ( !$entry_count ) $entry_count = EC_DEFAULT;
    // $_ENTRY_COUNT = $entry_count;
    //表示タイプをグローバル変数に格納
    global $_ENTRY_TYPE;
    //表示タイプのデフォルト設定
    if ( !$entry_type ) $entry_type = ET_DEFAULT;
    $_ENTRY_TYPE = $entry_type;
    // //表示タイプのデフォルト設定
    // if ( !$count_days ) $count_days = PCD_DEFAULT;
    // $_COUNT_DAYS = $count_days;
    //表示タイプをグローバル変数に格納
    global $_RANKING_VISIBLE;
    //表示タイプのデフォルト設定
    if ( !$ranking_visible ) $ranking_visible = 0;
    $_RANKING_VISIBLE = $ranking_visible;

    //_v($count_days);

    //classにwidgetと一意となるクラス名を追加する
    if ( //「表示モード」が「全ての人気記事」のとき
               ($widget_mode == WM_DEFAULT) ||
               //「表示モード」が「カテゴリ別人気記事」のとき
               ( ($widget_mode == 'category') && get_category_ids() ) ):
      echo $args['before_widget'];
      if ($title !== null) {
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
      generate_popular_entries_tag($count_days, $entry_count, $entry_type, $ranking_visible, $pv_visible, $cat_ids);

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
    if (isset($new_instance['ranking_visible']))
      $instance['ranking_visible'] = strip_tags($new_instance['ranking_visible']);
    if (isset($new_instance['pv_visible']))
      $instance['pv_visible'] = strip_tags($new_instance['pv_visible']);

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
      );
    }
    $widget_mode = isset($instance['widget_mode']) ? esc_attr($instance['widget_mode']) : WM_DEFAULT;
    $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
    $entry_count = isset($instance['entry_count']) ? esc_attr($instance['entry_count']) : EC_DEFAULT;
    $entry_type = isset($instance['entry_type']) ? esc_attr($instance['entry_type']) : ET_DEFAULT;
    $count_days = isset($instance['count_days']) ? esc_attr($instance['count_days']) : PCD_DEFAULT;
    $ranking_visible = isset($instance['ranking_visible']) ? esc_attr($instance['ranking_visible']) : 0;
    $pv_visible = isset($instance['pv_visible']) ? esc_attr($instance['pv_visible']) : 0;
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
    <?php //表示タイプフォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('entry_type'); ?>">
        <?php _e( '表示タイプ', THEME_NAME ) ?>
      </label><br />
      <input class="widefat" id="<?php echo $this->get_field_id('entry_type'); ?>" name="<?php echo $this->get_field_name('entry_type'); ?>"  type="radio" value="<?php echo ET_DEFAULT; ?>" <?php echo ( ($entry_type == ET_DEFAULT || !$entry_type ) ? ' checked="checked"' : ""); ?> /><?php _e( 'デフォルト', THEME_NAME ) ?><br />
      <input class="widefat" id="<?php echo $this->get_field_id('entry_type'); ?>" name="<?php echo $this->get_field_name('entry_type'); ?>"  type="radio" value="<?php echo ET_LARGE_THUMB; ?>"<?php echo ($entry_type == ET_LARGE_THUMB ? ' checked="checked"' : ""); ?> /><?php _e( '大きなサムネイル', THEME_NAME ) ?><br />
      <input class="widefat" id="<?php echo $this->get_field_id('entry_type'); ?>" name="<?php echo $this->get_field_name('entry_type'); ?>"  type="radio" value="<?php echo ET_LARGE_THUMB_ON; ?>"<?php echo ($entry_type == ET_LARGE_THUMB_ON ? ' checked="checked"' : ""); ?> /><?php _e( 'タイトルを重ねた大きなサムネイル', THEME_NAME ) ?><br />
    </p>
    <?php //集計期間 ?>
    <p>
      <?php
        generate_label_tag($this->get_field_id('count_days'), __('集計期間', THEME_NAME) );

        $options = array(
          '1' => __( '本日', THEME_NAME ),
          '7' => __( '7日間', THEME_NAME ),
          '30' => __( '30日間', THEME_NAME ),
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
    <?php
  }
}
if (is_access_count_enable()) {
  add_action('widgets_init', function(){register_widget('PopularEntryWidgetItem');});
}
