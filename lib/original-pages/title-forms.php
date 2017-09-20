<div class="metabox-holder">

<!-- Google Analytics設定 -->
<div id="analytics" class="postbox">
  <h2 class="hndle"><?php _e( 'Google Analytics設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'Google Analyticsの解析タグの設定です。', THEME_NAME ) ?></p>

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


<!-- Google Search Console設定 -->
<div id="analytics" class="postbox">
  <h2 class="hndle"><?php _e( 'Google Search Console設定', THEME_NAME ) ?></h2>
  <div class="inside">
    
    <p><?php _e( 'Google Search Consoleのサイト認証タグの設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- Google Search Console ID -->
        <tr>
          <th scope="row">
            <label for="<?php echo OP_GOOGLE_SEARCH_CONSOLE_ID; ?>"><?php _e( 'Google Search Console ID', THEME_NAME ) ?></label>
          </th>
          <td>
            <input type="text" name="<?php echo OP_GOOGLE_SEARCH_CONSOLE_ID; ?>" size="<?php echo DEFAULT_INPUT_COLS; ?>" value="<?php echo get_google_search_console_id(); ?>" placeholder="<?php _e( 'サイト認証IDのみ入力', THEME_NAME ); ?>">
            <p class="tips"><?php _e( 'Google Search Consoleのサイト認証IDを入力してください。', THEME_NAME ) ?></p>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


<!-- Ptengine設定 -->
<div id="ptengine" class="postbox">
  <h2 class="hndle"><?php _e( 'Ptengine設定', THEME_NAME ) ?></h2>
  <div class="inside">
    
    <p><?php _e( 'Ptengineの解析タグの設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- PtengineのトラッキングID -->
        <tr>
          <th scope="row">
            <label for="<?php echo OP_PTENGINE_TRACKING_ID; ?>"><?php _e( 'PtengineのトラッキングID', THEME_NAME ) ?></label>
          </th>
          <td>
            <input type="text" name="<?php echo OP_PTENGINE_TRACKING_ID; ?>" size="<?php echo DEFAULT_INPUT_COLS; ?>" value="<?php echo get_ptengine_tracking_id(); ?>" placeholder="<?php _e( 'PtengineのトラッキングIDのみ入力', THEME_NAME ); ?>">
            <p class="tips"><?php _e( 'PtengineのトラッキングIDを入力してください。', THEME_NAME ) ?></p>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


<!-- その他のアクセス解析設定 -->
<div id="other" class="postbox">
  <h2 class="hndle"><?php _e( 'その他のアクセス解析設定', THEME_NAME ) ?></h2>
  <div class="inside">
    
    <p><?php _e( 'ヘッダーやフッターに、その他サービスのアクセス解析タグをそのまま貼り付けます。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- アクセス解析ヘッダータグ -->
        <tr>
          <th scope="row">
            <label for="<?php echo OP_OTHER_ANALYTICS_HEADER_TAGS; ?>"><?php _e( 'アクセス解析タグ（ヘッダー用）', THEME_NAME ) ?></label>
          </th>
          <td>
            <textarea name="<?php echo OP_OTHER_ANALYTICS_HEADER_TAGS; ?>" cols="<?php echo DEFAULT_INPUT_COLS; ?>" rows="<?php echo DEFAULT_INPUT_ROWS; ?>" placeholder="<?php _e( 'ヘッダー用のアクセス解析タグの入力', THEME_NAME ) ?>"><?php echo get_other_analytics_header_tags(); ?></textarea>
            <p class="tips"><?php _e( 'ヘッダーに挿入する必要のある、その他アクセス解析タグを入力してください。', THEME_NAME ); ?></p>
          </td>
        </tr>

        <!-- アクセス解析フッタータグ -->
        <tr>
          <th scope="row">
            <label for="<?php echo OP_OTHER_ANALYTICS_FOOTER_TAGS; ?>"><?php _e( 'アクセス解析タグ（フッター用）', THEME_NAME ) ?></label>
          </th>
          <td>
            <textarea name="<?php echo OP_OTHER_ANALYTICS_FOOTER_TAGS; ?>" cols="<?php echo DEFAULT_INPUT_COLS; ?>" rows="<?php echo DEFAULT_INPUT_ROWS; ?>" placeholder="<?php _e( 'フッター用のアクセス解析タグの入力', THEME_NAME ) ?>"><?php echo get_other_analytics_footer_tags(); ?></textarea>
            <p class="tips"><?php _e( 'フッターに挿入する必要のある、その他アクセス解析タグを入力してください。', THEME_NAME ); ?></p>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


</div><!-- /.metabox-holder -->