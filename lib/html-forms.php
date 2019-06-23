<?php //HTMLフォーム生成関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

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

//brタグの出力
if ( !function_exists( 'generate_br_tag' ) ):
function generate_br_tag(){
  echo '<br>';
}
endif;


//セレクトボックスの生成
if ( !function_exists( 'generate_selectbox_tag' ) ):
function generate_selectbox_tag($name, $options, $now_value, $label = null, $icon_font_visible = false){
  $style = null;
  if ($icon_font_visible) {
    $style = ' style="font-family: FontAwesome;font-size: 20px;text-align: center;"';
  }
  ob_start();
  if ($label) {
    generate_label_tag($name, $label);
    generate_br_tag();
  }

  ?>
  <select name="<?php echo $name; ?>"<?php echo $style; ?>>
    <?php
    foreach ($options as $value => $caption) {
      //アイコンフォントを利用する場合
      $add_option_class = null;
      ?>
      <option value="<?php echo $value; ?>"<?php the_option_selected($value, $now_value) ?><?php echo $add_option_class; ?>><?php echo $caption; ?></option>
    <?php } ?>
  </select>
  <?php
  $res = ob_get_clean();
  echo apply_filters('admin_input_form_tag', $name, $res);
}
endif;


//レンジボックスの生成
if ( !function_exists( 'generate_range_tag' ) ):
function generate_range_tag($name, $value, $min, $max, $step){
  ob_start();?>
  <div class="range-wrap">
    <output id="<?php echo $name; ?>"><?php echo $value; ?></output>
    <span class="range-min"><?php echo $min; ?></span><input type="range" name="<?php echo $name; ?>" value="<?php echo $value; ?>" min="<?php echo $min; ?>" max="<?php echo $max; ?>" step="<?php echo $step; ?>"
    oninput="document.getElementById('<?php echo $name; ?>').value=this.value"><span class="range-min"><?php echo $max; ?></span>
  </div>
  <?php
  $res = ob_get_clean();
  echo apply_filters('admin_input_form_tag', $name, $res);
}
endif;

//スキン制御タグで囲む
if ( !function_exists( 'get_skin_control_tag' ) ):
function get_skin_control_tag($tag){
  return '<div class="skin-control">'.$tag.'</div>';
}
endif;

//入力フォームをスキン制御タグで囲む
add_filter( 'admin_input_form_tag', 'wrap_skin_control_tag', 10, 2 );
if ( !function_exists( 'wrap_skin_control_tag' ) ):
function wrap_skin_control_tag($name, $tag){
  // if ($name == '404_page_title') {
  //   _v(get_skin_option($name));
  // }
  if (get_skin_option($name) !== null ) {
    $tag = get_skin_control_tag($tag);
  }
  return $tag;
}
endif;

//チェックボックスの生成
if ( !function_exists( 'generate_checkbox_tag' ) ):
function generate_checkbox_tag($name, $now_value, $label){
  ob_start();?>
  <input type="checkbox" name="<?php echo $name; ?>" value="1"<?php the_checkbox_checked($now_value); ?>><?php echo $label; ?>
  <?php
  $res = ob_get_clean();
  echo apply_filters('admin_input_form_tag', $name, $res);
}
endif;


//ラジオボックスの生成
if ( !function_exists( 'generate_radiobox_tag' ) ):
function generate_radiobox_tag($name, $options, $now_value){
  ob_start();?>
  <ul>
    <?php foreach ($options as $value => $caption) {
    // _v($value.' == '.$now_value);
    // _v($value == $now_value);
    ?>
    <li><input type="radio" name="<?php echo $name; ?>" value="<?php echo $value; ?>"<?php the_checkbox_checked($value, $now_value) ?>><?php echo $caption; ?></li>
    <?php } ?>
  </ul>
  <?php
  $res = ob_get_clean();
  echo apply_filters('admin_input_form_tag', $name, $res);
}
endif;


//ラベルの生成
if ( !function_exists( 'generate_label_tag' ) ):
function generate_label_tag($name, $caption){?>
  <label for="<?php echo $name; ?>"><?php echo $caption; ?></label>
  <?php
}
endif;

//必須入力項目案内
if ( !function_exists( 'generate_necessity_input_tag' ) ):
function generate_necessity_input_tag($message = '*'){?>
  <span class="necessity-input"><?php echo $message; ?></span>
<?php
}
endif;


//入力できないフォームクラスコードの生成
if ( !function_exists( 'get_not_allowed_form_class' ) ):
function get_not_allowed_form_class($is_allowed, $in = false){
  if (!$is_allowed) {
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
function get_help_page_tag($url, $text = null){
  $link_text = $text ? $text : __( '解説ページ', THEME_NAME );
  $tag = ' <a href="'.$url.'" target="_blank" rel="noopener" class="help-page">'.$link_text.'</a>';
  return $tag;
}
endif;
if ( !function_exists( 'generate_help_page_tag' ) ):
function generate_help_page_tag($url, $text = null){
  echo get_help_page_tag($url, $text);
}
endif;


//通知メッセージの生成
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

//エラーメッセージの生成
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
function generate_textbox_tag($name, $value, $placeholder, $cols = DEFAULT_INPUT_COLS){
  ob_start();?>
  <input type="text" name="<?php echo $name; ?>" size="<?php echo $cols; ?>" value="<?php echo esc_attr(stripslashes_deep(strip_tags($value))); ?>" placeholder="<?php echo esc_attr($placeholder); ?>">
  <?php
  $res = ob_get_clean();
  echo apply_filters('admin_input_form_tag', $name, $res);
}
endif;

//テキストエリアの生成
if ( !function_exists( 'generate_textarea_tag' ) ):
function generate_textarea_tag($name, $value, $placeholder, $rows = DEFAULT_INPUT_ROWS,  $cols = DEFAULT_INPUT_COLS){
  ob_start();?>
  <textarea name="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>" rows="<?php echo $rows; ?>" cols="<?php echo $cols; ?>"><?php echo $value; ?></textarea>
  <?php
  $res = ob_get_clean();
  echo apply_filters('admin_input_form_tag', $name, $res);
}
endif;


//ナンバーボックスの生成
if ( !function_exists( 'generate_number_tag' ) ):
function generate_number_tag($name, $value, $placeholder = '', $min = 1, $max = 100, $step = 1){
  ob_start();?>
  <input type="number" name="<?php echo $name; ?>" value="<?php echo $value; ?>" placeholder="<?php echo $placeholder; ?>" min="<?php echo $min; ?>" max="<?php echo $max; ?>" step="<?php echo $step; ?>">
  <?php
  $res = ob_get_clean();
  echo apply_filters('admin_input_form_tag', $name, $res);
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
  if (!is_singular() && !is_archive() && !is_search() && $is_header) {
    $tag = 'h1';
  }
  if ($is_header) {
    $class = ' logo-header';
  } else {
    $class = ' logo-footer';
  }

  $logo_url = get_the_site_logo_url();
  $footer_logo_ur = get_footer_logo_url();
  if (!$is_header && $footer_logo_ur) {
    $logo_url = $footer_logo_ur;
  }
  if ( $logo_url ) {
    $class .= ' logo-image';
  } else {
    $class .= ' logo-text';
  }
  //ロゴの幅設定
  $site_logo_width = get_the_site_logo_width();
  $width_attr = null;
  if ($site_logo_width && $is_header) {
    $width_attr = ' width="'.$site_logo_width.'"';
  }
  //ロゴの高さ設定
  $site_logo_height = get_the_site_logo_height();
  $height_attr = null;
  if ($site_logo_height && $is_header) {
    $height_attr = ' height="'.$site_logo_height.'"';
  }


  $logo_before_tag = '<'.$tag.' class="logo'.$class.'"><a href="'.esc_url(get_home_url()).'" class="site-name site-name-text-link" itemprop="url"><span class="site-name-text" itemprop="name about">';
  $logo_after_tag = '</span></a></'.$tag.'>';
  if ($logo_url) {
    $site_logo_tag = '<img class="site-logo-image" src="'.$logo_url.'" alt="'.esc_attr(get_bloginfo('name')).'"'.$width_attr.$height_attr.'>';
  } else {
    $site_logo_tag = get_bloginfo('name');
  }
  $all_tag = $logo_before_tag.$site_logo_tag.$logo_after_tag;
  echo apply_filters( 'the_site_logo_tag', $all_tag, $is_header );
}
endif;

//カラーピッカーの生成
if ( !function_exists( 'generate_color_picker_tag' ) ):
function generate_color_picker_tag($name, $value, $label){
  ob_start();?>
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

    $res = ob_get_clean();
    echo apply_filters('admin_input_form_tag', $name, $res);
}
endif;


//ビジュアルエディターの生成
if ( !function_exists( 'generate_visuel_editor_tag' ) ):
function generate_visuel_editor_tag($name, $content, $editor_id = 'wp_editor', $textarea_rows = 16){
  ob_start();
  $settings = array(
    'textarea_name' => $name,
    'textarea_rows' => $textarea_rows,
  ); //配列としてデータを渡すためname属性を指定する
  wp_editor( $content, $editor_id, $settings );
  $res = ob_get_clean();
  echo apply_filters('admin_input_form_tag', $name, $res);
}
endif;

//メインカラム広告の詳細設定フォーム
if ( !function_exists( 'generate_main_column_ad_detail_setting_forms' ) ):
function generate_main_column_ad_detail_setting_forms($name, $value, $label_name = null, $label_value = null, $body_ad_name = null, $body_ad_value = null){ ?>
 <span class="toggle">
  <span class="toggle-link"><?php _e( '詳細設定', THEME_NAME ) ?></span>
  <div class="toggle-content">
    <div class="detail-area">
    <?php
    global $_MAIN_DATA_AD_FORMATS;
    $options = $_MAIN_DATA_AD_FORMATS;
    generate_selectbox_tag($name, $options, $value, __( 'フォーマット', THEME_NAME ));
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

//トグルフォーム
if ( !function_exists( 'generate_toggle_area' ) ):
function generate_toggle_area($caption, $content){ ?>
 <span class="toggle">
  <span class="toggle-link"><?php echo $caption; ?></span>
  <div class="toggle-content">
    <?php echo $content; ?>
  </div>
</span>
<?php
}
endif;

//トグルが入力されているか
if ( !function_exists( 'generate_toggle_entered' ) ):
function generate_toggle_entered($is_entered){ ?>
  <span class="toggle-entered">[<?php
    if ($is_entered) {
      _e( '入力済', THEME_NAME );
    } else {
      _e( '未入力', THEME_NAME );
    }
   ?>]</span>
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
    <?php
    global $_SIDEBAR_DATA_AD_FORMATS;
    $options = $_SIDEBAR_DATA_AD_FORMATS;
    generate_selectbox_tag($name, $options, $value, __( 'フォーマット', THEME_NAME ));
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
  ob_start();
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
  $res = ob_get_clean();
  echo apply_filters('admin_input_form_tag', $name, $res);
}
endif;


//カテゴリチェックリストの作成
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

//階層化カテゴリチェックリストの出力
if ( !function_exists( 'generate_hierarchical_category_check_list' ) ):
function generate_hierarchical_category_check_list( $cat, $name, $checks, $width = 0 ) {
  ob_start();
  if ($width == 0) {
    $width = 'auto';
  } else {
    $width = $width.'px';
  }
  echo '<div class="tab-content category-check-list '.$name.'-list" style="width: '.$width.';">';
  hierarchical_category_check_list( $cat, $name, $checks );
  echo '</div>';

  $res = ob_get_clean();
  echo apply_filters('admin_input_form_tag', $name, $res);
}
endif;

//階層化カテゴリチェックリストの出力の再帰関数
if ( !function_exists( 'hierarchical_category_check_list' ) ):
function hierarchical_category_check_list( $cat, $name, $checks ) {
  if (is_string($checks)) {
    $checks = array();
  }
    // wpse-41548 // alchymyth // a hierarchical list of all categories //

  $next = get_categories('hide_empty=false&orderby=name&order=ASC&parent=' . $cat);

  if( $next ) :
    foreach( $next as $cat ) :
      $checked = '';
      // if (is_string($checks)) {
      //   $checks = array();
      // }
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

//タグチェックリスト
if ( !function_exists( 'generate_tagcloud_check_list' ) ):
function generate_tagcloud_check_list($name, $checks = array()){
  if (is_string($checks)) {
    $checks = array();
  }
  $html = '<div class="tagcloud tagcloud-list '.$name.'-list" style="width: 100%;">';
  $tags = get_tags();
  //_v($tags);
	if ($tags) {
		foreach($tags as $tag) {
      $checked = null;
      if (in_array($tag->term_id, $checks)) {
        $checked = ' checked="checked"';
      }
      $id = $tag->term_id;
      $input_id = $name.'-'.$id;
			$html .= '<span class="tag-cloud-link admin-tag tag-link-"'.$id.'"><label for="'.$input_id.'"><input type="checkbox" id="'.$input_id.'" name="'.$name.'[]" value="'.$id.'"'.$checked.'>' . $tag->name . '<span class="tag-link-count">('.$tag->count.')</span></label></span>';
		}
	}
  $html .= '</div>';
  echo apply_filters('admin_input_form_tag', $name, $html);
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
function generate_popular_entries_tag($days = 'all', $entry_count = 5, $entry_type = ET_DEFAULT, $ranking_visible = 0, $pv_visible = 0, $cat_ids = array(), $exclude_post_ids = array(), $exclude_cat_ids = array()){
  // if (DEBUG_MODE) {
  //   $time_start = microtime(true);
  // }
  //var_dump($cat_ids);

  $records = get_access_ranking_records($days, $entry_count, $entry_type, $cat_ids, $exclude_post_ids, $exclude_cat_ids);
  //_v($records);

  // if (DEBUG_MODE) {
  //   $time = microtime(true) - $time_start;
  //   echo('<pre>');
  //   echo($time);
  //   echo('</pre>');
  // }


  //var_dump($records);
  $thumb_size = get_popular_entries_thumbnail_size($entry_type);
  ?>
  <div class="popular-entry-cards widget-entry-cards no-icon cf<?php echo get_additional_popular_entry_cards_classes($entry_type, $ranking_visible, $pv_visible, null); ?>">
  <?php if ( $records ) :
    $i = 1;
    foreach ($records as $post):
      $permalink = get_permalink( $post->ID );
      $title = $post->post_title;
      //$no_thumbnail_url = get_template_directory_uri().'/images/no-image-320.png';
      $no_thumbnail_url = ($entry_type == ET_DEFAULT) ? get_no_image_120x68_url() : get_no_image_320x180_url();
      $w   = ($entry_type == ET_DEFAULT) ? THUMB120WIDTH  : THUMB320WIDTH;
      $h   = ($entry_type == ET_DEFAULT) ? THUMB120HEIGHT : THUMB320HEIGHT;
      //$no_thumbnail_url = get_no_image_320x180_url();

      $post_thumbnail = get_the_post_thumbnail( $post->ID, $thumb_size, array('alt' => '') );
      $pv = $post->sum_count;

      if ($post_thumbnail) {
        $post_thumbnail_img = $post_thumbnail;
      } else {
        $post_thumbnail_img = '<img src="'.esc_url($no_thumbnail_url).'" alt="" class="no-image popular-entry-card-thumb-no-image widget-entry-card-thumb-no-image" width="'.$w.'" height="'.$h.'" />';
      }

      ?>
  <a href="<?php echo $permalink; ?>" class="popular-entry-card-link a-wrap no-<?php echo $i; ?>" title="<?php echo esc_attr($title); ?>">
    <div class="popular-entry-card widget-entry-card e-card cf">
      <figure class="popular-entry-card-thumb widget-entry-card-thumb card-thumb">
        <?php echo $post_thumbnail_img; ?>
        <?php
        $is_visible = apply_filters('is_popular_entry_card_category_label_visible', false);
        $is_visible = apply_filters('is_widget_entry_card_category_label_visible', $is_visible);
        the_nolink_category($post->ID, $is_visible); //カテゴリラベルの取得 ?>
      </figure><!-- /.popular-entry-card-thumb -->

      <div class="popular-entry-card-content widget-entry-card-content card-content">
        <span class="popular-entry-card-title widget-entry-card-title card-title"><?php echo $title;?></span>
        <?php if ($pv_visible): ?>
          <span class="popular-entry-card-pv widget-entry-card-pv"><?php echo $pv == '1' ? $pv.' view' : $pv.' views';?></span>
        <?php endif ?>
        <?php generate_widget_entry_card_date('popular', $post->ID); ?>
      </div><!-- /.popular-entry-content -->
    </div><!-- /.popular-entry-card -->
  </a><!-- /.popular-entry-card-link -->

  <?php
  $i++;
  endforeach;
  else :
    echo '<p>'.__( '人気記事は見つかりませんでした。', THEME_NAME ).'</p>';//見つからない時のメッセージ
  endif; ?>
  </div>
<?php
}
endif;

//nofollowリンクの作成
if ( !function_exists( 'get_nofollow_link' ) ):
function get_nofollow_link($url, $text){
  return '<a href="'.$url.'" rel="nofollow">'.$text.'</a>';
}
endif;

//汎用エントリーウィジェットのタグ生成
if ( !function_exists( 'generate_widget_entries_tag' ) ):
// function generate_widget_entries_tag($entry_count = 5, $entry_type = ET_DEFAULT, $cat_ids = array(), $include_children = 0, $post_type = null, $taxonomy = 'category', $random = 0, $action = null){
function generate_widget_entries_tag($atts){
  extract(shortcode_atts(array(
    'entry_count' => 5,
    'cat_ids' => array(),
    'tag_ids' => array(),
    'entry_type' => ET_DEFAULT,
    'include_children' => 0,
    'post_type' => null,
    'taxonomy' => 'category',
    'sticky' => 0,
    'random' => 0,
    'modified' => 0,
    'order' => 'desc',
    'action' => null,
    'exclude_cat_ids' => array(),
  ), $atts));
  global $post;

  //ランダムが有効な時は関連記事
  if ($random) {
    $prefix = 'widget-related';
  } else {
    $prefix = 'new';
  }

  $args = array(
    'posts_per_page' => $entry_count,
    'no_found_rows' => true,
    'action' => $action,
  );
  if (!$sticky) {
    $args += array(
      'post__not_in' => get_sticky_post_ids(),
    );
  }
  if ($order) {
    $args += array(
      'order' => strtoupper($order),
    );
  }
  if ($post_type) {
    $args += array(
      'post_type' => explode(',', $post_type)
    );
  }
  if ($random && $modified) {
    $args += array(
      'orderby' => array('rand', 'modified'),
    );
  } else {
    if ($random) {
      $args += array(
        'orderby' => 'rand',
      );
    }
    if ($modified) {
      $args += array(
        'orderby' => 'modified',
      );
    }
  }
  //関連記事の場合は表示ページを除外
  if ($random) {
    $post_id = get_the_ID();
    if (isset($args['post__not_in'])) {
      $args['post__not_in'][] = $post_id;
    } else {
      $args['post__not_in'] = array($post_id);
    }
  }

  //除外カテゴリーの設定
  if (!empty($exclude_cat_ids)) {
    // _v($cat_ids);
    // _v($exclude_cat_ids);
    $cat_ids = array_diff($cat_ids, $exclude_cat_ids);
    // _v($cat_ids);
  }
  //カテゴリー・タグの指定
  if ( $cat_ids || $tag_ids ) {
    //_v($cat_ids);
    $tax_querys = array();
    if ($cat_ids) {
      $tax_querys[] = array(
        'taxonomy' => $taxonomy,
        'terms' => $cat_ids,
        'include_children' => $include_children,
        'field' => 'term_id',
        'operator' => 'IN'
      );
    }
    if ($tag_ids) {
      $tax_querys[] = array(
        'taxonomy' => 'post_tag',
        'terms' => $tag_ids,
        'field' => 'term_id',
        'operator' => 'IN'
      );
    }
    //_v($tax_querys);
    $args += array(
      'tax_query' => array(
        $tax_querys,
        'relation' => 'AND'
      )
    );

  }
  // _v($args);
  $thumb_size = get_widget_entries_thumbnail_size($entry_type);

  if ($random) {
    $args = apply_filters('widget_related_entries_args', $args);
    $thumb_size = apply_filters('get_related_entries_thumbnail_size', $thumb_size, $entry_type);
  } else {
    $args = apply_filters('widget_new_entries_args', $args);
    $thumb_size = apply_filters('get_new_entries_thumbnail_size', $thumb_size, $entry_type);
  }
  $args = apply_filters('widget_entries_args', $args);
  //_v($args);
  //query_posts( $args ); //クエリの作成
  $query = new WP_Query( $args );
  ?>
  <div class="<?php echo $prefix; ?>-entry-cards widget-entry-cards no-icon cf<?php echo get_additional_widget_entriy_cards_classes($entry_type); ?>">
  <?php //if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
  <?php if ( $query -> have_posts() ) : while ( $query -> have_posts() ) : $query -> the_post(); ?>
  <a href="<?php echo esc_url(get_the_permalink()); ?>" class="<?php echo $prefix; ?>-entry-card-link widget-entry-card-link a-wrap" title="<?php echo esc_attr(get_the_title()); ?>">
    <div class="<?php echo $prefix; ?>-entry-card widget-entry-card e-card cf">
      <figure class="<?php echo $prefix; ?>-entry-card-thumb widget-entry-card-thumb card-thumb">
      <?php if ( has_post_thumbnail() ): // サムネイルを持っているときの処理 ?>
        <?php the_post_thumbnail( $thumb_size, array('alt' => '') ); ?>
      <?php else: // サムネイルを持っていないときの処理

        $url = ($entry_type == ET_DEFAULT) ? get_no_image_120x68_url() : get_no_image_320x180_url();
        $w   = ($entry_type == ET_DEFAULT) ? THUMB120WIDTH  : THUMB320WIDTH;
        $h   = ($entry_type == ET_DEFAULT) ? THUMB120HEIGHT : THUMB320HEIGHT;

        ?>
        <img src="<?php echo esc_url($url); ?>" alt="" class="no-image <?php echo $prefix; ?>-entry-card-thumb-no-image widget-entry-card-thumb-no-image" width="<?php echo $w; ?>" height="<?php echo $h; ?>" />
      <?php endif; ?>
      <?php
        if ($random) {
          $is_visible = apply_filters('is_widget_related_entry_card_category_label_visible', false);
        } else {
          $is_visible = apply_filters('is_new_entry_card_category_label_visible', false);
        }
        $is_visible = apply_filters('is_widget_entry_card_category_label_visible', $is_visible);
        $post_id = isset($post->ID) ? $post->ID : null;
        the_nolink_category($post_id, $is_visible); //カテゴリラベルの取得 ?>
      </figure><!-- /.new-entry-card-thumb -->

      <div class="<?php echo $prefix; ?>-entry-card-content widget-entry-card-content card-content">
        <div class="<?php echo $prefix; ?>-entry-card-title widget-entry-card-title card-title"><?php the_title();?></div>
        <?php generate_widget_entry_card_date($prefix); ?>
      </div><!-- /.new-entry-content -->
    </div><!-- /.new-entry-card -->
  </a><!-- /.new-entry-card-link -->
  <?php endwhile;
  else :
    echo '<p>'.__( '記事は見つかりませんでした。', THEME_NAME ).'</p>';//見つからない時のメッセージ
  endif; ?>
  <?php wp_reset_postdata(); ?>
  <?php //wp_reset_query(); ?>
  </div>
<?php
}
endif;

if ( !function_exists( 'generate_widget_entry_card_date' ) ):
function generate_widget_entry_card_date($prefix, $post_id = null){?>
<div class="<?php echo $prefix; ?>-entry-card-date widget-entry-card-date display-none">
  <span class="<?php echo $prefix; ?>-entry-card-post-date widget-entry-card-post-date post-date"><?php echo get_the_time(get_site_date_format(), $post_id); ?></span><?php
    //更新日の取得
    $update_time = get_update_time(get_site_date_format(), $post_id);
  if($update_time):
  ?><span class="<?php echo $prefix; ?>-entry-card-update-date widget-entry-card-update-date post-update"><?php echo $update_time; ?></span><?php
  endif; ?>
</div><?php
}
endif;

//プロフィールボックス生成関数
if ( !function_exists( 'generate_author_box_tag' ) ):
function generate_author_box_tag($id = null, $label = null, $is_image_circle = 0){
  $user_id = get_the_author_meta( 'ID' );
  if (!$user_id || is_404()) {
    $user_id = get_sns_default_follow_user();
  }
  if ($id && get_userdata( $id )) {
    $user_id = $id;
  }

  ?>
  <div class="author-box border-element no-icon cf">
    <?php //ウィジェット名がある場合
    $image_class = $is_image_circle ? ' circle-image' : null;
    if ($label): ?>
      <div class="author-widget-name">
        <?php echo $label; ?>
      </div>
    <?php endif ?>
    <figure class="author-thumb<?php echo $image_class; ?>">
      <?php echo get_avatar( $user_id, 200 ); ?>
    </figure>
    <div class="author-content">
      <div class="author-name">
        <?php
        if ($user_id) {
          $description = get_the_author_description_text($user_id);
          //$description = trim(get_the_author_description_text());
          if (empty($description)) {
            $description = get_the_author_meta('description', $user_id);
          }
          $description = wpautop($description);

          if (!is_buddypress_page()) {
            //プロフィールページURLの取得
            $profile_page_url = get_the_author_profile_page_url($user_id);

            $author_display_name = strip_tags(get_the_author_display_name($user_id));
            if ($profile_page_url) {
              $name = '<a href="'.$profile_page_url.'">'.$author_display_name.'</a>';
              //$name = get_nofollow_link($profile_page_url, $author_display_name);
              //echo $name;
            } else {
              //$name = get_the_author_posts_link($user_id);
              $user = get_userdata( $user_id );
              $name = sprintf( '<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
                esc_url( get_author_posts_url( $user->ID, $user->user_nicename ) ),
                /* translators: %s: author's display name */
                esc_attr( sprintf( __( 'Posts by %s' ), $user->display_name ) ),
                $user->display_name
              );
              //_v($user);
            }
          } else {
            $author_display_name = strip_tags(get_the_author_display_name($user_id));
            $author_website_url = strip_tags(get_the_author_website_url($user_id));
            $description = strip_tags($description);
            $name = $author_display_name;
            if ($author_website_url) {
              $name = '<a href="'.esc_url($author_website_url).'" target="_blank" rel="nofollow">'.esc_html($author_display_name).'</a>';
            }
            //echo $name;
          }
          echo apply_filters( 'the_author_box_name', $name );


        } else {
          echo __( '未登録のユーザーさん', THEME_NAME );
        }
         ?>
      </div>
      <div class="author-description">
        <?php
        if ($description) {
          echo $description;
        } elseif (!$user_id) {
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

      </div>
      <?php if ($user_id): ?>
      <div class="author-follows">
        <?php
        set_query_var( '_USER_ID', $user_id );
        get_template_part_with_option('tmp/sns-follow-buttons', SF_PROFILE);
        set_query_var( '_USER_ID', null ); ?>
      </div>
      <?php endif ?>

    </div>
  </div>
<?php
}
endif;

//メッセージボックスを作成する
if ( !function_exists( 'get_message_box_tag' ) ):
function get_message_box_tag($message, $classes){
  return '<div class="'.$classes.'">'.$message.'</div>';
}
endif;

//管理者エラーメッセージボックスを作成する
if ( !function_exists( 'get_admin_errormessage_box_tag' ) ):
function get_admin_errormessage_box_tag($message){
  $admin_message = '<b>'.__( '管理者用エラーメッセージ', THEME_NAME ).'</b><br>';
  $admin_message .= $message;
  return get_message_box_tag($admin_message, 'warning-box fz-14px');
}
endif;
