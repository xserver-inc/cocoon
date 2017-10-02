<div class="metabox-holder">

<!-- ヘッダー設定 -->
<div id="header" class="postbox">
  <h2 class="hndle"><?php _e( 'ヘッダー設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ヘッダーの表示設定を行います。', THEME_NAME ) ?></p>
    <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
    <div class="demo">
      <?php get_template_part('tmp/header-container'); ?>
    </div>

    <table class="form-table">
      <tbody>

        <!-- ヘッダーロゴ -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_THE_SITE_LOGO_URL, __( 'ヘッダーロゴ', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            genelate_upload_image_tag(OP_THE_SITE_LOGO_URL, get_the_site_logo_url());
            genelate_tips_tag(__( 'ヘッダー部分に表示する画像を設定します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ヘッダー背景画像 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_HEADER_BACKGROUND_IMAGE_URL, __( 'ヘッダー背景画像', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            genelate_upload_image_tag(OP_HEADER_BACKGROUND_IMAGE_URL, get_header_background_image_url());
            genelate_tips_tag(__( 'ヘッダー背景として表示する画像を設定します。', THEME_NAME ));

            //ヘッダー背景画像の固定
            genelate_checkbox_tag(OP_HEADER_BACKGROUND_ATTACHMENT_FIXED, is_header_background_attachment_fixed(), __( 'ヘッダー背景画像の固定', THEME_NAME ));
            genelate_tips_tag(__( 'ヘッダーに設定した背景画像を固定します。上下にスクロールしたときに背景画像が移動しなくなります。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ヘッダーレイアウト -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_HEADER_LAYOUT_TYPE, __( 'ヘッダーレイアウト', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'center_logo' => 'センターロゴ',
              'top_menu' => 'トップメニュー',
              'top_menu_small' => 'トップメニュー（小）',
            );
            genelate_radiobox_tag(OP_HEADER_LAYOUT_TYPE, $options, get_header_layout_type());
            genelate_tips_tag(__( 'ヘッダーの表示形式を選択します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ヘッダー色 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag('', __( 'ヘッダー色', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            genelate_color_picker_tag(OP_HEADER_BACKGROUND_COLOR,  get_header_background_color(), '背景色');
            genelate_tips_tag(__( 'グローバルナビ上のヘッダー背景色を選択します。', THEME_NAME ));

            genelate_color_picker_tag(OP_HEADER_TEXT_COLOR,  get_header_text_color(), '文字色');
            genelate_tips_tag(__( 'グローバルナビ上のヘッダーテキスト色を選択します。', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>


</div><!-- /.metabox-holder -->