<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- 管理画面設定 -->
<div id="admin" class="postbox">
  <h2 class="hndle"><?php _e( '管理画面設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '管理画面の機能設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- 管理者向け設定  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( '管理メニュー', THEME_NAME ) );
            generate_preview_tooltip_tag('https://im-cocoon.net/wp-content/uploads/admin-menu.png', __( '管理画面に素早く移動するためのメニューリンクです。', THEME_NAME )); ?>
          </th>
          <td>
            <?php

            //ツールバー（管理バー）に独自管理メニューを表示する
            generate_checkbox_tag(OP_ADMIN_TOOL_MENU_VISIBLE, is_admin_tool_menu_visible(), __( 'ツールバー（管理バー）に独自管理メニューを表示する', THEME_NAME ));
            generate_tips_tag(__( 'ツールバー（管理バー）に手軽に設定画面にアクセスできるメニューを表示します。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

<?php if (false): ?>
<!-- ダッシュボードメッセージ -->
<div id="admin-dashboard" class="postbox">
  <h2 class="hndle"><?php _e( 'ダッシュボードメッセージ', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'WordPress管理画面上部に表示されるメッセージに関する設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <!-- メッセージ表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( 'メッセージ表示', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php

            //ダッシュボードメッセージの表示
            generate_checkbox_tag(OP_DASHBOARD_MESSAGE_VISIBLE, is_dashboard_message_visible(), __( 'ダッシュボードメッセージの表示', THEME_NAME ));
            generate_tips_tag(__( '管理画面上部のメッセージを表示するかどうか。', THEME_NAME ).__( '新型コロナウイルスに関する情報は、WHOの終息宣言が出るか、それに近い状態になるまで表示します（今回のウイルスの性質的に完全に終息できるものかもわからないので）。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>
<?php endif; ?>

<!-- 投稿一覧設定 -->
<div id="admin-single-index" class="postbox">
  <h2 class="hndle"><?php _e( '投稿・固定ページ一覧設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php
      _e( '管理画面の投稿・固定ページ一覧ページの設定です。', THEME_NAME );
      generate_help_page_tag('https://wp-cocoon.com/post-columns-switch/');
     ?></p>

    <table class="form-table">
      <tbody>

        <!-- カラム表示  -->
        <tr>
          <th scope="row">
            <?php
            generate_label_tag('', __( 'カラム表示', THEME_NAME ) );
            generate_preview_tooltip_tag('https://im-cocoon.net/wp-content/uploads/columns-display.png', __( '投稿・固定ページ記事一覧テーブルのカラム操作。', THEME_NAME ));
             ?>
          </th>
          <td>
            <?php

            generate_checkbox_tag(OP_ADMIN_LIST_AUTHOR_VISIBLE , is_admin_list_author_visible(), __( '投稿者を表示する', THEME_NAME ));
            echo '<br>';

            generate_checkbox_tag(OP_ADMIN_LIST_CATEGORIES_VISIBLE , is_admin_list_categories_visible(), __( 'カテゴリーを表示する', THEME_NAME ));
            echo '<br>';

            generate_checkbox_tag(OP_ADMIN_LIST_TAGS_VISIBLE , is_admin_list_tags_visible(), __( 'タグを表示する', THEME_NAME ));
            echo '<br>';

            generate_checkbox_tag(OP_ADMIN_LIST_COMMENTS_VISIBLE , is_admin_list_comments_visible(), __( 'コメントを表示する', THEME_NAME
            ));
            echo '<br>';

            generate_checkbox_tag(OP_ADMIN_LIST_DATE_VISIBLE , is_admin_list_date_visible(), __( '日付を表示する', THEME_NAME ));
            echo '<br>';

            generate_checkbox_tag(OP_ADMIN_LIST_POST_ID_VISIBLE , is_admin_list_post_id_visible(), __( 'IDを表示する', THEME_NAME ));
            echo '<br>';

            generate_checkbox_tag(OP_ADMIN_LIST_WORD_COUNT_VISIBLE , is_admin_list_word_count_visible(), __( '文字数を表示する', THEME_NAME ));
            echo '<br>';

            generate_checkbox_tag(OP_ADMIN_LIST_PV_VISIBLE , is_admin_list_pv_visible(), __( 'PVを表示する', THEME_NAME ).__( '（※投稿・固定ページ一覧が重い場合は無効にしてください）', THEME_NAME ));
            echo '<br>';

            generate_checkbox_tag(OP_ADMIN_LIST_EYECATCH_VISIBLE , is_admin_list_eyecatch_visible(), __( 'アイキャッチを表示する', THEME_NAME ));
            echo '<br>';

            generate_checkbox_tag(OP_ADMIN_LIST_MEMO_VISIBLE , is_admin_list_memo_visible(), __( 'メモの内容を表示する', THEME_NAME ));
            generate_tips_tag(__( '投稿一覧テーブルのカラム表示を切り替えます。', THEME_NAME ));

            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>






<!-- 管理者パネル -->
<div id="admin-panel" class="postbox">
  <h2 class="hndle"><?php _e( '管理者パネル', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '管理者向けのPV表示や編集リンクの表示です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- 管理者パネルの表示  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ADMIN_PANEL_DISPLAY_TYPE, __( '管理者パネルの表示', THEME_NAME ) );
            generate_preview_tooltip_tag('https://im-cocoon.net/wp-content/uploads/admin-panel.png', __( '管理者用のPV表示エリア、各種チェックツール表示用のパネルです。', THEME_NAME )); ?>
          </th>
          <td>
            <?php            $options = array(
              'all' => __( '全て表示する', THEME_NAME ),
              'pc_only' => __( 'PCのみ表示する', THEME_NAME ),
              'mobile_only' => __( 'モバイルのみ表示する', THEME_NAME ),
              'none' => __( '表示しない', THEME_NAME ),
            );
            generate_selectbox_tag(OP_ADMIN_PANEL_DISPLAY_TYPE, $options, get_admin_panel_display_type());
            generate_tips_tag(__( '管理者パネルの表示形式を選択します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- PVの表示  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ADMIN_PANEL_PV_AREA_VISIBLE, __( 'PVの表示', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_ADMIN_PANEL_PV_AREA_VISIBLE, is_admin_panel_pv_area_visible(), __( 'PVエリアを表示する', THEME_NAME ));
            generate_tips_tag(__( '管理者パネル内のPVエリアを表示します。', THEME_NAME ).get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/admin-panel-pv.png'));
            ?>
          </td>
        </tr>


        <!-- 編集エリアの表示  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ADMIN_PANEL_EDIT_AREA_VISIBLE, __( '編集エリアの表示', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_ADMIN_PANEL_EDIT_AREA_VISIBLE, is_admin_panel_edit_area_visible(), __( '編集エリアを表示する', THEME_NAME ));
            generate_tips_tag(__( '管理者パネル内の編集エリアを表示します。', THEME_NAME ));
            ?>
            <div class="indent">
              <?php
              generate_checkbox_tag(OP_ADMIN_PANEL_WP_DASHBOARD_VISIBLE , is_admin_panel_wp_dashboard_visible(), __( 'ダッシュボードリンクを表示する', THEME_NAME ));
              generate_tips_tag(__( 'WordPressのダッシュボードに移動するためのリンクです。', THEME_NAME ));

              generate_checkbox_tag(OP_ADMIN_PANEL_WP_EDIT_VISIBLE , is_admin_panel_wp_edit_visible(), __( '投稿編集リンクの表示', THEME_NAME ));
              generate_tips_tag(__( 'WordPress管理画面で投稿内容を編集するためのリンクです。', THEME_NAME ));

              generate_checkbox_tag(OP_ADMIN_PANEL_WLW_EDIT_VISIBLE , is_admin_panel_wlw_edit_visible(), __( 'WLW編集リンクの表示', THEME_NAME ));
              generate_tips_tag(__( 'Windows Live Writerで投稿内容を編集するためのリンクです。', THEME_NAME ));
               ?>
            </div>
          </td>
        </tr>

        <?php if (false): ?>
        <!-- AMPエリアの表示  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ADMIN_PANEL_AMP_AREA_VISIBLE, __( 'AMPエリアの表示', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_ADMIN_PANEL_AMP_AREA_VISIBLE, is_admin_panel_amp_area_visible(), __( 'AMPエリア表示する', THEME_NAME ));
            generate_tips_tag(__( 'AMP動作確認・テストリンクなどを表示します。', THEME_NAME ));
            ?>
            <div class="indent">
              <?php
              generate_checkbox_tag(OP_ADMIN_GOOGLE_AMP_TEST_VISIBLE, is_admin_google_amp_test_visible(), __( 'Google AMPテストを表示', THEME_NAME ));
              generate_tips_tag(__( '<a href="https://search.google.com/test/amp" target="_blank" rel="noopener">AMP テスト - Google Search Console</a>でチェックするためのリンクの表示。', THEME_NAME ));

              generate_checkbox_tag(OP_ADMIN_THE_AMP_VALIDATOR_VISIBLE, is_admin_the_amp_validator_visible(), __( 'The AMP Validatorを表示', THEME_NAME ));
              generate_tips_tag(__( '<a href="https://validator.ampproject.org/#" target="_blank" rel="noopener">The AMP Validator</a>でチェックするためのリンクの表示。', THEME_NAME ));

              // generate_checkbox_tag(OP_ADMIN_AMPBENCH_VISIBLE, is_admin_ampbench_visible(), __( 'AMPBenchを表示', THEME_NAME ));
              // generate_tips_tag(__( '<a href="https://ampbench.appspot.com/" target="_blank" rel="noopener">AMPBench</a>でチェックするためのリンクの表示。', THEME_NAME ));
              ?>
            </div>
          </td>
        </tr>
        <?php endif; ?>


        <!-- チェックツールエリアの表示  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ADMIN_PANEL_CHECK_TOOLS_AREA_VISIBLE, __( 'チェックツールエリアの表示', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_ADMIN_PANEL_CHECK_TOOLS_AREA_VISIBLE, is_admin_panel_check_tools_area_visible(), __( 'チェックツールエリアを表示する', THEME_NAME ));
            generate_tips_tag(__( 'ページを診断するためのチェックツールを表示するエリアを表示します。PageSpeed Insights、構造化データチェック、HTML5チェック、Twitterの反応など。', THEME_NAME ));
            ?>
            <div class="indent">
              <?php
              generate_checkbox_tag(OP_ADMIN_PAGESPEED_INSIGHTS_VISIBLE, is_admin_pagespeed_insights_visible(), __( 'PageSpeed Insightsを表示する', THEME_NAME ));
              generate_tips_tag(__( '<a href="https://developers.google.com/speed/pagespeed/insights/?filter_third_party_resources=true" target="_blank" rel="noopener">PageSpeed Insights</a>リンクの表示。', THEME_NAME ));

              generate_checkbox_tag(OP_ADMIN_GTMETRIX_VISIBLE, is_admin_gtmetrix_visible(), __( 'GTmetrixを表示する', THEME_NAME ));
              generate_tips_tag(__( '<a href="https://gtmetrix.com/" target="_blank" rel="noopener">GTmetrix</a>リンクの表示。', THEME_NAME ));

              generate_checkbox_tag(OP_ADMIN_STRUCTURED_DATA_VISIBLE, is_admin_structured_data_visible(), __( '構造化データチェックを表示する', THEME_NAME ));
              generate_tips_tag(__( '<a href="https://search.google.com/test/rich-results?hl=ja" target="_blank" rel="noopener">リッチリザルトテストツール</a>リンクの表示。', THEME_NAME ));

              generate_checkbox_tag(OP_ADMIN_NU_HTML_CHECKER_VISIBLE, is_admin_nu_html_checker_visible(), __( 'HTML5チェックを表示する', THEME_NAME ));
              generate_tips_tag(__( '<a href="https://validator.w3.org/nu/" target="_blank" rel="noopener">Nu Html Checker</a>リンクの表示。', THEME_NAME ));

              generate_checkbox_tag(OP_ADMIN_SEOCHEKI_VISIBLE, is_admin_seocheki_visible(), __( 'SEOチェキを表示する', THEME_NAME ));
              generate_tips_tag(__( '<a href="http://seocheki.net/" target="_blank" rel="noopener">SEOチェキ! 無料で使えるSEOツール</a>リンクの表示。', THEME_NAME ));

              generate_checkbox_tag(OP_ADMIN_TWEET_CHECK_VISIBLE, is_admin_tweet_check_visible(), __( 'ツイート検索を表示する', THEME_NAME ));
              generate_tips_tag(__( '投稿・固定ページ・インデックスページに対するツイート検索リンクの表示。', THEME_NAME ));
               ?>
            </div>
          </td>
        </tr>

        <!-- レスポンシブツールエリアの表示  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ADMIN_PANEL_RESPONSIVE_TOOLS_AREA_VISIBLE, __( 'レスポンシブツールエリアの表示', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_ADMIN_PANEL_RESPONSIVE_TOOLS_AREA_VISIBLE, is_admin_panel_responsive_tools_area_visible(), __( 'レスポンシブチェックツールエリアを表示する', THEME_NAME ));
            generate_tips_tag(__( 'レスポンシブ表示状態を効率的にチェックできるツールエリアの表示を切り替えます。', THEME_NAME ));
            ?>
            <div class="indent">
              <?php
              generate_checkbox_tag(OP_ADMIN_RESPONSINATOR_VISIBLE, is_admin_responsinator_visible(), __( 'Responsinatorチェックを表示する', THEME_NAME ));
              generate_tips_tag(__( '<a href="http://www.responsinator.com/" target="_blank" rel="noopener">Responsinator</a>チェック用リンクの表示。', THEME_NAME ));

              generate_checkbox_tag(OP_ADMIN_SIZZY_VISIBLE, is_admin_sizzy_visible(), __( 'Sizzyチェックを表示する', THEME_NAME ));
              generate_tips_tag(__( '<a href="https://sizzy.co/" target="_blank" rel="noopener">Sizzy</a>チェック用リンクの表示。', THEME_NAME ));

              generate_checkbox_tag(OP_ADMIN_MULTI_SCREEN_RESOLUTION_TEST_VISIBLE, is_admin_multi_screen_resolution_test_visible(), __( 'ScreenResolutionチェックを表示する', THEME_NAME ));
              generate_tips_tag(__( '<a href="http://whatismyscreenresolution.net/multi-screen-test" target="_blank" rel="noopener">WhatIsMyScreenResolution</a>チェック用リンクの表示。', THEME_NAME ));
              ?>

            </div>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>


<!-- インデックス設定 -->
<div id="admin-index_" class="postbox">
  <h2 class="hndle"><?php _e( 'インデックス設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'インデックスページのエントリーカードに表示されるPVの設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- エントリーカード  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( 'エントリーカード', THEME_NAME ) );
            generate_preview_tooltip_tag('https://im-cocoon.net/wp-content/uploads/admin-index-pv.png', __( 'エントリーカードのPV表示エリアの設定です。', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            //インデックスのエントリーカードにPV数を表示
            generate_checkbox_tag(OP_ADMIN_INDEX_PV_VISIBLE, is_admin_index_pv_visible(), __( 'インデックスにPV数を表示する', THEME_NAME ));
            generate_tips_tag(__( 'インデックスページのエントリーカードごとにPV数を表示します。集計方法がJetpackの場合は、初回アクセス時に情報取得に時間がかかります。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/admin-index-pv/'));
              ?>
            <span><?php _e( 'アクセス集計方法', THEME_NAME ) ?></span>
            <?php
            $theme = '';
            //テーマのアクセス取得が有効でないとき
            if (!is_access_count_enable()) {
              $theme = '<span class="red">'.__( '※テーマのアクセス集計が有効になっていません。', THEME_NAME ).'</span>';
            }
            $jet = '';
            //Jetpackの統計機能が有効でないとき
            if (!is_jetpack_stats_module_active()) {
              $jet = '<span class="red">'.__( '※Jetpackの統計機能が有効になっていません。', THEME_NAME ).'</span>';
            }
            $options = array(
              THEME_NAME => __( 'テーマ独自', THEME_NAME ).$theme,
              'jetpack' => __( 'Jetpack', THEME_NAME ).$jet,
            );
            generate_radiobox_tag(OP_ADMIN_PANEL_PV_TYPE, $options, get_admin_panel_pv_type());
            generate_tips_tag(__( '管理者パネルで表示するPVの取得方法を選択します。', THEME_NAME ));

              ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>



</div><!-- /.metabox-holder -->
