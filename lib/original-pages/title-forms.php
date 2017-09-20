<div class="metabox-holder">

<!-- フロントページタイトル設定 -->
<div id="title" class="postbox">
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
              <div class="search-result">
                <a href="<?php echo home_url(); ?>" class="title" target="_blank"><?php echo get_front_page_title_caption(); ?></a>
                <div class="url"><?php echo home_url(); ?></div>
                <div class="description"><?php echo get_front_page_meta_description(); ?></div>
              </div>
            </div>
            <?php genelate_tips_tag(__( 'プレビューはあくまで目安です。表示は検索エンジンによって変更される可能性があります。', THEME_NAME )) ?>
          </td>
        </tr>

        <!-- フロントページタイトル  -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_FRONT_PAGE_TITLE_FORMA, __( 'フロントページタイトル', THEME_NAME ) ); ?>
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

        <!--  メタディスクリプション -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_FRONT_PAGE_META_DESCRIPTION, __( 'サイトの説明', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            genelate_textbox_tag(OP_FRONT_PAGE_META_DESCRIPTION, get_front_page_meta_description(), __( 'メタディスクリプションを入力', THEME_NAME ));
            genelate_tips_tag(__( 'フロントページで出力するメタディスクリプションタグの内容を入力してください。入力しない場合は、メタタグは出力されません。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!--  メタキーワード -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_FRONT_PAGE_META_KEYWORDS, __( 'メタキーワード', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            genelate_textbox_tag(OP_FRONT_PAGE_META_KEYWORDS, get_front_page_meta_keywords(), __( 'キーワード1,キーワード2,キーワード3,...', THEME_NAME ));
            genelate_tips_tag(__( 'フロントページで出力するメタキーワードタグの内容を,（カンマ）区切りで入力してください。入力しない場合は、メタタグは出力されません。※SEO的にはほとんど意味のない設定だと思います。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->