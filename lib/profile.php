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
if ( !function_exists( 'get_the_author_upladed_avatar_url' ) ):
function get_the_author_upladed_avatar_url($user_id){
  if (!$user_id) {
    $user_id = get_the_posts_author_id();
  }
  return esc_html(get_the_author_meta('upladed_avatar', $user_id));
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
        generate_upload_image_tag('upladed_avatar', get_the_author_upladed_avatar_url($user->ID));
       ?>
       <p class="description"><?php _e( '自前でプロフィール画像をアップロードする場合は画像を選択してください。Gravatarよりこちらのプロフィール画像が優先されます。240×240pxの正方形の画像がお勧めです。', THEME_NAME ) ?><?php _e( 'ページサイズ縮小のため<a href="https://tinypng.com/" target="_blank">TinyPNG</a>等で登録前にで圧縮することをおすすめします。', THEME_NAME ) ?></p>
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
add_action('profile_update', 'update_avatar_to_user_profile');
add_action('personal_options_update', 'update_avatar_to_user_profile');
if ( !function_exists( 'update_avatar_to_user_profile' ) ):
function update_avatar_to_user_profile($user_id) {
  if ( current_user_can('edit_user',$user_id) || is_user_administrator() ){
    update_user_meta($user_id, 'upladed_avatar', $_POST['upladed_avatar']);
    update_user_meta($user_id, 'profile_page_url', $_POST['profile_page_url']);

    //LINE@ URLの%40が消えるので@に変換する処理
    $_POST['line_at_url'] = str_replace('%40', '@', $_POST['line_at_url']);
    //_v($_POST['line_at_url']);
  }
}
endif;

//プロフィール画像を変更する
add_filter( 'get_avatar' , 'get_uploaded_user_profile_avatar' , 1 , 5 );
if ( !function_exists( 'get_uploaded_user_profile_avatar' ) ):
function get_uploaded_user_profile_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
  if ( is_numeric( $id_or_email ) )
    $user_id = (int) $id_or_email;
  elseif ( is_string( $id_or_email ) && ( $user = get_user_by( 'email', $id_or_email ) ) )
    $user_id = $user->ID;
  elseif ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) )
    $user_id = (int) $id_or_email->user_id;

  if ( empty( $user_id ) )
    return $avatar;

  if (get_the_author_upladed_avatar_url($user_id)) {
    $alt = !empty($alt) ? $alt : get_the_author_meta( 'display_name', $user_id );;
    $author_class = is_author( $user_id ) ? ' current-author' : '' ;
    $avatar = "<img alt='" . esc_attr( $alt ) . "' src='" . esc_url( get_the_author_upladed_avatar_url($user_id) ) . "' class='avatar avatar-{$size}{$author_class} photo' height='{$size}' width='{$size}' />";
  }

  return $avatar;
}
endif;
