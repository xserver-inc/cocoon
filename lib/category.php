<?php //カテゴリ関係

///////////////////////////////////////
// 拡張カテゴリ設定
///////////////////////////////////////

//define('CATEGORY_META_PREFIX', 'category_meta_');

if ( !function_exists( 'get_category_meta_key' ) ):
function get_category_meta_key($cat_id){
  return 'category_meta_'.$cat_id;
}
endif;

//拡張カテゴリ編集フォーム
add_action ( 'edit_category_form_fields', 'extra_category_fields');
if ( !function_exists( 'extra_category_fields' ) ):
function extra_category_fields( $tag ) {
    $cat_id = $tag->term_id;
    $cat_meta = get_term_meta( $cat_id, get_category_meta_key($cat_id), true );
    _v($cat_meta);
?>
<tr class="form-field">
  <th><label for="category_color"><?php _e( 'カテゴリ色', THEME_NAME ) ?></label></th>
  <td><?php
    $category_color = !empty($cat_meta['category_color']) ? $cat_meta['category_color'] : '';
    generate_color_picker_tag('cat_meta[category_color]',  $category_color, '');
  ?>
    <p class="description"><?php _e( 'カテゴリの色を指定します。', THEME_NAME ) ?></p>
  </td>
</tr>
<tr class="form-field">
    <th><label for="category_title"><?php _e( 'カテゴリタイトル', THEME_NAME ) ?></label></th>
    <td><input type="text" name="cat_meta[category_title]" id="category_title" size="25" value="<?php if(isset ( $cat_meta['category_title'])) echo esc_html($cat_meta['category_title']) ?>" /></td>
</tr>
<tr class="form-field">
    <th><label for="extra_text">その他テキスト</label></th>
    <td><input type="text" name="cat_meta[extra_text]" id="extra_text" size="25" value="<?php if(isset ( $cat_meta['extra_text'])) echo esc_html($cat_meta['extra_text']) ?>" /></td>
</tr>
<tr class="form-field">
  <th><label for="upload_image">画像URL</label></th>
  <td>
    <input id="upload_image" type="text" size="36" name="cat_meta[img]" value="<?php if(isset ( $cat_meta['img'])) echo esc_html($cat_meta['img']) ?>" /><br />
    画像を追加: <img src="images/media-button-other.gif" alt="画像を追加"  id="upload_image_button" value="Upload Image" style="cursor:pointer;" />
  </td>
</tr>
<?php
}
endif;

//拡張カテゴリ情報の保存
add_action ( 'edited_term', 'save_extra_category_fileds');
if ( !function_exists( 'save_extra_category_fileds' ) ):
function save_extra_category_fileds( $term_id ) {
  if ( isset( $_POST['cat_meta'] ) ) {
    $cat_id = $term_id;
    $cat_meta = get_term_meta( $cat_id, get_category_meta_key($cat_id), true);
    $cat_keys = array_keys($_POST['cat_meta']);
    //_v($cat_keys);
    foreach ($cat_keys as $key){
      if (isset($_POST['cat_meta'][$key])){
         $cat_meta[$key] = $_POST['cat_meta'][$key];
      }
    }
    update_term_meta( $cat_id, get_category_meta_key($cat_id), $cat_meta );
  }
}
endif;


//カテゴリ説明文をビジュアルエディターにするカスタマイズ
/*
// remove the html filtering
remove_filter( 'pre_term_description', 'wp_filter_kses' );
remove_filter( 'term_description', 'wp_kses_data' );

add_filter('edit_category_form_fields', 'cat_description');
if ( !function_exists( 'cat_description' ) ):
function cat_description($tag){
  ?>
    <table class="form-table">
      <tr class="form-field">
        <th scope="row" valign="top"><label for="description"><?php _e('Description'); ?></label></th>
        <td>
        <?php
          $settings = array('wpautop' => true, 'media_buttons' => true, 'quicktags' => true, 'textarea_rows' => '15', 'textarea_name' => 'description' );
          wp_editor(wp_kses_post($tag->description , ENT_QUOTES, 'UTF-8'), 'cat_description', $settings);
        ?>
        <br />
        <span class="description"><?php _e('The description is not prominent by default; however, some themes may show it.'); ?></span>
        </td>
      </tr>
    </table>
  <?php
}
endif;


*/

//デフォルトのカテゴリ説明文を削除
//add_action('admin_head', 'remove_default_category_description');
if ( !function_exists( 'remove_default_category_description' ) ):
function remove_default_category_description(){
  global $current_screen;
  if ( $current_screen->id == 'edit-category' )
  {
  ?>
    <script type="text/javascript">
    jQuery(function($) {
        $('textarea#description').closest('tr.form-field').remove();
    });
    </script>
  <?php
  }
}
endif;
