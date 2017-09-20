<div class="metabox-holder">

<!-- フロントページタイトル設定 -->
<div id="title" class="postbox">
  <h2 class="hndle"><?php _e( 'フロントページタイトル設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'タイトル、メタディスクリプション、メタキーワードの設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo">
              <div class="search-result">
                <a href="" class="title"></a>
                <div class="description"></div>
              </div>
            </div>
          </td>
        </tr>

        <!-- フロントページタイトルフォーマット  -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_FRONT_PAGE_TITLE_FORMA, __( 'フロントページのタイトル', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'sitename' => 'サイト名', 
              'sitename_tagline' => 'サイト名'.get_title_separator().'キャッチフレーズ', 
            );
            genelate_radiobox_tag(OP_FRONT_PAGE_TITLE_FORMAT, $options, get_front_page_title_format());
            genelate_tips_tag(__( 'フロントページで出力するタイトルタグのフォーマットを選択してください', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->