<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- 内部ブログカード設定 -->
<div id="internal-blogcard" class="postbox">
  <h2 class="hndle"><?php _e( '内部ブログカード設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'URLやURLリンクをブログカード形式で表示するための設定です。内部ブログカードは、投稿・固定ページを表示するためのものです。', THEME_NAME );
    echo get_help_page_tag('https://wp-cocoon.com/how-to-use-internal-blogcard/'); ?></p>

    <table class="form-table">
      <tbody>
      <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_internal_blogcard', true)): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo">
            <?php
            $rand_post = get_random_posts(1);
            //_v($rand_post[0]);
            if ($rand_post) {
              if (is_internal_blogcard_enable()) {
                echo url_to_internal_blogcard_tag(get_the_permalink($rand_post->ID));
              } else {
                echo '<a href="'.get_the_permalink($rand_post->ID).'">'.get_the_permalink($rand_post->ID).'</a>';
              }
            }
            generate_tips_tag(__( 'ランダムで投稿を取得しています。', THEME_NAME ));

            ?>
            </div>
          </td>
        </tr>
        <?php endif; ?>

        <!--  ブログカード表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_INTERNAL_BLOGCARD_ENABLE, __( 'ブログカード表示', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_INTERNAL_BLOGCARD_ENABLE, is_internal_blogcard_enable(), __( 'ブログカード表示を有効にする', THEME_NAME ));
            generate_tips_tag(__( '本文中のURLやURLリンクをブログカード表示します。', THEME_NAME ));
            ?>
          </td>
        </tr>


        <!-- サムネイルスタイル  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_INTERNAL_BLOGCARD_THUMBNAIL_STYLE, __( 'サムネイルスタイル', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'left' => __( '左側', THEME_NAME ),
              'right' => __( '右側', THEME_NAME ),
            );
            generate_radiobox_tag(OP_INTERNAL_BLOGCARD_THUMBNAIL_STYLE, $options, get_internal_blogcard_thumbnail_style());
            generate_tips_tag(__( 'サムネイルの表示位置を選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 日付表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_INTERNAL_BLOGCARD_DATE_TYPE, __('日付表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'none' => __( 'なし', THEME_NAME ),
              'post_date' => __( '投稿日', THEME_NAME ),
              'up_date' => __( '更新日', THEME_NAME ),
            );
            generate_radiobox_tag(OP_INTERNAL_BLOGCARD_DATE_TYPE, $options, get_internal_blogcard_date_type());
            generate_tips_tag(__( 'ブログカードに表示する日付を設定します。更新日設定時、更新日がない場合は投稿日が表示されます。', THEME_NAME ));
            ?>
          </td>
        </tr>


        <!--  リンクの開き方 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_INTERNAL_BLOGCARD_TARGET_BLANK, __( 'リンクの開き方', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            //generate_alert_tag('この機能は「内部リンク設定」機能に統合されました。この項目は、数バージョン表示の後削除されます。');
            generate_checkbox_tag(OP_INTERNAL_BLOGCARD_TARGET_BLANK, is_internal_blogcard_target_blank(), __( '新しいタブで開く', THEME_NAME ));
            generate_tips_tag(__( 'ブログカードクリック時に新規タブを開きます。「内部リンク」が設定されている場合は、そちらが優先されます。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
