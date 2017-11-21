<div class="metabox-holder">

<!-- ブラウザキャッシュ -->
<div id="speed-up" class="postbox">
  <h2 class="hndle"><?php _e( 'ブラウザキャッシュ', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ブラウザキャッシュを設定します。ブラウザキャッシュを設定することで、次回からサーバーではなくローカルのリソースファイルが読み込まれることになるので高速化が図れます。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- ブラウザキャッシュ  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_BROWSER_CACHE_ENABLE, __( 'ブラウザキャッシュ', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_BROWSER_CACHE_ENABLE , is_browser_cache_enable(), __( 'ブラウザキャッシュの有効化', THEME_NAME ));
            generate_tips_tag(__( 'ブラウザキャッシュを有効化することで、訪問者が2回目以降リソースファイルをサーバーから読み込む時間を軽減できます。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->