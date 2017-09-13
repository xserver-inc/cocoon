<div class="metabox-holder">

<!-- フォローボタン -->
<div id="analytics" class="postbox">
  <h2 class="hndle"><?php _e( 'フォローボタン', THEME_NAME ) ?></h2>
  <div class="inside">

    <table class="form-table">
      <tbody>

        <!-- フォローメッセージ -->
        <tr>
          <th scope="row">
            <label for="<?php echo OP_SNS_FOLLOW_MESSAGE; ?>"><?php _e( 'フォローメッセージ', THEME_NAME ) ?></label>
          </th>
          <td>
            <input type="text" name="<?php echo OP_SNS_FOLLOW_MESSAGE; ?>" size="<?php echo DEFAULT_INPUT_COLS; ?>" value="<?php echo get_sns_follow_message(); ?>" placeholder="<?php _e( 'フォローメッセージの入力', THEME_NAME ); ?>">
            <p class="tips"><?php _e( '訪問者にフォローを促すメッセージを入力してください。', THEME_NAME ) ?></p>
          </td>
        </tr>

        <!-- SNSサービスのURL -->
        <tr>
          <th scope="row">
            <label><?php _e( 'SNSサービスのURL', THEME_NAME ) ?></label>
          </th>
          <td>
            <p><?php _e( '以下のアカウントURLを設定する場合は、プロフィールページから設定してください。', THEME_NAME ) ?></p>
            <ul class="sns-follow-urls">
              <li><?php _e( 'ウェブサイト', THEME_NAME ) ?></li>
              <li><?php _e( 'Twitter', THEME_NAME ) ?></li>
              <li><?php _e( 'Facebook', THEME_NAME ) ?></li>
              <li><?php _e( 'Google+', THEME_NAME ) ?></li>
              <li><?php _e( 'はてなブックマーク', THEME_NAME ) ?></li>
              <li><?php _e( 'Instagram', THEME_NAME ) ?></li>
              <li><?php _e( 'Pinterest', THEME_NAME ) ?></li>
              <li><?php _e( 'YouTube', THEME_NAME ) ?></li>
              <li><?php _e( 'Flickr', THEME_NAME ) ?></li>
              <li><?php _e( 'GitHub', THEME_NAME ) ?></li>
            </ul>
            <p><a href="profile.php"><?php _e( 'あなたのプロフィール', THEME_NAME ) ?></a>から設定</p>
            <p class="tips"><?php _e( '現在ログイン中のユーザーのSNSフォローページを設定します。', THEME_NAME ) ?></p>
          </td>
        </tr>

        <!-- デフォルトユーザー -->
        <tr>
          <th scope="row">
            <label for="<?php echo OP_SNS_DEFAULT_FOLLOW_USER; ?>"><?php _e( 'デフォルトユーザー', THEME_NAME ) ?></label>
          </th>
          <td>
            <?php echo get_easy_selectbox_tag(OP_SNS_DEFAULT_FOLLOW_USER, get_sns_default_follow_user()); ?>
            <p class="tips"><?php _e( '投稿・固定ページ以外でフォローボタンを表示するユーザーを選択してください。', THEME_NAME ) ?></p>
          </td>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo">
            <?php get_template_part('/tmp/sns-follow-pages'); ?>
            </div>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>




</div><!-- /.metabox-holder -->