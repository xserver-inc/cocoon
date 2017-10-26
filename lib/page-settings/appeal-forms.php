<div class="metabox-holder">

<!-- アピールエリア -->
<div id="appeal-area" class="postbox">
  <h2 class="hndle"><?php _e( 'アピールエリア設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ヘッダー下でアピールしたい内容を入力します。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo appeal-area-demo" style="">
              <?php get_template_part('tmp/appeal') ?>
            </div>
          </td>
        </tr>

        <!-- アピールエリアの表示 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_APPEAL_AREA_VISIBLE, __('アピールエリアの表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_checkbox_tag(OP_APPEAL_AREA_VISIBLE , is_appeal_area_visible(), __( 'アピールエリアを表示する', THEME_NAME ));
            genelate_tips_tag(__( 'アピールエリア全体の表示を切り替えます。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 高さ -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_APPEAL_AREA_HEIGHT, __('高さ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_number_tag(OP_APPEAL_AREA_HEIGHT,  get_appeal_area_height(), 200, 800);
            genelate_tips_tag(__( 'アピールエリアの高さをpx数で指定します。モバイル環境では高さは無効になります。（最小：200px、最大：800px）', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- エリア画像 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_APPEAL_AREA_IMAGE_URL, __('エリア画像', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_upload_image_tag(OP_APPEAL_AREA_IMAGE_URL, get_appeal_area_image_url());
            genelate_tips_tag(__( 'アピールエリアの背景に表示する画像を設定します。', THEME_NAME ));

            //ヘッダー背景画像の固定
            genelate_checkbox_tag(OP_APPEAL_AREA_BACKGROUND_ATTACHMENT_FIXED, is_appeal_area_background_attachment_fixed(), __( 'アピールエリア背景画像の固定', THEME_NAME ));
            genelate_tips_tag(__( 'アピールエリアに設定した背景画像を固定します。上下にスクロールしたときに背景画像が移動しなくなります。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- メッセージ -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_APPEAL_AREA_MESSAGE, __('メッセージ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_textarea_tag(OP_APPEAL_AREA_MESSAGE, get_appeal_area_message(), __( '', THEME_NAME ), 3) ;
            genelate_tips_tag(__( 'アピールエリアに表示するメッセージを入力してください。HTMLの入力も可能です。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ボタンメッセージ -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_APPEAL_AREA_BUTTON_MESSAGE, __('ボタンメッセージ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_textbox_tag(OP_APPEAL_AREA_BUTTON_MESSAGE, get_appeal_area_button_message(), __( '', THEME_NAME ));
            genelate_tips_tag(__( 'ボタンに表示する文字を入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ボタンリンク先 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_APPEAL_AREA_BUTTON_URL, __('ボタンリンク先', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_textbox_tag(OP_APPEAL_AREA_BUTTON_URL, get_appeal_area_button_url(), __( '', THEME_NAME ));
            genelate_tips_tag(__( 'ボタンのリンク先となるURLを入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ボタン色 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_APPEAL_AREA_BUTTON_BACKGROUND_COLOR, __('ボタン色', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_color_picker_tag(OP_APPEAL_AREA_BUTTON_BACKGROUND_COLOR,  get_appeal_area_button_background_color(), 'ボタン色');
            genelate_tips_tag(__( 'ボタン全体の色を選択してください。文字は白色となるので濃いめの色を設定することをおすすめします。', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->