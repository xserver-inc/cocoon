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
  if ( (is_array($val1) && in_array($val2, $val1)) || ($val1 == $val2) ) {
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
  echo apply_filters('admin_input_form_tag', $res, $name);
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
  echo apply_filters('admin_input_form_tag', $res, $name);
}
endif;

//スキン制御タグで囲む
if ( !function_exists( 'get_skin_control_tag' ) ):
function get_skin_control_tag($tag){
  return '<div class="skin-control">'.$tag.'</div>';
}
endif;

//入力フォームをスキン制御タグで囲む
add_filter( 'admin_input_form_tag', 'wrap_skin_control_tag', 10, 3 );
if ( !function_exists( 'wrap_skin_control_tag' ) ):
function wrap_skin_control_tag($tag, $name, $value = 1){
  if (is_form_skin_option($name, $value)) {
    global $_FORM_SKIN_OPTIONS;
    $name = str_replace('[]', '', $name);
    // _v($_FORM_SKIN_OPTIONS);
    // _v($name);
    // _v($value);
    if (isset($_FORM_SKIN_OPTIONS[$name])) {
      //値が配列だった場合配列だけをスキン制御タグで囲む
      if (is_array($_FORM_SKIN_OPTIONS[$name]) && in_array(trim($value), $_FORM_SKIN_OPTIONS[$name])) {
        $tag = get_skin_control_tag($tag);
      }
      //値が文字列・数字だった場合スキン制御タグで囲む
      elseif (is_string($_FORM_SKIN_OPTIONS[$name]) || is_numeric($_FORM_SKIN_OPTIONS[$name])){
        $tag = get_skin_control_tag($tag);
      }
    }
  }
  return $tag;
}
endif;

//チェックボックスの生成
if ( !function_exists( 'generate_checkbox_tag' ) ):
function generate_checkbox_tag($name, $now_value, $label, $default_value = 1){
  ob_start();
  $id = $name.'_'.$default_value;
  ?>
  <input type="checkbox" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $default_value; ?>"<?php the_checkbox_checked($now_value, $default_value); ?>><?php generate_label_tag($id, $label); ?>
  <?php
  $res = ob_get_clean();
  echo apply_filters('admin_input_form_tag', $res, $name, $default_value);
}
endif;


//ラジオボックスの生成
if ( !function_exists( 'generate_radiobox_tag' ) ):
function generate_radiobox_tag($name, $options, $now_value, $label = null){
  ob_start();
  $i = 0;
  if ($label) {
    generate_label_tag($name, $label);
    generate_br_tag();
  }?>
  <ul>
    <?php foreach ($options as $value => $caption) {
    $id = $name.'_'.$i;
    // _v($value.' == '.$now_value);
    // _v($value == $now_value);
    ?>
    <li><input type="radio" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $value; ?>"<?php the_checkbox_checked($value, $now_value) ?>><label for="<?php echo $id; ?>"><?php echo $caption; ?></label></li>
    <?php
    $i++;
    } ?>
  </ul>
  <?php
  $res = ob_get_clean();
  echo apply_filters('admin_input_form_tag', $res, $name);
}
endif;


//ラベルの取得
if ( !function_exists( 'get_label_tag' ) ):
function get_label_tag($id, $caption){
  return '<label for="'. $id.'">'.$caption.'</label>';
}
endif;


//ラベルの生成
if ( !function_exists( 'generate_label_tag' ) ):
function generate_label_tag($id, $caption){
  echo get_label_tag($id, $caption);
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
function generate_tips_tag($caption){
  ob_start();?>
  <p class="tips"><span class="fa fa-info-circle" aria-hidden="true"></span> <?php echo $caption; ?></p>
  <?php
  $tag = ob_get_clean();
  $tag = change_fa($tag);
  echo $tag;
}
endif;


//アラート文の生成
if ( !function_exists( 'generate_alert_tag' ) ):
function generate_alert_tag($caption){
  ob_start();?>
  <p class="alert"><span class="fa fa-exclamation-triangle" aria-hidden="true"></span> <?php echo $caption; ?></p>
  <?php
  $tag = ob_get_clean();
  $tag = change_fa($tag);
  echo $tag;
}
endif;

//ハウツー説明文の取得
if ( !function_exists( 'get_howto_tag' ) ):
function get_howto_tag($caption, $id = ''){
  $caption = apply_filters('howto_tag_caption', $caption, $id);
  return '<p class="howto">'.$caption.'</p>';
}
endif;


//ハウツー説明文の生成
if ( !function_exists( 'generate_howto_tag' ) ):
function generate_howto_tag($caption, $id = ''){
  echo get_howto_tag($caption, $id);
}
endif;

//解説ページへのリンク取得
if ( !function_exists( 'get_help_page_tag' ) ):
function get_help_page_tag($url, $text = null){
  $link_text = $text ? $text : __( '解説ページ', THEME_NAME );
  $tag = ' <a href="'.$url.'" target="_blank" rel="noopener" class="help-page"><span class="fa fa-question-circle" aria-hidden="true"></span> '.$link_text.'</a>';
  $tag = change_fa($tag);
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
  <input type="text" id="<?php echo $name; ?>" name="<?php echo $name; ?>" size="<?php echo $cols; ?>" value="<?php echo esc_attr(stripslashes_deep(strip_tags($value))); ?>" placeholder="<?php echo esc_attr($placeholder); ?>">
  <?php
  $res = ob_get_clean();
  echo apply_filters('admin_input_form_tag', $res, $name);
}
endif;

//テキストエリアの生成
if ( !function_exists( 'generate_textarea_tag' ) ):
function generate_textarea_tag($name, $value, $placeholder, $rows = DEFAULT_INPUT_ROWS,  $cols = DEFAULT_INPUT_COLS, $style = null){
  $style_tag = null;
  if ($style) {
    $style_tag = ' style="'.$style.'"';
  }
  ob_start();?>
  <textarea id="<?php echo $name; ?>" name="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>" rows="<?php echo $rows; ?>" cols="<?php echo $cols; ?>"<?php echo $style_tag; ?>><?php echo $value; ?></textarea>
  <?php
  $res = ob_get_clean();
  echo apply_filters('admin_input_form_tag', $res, $name);
}
endif;


//ナンバーボックスの生成
if ( !function_exists( 'generate_number_tag' ) ):
function generate_number_tag($name, $value, $placeholder = '', $min = 1, $max = 100, $step = 1, $width = 100){
  $width_attr = null;
  if ($width) {
    $width_attr = ' style="width: '.$width.'px;"';
  }
  ob_start();?>
  <input type="number" name="<?php echo $name; ?>" value="<?php echo $value; ?>" placeholder="<?php echo $placeholder; ?>" min="<?php echo $min; ?>" max="<?php echo $max; ?>" step="<?php echo $step; ?>"<?php echo $width_attr; ?>>
  <?php
  $res = ob_get_clean();
  echo apply_filters('admin_input_form_tag', $res, $name);
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

  if ($is_header) {
    $img_class = 'header-site-logo-image';
  } else {
    $img_class = 'footer-site-logo-image';
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
  //パーマリンク設定とホームURLの出力を合わせる
  $home_url = user_trailingslashit(get_home_url());
  $home_url = apply_filters('site_logo_url', $home_url);
  //サイトロゴテキスト（Alt属性）
  $site_logo_text = apply_filters('site_logo_text', get_bloginfo('name'));
  if ($is_header) {
    $home_url = apply_filters('header_site_logo_url', $home_url);
    $site_logo_text = apply_filters('header_site_logo_text', $site_logo_text);
  } else {
    $home_url = apply_filters('footer_site_logo_url', $home_url);
    $site_logo_text = apply_filters('footer_site_logo_text', $site_logo_text);
  }
  $itemprop = null;
  if (!$logo_url) {
    $itemprop = ' itemprop="name about"';
  }
  $logo_before_tag = '<'.$tag.' class="logo'.$class.'"><a href="'.esc_url($home_url).'" class="site-name site-name-text-link" itemprop="url"><span class="site-name-text"'.$itemprop.'>';
  $logo_after_tag = '</span></a></'.$tag.'>';
  if ($logo_url) {
    $site_logo_tag = '<img class="site-logo-image '.$img_class.'" src="'.$logo_url.'" alt="'.esc_attr($site_logo_text).'"'.$width_attr.$height_attr.'><meta itemprop="name about" content="' . esc_attr($site_logo_text) . '">';
  } else {
    $site_logo_tag = esc_html($site_logo_text);
  }
  $all_tag = $logo_before_tag.$site_logo_tag.$logo_after_tag;
  echo apply_filters( 'the_site_logo_tag', $all_tag, $is_header, $home_url, $site_logo_text, $site_logo_width, $site_logo_height );
}
endif;

//カラーピッカーの生成
if ( !function_exists( 'generate_color_picker_tag' ) ):
function generate_color_picker_tag($name, $value, $label){
  ob_start();?>
  <p><label for="<?php echo $name; ?>"><?php echo $label; ?></label></p>
  <p><input type="text" name="<?php echo $name; ?>" value="<?php echo esc_html($value); ?>" ></p>
  <?php wp_enqueue_script( 'wp-color-picker' );
  $data = '(function( $ ) {
        var options = {
            defaultColor: false,
            change: function(event, ui){},
            clear: function() {},
            hide: true,
            palettes: true
        };
        $("input:text[name=\''.$name.'\']").wpColorPicker(options);
    })( jQuery );';
    wp_add_inline_script( 'wp-color-picker', $data, 'after' ) ;

    $res = ob_get_clean();
    echo apply_filters('admin_input_form_tag', $res, $name);
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
  echo apply_filters('admin_input_form_tag', $res, $name);
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
    generate_selectbox_tag($name, $options, $value, __( 'フォーマット（広告ユニット）', THEME_NAME ));
    //ラベル表示の設定
    if ($label_name) {
      echo '<p>';
      generate_checkbox_tag( $label_name, $label_value, __( '広告ラベルを表示', THEME_NAME ));
      echo '</p>';
    }

    //本文中広告用の設定
    if (isset($body_ad_name)){
      echo '<div>';
      generate_checkbox_tag( $body_ad_name, $body_ad_value, __( '広告の表示数を制御する', THEME_NAME ));
        echo '<div class="indent">';
        generate_label_tag(OP_AD_POS_CONTENT_MIDDLE_COUNT, __( '先頭から', THEME_NAME ));
        generate_number_tag(OP_AD_POS_CONTENT_MIDDLE_COUNT,  get_ad_pos_content_middle_count(), -1, -1, 100, 1, 70);
        echo '<span>'.__( '個まで', THEME_NAME ).'</span>';
        echo '<br>';
        echo '<span>'.__( '※-1で全てのH2見出し手前に広告を挿入', THEME_NAME ).'</span>';
        echo '</div>';
      echo '</div>';
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
    generate_selectbox_tag($name, $options, $value, __( 'フォーマット（広告ユニット）', THEME_NAME ));
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

              title: "<?php _e( '画像を選択してください。', THEME_NAME ) ?>",

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
  echo apply_filters('admin_input_form_tag', $res, $name);
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

  echo '<ul class="category-check-list-root"><li>'; echo "\n";

  hierarchical_category_check_list( $cat, $name, $checks );

  echo '</div>';

  $res = ob_get_clean();

  echo apply_filters('admin_input_form_tag', $res, $name);
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

  if( $next ) {
    foreach( $next as $cat ) :
      $checked = '';

      if (in_array($cat->term_id, $checks)) {
        $checked = ' checked="checked"';
      }
      $id = $name.'_'.$cat->term_id;
      echo '<ul><li><input type="checkbox" name="'.$name.'[]" id="'.$id.'" value="'.$cat->term_id.'"'.$checked.'><label for="'.$id.'">' . $cat->name . '</label>';
      hierarchical_category_check_list( $cat->term_id, $name, $checks );
    endforeach;
  };
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
  echo apply_filters('admin_input_form_tag', $html, $name);
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

  $id = $name.'_'.'is_front_page';
  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_front_page" id="'.$id.'" ';
  checked(in_array('is_front_page', $checks));
  echo '><label for="'.$id.'">' . __( 'フロントページのみ', THEME_NAME ) . '</label></li>';

  $id = $name.'_'.'is_single';
  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_single" id="'.$id.'" ';
  checked(in_array('is_single', $checks));
  echo '><label for="'.$id.'">' . __( '投稿', THEME_NAME ) . '</label></li>';

  $id = $name.'_'.'is_page';
  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_page" id="'.$id.'" ';
  checked(in_array('is_page', $checks));
  echo '><label for="'.$id.'">' . __( '固定ページ', THEME_NAME ) . '</label></li>';

  $id = $name.'_'.'is_category';
  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_category" id="'.$id.'" ';
  checked(in_array('is_category', $checks));
  echo '><label for="'.$id.'">' . __( 'カテゴリー一覧', THEME_NAME ) . '</label></li>';

  $id = $name.'_'.'is_tag';
  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_tag" id="'.$id.'" ';
  checked(in_array('is_tag', $checks));
  echo '><label for="'.$id.'">' . __( 'タグ一覧', THEME_NAME ) . '</label></li>';

  $id = $name.'_'.'is_author';
  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_author" id="'.$id.'" ';
  checked(in_array('is_author', $checks));
  echo '><label for="'.$id.'">' . __( '著者一覧', THEME_NAME ) . '</label></li>';

  $id = $name.'_'.'is_archive';
  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_archive" id="'.$id.'" ';
  checked(in_array('is_archive', $checks));
  echo '><label for="'.$id.'">' . __( 'アーカイブ一覧', THEME_NAME ) . '</label></li>';

  $id = $name.'_'.'is_search';
  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_search" id="'.$id.'" ';
  checked(in_array('is_search', $checks));
  echo '><label for="'.$id.'">' . __( '検索結果一覧', THEME_NAME ) . '</label></li>';

  $id = $name.'_'.'is_404';
  echo '<li><input type="checkbox" name="'.$name.'[]" value="is_404" id="'.$id.'" ';
  checked(in_array('is_404', $checks));
  echo '><label for="'.$id.'">' . __( '404ページ', THEME_NAME ) . '</label></li>';

  if (is_amp_enable()) {
    $id = $name.'_'.'is_amp';
    echo '<li><input type="checkbox" name="'.$name.'[]" value="is_amp" id="'.$id.'" ';
    checked(in_array('is_amp', $checks));
    echo '><label for="'.$id.'">' . __( 'AMPページ', THEME_NAME ) . '</label></li>';
  }

  if (is_wpforo_exist()) {
    $id = $name.'_'.'is_wpforo_plugin_page';
    echo '<li><input type="checkbox" name="'.$name.'[]" value="is_wpforo_plugin_page" id="'.$id.'" ';
    checked(in_array('is_wpforo_plugin_page', $checks));
    echo '><label for="'.$id.'">' . __( 'wpForoページ', THEME_NAME ) . '</label></li>';
  }

  if (is_bbpress_exist()) {
    $id = $name.'_'.'is_bbpress_page';
    echo '<li><input type="checkbox" name="'.$name.'[]" value="is_bbpress_page" id="'.$id.'" ';
    checked(in_array('is_bbpress_page', $checks));
    echo '><label for="'.$id.'">' . __( 'bbPressページ', THEME_NAME ) . '</label></li>';
  }

  if (is_buddypress_exist()) {
    $id = $name.'_'.'is_buddypress_page';
    echo '<li><input type="checkbox" name="'.$name.'[]" value="is_buddypress_page" id="'.$id.'" ';
    checked(in_array('is_buddypress_page', $checks));
    echo '><label for="'.$id.'">' . __( 'BuddyPressページ', THEME_NAME ) . '</label></li>';
  }

  if (is_plugin_fourm_exist()) {
    $id = $name.'_'.'is_plugin_fourm_page';
    echo '<li><input type="checkbox" name="'.$name.'[]" value="is_plugin_fourm_page" id="'.$id.'" ';
    checked(in_array('is_plugin_fourm_page', $checks));
    echo '><label for="'.$id.'">' . __( 'プラグインフォーラムページ（bbPress、BuddyPress、wpForo）', THEME_NAME ) . '</label></li>';
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
    $id = $name.'_'.$uid;
    echo '<li><input type="checkbox" name="'.$name.'[]" value="'.$uid.'" id="'.$id.'"';
    checked(in_array($uid, $checks));
    echo '><label for="'.$id.'">' . $user->display_name . '</label></li>';
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


//タグエディット
if ( !function_exists( 'generate_tag_check_list' ) ):
function generate_tag_check_list( $name, $value, $width = 0 ) {
  if ($width == 0) {
    $width = 'auto';
  } else {
    $width = $width.'px';
  }

  echo '<div class="tab-content tag-check-list '.$name.'-list" style="width: '.$width.';">';

  echo '<p>'.__( 'タグID入力', THEME_NAME ).'</p>';
  generate_textbox_tag($name, $value, __( '例：111,222,333', THEME_NAME ));
  echo '<p>'.__( 'タグIDをカンマ区切りで入力してください。', THEME_NAME ).'</p>';

  echo '</div>';
}
endif;


//カスタム投稿タイプエディット
if ( !function_exists( 'generate_custom_post_type_check_list' ) ):
function generate_custom_post_type_check_list( $name, $checks, $width = 0 ) {
  if ($width == 0) {
    $width = 'auto';
  } else {
    $width = $width.'px';
  }

  if (empty($checks)) {
    $checks = array();
  }

  echo '<div class="tab-content custom-post-type-check-list '.$name.'-list cocoon-donation-privilege" style="width: '.$width.';"><ul>';

  $custom_post_types = get_custum_post_types();

  foreach($custom_post_types as $custom_post_type) {
    $post_type_object = get_post_type_object($custom_post_type);
    $label = ($post_type_object->label) ? $post_type_object->label : $custom_post_type;
    $id = $id = $name.'_'.$custom_post_type;

    echo '<li><input type="checkbox" name="'.esc_attr($name).'[]" value="'.esc_attr($custom_post_type).'" id="'.esc_attr($id).'"';
    checked(in_array($custom_post_type, $checks));
    echo '><label for="'.$id.'">' . esc_html($label) . '</label></li>';
  } //foreach

  echo '</ul></div>';

  // echo '<div class="tab-content tag-check-list '.$name.'-list" style="width: '.$width.';">';

  // echo '<p>'.__( 'タグID入力', THEME_NAME ).'</p>';
  // generate_textbox_tag($name, $value, __( '例：111,222,333', THEME_NAME ));
  // echo '<p>'.__( 'タグIDをカンマ区切りで入力してください。', THEME_NAME ).'</p>';

  // echo '</div>';
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
function generate_popular_entries_tag($atts){
  extract(shortcode_atts(array(
    'days' => 'all',
    'entry_count' => 5,
    'entry_type' => ET_DEFAULT,
    'ranking_visible' => 0,
    'pv_visible' => 0,
    'cat_ids' => array(),
    'children' => 0,
    'exclude_post_ids' => array(),
    'exclude_cat_ids' => array(),
    'bold' => 0,
    'arrow' => 0,
    'class' => null,
    'snippet' => 0,
    'author' => null,
    'post_type' => 'post',
    'horizontal' => 0,
  ), $atts));

  //Swiperスクリプトコードを呼び出すかどうか
  global $_IS_SWIPER_ENABLE;
  if ($horizontal) {
    $_IS_SWIPER_ENABLE = true;
  }


  $records = get_access_ranking_records($days, $entry_count, $entry_type, $cat_ids, $exclude_post_ids, $exclude_cat_ids, $children, $author, $post_type, $snippet);

  $thumb_size = get_popular_entries_thumbnail_size($entry_type);
  $atts = array(
    'type' => $entry_type,
    'ranking_visible' => $ranking_visible,
    'pv_visible' => $pv_visible,
    'bold' => $bold,
    'arrow' => $arrow,
    'class' => $class,
    'horizontal' => $horizontal,
  );
  $cards_classes = get_additional_widget_entry_cards_classes($atts);
  $swiper_slide = null;
  if ($horizontal) {
    $swiper_slide = ' swiper-slide';
  }
  ?>
  <div class="popular-entry-cards widget-entry-cards no-icon cf<?php echo $cards_classes; ?>">
  <?php if ( $horizontal ) : ?>
    <div class="swiper-wrapper">
  <?php endif; ?>
  <?php if ( $records ) :
    $i = 1;
    foreach ($records as $post):
      $permalink = get_permalink( $post->ID );
      $title = $post->post_title;
      $no_thumbnail_url = ($entry_type == ET_DEFAULT) ? get_no_image_120x68_url($post->ID) : get_no_image_320x180_url($post->ID);
      $w   = ($entry_type == ET_DEFAULT) ? THUMB120WIDTH  : THUMB320WIDTH;
      $h   = ($entry_type == ET_DEFAULT) ? THUMB120HEIGHT : THUMB320HEIGHT;

      $post_thumbnail = get_the_post_thumbnail( $post->ID, $thumb_size, array('alt' => '') );
      $pv = $post->sum_count;

      if ($post_thumbnail) {
        $post_thumbnail_img = $post_thumbnail;
      } else {
        $post_thumbnail_img = get_original_image_tag($no_thumbnail_url, $w, $h, 'no-image popular-entry-card-thumb-no-image widget-entry-card-thumb-no-image', '');
      }

      //スニペット表示
      $snippet_tag = '';
      //「タイトルを重ねた大きなサムネイル」の時はスニペットを表示させない
      if ($snippet && isset($post->ID) && isset(get_post($post->ID)->post_content) && $entry_type !== ET_LARGE_THUMB_ON) {
        $snippet_tag = '<div class="popular-entry-card-snippet widget-entry-card-snippet card-snippet">'.get_the_snippet(get_post($post->ID)->post_content, get_entry_card_excerpt_max_length(), $post->ID).'</div>';
      }

      $pv_tag = null;
      if ($pv_visible){
        $pv_text = $pv == '1' ? $pv.' view' : $pv.' views';
        $pv_tag = '<span class="popular-entry-card-pv widget-entry-card-pv">'.$pv_text.'</span>';
      }
      ?>
  <a href="<?php echo $permalink; ?>" class="popular-entry-card-link widget-entry-card-link a-wrap no-<?php echo $i; ?><?php echo $swiper_slide; ?>" title="<?php echo esc_attr($title); ?>">
    <div <?php post_class( array('post-'.$post->ID, 'popular-entry-card', 'widget-entry-card', 'e-card', 'cf'), $post->ID ); ?>>
      <figure class="popular-entry-card-thumb widget-entry-card-thumb card-thumb">
        <?php echo $post_thumbnail_img; ?>
        <?php
        $is_visible = apply_filters('is_popular_entry_card_category_label_visible', false);
        $is_visible = apply_filters('is_widget_entry_card_category_label_visible', $is_visible);
        the_nolink_category($post->ID, $is_visible); //カテゴリーラベルの取得 ?>
      </figure><!-- /.popular-entry-card-thumb -->

      <div class="popular-entry-card-content widget-entry-card-content card-content">
        <div class="popular-entry-card-title widget-entry-card-title card-title"><?php echo $title;?></div>
        <?php echo $snippet_tag; ?>
        <?php if ($entry_type != ET_LARGE_THUMB_ON): ?>
          <?php echo $pv_tag; ?>
        <?php endif ?>
        <?php generate_widget_entry_card_date('popular', $post->ID); ?>
      </div><!-- /.popular-entry-content -->
      <?php if ($entry_type == ET_LARGE_THUMB_ON): ?>
        <?php echo $pv_tag; ?>
      <?php endif ?>
    </div><!-- /.popular-entry-card -->
  </a><!-- /.popular-entry-card-link -->

  <?php
  $i++;
  endforeach;
  else :
    echo '<p>'.__( '人気記事は見つかりませんでした。', THEME_NAME ).'</p>';//見つからない時のメッセージ
  endif; ?>
  <?php if ( $horizontal ) : ?>
    </div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
  <?php endif; ?>
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
function generate_widget_entries_tag($atts){
  extract(shortcode_atts(array(
    'entry_count' => 5,
    'cat_ids' => array(),
    'tag_ids' => array(),
    'type' => ET_DEFAULT,
    'include_children' => 0,
    'post_type' => null,
    'taxonomy' => 'category',
    'sticky' => 0,
    'random' => 0,
    'modified' => is_index_sort_orderby_modified(),
    'order' => 'desc',
    'action' => null,
    'exclude_cat_ids' => array(),
    'bold' => 0,
    'arrow' => 0,
    'class' => null,
    'snippet' => 0,
    'author' => null,
    'offset' => 0,
    'horizontal' => 0,
    'ex_posts' => null,
    'ex_cats' => null,
    'ordered_posts' => null,
  ), $atts));

  //Swiperスクリプトコードを呼び出すかどうか
  global $_IS_SWIPER_ENABLE;
  if ($horizontal) {
    $_IS_SWIPER_ENABLE = true;
  }

  //ランダムが有効な時は関連記事
  if ($random) {
    $prefix = WIDGET_RELATED_ENTRY_CARD_PREFIX;
  } else {
    $prefix = WIDGET_NEW_ENTRY_CARD_PREFIX;
  }

  $args = array(
    'posts_per_page' => $entry_count,
    'no_found_rows' => true,
    'action' => $action,
    'offset' => $offset,
  );
  if (!$sticky) {
    $args += array(
      'ignore_sticky_posts' => true,
    );
  }
  //並べ替え
  if ($order) {
    $args += array(
      'order' => strtoupper($order),
    );
  }
  //著者オプション
  if ($author){
    $args += array(
      'author' => $author,
    );
  }
  //除外カテゴリー
  $exclude_category_ids = get_archive_exclude_category_ids();
  if ($exclude_category_ids && is_array($exclude_category_ids)) {
    $args += array(
      'category__not_in' => $exclude_category_ids,
    );
  }
  //除外投稿
  $exclude_post_ids = get_archive_exclude_post_ids();
  if ($exclude_post_ids && is_array($exclude_post_ids)) {
    if (isset($args['post__not_in']) && is_array($args['post__not_in'])) {
      foreach ($exclude_post_ids as $exclude_post_id) {
        array_push($args['post__not_in'], $exclude_post_id);
      }
    } else {
      $args['post__not_in'] = $exclude_post_ids;
    }
  }
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
  //関連記事の場合は表示ページを除外
  if (is_single() && $random) {
    $post_id = get_the_ID();
    if (isset($args['post__not_in'])) {
      $args['post__not_in'][] = $post_id;
    } else {
      $args['post__not_in'] = array($post_id);
    }
  }

  //除外カテゴリーの設定
  if (!empty($exclude_cat_ids)) {
    $cat_ids = array_diff($cat_ids, $exclude_cat_ids);
  }
  //カテゴリー・タグの指定
  if ( $cat_ids || $tag_ids ) {
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
    $args += array(
      'tax_query' => array(
        $tax_querys,
        'relation' => 'AND'
      )
    );
  }
  $thumb_size = get_widget_entries_thumbnail_size($type);

  //除外記事ショートコードオプション
  if ($ex_posts) {
    $args += array(
      'post__not_in' => $ex_posts,
    );
  }
  //除外カテゴリーショートコードオプション
  if ($ex_cats) {
    $args += array(
      'category__not_in' => $ex_cats,
    );
  }
  //順序付きポスト
  if ($ordered_posts) {
    $post_type = 'post,page';// デフォルトでpost_typeは投稿と固定ページ
    $args = array(
      'ignore_sticky_posts' => true, // 固定記事は表示しない
      'posts_per_page'      => -1,   // 表示数を設定した記事を全件表示
      'post__in'  => $ordered_posts, // post__inで投稿IDを指定
      'orderby'   => 'post__in',     // 指定したID順にソート
    );
  }
  //投稿タイプ
  if ($post_type) {
    $args += array(
      'post_type' => explode(',', $post_type)
    );
  }

  if ($random) {
    $args = apply_filters('widget_related_entries_args', $args);
    $thumb_size = apply_filters('get_related_entries_thumbnail_size', $thumb_size, $type);
  } else {
    $args = apply_filters('widget_new_entries_args', $args);
    $thumb_size = apply_filters('get_new_entries_thumbnail_size', $thumb_size, $type);
  }
  $args = apply_filters('widget_entries_args', $args);

  //クエリの作成
  $query = new WP_Query( $args );
  $atts = array(
    'type' => $type,
    'bold' => $bold,
    'arrow' => $arrow,
    'class' => $class,
    'horizontal' => $horizontal,
  );
  $cards_classes = get_additional_widget_entry_cards_classes($atts);
  ?>
  <div class="<?php echo $prefix; ?>-entry-cards widget-entry-cards no-icon cf<?php echo $cards_classes; ?>">
  <?php if ( $horizontal ) : ?>
    <div class="swiper-wrapper">
  <?php endif; ?>
  <?php //if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
  <?php if ( $query -> have_posts() ) : while ( $query -> have_posts() ) : $query -> the_post(); ?>
    <?php //エントリーカードリンクタグの生成
    $atts = array(
      'prefix' => $prefix,
      'url' => get_the_permalink(),
      'title' => get_the_title(),
      'thumb_size' => $thumb_size,
      'type' => $type,
      'horizontal' => $horizontal,
    );
    if ($snippet) {
      $atts += array(
        'snippet' => get_the_snippet( get_the_content(''), get_entry_card_excerpt_max_length() ),
      );
    }
    //var_dump($atts);
    echo get_widget_entry_card_link_tag($atts); ?>
  <?php endwhile;
  else :
    echo '<p>'.__( '記事は見つかりませんでした。', THEME_NAME ).'</p>';//見つからない時のメッセージ
  endif; ?>
  <?php wp_reset_postdata(); ?>
  <?php //wp_reset_query(); ?>
  <?php if ( $horizontal ) : ?>
    </div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
  <?php endif; ?>
  </div>
<?php
}
endif;

//ウィジェットエントリーカードサムネイルの取得
if ( !function_exists( 'get_widget_entry_card_thumbnail_tag' ) ):
function get_widget_entry_card_thumbnail_tag($prefix, $thumb_size, $type){
  global $post;
  ob_start();
  if ( has_post_thumbnail() ){ // サムネイルを持っているときの処理
    the_post_thumbnail( $thumb_size, array('alt' => '') );
  } else { // サムネイルを持っていないときの処理
    echo get_widget_entry_card_no_image_tag($type, $prefix);
  }
  if (!is_widget_navi_entry_card_prefix($prefix)) {//ナビカードではないとき
    if ($prefix == WIDGET_RELATED_ENTRY_CARD_PREFIX) {//関連記事
      $is_visible = apply_filters('is_widget_related_entry_card_category_label_visible', false);
    } else {//新着記事
      $is_visible = apply_filters('is_new_entry_card_category_label_visible', false);
    }
    $is_visible = apply_filters('is_widget_entry_card_category_label_visible', $is_visible);
    $post_id = isset($post->ID) ? $post->ID : null;
    the_nolink_category($post_id, $is_visible); //カテゴリーラベルの取得
  }
  return ob_get_clean();
}
endif;

//ナビカードサムネイルの取得
if ( !function_exists( 'get_navi_entry_card_thumbnail_tag' ) ):
function get_navi_entry_card_thumbnail_tag($image_attributes, $title, $class){
  return get_original_image_tag($image_attributes[0], $image_attributes[1], $image_attributes[2], $class, $title);
}
endif;

//ナビカードのプレフィックスかどうか
if ( !function_exists( 'is_widget_navi_entry_card_prefix' ) ):
function is_widget_navi_entry_card_prefix($prefix){
  return $prefix == WIDGET_NAVI_ENTRY_CARD_PREFIX;
}
endif;

//ウィジェットエントリーカードもNO IMAGEタグの取得
if ( !function_exists( 'get_widget_entry_card_no_image_tag' ) ):
function get_widget_entry_card_no_image_tag($type, $prefix){
  $is_large_image_use = is_widget_entry_card_large_image_use($type);
  $url = (!$is_large_image_use) ? get_no_image_120x68_url() : get_no_image_320x180_url();
  $w   = (!$is_large_image_use) ? THUMB120WIDTH  : THUMB320WIDTH;
  $h   = (!$is_large_image_use) ? THUMB120HEIGHT : THUMB320HEIGHT;

  $tag = get_original_image_tag($url, $w, $h, 'no-image '.$prefix.'-entry-card-thumb-no-image widget-entry-card-thumb-no-image', '');
  return $tag;
}
endif;

//ウィジェットエントリーカードの日付
if ( !function_exists( 'generate_widget_entry_card_date' ) ):
function generate_widget_entry_card_date($prefix, $post_id = null){?>
<?php do_action( 'widget_entry_card_date_before', $prefix, $post_id); ?>
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
  $description = '';
  $user_id = get_the_author_meta( 'ID' );
  if (!$user_id || !is_singular()) {
    $user_id = get_sns_default_follow_user();
  }
  if ($id && get_userdata( $id )) {
    $user_id = $id;
  }
  //WordPressインストール初期時でユーザーが取得できないとき
  if ($user_id === 0) {
    # code...
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
          if (empty($description)) {
            $description = get_the_author_meta('description', $user_id);
          }
          $description = wpautop($description);
          $description = do_shortcode($description);

          if (!is_buddypress_page()) {
            //プロフィールページURLの取得
            $profile_page_url = get_the_author_profile_page_url($user_id);

            $author_display_name = strip_tags(get_the_author_display_name($user_id));
            if ($profile_page_url) {
              $name = '<a href="'.esc_url($profile_page_url).'">'.esc_html($author_display_name).'</a>';
            } else {
              $user = get_userdata( $user_id );
              if ($user) {
                $name = sprintf( '<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
                  esc_url( get_author_posts_url( $user->ID, $user->user_nicename ) ),
                  /* translators: %s: author's display name */
                  esc_attr( sprintf( __( 'Posts by %s' ), $user->display_name ) ),
                  $user->display_name
                );
              } else {
                $name = __( 'NO USER' );
              }
            }
          } else {
            $author_display_name = strip_tags(get_the_author_display_name($user_id));
            $author_website_url = strip_tags(get_the_author_website_url($user_id));
            $name = $author_display_name;
            if ($author_website_url) {
              $name = '<a href="'.esc_url($author_website_url).'" target="_blank" rel="nofollow noopener">'.esc_html($author_display_name).'</a>';
            }
          }
          echo apply_filters( 'the_author_box_name', $name, $user_id );


        } else {
          echo __( '未登録のユーザーさん', THEME_NAME );
        }
         ?>
      </div>
      <div class="author-description">
        <?php
        if ($description) {
          echo apply_filters( 'the_author_box_description', $description, $user_id );
        } elseif (!$user_id) {
          if (is_buddypress_exist()) {
            $msg = __( '未登録のユーザーさんです。', THEME_NAME );
            $msg .= '<br>';
            $msg .= __( 'ログイン・登録はこちら→', THEME_NAME );
            $msg .= '<a href="'.wp_login_url().'">';
            $msg .= __( 'ログインページ', THEME_NAME );
            $msg .= '</a>';
            echo wpautop($msg);
          } else {//WordPressインストール初期時のユーザーID。未ログインでCocoon設定を保存していない時
            echo wpautop(__( '未登録ユーザーです。ログインして、Cocoon設定の保存ボタンを押してください。', THEME_NAME ));
          }
        } elseif (is_user_logged_in()) {
          echo wpautop(__( 'プロフィール内容は管理画面から変更可能です→', THEME_NAME ).'<a href="' . esc_url(home_url() . '/wp-admin/user-edit.php?user_id='.get_the_author_meta( 'ID' )).'">'.__( 'プロフィール設定画面', THEME_NAME ).'</a><br>'.__( '※このメッセージは、ログインユーザーにしか表示されません。', THEME_NAME ));
        }
        ?>

      </div>
      <?php if ($user_id): ?>
      <div class="profile-follows author-follows">
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

//フォントの太さサンプルタグの取得
if ( !function_exists( 'get_font_weight_demo_tag' ) ):
function get_font_weight_demo_tag($weights){
  $taged_weights = array();
  foreach ($weights as $weight) {
    $taged_weights[] = '<span style="font-weight:'.$weight.';">'.$weight.'</span>';
  }
  return implode(', ', $taged_weights);
}
endif;

//ウィジェットエントリーカードリンクタグの取得
if ( !function_exists( 'get_widget_entry_card_link_tag' ) ):
function get_widget_entry_card_link_tag($atts){
  extract(shortcode_atts(array(
    'prefix' => WIDGET_NEW_ENTRY_CARD_PREFIX,
    'url' => null,
    'title' => null,
    'snippet' => null,
    'thumb_size' => null,
    'image_attributes' => null,
    'ribbon_no' => null,
    'type' => null,
    'classes' => null,
    'object' => 'post',
    'object_id' => null,
    'horizontal' => 0,
  ), $atts));
  $class_text = null;
  if (isset($classes[0]) && !empty($classes[0])) {
    $class_text = ' '.implode(' ', $classes);
  }
  //リボンタグの取得
  $ribbon_tag = get_navi_card_ribbon_tag($ribbon_no);
  $swiper_slide = null;
  if ($horizontal) {
    $swiper_slide = ' swiper-slide';
  }

  //クラス名の取得
  if ($prefix === WIDGET_NAVI_ENTRY_CARD_PREFIX) {
    $div_class = 'class="'.$prefix.'-entry-card widget-entry-card e-card cf"';
  } else {
    $div_class = 'class="'.implode(' ', get_post_class( array('post-'.get_the_ID(), $prefix.'-entry-card', 'widget-entry-card', 'e-card', 'cf') )).'"';
  }

  ob_start(); ?>
  <a href="<?php echo esc_url($url); ?>" class="<?php echo $prefix; ?>-entry-card-link widget-entry-card-link a-wrap<?php echo $class_text; ?><?php echo $swiper_slide; ?>" title="<?php echo esc_attr($title); ?>">
    <div <?php echo $div_class; ?>>
      <?php echo $ribbon_tag; ?>
      <figure class="<?php echo $prefix; ?>-entry-card-thumb widget-entry-card-thumb card-thumb">
        <?php //$prefixがnaviのとき
        if (is_widget_navi_entry_card_prefix($prefix)) {
          $class = 'navi-entry-card-image widget-entry-card-image card-thumb';

          //投稿・固定ページ・カスタム投稿の場合
          $post_types = get_custum_post_types();
          if ($object === 'post' || $object === 'page' || in_array($object, $post_types)) {
            if ($type === ET_DEFAULT) {
              $size = THUMB120;
            } else {
              $size = THUMB320;
            }
            $attr = array();
            $attr['class'] = $class;

            $thumbnail_tag = get_the_post_thumbnail( $object_id, $size, $attr );
            if ($thumbnail_tag) {
              echo $thumbnail_tag;
            } else {
              echo get_widget_entry_card_no_image_tag(ET_DEFAULT, $prefix);
            }
          } else {
            if ($object === 'category') {
              //カテゴリーの場合
              $class = 'category-image '.$class;
              if (!get_the_category_eye_catch_url($object_id)) {
                //NO IMAGEの場合
                $class = 'no-image '.$class;
              }
            }
            echo get_navi_entry_card_thumbnail_tag($image_attributes, $title, $class);
          }
        } else {
          //新着記事・関連記事など
          echo get_widget_entry_card_thumbnail_tag($prefix, $thumb_size, $type);
        }
        ?>
      </figure><!-- /.entry-card-thumb -->

      <div class="<?php echo $prefix; ?>-entry-card-content widget-entry-card-content card-content">
        <div class="<?php echo $prefix; ?>-entry-card-title widget-entry-card-title card-title"><?php echo $title;?></div>
        <?php if ($snippet): ?>
        <div class="<?php echo $prefix; ?>-entry-card-snippet widget-entry-card-snippet card-snippet"><?php echo $snippet; ?></div>
        <?php endif; ?>
        <?php
        if (!is_widget_navi_entry_card_prefix($prefix)) {
          generate_widget_entry_card_date($prefix);
        } ?>
      </div><!-- /.entry-content -->
    </div><!-- /.entry-card -->
  </a><!-- /.entry-card-link -->
<?php
  return ob_get_clean();
}
endif;

//イメージURLから属性の取得
if ( !function_exists( 'get_navi_card_image_url_attributes' ) ):
function get_navi_card_image_url_attributes($image_url, $type = ET_DEFAULT){
  if (!$image_url) {
    return false;
  }
  if (is_widget_entry_card_large_image_use($type)) {
    $w = THUMB320WIDTH;
    $h = THUMB320HEIGHT;
    $aw = THUMB320WIDTH_DEF;
    $ah = THUMB320HEIGHT_DEF;
  } else {
    $w = THUMB120WIDTH;
    $h = THUMB120HEIGHT;
    $aw = THUMB120WIDTH_DEF;
    $ah = THUMB120HEIGHT_DEF;
  }

  $sized_image_url = get_image_sized_url($image_url, $w, $h);
  $image_attributes = array();
  $image_attributes[1] = $aw;
  $image_attributes[2] = $ah;
  if (file_exists(url_to_local($sized_image_url))) {
    $image_attributes[0] = $sized_image_url;
  } else {
    $image_attributes[0] = $image_url;
  }
  return $image_attributes;
}
endif;

//大きな画像を使用するか
if ( !function_exists( 'is_widget_entry_card_large_image_use' ) ):
function is_widget_entry_card_large_image_use($type){
  return ($type == ET_LARGE_THUMB) || ($type == ET_LARGE_THUMB_ON);
}
endif;

//ナビカードイメージ属性の取得
if ( !function_exists( 'get_navi_card_image_attributes' ) ):
function get_navi_card_image_attributes($menu, $type = ET_DEFAULT){
  $url = $menu->url;
  $object_id = $menu->object_id;
  $object = $menu->object;

  $is_large_image_use = is_widget_entry_card_large_image_use($type);
  $thumb_size = $is_large_image_use ? THUMB320 : THUMB120;
  //大きなサムネイル画像を使用する場合
  $image_attributes = array();
  $post_types = get_custum_post_types();
  //投稿ページ・固定ページ・カスタム投稿ページ
  if ($object == 'post' || $object == 'page' || in_array($object, $post_types)) {
    $thumbnail_id = get_post_thumbnail_id($object_id);
    $image_attributes = wp_get_attachment_image_src($thumbnail_id, $thumb_size);
  }
  elseif ($object == 'category'){//カテゴリーアイキャッチの取得
    $image_url = get_the_category_eye_catch_url($object_id);
    $thumb_id = attachment_url_to_postid( $image_url );
    $thumb_img = wp_get_attachment_image_src($thumb_id, $thumb_size);
    if (isset($thumb_img[0]) && $thumb_img[0]) {
      $image_url = $thumb_img[0];
    }
    $image_attributes = get_navi_card_image_url_attributes($image_url, $type);
  }
  elseif ($object == 'post_tag'){//タグアイキャッチの取得
    $image_url = get_the_tag_eye_catch_url($object_id);
    $thumb_id = attachment_url_to_postid( $image_url );
    $thumb_img = wp_get_attachment_image_src($thumb_id, $thumb_size);
    if (isset($thumb_img[0]) && $thumb_img[0]) {
      $image_url = $thumb_img[0];
    }
    $image_attributes = get_navi_card_image_url_attributes($image_url, $type);
  }
  elseif ($object == 'custom') {//カスタムメニュー
    //カテゴリーページのアイキャッチを取得
    $category_obj = url_to_category_object($url);
    if ($category_obj && isset($category_obj->term_id)) {
      $image_url = get_the_category_eye_catch_url($category_obj->term_id);
      $image_attributes = get_navi_card_image_url_attributes($image_url, $type);
    } else {
      //タグページのアイキャッチを取得
      $tag_obj = url_to_tag_object($url);
      if ($tag_obj && isset($tag_obj->term_id)) {
        $image_url = get_the_tag_eye_catch_url($tag_obj->term_id);
        $image_attributes = get_navi_card_image_url_attributes($image_url, $type);
      }
    }

  }

  //アイキャッチがない場合
  if (!$image_attributes) {
    $image_attributes = array();
    if ($is_large_image_use) {
      $image_attributes[0] = get_no_image_320x180_url($object_id, false); //postするタイプのページではない引数を追加
      $image_attributes[1] = THUMB320WIDTH_DEF;
      $image_attributes[2] = THUMB320HEIGHT_DEF;
    } else {
      $image_attributes[0] = get_no_image_120x68_url($object_id, false); //postするタイプのページではない引数を追加
      $image_attributes[1] = THUMB120WIDTH_DEF;
      $image_attributes[2] = THUMB120HEIGHT_DEF;
    }
  }
  return $image_attributes;
}
endif;

//リボンタグ取得関数
if ( !function_exists( 'get_navi_card_ribbon_tag' ) ):
function get_navi_card_ribbon_tag($ribbon_no){
  $caption = null;
  // おすすめ・新着記事　名称を変えれば何にでも使える（注目・必見・お得etc）
  switch ($ribbon_no) {
    case '1':
      $caption = __( 'おすすめ', THEME_NAME );
      break;
    case '2':
      $caption = __( '新着', THEME_NAME );
      break;
    case '3':
      $caption = __( '注目', THEME_NAME );
      break;
    case '4':
      $caption = __( '必見', THEME_NAME );
      break;
    case '5':
      $caption = __( 'お得', THEME_NAME );
      break;
  }
  $tag = '';
  if ($caption){
    $caption = apply_filters('get_navi_card_ribbon_caption', $caption, $ribbon_no);
    $tag = '<div class="ribbon ribbon-top-left ribbon-color-'.$ribbon_no.'"><span>'.$caption.'</span></div>';
  }
  return $tag;
}
endif;

//ナビカードを囲むタグ
if ( !function_exists( 'get_navi_card_wrap_tag' ) ):
function get_navi_card_wrap_tag($atts){
  extract(shortcode_atts(array(
    'tag' => '',
    'type' => 0,
    'bold' => 0,
    'arrow' => 0,
    'class' => null,
    'horizontal' => 0,
  ), $atts));

  //Swiperスクリプトコードを呼び出すかどうか
  global $_IS_SWIPER_ENABLE;
  if ($horizontal) {
    $_IS_SWIPER_ENABLE = true;
    $tag = '<div class="swiper-wrapper">'.$tag.'</div><div class="swiper-button-prev"></div><div class="swiper-button-next"></div>';
  }

  $navi_card_class = get_additional_widget_entry_cards_classes($atts);
  $tag = '<div class="navi-entry-cards widget-entry-cards no-icon'.esc_attr($navi_card_class).'">'.$tag.'</div>';
  return $tag;
}
endif;

//新着記事生成タグ
if ( !function_exists( 'generate_info_list_tag' ) ):
function generate_info_list_tag($atts){
  extract(shortcode_atts(array(
    'count' => 5,
    'cats' => 'all',
    'caption' => __( '新着情報', THEME_NAME ),
    'frame' => 1,
    'divider' => 1,
    'modified' => 0,
    'offset' => 0,
    'action' => null,
  ), $atts));

  $args = array(
    'cat' => $cats,
    'no_found_rows' => true,
    'ignore_sticky_posts' => true,
    'posts_per_page' => $count,
    'offset' => $offset,
    'action' => $action,
  );

  //更新日順
  if ($modified) {
    $args += array(
      'orderby' => 'modified',
    );
  }

  //インデックス除外カテゴリー
  $exclude_category_ids = get_archive_exclude_category_ids();
  if ($exclude_category_ids && is_array($exclude_category_ids)) {
    $args += array(
      'category__not_in' => $exclude_category_ids,
    );
  }

  //投稿編集のその他設定「アーカイブに出力しない」を除外
  $exclude_post_ids = get_archive_exclude_post_ids();
  if ($exclude_post_ids && is_array($exclude_post_ids)) {
    $args['post__not_in'] = $exclude_post_ids;
  }

  $args = apply_filters( 'get_info_list_args', $args );
  $query = new WP_Query( $args );
  $frame_class = ($frame ? ' is-style-frame-border' : '');
  $divider_class = ($divider ? ' is-style-divider-line' : '');
  if( $query -> have_posts() ): //投稿が存在する時 ?>
  <div id="info-list" class="info-list<?php echo $frame_class; ?><?php echo $divider_class; ?>">
    <?php if ($caption): ?>
      <div class="info-list-caption"><?php echo esc_html($caption); ?></div>
    <?php endif; ?>
    <?php while ($query -> have_posts()) : $query -> the_post();
      $date = get_the_time(get_site_date_format());
      $update_date = get_update_time(get_site_date_format());
      if ($modified && $update_date) {
        $date = $update_date;
      }
    ?>
      <div class="info-list-item">
        <div class="info-list-item-content"><a href="<?php the_permalink(); ?>" class="info-list-item-content-link"><?php the_title();?></a></div>
        <?php do_action('info_list_item_meta_before'); ?>
        <div class="info-list-item-meta">
          <span class="info-list-item-date"><?php echo $date; ?></span><span class="info-list-item-categorys"><?php the_nolink_categories() ?></span>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
  <?php else :
    echo '<p>'.__( '記事は見つかりませんでした。', THEME_NAME ).'</p>';//見つからない時のメッセージ
  endif; ?>
  <?php wp_reset_postdata(); ?>
  <?php //wp_reset_query(); ?>
<?php
}
endif;
