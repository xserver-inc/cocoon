<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<!-- トップシェアボタン設定 -->
<div id="sns-share-top" class="postbox">
  <h2 class="hndle"><?php _e( 'トップシェアボタン設定', THEME_NAME ) ?></h2>
  <div class="inside">
    <p><?php _e( 'トップシェアボタンの表示に関する設定です。', THEME_NAME ) ?></p>
    <table class="form-table">
      <tbody>

        <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_sns_share_top', true)): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo">
            <?php //テンプレートの読み込み
              if (is_sns_top_share_buttons_visible())
                get_template_part_with_option('tmp/sns-share-buttons', SS_TOP); ?>
            </div>
          </td>
        </tr>
        <?php endif; ?>

        <!-- トップシェアボタンの表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_TOP_SHARE_BUTTONS_VISIBLE, __( 'トップシェアボタンの表示', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag( OP_SNS_TOP_SHARE_BUTTONS_VISIBLE, is_sns_top_share_buttons_visible(), __( 'メインカラムトップシェアボタンを表示する', THEME_NAME ));
            generate_tips_tag(__( 'トップシェアボタンの表示を切り替えます。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 表示切替 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( '表示切替', THEME_NAME )); ?>
          </th>
          <td>
            <p><?php _e( '個々のシェアボタンの表示切り替え。', THEME_NAME ) ?></p>
            <ul>
              <li>
                <?php generate_checkbox_tag(OP_TOP_TWITTER_SHARE_BUTTON_VISIBLE, is_top_twitter_share_button_visible(), __( 'X（旧Twitter）', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_TOP_MASTODON_SHARE_BUTTON_VISIBLE, is_top_mastodon_share_button_visible(), __( 'Mastodon', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_TOP_BLUESKY_SHARE_BUTTON_VISIBLE, is_top_bluesky_share_button_visible(), __( 'Bluesky', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_TOP_MISSKEY_SHARE_BUTTON_VISIBLE, is_top_misskey_share_button_visible(), __( 'Misskey', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_TOP_FACEBOOK_SHARE_BUTTON_VISIBLE, is_top_facebook_share_button_visible(), __( 'Facebook', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_TOP_HATEBU_SHARE_BUTTON_VISIBLE, is_top_hatebu_share_button_visible(), __( 'はてなブックマーク', THEME_NAME )); ?>
              </li>
              <!-- <li>
                <?php generate_checkbox_tag(OP_TOP_GOOGLE_PLUS_SHARE_BUTTON_VISIBLE, is_top_google_plus_share_button_visible(), __( 'Google', THEME_NAME )); ?>
              </li> -->
              <li>
                <?php generate_checkbox_tag(OP_TOP_POCKET_SHARE_BUTTON_VISIBLE, is_top_pocket_share_button_visible(), __( 'Pocket', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_TOP_LINE_AT_SHARE_BUTTON_VISIBLE, is_top_line_at_share_button_visible(), __( 'LINE@', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_TOP_PINTEREST_SHARE_BUTTON_VISIBLE, is_top_pinterest_share_button_visible(), __( 'Pinterest', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_TOP_LINKEDIN_SHARE_BUTTON_VISIBLE, is_top_linkedin_share_button_visible(), __( 'LinkedIn', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_TOP_COPY_SHARE_BUTTON_VISIBLE,  is_top_copy_share_button_visible(), __( 'タイトルとURLをコピー', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_TOP_COMMENT_SHARE_BUTTON_VISIBLE,  is_top_comment_share_button_visible(), __( 'コメント', THEME_NAME )); ?>
              </li>
            </ul>
            <p><?php _e( '表示するシェアボタンを選択してください。', THEME_NAME ) ?></p>
          </td>
        </tr>


        <!-- 表示ページ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( '表示ページ', THEME_NAME )); ?>
          </th>
          <td>
            <p><?php _e( 'シェアボタンを表示するページの切り替え。', THEME_NAME ) ?></p>
            <ul>
              <li>
                <?php generate_checkbox_tag(OP_SNS_FRONT_PAGE_TOP_SHARE_BUTTONS_VISIBLE, is_sns_front_page_top_share_buttons_visible(), __( 'フロントページ', THEME_NAME ).__( '（インデックスページ）', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_SNS_SINGLE_TOP_SHARE_BUTTONS_VISIBLE, is_sns_single_top_share_buttons_visible(), __( '投稿', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_SNS_PAGE_TOP_SHARE_BUTTONS_VISIBLE, is_sns_page_top_share_buttons_visible(), __( '固定ページ', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_SNS_CATEGORY_TOP_SHARE_BUTTONS_VISIBLE, is_sns_category_top_share_buttons_visible(), __( 'カテゴリー', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_SNS_TAG_TOP_SHARE_BUTTONS_VISIBLE, is_sns_tag_top_share_buttons_visible(), __( 'タグ', THEME_NAME )); ?>
              </li>
            </ul>
            <p><?php _e( 'シェアボタンを表示するページを選択してください。', THEME_NAME );echo get_help_page_tag('https://wp-cocoon.com/sns-button-display-switching/'); ?></p>
          </td>
        </tr>


        <!-- ボタンカラー -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_TOP_SHARE_BUTTON_COLOR, __( 'ボタンカラー', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            $options = array(
              'monochrome' => __( 'モノクロ', THEME_NAME ),
              'brand_color' => __( 'ブランドカラー', THEME_NAME ),
              'brand_color_white' => __( 'ブランドカラー（白抜き）', THEME_NAME ),
            );
            generate_selectbox_tag(OP_SNS_TOP_SHARE_BUTTON_COLOR, $options, get_sns_top_share_button_color());
            generate_tips_tag(__( 'トップシェアボタンの配色を選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>



        <!-- カラム数 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_TOP_SHARE_COLUMN_COUNT, __( 'カラム数', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            $options = array(
              '1' => __( '1列', THEME_NAME ),
              '2' => __( '2列', THEME_NAME ),
              '3' => __( '3列', THEME_NAME ),
              '4' => __( '4列', THEME_NAME ),
              '5' => __( '5列', THEME_NAME ),
              '6' => __( '6列', THEME_NAME ),
            );
            generate_selectbox_tag(OP_SNS_TOP_SHARE_COLUMN_COUNT, $options, get_sns_top_share_column_count());
            generate_tips_tag(__( 'トップシェアボタンのカラム数を選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>


        <!-- ロゴ・キャプション配置 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_TOP_SHARE_LOGO_CAPTION_POSITION, __( 'ロゴ・キャプション配置', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            $options = array(
              'left_and_right' => __( 'ロゴ・キャプション 左右', THEME_NAME ),
              'high_and_low_lc' => __('ロゴ・キャプション 上下' , THEME_NAME ),
              'high_and_low_cl' => __( 'キャプション・ロゴ 上下', THEME_NAME ),
            );
            generate_selectbox_tag(OP_SNS_TOP_SHARE_LOGO_CAPTION_POSITION, $options, get_sns_top_share_logo_caption_position());
            generate_tips_tag(__( 'トップシェアボタンのロゴとキャプションの配置を選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- シェア数の表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_TOP_SHARE_BUTTONS_COUNT_VISIBLE, __( 'シェア数の表示', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag( OP_SNS_TOP_SHARE_BUTTONS_COUNT_VISIBLE, is_sns_top_share_buttons_count_visible(), __( 'シェア数を表示', THEME_NAME ));
            generate_tips_tag(__( 'トップシェアボタンのシェア数表示を切り替えます。', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>
