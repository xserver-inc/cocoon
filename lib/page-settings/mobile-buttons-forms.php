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

            <!-- モバイルボタンレイアウト -->
            <tr>
              <th scope="row">
                <?php generate_label_tag(OP_MOBILE_BUTTON_LAYOUT_TYPE, __('モバイルボタンレイアウト', THEME_NAME) ); ?>
              </th>
              <td>
                    <?php
                    $options = array(
                      'none' => __( 'ボタンを表示しない（ミドルメニューのみ）', THEME_NAME ),
                      'top' => __( 'トップボタン', THEME_NAME ),
                      'slide_in' => __( 'スライドインボタン', THEME_NAME ),
                      // 'slidein' => __( 'スライドインメニューボタン', THEME_NAME ),
                      // 'top_slidein' => __( 'トップボタン＆スライドインメニューボタン', THEME_NAME ),
                    );
                    generate_radiobox_tag(OP_MOBILE_BUTTON_LAYOUT_TYPE, $options, get_mobile_button_layout_type());
                    generate_tips_tag(__( 'モバイルメニュー等を表示するための設定を行います。768px以下で表示されます。※「トップボタン」はAMPページでは表示されません。', THEME_NAME ));

                    echo '<div'.get_not_allowed_form_class(is_mobile_button_layout_type_slide_in()).'>';
                    generate_checkbox_tag(OP_SLIDE_IN_CONTENT_BOTTOM_SIDEBAR_VISIBLE , is_slide_in_content_bottom_sidebar_visible(), __( 'スライドインボタン時コンテンツ下のサイドバーを表示', THEME_NAME ));
                    generate_tips_tag(__( '「モバイルボタンレイアウト」で「スライドインボタン」が表示されているときメインカラム下に表示されるサイドバーを表示するかどうか。', THEME_NAME ));
                    echo '<div>';
                    ?>

              </td>
            </tr>

            <!-- エントリーカードスニペット -->
<!--             <tr>
              <th scope="row">
                <?php //generate_label_tag(OP_SMARTPHONE_ENTRY_CARD_SNIPPET_VISIBLE, __('エントリーカードスニペット', THEME_NAME) ); ?>
              </th>
              <td>
                <?php
                // generate_checkbox_tag(OP_SMARTPHONE_ENTRY_CARD_SNIPPET_VISIBLE , is_smartphone_entry_card_snippet_visible(), __( 'スマホ端末でスニペットを表示', THEME_NAME ));
                // generate_tips_tag(__( 'スマホ環境でスニペット（抜粋）を表示するかどうか。スマホ向けの480px以下で適用されます。', THEME_NAME ));
                ?>
              </td>
            </tr> -->


          </tbody>
        </table>

      </div>
      <div style="width: 380px">
        <?php if(DEBUG_ADMIN_DEMO_ENABLE): ?>
          <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
          <div class="demo mobile-demo" style="width: 370px;">
            <iframe id="mobile-demo" class="iframe-demo" src="<?php echo home_url(); ?>" width="360" height="640"></iframe>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

</div><!-- /.metabox-holder -->
