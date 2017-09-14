<div class="metabox-holder">

<!-- フォローボタン -->
<div id="sns-share" class="postbox">
  <h2 class="hndle"><?php _e( 'フォローボタン', THEME_NAME ) ?></h2>
  <div class="inside">
    <p><?php _e( 'フォローボタンの表示に関する設定です。', THEME_NAME ) ?></p>
    <table class="form-table">
      <tbody>

        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo">
            <?php //テンプレートの読み込み
              if (is_sns_follow_buttons_visible())
                get_template_part('/tmp/sns-share-buttons'); ?>
            </div>
          </td>
        </tr>

        <!-- フォローボタンの表示 -->
        <tr>
          <th scope="row">
            <label for="<?php echo OP_SNS_FOLLOW_BUTTONS_VISIBLE; ?>"><?php _e( 'フォローボタンの表示', THEME_NAME ) ?></label>
          </th>
          <td>
             <input type="checkbox" name="<?php echo OP_SNS_FOLLOW_BUTTONS_VISIBLE; ?>" value="1"<?php the_checkbox_checked(is_sns_follow_buttons_visible()); ?>><?php _e("本文下のフォローボタンを表示する",THEME_NAME ); ?>
            <p class="tips"><?php _e( '投稿・固定ページの本文下にあるフォローボタンの表示を切り替えます。', THEME_NAME ) ?></p>
          </td>
        </tr>

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
            <p><?php echo THEME_NAME_CAMEL; ?><?php _e( 'は、ログインユーザーごとにフォローページを設定できます。', THEME_NAME ) ?></p>
            <p><?php _e( '以下のアカウントURLを設定する場合は、プロフィールページから設定してください。', THEME_NAME ) ?>(<a href="profile.php"><?php _e( 'あなたのプロフィール', THEME_NAME ) ?></a>)</p>
            <ul class="list-style-disc">
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
            <p class="tips"><?php _e( '現ログインユーザーのSNSフォローページを設定します。', THEME_NAME ) ?></p>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>




</div><!-- /.metabox-holder -->