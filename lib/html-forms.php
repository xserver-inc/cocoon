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
  <?php foreach ($options as $value => $caption) { ?>
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


//説明文の生成
if ( !function_exists( 'generate_tips_tag' ) ):
function generate_tips_tag($caption){?>
  <p class="tips"><?php echo $caption; ?></p>
  <?php
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
function generate_number_tag($name, $value, $min = 1, $max = 100){?>
  <input type="number" name="<?php echo $name; ?>" value="<?php echo $value; ?>" min="<?php echo $min; ?>" max="<?php echo $max; ?>">
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

  $logo_before_tag = '<'.$tag.' class="logo'.$class.'" itemscope itemtype="http://schema.org/Organization">';
  $logo_after_tag = '</'.$tag.'>';
  if (get_the_site_logo_url()) {
    $site_logo_tag = '<a href="'.get_home_url().'" class="site-name site-name-image-link"><img src="'.get_the_site_logo_url().'" alt="'.get_bloginfo('name').'"></a>';
  } else {
    $site_logo_tag = '<a href="'.get_home_url().'" class="site-name site-name-text-link">'.get_bloginfo('name').'</a>';
  }
  echo $logo_before_tag.$site_logo_tag.$logo_after_tag;
}
endif;




//ツールチップの生成
if ( !function_exists( 'generate_tooltip_tag' ) ):
function generate_tooltip_tag($content){?>
  <span class="tooltip fa fa-exclamation-triangle">
    <span class="tip-content">
      <?php echo esc_html($content); ?>
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
function generate_visuel_editor_tag($name, $content, $editor_id = 'wp_editor'){
  $settings = array( 'textarea_name' => $name ); //配列としてデータを渡すためname属性を指定する
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
    $options = MAIN_DATA_AD_FORMATS;
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
    $options = SIDEBAR_DATA_AD_FORMATS;
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
  echo '<div class="category-check-list '.$name.'-list" style="width: '.$width.';">';
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
      //デフォルトのカテゴリは誤動作を起こすので除外
      if ($cat->term_id == 1) {
        continue;
      }
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

  echo '<div class="page-display-check-list '.$name.'-list" style="width: '.$width.';"><ul>';

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

  echo '<div class="author-check-list '.$name.'-list" style="width: '.$width.';"><ul>';

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
