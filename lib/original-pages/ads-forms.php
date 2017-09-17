<!-- アドセンス設定 -->
<div id="ads" class="postbox">
  <h2 class="hndle"><?php _e( 'アドセンス設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <table class="form-table">
      <tbody>

        <!-- 広告の表示 -->
        <tr>
          <th scope="row">
            <label for="<?php echo OP_ALL_ADS_VISIBLE; ?>"><?php _e( '広告の表示', THEME_NAME ) ?></label>
          </th>
          <td>
            <input type="checkbox" name="<?php echo OP_ALL_ADS_VISIBLE; ?>" value="1"<?php the_checkbox_checked(is_all_ads_visible()); ?>><?php _e("全ての広告を表示する",THEME_NAME ); ?>
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


        <!-- 広告ラベル -->
        <tr>
          <th scope="row">
            <label for="<?php echo OP_AD_LABEL; ?>"><?php _e( '広告ラベル', THEME_NAME ) ?></label>
          </th>
          <td>
            <input type="text" name="<?php echo OP_AD_LABEL; ?>" size="<?php echo DEFAULT_INPUT_COLS; ?>" value="<?php echo get_ad_label(); ?>" placeholder="<?php _e( '「スポンサーリンク」か「広告」推奨', THEME_NAME ); ?>">
            <p class="tips"><?php _e( '広告上部ラベルに表示されるテキストの入力です。', THEME_NAME ) ?></p>
          </td>
        </tr>

        <!-- 広告の表示位置 -->
        <tr>
          <th scope="row">
            <label for="<?php echo OP_AD_POS_INDEX_TOP_VISIBLE; ?>"><?php _e( '広告の表示位置', THEME_NAME ) ?></label>
          </th>
          <td>

            <div class="col-2">

              <div>

                <!-- インデックスページ -->
                <p><strong><?php _e( 'インデックスページの表示位置', THEME_NAME ) ?></strong></p>
                <ul>
                  <li>
                    <input type="checkbox" name="<?php echo OP_AD_POS_INDEX_TOP_VISIBLE; ?>" value="1"<?php the_checkbox_checked(is_ad_pos_index_top_visible()); ?>><?php _e('トップ' ,THEME_NAME ); ?>
                    <?php //詳細設定
                    echo_main_column_ad_detail_setting_forms(OP_AD_POS_INDEX_TOP_FORMAT, get_ad_pos_index_top_format()); ?>
                  </li>
                  <li>
                    <input type="checkbox" name="<?php echo OP_AD_POS_INDEX_MIDDLE_VISIBLE; ?>" value="1"<?php the_checkbox_checked(is_ad_pos_index_middle_visible()); ?>><?php _e('ミドル' ,THEME_NAME ); ?>
                    <?php //詳細設定
                    echo_main_column_ad_detail_setting_forms(OP_AD_POS_INDEX_MIDDLE_FORMAT, get_ad_pos_index_middle_format()); ?>              </li>
                  <li>
                    <input type="checkbox" name="<?php echo OP_AD_POS_INDEX_BOTTOM_VISIBLE; ?>" value="1"<?php the_checkbox_checked(is_ad_pos_index_bottom_visible()); ?>><?php _e('ボトム' ,THEME_NAME ); ?>
                    <?php //詳細設定
                    echo_main_column_ad_detail_setting_forms(OP_AD_POS_INDEX_BOTTOM_FORMAT, get_ad_pos_index_bottom_format()); ?>

                  </li>
                </ul>

                <!-- サイドバー -->
                <p><strong><?php _e( 'サイドバーの表示位置', THEME_NAME ) ?></strong></p>
                <ul>
                  <li>
                    <input type="checkbox" name="<?php echo OP_AD_POS_SIDEBAR_TOP_VISIBLE; ?>" value="1"<?php the_checkbox_checked(is_ad_pos_sidebar_top_visible()); ?>><?php _e('サイドバートップ' ,THEME_NAME ); ?>
                  </li>
                  <li>
                    <input type="checkbox" name="<?php echo OP_AD_POS_SIDEBAR_BOTTOM_VISIBLE; ?>" value="1"<?php the_checkbox_checked(is_ad_pos_sidebar_bottom_visible()); ?>><?php _e('サイドバーボトム' ,THEME_NAME ); ?>
                  </li>
                </ul>

              </div>

              <!-- 投稿・固定ページ -->
              <div>
                <p><strong><?php _e( '投稿・固定ページの表示位置', THEME_NAME ) ?></strong></p>
                <ul>
                  <li>
                    <input type="checkbox" name="<?php echo OP_AD_POS_ABOVE_TITLE_VISIBLE; ?>" value="1"<?php the_checkbox_checked(is_ad_pos_above_title_visible()); ?>><?php _e('タイトル上' ,THEME_NAME ); ?>
                    <?php //詳細設定
                    echo_main_column_ad_detail_setting_forms(OP_AD_POS_ABOVE_TITLE_FORMAT, get_ad_pos_above_title_format()); ?>
                  </li>
                  <li>
                    <input type="checkbox" name="<?php echo OP_AD_POS_BELOW_TITLE_VISIBLE; ?>" value="1"<?php the_checkbox_checked(is_ad_pos_below_title_visible()); ?>><?php _e('タイトル下' ,THEME_NAME ); ?>
                    <?php //詳細設定
                    echo_main_column_ad_detail_setting_forms(OP_AD_POS_BELOW_TITLE_FORMAT, get_ad_pos_below_title_format()); ?>
                  </li>
                  <li>
                    <input type="checkbox" name="<?php echo OP_AD_POS_CONTENT_TOP_VISIBLE; ?>" value="1"<?php the_checkbox_checked(is_ad_pos_content_top_visible()); ?>><?php _e('本文上' ,THEME_NAME ); ?>
                    <?php //詳細設定
                    echo_main_column_ad_detail_setting_forms(OP_AD_POS_CONTENT_TOP_FORMAT, get_ad_pos_content_top_format()); ?>
                  </li>
                  <li>
                    <input type="checkbox" name="<?php echo OP_AD_POS_CONTENT_MIDDLE_VISIBLE; ?>" value="1"<?php the_checkbox_checked(is_ad_pos_content_middle_visible()); ?>><?php _e('本文中' ,THEME_NAME ); ?>
                    <?php //詳細設定
                    echo_main_column_ad_detail_setting_forms(OP_AD_POS_CONTENT_MIDDLE_FORMAT, get_ad_pos_content_middle_format(), OP_AD_POS_ALL_CONTENT_MIDDLE_VISIBLE, is_ad_pos_all_content_middle_visible()); ?>
                  </li>
                  <li>
                    <input type="checkbox" name="<?php echo OP_AD_POS_CONTENT_BOTTOM_VISIBLE; ?>" value="1"<?php the_checkbox_checked(is_ad_pos_content_bottom_visible()); ?>><?php _e('本文下' ,THEME_NAME ); ?>
                    <?php //詳細設定
                    echo_main_column_ad_detail_setting_forms(OP_AD_POS_CONTENT_BOTTOM_FORMAT, get_ad_pos_content_bottom_format()); ?>
                  </li>
                  <li>
                    <input type="checkbox" name="<?php echo OP_AD_POS_ABOVE_SNS_BUTTONS_VISIBLE; ?>" value="1"<?php the_checkbox_checked(is_ad_pos_above_sns_buttons_visible()); ?>><?php _e('SNSボタン上（本文下部分）' ,THEME_NAME ); ?>
                    <?php //詳細設定
                    echo_main_column_ad_detail_setting_forms(OP_AD_POS_ABOVE_SNS_BUTTONS_FORMAT, get_ad_pos_above_sns_buttons_format()); ?>
                  </li>
                  <li>
                    <input type="checkbox" name="<?php echo OP_AD_POS_BELOW_SNS_BUTTONS_VISIBLE; ?>" value="1"<?php the_checkbox_checked(is_ad_pos_below_sns_buttons_visible()); ?>><?php _e('SNSボタン下（本文下部分）' ,THEME_NAME ); ?>
                    <?php //詳細設定
                    echo_main_column_ad_detail_setting_forms(OP_AD_POS_BELOW_SNS_BUTTONS_FORMAT, get_ad_pos_below_sns_buttons_format()); ?>
                  </li>
                  <li>
                    <input type="checkbox" name="<?php echo OP_AD_POS_BELOW_RELATED_POSTS_VISIBLE; ?>" value="1"<?php the_checkbox_checked(is_ad_pos_below_related_posts_visible()); ?>><?php _e('関連記事下（投稿ページのみ）' ,THEME_NAME ); ?>
                    <?php //詳細設定
                    echo_main_column_ad_detail_setting_forms(OP_AD_POS_BELOW_RELATED_POSTS_FORMAT, get_ad_pos_below_related_posts_format()); ?>
                  </li>
                </ul>


              </div>
            </div>

            <p class="tips"><?php _e( 'それぞれのページで広告を表示する位置を設定します。', THEME_NAME ) ?></p>

            <p class="alert"><?php _e( '設定によっては、アドセンスポリシー違反になる可能性もあるので設定後は念入りに動作確認をしてください。', THEME_NAME ) ?></p>

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

    <table class="form-table">
      <tbody>
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

  </div>
</div>
