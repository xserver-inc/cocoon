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
            <?php generate_label_tag('', __( '管理者向け設定', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php

            //アドミンバーに独自管理メニューを表示
            generate_checkbox_tag(OP_ADMIN_TOOL_MENU_VISIBLE, is_admin_tool_menu_visible(), __( 'アドミンバーに独自管理メニューを表示', THEME_NAME ));
            generate_tips_tag(__( '管理者バーに手軽に設定画面にアクセスできるメニューを表示します。', THEME_NAME ));

            //ページ公開前に確認アラートを出す
            generate_checkbox_tag(OP_CONFIRMATION_BEFORE_PUBLISH, is_confirmation_before_publish(), __( 'ページ公開前に確認アラートを出す', THEME_NAME ));
            generate_tips_tag(__( '記事を投稿する前に確認ダイアログを表示します。', THEME_NAME ));

            //タイトル等の文字数カウンター表示
            generate_checkbox_tag(OP_ADMIN_EDITOR_COUNTER_VISIBLE, is_admin_editor_counter_visible(), __( 'タイトル等の文字数カウンター表示', THEME_NAME ));
            generate_tips_tag(__( 'タイトルや、SEOタイトル、メタディスクリプションの文字数を表示します。', THEME_NAME ));

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
            <?php generate_label_tag(OP_ADMIN_PANEL_VISIBLE, __( '管理者パネルの表示', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_ADMIN_PANEL_VISIBLE, is_admin_panel_visible(), __( '管理者パネルを表示する', THEME_NAME ));
            generate_tips_tag(__( '投稿・固定ページ下部に管理者向けの情報をエリアを表示します。', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>



</div><!-- /.metabox-holder -->