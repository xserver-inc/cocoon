<div class="metabox-holder">

<!-- Google Tag Manager設定 -->
<div id="gtm" class="postbox">
  <h2 class="hndle"><?php _e( 'Googleタグマネージャ設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'Google Tag Managerの解析タグの設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- タグマネージャID -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_GOOGLE_TAG_MANAGER_TRACKING_ID, __( 'タグマネージャID', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_GOOGLE_TAG_MANAGER_TRACKING_ID, get_google_tag_manager_tracking_id(), __( 'GTM-XXXXXXX', THEME_NAME ));
            generate_tips_tag(__( 'GoogleタグマネージャのトラッキングIDを入力してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/google-tag-manager-id/'));
            ?>
          </td>
        </tr>

        <!-- AMP用タグマネージャID -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_GOOGLE_TAG_MANAGER_AMP_TRACKING_ID, __( 'AMP用 タグマネージャID', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_GOOGLE_TAG_MANAGER_AMP_TRACKING_ID, get_google_tag_manager_amp_tracking_id(), __( 'GTM-XXXXXXX', THEME_NAME ));
            generate_tips_tag(__( 'AMP用のGoogleタグマネージャのトラッキングIDを入力してください。新たにAMP用のコンテナを作成しIDを設定してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/google-tag-manager-amp-id/'));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

<!-- Google Analytics設定 -->
<div id="ga" class="postbox">
  <h2 class="hndle"><?php _e( 'Google Analytics設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'Google Analyticsの解析タグの設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- Google AnalyticsトラッキングID -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_GOOGLE_ANALYTICS_TRACKING_ID, __( 'Google AnalyticsトラッキングID', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_GOOGLE_ANALYTICS_TRACKING_ID, get_google_analytics_tracking_id(), __( 'UA-00000000-0', THEME_NAME ));
            generate_tips_tag(__( 'Google AnalyticsのトラッキングIDを入力してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/google-analytics/'));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


<!-- Google Search Console設定 -->
<div id="gsc" class="postbox">
  <h2 class="hndle"><?php _e( 'Google Search Console設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'Google Search Consoleのサイト認証タグの設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- Google Search Console ID -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_GOOGLE_SEARCH_CONSOLE_ID, __( 'Google Search Console ID', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_GOOGLE_SEARCH_CONSOLE_ID, get_google_search_console_id(), __( 'サイト認証IDのみ入力', THEME_NAME ));
            generate_tips_tag(__( 'Google Search Consoleのサイト認証IDを入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

<?php if (0): ?>
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
            <?php generate_label_tag(OP_PTENGINE_TRACKING_ID, __( 'PtengineのトラッキングID', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_PTENGINE_TRACKING_ID, get_ptengine_tracking_id(), __( 'PtengineのトラッキングIDのみ入力', THEME_NAME ));
            generate_tips_tag(__( 'PtengineのトラッキングIDを入力してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/ptengine/'));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>
<?php endif ?>



<!-- その他のアクセス解析設定 -->
<div id="other" class="postbox">
  <h2 class="hndle"><?php _e( 'その他のアクセス解析設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ヘッダーやフッターに、その他サービスのアクセス解析タグをそのまま貼り付けます。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- アクセス解析headタグ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_OTHER_ANALYTICS_HEAD_TAGS, __( 'アクセス解析タグ（ヘッド用）', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textarea_tag(OP_OTHER_ANALYTICS_HEAD_TAGS, get_other_analytics_head_tags(), __( 'head用のアクセス解析タグの入力', THEME_NAME )) ;
            generate_tips_tag(__( 'ヘッドタグ（&lt;head&gt;&lt;/head&gt;）内に挿入する必要のある、その他アクセス解析タグを入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- アクセス解析ヘッダータグ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_OTHER_ANALYTICS_HEADER_TAGS, __( 'アクセス解析タグ（ヘッダー用）', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textarea_tag(OP_OTHER_ANALYTICS_HEADER_TAGS, get_other_analytics_header_tags(), __( 'ヘッダー用のアクセス解析タグの入力', THEME_NAME )) ;
            generate_tips_tag(__( 'ヘッダー（&lt;body&gt;直後）に挿入する必要のある、その他アクセス解析タグを入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- アクセス解析フッタータグ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_OTHER_ANALYTICS_FOOTER_TAGS, __( 'アクセス解析タグ（フッター用）', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textarea_tag(OP_OTHER_ANALYTICS_FOOTER_TAGS, get_other_analytics_footer_tags(), __( 'フッター用のアクセス解析タグの入力', THEME_NAME )) ;
            generate_tips_tag(__( 'フッター（&lt;/body&gt;直前）に挿入する必要のある、その他アクセス解析タグを入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


</div><!-- /.metabox-holder -->