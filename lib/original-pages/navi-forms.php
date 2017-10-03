<div class="metabox-holder">

<!-- グローバルナビ設定 -->
<div id="global-navi" class="postbox">
  <h2 class="hndle"><?php _e( 'グローバルナビ設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'グローバルナビの表示設定を行います。', THEME_NAME ) ?></p>
    <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
    <div class="demo">
      <?php get_template_part('tmp/header-container'); ?>
    </div>

    <table class="form-table">
      <tbody>

        <!-- メニュー色設定 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag('', __( 'メニュー色設定', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            genelate_color_picker_tag(OP_GLOBAL_NAVI_BACKGROUND_COLOR,  get_global_navi_background_color(), 'グローバルナビ色');
            genelate_tips_tag(__( 'グローバルナビ全体の背景色を選択します。', THEME_NAME ));

            genelate_color_picker_tag(OP_GLOBAL_NAVI_TEXT_COLOR,  get_global_navi_text_color(), '文字色');
            genelate_tips_tag(__( 'グローバルナビ全体のテキスト色を選択します。', THEME_NAME ));

            // genelate_color_picker_tag(OP_GLOBAL_NAVI_HOVER_BACKGROUND_COLOR,  get_global_navi_hover_background_color(), 'メニュー背景ホバー色');
            // genelate_tips_tag(__( 'グローバルナビメニューにマウスカーソルを置いたときの背景色を選択します。', THEME_NAME ));
            ?>
          </td>
        </tr>


        <!-- メニュー幅 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag('', __( 'メニュー幅', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            genelate_label_tag(OP_GLOBAL_NAVI_MENU_WIDTH, __( 'トップメニュー幅', THEME_NAME ) );
            genelate_number_tag(OP_GLOBAL_NAVI_MENU_WIDTH,  get_global_navi_menu_width(), 100, 300);
            genelate_tips_tag(__( 'グローバルナビのメニュー幅をpx数で指定します。', THEME_NAME ));

            genelate_label_tag(OP_GLOBAL_NAVI_SUB_MENU_WIDTH, __( 'サブメニュー幅', THEME_NAME ) );
            genelate_number_tag(OP_GLOBAL_NAVI_SUB_MENU_WIDTH,  get_global_navi_sub_menu_width(), 100, 500);
            genelate_tips_tag(__( 'グローバルナビのサブメニュー幅をpx数で指定します。', THEME_NAME ));

            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>


</div><!-- /.metabox-holder -->