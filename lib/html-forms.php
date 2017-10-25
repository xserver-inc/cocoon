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
if ( !function_exists( 'genelate_author_list_selectbox_tag' ) ):
function genelate_author_list_selectbox_tag($name, $value){
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
if ( !function_exists( 'genelate_selectbox_tag' ) ):
function genelate_selectbox_tag($name, $options, $now_value, $icon_font_visible = false){?>
<select name="<?php echo $name; ?>">
  <?php
  foreach ($options as $value => $caption) {
    //アイコンフォントを利用する場合
    $add_option_class = null;
    if ($icon_font_visible) {
      $add_option_class = ' class="fa '.$caption.'"';
    } ?>
    <option value="<?php echo $value; ?>"<?php the_option_selected($value, $now_value) ?><?php echo $add_option_class; ?>><?php echo $caption; ?></option>
  <?php } ?>
</select>
<?php
}
endif;


//チェックボックスの生成
if ( !function_exists( 'genelate_checkbox_tag' ) ):
function genelate_checkbox_tag($name, $now_value, $label){?>
  <input type="checkbox" name="<?php echo $name; ?>" value="1"<?php the_checkbox_checked($now_value); ?>><?php echo $label; ?>
  <?php
}
endif;


//ラジオボックスの生成
if ( !function_exists( 'genelate_radiobox_tag' ) ):
function genelate_radiobox_tag($name, $options, $now_value){?>
<ul>
  <?php foreach ($options as $value => $caption) { ?>
  <li><input type="radio" name="<?php echo $name; ?>" value="<?php echo $value; ?>"<?php the_checkbox_checked($value, $now_value) ?>><?php echo $caption; ?></li>
  <?php } ?>
</ul>
  <?php
}
endif;


//ラベルの生成
if ( !function_exists( 'genelate_label_tag' ) ):
function genelate_label_tag($name, $caption){?>
  <label for="<?php echo $name; ?>"><?php echo $caption; ?></label>
  <?php
}
endif;


//説明文の生成
if ( !function_exists( 'genelate_tips_tag' ) ):
function genelate_tips_tag($caption){?>
  <p class="tips"><?php echo $caption; ?></p>
  <?php
}
endif;


//テキストボックスの生成
if ( !function_exists( 'genelate_textbox_tag' ) ):
function genelate_textbox_tag($name, $value, $placeholder, $cols = DEFAULT_INPUT_COLS){?>
  <input type="text" name="<?php echo $name; ?>" size="<?php echo $cols; ?>" value="<?php echo $value; ?>" placeholder="<?php echo $placeholder; ?>">
  <?php
}
endif;

//テキストエリアの生成
if ( !function_exists( 'genelate_textarea_tag' ) ):
function genelate_textarea_tag($name, $value, $placeholder, $rows = DEFAULT_INPUT_ROWS,  $cols = DEFAULT_INPUT_COLS){?>
  <textarea name="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>" rows="<?php echo $rows; ?>" cols="<?php echo $cols; ?>"><?php echo $value; ?></textarea>
  <?php
}
endif;


//ナンバーボックスの生成
if ( !function_exists( 'genelate_number_tag' ) ):
function genelate_number_tag($name, $value, $min = 1, $max = 100){?>
  <input type="number" name="<?php echo $name; ?>" value="<?php echo $value; ?>" min="<?php echo $min; ?>" max="<?php echo $max; ?>">
  <?php
}
endif;

//サイトロゴの生成
if ( !function_exists( 'genelate_the_site_logo_tag' ) ):
function genelate_the_site_logo_tag($is_header = true){
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
if ( !function_exists( 'genelate_tooltip_tag' ) ):
function genelate_tooltip_tag($content){?>
  <span class="tooltip fa fa-question-circle">
    <span class="tip-content">
      <?php echo $content; ?>
    </span>
  </span>
  <?php
}
endif;


//カラーピッカーの生成
if ( !function_exists( 'genelate_color_picker_tag' ) ):
function genelate_color_picker_tag($name, $value, $label){?>
  <p><label for="<?php echo $name; ?>"><?php echo $label; ?></label></p>
  <p><input type="text" name="<?php echo $name; ?>" value="<?php echo $value; ?>" ></p>
  <?php wp_enqueue_script( 'wp-color-picker' );
  $data = minify_js('(function( $ ) {
        var options = {
            defaultColor: false,
            change: function(event, ui){},
            clear: function() {},
            hide: true,
            palettes: true
        };
        $("input:text[name='.$name.']").wpColorPicker(options);
    })( jQuery );');
    wp_add_inline_script( 'wp-color-picker', $data, 'after' ) ;

}
endif;


//メインカラム広告の詳細設定フォーム
if ( !function_exists( 'genelate_main_column_ad_detail_setting_forms' ) ):
function genelate_main_column_ad_detail_setting_forms($name, $value, $label_name, $label_value, $body_ad_name = null, $body_ad_value = null){ ?>
 <span class="toggle">
  <span class="toggle-link"><?php _e( '詳細設定', THEME_NAME ) ?></span>
  <div class="toggle-content">
    <div class="detail-area">
    <?php _e( 'フォーマット：', THEME_NAME ) ?>
    <?php
    $options = array(
      DATA_AD_FORMAT_AUTO => 'オート（AdSenseにおまかせ）',
      DATA_AD_FORMAT_HORIZONTAL => 'バナー',
      DATA_AD_FORMAT_RECTANGLE => 'レスポンシブレクタングル',
      AD_FORMAT_SINGLE_RECTANGLE => 'シングルレクタングル',
      AD_FORMAT_DABBLE_RECTANGLE => 'ダブルレクタングル',
    );
    genelate_selectbox_tag($name, $options, $value);
    //ラベル表示の設定
    echo '<p>';
    genelate_checkbox_tag( $label_name, $label_value, __( '広告ラベルを表示', THEME_NAME ));
    echo '</p>';

    //本文中広告用の設定
    if (isset($body_ad_name)){
      echo '<p>';
      genelate_checkbox_tag( $body_ad_name, $body_ad_value, __( '全てのH2見出し手前に広告を挿入', THEME_NAME ));
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
if ( !function_exists( 'genelate_sidebar_ad_detail_setting_forms' ) ):
function genelate_sidebar_ad_detail_setting_forms($name, $value, $label_name, $label_value){ ?>
 <span class="toggle">
  <span class="toggle-link"><?php _e( '詳細設定', THEME_NAME ) ?></span>
  <div class="toggle-content">
    <div class="detail-area">
    <?php _e( 'フォーマット：', THEME_NAME ) ?>
    <?php
    $options = array(
      DATA_AD_FORMAT_AUTO => 'オート（AdSenseにおまかせ）',
      DATA_AD_FORMAT_HORIZONTAL => 'バナー',
      DATA_AD_FORMAT_RECTANGLE => 'レクタングル',
      DATA_AD_FORMAT_VERTICAL => 'ラージスカイスクレイパー',
    );
    genelate_selectbox_tag($name, $options, $value);
    //ラベル表示の設定
    echo '<p>';
    genelate_checkbox_tag( $label_name, $label_value, __( '広告ラベルを表示', THEME_NAME ));
    echo '</p>';
    ?>
    </div>
  </div>
</span>
<?php
}
endif;



//画像をアップロードボックス生成
if ( !function_exists( 'genelate_upload_image_tag' ) ):
function genelate_upload_image_tag($name, $value){?>
  <input name="<?php echo $name; ?>" type="text" value="<?php echo $value; ?>" />
  <input type="button" name="<?php echo $name; ?>_select" value="<?php _e( '選択', THEME_NAME ) ?>" />
  <input type="button" name="<?php echo $name; ?>_clear" value="<?php _e( 'クリア', THEME_NAME ) ?>" />
  <div id="<?php echo $name; ?>_thumbnail" class="uploded-thumbnail">
    <?php if ($value): ?>
      <img src="<?php echo $value; ?>" alt="選択中の画像">
    <?php endif ?>
  </div>
  <?php if (0/*$value*/): ?>
    <?php genelate_tips_tag(__( '大きな画像は縮小して表示されます。', THEME_NAME )) ?>
  <?php endif ?>

  <script type="text/javascript">
  (function ($) {

      var custom_uploader;

      $("input:button[name=<?php echo $name; ?>_select]").click(function(e) {

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
                  $("input:text[name=<?php echo $name; ?>]").val("");
                  $("#<?php echo $name; ?>_thumbnail").empty();

                  /* テキストフォームに画像の URL を表示 */
                  $("input:text[name=<?php echo $name; ?>]").val(file.attributes.sizes.full.url);

                  /* プレビュー用に選択されたサムネイル画像を表示 */
                  $("#<?php echo $name; ?>_thumbnail").append('<img src="'+file.attributes.sizes.full.url+'" />');

              });
          });

          custom_uploader.open();

      });

      /* クリアボタンを押した時の処理 */
      $("input:button[name=<?php echo $name; ?>_clear]").click(function() {

          $("input:text[name=<?php echo $name; ?>]").val("");
          $("#<?php echo $name; ?>_thumbnail").empty();

      });

  })(jQuery);
  </script>
  <?php
}
endif;


//カテゴリチェックリストの作成
//require_once( ABSPATH . '/wp-admin/includes/template.php' );
//add_shortcode('frontend-category-checklist', 'frontend_category_checklist');
if ( !function_exists( 'genelate_category_checklist' ) ):
function genelate_category_checklist( $post_id = 0, $descendants_and_self = 0, $selected_cats = false,
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

if ( !function_exists( 'genelate_hierarchical_category_check_list' ) ):
function genelate_hierarchical_category_check_list( $cat, $name, $checks, $width = auto ) {
  echo '<div class="category-check-list '.$name.'-list" style="width: '.$width.';">';
  hierarchical_category_check_list( $cat, $name, $checks );
  echo '</div>';
}
endif;

if ( !function_exists( 'hierarchical_category_check_list' ) ):
function hierarchical_category_check_list( $cat, $name, $checks ) {
    // wpse-41548 // alchymyth // a hierarchical list of all categories //

  $next = get_categories('hide_empty=false&orderby=name&order=ASC&parent=' . $cat);

  if( $next ) :
    foreach( $next as $cat ) :
      $checked = '';
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
