<?php //プロフィールプロフィールに関連する関数

//プロフィール画面で設定したプロフィール画像
if ( !function_exists( 'get_the_author_upladed_avatar' ) ):
function get_the_author_upladed_avatar(){
  return esc_html(get_the_author_meta('upladed_avatar', get_the_posts_author_id()));
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
        generate_upload_image_tag('upladed_avatar', get_the_author_upladed_avatar());
       ?>
       <p class="description"><?php _e( '自前でプロフィール画像をアップロードする場合は画像を選択してください。Gravatarよりこちらのプロフィール画像が優先されます。300×300以上の正方形の画像がお勧めです。', THEME_NAME ) ?></p>
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