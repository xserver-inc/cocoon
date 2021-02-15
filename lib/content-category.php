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

if ( !function_exists( 'get_the_category_meta_key' ) ):
function get_the_category_meta_key($cat_id){
  return 'category_meta_'.$cat_id;
}
endif;

//カテゴリメタ情報の取得
if ( !function_exists( 'get_the_category_meta' ) ):
function get_the_category_meta($cat_id = null){
  if (empty($cat_id) && is_category()) {
    //カテゴリがないときはカテゴリIDを取得
    $cat_id = get_query_var('cat');
  }
  //カテゴリIDが正常な場合
  if ($cat_id) {
    $key = get_the_category_meta_key($cat_id);
    if (term_metadata_exists($cat_id, $key)) {
      $res = get_term_meta( $cat_id, $key, true );
      if (is_array($res)) {
        return $res;
      }
    }
  }
  return array();
}
endif;

//カテゴリ色の取得
if ( !function_exists( 'get_the_category_color' ) ):
function get_the_category_color($cat_id = null){
  if (!$cat_id) {
    $cat_id = get_query_var('cat');
  }
  if (term_metadata_exists($cat_id, 'the_category_color')) {
    return get_term_meta( $cat_id, 'the_category_color', true );
  } else {//旧バージョン対応
    $meta = get_the_category_meta($cat_id);
    if (!empty($meta['color']))
      return $meta['color'];
  }
}
endif;

//カテゴリ文字色の取得
if ( !function_exists( 'get_the_category_text_color' ) ):
function get_the_category_text_color($cat_id = null){
  if (!$cat_id) {
    $cat_id = get_query_var('cat');
  }
  if (term_metadata_exists($cat_id, 'the_category_text_color')) {
    return get_term_meta( $cat_id, 'the_category_text_color', true );
  } else {//旧バージョン対応
    $meta = get_the_category_meta($cat_id);
    if (!empty($meta['text_color']))
      return $meta['text_color'];
  }
}
endif;

//カテゴリタイトルの取得
if ( !function_exists( 'get_the_category_title' ) ):
function get_the_category_title($cat_id = null, $is_cat_name = true){
  if (!$cat_id) {
    $cat_id = get_query_var('cat');
  }
  $res = null;
  if (term_metadata_exists($cat_id, 'the_category_title')) {
    $res = get_term_meta( $cat_id, 'the_category_title', true );
  } else {//旧バージョン対応
    $meta = get_the_category_meta($cat_id);
    if (!empty($meta['title'])){
      $res = $meta['title'];
    }
  }
  //タイトルが存在しない場合はカテゴリ名を利用する
  if (!$res && $is_cat_name) {
    $res = get_category($cat_id)->name;
  }
  return $res;
}
endif;

//カテゴリ本文の取得
if ( !function_exists( 'get_the_category_content' ) ):
function get_the_category_content($cat_id = null, $for_editor = false){
  if (!$cat_id) {
    $cat_id = get_query_var('cat');
  }
  if (term_metadata_exists($cat_id, 'the_category_content')) {
    //取得できた場合はそのまま返す（本文編集などでも使われる）
    $content = get_term_meta( $cat_id, 'the_category_content', true );
  } else {//旧バージョン対応
    $meta = get_the_category_meta($cat_id);
    if (!empty($meta['content']))
      $content = $meta['content'];
    else
      $content = category_description($cat_id);
  }
  if (!$for_editor) {
    //$content = wpautop($content);
    $content = apply_filters( 'the_category_tag_content', $content );//カテゴリー・タグ本文共通
    $content = apply_filters( 'the_category_content', $content );
  }

  return $content;
}
endif;

//アイキャッチの取得
if ( !function_exists( 'get_the_category_eye_catch_url' ) ):
function get_the_category_eye_catch_url($cat_id = null){
  if (!$cat_id) {
    $cat_id = get_query_var('cat');
  }
  if (term_metadata_exists($cat_id, 'the_category_eye_catch_url')) {
    $eye_catch_url = get_term_meta( $cat_id, 'the_category_eye_catch_url', true );
  } else {//旧バージョン対応
    $meta = get_the_category_meta($cat_id);
    if (!empty($meta['eye_catch'])){
      $eye_catch_url = $meta['eye_catch'];
    } else {
      $eye_catch_url = '';
    }
  }

  //画像が存在しているか
  if (file_exists(url_to_local($eye_catch_url))) {
    return $eye_catch_url;
  } else {
    return '';
  }
}
endif;

//カテゴリのメタディスクリプション
if ( !function_exists( 'get_the_category_meta_description' ) ):
function get_the_category_meta_description($cat_id = null){
  if (!$cat_id) {
    $cat_id = get_query_var('cat');
  }
  if (term_metadata_exists($cat_id, 'the_category_meta_description')) {
    return get_term_meta( $cat_id, 'the_category_meta_description', true );
  } else {//旧バージョン対応
    $meta = get_the_category_meta($cat_id);
    if (!empty($meta['description']))
      return $meta['description'];
  }
}
endif;

//カテゴリのスニペット文を取得
if ( !function_exists( 'get_the_category_snippet' ) ):
function get_the_category_snippet($cat_id){
  $snippet = get_the_category_meta_description($cat_id);
  if (!$snippet) {
    //カテゴリ説明を取得
    $snippet = category_description($cat_id);
  }
  if (!$snippet) {
    //カテゴリ内容の抜粋
    $snippet = get_content_excerpt(get_the_category_content($cat_id), get_entry_card_excerpt_max_length());
  }
  if (!$snippet) {
    //カテゴリ説明を取得
    $cat = get_category($cat_id);
    if ($cat) {
      $snippet = sprintf( __( '「%s」の記事一覧です。', THEME_NAME ), $cat->name );
    }
  }
  return $snippet;
}
endif;

//キーワードの取得
if ( !function_exists( 'get_the_category_meta_keywords' ) ):
function get_the_category_meta_keywords($cat_id = null){
  if (!$cat_id) {
    $cat_id = get_query_var('cat');
  }
  if (term_metadata_exists($cat_id, 'the_category_meta_keywords')) {
    return get_term_meta( $cat_id, 'the_category_meta_keywords', true );
  } else {//旧バージョン対応
    $meta = get_the_category_meta($cat_id);
    if (!empty($meta['keywords']))
      return $meta['keywords'];
  }
}
endif;

//noindexの取得
if ( !function_exists( 'get_the_category_noindex' ) ):
function get_the_category_noindex($cat_id = null){
  if (!$cat_id) {
    $cat_id = get_query_var('cat');
  }
  return get_term_meta( $cat_id, 'the_category_noindex', true );
}
endif;

//拡張カテゴリ編集フォーム
add_action ( 'category_edit_form_fields', 'extra_category_fields');
if ( !function_exists( 'extra_category_fields' ) ):
function extra_category_fields( $cat ) {
    $cat_id = $cat->term_id;
    //$cat_meta = get_the_category_meta($cat_id);
    //_v($cat_meta);
?>
<tr class="form-field term-color-wrap">
  <th><label for="color"><?php _e( 'カテゴリ色', THEME_NAME ) ?></label></th>
  <td>
    <div style="float: left;padding-right: 30px;">
      <?php
      $the_category_color = get_the_category_color($cat_id);
      generate_label_tag('the_category_color', __( '背景色', THEME_NAME ));
      echo '<br>';
      generate_color_picker_tag('the_category_color',  $the_category_color, '');
      ?>
      <p class="description"><?php _e( 'カテゴリの色を指定します。', THEME_NAME ) ?></p>
    </div>
    <div style="">
      <?php
      $the_category_text_color = get_the_category_text_color($cat_id);
      generate_label_tag('the_category_text_color', __( '文字色', THEME_NAME ));
      echo '<br>';
      generate_color_picker_tag('the_category_text_color',  $the_category_text_color, '');
      ?>
      <p class="description"><?php _e( 'カテゴリの文字色を指定します。入力しない場合は、白色になります。', THEME_NAME ) ?></p>
    </div>
  </td>
</tr>
<tr class="form-field term-title-wrap">
  <th><label for="title"><?php _e( 'カテゴリタイトル', THEME_NAME ) ?></label></th>
  <td>
    <?php
    $the_category_title = get_the_category_title($cat_id, false);
    ?>
    <input type="text" name="the_category_title" id="title" size="25" value="<?php echo esc_attr($the_category_title) ?>" placeholder="<?php _e( 'カテゴリページのタイトル', THEME_NAME ) ?>" />
    <p class="description"><?php _e( 'カテゴリページのタイトルを指定します。カテゴリページのタイトルタグにここで入力したテキストが適用されます。', THEME_NAME ) ?></p>
  </td>
</tr>
<tr class="form-field term-content-wrap">
  <th><label for="content"><?php _e( 'カテゴリ本文', THEME_NAME ) ?></label></th>
  <td><?php
    $the_category_content = get_the_category_content($cat_id, true);
    generate_visuel_editor_tag('the_category_content', $the_category_content, 'content');
   ?>
    <p class="description"><?php _e( 'カテゴリページで表示されるメインコンテンツを入力してください。', THEME_NAME ) ?></p>
   </td>
</tr>
<tr class="form-field term-eye-catch-wrap">
  <th><label for="eye_catch"><?php _e( 'アイキャッチ', THEME_NAME ) ?></label></th>
  <td><?php
    $the_category_eye_catch_url = get_the_category_eye_catch_url($cat_id);
    generate_upload_image_tag('the_category_eye_catch_url', $the_category_eye_catch_url, 'eye_catch');
   ?>
    <p class="description"><?php _e( 'タイトル下に表示されるアイキャッチ画像を選択してください。', THEME_NAME ) ?></p>
   </td>
</tr>
<tr class="form-field term-meta-description-wrap">
  <th><label for="description"><?php _e( 'メタディスクリプション', THEME_NAME ) ?></label></th>
  <td>
    <?php
    $the_category_meta_description = get_the_category_meta_description($cat_id);
    generate_textarea_tag('the_category_meta_description', $the_category_meta_description, __( 'カテゴリページの説明文を入力してください', THEME_NAME ), 3) ;
     ?>
    <p class="description"><?php _e( 'カテゴリページの説明を入力します。ここに入力したテキストはメタディスクリプションタグとして利用されます。', THEME_NAME ) ?></p>
  </td>
</tr>
<tr class="form-field term-meta-keywords-wrap">
  <th><label for="keywords"><?php _e( 'メタキーワード', THEME_NAME ) ?></label></th>
  <td>
    <?php
    $the_category_meta_keywords = get_the_category_meta_keywords($cat_id);
    ?>
    <input type="text" name="the_category_meta_keywords" id="keywords" size="25" value="<?php echo esc_attr($the_category_meta_keywords) ?>" placeholder="<?php _e( 'キーワード1,キーワード2,キーワード3', THEME_NAME ) ?>" />
    <p class="description"><?php _e( 'カテゴリページのメタキーワードをカンマ区切りで入力してください。※現在はあまり意味のない設定となっています。', THEME_NAME ) ?></p>
  </td>
</tr>
<tr class="form-field term-noindex-wrap">
  <th><label for="noindex"><?php _e( 'noindex', THEME_NAME ) ?></label></th>
  <td>
    <?php
    $the_category_noindex = get_the_category_noindex($cat_id);

    //noindex
    generate_checkbox_tag('the_category_noindex' , $the_category_noindex, __( 'インデックスしない（noindex）', THEME_NAME ));
    generate_howto_tag(__( 'このページが検索エンジンにインデックスされないようにメタタグを設定します。', THEME_NAME ).__( 'Cocoon設定の「SEO」タブにあるカテゴリーのnoindex設定が優先されます。', THEME_NAME ), 'the_category_noindex');
    ?>
  </td>
</tr>
<?php
}
endif;

//拡張カテゴリ情報の保存
add_action ( 'edited_term', 'save_extra_category_fileds');
if ( !function_exists( 'save_extra_category_fileds' ) ):
function save_extra_category_fileds( $term_id ) {
  if (isset($_POST['taxonomy']) && ($_POST['taxonomy'] === 'category')) {
    $cat_id = $term_id;

    if ( isset( $_POST['the_category_color'] ) ) {
      $the_category_color = $_POST['the_category_color'];
      update_term_meta( $cat_id, 'the_category_color', $the_category_color );
    }

    if ( isset( $_POST['the_category_text_color'] ) ) {
      $the_category_text_color = $_POST['the_category_text_color'];
      update_term_meta( $cat_id, 'the_category_text_color', $the_category_text_color );
    }

    if ( isset( $_POST['the_category_title'] ) ) {
      $the_category_title = $_POST['the_category_title'];
      update_term_meta( $cat_id, 'the_category_title', $the_category_title );
    }

    if ( isset( $_POST['the_category_content'] ) ) {
      $the_category_content = $_POST['the_category_content'];
      update_term_meta( $cat_id, 'the_category_content', $the_category_content );
    }

    if ( isset( $_POST['the_category_eye_catch_url'] ) ) {
      $the_category_eye_catch_url = $_POST['the_category_eye_catch_url'];
      update_term_meta( $cat_id, 'the_category_eye_catch_url', $the_category_eye_catch_url );
    }

    if ( isset( $_POST['the_category_meta_description'] ) ) {
      $the_category_meta_description = $_POST['the_category_meta_description'];
      update_term_meta( $cat_id, 'the_category_meta_description', $the_category_meta_description );
    }

    if ( isset( $_POST['the_category_meta_keywords'] ) ) {
      $the_category_meta_keywords = $_POST['the_category_meta_keywords'];
      update_term_meta( $cat_id, 'the_category_meta_keywords', $the_category_meta_keywords );
    }

    $the_category_noindex = !empty($_POST['the_category_noindex']) ? 1 : 0;
    update_term_meta( $cat_id, 'the_category_noindex', $the_category_noindex );

    //旧バージョンの値を削除
    $key = get_the_category_meta_key($cat_id);
    if (term_metadata_exists($cat_id, $key)) {
      delete_term_meta($cat_id, $key);
    }
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
