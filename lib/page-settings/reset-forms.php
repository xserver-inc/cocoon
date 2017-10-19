<div class="metabox-holder">

<!-- 設定のリセット -->
<div id="reset" class="postbox">
  <h2 class="hndle"><?php _e( '設定のリセット', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '全ての設定内容をリセットします。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- リセット  -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_RESET_ALL_SETTINGS, __( 'リセット', THEME_NAME ) ); ?>
          </th>
          <td>
            <!-- <input name="reset_all_settings111" value="1" type="checkbox"> -->
            <?php
            genelate_checkbox_tag(OP_RESET_ALL_SETTINGS , 0, __( '全ての設定をリセットする', THEME_NAME ));
            genelate_tips_tag(__( 'テーマ設定をデフォルト状態に戻します。チェックをつけて設定の保存を行ってください。', THEME_NAME ));
            genelate_checkbox_tag(OP_CONFIRM_RESET_ALL_SETTINGS , 0, __( 'リセット動作の確認', THEME_NAME ));
            genelate_tips_tag(__( '誤って設定を削除するのを防ぐために確認のためもう一度チェックしてください。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->