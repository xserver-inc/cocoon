<table class="form-table">
  <tbody>

    <!-- 広告の表示 -->
    <tr>
      <th scope="row">
        <label for="<?php echo OP_ALL_ADS_VISIBLE; ?>"><?php _e( '広告の表示', THEME_NAME ) ?></label>
      </th>
      <td>
        <input type="checkbox" name="<?php echo OP_ALL_ADS_VISIBLE; ?>" value="1"<?php echo(is_all_ads_visible() ? ' checked="checked"' : ''); ?>><?php _e("全ての広告を表示する",THEME_NAME ); ?>
        <p class="tips"><?php _e( '全ての広告の表示を切り替えます。', THEME_NAME ) ?></p>
      </td>
    </tr>

    <!-- 広告コード -->
    <tr>
      <th scope="row">
        <label for="<?php echo OP_AD_CODE; ?>"><?php _e( '広告コード', THEME_NAME ) ?></label>
      </th>
      <td>
        <textarea name="<?php echo OP_AD_CODE; ?>" cols="<?php echo DEFAULT_INPUT_COLS; ?>" rows="<?php echo DEFAULT_INPUT_ROWS; ?>" placeholder="<?php _e( 'AdSenseのレスポンシブコードを入力してください', THEME_NAME ) ?>"><?php echo get_ad_code(); ?></textarea>
        <p class="tips"><?php _e( 'AdSenseのレスポンシブ広告コードを入力してください。', THEME_NAME ); ?></p>
      </td>
    </tr>


    <!-- 広告コード -->
    <tr>
      <th scope="row">
        <label for="<?php echo OP_AD_LABEL; ?>"><?php _e( '広告ラベル', THEME_NAME ) ?></label>
      </th>
      <td>
        <input type="text" name="<?php echo OP_AD_LABEL; ?>" size="<?php echo DEFAULT_INPUT_COLS; ?>" value="<?php echo get_ad_label(); ?>" placeholder="<?php _e( '「スポンサーリンク」か「広告」推奨', THEME_NAME ); ?>">
        <p class="tips"><?php _e( '広告上部ラベルに表示されるテキストの入力です。', THEME_NAME ) ?></p>
      </td>
    </tr>


    <!-- 広告除外記事ID -->
    <tr>
      <th scope="row">
        <label for="<?php echo OP_AD_EXCLUDE_POST_IDS; ?>"><?php _e( '広告除外記事ID', THEME_NAME ) ?></label>
      </th>
      <td>
        <input type="text" name="<?php echo OP_AD_EXCLUDE_POST_IDS; ?>" size="<?php echo DEFAULT_INPUT_COLS; ?>" value="<?php echo get_ad_exclude_post_ids(); ?>" placeholder="<?php _e( '例：111,222,3333', THEME_NAME ); ?>">
        <p class="tips"><?php _e( '広告を非表示にする投稿・固定ページのIDを,（カンマ）区切りで指定してください。', THEME_NAME ) ?></p>
      </td>
    </tr>

    <!-- 広告除外カテゴリーID -->
    <tr>
      <th scope="row">
        <label for="<?php echo OP_AD_EXCLUDE_CATEGORY_IDS; ?>"><?php _e( '広告除外カテゴリーID', THEME_NAME ) ?></label>
      </th>
      <td>
        <input type="text" name="<?php echo OP_AD_EXCLUDE_CATEGORY_IDS; ?>" size="<?php echo DEFAULT_INPUT_COLS; ?>" value="<?php echo get_ad_exclude_category_ids(); ?>" placeholder="<?php _e( '例：111,222,3333', THEME_NAME ); ?>">
        <p class="tips"><?php _e( '広告を非表示にするカテゴリーのIDを,（カンマ）区切りで指定してください。', THEME_NAME ) ?></p>
      </td>
    </tr>

  </tbody>
</table>