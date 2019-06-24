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

//define('CATEGORY_META_PREFIX', 'category_meta_');

if ( !function_exists( 'get_category_meta_key' ) ):
function get_category_meta_key($cat_id){
  return 'category_meta_'.$cat_id;
}
endif;

//カテゴリメタ情報の取得
if ( !function_exists( 'get_category_meta' ) ):
function get_category_meta($cat_id = null){
  if (empty($cat_id) && is_category()) {
    //カテゴリがないときはカテゴリIDを取得
    $cat_id = get_query_var('cat');
  }
  //カテゴリIDが正常な場合
  if ($cat_id) {
    $res = get_term_meta( $cat_id, get_category_meta_key($cat_id), true );
    //_v($res);
    if (is_array($res)) {
      return $res;
    } else {
      return array();
    }
  }
}
endif;

//カテゴリ色の取得
if ( !function_exists( 'get_category_color' ) ):
function get_category_color($cat_id = null){
  $meta = get_category_meta($cat_id);
  if (!empty($meta['color']))
    return $meta['color'];
}
endif;

//カテゴリ文字色の取得
if ( !function_exists( 'get_category_text_color' ) ):
function get_category_text_color($cat_id = null){
  $meta = get_category_meta($cat_id);
  if (!empty($meta['text_color']))
    return $meta['text_color'];
}
endif;

//カテゴリタイトルの取得
if ( !function_exists( 'get_category_title' ) ):
function get_category_title($cat_id = null){
  $meta = get_category_meta($cat_id);
  if (!empty($meta['title']))
    return $meta['title'];
  else
    return get_category($cat_id)->name;
}
endif;

//カテゴリ本文の取得
if ( !function_exists( 'get_category_content' ) ):
function get_category_content($cat_id = null){
  if (!$cat_id) {
    $cat_id = get_query_var('cat');
  }
  $meta = get_category_meta($cat_id);
  if (!empty($meta['content']))
    $content = $meta['content'];
  else
    $content = category_description($cat_id);

  $content = wpautop($content);
  $content = apply_filters( 'the_category_content', $content );
  return $content;
}
endif;

//アイキャッチの取得
if ( !function_exists( 'get_category_eye_catch_url' ) ):
function get_category_eye_catch_url($cat_id = null){
  $meta = get_category_meta($cat_id);
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

//カテゴリ色の取得
if ( !function_exists( 'get_category_description' ) ):
function get_category_description($cat_id = null){
  $meta = get_category_meta($cat_id);
  if (!empty($meta['description']))
    return $meta['description'];
}
endif;

//カテゴリのスニペット文を取得
if ( !function_exists( 'get_category_snipet' ) ):
function get_category_snipet($cat_id){
  $snipet = get_category_description($cat_id);
  if (!$snipet) {
    //カテゴリ説明を取得
    $snipet = category_description($cat_id);
  }
  if (!$snipet) {
    //カテゴリ内容の抜粋
    $snipet = get_content_excerpt(get_category_content($cat_id), get_entry_card_excerpt_max_length());
  }
  if (!$snipet) {
    //カテゴリ説明を取得
    $cat = get_category($cat_id);
    if ($cat) {
      $snipet = sprintf( __( '「%s」の記事一覧です。', THEME_NAME ), $cat->name );
    }

  }
  return $snipet;
}
endif;

//カテゴリ色の取得
if ( !function_exists( 'get_category_keywords' ) ):
function get_category_keywords($cat_id = null){
  $meta = get_category_meta($cat_id);
  if (!empty($meta['keywords']))
    return $meta['keywords'];
}
endif;

//拡張カテゴリ編集フォーム
add_action ( 'edit_category_form_fields', 'extra_category_fields');
if ( !function_exists( 'extra_category_fields' ) ):
function extra_category_fields( $tag ) {
    $cat_id = $tag->term_id;
    $cat_meta = get_category_meta($cat_id);
    //_v($cat_meta);
?>
<tr class="form-field term-color-wrap">
  <th><label for="color"><?php _e( 'カテゴリ色', THEME_NAME ) ?></label></th>
  <td>
    <div style="float: left;padding-right: 30px;">
      <?php
      $color = !empty($cat_meta['color']) ? $cat_meta['color'] : '';
      generate_label_tag('cat_meta[color]', __( '背景色', THEME_NAME ));
      echo '<br>';
      generate_color_picker_tag('cat_meta[color]',  $color, '');
      ?>
      <p class="description"><?php _e( 'カテゴリの色を指定します。', THEME_NAME ) ?></p>
    </div>
    <div style="">
      <?php
      $color = !empty($cat_meta['text_color']) ? $cat_meta['text_color'] : '';
      generate_label_tag('cat_meta[text_color]', __( '文字色', THEME_NAME ));
      echo '<br>';
      generate_color_picker_tag('cat_meta[text_color]',  $color, '');
      ?>
      <p class="description"><?php _e( 'カテゴリの文字色を指定します。入力しない場合は、白色になります。', THEME_NAME ) ?></p>
    </div>
  </td>
</tr>
<tr class="form-field term-title-wrap">
  <th><label for="title"><?php _e( 'カテゴリタイトル', THEME_NAME ) ?></label></th>
  <td>
    <input type="text" name="cat_meta[title]" id="title" size="25" value="<?php if(isset ( $cat_meta['title'])) echo esc_html($cat_meta['title']) ?>" placeholder="<?php _e( 'カテゴリページのタイトル', THEME_NAME ) ?>" />
    <p class="description"><?php _e( 'カテゴリページのタイトルを指定します。カテゴリページのタイトルタグにここで入力したテキストが適用されます。', THEME_NAME ) ?></p>
  </td>
</tr>
<tr class="form-field term-content-wrap">
  <th><label for="content"><?php _e( 'カテゴリ本文', THEME_NAME ) ?></label></th>
  <td><?php
    $content = isset($cat_meta['content']) ? $cat_meta['content'] : '';
    generate_visuel_editor_tag('cat_meta[content]', $content, 'content');
   ?>
    <p class="description"><?php _e( 'カテゴリページで表示されるメインコンテンツを入力してください。', THEME_NAME ) ?></p>
   </td>
</tr>
<tr class="form-field term-eye-catch-wrap">
  <th><label for="eye_catch"><?php _e( 'アイキャッチ', THEME_NAME ) ?></label></th>
  <td><?php
    $eye_catch = isset($cat_meta['eye_catch']) ? $cat_meta['eye_catch'] : '';
    generate_upload_image_tag('cat_meta[eye_catch]', $eye_catch, 'eye_catch');
   ?>
    <p class="description"><?php _e( 'タイトル下に表示されるアイキャッチ画像を選択してください。', THEME_NAME ) ?></p>
   </td>
</tr>
<tr class="form-field term-meta-description-wrap">
  <th><label for="description"><?php _e( 'メタディスクリプション', THEME_NAME ) ?></label></th>
  <td>
    <?php
    $description = isset($cat_meta['description']) ? $cat_meta['description'] : '';
    generate_textarea_tag('cat_meta[description]', $description, __( 'カテゴリページの説明文を入力してください', THEME_NAME ), 3) ;
     ?>
    <p class="description"><?php _e( 'カテゴリページの説明を入力します。ここに入力したテキストはメタディスクリプションタグとして利用されます。', THEME_NAME ) ?></p>
  </td>
</tr>
<tr class="form-field term-meta-keywords-wrap">
  <th><label for="keywords"><?php _e( 'メタキーワード', THEME_NAME ) ?></label></th>
  <td>
    <input type="text" name="cat_meta[keywords]" id="keywords" size="25" value="<?php if(isset ( $cat_meta['keywords'])) echo esc_html($cat_meta['keywords']) ?>" placeholder="<?php _e( 'キーワード1,キーワード2,キーワード3', THEME_NAME ) ?>" />
    <p class="description"><?php _e( 'カテゴリページのメタキーワードをカンマ区切りで入力してください。※現在はあまり意味のない設定となっています。', THEME_NAME ) ?></p>
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
    $cat_meta = $_POST['cat_meta'];

    update_term_meta( $cat_id, get_category_meta_key($cat_id), $cat_meta );
  }
}
endif;

//デフォルトのカテゴリ説明文の位置の移動
add_action('admin_head', 'move_default_category_description');
if ( !function_exists( 'move_default_category_description' ) ):
function move_default_category_description(){
  global $current_screen;

  if ( $current_screen->id == 'edit-category' || $current_screen->id == 'edit-post_tag' )
  {
    $name_description = __( 'カテゴリーとしてウィジェットやラベル等で表示される名前です。', THEME_NAME );
    if ($current_screen->id == 'edit-category') {
      $description = __( '基本的にカテゴリ設定の一覧テーブルに説明文を表示するための入力です。', THEME_NAME );
    } else {
      $description = __( '基本的にタグ設定の一覧テーブルに説明文を表示するための入力です。', THEME_NAME );
    }
  ?>
    <script type="text/javascript">
    jQuery(function($) {
      $('.term-description-wrap').insertAfter('.term-meta-keywords-wrap');
      $('#name + .description').text('<?php echo $name_description; ?>');
      $('textarea#description + .description').text('<?php echo $description; ?>');
      $('textarea#tag-description + p').text('<?php echo $description; ?>');
    });
    </script>
  <?php
  }
}
endif;
