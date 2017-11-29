<?php //カテゴリ関係

///////////////////////////////////////
// カテゴリ編集フォーム
///////////////////////////////////////
add_action ( 'edit_category_form_fields', 'extra_category_fields');
function extra_category_fields( $tag ) {
    $t_id = $tag->term_id;
    $cat_meta = get_option("cat_$t_id");
?>
<tr class="form-field">
  <th><label for="extra_text"><?php _e( 'カテゴリ色', THEME_NAME ) ?></label></th>
  <td><input type="text" name="Cat_meta[extra_text]" id="extra_text" size="25" value="<?php if(isset ( $cat_meta['extra_text'])) echo esc_html($cat_meta['extra_text']) ?>" /></td>
</tr>
<tr class="form-field">
  <th><label for="upload_image">画像URL</label></th>
  <td>
    <input id="upload_image" type="text" size="36" name="Cat_meta[img]" value="<?php if(isset ( $cat_meta['img'])) echo esc_html($cat_meta['img']) ?>" /><br />
    画像を追加: <img src="images/media-button-other.gif" alt="画像を追加"  id="upload_image_button" value="Upload Image" style="cursor:pointer;" />
  </td>
</tr>
<?php
}