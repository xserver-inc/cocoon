<div class="metabox-holder">

<!-- Analytics設定 -->
<div id="analytics" class="postbox">
  <h2 class="hndle"><?php _e( 'Google Analytics設定', THEME_NAME ) ?></h2>
  <div class="inside">

<table class="form-table">
  <tbody>

    <!-- Google AnalyticsトラッキングID -->
    <tr>
      <th scope="row">
        <label for="<?php echo OP_GOOGLE_ANALYTICS_TRACKING_ID; ?>"><?php _e( 'Google AnalyticsトラッキングID', THEME_NAME ) ?></label>
      </th>
      <td>
        <input type="text" name="<?php echo OP_GOOGLE_ANALYTICS_TRACKING_ID; ?>" size="<?php echo DEFAULT_INPUT_COLS; ?>" value="<?php echo get_google_analytics_tracking_id(); ?>" placeholder="<?php _e( 'UA-00000000-0', THEME_NAME ); ?>">
        <p class="tips"><?php _e( 'Google AnalyticsのトラッキングIDを入力してください。', THEME_NAME ) ?></p>
      </td>
    </tr>

  </tbody>
</table>

  </div>
</div>

</div><!-- /.metabox-holder -->