<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<!-- 広告設定 -->
<div id="ads" class="postbox">
  <h2 class="hndle"><?php _e( '広告設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '広告全般に関する設定です。アドセンス設定や、ウィジェットの設定も含みます。', THEME_NAME ); ?></p>

    <table class="form-table">
      <tbody>

        <!-- 広告の表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ALL_ADS_VISIBLE, __( '広告の表示', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_ALL_ADS_VISIBLE, is_all_ads_visible(), __("全ての広告を表示する",THEME_NAME ));
            generate_tips_tag(__( 'アドセンス設定、ウィジェット設定等、全ての広告の表示を切り替えます。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 広告ラベル -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_AD_LABEL_CAPTION, __( '広告ラベル', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_AD_LABEL_CAPTION, get_ad_label_caption(), __( '「スポンサーリンク」か「広告」推奨', THEME_NAME ));
            generate_tips_tag(__( '広告上部ラベルに表示されるテキストの入力です。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

<!-- アドセンス設定 -->
<div id="adsense" class="postbox">
  <h2 class="hndle"><?php _e( 'アドセンス設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'アドセンス広告に関する設定です。一応通常広告でも利用できるようにはなっています。', THEME_NAME );
    echo get_help_page_tag('https://wp-cocoon.com/how-to-set-adsense/'); ?></p>

    <table class="form-table">
      <tbody>

        <!-- アドセンス広告表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ALL_ADSENSES_VISIBLE, __( 'アドセンス広告の表示', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag( OP_ALL_ADSENSES_VISIBLE, is_all_adsenses_visible(), __( '全てのアドセンス広告を表示する', THEME_NAME ));
            generate_tips_tag(__( '「アドセンス設定」で設定した、アドセンス広告全ての表示を切り替えます。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 広告コード -->
        <tr>
          <th scope="row">
            <label for="<?php echo OP_AD_CODE; ?>"><?php _e( '広告コード', THEME_NAME ) ?></label>
          </th>
          <td>
            <?php
            //標準広告
            generate_textarea_tag(OP_AD_CODE, get_ad_code(), __( 'アドセンスのレスポンシブコードを入力', THEME_NAME )) ;
            generate_tips_tag(__( 'アドセンスのレスポンシブ広告コードを入力してください。サーバーのファイアウォールにより、保存時に403エラーが出る場合はscriptタグを取り除いて入力してみてください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 自動AdSense -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ADSENSE_DISPLAY_METHOD, __( 'アドセンス表示方式', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            // $options = array(
            //   'by_auto' => __( 'アドセンス自動広告のみ利用', THEME_NAME ),
            //   //'by_auto_and_myself' => __( '自動広告とマニュアル広告を併用', THEME_NAME ),
            //   'by_myself' => __( 'マニュアル広告設定（自前で位置を設定）', THEME_NAME ).__( '※要AdSense管理画面で自動広告無効', THEME_NAME ).get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/auto-adsense-off.png', __( 'AdSenseの「広告」設定からドメインの「編集」ボタンを押して「自動広告」の「無効」にしてください。', THEME_NAME ), 500),
            // );
            // generate_radiobox_tag(OP_ADSENSE_DISPLAY_METHOD, $options, get_adsense_display_method());
            // generate_tips_tag(__( '「自動広告」を選択した場合は、AdSenseが勝手に広告コードを挿入するので制御はできません。「自動広告のみ」が有効の場合、「広告の表示位置」や「[ad]ショートコード」で設定した広告の表示は無効になります。', THEME_NAME ));


            generate_checkbox_tag(OP_MOBILE_ADSENSE_WIDTH_WIDE, is_mobile_adsense_width_wide(), __("モバイル広告の幅を広くする",THEME_NAME ));
            generate_tips_tag(__( 'モバイルでAdSenseの幅を画面いっぱいにします。', THEME_NAME ).__( 'AdSenseタグの「data-full-width-responsive」を「true」にします。', THEME_NAME ).__( 'この機能が有効な場合、AdSenseの仕様で"horizontal","vertical"広告をモバイルで表示した際は"rectangle"として表示されます。', THEME_NAME ).__( '意図通りのサイズで表示する場合は機能を無効にしてください。', THEME_NAME ));
            ?>
          </td>
        </tr>


        <!-- 広告の表示位置 -->
        <tr<?php generate_not_allowed_form_class(!is_auto_adsens_only_enable()); ?>>
          <th scope="row">
            <?php generate_label_tag(OP_AD_POS_INDEX_TOP_VISIBLE, __( '広告の表示位置', THEME_NAME )); ?>
          </th>
          <td>

            <div class="col-2">

              <div>

                <!-- インデックスページ -->
                <p><strong><?php _e( 'インデックスページの表示位置', THEME_NAME ) ?></strong></p>
                <ul>
                  <li>
                    <?php
                    generate_checkbox_tag(OP_AD_POS_INDEX_TOP_VISIBLE, is_ad_pos_index_top_visible(), __('トップ' ,THEME_NAME ));
                    //詳細設定
                    generate_main_column_ad_detail_setting_forms(OP_AD_POS_INDEX_TOP_FORMAT, get_ad_pos_index_top_format(), OP_AD_POS_INDEX_TOP_LABEL_VISIBLE, is_ad_pos_index_top_label_visible());

                    ?>
                  </li>
                  <li>
                    <?php
                    generate_checkbox_tag(OP_AD_POS_INDEX_MIDDLE_VISIBLE, is_ad_pos_index_middle_visible(), __('ミドル' ,THEME_NAME ));
                    //詳細設定
                    generate_main_column_ad_detail_setting_forms(OP_AD_POS_INDEX_MIDDLE_FORMAT, get_ad_pos_index_middle_format(), OP_AD_POS_INDEX_MIDDLE_LABEL_VISIBLE, is_ad_pos_index_middle_label_visible()); ?>              </li>
                  <li>
                    <?php
                    generate_checkbox_tag(OP_AD_POS_INDEX_BOTTOM_VISIBLE, is_ad_pos_index_bottom_visible(), __('ボトム' ,THEME_NAME ));
                    //詳細設定
                    generate_main_column_ad_detail_setting_forms(OP_AD_POS_INDEX_BOTTOM_FORMAT, get_ad_pos_index_bottom_format(), OP_AD_POS_INDEX_BOTTOM_LABEL_VISIBLE, is_ad_pos_index_bottom_label_visible()); ?>

                  </li>
                </ul>

                <!-- サイドバー -->
                <p><strong><?php _e( 'サイドバーの表示位置', THEME_NAME ) ?></strong></p>
                <ul>
                  <li>
                    <?php
                    generate_checkbox_tag(OP_AD_POS_SIDEBAR_TOP_VISIBLE, is_ad_pos_sidebar_top_visible(), __('サイドバートップ' ,THEME_NAME ));
                    generate_sidebar_top_ad_tip_tag();
                    //詳細設定
                    generate_sidebar_ad_detail_setting_forms(OP_AD_POS_SIDEBAR_TOP_FORMAT, get_ad_pos_sidebar_top_format(), OP_AD_POS_SIDEBAR_TOP_LABEL_VISIBLE, is_ad_pos_sidebar_top_label_visible()); ?>
                  </li>
                  <li>
                    <?php
                    generate_checkbox_tag(OP_AD_POS_SIDEBAR_BOTTOM_VISIBLE, is_ad_pos_sidebar_bottom_visible(), __('サイドバーボトム' ,THEME_NAME ));
                    //詳細設定
                    generate_sidebar_ad_detail_setting_forms(OP_AD_POS_SIDEBAR_BOTTOM_FORMAT, get_ad_pos_sidebar_bottom_format(), OP_AD_POS_SIDEBAR_BOTTOM_LABEL_VISIBLE, is_ad_pos_sidebar_bottom_label_visible()); ?>
                  </li>
                </ul>

              </div>

              <!-- 投稿・固定ページ -->
              <div>
                <p><strong><?php _e( '投稿・固定ページの表示位置', THEME_NAME ) ?></strong></p>
                <ul>
                  <li>
                    <?php
                    generate_checkbox_tag(OP_AD_POS_ABOVE_TITLE_VISIBLE, is_ad_pos_above_title_visible(), __('タイトル上' ,THEME_NAME ));
                    generate_main_column_top_ad_tip_tag();
                    //詳細設定
                    generate_main_column_ad_detail_setting_forms(OP_AD_POS_ABOVE_TITLE_FORMAT, get_ad_pos_above_title_format(), OP_AD_POS_ABOVE_TITLE_LABEL_VISIBLE, is_ad_pos_above_title_label_visible()); ?>
                  </li>
                  <li>
                    <?php
                    generate_checkbox_tag(OP_AD_POS_BELOW_TITLE_VISIBLE, is_ad_pos_below_title_visible(), __('タイトル下' ,THEME_NAME ));
                    generate_main_column_top_ad_tip_tag();
                    //詳細設定
                    generate_main_column_ad_detail_setting_forms(OP_AD_POS_BELOW_TITLE_FORMAT, get_ad_pos_below_title_format(), OP_AD_POS_BELOW_TITLE_LABEL_VISIBLE, is_ad_pos_below_title_label_visible()); ?>
                  </li>
                  <li>
                    <?php
                    generate_checkbox_tag(OP_AD_POS_CONTENT_TOP_VISIBLE, is_ad_pos_content_top_visible(), __('本文上' ,THEME_NAME ));
                    //詳細設定
                    generate_main_column_ad_detail_setting_forms(OP_AD_POS_CONTENT_TOP_FORMAT, get_ad_pos_content_top_format(), OP_AD_POS_CONTENT_TOP_LABEL_VISIBLE, is_ad_pos_content_top_label_visible()); ?>
                  </li>
                  <li>
                    <?php
                    generate_checkbox_tag(OP_AD_POS_CONTENT_MIDDLE_VISIBLE, is_ad_pos_content_middle_visible(), __('本文中' ,THEME_NAME ));
                    //詳細設定
                    generate_main_column_ad_detail_setting_forms(OP_AD_POS_CONTENT_MIDDLE_FORMAT, get_ad_pos_content_middle_format(), OP_AD_POS_CONTENT_MIDDLE_LABEL_VISIBLE, is_ad_pos_content_middle_label_visible(), OP_AD_POS_ALL_CONTENT_MIDDLE_VISIBLE, is_ad_pos_all_content_middle_visible()); ?>
                  </li>
                  <li>
                    <?php
                    generate_checkbox_tag(OP_AD_POS_CONTENT_BOTTOM_VISIBLE, is_ad_pos_content_bottom_visible(), __('本文下' ,THEME_NAME ));
                    //詳細設定
                    generate_main_column_ad_detail_setting_forms(OP_AD_POS_CONTENT_BOTTOM_FORMAT, get_ad_pos_content_bottom_format(), OP_AD_POS_CONTENT_BOTTOM_LABEL_VISIBLE, is_ad_pos_content_bottom_label_visible()); ?>
                  </li>
                  <li>
                    <?php
                    generate_checkbox_tag(OP_AD_POS_ABOVE_SNS_BUTTONS_VISIBLE, is_ad_pos_above_sns_buttons_visible(), __('SNSボタン上（本文下部分）' ,THEME_NAME ));
                    //詳細設定
                    generate_main_column_ad_detail_setting_forms(OP_AD_POS_ABOVE_SNS_BUTTONS_FORMAT, get_ad_pos_above_sns_buttons_format(), OP_AD_POS_ABOVE_SNS_BUTTONS_LABEL_VISIBLE, is_ad_pos_above_sns_buttons_label_visible()); ?>
                  </li>
                  <li>
                    <?php
                    generate_checkbox_tag(OP_AD_POS_BELOW_SNS_BUTTONS_VISIBLE, is_ad_pos_below_sns_buttons_visible(), __('SNSボタン下（本文下部分）' ,THEME_NAME ));
                    //詳細設定
                    generate_main_column_ad_detail_setting_forms(OP_AD_POS_BELOW_SNS_BUTTONS_FORMAT, get_ad_pos_below_sns_buttons_format(), OP_AD_POS_BELOW_SNS_BUTTONS_LABEL_VISIBLE, is_ad_pos_below_sns_buttons_label_visible()); ?>
                  </li>
                  <li>
                    <?php
                    generate_checkbox_tag(OP_AD_POS_BELOW_RELATED_POSTS_VISIBLE, is_ad_pos_below_related_posts_visible(), __('関連記事下（投稿ページのみ）' ,THEME_NAME ));
                    //詳細設定
                    generate_main_column_ad_detail_setting_forms(OP_AD_POS_BELOW_RELATED_POSTS_FORMAT, get_ad_pos_below_related_posts_format(), OP_AD_POS_BELOW_RELATED_POSTS_LABEL_VISIBLE, is_ad_pos_below_related_posts_label_visible()); ?>
                  </li>
                </ul>


              </div>
            </div>

            <p class="tips"><?php _e( 'それぞれのページで広告を表示する位置を設定します。', THEME_NAME ) ?></p>

            <p class="alert"><?php _e( '設定によっては、アドセンスポリシー違反になる可能性もあるので設定後は念入りに動作確認をしてください。', THEME_NAME ) ?></p>

          </td>
        </tr>

        <!-- 挿入型広告の利用 -->
        <tr<?php generate_not_allowed_form_class(!is_auto_adsens_only_enable()); ?>>
          <th scope="row">
            <?php generate_label_tag(OP_AD_SHORTCODE_ENABLE, __('挿入型広告', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_AD_SHORTCODE_ENABLE , is_ad_shortcode_enable(), __( '[ad]ショートコード・広告ブロックを有効にする', THEME_NAME ));
            //詳細設定
            generate_main_column_ad_detail_setting_forms(OP_AD_SHORTCODE_FORMAT, get_ad_shortcode_format(), OP_AD_SHORTCODE_LABEL_VISIBLE, is_ad_shortcode_label_visible());

            generate_tips_tag(__( '本文内に[ad]と入力したり「広告ブロック」を挿入した場合、その部分に「広告コード」に設定してある広告を表示します。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/ad-shortcode/'));
            ?>
            <p class="alert"><?php _e( '設定によっては、アドセンスポリシー違反になる可能性もあるので設定後は念入りに動作確認をしてください。', THEME_NAME ) ?></p>
          </td>
        </tr>

        <!-- ads.txt -->
        <tr>
          <th scope="row">
            <label for="<?php echo OP_AD_ADS_TXT_CONTENT; ?>"><?php _e( 'ads.txt編集', THEME_NAME ) ?></label>
          </th>
          <td>
            <?php
            //ads.txtの更新を有効化するか
            generate_checkbox_tag(OP_AD_ADS_TXT_ENABLE , is_ad_ads_txt_enable(), __( 'ads.txtの更新を有効にする', THEME_NAME ));
            generate_br_tag();
            generate_br_tag();

            //ads.txtの編集
            generate_textarea_tag(OP_AD_ADS_TXT_CONTENT, get_ad_ads_txt_content(), __( 'ads.txtの内容を入力', THEME_NAME )) ;
            generate_tips_tag(__( 'ads.txt（アズテキスト）とは、Webの広告枠の販売者を厳密に管理し、偽の広告枠が広告主に提供されるのを防ぐためのテキストファイルです。', THEME_NAME ).__( 'Adsenseのみなどを除く第三者（他社）広告との併用配信をする際は設置が必須です。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/ads-txt/'));
            ?>
            <p><a href="https://support.google.com/adsense/answer/12171612" class="help-page" target="_blank" rel="noopener"><span class="fa fa-question-circle" aria-hidden="true"></span> <?php _e( 'ads.txtガイド', THEME_NAME ) ?></a></p>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>


<!-- PR表記設定 -->
<div id="exclude-pr-label" class="postbox">
<h2 class="hndle"><?php _e( 'PR表記設定', THEME_NAME ) ?></h2>
<div class="inside">

  <p><?php _e( '消費者庁の景品表示法の指定告示（通称：ステマ規制）に対応するための「PR表記」に関する設定です。', THEME_NAME );
           echo get_help_page_tag('https://wp-cocoon.com/pr-label/') ?></p>

  <table class="form-table">
    <tbody>
      <!-- 自動挿入ページ -->
      <tr>
        <th scope="row">
          <?php generate_label_tag(OP_PR_LABEL_SINGLE_VISIBLE, __( '自動挿入ページ', THEME_NAME )); ?>
        </th>
        <td>
          <?php
            generate_checkbox_tag(OP_PR_LABEL_SINGLE_VISIBLE, is_pr_label_single_visible(), __( '全ての投稿ページ', THEME_NAME ));
            generate_tips_tag(__('全投稿ページで「自動挿入エリア」で設定した場所に「PR表記」を挿入します。', THEME_NAME));

            generate_checkbox_tag(OP_PR_LABEL_PAGE_VISIBLE, is_pr_label_page_visible(), __( '全ての固定ページ', THEME_NAME ));
            generate_tips_tag(__('全固定ページで「自動挿入エリア」で設定した場所に「PR表記」を挿入します。', THEME_NAME));

            generate_checkbox_tag(OP_PR_LABEL_CATEGORY_PAGE_VISIBLE, is_pr_label_category_page_visible(), __( '全てのカテゴリーページ', THEME_NAME ));
            generate_tips_tag(__('全カテゴリーページで「自動挿入エリア」で設定した場所に「PR表記」を挿入します。', THEME_NAME));

            generate_checkbox_tag(OP_PR_LABEL_TAG_PAGE_VISIBLE, is_pr_label_tag_page_visible(), __( '全てのタグページ', THEME_NAME ));
            generate_tips_tag(__('全タグページで「自動挿入エリア」で設定した場所に「PR表記」を挿入します。', THEME_NAME));
          ?>
        </td>
      </tr>

      <!-- 自動挿入エリア -->
      <tr>
        <th scope="row">
          <?php generate_label_tag(OP_PR_LABEL_SMALL_VISIBLE, __( '自動挿入エリア', THEME_NAME )); ?>
        </th>
        <td>
          <?php
            generate_checkbox_tag(OP_PR_LABEL_SMALL_VISIBLE, is_pr_label_small_visible(), __( 'メインカラム左上', THEME_NAME ).__( '（小）', THEME_NAME ));
            generate_tips_tag(__('メインカラムの左上に「PR表記」を表示します。', THEME_NAME).get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/pr-small.png'));

            generate_checkbox_tag(OP_PR_LABEL_LARGE_VISIBLE, is_pr_label_large_visible(), __( '本文の上', THEME_NAME ).__( '（大）', THEME_NAME ));
            generate_tips_tag(__('記事本文の上部に「PR表記」を挿入します。', THEME_NAME).get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/pr-large.png'));
          ?>
        </td>
      </tr>

      <!-- 表示テキスト -->
      <tr>
        <th scope="row">
          <?php generate_label_tag(OP_PR_LABEL_SMALL_CAPTION, __( '表示テキスト', THEME_NAME )); ?>
        </th>
        <td>
          <?php
            //テキスト（小
            generate_label_tag(OP_PR_LABEL_SMALL_CAPTION, __('テキスト（小）', THEME_NAME) );
            echo '<br>';
            generate_textbox_tag(OP_PR_LABEL_SMALL_CAPTION, get_pr_label_small_caption(), __( PR_LABEL_SMALL_CAPTION, THEME_NAME ));
            generate_tips_tag(__( 'メインカラム左上に表示される「PR表記」の文言を入力してください。', THEME_NAME ).get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/pr-small.png'));

            //テキスト（大）
            generate_label_tag(OP_PR_LABEL_LARGE_CAPTION, __('テキスト（大）', THEME_NAME) );
            echo '<br>';
            generate_textbox_tag(OP_PR_LABEL_LARGE_CAPTION, get_pr_label_large_caption(), PR_LABEL_LARGE_CAPTION);
            generate_tips_tag(__( '記事本文上に表示される「PR表記」の文言を入力してください。', THEME_NAME ).get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/pr-large.png'));
          ?>
        </td>
      </tr>

      <!-- PR表記除外記事ID -->
      <tr>
        <th scope="row">
          <?php generate_label_tag(OP_PR_LABEL_EXCLUDE_POST_IDS, __( '除外記事ID', THEME_NAME )); ?>
        </th>
        <td>
          <?php
          generate_textbox_tag(OP_PR_LABEL_EXCLUDE_POST_IDS, get_pr_label_exclude_post_ids(), __( '例：111,222,3333', THEME_NAME ));
          generate_tips_tag(__( '「PR表記」を非表示にする投稿・固定ページのIDを,（カンマ）区切りで指定してください。', THEME_NAME ));
          ?>
        </td>
      </tr>

      <!-- PR表記除外カテゴリーID -->
      <tr>
        <th scope="row">
          <?php generate_label_tag(OP_PR_LABEL_EXCLUDE_CATEGORY_IDS, __( '除外カテゴリー', THEME_NAME )); ?>
        </th>
        <td>
          <?php
          generate_hierarchical_category_check_list( 0, OP_PR_LABEL_EXCLUDE_CATEGORY_IDS, get_pr_label_exclude_category_ids(), 300 );
          generate_tips_tag(__( '「PR表記」を非表示にするカテゴリーを選択してください。', THEME_NAME ).__( '除外したカテゴリーに属する投稿ページもまとめて非表示になります。', THEME_NAME ));
          ?>
        </td>
      </tr>


      <!-- PR表記除外タグID -->
      <tr>
        <th scope="row">
          <?php generate_label_tag(OP_PR_LABEL_EXCLUDE_TAG_IDS, __( '除外タグID', THEME_NAME )); ?>
        </th>
        <td>
          <?php
          generate_textbox_tag(OP_PR_LABEL_EXCLUDE_TAG_IDS, get_pr_label_exclude_tag_ids(), __( '例：111,222,3333', THEME_NAME ));
          generate_tips_tag(__( '「PR表記」を非表示にするタグページのIDを,（カンマ）区切りで指定してください。', THEME_NAME ).__( '除外したタグに属する投稿ページもまとめて非表示になります。', THEME_NAME ));
          ?>
        </td>
      </tr>
    </tbody>
  </table>
  </div>
</div>


<!-- バリューコマース -->
<div id="valuecommerce-ads" class="postbox">
  <h2 class="hndle"><?php _e( 'バリューコマース', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'バリューコマース関連の広告設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <!-- LinkSwitch -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_AD_LINKSWITCH_ENABLE, __( 'LinkSwitch', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_AD_LINKSWITCH_ENABLE, is_ad_linkswitch_enable(), __( 'LinkSwitchを有効にする', THEME_NAME ));
            generate_tips_tag(__('バリューコマースのLinkSwitch機能を有効にするか。LinkSwitch IDが入力されている必要があります。', THEME_NAME));


            generate_label_tag(OP_AD_LINKSWITCH_ID, __( 'LinkSwitch ID', THEME_NAME ));
            echo '<br>';
            generate_textbox_tag(OP_AD_LINKSWITCH_ID, get_ad_linkswitch_id(), __( 'LinkSwitch IDの入力', THEME_NAME ));
            echo get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/linkswtch-id.png', __( 'バリューコマースの「便利ツール」メニューの「LinkSwitch」からIDを取得してください。', THEME_NAME ));
            generate_tips_tag(__( 'LinkSwitchタグから取得できるIDを入力してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/linkswitch/'));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


<!-- 広告除外設定 -->
<div id="exclude-ads" class="postbox">
  <h2 class="hndle"><?php _e( '広告除外設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '広告を表示したくないページやカテゴリーの設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <!-- 広告除外記事ID -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_AD_EXCLUDE_POST_IDS, __( '広告除外記事ID', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_AD_EXCLUDE_POST_IDS, get_ad_exclude_post_ids(), __( '例：111,222,3333', THEME_NAME ));
            generate_tips_tag(__( '広告を非表示にする投稿・固定ページのIDを,（カンマ）区切りで指定してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 広告除外カテゴリーID -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_AD_EXCLUDE_CATEGORY_IDS, __( '広告除外カテゴリー', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_hierarchical_category_check_list( 0, OP_AD_EXCLUDE_CATEGORY_IDS, get_ad_exclude_category_ids(), 300 );
            //generate_textbox_tag(OP_AD_EXCLUDE_CATEGORY_IDS, get_ad_exclude_category_ids(), __( '例：111,222,3333', THEME_NAME ));
            generate_tips_tag(__( '広告を非表示にするカテゴリーを選択してください。', THEME_NAME ).__( '除外したカテゴリーに属する投稿ページもまとめて非表示になります。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>
