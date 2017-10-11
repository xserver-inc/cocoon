<div class="metabox-holder">

<!-- フロントページタイトル設定 -->
<div id="title-front" class="postbox">
  <h2 class="hndle"><?php _e( 'フロントページ設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'フロントページの、タイトル、メタディスクリプション、メタキーワードの設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo">
              <?php get_template_part('tmp/related-entries') ?>
            </div>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->