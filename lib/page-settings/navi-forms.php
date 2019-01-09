<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

        <!-- グローバルナビ宇メニュー色設定 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( 'グローバルナビメニュー色', THEME_NAME ) ); ?>
            <?php generate_select_color_tip_tag(); ?>
          </th>
          <td>
            <?php
            generate_color_picker_tag(OP_GLOBAL_NAVI_BACKGROUND_COLOR,  get_global_navi_background_color(), 'グローバルナビ色');
            generate_tips_tag(__( 'グローバルナビ全体の背景色を選択します。', THEME_NAME ));

            generate_color_picker_tag(OP_GLOBAL_NAVI_TEXT_COLOR,  get_global_navi_text_color(), 'グローバルナビ文字色');
            generate_tips_tag(__( 'グローバルナビ全体のテキスト色を選択します。', THEME_NAME ));

            // generate_color_picker_tag(OP_GLOBAL_NAVI_HOVER_BACKGROUND_COLOR,  get_global_navi_hover_background_color(), 'メニュー背景ホバー色');
            // generate_tips_tag(__( 'グローバルナビメニューにマウスカーソルを置いたときの背景色を選択します。', THEME_NAME ));
            ?>
          </td>
        </tr>


        <!-- メニュー幅 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( 'グローバルナビメニュー幅', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_label_tag(OP_GLOBAL_NAVI_MENU_WIDTH, __( 'トップメニュー幅', THEME_NAME ) );
            echo '<br>';
            generate_number_tag(OP_GLOBAL_NAVI_MENU_WIDTH,  get_global_navi_menu_width(), 176, 100, 300);
            generate_tips_tag(__( 'グローバルナビのメニュー幅をpx数で指定します。未記入でデフォルト幅になります。', THEME_NAME ));

            generate_checkbox_tag(OP_GLOBAL_NAVI_MENU_TEXT_WIDTH_ENABLE , is_global_navi_menu_text_width_enable(), __( 'メニュー幅をテキストに合わせる', THEME_NAME ));
            generate_tips_tag(__( 'メニュー幅を均一にせずにテキスト幅で設定します。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/text-width-global-nav-items/'));


            echo '<br>';

            generate_label_tag(OP_GLOBAL_NAVI_SUB_MENU_WIDTH, __( 'サブメニュー幅', THEME_NAME ) );
            echo '<br>';
            generate_number_tag(OP_GLOBAL_NAVI_SUB_MENU_WIDTH,  get_global_navi_sub_menu_width(), 240, 100, 500);
            generate_tips_tag(__( 'グローバルナビのサブメニュー幅をpx数で指定します。未記入でデフォルト幅になります。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- グローバルナビの固定  -->
        <!--
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( 'グローバルナビの固定', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_GLOBAL_NAVI_FIXED, is_global_navi_fixed(), __( 'グローバルナビメニューを固定する', THEME_NAME ));
            generate_tips_tag(__( 'ページをスクロールしても、メニューが追従してきます。', THEME_NAME ));

            ?>
          </td>
        </tr>
        -->
