<?php //プロフィールプロフィールに関連する関数

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

//ユーザー情報追加
add_action('show_user_profile', 'add_avatar_to_user_profile');
add_action('edit_user_profile', 'add_avatar_to_user_profile');
if ( !function_exists( 'add_avatar_to_user_profile' ) ):
function add_avatar_to_user_profile($user) {
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
  </table>
<?php
}
endif;

//入力した値を保存する
add_action('personal_options_update', 'update_avatar_to_user_profile');
if ( !function_exists( 'update_avatar_to_user_profile' ) ):
function update_avatar_to_user_profile($user_id) {
  if ( current_user_can('edit_user',$user_id) ){
    update_user_meta($user_id, 'upladed_avatar', $_POST['upladed_avatar']);
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
