<?php //ウィジェットの表示制御

///////////////////////////////////////
// ウィジェットフォーム
///////////////////////////////////////
add_filter( 'in_widget_form', 'display_widgets_in_widget_form', 10, 3 );
if ( !function_exists( 'display_widgets_in_widget_form' ) ):
function display_widgets_in_widget_form( $widget, $return, $instance ){
  //var_dump($widget);
  // var_dump($widget->get_settings());
  // var_dump($return);
  // var_dump($instance);
  $info = display_widgets_info_by_id( $widget->id );
  //var_dump($info);
  //値の初期化
  $widget_action_def = 'hide';
  $widget_categories_def = array();
  $widget_pages_def = array();
  $widget_authors_def = array();
  $widget_posts_def = '';
  $widget_fixed_pages_def = '';
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
    if (isset($info['widget_authors'])) {
      $widget_authors_def = $info['widget_authors'];
    }
    if (!empty($info['widget_posts'])) {
      $widget_posts_def = $info['widget_posts'];
    }
    if (!empty($info['widget_fixed_pages'])) {
      $widget_fixed_pages_def = $info['widget_fixed_pages'];
    }
  }

  $widget_action = isset( $instance['widget_action'] ) ? $instance['widget_action'] : $widget_action_def;
  $widget_categories = isset( $instance['widget_categories'] ) ? $instance['widget_categories'] : $widget_categories_def;
  $widget_pages = isset( $instance['widget_pages'] ) ? $instance['widget_pages'] : $widget_pages_def;
  $widget_authors = isset( $instance['widget_authors'] ) ? $instance['widget_authors'] : $widget_authors_def;
  $widget_posts = !empty( $instance['widget_posts'] ) ? $instance['widget_posts'] : $widget_posts_def;
  $widget_fixed_pages = !empty( $instance['widget_fixed_pages'] ) ? $instance['widget_fixed_pages'] : $widget_fixed_pages_def;

  ?>
  <?php
  //ウィジェットIDを取得
  $widget_id = $widget->id;
  //フィールドID
  $field_id = $widget->get_field_id('toggle-link');
  //var_dump($field_id);
  //ウィジェットナンバーを取得
  $widget_number = $widget->number;
  //ウィジェットをD&Dでエリアにドロップ時スクデットナンバーを取得できないときに無理やり取得する
  if (preg_match('/__i__/', $widget_id)) {
    foreach( $widget->get_settings() as $index => $settings ) {
      $widget_number = $index + 1;
    }
    $widget_id = str_replace('__i__', $widget_number, $widget_id);
  }
  //var_dump($widget_id);
  $toggle_name = 'tlink-'.$widget_id;
   ?>
    <div class="display-widgets-toggle toggle-link" id="<?php echo $toggle_name; ?>"><?php _e( '表示設定', THEME_NAME ) ?></div>
    <div class="display-widgets-area toggle-content">
      <label for="<?php echo $widget->get_field_id('widget_action'); ?>">
        <?php esc_html_e('ウィジェットの表示', THEME_NAME) ?>
      </label>
      <?php
        $options = array(
          'hide' => __( 'チェック・入力したページで非表示', THEME_NAME ),
          'show' => __( 'チェック・入力したページで表示', THEME_NAME ),
        );
        generate_selectbox_tag($widget->get_field_name('widget_action'), $options, $widget_action);?>
        <div id="tabs-<?php echo $widget_id; ?>" class="tabs-widget">
        <ul>
          <li id="cat-<?php echo $widget_id; ?>" class="cat-tab"><?php _e( 'カテゴリー', THEME_NAME ) ?></li>
          <li id="page-<?php echo $widget_id; ?>" class="page-tab"><?php _e( 'ページ', THEME_NAME ) ?></li>
          <li id="author-<?php echo $widget_id; ?>" class="author-tab"><?php _e( '著者', THEME_NAME ) ?></li>
          <li id="post-<?php echo $widget_id; ?>" class="post-tab"><?php _e( '投稿', THEME_NAME ) ?></li>
          <li id="fixed-page-<?php echo $widget_id; ?>" class="fixed-page-tab"><?php _e( '固定ページ', THEME_NAME ) ?></li>
        </ul>
        <?php
        //echo get_hierarchical_category_check_list_box(0, $widget->get_field_name('widget_categories'), $widget_categories);
        generate_hierarchical_category_check_list(0, $widget->get_field_name('widget_categories'), $widget_categories);
        //var_dump($widget_pages);
        generate_page_display_check_list($widget->get_field_name('widget_pages'), $widget_pages);
        generate_author_check_list($widget->get_field_name('widget_authors'), $widget_authors);
        generate_post_check_list($widget->get_field_name('widget_posts'), $widget_posts);
        generate_fixed_page_check_list($widget->get_field_name('widget_fixed_pages'), $widget_fixed_pages);

        //_enqueue_script( 'tab-js-jquery', 'https://code.jquery.com/jquery.min.js');
       ?>
      </div>
    </div>

  <script type="text/javascript">
    // $(document).on("click", "#<?php echo $toggle_name; ?>", function(){
    //     $(this).next(".toggle-content").toggle();
    // });
    $(document).ready(function(){
      $("#<?php echo $toggle_name; ?>").click(function(){
        //console.log('a');
        $(this).next(".toggle-content").toggle();
      });
    });
  </script>
  <?php
  $parent = '#tabs-'.$widget_id;
   ?>
  <script type='text/javascript'>
    $(document).ready(function(){
      function set_tab_css(ele) {
        $('<?php echo $parent; ?> > ul > li').css('border-color', '#333');
        $(ele).css('border-color', '#aaa');
        $('<?php echo $parent; ?> > ul > li').css('background-color', '#f1f1f1');
        $(ele).css('background-color', '#fff');
      }
      //タブ制御
      $("#cat-<?php echo $widget_id; ?>").click(function(){
        set_tab_css(this);
        $('<?php echo $parent; ?> .category-check-list').show();
        $('<?php echo $parent; ?> .page-display-check-list').hide();
        $('<?php echo $parent; ?> .author-check-list').hide();
        $('<?php echo $parent; ?> .post-check-list').hide();
        $('<?php echo $parent; ?> .fixed-page-check-list').hide();
      });
      $("#page-<?php echo $widget_id; ?>").click(function(){
        set_tab_css(this);
        $('<?php echo $parent; ?> .category-check-list').hide();
        $('<?php echo $parent; ?> .page-display-check-list').show();
        $('<?php echo $parent; ?> .author-check-list').hide();
        $('<?php echo $parent; ?> .post-check-list').hide();
        $('<?php echo $parent; ?> .fixed-page-check-list').hide();
      });
      $("#author-<?php echo $widget_id; ?>").click(function(){
        set_tab_css(this);
        $('<?php echo $parent; ?> .category-check-list').hide();
        $('<?php echo $parent; ?> .page-display-check-list').hide();
        $('<?php echo $parent; ?> .author-check-list').show();
        $('<?php echo $parent; ?> .post-check-list').hide();
        $('<?php echo $parent; ?> .fixed-page-check-list').hide();
      });
      $("#post-<?php echo $widget_id; ?>").click(function(){
        set_tab_css(this);
        $('<?php echo $parent; ?> .category-check-list').hide();
        $('<?php echo $parent; ?> .page-display-check-list').hide();
        $('<?php echo $parent; ?> .author-check-list').hide();
        $('<?php echo $parent; ?> .post-check-list').show();
        $('<?php echo $parent; ?> .fixed-page-check-list').hide();
      });
      $("#fixed-page-<?php echo $widget_id; ?>").click(function(){
        set_tab_css(this);
        $('<?php echo $parent; ?> .category-check-list').hide();
        $('<?php echo $parent; ?> .page-display-check-list').hide();
        $('<?php echo $parent; ?> .author-check-list').hide();
        $('<?php echo $parent; ?> .post-check-list').hide();
        $('<?php echo $parent; ?> .fixed-page-check-list').show();
      });

    });
  </script>


<!--
  <script type="text/javascript">
    $('.tabs-widget li').on('click', function(e) {
      var index = $('.tabs-widget').index(this);
      console.log(index)
      //console.log(e.target);
      //_v($this);
    });
  </script> -->
<?php if ($widget->updated): ?>
<!--   <script type='text/javascript' src='//code.jquery.com/jquery.min.js'></script>
  <script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/jquery.tabs.js'></script>
  <script type='text/javascript'>
    tabify("#tabs-<?php echo $widget_id; ?>");
  </script>
  <script type="text/javascript">
    $(".toggle-link").click(function(){
      $(this).next(".toggle-content").toggle();
    });
  </script> -->
<?php else:
  // //タブの読み込み
  // wp_enqueue_script( 'tab-js-jquery', '//code.jquery.com/jquery.min.js', array( 'jquery' ), false, true );
  // wp_enqueue_script( 'tab-js', get_template_directory_uri() . '/js/jquery.tabs.js', array( 'tab-js-jquery' ), false, true );
  // $data = '$(document).ready( function() {
  //            tabify("#tabs-'.$widget_id.'");
  //          });';
  // wp_add_inline_script( 'tab-js', $data, 'after' ) ;

  //   //管理画面用のJavaScriptの読み込み
  //   wp_enqueue_script( 'admin-javascript', get_template_directory_uri() . '/js/admin-javascript.js', array( ), false, true );
endif ?>

  <?php

  return;
}
endif;

//ウィジェットのD&D時に適用するスクリプト
add_action( 'admin_footer', 'widget_custom_script' );
if ( !function_exists( 'widget_custom_script' ) ):
function widget_custom_script() {
?>
<script type="text/javascript">
jQuery(document).ready(function($){
  function loadScript(url) {
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = url;
  };

  function get_target_id(widgetContext, selector) {
    //console.log(ui);
    return "#"+$(widgetContext).find(selector)[0].id;
  }


  function set_click_events(ui) {
    //console.log(ui);
    $(ui).addClass('toggle-event-on');
    //var btn_id = "#"+$(ui.item.context).find(".toggle-link")[0].id;
    var btn_id = get_target_id(ui, ".toggle-link");
    $(document).on("click", btn_id, function(){
      $(this).next(".toggle-content").toggle();
    });


    var parent_id = get_target_id(ui, ".tabs-widget");
    var cat_tab_id = get_target_id(ui, ".cat-tab");
    var page_tab_id = get_target_id(ui, ".page-tab");
    var author_tab_id = get_target_id(ui, ".author-tab");
    var post_tab_id = get_target_id(ui, ".post-tab");
    var fixed_page_tab_id = get_target_id(ui, ".fixed-page-tab");


    function set_tab_css(ele) {
      $(parent_id+' > ul > li').css('border-color', '#333');
      $(ele).css('border-color', '#aaa');
      $(parent_id+' > ul > li').css('background-color', '#f1f1f1');
      $(ele).css('background-color', '#fff');
    }

    // var parent_id = "#"+$(ui.item.context).find(".tabs-widget")[0].id;
    // var cat_tab_id = "#"+$(ui.item.context).find(".cat-tab")[0].id;
    // var page_tab_id = "#"+$(ui.item.context).find(".page-tab")[0].id;
    // var author_tab_id = "#"+$(ui.item.context).find(".author-tab")[0].id;
    // console.log(cat_tab);
    // console.log(tabs_widget_id);
    $(document).on("click", cat_tab_id, function(){
      set_tab_css(this);
      $(parent_id+' .category-check-list').show();
      $(parent_id+' .page-display-check-list').hide();
      $(parent_id+' .author-check-list').hide();
      $(parent_id+' .post-check-list').hide();
      $(parent_id+' .fixed-page-check-list').hide();
    });
    $(document).on("click", page_tab_id, function(){
      set_tab_css(this);
      $(parent_id+' .category-check-list').hide();
      $(parent_id+' .page-display-check-list').show();
      $(parent_id+' .author-check-list').hide();
      $(parent_id+' .post-check-list').hide();
      $(parent_id+' .fixed-page-check-list').hide();
    });
    $(document).on("click", author_tab_id, function(){
      set_tab_css(this);
      $(parent_id+' .category-check-list').hide();
      $(parent_id+' .page-display-check-list').hide();
      $(parent_id+' .author-check-list').show();
      $(parent_id+' .post-check-list').hide();
      $(parent_id+' .fixed-page-check-list').hide();
    });
    $(document).on("click", post_tab_id, function(){
      set_tab_css(this);
      $(parent_id+' .category-check-list').hide();
      $(parent_id+' .page-display-check-list').hide();
      $(parent_id+' .author-check-list').hide();
      $(parent_id+' .post-check-list').show();
      $(parent_id+' .fixed-page-check-list').hide();
    });
    $(document).on("click", fixed_page_tab_id, function(){
      set_tab_css(this);
      $(parent_id+' .category-check-list').hide();
      $(parent_id+' .page-display-check-list').hide();
      $(parent_id+' .author-check-list').hide();
      $(parent_id+' .post-check-list').hide();
      $(parent_id+' .fixed-page-check-list').show();
    });

  }

  $('.widget-top').on('click', function(){
    //console.log('click');
    var widgetContext = $(this).context.parentNode;
    //console.log(widgetContext);
    set_click_events(widgetContext);
      // var id = $(this).parent.attr("id");
      // alert(id);
  });

  $('.display-widgets-toggle').on('click', function(){
    //console.log('display-widgets-toggle click');
    var widgetContext = $(this).context.parentNode;
    widgetContext = $(widgetContext).context.parentNode;
    widgetContext = $(widgetContext).context.parentNode;
    widgetContext = $(widgetContext).context.parentNode;
    //console.log(widgetContext);
    if (!$(widgetContext).hasClass('toggle-event-on')) {
      set_click_events(widgetContext);
    }

      // var id = $(this).parent.attr("id");
      // alert(id);
  });

  // 1. when dropped in
  $('div.widgets-sortables').bind('sortstop',function(event,ui){
    //console.log('just dropped in');
    //console.log(ui);
    //console.log(ui.item.context);
    var widgetContext = ui.item.context;
    set_click_events(widgetContext);
    // loadScript('http://cocoon.dev/wp-includes/js/thickbox/thickbox.js');
    // loadScript('http://cocoon.dev/wp-admin/js/media-upload.min.js');
  });
  // 2. do some stuff on load
  //console.log('onLoad');
  //console.log($);
  //set_click_events(ui);
  // 3. on action
  $(document).delegate('.our_widget_class', 'change', function(ev) {
    // you have to find the parent widget here,
    // and do something for it. And this is not easy
    // because the widget shouldn't have it's ID yet, but still possible.
    // This is actually the safest part of the whole process (maybe just for me)
    //console.log('the element changed');
  });
  // 4. on save
  $('body').ajaxSuccess(function(evt, request, settings) {
    //console.log('saved');
    //set_click_events(ui);
    //load_scripts();
  });
});
</script>
<?php
}
endif;

///////////////////////////////////////
// ウィジェット保存時のコールバック
///////////////////////////////////////
add_filter( 'widget_update_callback', 'display_widgets_update_callback', 10, 4);
if ( !function_exists( 'display_widgets_update_callback' ) ):
function display_widgets_update_callback( $instance, $new_instance, $old_instance, $this_widget ){
  //実行条件
  if ( isset( $new_instance['widget_action'] ) )
    $instance['widget_action'] = $new_instance['widget_action'];
  else
    $instance['widget_action'] = 'hide';

  //カテゴリ条件
  if ( isset( $new_instance['widget_categories'] ) )
    $instance['widget_categories'] = $new_instance['widget_categories'];
  else
    $instance['widget_categories'] = array();

  //ページ条件
  if ( isset( $new_instance['widget_pages'] ) )
    $instance['widget_pages'] = $new_instance['widget_pages'];
  else
    $instance['widget_pages'] = array();

  //投稿者条件
  if ( isset( $new_instance['widget_authors'] ) )
    $instance['widget_authors'] = $new_instance['widget_authors'];
  else
    $instance['widget_authors'] = array();

  //投稿ページ条件
  if ( isset( $new_instance['widget_posts'] ) )
    $instance['widget_posts'] = $new_instance['widget_posts'];
  else
    $instance['widget_posts'] = array();

  //固定ページ条件
  if ( isset( $new_instance['widget_fixed_pages'] ) )
    $instance['widget_fixed_pages'] = $new_instance['widget_fixed_pages'];
  else
    $instance['widget_fixed_pages'] = array();

  return $instance;
}
endif;

///////////////////////////////////////
// ウィジェットの表示制御
///////////////////////////////////////
if ( !function_exists( 'is_display_widgets_widget_visible' ) ):
function is_display_widgets_widget_visible( $info ){
  $widget_action = isset($info['widget_action']) ? $info['widget_action'] : 'hide';
  $widget_categories = isset($info['widget_categories']) ? $info['widget_categories'] : array();
  $widget_pages = isset($info['widget_pages']) ? $info['widget_pages'] : array();
  $widget_authors = isset($info['widget_authors']) ? $info['widget_authors'] : array();
  $widget_posts = isset($info['widget_posts']) ? $info['widget_posts'] : '';
  $widget_fixed_pages = isset($info['widget_fixed_pages']) ? $info['widget_fixed_pages'] : '';

  $display = false;
  // //ウィジェットを表示する条件
  // if ($widget_action == 'show') {
  //   if (empty($widget_categories)) {
  //     $display = false;
  //   } else {
  //     $display = in_category($widget_categories);// || is_category($widget_categories);
  //   }
  // } elseif ($widget_action == 'hide') {
  //   if (empty($widget_categories)) {
  //     $display = true;
  //   } else {
  //     $display = !(in_category($widget_categories) || is_category($widget_categories));
  //   }
  // }
//var_dump(($widget_categories));

  // //チェックリストすべてが空かどうか
  $is_all_empty = empty($widget_categories) && empty($widget_pages) && empty($widget_authors) && empty($widget_posts) && empty($widget_fixed_pages);
  //カテゴリーリストに何かチェックがついている場合
  if (!empty($widget_categories)) {
    $display = $display || in_category($widget_categories) || is_category($widget_categories);
    // if ($widget_action == 'hide') {
    //   $display = $display || is_single();
    // }
  }

  //ページリストに何かチェックがついている場合
  if (!empty($widget_pages)) {
    foreach ($widget_pages as $value) {
      switch ($value) {
        case 'is_single':
          $display = $display || is_single();
          break;
        case 'is_page':
          $display = $display || is_page();
          break;
        case 'is_category':
          $display = $display || is_category();
          break;
        case 'is_tag':
          $display = $display || is_tag();
          break;
        case 'is_author':
          $display = $display || is_author();
          break;
        case 'is_archive':
          $display = $display || is_archive();
          break;
        case 'is_search':
          $display = $display || is_search();
          break;
        case 'is_404':
          $display = $display || is_404();
          break;
      }
    }
  }

  //投稿者リストに何かチェックがついている場合
  if (!empty($widget_authors)) {
    $display = $display || in_authors($widget_authors) || is_authors($widget_authors);
    //var_dump($display);
    //var_dump(is_author(2));
  }

  //投稿IDが設定されているとき
  if (!empty($widget_posts)) {
    $widget_posts = sanitize_comma_text($widget_posts);
    $widget_posts = explode(',', $widget_posts);
    $display = $display || is_single($widget_posts);
  }

  //固定ページIDが設定されているとき
  if (!empty($widget_fixed_pages)) {
    $widget_fixed_pages = sanitize_comma_text($widget_fixed_pages);
    $widget_fixed_pages = explode(',', $widget_fixed_pages);
    $display = $display || is_page($widget_fixed_pages);
  }

  //ウィジェットを表示する条件
  if ($widget_action == 'show') {
    if ($is_all_empty) {
      $display = false;
    } else {
      $display = $display;
    }
  } elseif ($widget_action == 'hide') {
    if ($is_all_empty) {
      $display = true;
    } else {
      $display = !$display;
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