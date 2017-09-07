<table class="form-table">
  <tbody>
    <tr>
      <th scope="row">
        <label for="<?php echo AD_ALL_ADS_VISIBLE_NAME; ?>"><?php _e( '広告の表示', THEME_NAME ) ?></label>
      </th>
      <td>
        <input type="checkbox" name="<?php echo AD_ALL_ADS_VISIBLE_NAME; ?>" id="<?php echo AD_ALL_ADS_VISIBLE_NAME; ?>" value="1"<?php echo(is_all_ads_visible() ? ' checked="checked"' : ''); ?>><?php _e("全ての広告を表示する",THEME_NAME ); ?>
        <p class="tips"><?php _e( '全ての広告の表示を切り替えます。', THEME_NAME ) ?></p>
      </td>

    </tr>
    <tr>
      <th scope="row">
        <label for="<?php echo AD_AD_CODE_NAME; ?>"><?php _e( '広告コード', THEME_NAME ) ?></label>
      </th>
      <td>
        <textarea id="<?php echo AD_AD_CODE_NAME; ?>" name="<?php echo AD_AD_CODE_NAME; ?>" cols="46" rows="6" placeholder="AdSenseのレスポンシブコードを入力してください"><?php echo get_ad_code(); ?></textarea>
        <p class="tips"><?php _e( 'AdSenseのレスポンシブ広告コードを入力してください。', THEME_NAME ) ?></p>
      </td>

    </tr>
  </tbody>
</table>