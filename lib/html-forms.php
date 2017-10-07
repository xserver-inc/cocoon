<?php //HTMLフォーム生成関数

//著者セレクトボックスを手軽に作成する
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
function genelate_selectbox_tag($name, $options, $now_value){?>
<select name="<?php echo $name; ?>">
  <?php foreach ($options as $value => $caption) { ?>
  <option value="<?php echo $value; ?>"<?php the_option_selected($value, $now_value) ?>><?php echo $caption; ?></option>
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
    $site_logo_tag = '<a href="'.get_home_url().'" class="site-name site-name-text">'.get_bloginfo('name').'</a>';
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



//画像をアップロードボックス生成
if ( !function_exists( 'genelate_upload_image_tag' ) ):
function genelate_upload_image_tag($name, $value){?>
  <input name="<?php echo $name; ?>" type="text" value="<?php echo $value; ?>" />
  <input type="button" name="<?php echo $name; ?>_slect" value="<?php _e( '選択', THEME_NAME ) ?>" />
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

      $("input:button[name=<?php echo $name; ?>_slect]").click(function(e) {

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

