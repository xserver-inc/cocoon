<table class="form-table">
  <tbody>
    <tr>
      <th scope="row">
        <label for="<?php echo AD_ADS_VISIBLE_NAME; ?>">広告の表示</label>
      </th>
      <td>
        <input type="checkbox" name="<?php echo AD_ADS_VISIBLE_NAME; ?>" id="<?php echo AD_ADS_VISIBLE_NAME; ?>" value="1"<?php echo($ads_visible_val ? ' checked="checked"' : ''); ?>><?php _e("全ての広告を表示する",THEME_NAME ); ?>
        <p class="tips"><?php _e( '全ての広告の表示を切り替えます。', THEME_NAME ) ?></p>
      </td>

    </tr>
    <tr>
      <th scope="row">
        <label for="<?php echo AD_AD_CODE_NAME; ?>">広告コード</label>
      </th>
      <td>
        <textarea id="<?php echo AD_AD_CODE_NAME; ?>" name="<?php echo AD_AD_CODE_NAME; ?>" cols="46" rows="6" placeholder="AdSenseのレスポンシブコードを入力してください"><?php echo stripslashes_deep($ad_code_val); ?></textarea>
        <p class="tips"><?php _e( 'AdSenseのレスポンシブ広告コードを入力してください。', THEME_NAME ) ?></p>
      </td>

    </tr>
  </tbody>
</table>