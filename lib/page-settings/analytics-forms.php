<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- アクセス解析設定 -->
<div id="analytics-all" class="postbox">
  <h2 class="hndle"><?php _e( 'アクセス解析設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'アクセス解析全般に適用される設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- 解析全般 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ANALYTICS_ADMIN_INCLUDE, __('解析全般', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_ANALYTICS_ADMIN_INCLUDE , is_analytics_admin_include(), __( 'サイト管理者も含めてアクセス解析する', THEME_NAME ));
            generate_tips_tag(__( 'サイト管理者に対してアクセス解析タグを出力するかどうかの設定です。サイト管理者を解析したくない場合は無効にしてください。', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>

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
            generate_tips_tag(__( 'GoogleタグマネージャのトラッキングIDを入力してください。Google AnalyticsトラッキングIDが入っていてもこちらが優先して計測されます。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/google-tag-manager-id/'));
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

        <!-- GA4測定ID -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_GA4_TRACKING_ID, __( 'GA4測定ID', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_GA4_TRACKING_ID, get_ga4_tracking_id(), __( 'G-XXXXXXXXXXXX', THEME_NAME ));
            generate_tips_tag(__( 'Google Analytics 4の測定IDを入力してください。タグマネージャのトラッキングIDが入っている場合はタグマネージャが優先されます。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/ga4/'));
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
            generate_tips_tag(__( 'Google Search Consoleのサイト認証IDを入力してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/google-search-console/'));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


<!-- Clarity設定 -->
<div id="clarity" class="postbox">
  <h2 class="hndle"><?php _e( 'Clarity設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ヒートマップ分析ツールClarityの設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- プロジェクトID -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_CLARITY_PROJECT_ID, __( 'プロジェクトID', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_CLARITY_PROJECT_ID, get_clarity_project_id(), __( 'プロジェクトIDのみ入力', THEME_NAME ));
            generate_tips_tag(__( 'ClarityのプロジェクトIDを入力してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/clarity/'));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>



<!-- その他のアクセス解析設定 -->
<div id="other" class="postbox">
  <h2 class="hndle"><?php _e( 'その他のアクセス解析・認証コード設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ヘッダーやフッターに、その他サービスのアクセス解析・サイト認証タグをそのまま貼り付けます。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- アクセス解析headタグ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_OTHER_ANALYTICS_HEAD_TAGS, __( 'ヘッド用コード', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textarea_tag(OP_OTHER_ANALYTICS_HEAD_TAGS, get_other_analytics_head_tags(), __( 'head用のタグ入力', THEME_NAME )) ;
            generate_tips_tag(__( 'ヘッドタグ（&lt;head&gt;&lt;/head&gt;）内に挿入する必要のある、その他アクセス解析・認証タグを入力してください。アドセンス認証コードもこちらに貼り付けて、審査を受けるのが最も楽かと思います。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- アクセス解析ヘッダータグ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_OTHER_ANALYTICS_HEADER_TAGS, __( 'ヘッダー用コード', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textarea_tag(OP_OTHER_ANALYTICS_HEADER_TAGS, get_other_analytics_header_tags(), __( 'ヘッダー用のタグ入力', THEME_NAME )) ;
            generate_tips_tag(__( 'ヘッダー（&lt;body&gt;直後）に挿入する必要のある、その他アクセス解析・認証タグを入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- アクセス解析フッタータグ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_OTHER_ANALYTICS_FOOTER_TAGS, __( 'フッター用コード', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textarea_tag(OP_OTHER_ANALYTICS_FOOTER_TAGS, get_other_analytics_footer_tags(), __( 'フッター用のタグ入力', THEME_NAME )) ;
            generate_tips_tag(__( 'フッター（&lt;/body&gt;直前）に挿入する必要のある、その他アクセス解析・認証タグを入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


</div><!-- /.metabox-holder -->
