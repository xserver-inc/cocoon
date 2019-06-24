<?php //カテゴリ関係
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// 拡張カテゴリ設定
///////////////////////////////////////
if ( !function_exists( 'get_tag_meta_key' ) ):
function get_tag_meta_key($tag_id){
  return 'tag_meta_'.$tag_id;
}
endif;

//タグメタ情報の取得
if ( !function_exists( 'get_tag_meta' ) ):
function get_tag_meta($tag_id = null){
  if (empty($tag_id) && is_tag()) {
    //タグないときIDを取得
    $tag_id = get_query_var('tag_id');
  }
  //IDが正常な場合
  if ($tag_id) {
    $res = get_term_meta( $tag_id, get_tag_meta_key($tag_id), true );
    if (is_array($res)) {
      return $res;
    } else {
      return array();
    }
  }
}
endif;

//タイトルの取得
if ( !function_exists( 'get_tag_title' ) ):
function get_tag_title($tag_id = null){
  $meta = get_tag_meta($tag_id);
  if (!empty($meta['title']))
    return $meta['title'];
  else
    return get_tag($tag_id)->name;
}
endif;

//本文の取得
if ( !function_exists( 'get_tag_content' ) ):
function get_tag_content($tag_id = null){
  if (!$tag_id) {
    $tag_id = get_query_var('tag_id');
  }
  $meta = get_tag_meta($tag_id);
  if (!empty($meta['content']))
    $content = $meta['content'];
  else
    $content = tag_description($tag_id);

  $content = wpautop($content);
  $content = apply_filters( 'the_tag_content', $content );
  return $content;
}
endif;

//画像の取得
if ( !function_exists( 'get_tag_eye_catch_url' ) ):
function get_tag_eye_catch_url($tag_id = null){
  $meta = get_tag_meta($tag_id);
  if (!empty($meta['eye_catch'])){
    $eye_catch_url = $meta['eye_catch'];
    //画像が存在しているか
    if (file_exists(url_to_local($eye_catch_url))) {
      return $eye_catch_url;
    } else {
      return '';
    }
  } else {
    return '';
  }
}
endif;

//説明文の取得
if ( !function_exists( 'get_tag_description' ) ):
function get_tag_description($tag_id = null){
  $meta = get_tag_meta($tag_id);
  if (!empty($meta['description']))
    return $meta['description'];
}
endif;

//スニペット文を取得
if ( !function_exists( 'get_tag_snipet' ) ):
function get_tag_snipet($tag_id){
  $snipet = get_tag_description($tag_id);
  if (!$snipet) {
    //説明を取得
    $snipet = tag_description($tag_id);
  }
  if (!$snipet) {
    //内容の抜粋
    $snipet = get_content_excerpt(get_tag_content($tag_id), get_entry_card_excerpt_max_length());
  }
  if (!$snipet) {
    //説明を取得
    $tag = get_tag($tag_id);
    if ($tag) {
      $snipet = sprintf( __( '「%s」の記事一覧です。', THEME_NAME ), $tag->name );
    }

  }
  return $snipet;
}
endif;

//キーワードの取得
if ( !function_exists( 'get_tag_keywords' ) ):
function get_tag_keywords($tag_id = null){
  $meta = get_tag_meta($tag_id);
  if (!empty($meta['keywords']))
    return $meta['keywords'];
}
endif;

//編集フォーム
add_action ( 'edit_tag_form_fields', 'extra_tag_fields');
if ( !function_exists( 'extra_tag_fields' ) ):
function extra_tag_fields( $tag ) {
    $tag_id = $tag->term_id;
    $tag_meta = get_tag_meta($tag_id);
?>
<tr class="form-field term-title-wrap">
  <th><label for="title"><?php _e( 'タグタイトル', THEME_NAME ) ?></label></th>
  <td>
    <input type="text" name="tag_meta[title]" id="title" size="25" value="<?php if(isset ( $tag_meta['title'])) echo esc_html($tag_meta['title']) ?>" placeholder="<?php _e( 'タグページのタイトル', THEME_NAME ) ?>" />
    <p class="description"><?php _e( 'タグページのタイトルを指定します。タグページのタイトルタグにここで入力したテキストが適用されます。', THEME_NAME ) ?></p>
  </td>
</tr>
<tr class="form-field term-content-wrap">
  <th><label for="content"><?php _e( 'タグ本文', THEME_NAME ) ?></label></th>
  <td><?php
    $content = isset($tag_meta['content']) ? $tag_meta['content'] : '';
    generate_visuel_editor_tag('tag_meta[content]', $content, 'content');
    ?>
    <p class="description"><?php _e( 'タグページで表示されるメインコンテンツを入力してください。', THEME_NAME ) ?></p>
    </td>
</tr>
<tr class="form-field term-eye-catch-wrap">
  <th><label for="eye_catch"><?php _e( 'アイキャッチ', THEME_NAME ) ?></label></th>
  <td><?php
    $eye_catch = isset($tag_meta['eye_catch']) ? $tag_meta['eye_catch'] : '';
    generate_upload_image_tag('tag_meta[eye_catch]', $eye_catch, 'eye_catch');
    ?>
    <p class="description"><?php _e( 'タイトル下に表示されるアイキャッチ画像を選択してください。', THEME_NAME ) ?></p>
    </td>
</tr>
<tr class="form-field term-meta-description-wrap">
  <th><label for="description"><?php _e( 'メタディスクリプション', THEME_NAME ) ?></label></th>
  <td>
    <?php
    $description = isset($tag_meta['description']) ? $tag_meta['description'] : '';
    generate_textarea_tag('tag_meta[description]', $description, __( 'タグページの説明文を入力してください', THEME_NAME ), 3) ;
      ?>
    <p class="description"><?php _e( 'タグページの説明を入力します。ここに入力したテキストはメタディスクリプションタグとして利用されます。', THEME_NAME ) ?></p>
  </td>
</tr>
<tr class="form-field term-meta-keywords-wrap">
  <th><label for="keywords"><?php _e( 'メタキーワード', THEME_NAME ) ?></label></th>
  <td>
    <input type="text" name="tag_meta[keywords]" id="keywords" size="25" value="<?php if(isset ( $tag_meta['keywords'])) echo esc_html($tag_meta['keywords']) ?>" placeholder="<?php _e( 'キーワード1,キーワード2,キーワード3', THEME_NAME ) ?>" />
    <p class="description"><?php _e( 'タグページのメタキーワードをカンマ区切りで入力してください。※現在はあまり意味のない設定となっています。', THEME_NAME ) ?></p>
  </td>
</tr>
<?php
}
endif;

//情報の保存
add_action ( 'edited_term', 'save_extra_tag_fileds');
if ( !function_exists( 'save_extra_tag_fileds' ) ):
function save_extra_tag_fileds( $term_id ) {
  if ( isset( $_POST['tag_meta'] ) ) {
    $tag_id = $term_id;
    $tag_meta = $_POST['tag_meta'];
    update_term_meta( $tag_id, get_tag_meta_key($tag_id), $tag_meta );
  }
}
endif;
