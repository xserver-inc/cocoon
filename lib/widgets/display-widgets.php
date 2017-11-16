<?php //ウィジェットの表示制御

///////////////////////////////////////
// ウィジェットフォーム
///////////////////////////////////////
add_filter( 'in_widget_form', 'display_widgets_in_widget_form', 10, 3 );
if ( !function_exists( 'display_widgets_in_widget_form' ) ):
function display_widgets_in_widget_form( $widget, $return, $instance ){
  $info = display_widgets_info_by_id( $widget->id );
  //var_dump($info);
  //値の初期化
  $widget_action_def = 'hide';
  $widget_categories_def = array();
  $widget_pages_def = array();
  if ($info) {
    if (isset($info['widget_action'])) {
      $widget_action_def = $info['widget_action'];
    }
    if (isset($info['widget_categories'])) {
      $widget_categories_def = $info['widget_categories'];
    }
    if (isset($info['widget_pages'])) {
      $widget_pages_def = $info['widget_pages'];
    }
  }

  //$widget_logic = isset( $instance['widget_logic'] ) ? $instance['widget_logic'] : display_widgets_info_by_id( $widget->id );
  $widget_action = isset( $instance['widget_action'] ) ? $instance['widget_action'] : $widget_action_def;
  $widget_categories = isset( $instance['widget_categories'] ) ? $instance['widget_categories'] : $widget_categories_def;
  $widget_pages = isset( $instance['widget_pages'] ) ? $instance['widget_pages'] : $widget_pages_def;

  ?>
    <div class="display-widgets-area">
      <label for="<?php echo $widget->get_field_id('widget_action'); ?>">
        <?php esc_html_e('ウィジェットの表示', THEME_NAME) ?>
      </label>
      <?php
        $options = array(
          'hide' => __( 'チェックしたページで非表示', THEME_NAME ),
          'show' => __( 'チェックしたページで表示', THEME_NAME ),
        );
        generate_selectbox_tag($widget->get_field_name('widget_action'), $options, $widget_action);
        //echo get_hierarchical_category_check_list_box(0, $widget->get_field_name('widget_categories'), $widget_categories);
        generate_hierarchical_category_check_list(0, $widget->get_field_name('widget_categories'), $widget_categories);
        //var_dump($widget_pages);
        generate_page_display_check_list($widget->get_field_name('widget_pages'), $widget_pages);
       ?>
    </div>
  <?php
  return;
}
endif;

///////////////////////////////////////
// ウィジェット保存時のコールバック
///////////////////////////////////////
add_filter( 'widget_update_callback', 'display_widgets_update_callback', 10, 4);
if ( !function_exists( 'display_widgets_update_callback' ) ):
function display_widgets_update_callback( $instance, $new_instance, $old_instance, $this_widget ){
  if ( isset( $new_instance['widget_logic'] ) )
    $instance['widget_logic'] = $new_instance['widget_logic'];
  if ( isset( $new_instance['widget_action'] ) )
    $instance['widget_action'] = $new_instance['widget_action'];
  if ( isset( $new_instance['widget_categories'] ) )
    $instance['widget_categories'] = $new_instance['widget_categories'];
  if ( isset( $new_instance['widget_pages'] ) )
    $instance['widget_pages'] = $new_instance['widget_pages'];

  //var_dump($instance['widget_pages']);


  return $instance;
}
endif;

///////////////////////////////////////
// ウィジェットの表示制御
///////////////////////////////////////
if ( !function_exists( 'is_display_widgets_widget_visible' ) ):
function is_display_widgets_widget_visible( $info ){
  $widget_action = !empty($info['widget_action']) ? $info['widget_action'] : 'hide';
  $widget_categories = !empty($info['widget_categories']) ? $info['widget_categories'] : array();

  $display = true;
  //ウィジェットを表示する条件
  if ($widget_action == 'show') {
    if (empty($widget_categories)) {
      $display = false;
    } else {
      $display = is_category($widget_categories) || in_category($widget_categories);
    }
  } elseif ($widget_action == 'hide') {
    if (empty($widget_categories)) {
      $display = true;
    } else {
      $display = !is_category($widget_categories) && !in_category($widget_categories);
    }
  }
  // var_dump($widget_action);
  // var_dump($widget_categories);
  // var_dump($display);
  return $display;
}
endif;

///////////////////////////////////////
// ウィジェット表示フィルター（表示しない場合は取り除く）
///////////////////////////////////////
if (!is_admin()) {
  add_filter( 'sidebars_widgets', 'display_widgets_filter_sidebars_widgets', 10);
}
if ( !function_exists( 'display_widgets_filter_sidebars_widgets' ) ):
function display_widgets_filter_sidebars_widgets( $sidebars_widgets ){
  global $wp_reset_query_is_done, $wl_options, $wl_in_customizer;

  if ( $wl_in_customizer )
    return $sidebars_widgets;

  // reset any database queries done now that we're about to make decisions based on the context given in the WP query for the page
  if ( !empty( $wl_options['widget_logic-options-wp_reset_query'] ) && empty( $wp_reset_query_is_done ) )
  {
    wp_reset_query();
    $wp_reset_query_is_done = true;
  }

  // loop through every widget in every sidebar (barring 'wp_inactive_widgets') checking WL for each one
  foreach($sidebars_widgets as $widget_area => $widget_list)  {
    if ($widget_area=='wp_inactive_widgets' || empty($widget_list))
      continue;

    foreach($widget_list as $pos => $widget_id)    {
      //$logic = 'a';
      $info = display_widgets_info_by_id( $widget_id );
      //_v($info);

      if ( !is_display_widgets_widget_visible( $info ) )
        unset($sidebars_widgets[$widget_area][$pos]);
    }
  }
  return $sidebars_widgets;
}
endif;

///////////////////////////////////////
// ウィジェットの表示情報の取得
///////////////////////////////////////
if ( !function_exists( 'display_widgets_info_by_id' ) ):
function display_widgets_info_by_id( $widget_id ){
  global $wl_options;

  if ( preg_match( '/^(.+)-(\d+)$/', $widget_id, $m ) )  {
    $widget_class = $m[1];
    $widget_i = $m[2];

    $info = get_option( 'widget_'.$widget_class );
    if ( empty( $info[ $widget_i ] ) )
      return '';

    $info = $info[ $widget_i ];
  }
  else {
    $info = (array)get_option( 'widget_'.$widget_id, array() );
  }

  // //var_dump($info);
  // if ( isset( $info['widget_logic'] ) ){
  //   $logic = $info['widget_logic'];
  // }
  // elseif ( isset( $wl_options[ $widget_id ] ) )  {
  //   $logic = stripslashes( $wl_options[ $widget_id ] );
  //   widget_logic_save( $widget_id, $logic );

  //   unset( $wl_options[ $widget_id ] );
  //   update_option( 'widget_logic', $wl_options );
  // }
  // else {
  //   $logic = '';
  // }

  return $info;
}
endif;


// if ( !function_exists( 'display_widgets_info_by_id' ) ):
// function display_widgets_info_by_id( $widget_id ){
//   global $wl_options;

//   if ( preg_match( '/^(.+)-(\d+)$/', $widget_id, $m ) )  {
//     $widget_class = $m[1];
//     $widget_i = $m[2];

//     $info = get_option( 'widget_'.$widget_class );
//     if ( empty( $info[ $widget_i ] ) )
//       return '';

//     $info = $info[ $widget_i ];
//   }
//   else {
//     $info = (array)get_option( 'widget_'.$widget_id, array() );
//   }

//   // //var_dump($info);
//   // if ( isset( $info['widget_logic'] ) ){
//   //   $logic = $info['widget_logic'];
//   // }  elseif ( isset( $wl_options[ $widget_id ] ) )  {
//   //   // $logic = stripslashes( $wl_options[ $widget_id ] );
//   //   // widget_logic_save( $widget_id, $logic );

//   //   // unset( $wl_options[ $widget_id ] );
//   //   // update_option( 'widget_logic', $wl_options );
//   // } else {
//   //   $logic = '';
//   // }

//   return $info;
// }
// endif;