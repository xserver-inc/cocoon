<div class="metabox-holder">

<!-- モバイルボタン -->
<div id="mobile-buttons" class="postbox">
  <h2 class="hndle"><?php _e( 'モバイルボタン設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'モバイル環境で表示するメニューボタンの設定です。768px以下で表示されます。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- モバイルボタンレイアウト -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_MOBILE_BUTTON_LAYOUT_TYPE, __('モバイルボタンレイアウト', THEME_NAME) ); ?>
          </th>
          <td>
            <div class="col-2">
              <div>
                <?php
                $options = array(
                  'none' => __( 'ボタンを表示しない（ミドルメニューのみ）', THEME_NAME ),
                  'top' => __( 'トップボタン', THEME_NAME ),
                  'slide_in' => __( 'スライドインボタン', THEME_NAME ),
                  // 'slidein' => __( 'スライドインメニューボタン', THEME_NAME ),
                  // 'top_slidein' => __( 'トップボタン＆スライドインメニューボタン', THEME_NAME ),
                );
                generate_radiobox_tag(OP_MOBILE_BUTTON_LAYOUT_TYPE, $options, get_mobile_button_layout_type());
                generate_tips_tag(__( 'モバイルメニュー等を表示するための設定を行います。', THEME_NAME ));
                ?>
              </div>
              <div>
                <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
                <div class="demo mobile-demo" style="width: 370px;">
                  <iframe id="mobile-demo" class="iframe-demo" src="<?php echo home_url(); ?>" width="360" height="640"></iframe>
                </div>
              </div>
            </div>

          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->