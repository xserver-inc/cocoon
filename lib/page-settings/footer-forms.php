<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- フッター設定 -->
<div id="footer-area" class="postbox">
  <h2 class="hndle"><?php _e( 'フッター設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'フッターやクレジット表示設定です。', THEME_NAME ) ?></p>

    <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_footer', true)): ?>
    <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
    <div id="footer" class="demo">
      <?php get_template_part('tmp/footer-bottom'); ?>
    </div>
    <?php endif; ?>

    <table class="form-table">
      <tbody>
        <!-- フッター色 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_FOOTER_BACKGROUND_COLOR, __('フッター色', THEME_NAME) ); ?>
            <?php generate_select_color_tip_tag(); ?>
          </th>
          <td>
            <?php
            generate_color_picker_tag(OP_FOOTER_BACKGROUND_COLOR,  get_footer_background_color(), 'フッター背景色');

            generate_tips_tag(__( 'サイト下部（フッター部分）の背景色を指定します。', THEME_NAME ));

            generate_color_picker_tag(OP_FOOTER_TEXT_COLOR,  get_footer_text_color(), 'フッター文字色');
            generate_tips_tag(__( 'サイト下部（フッター部分）のテキスト色を指定します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- フッター表示タイプ  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_FOOTER_DISPLAY_TYPE, __( 'フッター表示タイプ', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php

            $options = array(
              'logo_enable' => 'ロゴ＆メニュー＆クレジット',
              'left_and_right' => 'メニュー＆クレジット（左右）',
              'up_and_down' => 'メニュー＆クレジット（中央揃え）',
            );
            generate_radiobox_tag(OP_FOOTER_DISPLAY_TYPE, $options, get_footer_display_type())

            ?>
          </td>
        </tr>

        <!-- フッターロゴ  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_FOOTER_LOGO_URL, __( 'フッターロゴ', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_upload_image_tag(OP_FOOTER_LOGO_URL, get_footer_logo_url());
            generate_tips_tag(__( 'フッター部分に表示されるロゴ画像です。未入力だとヘッダーロゴが出力されます。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- クレジット表記  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( 'クレジット表記', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_label_tag(OP_SITE_INITIATION_YEAR, __( 'サイト開設年：', THEME_NAME ));
            generate_number_tag(OP_SITE_INITIATION_YEAR, get_site_initiation_year(), '', 1970, intval(date_i18n('Y')));
            echo '<br>';

            generate_label_tag(OP_COPYRIGHT_NAME, __( '著作権者表記：', THEME_NAME ));
            generate_textbox_tag(OP_COPYRIGHT_NAME, get_copyright_name(), '', 15);
            echo __( '※無記入だとサイト名', THEME_NAME );

            $options = array(
              'simple' => '© '.get_site_initiation_year().' '.get_copyright_display_name().'.',
              //'simple_year' => '© '.get_site_initiation_year().' '.get_copyright_display_name(),
              'simple_year_begin_to_now' => '© '.get_site_initiation_year().'-'.date_i18n('Y').' '.get_copyright_display_name().'.',
              'full' => 'Copyright © '.get_site_initiation_year().' '.get_copyright_display_name().' All Rights Reserved.',
              'full_year_begin_to_now' => 'Copyright © '.get_site_initiation_year().'-'.date_i18n('Y').' '.get_copyright_display_name().' All Rights Reserved.',
              'user_credit' => '独自表記',
            );
            generate_radiobox_tag(OP_CREDIT_NOTATION, $options, get_credit_notation());

            generate_label_tag(OP_USER_CREDIT_NOTATION, __( '上記設定で「独自表記」と入力した場合', THEME_NAME ));
            echo '<br>';
            generate_textarea_tag(OP_USER_CREDIT_NOTATION, get_user_credit_notation(), __( 'クレジット表記を入力してください。タグ入力も可能です。', THEME_NAME ), 4)

            ?>
          </td>
        </tr>


        <!-- メニュー幅 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_FOOTER_NAVI_MENU_WIDTH, __( 'フッターメニュー幅', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_FOOTER_NAVI_MENU_WIDTH,  get_footer_navi_menu_width(), 120, 70, 300);
            generate_tips_tag(__( 'フッターのメニュー幅をpx数で指定します。未記入でデフォルト幅になります。', THEME_NAME ));

            generate_checkbox_tag(OP_FOOTER_NAVI_MENU_TEXT_WIDTH_ENABLE , is_footer_navi_menu_text_width_enable(), __( 'メニュー幅をテキストに合わせる', THEME_NAME ));
            generate_tips_tag(__( 'メニュー幅を均一にせずにテキスト幅で設定します。', THEME_NAME ));

            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>



</div><!-- /.metabox-holder -->
