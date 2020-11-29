<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- モバイルボタン -->
<div id="mobile-buttons" class="postbox">
  <h2 class="hndle"><?php _e( 'モバイル設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'モバイル環境で表示するレイアウトの設定です。', THEME_NAME ) ?></p>

    <div class="col-2">
      <div style="width: 100%">

        <table class="form-table">
          <tbody>

            <!-- モバイルメニュー -->
            <tr>
              <th scope="row">
                <?php generate_label_tag(OP_MOBILE_BUTTON_LAYOUT_TYPE, __('モバイルメニュー', THEME_NAME) ); ?>
              </th>
              <td>
                <?php
                $options = array(
                  'none' => __( 'ボタンを表示しない（ミドルメニューのみ）', THEME_NAME ),
                  'top' => __( 'トップメニュー', THEME_NAME ),
                  'header_mobile_buttons' => __( 'ヘッダーモバイルボタン', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/mobile-header-buttons/'),
                  'footer_mobile_buttons' => __( 'フッターモバイルボタン', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/mobile-footer-menu/'),
                  'header_and_footer_mobile_buttons' => __( 'ヘッダー・フッターモバイルボタン', THEME_NAME ),
                  // 'slidein' => __( 'スライドインメニューボタン', THEME_NAME ),
                  // 'top_slidein' => __( 'トップボタン＆スライドインメニューボタン', THEME_NAME ),
                );
                generate_radiobox_tag(OP_MOBILE_BUTTON_LAYOUT_TYPE, $options, get_mobile_button_layout_type());
                generate_tips_tag(__( 'モバイルメニュー等を表示するための設定を行います。1024px未満で表示されます。※「トップボタン」はAMPページでは表示されません。', THEME_NAME ));
                ?>

              </td>
            </tr>



            <!-- モバイルボタン -->
            <tr <?php echo get_not_allowed_form_class(is_mobile_button_layout_type_mobile_buttons()); ?>>
              <th scope="row">
                <?php generate_label_tag('', __('モバイルボタン', THEME_NAME) ); ?>
              </th>
              <td>
                <?php

                generate_checkbox_tag(OP_FIXED_MOBILE_BUTTONS_ENABLE , is_fixed_mobile_buttons_enable(), __( 'モバイルボタンの固定表示', THEME_NAME ));
                generate_tips_tag(__( '「モバイルボタンレイアウト」で「モバイルボタン」が選択されているときボタンを固定表示するか。無効の場合はスクロールするとモバイルボタンが隠れます。', THEME_NAME ));

                generate_checkbox_tag(OP_MOBILE_HEADER_LOGO_VISIBLE , is_mobile_header_logo_visible(), __( 'サイトヘッダーロゴを表示する', THEME_NAME ));
                generate_tips_tag(__( 'モバイルでヘッダーロゴを表示するか。', THEME_NAME ));

                generate_checkbox_tag(OP_SLIDE_IN_CONTENT_BOTTOM_SIDEBAR_VISIBLE , is_slide_in_content_bottom_sidebar_visible(), __( 'モバイルボタン時コンテンツ下のサイドバーを表示', THEME_NAME ));
                generate_tips_tag(__( '「モバイルボタンレイアウト」で「モバイルボタン」が選択されているときメインカラム下に表示されるサイドバーを表示するかどうか。', THEME_NAME ));

                ?>
              </td>
            </tr>


          </tbody>
        </table>

      </div>

      <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_mobile', true)): ?>
      <div style="width: 380px">
          <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
          <div class="demo mobile-demo" style="width: 370px;">
            <iframe id="mobile-demo" class="iframe-demo" src="<?php echo home_url(); ?>" width="360" height="640"></iframe>
          </div>
      </div>
      <?php endif; ?>
    </div>

  </div>
</div>

</div><!-- /.metabox-holder -->
