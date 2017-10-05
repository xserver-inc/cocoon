
        <!-- グローバルナビ宇メニュー色設定 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag('', __( 'グローバルナビメニュー', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            genelate_color_picker_tag(OP_GLOBAL_NAVI_BACKGROUND_COLOR,  get_global_navi_background_color(), 'グローバルナビ色');
            genelate_tips_tag(__( 'グローバルナビ全体の背景色を選択します。', THEME_NAME ));

            genelate_color_picker_tag(OP_GLOBAL_NAVI_TEXT_COLOR,  get_global_navi_text_color(), 'グローバルナビ文字色');
            genelate_tips_tag(__( 'グローバルナビ全体のテキスト色を選択します。', THEME_NAME ));

            // genelate_color_picker_tag(OP_GLOBAL_NAVI_HOVER_BACKGROUND_COLOR,  get_global_navi_hover_background_color(), 'メニュー背景ホバー色');
            // genelate_tips_tag(__( 'グローバルナビメニューにマウスカーソルを置いたときの背景色を選択します。', THEME_NAME ));
            ?>
          </td>
        </tr>


        <!-- メニュー幅 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag('', __( 'グローバルナビメニュー幅', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            genelate_label_tag(OP_GLOBAL_NAVI_MENU_WIDTH, __( 'トップメニュー幅', THEME_NAME ) );
            echo '<br>';
            genelate_number_tag(OP_GLOBAL_NAVI_MENU_WIDTH,  get_global_navi_menu_width(), 100, 300);
            genelate_tips_tag(__( 'グローバルナビのメニュー幅をpx数で指定します。未記入でデフォルト幅になります。', THEME_NAME ));

            genelate_label_tag(OP_GLOBAL_NAVI_SUB_MENU_WIDTH, __( 'サブメニュー幅', THEME_NAME ) );
            echo '<br>';
            genelate_number_tag(OP_GLOBAL_NAVI_SUB_MENU_WIDTH,  get_global_navi_sub_menu_width(), 100, 500);
            genelate_tips_tag(__( 'グローバルナビのサブメニュー幅をpx数で指定します。未記入でデフォルト幅になります。', THEME_NAME ));

            ?>
          </td>
        </tr>

        <!-- グローバルナビの固定  -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag('', __( 'グローバルナビの固定', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            genelate_checkbox_tag(OP_GLOBAL_NAVI_FIXED, is_global_navi_fixed(), __( 'グローバルナビメニューを固定する', THEME_NAME ));
            genelate_tips_tag(__( 'ページをスクロールしても、メニューが追従してきます。', THEME_NAME ));

            ?>
          </td>
        </tr>