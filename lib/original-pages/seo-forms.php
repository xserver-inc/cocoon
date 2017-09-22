<div class="metabox-holder">

<!-- headタグ設定 -->
<div id="seo-head" class="postbox">
  <h2 class="hndle"><?php _e( 'headタグ設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'headタグ内に追加するlinkタグの設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- headタグに挿入  -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_CANONICAL_TAG_ENABLE, __( 'headタグに挿入', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php

            //canonicalタグ
            genelate_checkbox_tag(OP_CANONICAL_TAG_ENABLE, is_canonical_tag_enable(), __( 'canonicalタグの追加', THEME_NAME ));
            genelate_tips_tag(__( 'Wordpressデフォルトでも投稿・固定ページには、canonicalタグは挿入されます。', THEME_NAME ).'<br>'.__( 'この機能を有効にするとトップページやカテゴリページ等にもcanonicalタグが挿入されます。', THEME_NAME ));

            //prev nextタグ
            genelate_checkbox_tag(OP_PREV_NEXT_ENABLE, is_prev_next_enable(), __( '分割ページにrel="next"/"prev"タグの追加', THEME_NAME ));
            genelate_tips_tag(__( '検索エンジンに続き物ページの順番を知らせます。', THEME_NAME ));

            //カテゴリページの2ページ目以降をnoindexとする
            genelate_checkbox_tag(OP_PAGED_CATEGORY_PAGE_NOINDEX, is_paged_category_page_noindex(), __( 'カテゴリページの2ページ目以降をnoindexとする', THEME_NAME ));
            genelate_tips_tag(__( 'カテゴリページのトップページ以外はnoindex設定にします。', THEME_NAME ));

            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>


</div><!-- /.metabox-holder -->