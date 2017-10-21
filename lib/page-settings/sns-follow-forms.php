<div class="metabox-holder">

<!-- フォローボタン -->
<div id="sns-follow" class="postbox">
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
                get_template_part('/tmp/sns-follow-buttons'); ?>
            </div>
          </td>
        </tr>

        <!-- フォローボタンの表示 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_SNS_FOLLOW_BUTTONS_VISIBLE, __( 'フォローボタンの表示', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            genelate_checkbox_tag( OP_SNS_FOLLOW_BUTTONS_VISIBLE, is_sns_follow_buttons_visible(), __( '本文下のフォローボタンを表示する', THEME_NAME ));
            genelate_tips_tag(__( '投稿・固定ページの本文下にあるフォローボタンの表示を切り替えます。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- フォローメッセージ -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_SNS_FOLLOW_MESSAGE, __( 'フォローメッセージ', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            //var_dump(get_sns_follow_message());
            genelate_textbox_tag(OP_SNS_FOLLOW_MESSAGE, get_sns_follow_message(), __( 'フォローメッセージの入力', THEME_NAME ));
            genelate_tips_tag(__( '訪問者にフォローを促すメッセージを入力してください。', THEME_NAME ).REP_AUTHOR.__( 'には、投稿者のそれぞれの表示名が入ります。', THEME_NAME ));
            ?>
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

        <!-- feedlyの表示 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_FEEDLY_FOLLOW_BUTTON_VISIBLE, __( 'feedlyの表示', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            genelate_checkbox_tag( OP_FEEDLY_FOLLOW_BUTTON_VISIBLE, is_feedly_follow_button_visible(), __( 'feedlyフォローボタンを表示する', THEME_NAME ));
            genelate_tips_tag(__( 'feedlyフォローボタンを表示します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- RSSの表示 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_RSS_FOLLOW_BUTTON_VISIBLE, __( 'RSSの表示', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            genelate_checkbox_tag( OP_RSS_FOLLOW_BUTTON_VISIBLE, is_rss_follow_button_visible(), __( 'RSS購読ボタンを表示する', THEME_NAME ));
            genelate_tips_tag(__( 'RSS購読料のボタンを表示します。', THEME_NAME ));
            ?>
          </td>
        </tr>


        <!-- ボタンカラー -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_SNS_FOLLOW_BUTTON_COLOR, __( 'ボタンカラー', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            $options = array(
              'monochrome' => 'モノクロ',
              'brand_color' => 'ブランドカラー',
              'brand_color_white' => 'ブランドカラー（白抜き）',
            );
            genelate_selectbox_tag(OP_SNS_FOLLOW_BUTTON_COLOR, $options, get_sns_follow_button_color());
            genelate_tips_tag(__( 'シェアボタンの配色を選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- デフォルトユーザー -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_SNS_DEFAULT_FOLLOW_USER, __( 'デフォルトユーザー', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            genelate_author_list_selectbox_tag(OP_SNS_DEFAULT_FOLLOW_USER, get_sns_default_follow_user());
            genelate_tips_tag(__( '投稿・固定ページ以外でフォローボタンを表示するユーザーを選択してください。', THEME_NAME ));
             ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>




</div><!-- /.metabox-holder -->