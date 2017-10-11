<div class="metabox-holder">

<!-- 関連記事 -->
<div id="single-page" class="postbox">
  <h2 class="hndle"><?php _e( '関連記事設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '関連記事の表示に関する設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo" style="max-height: 300px;overflow: auto;">
              <?php get_template_part('tmp/related-entries') ?>
            </div>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->