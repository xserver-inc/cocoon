<?php //プロフィールプロフィールに関連する関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// 自前でプロフィール画像のアップロード
///////////////////////////////////////
//プロフィール画面で設定したプロフィール画像
if ( !function_exists( 'get_the_author_uploaded_avatar_url' ) ):
function get_the_author_uploaded_avatar_url($user_id){
  if (!$user_id) {
    $user_id = get_the_posts_author_id();
  }
  //ユーザーメタキーは既存データ保持のため誤字のまま
  return esc_html(get_the_author_meta('upladed_avatar', $user_id));
}
endif;
//エイリアス（関数名にスペルミスがあったので。子テーマカスタマイズ時のエラー回避用）
if ( !function_exists( 'get_the_author_upladed_avatar_url' ) ):
function get_the_author_upladed_avatar_url($user_id){
  return get_the_author_uploaded_avatar_url($user_id);
}
endif;
//プロフィール画面で設定したプロフィールページURL
if ( !function_exists( 'get_the_author_profile_page_url' ) ):
function get_the_author_profile_page_url($user_id){
  if (!$user_id) {
    $user_id = get_the_posts_author_id();
  }
  return trim(esc_html(get_the_author_meta('profile_page_url', $user_id)));
}
endif;

//ユーザー情報追加
add_action('show_user_profile', 'add_avatar_to_user_profile');
add_action('edit_user_profile', 'add_avatar_to_user_profile');
if ( !function_exists( 'add_avatar_to_user_profile' ) ):
function add_avatar_to_user_profile($user) {
  if (is_admin()) {
?>
  <h3><?php _e( 'プロフィール画像', THEME_NAME ) ?></h3>
  <table class="form-table">
    <tr>
      <th>
        <label for="avatar"><?php _e( 'プロフィール画像のアップロード', THEME_NAME ) ?></label>
      </th>
      <td>
      <?php
        generate_upload_image_tag('upladed_avatar', get_the_author_uploaded_avatar_url($user->ID));
       ?>
       <p class="description"><?php _e( '自前でプロフィール画像をアップロードする場合は画像を選択してください。Gravatarよりこちらのプロフィール画像が優先されます。240×240pxの正方形の画像がお勧めです。', THEME_NAME ) ?><?php _e( 'ページサイズ縮小のため<a href="https://tinypng.com/" target="_blank" rel="noopener">TinyPNG</a>等で登録前にで圧縮することをおすすめします。', THEME_NAME ) ?></p>
      </td>
    </tr>

    <tr>
      <th>
        <?php generate_label_tag('profile_page_url', __('プロフィールページURL', THEME_NAME) ); ?>
      </th>
      <td>
      <?php
        generate_textbox_tag('profile_page_url', get_the_author_profile_page_url($user->ID), 'https://');
       ?>
       <p class="description"><?php _e( 'プロフィール情報が入力してあるページURLを入力してください。プロフィール名のリンクがプロフィールページに変更されます。未入力の場合は、著者のアーカイブページにリンクされます。', THEME_NAME ) ?></p>
      </td>
    </tr>
  </table>
<?php
  }
}
endif;

//入力した値を保存する
//WordPress 6.1にアップデート新規投稿で投稿をポストするとなぜか呼び出されるようになった
//投稿ポストで読み込まれた$_POSTは空なのでupladed_avatarとprofile_page_urlが空手上書きされる
//↓https://wp-cocoon.com/wp-content/uploads/2022/11/profile_update.png
// add_action('profile_update', 'update_avatar_to_user_profile');
add_action('personal_options_update', 'update_avatar_to_user_profile');
add_action('edit_user_profile_update', 'update_avatar_to_user_profile');
if ( !function_exists( 'update_avatar_to_user_profile' ) ):
function update_avatar_to_user_profile($user_id) {
  $user_id = (int)$user_id;
  if ( !$user_id || !current_user_can('edit_user', $user_id) ) {
    return;
  }

  // WordPress標準プロフィールフォームで発行されるnonceの検証
  if ( !isset($_POST['_wpnonce']) || !is_scalar($_POST['_wpnonce']) ) {
    return;
  }
  $nonce = sanitize_text_field(wp_unslash((string)$_POST['_wpnonce']));
  if ( !wp_verify_nonce($nonce, 'update-user_'.$user_id) ) {
    return;
  }

  // POSTに存在するCocoon独自メタだけを対象にした部分更新
  $url_meta_keys = array('upladed_avatar', 'profile_page_url');
  foreach ($url_meta_keys as $meta_key) {
    if ( !array_key_exists($meta_key, $_POST) || !is_scalar($_POST[$meta_key]) ) {
      continue;
    }

    $value = esc_url_raw(wp_unslash((string)$_POST[$meta_key]));
    update_user_meta($user_id, $meta_key, $value);
  }

  if ( array_key_exists('line_at_url', $_POST) ) {
    // 配列入力をWordPress標準保存へ渡さないための未送信扱い
    if ( !is_scalar($_POST['line_at_url']) ) {
      unset($_POST['line_at_url']);
      return;
    }

    // LINE@ URLの%40を維持するための@への変換
    $line_at_url = wp_unslash((string)$_POST['line_at_url']);
    $_POST['line_at_url'] = esc_url_raw(str_replace('%40', '@', $line_at_url));
  }
}
endif;

//プロフィール画像を変更する
add_filter( 'get_avatar' , 'get_uploaded_user_profile_avatar' , 100000 , 6 );//Ultimate Memberプラグインと干渉するため100000にした
if ( !function_exists( 'get_uploaded_user_profile_avatar' ) ):
function get_uploaded_user_profile_avatar( $avatar, $id_or_email, $size, $default, $alt, $args ) {
  if ( is_numeric( $id_or_email ) )
    $user_id = (int) $id_or_email;
  elseif ( is_string( $id_or_email ) && ( $user = get_user_by( 'email', $id_or_email ) ) )
    $user_id = $user->ID;
  elseif ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) )
    $user_id = (int) $id_or_email->user_id;

  if ( empty( $user_id ) || $args['force_default'] == true)
    return $avatar;

  if (get_the_author_uploaded_avatar_url($user_id)) {
    $alt = !empty($alt) ? $alt : get_the_author_meta( 'display_name', $user_id );;
    $author_class = is_author( $user_id ) ? ' current-author' : '' ;
    $avatar = "<img alt='" . esc_attr( $alt ) . "' src='" . esc_url( get_the_author_uploaded_avatar_url($user_id) ) . "' class='avatar avatar-{$size}{$author_class} photo' height='{$size}' width='{$size}' />";
  }

  return $avatar;
}
endif;
