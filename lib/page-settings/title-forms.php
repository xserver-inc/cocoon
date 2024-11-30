<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- フロントページタイトル設定 -->
<div id="title-front" class="postbox">
  <h2 class="hndle"><?php _e( 'フロントページ設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'フロントページの、タイトル、メタディスクリプション、メタキーワードの設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_title_front', true)): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo">
              <div class="search-result">
                <a href="<?php echo home_url(); ?>" class="title" target="_blank" rel="noopener"><?php echo get_front_page_title_caption(); ?></a>
                <div class="url"><?php echo home_url(); ?></div>
                <div class="description"><?php echo get_front_page_meta_description(); ?></div>
              </div>
            </div>
            <?php generate_tips_tag(__( 'プレビューはあくまで目安です。表示は検索エンジンによって変更される可能性があります。', THEME_NAME )) ?>
          </td>
        </tr>
        <?php endif; ?>

        <!-- フロントページタイトル  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_FRONT_PAGE_TITLE_FORMAT, __( 'フロントページタイトル', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'sitename' => __( 'サイト名', THEME_NAME ),
              'sitename_tagline' => __( 'サイト名', THEME_NAME ).' '.get_title_separator_caption().' '.__( 'キャッチフレーズ', THEME_NAME ),
              'free' => __( '自由形式', THEME_NAME ),
            );
            generate_radiobox_tag(OP_FRONT_PAGE_TITLE_FORMAT, $options, get_front_page_title_format());
            _e( '自由形式タイトル', THEME_NAME );
            echo '<br>';
            generate_textbox_tag(OP_FREE_FRONT_PAGE_TITLE, get_free_front_page_title(), __( '自由形式のタイトルを入力してください', THEME_NAME ));
            generate_tips_tag(__( 'フロントページで出力するタイトルタグのフォーマットを選択してください。自由に設定する場合は「自由形式タイトル」ボックスに入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!--  メタディスクリプション -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_FRONT_PAGE_META_DESCRIPTION, __( 'サイトの説明', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_META_DESCRIPTION_TO_FRONT_PAGE, is_meta_description_to_front_page(), __( 'メタディスクリプションタグを出力する', THEME_NAME ));
            generate_tips_tag(__( 'フロントページのheadタグ内に、メタディスクリプションタグを出力するか。', THEME_NAME ));
            ?>
            <?php
            generate_textbox_tag(OP_FRONT_PAGE_META_DESCRIPTION, get_front_page_meta_description(), __( 'メタディスクリプションを入力', THEME_NAME ));
            generate_tips_tag(__( 'フロントページで出力するメタディスクリプションタグの内容を入力してください。', THEME_NAME ).__('入力しない場合は、キャッチフレーズが表示されます。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!--  メタキーワード -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_FRONT_PAGE_META_KEYWORDS, __( 'メタキーワード', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_META_KEYWORDS_TO_FRONT_PAGE, is_meta_keywords_to_front_page(), __( 'メタキーワードタグを出力する', THEME_NAME ));
            generate_tips_tag(__( 'フロントページのheadタグ内に、メタキーワードタグを出力するか。', THEME_NAME ));
            ?>
            <?php
            generate_textbox_tag(OP_FRONT_PAGE_META_KEYWORDS, get_front_page_meta_keywords(), __( 'キーワード1,キーワード2,キーワード3,...', THEME_NAME ));
            generate_tips_tag(__( 'フロントページで出力するメタキーワードタグの内容を,（カンマ）区切りで入力してください。入力しない場合は、メタタグは出力されません。※SEO的にはほとんど意味のない設定だと思います。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


<!-- 投稿・固定ページタイトル設定 -->
<div id="title-singular" class="postbox">
  <h2 class="hndle"><?php _e( '投稿・固定ページ設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '投稿・固定ページの、タイトル、メタディスクリプション、メタキーワードの設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_title_singular', true)): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <?php
            $rand_post = get_random_posts(1);
            //var_dump($rand_post)
             ?>
            <?php if ($rand_post): ?>
              <div class="demo">
                <div class="search-result">
                  <a href="<?php the_permalink($rand_post->ID); ?>" class="title" target="_blank" rel="noopener"><?php echo get_singular_title_caption($rand_post); ?></a>
                  <div class="url"><?php the_permalink($rand_post->ID); ?></div>
                  <div class="description">
                    <?php
                    if (is_meta_description_to_singular()) {
                      echo 'SEO設定のメタディスクリプション';
                    } else {
                      echo  get_content_excerpt( $rand_post->post_content, 100 );

                    } ?>
                  </div>
                </div>
              </div>
            <?php endif ?>

            <?php
            generate_tips_tag(__( 'ランダムで投稿を取得しています。', THEME_NAME ));
            generate_tips_tag(__( 'プレビューはあくまで目安です。表示は検索エンジンによって変更される可能性があります。', THEME_NAME )); ?>
          </td>
        </tr>
        <?php endif; ?>

        <!-- 投稿・固定ページタイトル  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SINGULAR_PAGE_TITLE_FORMAT, __( '投稿・固定ページタイトル', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'pagetitle_only' => __( 'ページタイトル', THEME_NAME ),
              'pagetitle_sitename' => __( 'ページタイトル', THEME_NAME ).get_title_separator_caption().__( 'サイト名', THEME_NAME ),
              'sitename_pagetitle' => __( 'サイト名', THEME_NAME ).get_title_separator_caption().__( 'ページタイトル', THEME_NAME ),
            );
            generate_radiobox_tag(OP_SINGULAR_PAGE_TITLE_FORMAT, $options, get_singular_page_title_format());
            generate_tips_tag(__( '投稿・固定ページで出力するタイトルタグのフォーマットを選択してください。', THEME_NAME ));

            ?>
          </td>
        </tr>

        <!--  メタディスクリプション -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_META_DESCRIPTION_TO_SINGULAR, __( 'メタディスクリプション', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_META_DESCRIPTION_TO_SINGULAR, is_meta_description_to_singular(), __( 'メタディスクリプションタグを出力する', THEME_NAME ));
            generate_tips_tag(__( '投稿・固定ページのページのheadタグ内に、メタディスクリプションタグを出力するか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!--  メタキーワード -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_META_KEYWORDS_TO_SINGULAR, __( 'メタキーワード', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_META_KEYWORDS_TO_SINGULAR, is_meta_keywords_to_singular(), __( 'メタキーワードタグを出力する', THEME_NAME ));
            generate_tips_tag(__( '投稿・固定ページのページのheadタグ内に、メタキーワードタグを出力するか。※SEO的にはほとんど意味のない設定だと思います。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


<!-- カテゴリー・タグページタイトル設定 -->
<div id="title-category-tag" class="postbox">
  <h2 class="hndle"><?php _e( 'カテゴリー・タグページ設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'カテゴリー・タグページの、タイトル、メタディスクリプション、メタキーワードの設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_title_category', true)): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <?php
            //全カテゴリデータを取得
            $cat_all = get_terms( 'category', 'fields=all&get=all' );
            //全カテゴリー数から乱数の配列を生成
            $cat_list = range(0, count( $cat_all )-1);
            shuffle( $cat_list );
            $rand_category = isset($cat_list[0]) ? $cat_all[$cat_list[0]]: null;
            //var_dump($rand_category);
             ?>
            <?php if ($rand_category):

             ?>
              <div class="demo">
                <div class="search-result">
                  <a href="<?php echo get_category_link($rand_category->term_id); ?>" class="title" target="_blank" rel="noopener"><?php echo get_category_title_caption($rand_category); ?></a>
                  <div class="url"><?php echo get_category_link($rand_category->term_id); ?></div>
                  <div class="description">
                    <?php
                    if (is_meta_description_to_category()) {
                      echo get_category_meta_description($rand_category);
                    } ?>
                  </div>
                </div>
              </div>
            <?php endif ?>

            <?php
            generate_tips_tag(__( 'カテゴリーページのランダム表示です。', THEME_NAME ));
            generate_tips_tag(__( 'プレビューはあくまで目安です。表示は検索エンジンによって変更される可能性があります。', THEME_NAME )); ?>
          </td>
        </tr>
        <?php endif; ?>

        <!-- カテゴリーページタイトル  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_CATEGORY_PAGE_TITLE_FORMAT, __( 'ページタイトル', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'category_only' => __( 'ページタイトル', THEME_NAME ),
              'category_sitename' => __( 'ページタイトル', THEME_NAME ).get_title_separator_caption().__( 'サイト名', THEME_NAME ),
              'sitename_category' => __( 'サイト名', THEME_NAME ).get_title_separator_caption().__( 'ページタイトル', THEME_NAME ),
            );
            generate_radiobox_tag(OP_CATEGORY_PAGE_TITLE_FORMAT, $options, get_category_page_title_format());
            generate_tips_tag(__( 'カテゴリー・タグページで出力するタイトルタグのフォーマットを選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!--  メタディスクリプション -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_META_DESCRIPTION_TO_CATEGORY, __( 'メタディスクリプション', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_META_DESCRIPTION_TO_CATEGORY, is_meta_description_to_category(), __( 'メタディスクリプションタグを出力する', THEME_NAME ));
            generate_tips_tag(__( 'カテゴリー・タグページのページのheadタグ内に、メタディスクリプションタグを出力するか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!--  メタキーワード -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_META_KEYWORDS_TO_CATEGORY, __( 'メタキーワード', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_META_KEYWORDS_TO_CATEGORY, is_meta_keywords_to_category(), __( 'メタキーワードタグを出力する', THEME_NAME ));
            generate_tips_tag(__( 'カテゴリー・タグページのページのheadタグ内に、メタキーワードタグを出力するか。※SEO的にはほとんど意味のない設定だと思います。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

<!-- タイトル共通設定 -->
<div id="title-common" class="postbox">
  <h2 class="hndle"><?php _e( 'タイトル共通設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'タイトルで使用される区切り文字の設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- 簡略化したサイト名  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_TITLE_SEPARATOR, __( '簡略化したサイト名', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_label_tag(OP_SIMPLIFIED_SITE_NAME, __('短縮形のサイト名', THEME_NAME) );
            echo '<br>';
            generate_textbox_tag(OP_SIMPLIFIED_SITE_NAME, get_simplified_site_name(), '', 20);
            generate_tips_tag(__( 'サイト名が長すぎるので簡略化したサイト名をタイトルに含めたい場合は入力してください。入力しない場合は、通常のサイト名が表示されます。この短縮サイト名は投稿・固定・カテゴリー・タグページで適用されます', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- セパレーター  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_TITLE_SEPARATOR, __( 'セパレーター', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'pipe'   => __( ' | （パイプ）', THEME_NAME ),
              'hyphen' => __( ' - （ハイフン）', THEME_NAME ),
            );
            generate_radiobox_tag(OP_TITLE_SEPARATOR, $options, get_title_separator());
            generate_tips_tag(__( 'タイトルの区切りとなる文字を設定してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>
</div><!-- /.metabox-holder -->
