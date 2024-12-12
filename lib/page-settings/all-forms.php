<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- 全体設定 -->
<div id="all" class="postbox">
  <h2 class="hndle"><?php _e( '全体設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ページ全体の表示に関する設定です。', THEME_NAME ) ?></p>

    <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_all', true)): ?>
      <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
      <div class="demo iframe-standard-demo all-demo">
        <iframe id="all-demo" class="iframe-demo" src="<?php echo home_url(); ?>" width="1000" height="400" loading="lazy"></iframe>
      </div>
    <?php endif; ?>

    <table class="form-table">
      <tbody>

        <!-- キーカラー -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SITE_KEY_COLOR, __('キーカラー', THEME_NAME) ); ?>
            <?php generate_select_color_tip_tag(); ?>
          </th>
          <td>
            <?php
            generate_color_picker_tag(OP_SITE_KEY_COLOR,  get_site_key_color(), __( 'サイトキーカラー', THEME_NAME ));

            generate_tips_tag(__( 'サイト全体のポイントとなる部分に適用される背景色を指定します。', THEME_NAME ));

            generate_color_picker_tag(OP_SITE_KEY_TEXT_COLOR,  get_site_key_text_color(), __( 'サイトキーテキストカラー', THEME_NAME ));
            generate_tips_tag(__( 'サイト全体のポイントとなる部分に適用されるテキスト色を指定します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- サイトフォント  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SITE_FONT_FAMILY, __('サイトフォント', THEME_NAME) ); ?>
          </th>
          <td>
            <div class="col-2">
              <div style="min-width: 270px;">
                <?php
                //フォント
                $options = array(
                  'hiragino' => __( 'ヒラギノ角ゴ, メイリオ', THEME_NAME ).__( '（デフォルト）', THEME_NAME ),
                  'meiryo' => __( 'メイリオ, ヒラギノ角ゴ', THEME_NAME ),
                  'yu_gothic' => __( '游ゴシック体, ヒラギノ角ゴ', THEME_NAME ),
                  'ms_pgothic' => __( 'ＭＳ Ｐゴシック, ヒラギノ角ゴ', THEME_NAME ),
                  'noto_sans_jp' => __( 'Noto Sans JP（WEBフォント）', THEME_NAME ),
                  'noto_serif_jp' => __( 'Noto Serif JP（WEBフォント）', THEME_NAME ),
                  'mplus_1p' => __( 'Mplus 1p（WEBフォント）', THEME_NAME ),
                  'rounded_mplus_1c' => __( 'Rounded Mplus 1c（WEBフォント）', THEME_NAME ),
                  'kosugi' => __( '小杉ゴシック（WEBフォント）', THEME_NAME ),
                  'kosugi_maru' => __( '小杉丸ゴシック（WEBフォント）', THEME_NAME ),
                  // 'hannari' => __( 'はんなり明朝（WEBフォント）', THEME_NAME ),
                  // 'kokoro' => __( 'こころ明朝（WEBフォント）', THEME_NAME ),
                  'sawarabi_gothic' => __( 'さわらびゴシック（WEBフォント）', THEME_NAME ),
                  'sawarabi_mincho' => __( 'さわらび明朝（WEBフォント）', THEME_NAME ),
                  // '' => __( '指定なし', THEME_NAME ),
                );
                if (is_wp_language_korean()) {
                  $options['noto_sans_korean'] = __( 'Noto Sans Korean', THEME_NAME ).__( '（韓国語WEBフォント）', THEME_NAME );
                  $options['pretendard'] = __( 'Pretendard', THEME_NAME ).__( '（韓国語WEBフォント）', THEME_NAME );
                }
                $options[''] = __( '指定なし', THEME_NAME );
                generate_selectbox_tag(OP_SITE_FONT_FAMILY, $options, get_site_font_family(), __( 'フォント', THEME_NAME ));
                generate_tips_tag(__( 'サイト全体適用されるフォントを選択します。', THEME_NAME ));

                //文字サイズ
                $font_options = array(
                  '12px' => __( '12px', THEME_NAME ),
                  '13px' => __( '13px', THEME_NAME ),
                  '14px' => __( '14px', THEME_NAME ),
                  '15px' => __( '15px', THEME_NAME ),
                  '16px' => __( '16px', THEME_NAME ),
                  '17px' => __( '17px', THEME_NAME ),
                  '18px' => __( '18px', THEME_NAME ),
                  '19px' => __( '19px', THEME_NAME ),
                  '20px' => __( '20px', THEME_NAME ),
                  '21px' => __( '21px', THEME_NAME ),
                  '22px' => __( '22px', THEME_NAME ),
                );
                generate_selectbox_tag(OP_SITE_FONT_SIZE, $font_options, get_site_font_size(), __( '文字サイズ', THEME_NAME ));
                generate_tips_tag(__( 'サイト全体のフォントサイズを変更します。', THEME_NAME ));

                //文字色
                generate_color_picker_tag(OP_SITE_TEXT_COLOR,  get_site_text_color(), __( '文字色', THEME_NAME ));

                generate_tips_tag(__( 'サイト全体の文字色を変更します。', THEME_NAME ));
                ?>

              </div>
              <div style="width: auto">
                <?php cocoon_template_part('tmp/font-preview'); ?>
              </div>
            </div>
          </td>
        </tr>

        <!-- モバイルサイトフォント -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_MOBILE_SITE_FONT_SIZE, __('モバイルサイトフォント', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
              generate_selectbox_tag(OP_MOBILE_SITE_FONT_SIZE, $font_options, get_mobile_site_font_size());
              generate_tips_tag(__( 'モバイル端末でのフォントサイズを変更します（横幅が480px以下の端末）。', THEME_NAME ));
             ?>
          </td>
        </tr>

        <!-- 文字の太さ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SITE_FONT_WEIGHT, __('文字の太さ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
              generate_range_tag(OP_SITE_FONT_WEIGHT, get_site_font_weight(), 100, 900, 100);
              generate_tips_tag(__( 'font-weightで、フォントの太さを指定します。フォントの太さは「100（細い）～900（太い）」で指定できます。ただし、細かく太さを設定できないフォントもありますので実際の太さを確認しながら設定してください。', THEME_NAME ));
             ?>
          </td>
        </tr>


        <!-- サイトアイコフォント  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SITE_ICON_FONT, __('サイトアイコンフォント', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              SITE_ICON_FONT_DEFAULT => __( 'Font Awesome 4', THEME_NAME ),
              'font_awesome_5' => __( 'Font Awesome 5', THEME_NAME ),
            );
            generate_radiobox_tag(OP_SITE_ICON_FONT, $options, get_site_icon_font());
            generate_tips_tag(__('サイト全体で使用するアイコンフォントを選択します。', THEME_NAME).get_help_page_tag('https://wp-cocoon.com/site-iconfont/'));
            ?>
          </td>
        </tr>


        <!-- サイト背景色 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SITE_BACKGROUND_COLOR, __('サイト背景色', THEME_NAME) ); ?>
            <?php generate_select_color_tip_tag(); ?>
          </th>
          <td>
            <?php
            generate_color_picker_tag(OP_SITE_BACKGROUND_COLOR,  get_site_background_color(), __( '背景色', THEME_NAME ));
            generate_tips_tag(__( 'サイト全体の背景色を選択します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- サイト背景画像 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SITE_BACKGROUND_IMAGE_URL, __('サイト背景画像', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_upload_image_tag(OP_SITE_BACKGROUND_IMAGE_URL, get_site_background_image_url());
            generate_tips_tag(__( 'サイト全体の背景画像を選択します。より詳細に背景画像を設定するには、当設定を無効にして、「外観→カスタマイズ」の「背景画像」設定から行ってください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- サイト幅を揃える  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ALIGN_SITE_WIDTH, __('サイト幅の均一化', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_ALIGN_SITE_WIDTH, is_align_site_width(), __( 'サイト幅を揃える', THEME_NAME ));
            generate_tips_tag(__('サイト全体の幅をコンテンツ幅で統一します。', THEME_NAME));
            ?>

          </td>
        </tr>

        <!-- サイトリンク色 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SITE_LINK_COLOR, __('サイトリンク色', THEME_NAME) ); ?>
            <?php generate_select_color_tip_tag(); ?>
          </th>
          <td>
            <?php
            generate_color_picker_tag(OP_SITE_LINK_COLOR,  get_site_link_color(), __( 'リンク色', THEME_NAME ));
            generate_tips_tag(__( 'サイトで利用されるリンク色を選択します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- サイト選択文字色 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SITE_SELECTION_COLOR, __('サイト選択文字色', THEME_NAME) ); ?>
            <?php generate_select_color_tip_tag(); ?>
          </th>
          <td>
            <?php
            generate_color_picker_tag(OP_SITE_SELECTION_COLOR,  get_site_selection_color(), __( '選択文字色', THEME_NAME ));
            generate_tips_tag(__( 'サイト内のテキストを選択した際の文字色です。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- サイト選択背景色 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SITE_SELECTION_BACKGROUND_COLOR, __('サイト選択背景色', THEME_NAME) ); ?>
            <?php generate_select_color_tip_tag(); ?>
          </th>
          <td>
            <?php
            generate_color_picker_tag(OP_SITE_SELECTION_BACKGROUND_COLOR,  get_site_selection_background_color(), __( '選択文字背景色', THEME_NAME ));
            generate_tips_tag(__( 'サイト内のテキストを選択した際の背景色です。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- サイドバーの位置  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SIDEBAR_POSITION, __( 'サイドバーの位置', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'sidebar_right' => __( 'サイドバー右', THEME_NAME ),
              'sidebar_left' => __( 'サイドバー左', THEME_NAME ),
            );
            //アドミンバーに独自管理メニューを表示
            generate_radiobox_tag(OP_SIDEBAR_POSITION, $options, get_sidebar_position());
            generate_tips_tag(__( 'サイドバーの表示位置の設定です。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- サイドバーの表示状態  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SIDEBAR_DISPLAY_TYPE, __( 'サイドバーの表示状態', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'display_all' => __( '全てのページで表示する', THEME_NAME ),
              'no_display_all' => __( '全てのページで非表示にする', THEME_NAME ),
              'no_display_front_page' => __( 'フロントページで非表示にする', THEME_NAME ),
              'no_display_index_pages' => __( 'インデックスページで非表示にする', THEME_NAME ),
              'no_display_pages' => __( '固定ページで非表示にする', THEME_NAME ),
              'no_display_singles' => __( '投稿ページで非表示にする', THEME_NAME ),
              'no_display_404_pages' => __( '404ページで非表示にする', THEME_NAME ),
            );
            //アドミンバーに独自管理メニューを表示
            generate_radiobox_tag(OP_SIDEBAR_DISPLAY_TYPE, $options, get_sidebar_display_type());
            generate_tips_tag(__( 'サイドバーを表示するページの設定です。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- サイトアイコン -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __('ファビコン', THEME_NAME) ); ?>
          </th>
          <td>
            <p><?php _e( 'ファビコン（サイトアイコン）設定は、管理画面から「外観 → カスタマイズ → サイト基本情報」にある「サイトアイコン」設定から行ってください。設定する画像は512×512 pxのPNG画像を推奨します。', THEME_NAME );
            echo get_help_page_tag('https://wp-cocoon.com/site-icon/') ?></p>
          </td>
        </tr>

        <!-- サイトアイコン -->
        <!-- <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SITE_ICON_URL, __('サイトアイコン', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            //var_dump(get_site_icon_url());
            generate_upload_image_tag(OP_SITE_ICON_URL, get_site_icon_url2());
            generate_tips_tag(__( 'サイトアイコンはサイトのアプリとブラウザーのアイコンとして使用されます。アイコンは正方形で、幅・高さともに 512 ピクセル以上である必要があります。', THEME_NAME ));
            ?>
          </td>
        </tr> -->

        <!-- サムネイル表示  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ALL_THUMBNAIL_VISIBLE, __('サムネイル表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_ALL_THUMBNAIL_VISIBLE, is_all_thumbnail_visible(), __( 'サイト全体のサムネイルを表示する', THEME_NAME ));
            generate_tips_tag(__('サイト内のサムネイル画像の表示を切り替えます。文章メインのサイト用設定です。※ブログカードは何かしら画像が取得できるので表示します。', THEME_NAME));
            ?>

          </td>
        </tr>

        <!-- 日付フォーマット -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SITE_DATE_FORMAT, __('日付フォーマット', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_SITE_DATE_FORMAT, get_site_date_format(), SITE_DATE_FORMAT);
            generate_tips_tag(__( 'テーマが使用する日付のフォーマット形式を入力してください（初期値：Y.m.d）。', THEME_NAME ));
            ?>
            <p><?php _e( '<a href="https://ja.wordpress.org/support/article/formatting-date-and-time/">日付と時刻の書式の解説</a>', THEME_NAME ) ?></p>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>



</div><!-- /.metabox-holder -->
