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

      </tbody>
    </table>

  </div>
</div>




</div><!-- /.metabox-holder -->