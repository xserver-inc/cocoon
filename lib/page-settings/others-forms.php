<div class="metabox-holder">

<!-- その他 -->
<div id="others" class="postbox">
  <h2 class="hndle"><?php _e( 'その他設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'その他の設定です。よくわからない場合は、変更しないことをおすすめします。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- 簡単SSL対応 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_EASY_SSL_ENABLE, __('簡単SSL対応', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_EASY_SSL_ENABLE , is_easy_ssl_enable(), __( '内部URLをSSL対応（簡易版）', THEME_NAME ));
            generate_tips_tag(__( 'サイトの内部リンクや、非SSLの画像・URLなど、HTTPS化する必要があるURLをSSL対応させて表示させます（※全てのURLに対応しているわけではありません）。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/easy-ssl/'));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->