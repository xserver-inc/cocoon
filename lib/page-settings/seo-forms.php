<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

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
            <?php generate_label_tag('', __( 'headタグに挿入', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php

            //canonicalタグ
            generate_checkbox_tag(OP_CANONICAL_TAG_ENABLE, is_canonical_tag_enable(), __( 'canonicalタグの追加', THEME_NAME ));
            generate_tips_tag(__( 'WordPressデフォルトでも投稿・固定ページには、canonicalタグは挿入されます。', THEME_NAME ).'<br>'.__( 'この機能を有効にするとトップページやカテゴリページ等にもcanonicalタグが挿入されます。', THEME_NAME ));

            //prev nextタグ
            generate_checkbox_tag(OP_PREV_NEXT_ENABLE, is_prev_next_enable(), __( '分割ページにrel="next"/"prev"タグの追加', THEME_NAME ));
            generate_tips_tag(__( '検索エンジンに続き物ページの順番を知らせます。無効にした場合はWordPressデフォルトのnext/prev設定になります。', THEME_NAME ));

            //カテゴリページをnoindexとする
            generate_checkbox_tag(OP_CATEGORY_PAGE_NOINDEX, is_category_page_noindex(), __( 'カテゴリページをnoindexとする', THEME_NAME ));
            generate_tips_tag(__( 'カテゴリページ全体をnoindex設定にします。', THEME_NAME ));

            echo '<div class="indent'.get_not_allowed_form_class(!is_category_page_noindex(), true).'">';
              //カテゴリページの2ページ目以降をnoindexとする
              generate_checkbox_tag(OP_PAGED_CATEGORY_PAGE_NOINDEX, is_paged_category_page_noindex(), __( 'カテゴリページの2ページ目以降をnoindexとする', THEME_NAME ));
              generate_tips_tag(__( 'カテゴリページのトップページ以外はnoindex設定にします。', THEME_NAME ));
            echo '</div>';


            //タグページをnoindexとする
            generate_checkbox_tag(OP_TAG_PAGE_NOINDEX, is_tag_page_noindex(), __( 'タグページをnoindexとする', THEME_NAME ));
            generate_tips_tag(__( 'タグのインデックスページをnoindex設定にします。', THEME_NAME ));


            echo '<div class="indent'.get_not_allowed_form_class(!is_tag_page_noindex(), true).'">';
              //タグページの2ページ目以降をnoindexとする
              generate_checkbox_tag(OP_PAGED_TAG_PAGE_NOINDEX, is_paged_tag_page_noindex(), __( 'タグページの2ページ目以降をnoindexとする', THEME_NAME ));
              generate_tips_tag(__( 'タグページのトップページ以外はnoindex設定にします。', THEME_NAME ));
            echo '</div>';

            //その他のアーカイブページをnoindexとする
            generate_checkbox_tag(OP_OTHER_ARCHIVE_PAGE_NOINDEX, is_other_archive_page_noindex(), __( 'その他のアーカイブページをnoindexとする', THEME_NAME ));
            generate_tips_tag(__( 'カテゴリ・タグ以外のアーカイブページをnoindex設定にします。', THEME_NAME ));

            //添付ファイルページをnoindexとする
            generate_checkbox_tag(OP_ATTACHMENT_PAGE_NOINDEX, is_attachment_page_noindex(), __( '添付ファイルページをnoindexとする', THEME_NAME ));
            generate_tips_tag(__( '画像や動画、ファイルなどの添付ページをnoindex設定にします。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 構造化データ  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( '構造化データ', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php

            //JSON-LD
            generate_checkbox_tag(OP_JSON_LD_TAG_ENABLE, is_json_ld_tag_enable(), __( 'JSON-LDを出力', THEME_NAME ));
            generate_tips_tag(__( '構造化データのJSON-LD情報をヘッダーに出力するかどうか。', THEME_NAME ));

            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

<!-- 日付の設定 -->
<div id="seo-head" class="postbox">
  <h2 class="hndle"><?php _e( '日付の設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <table class="form-table">
      <tbody>

        <!-- 表示する日付  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_CANONICAL_TAG_ENABLE, __( '表示する日付', THEME_NAME ) ); ?>
          </th>
          <td>

            <?php
            $options = array(
              'both_date' => __( '投稿日・更新日を表示', THEME_NAME ),
              // 'post_date' => __( '投稿日', THEME_NAME ),
              // 'update_date' => __( '更新日', THEME_NAME ),
              'post_date_only' => __( '投稿日のみ表示', THEME_NAME ),
              'update_date_only' => __( '更新日のみ表示', THEME_NAME ),
              'none' => __( '表示しない', THEME_NAME ),
            );
            generate_radiobox_tag(OP_SEO_DATE_TYPE, $options, get_seo_date_type());
            generate_tips_tag(__( '表示する日付形式を選択してください。表示する日付によって検索エンジンへの伝わり方が変わります。', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>


</div><!-- /.metabox-holder -->
