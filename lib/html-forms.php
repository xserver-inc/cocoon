<?php //HTMLフォーム生成関数

//著者セレクトボックスをの取得
if ( !function_exists( 'get_author_list_selectbox_tag' ) ):
function get_author_list_selectbox_tag($name, $value){
  $users = get_users( array('orderby'=>'ID','order'=>'ASC') );
  $html = '<select id="'.$name.'" name="'.$name.'">'.PHP_EOL;
  foreach($users as $user) {
    $uid = $user->ID;
    if ($uid == intval($value)) {
      $selected = " selected";
    } else {
      $selected = null;
    }
    $html .= '  <option value="'.$uid.'"'.$selected.'>'.$user->display_name.'</option>'.PHP_EOL;
  } //foreach
  $html .= '</select>'.PHP_EOL;
  return $html;
}
endif;

//著者セレクトボックスをの取得
if ( !function_exists( 'generate_author_list_selectbox_tag' ) ):
function generate_author_list_selectbox_tag($name, $value){
  echo get_author_list_selectbox_tag($name, $value);
}
endif;



//チェックボックスのチェックを付けるか
if ( !function_exists( 'the_checkbox_checked' ) ):
function the_checkbox_checked($val1, $val2 = 1){
  if ( $val1 == $val2 ) {
    echo ' checked="checked"';
  }
}
endif;


//セレクトボックスのチェックを付けるか
if ( !function_exists( 'the_option_selected' ) ):
function the_option_selected($val1, $val2){
  if ($val1 == $val2) {
    echo ' selected="selected"';
  }
}
endif;


//セレクトボックスの生成
if ( !function_exists( 'generate_selectbox_tag' ) ):
function generate_selectbox_tag($name, $options, $now_value, $icon_font_visible = false){
$style = null;
if ($icon_font_visible) {
  $style = ' style="font-family: FontAwesome;font-size: 20px;text-align: center;"';
}
?>
<select name="<?php echo $name; ?>"<?php echo $style; ?>>
  <?php
  foreach ($options as $value => $caption) {
    //アイコンフォントを利用する場合
    $add_option_class = null;
    // if ($icon_font_visible) {
    //   $add_option_class = ' class="fa '.$caption.'"';
    // }
    ?>
    <option value="<?php echo $value; ?>"<?php the_option_selected($value, $now_value) ?><?php echo $add_option_class; ?>><?php echo $caption; ?></option>
  <?php } ?>
</select>
<?php
}
endif;


//チェックボックスの生成
if ( !function_exists( 'generate_checkbox_tag' ) ):
function generate_checkbox_tag($name, $now_value, $label){?>
  <input type="checkbox" name="<?php echo $name; ?>" value="1"<?php the_checkbox_checked($now_value); ?>><?php echo $label; ?>
  <?php
}
endif;


//ラジオボックスの生成
if ( !function_exists( 'generate_radiobox_tag' ) ):
function generate_radiobox_tag($name, $options, $now_value){?>
<ul>
  <?php foreach ($options as $value => $caption) {
  // _v($value.' == '.$now_value);
  // _v($value == $now_value);
   ?>
  <li><input type="radio" name="<?php echo $name; ?>" value="<?php echo $value; ?>"<?php the_checkbox_checked($value, $now_value) ?>><?php echo $caption; ?></li>
  <?php } ?>
</ul>
  <?php
}
endif;


//ラベルの生成
if ( !function_exists( 'generate_label_tag' ) ):
function generate_label_tag($name, $caption){?>
  <label for="<?php echo $name; ?>"><?php echo $caption; ?></label>
  <?php
}
endif;



//入力できないフォームクラスコードの生成
if ( !function_exists( 'get_not_allowed_form_class' ) ):
function get_not_allowed_form_class($is_not_allowed, $in = false){
  if (!$is_not_allowed) {
    if ($in) {
      return ' not-allowed-form';
    } else {
      return ' class="not-allowed-form"';
    }

  }
}
endif;

//入力できないフォームクラスコードの生成
if ( !function_exists( 'generate_not_allowed_form_class' ) ):
function generate_not_allowed_form_class($is_not_allowed, $in = false){
  echo get_not_allowed_form_class($is_not_allowed);
  // if (!$is_enable || empty($is_enable)) {
  //   echo ' class="not-allowed-form"';
  // }
}
endif;


//説明文の生成
if ( !function_exists( 'generate_tips_tag' ) ):
function generate_tips_tag($caption){?>
  <p class="tips"><?php echo $caption; ?></p>
  <?php
}
endif;


//アラート文の生成
if ( !function_exists( 'generate_alert_tag' ) ):
function generate_alert_tag($caption){?>
  <p class="alert"><?php echo $caption; ?></p>
  <?php
}
endif;


//ハウツー説明文の生成
if ( !function_exists( 'generate_howro_tag' ) ):
function generate_howro_tag($caption){?>
  <p class="howto"><?php echo $caption; ?></p>
  <?php
}
endif;

//解説ページへのリンク取得
if ( !function_exists( 'get_help_page_tag' ) ):
function get_help_page_tag($url){
  $tag = ' <a href="'.$url.'" target="_blank" class="help-page">'.__( '解説ページ', THEME_NAME ).'</a>';
  return $tag;
}
endif;


//通知メッセージのの生成
if ( !function_exists( 'generate_notice_message_tag' ) ):
function generate_notice_message_tag($caption){?>
  <div class="updated">
    <p>
      <strong>
        <?php echo $caption; ?>
      </strong>
    </p>
  </div>
  <?php
}
endif;

//エラーメッセージのの生成
if ( !function_exists( 'generate_error_message_tag' ) ):
function generate_error_message_tag($caption){?>
  <div class="error">
    <p>
      <strong>
        <?php echo $caption; ?>
      </strong>
    </p>
  </div>
  <?php
}
endif;


//テキストボックスの生成
if ( !function_exists( 'generate_textbox_tag' ) ):
function generate_textbox_tag($name, $value, $placeholder, $cols = DEFAULT_INPUT_COLS){?>
  <input type="text" name="<?php echo $name; ?>" size="<?php echo $cols; ?>" value="<?php echo $value; ?>" placeholder="<?php echo $placeholder; ?>">
  <?php
}
endif;

//テキストエリアの生成
if ( !function_exists( 'generate_textarea_tag' ) ):
function generate_textarea_tag($name, $value, $placeholder, $rows = DEFAULT_INPUT_ROWS,  $cols = DEFAULT_INPUT_COLS){?>
  <textarea name="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>" rows="<?php echo $rows; ?>" cols="<?php echo $cols; ?>"><?php echo $value; ?></textarea>
  <?php
}
endif;


//ナンバーボックスの生成
if ( !function_exists( 'generate_number_tag' ) ):
function generate_number_tag($name, $value, $placeholder = '', $min = 1, $max = 100, $step = 1){?>
  <input type="number" name="<?php echo $name; ?>" value="<?php echo $value; ?>" placeholder="<?php echo $placeholder; ?>" min="<?php echo $min; ?>" max="<?php echo $max; ?>" step="<?php echo $step; ?>">
  <?php
}
endif;


//チェックボックスの生成
if ( !function_exists( 'generate_sort_options_tag' ) ):
function generate_sort_options_tag($keyword = null, $order_by = null){?>
  <!-- 抽出フォーム -->
  <div class="sort-options">
    <form method="post" action="">
    <input type="text" name="s" value="<?php echo $keyword; ?>" placeholder="<?php _e( 'タイトル検索', THEME_NAME ) ?>">
    <?php

      $options = array(
        'title' => __( 'タイトル昇順', THEME_NAME ),
        'title DESC' => __( 'タイトル降順', THEME_NAME ),
        'date' => __( '作成日昇順', THEME_NAME ),
        'date DESC, id DESC' => __( '作成日降順', THEME_NAME ),
        'modified' => __( '編集日昇順', THEME_NAME ),
        'modified DESC, id DESC' => __( '編集日降順', THEME_NAME ),
      );
      generate_selectbox_tag('order', $options, $order_by);
     ?>
     <input type="submit" name="" value="<?php _e( '抽出', THEME_NAME ) ?>">
     </form>
  </div>
  <?php
}
endif;


//サイトロゴの生成
if ( !function_exists( 'generate_the_site_logo_tag' ) ):
function generate_the_site_logo_tag($is_header = true){
  $tag = 'div';
  if (!is_singular() && $is_header) {
    $tag = 'h1';
  }
  if ($is_header) {
    $class = ' logo-header';
  } else {
    $class = ' logo-footer';
  }
  if (get_the_site_logo_url()) {
    $class .= ' logo-image';
  } else {
    $class .= ' logo-text';
  }
  //ロゴの幅設定
  $site_logo_width = get_the_site_logo_width();
  $width_attr = null;
  if ($site_logo_width) {
    $width_attr = ' width="'.$site_logo_width.'"';
  }
  //ロゴの高さ設定
  $site_logo_height = get_the_site_logo_height();
  $height_attr = null;
  if ($site_logo_height) {
    $height_attr = ' height="'.$site_logo_height.'"';
  }


  $logo_before_tag = '<'.$tag.' class="logo'.$class.'"><a href="'.get_home_url().'" class="site-name site-name-text-link" itemprop="url"><span class="site-name-text" itemprop="name about">';
  $logo_after_tag = '</span></a></'.$tag.'>';
  if (get_the_site_logo_url()) {
    $site_logo_tag = '<img src="'.get_the_site_logo_url().'" alt="'.get_bloginfo('name').'"'.$width_attr.$height_attr.'>';
  } else {
    $site_logo_tag = get_bloginfo('name');
  }
  echo $logo_before_tag.$site_logo_tag.$logo_after_tag;
}
endif;




//ツールチップの生成
if ( !function_exists( 'generate_tooltip_tag' ) ):
function generate_tooltip_tag($content){?>
  <span class="tooltip fa fa-exclamation-triangle">
    <span class="tip-content">
      <?php echo $content; ?>
    </span>
  </span>
  <?php
}
endif;


//カラーピッカーの生成
if ( !function_exists( 'generate_color_picker_tag' ) ):
function generate_color_picker_tag($name, $value, $label){?>
  <p><label for="<?php echo $name; ?>"><?php echo $label; ?></label></p>
  <p><input type="text" name="<?php echo $name; ?>" value="<?php echo esc_html($value); ?>" ></p>
  <?php wp_enqueue_script( 'wp-color-picker' );
  $data = minify_js('(function( $ ) {
        var options = {
            defaultColor: false,
            change: function(event, ui){},
            clear: function() {},
            hide: true,
            palettes: true
        };
        $("input:text[name=\''.$name.'\']").wpColorPicker(options);
    })( jQuery );');
    wp_add_inline_script( 'wp-color-picker', $data, 'after' ) ;

}
endif;


//ビジュアルエディターの生成
if ( !function_exists( 'generate_visuel_editor_tag' ) ):
function generate_visuel_editor_tag($name, $content, $editor_id = 'wp_editor', $textarea_rows = 10){
  $settings = array(
    'textarea_name' => $name,
    'textarea_rows' => $textarea_rows,
  ); //配列としてデータを渡すためname属性を指定する
  wp_editor( $content, $editor_id, $settings );
}
endif;

//メインカラム広告の詳細設定フォーム
if ( !function_exists( 'generate_main_column_ad_detail_setting_forms' ) ):
function generate_main_column_ad_detail_setting_forms($name, $value, $label_name = null, $label_value = null, $body_ad_name = null, $body_ad_value = null){ ?>
 <span class="toggle">
  <span class="toggle-link"><?php _e( '詳細設定', THEME_NAME ) ?></span>
  <div class="toggle-content">
    <div class="detail-area">
    <?php _e( 'フォーマット：', THEME_NAME ) ?>
    <?php
    global $_MAIN_DATA_AD_FORMATS;
    $options = $_MAIN_DATA_AD_FORMATS;
    generate_selectbox_tag($name, $options, $value);
    //ラベル表示の設定
    if ($label_name) {
      echo '<p>';
      generate_checkbox_tag( $label_name, $label_value, __( '広告ラベルを表示', THEME_NAME ));
      echo '</p>';
    }

    //本文中広告用の設定
    if (isset($body_ad_name)){
      echo '<p>';
      generate_checkbox_tag( $body_ad_name, $body_ad_value, __( '全てのH2見出し手前に広告を挿入', THEME_NAME ));
      echo '</p>';
    }
    ?>
    </div>
  </div>
</span>
<?php
}
endif;

//サイドバー広告の詳細設定フォーム
if ( !function_exists( 'generate_sidebar_ad_detail_setting_forms' ) ):
function generate_sidebar_ad_detail_setting_forms($name, $value, $label_name, $label_value){ ?>
 <span class="toggle">
  <span class="toggle-link"><?php _e( '詳細設定', THEME_NAME ) ?></span>
  <div class="toggle-content">
    <div class="detail-area">
    <?php _e( 'フォーマット：', THEME_NAME ) ?>
    <?php
    global $_SIDEBAR_DATA_AD_FORMATS;
    $options = $_SIDEBAR_DATA_AD_FORMATS;
    generate_selectbox_tag($name, $options, $value);
    //ラベル表示の設定
    echo '<p>';
    generate_checkbox_tag( $label_name, $label_value, __( '広告ラベルを表示', THEME_NAME ));
    echo '</p>';
    ?>
    </div>
  </div>
</span>
<?php
}
endif;



//画像をアップロードボックス生成
if ( !function_exists( 'generate_upload_image_tag' ) ):
function generate_upload_image_tag($name, $value, $id = null){
  $thumb_id = isset($id) ? $id : $name;
  ?>
  <input name="<?php echo $name; ?>" type="text" value="<?php echo $value; ?>" />
  <input type="button" name="<?php echo $name; ?>_select" value="<?php _e( '選択', THEME_NAME ) ?>" />
  <input type="button" name="<?php echo $name; ?>_clear" value="<?php _e( 'クリア', THEME_NAME ) ?>" />
  <div id="<?php echo $thumb_id; ?>_thumbnail" class="uploded-thumbnail">
    <?php if ($value): ?>
      <img src="<?php echo $value; ?>" alt="選択中の画像">
    <?php endif ?>
  </div>
  <?php if (0/*$value*/): ?>
    <?php generate_tips_tag(__( '大きな画像は縮小して表示されます。', THEME_NAME )) ?>
  <?php endif ?>

  <script type="text/javascript">
  (function ($) {

      var custom_uploader;

      $("input:button[name='<?php echo $name; ?>_select']").click(function(e) {

          e.preventDefault();

          if (custom_uploader) {

              custom_uploader.open();
              return;

          }

          custom_uploader = wp.media({

              title: "<?php _e( '画像を選択してください', THEME_NAME ) ?>",

              /* ライブラリの一覧は画像のみにする */
              library: {
                  type: "image"
              },

              button: {
                  text: "<?php _e( '画像の選択', THEME_NAME ) ?>"
              },

              /* 選択できる画像は 1 つだけにする */
              multiple: false

          });

          custom_uploader.on("select", function() {

              var images = custom_uploader.state().get("selection");

              /* file の中に選択された画像の各種情報が入っている */
              images.each(function(file){

                  /* テキストフォームと表示されたサムネイル画像があればクリア */
                  $("input:text[name='<?php echo $name; ?>']").val("");
                  $("#<?php echo $thumb_id; ?>_thumbnail").empty();

                  /* テキストフォームに画像の URL を表示 */
                  $("input:text[name='<?php echo $name; ?>']").val(file.attributes.sizes.full.url);

                  /* プレビュー用に選択されたサムネイル画像を表示 */
                  $("#<?php echo $thumb_id; ?>_thumbnail").append('<img src="'+file.attributes.sizes.full.url+'" />');

              });
          });

          custom_uploader.open();

      });

      /* クリアボタンを押した時の処理 */
      $("input:button[name='<?php echo $name; ?>_clear']").click(function() {

          $("input:text[name='<?php echo $name; ?>']").val("");
          $("#<?php echo $thumb_id; ?>_thumbnail").empty();

      });

  })(jQuery);
  </script>
  <?php
}
endif;


//カテゴリチェックリストの作成
//require_once( ABSPATH . '/wp-admin/includes/template.php' );
//add_shortcode('frontend-category-checklist', 'frontend_category_checklist');
if ( !function_exists( 'generate_category_checklist' ) ):
function generate_category_checklist( $post_id = 0, $descendants_and_self = 0, $selected_cats = false,
        $popular_cats = false, $walker = null, $checked_ontop = true) {
    //wp_category_checklist()定義ファイルの呼び出し
    require_once( ABSPATH . '/wp-admin/includes/template.php' ); ?>
    <div class="category-checklist-wrap">
        <ul id="category-checklist" data-wp-lists="list:category" class="category-checklist form-no-clear">
            <?php wp_category_checklist(
                $post_id,
                $descendants_and_self,
                $selected_cats,
                $popular_cats,
                $walker,
                $checked_ontop
            );  ?>
        </ul>
    </div>
  <?php
}
endif;

// if ( !function_exists( 'get_hierarchical_category_check_list_box' ) ):
// function get_hierarchical_category_check_list_box( $cat, $name, $checks, $width = 0 ) {
//   if ($width == 0) {
//     $width = 'auto';
//   } else {
//     $width = $width.'px';
//   }
//   $res = '';
//   $res .= '<div class="category-check-list '.$name.'-list" style="width: '.$width.';">';
//   $res .= get_hierarchical_category_check_list( $cat, $name, $checks );
//   $res .= '</div>';
//   return $res;
// }
// endif;

// if ( !function_exists( 'get_hierarchical_category_check_list' ) ):
// function get_hierarchical_category_check_list( $cat, $name, $checks ) {
//     // wpse-41548 // alchymyth // a hierarchical list of all categories //

//   $next = get_categories('hide_empty=false&orderby=name&order=ASC&parent=' . $cat);
//   $res = '';
//   if( $next ) :
//     foreach( $next as $cat ) :
//       $checked = '';
//       if (is_string($checks)) {
//         $checks = array();
//       }
//       if (in_array($cat->term_id, $checks)) {
//         $checked = ' checked="checked"';
//       }
//      $res .= '<ul><li><input type="checkbox" name="'.$name.'[]" value="'.$cat->term_id.'"'.$checked.'>' . $cat->name . '';
//       $res .= get_hierarchical_category_check_list( $cat->term_id, $name, $checks );
//     endforeach;
//   endif;

//   $res .= '</li></ul>'; echo "\n";
//   return $res;
// }
// endif;


//階層化カテゴリチェックリストの出力
if ( !function_exists( 'generate_hierarchical_category_check_list' ) ):
function generate_hierarchical_category_check_list( $cat, $name, $checks, $width = 0 ) {
  if ($width == 0) {
    $width = 'auto';
  } else {
    $width = $width.'px';
  }
  echo '<div class="tab-content category-check-list '.$name.'-list" style="width: '.$width.';">';
  hierarchical_category_check_list( $cat, $name, $checks );
  echo '</div>';
}
endif;

//階層化カテゴリチェックリストの出力の再帰関数
if ( !function_exists( 'hierarchical_category_check_list' ) ):
function hierarchical_category_check_list( $cat, $name, $checks ) {
    // wpse-41548 // alchymyth // a hierarchical list of all categories //

  $next = get_categories('hide_empty=false&orderby=name&order=ASC&parent=' . $cat);

  if( $next ) :
    foreach( $next as $cat ) :
      $checked = '';
      if (is_string($checks)) {
        $checks = array();
      }
      // //デフォルトのカテゴリは誤動作を起こすので除外
      // if ($cat->term_id == 1) {
      //   continue;
      // }
      if (in_array($cat->term_id, $checks)) {
        $checked = ' checked="checked"';
      }
      echo '<ul><li><input type="checkbox" name="'.$name.'[]" value="'.$cat->term_id.'"'.$checked.'>' . $cat->name . '';
      hierarchical_category_check_list( $cat->term_id, $name, $checks );
    endforeach;
  endif;

  echo '</li></ul>'; echo "\n";
}
endif;

//ページ表示チェックリスト
if ( !function_exists( 'generate_page_display_check_list' ) ):
function generate_page_display_check_list( $name, $checks, $width = 0 ) {
  if ($width == 0) {
    $width = 'auto';
  } else {
    $width = $width.'px';
  }

  if (empty($checks)) {
    $checks = array();
  }

  echo '<div class="tab-content page-display-check-list '.$name.'-list" style="width: '.$width.';"><ul>';

  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_front_page" ';
  checked(in_array('is_front_page', $checks));
  echo '>' . __( 'トップページのみ', THEME_NAME ) . '</li>';

  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_single" ';
  checked(in_array('is_single', $checks));
  echo '>' . __( '投稿', THEME_NAME ) . '</li>';

  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_page" ';
  checked(in_array('is_page', $checks));
  echo '>' . __( '固定ページ', THEME_NAME ) . '</li>';

  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_category" ';
  checked(in_array('is_category', $checks));
  echo '>' . __( 'カテゴリ一覧', THEME_NAME ) . '</li>';

  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_tag" ';
  checked(in_array('is_tag', $checks));
  echo '>' . __( 'タグ一覧', THEME_NAME ) . '</li>';

  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_author" ';
  checked(in_array('is_author', $checks));
  echo '>' . __( '著者一覧', THEME_NAME ) . '</li>';

  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_archive" ';
  checked(in_array('is_archive', $checks));
  echo '>' . __( 'アーカイブ一覧', THEME_NAME ) . '</li>';

  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_search" ';
  checked(in_array('is_search', $checks));
  echo '>' . __( '検索結果一覧', THEME_NAME ) . '</li>';

  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_404" ';
  checked(in_array('is_404', $checks));
  echo '>' . __( '404ページ一覧', THEME_NAME ) . '</li>';

  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_amp" ';
  checked(in_array('is_amp', $checks));
  echo '>' . __( 'AMPページ', THEME_NAME ) . '</li>';

  if (is_wpforo_exist()) {
    echo '<li><input type="checkbox" name="'.$name.'[]" value="is_wpforo_plugin_page" ';
    checked(in_array('is_wpforo_plugin_page', $checks));
    echo '>' . __( 'wpForoページ', THEME_NAME ) . '</li>';
  }

  if (is_bbpress_exist()) {
    echo '<li><input type="checkbox" name="'.$name.'[]" value="is_bbpress_page" ';
    checked(in_array('is_bbpress_page', $checks));
    echo '>' . __( 'bbPressページ', THEME_NAME ) . '</li>';
  }

  if (is_buddypress_exist()) {
    echo '<li><input type="checkbox" name="'.$name.'[]" value="is_buddypress_page" ';
    checked(in_array('is_buddypress_page', $checks));
    echo '>' . __( 'BuddyPressページ', THEME_NAME ) . '</li>';
  }

  if (is_plugin_fourm_exist()) {
    echo '<li><input type="checkbox" name="'.$name.'[]" value="is_plugin_fourm_page" ';
    checked(in_array('is_plugin_fourm_page', $checks));
    echo '>' . __( 'プラグインフォーラムページ（bbPress、BuddyPress、wpForo）', THEME_NAME ) . '</li>';
  }

  echo '</ul></div>';
}
endif;

//投稿者チェックリスト
if ( !function_exists( 'generate_author_check_list' ) ):
function generate_author_check_list( $name, $checks, $width = 0 ) {
  if ($width == 0) {
    $width = 'auto';
  } else {
    $width = $width.'px';
  }

  if (empty($checks)) {
    $checks = array();
  }

  echo '<div class="tab-content author-check-list '.$name.'-list" style="width: '.$width.';"><ul>';

  $users = get_users( array('orderby'=>'ID','order'=>'ASC') );
  foreach($users as $user) {
    $uid = $user->ID;
    echo '<li><input type="checkbox" name="'.$name.'[]" value="'.$uid.'" ';
    checked(in_array($uid, $checks));
    echo '>' . $user->display_name . '</li>';
  } //foreach

  echo '</ul></div>';
}
endif;

//投稿ページリスト
if ( !function_exists( 'generate_post_check_list' ) ):
function generate_post_check_list( $name, $value, $width = 0 ) {
  if ($width == 0) {
    $width = 'auto';
  } else {
    $width = $width.'px';
  }

  echo '<div class="tab-content post-check-list '.$name.'-list" style="width: '.$width.';">';

  echo '<p>'.__( '投稿ID入力', THEME_NAME ).'</p>';
  generate_textbox_tag($name, $value, __( '例：111,222,333', THEME_NAME ));
  echo '<p>'.__( '投稿IDをカンマ区切りで入力してください。', THEME_NAME ).'</p>';

  echo '</div>';
}
endif;


//固定ページリスト
if ( !function_exists( 'generate_fixed_page_check_list' ) ):
function generate_fixed_page_check_list( $name, $value, $width = 0 ) {
  if ($width == 0) {
    $width = 'auto';
  } else {
    $width = $width.'px';
  }

  echo '<div class="tab-content fixed-page-check-list '.$name.'-list" style="width: '.$width.';">';

  echo '<p>'.__( '固定ページID入力', THEME_NAME ).'</p>';
  generate_textbox_tag($name, $value, __( '例：111,222,333', THEME_NAME ));
  echo '<p>'.__( '固定ページIDをカンマ区切りで入力してください。', THEME_NAME ).'</p>';

  echo '</div>';
}
endif;

//Windows Live Writerで編集するためのリンクを作成する
if ( !function_exists( 'wlw_edit_post_link' ) ):
function wlw_edit_post_link($link, $before = '', $after = ''){
  $query = ( is_single() ? 'postid' : 'pageid' );
  echo $before.'<a href="wlw://'.get_the_site_domain().'/?'.$query.'=';
  echo the_ID();
  echo '">WLWで編集</a>'.$after;
}
endif;

//人気ランキングリストの取得
if ( !function_exists( 'generate_popular_entries_tag' ) ):
function generate_popular_entries_tag($days = 'all', $entry_count = 5, $entry_type = ET_DEFAULT, $ranking_visible = 0, $pv_visible = 0, $categories = array()){
  // if (DEBUG_MODE) {
  //   $time_start = microtime(true);
  // }
  //var_dump($categories);

  $records = get_access_ranking_records($days, $entry_count, $entry_type, $categories);

  // if (DEBUG_MODE) {
  //   $time = microtime(true) - $time_start;
  //   echo('<pre>');
  //   echo($time);
  //   echo('</pre>');
  // }


  //var_dump($records);
  $thumb_size = get_popular_entries_thumbnail_size($entry_type);
  ?>
  <div class="popular-entry-cards widget-entry-cards cf<?php echo get_additional_popular_entriy_cards_classes($entry_type, $ranking_visible, $pv_visible, null); ?>">
  <?php if ( $records ) :
    foreach ($records as $post):
      $permalink = get_permalink( $post->ID );
      $title = $post->post_title;
      $no_thumbnail_url = get_template_directory_uri().'/images/no-image-320.png';
      $post_thumbnail = get_the_post_thumbnail( $post->ID, $thumb_size, array('alt' => '') );
      $pv = $post->sum_count;

      if ($post_thumbnail) {
        $post_thumbnail_img = $post_thumbnail;
      } else {
        $post_thumbnail_img = '<img src="'.$no_thumbnail_url.'" alt="NO IMAGE" class="no-image popular-entry-card-thumb-no-image widget-entry-card-thumb-no-image" width="320" height="180" />';
      }

      // $pv_tag = '';
      // if ($pv_visible) {
      //   $pv_tag = '<span class="popular-entry-card-pv widget-entry-card-pv">'.$post->sum_count.'</span>';
      // }
      //_v($post_thumbnail_img);

      //var_dump($permalink);
      ?>
  <a href="<?php echo $permalink; ?>" class="popular-entry-card-link a-wrap" title="<?php echo $title; ?>">
    <div class="popular-entry-card widget-entry-card e-card cf">
      <figure class="popular-entry-card-thumb widget-entry-card-thumb card-thumb">
      <?php echo $post_thumbnail_img; ?>
      </figure><!-- /.popular-entry-card-thumb -->

      <div class="popular-entry-card-content widget-entry-card-content card-content">
        <span class="popular-entry-card-title widget-entry-card-title card-title"><?php echo $title;?></span>
        <?php if ($pv_visible): ?>
          <span class="popular-entry-card-pv widget-entry-card-pv"><?php echo $pv == '1' ? $pv.'view' : $pv.'views';?></span>
        <?php endif ?>
      </div><!-- /.popular-entry-content -->
    </div><!-- /.popular-entry-card -->
  </a><!-- /.popular-entry-card-link -->

  <?php endforeach;
  else :
    echo '<p>'.__( '人気記事は見つかりませんでした。', THEME_NAME ).'</p>';//見つからない時のメッセージ
  endif; ?>
  </div>
<?php
}
endif;

if ( !function_exists( 'generate_new_entries_tag' ) ):
function generate_new_entries_tag($entry_count = 5, $entry_type = ET_DEFAULT, $categories = array(), $include_children = 0){

  $args = array(
    'posts_per_page' => $entry_count,
    'no_found_rows' => true,
  );
  if ( $categories ) {
    //_v($categories);
    $args += array(
      'tax_query' => array(
        array(
          'taxonomy' => 'category',
          'terms' => $categories,
          'include_children' => $include_children,
          'field' => 'term_id',
          'operator' => 'IN'
          ),
        'relation' => 'AND'
      )
    );
  }
  $thumb_size = get_new_entries_thumbnail_size($entry_type);
  //query_posts( $args ); //クエリの作成
  $query = new WP_Query( $args );
  ?>
  <div class="new-entry-cards widget-entry-cards cf<?php echo get_additional_new_entriy_cards_classes($entry_type); ?>">
  <?php //if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
  <?php if ( $query -> have_posts() ) : while ( $query -> have_posts() ) : $query -> the_post(); ?>
  <a href="<?php the_permalink(); ?>" class="new-entry-card-link widget-entry-card-link a-wrap" title="<?php the_title(); ?>">
    <div class="new-entry-card widget-entry-card e-card cf">
      <figure class="new-entry-card-thumb widget-entry-card-thumb card-thumb">
      <?php if ( has_post_thumbnail() ): // サムネイルを持っているときの処理 ?>
        <?php the_post_thumbnail( $thumb_size, array('alt' => '') ); ?>
      <?php else: // サムネイルを持っていないときの処理 ?>
        <img src="<?php echo get_template_directory_uri(); ?>/images/no-image-320.png" alt="NO IMAGE" class="no-image new-entry-card-thumb-no-image widget-entry-card-thumb-no-image" width="320" height="180" />
      <?php endif; ?>
      </figure><!-- /.new-entry-card-thumb -->

      <div class="new-entry-card-content widget-entry-card-content card-content">
        <div class="new-entry-card-title widget-entry-card-title card-title"><?php the_title();?></div>
      </div><!-- /.new-entry-content -->
    </div><!-- /.new-entry-card -->
  </a><!-- /.new-entry-card-link -->
  <?php endwhile;
  else :
    echo '<p>'.__( '新着記事は見つかりませんでした。', THEME_NAME ).'</p>';//見つからない時のメッセージ
  endif; ?>
  <?php wp_reset_postdata(); ?>
  <?php //wp_reset_query(); ?>
  </div>
<?php
}
endif;

//プロフィールボックス生成関数
if ( !function_exists( 'generate_author_box_tag' ) ):
function generate_author_box_tag($label){
  $author_id = get_the_author_meta( 'ID' );
  if (!$author_id || is_404()) {
    $author_id = get_sns_default_follow_user();
  }

  // $auther_class = ' author-admin';
  // if ($author_id) {
  //   $author = new WP_User( get_the_author_meta( $author_id ) );
  //   if ($author && !in_array( 'administrator', $author->roles )) {
  //     $auther_class = ' author-guest';
  //   }
  // }
  ?>
  <div class="author-box cf">
    <?php //ウィジェット名がある場合
    if ($label): ?>
      <div class="author-widget-name">
        <?php echo $label; ?>
      </div>
    <?php endif ?>
    <figure class="author-thumb">
      <?php echo get_avatar( $author_id, 200 ); ?>
    </figure>
    <div class="author-content">
      <div class="author-display-name">
        <?php
        if ($author_id) {
          $description = get_the_author_description();

          if (!is_buddypress_page()) {
            the_author_posts_link();
          } else {
            $author_display_name = strip_tags(get_the_author_display_name());
            $author_website_url = strip_tags(get_the_author_website_url());
            $description = strip_tags($description);
            $name = $author_display_name;
            if ($author_website_url) {
              $name = '<a href="'.$author_website_url.'" target="_blank" rel="nofollow">'.$author_display_name.'</a>';
            }
            echo $name;
          }


        } else {
          echo __( '未登録のユーザーさん', THEME_NAME );
        }
         ?>
      </div>
      <div class="author-description">
        <?php
        if ($description) {
          echo $description;
        } elseif (!$author_id) {
          if (is_buddypress_exist()) {
            echo __( '未登録のユーザーさんです。', THEME_NAME );
            echo '<br>';
            echo __( 'ログイン・登録はこちら→', THEME_NAME );
            echo '<a href="'.wp_login_url().'">';
            echo __( 'ログインページ', THEME_NAME );
            echo '</a>';
          }

        } elseif (is_user_logged_in()) {
          echo __( 'プロフィール内容は管理画面から変更可能です→', THEME_NAME ).'<a href="/wp-admin/user-edit.php?user_id='.get_the_author_meta( 'ID' ).'">'.__( 'プロフィール設定画面', THEME_NAME ).'</a><br>'.__( '※このメッセージは、ログインユーザーにしか表示されません。', THEME_NAME );
        }
        ?>
        <?php if (0&&get_the_author_website_url()): ?>
          <span class="site-url">
            <a href="<?php echo get_the_author_website_url(); ?>" target="_blank"><?php echo get_the_author_website_url(); ?></a>
          </span>
        <?php endif ?>

      </div>
      <?php if ($author_id): ?>
      <div class="author-follows">
        <?php get_template_part('tmp/sns-follow-buttons'); ?>
      </div>
      <?php endif ?>

    </div>
  </div>
<?php
}
endif;