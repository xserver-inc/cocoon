<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- 本文画像設定 -->
<div id="content-image" class="postbox">
  <h2 class="hndle"><?php _e( '本文画像設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '投稿・固定ページの本文部分に関する画像の設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_images', true)): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo">
              <?php $class = is_baguettebox_effect_enable() ? 'entry-demo' : null; ?>
              <div class="<?php echo $class; ?><?php echo get_additional_entry_content_classes(); ?>">
              <?php if (add_lightbox_property( '$content' )): ?>

              <?php endif ?>
              <?php if (is_eyecatch_visible()){
              $content = '<p><a href="https://simplicity.sample.mixh.jp/wp-content/uploads/2017/09/cup.jpeg" target="_blank">
                  <img src="https://simplicity.sample.mixh.jp/wp-content/uploads/2017/09/cup-370.jpg" alt="デモ画像">
                </a>&nbsp;
                <a href="https://simplicity.sample.mixh.jp/wp-content/uploads/2017/09/tree.jpg" target="_blank">
                  <img src="https://simplicity.sample.mixh.jp/wp-content/uploads/2017/09/tree300.jpg" alt="デモ画像">
                </a></p>
                ';
                if (is_lightbox_effect_enable()) {
                  $content = add_lightbox_property($content);
                }
                if (is_lity_effect_enable()) {
                  $content = add_lity_property($content);
                }
                echo $content;
              } ?>
              </div>
            </div>
          </td>
        </tr>
        <?php endif; ?>

        <!-- アイキャッチの自動設定  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_EYECATCH_VISIBLE, __( 'アイキャッチの表示', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_EYECATCH_VISIBLE, is_eyecatch_visible(), __( '本文上にアイキャッチを表示する', THEME_NAME ));
            generate_tips_tag(__( '投稿・固定ページトップにアイキャッチを表示します。', THEME_NAME ));
            ?>
            <div class="indent<?php echo get_not_allowed_form_class(is_eyecatch_visible(), true); ?>">
              <?php
              generate_checkbox_tag(OP_EYECATCH_LABEL_VISIBLE, is_eyecatch_label_visible(), __( 'アイキャッチラベルを表示する', THEME_NAME ));
              generate_tips_tag(__( '投稿・固定ページのアイキャッチに表示されるカテゴリラベルの表示切り替えです。', THEME_NAME ));

            generate_checkbox_tag(OP_EYECATCH_CENTER_ENABLE , is_eyecatch_center_enable(), __( 'アイキャッチの中央寄せ', THEME_NAME ));
            generate_tips_tag(__( '投稿・固定ページに表示されるアイキャッチをカラムの中央に表示します。', THEME_NAME ));

            generate_checkbox_tag(OP_EYECATCH_WIDTH_100_PERCENT_ENABLE , is_eyecatch_width_100_percent_enable(), __( 'アイキャッチをカラム幅に引き伸ばす', THEME_NAME ));
            generate_tips_tag(__( 'アイキャッチ画像に小さな画像を使っていても、強制的にカラム幅に拡大して表示します。', THEME_NAME ));

            generate_checkbox_tag(OP_EYECATCH_CAPTION_VISIBLE , is_eyecatch_caption_visible(), __( 'アイキャッチにキャプションがある場合は表示する', THEME_NAME ));
            generate_tips_tag(__( 'アイキャッチ画像にキャプションが設定してある場合、表示するかどうか。', THEME_NAME ));
              ?>
            </div>
          </td>
        </tr>

        <!-- アイキャッチの自動設定  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_AUTO_POST_THUMBNAIL_ENABLE, __( 'アイキャッチの自動設定', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php

            generate_checkbox_tag(OP_AUTO_POST_THUMBNAIL_ENABLE, is_auto_post_thumbnail_enable(), __( 'アイキャッチ自動設定を有効にする', THEME_NAME ));
            generate_tips_tag(__( '記事を保存したり公開したりするときに、本文中に最初に出てくる画像をアイキャッチにします。※プレビューには反映されません。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!--  画像の囲み効果 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_IMAGE_WRAP_EFFECT, __( '画像の囲み効果', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'none' => 'なし',
              'border' => 'ボーダー（薄い枠線）',
              'border_bold' => 'ボーダー（薄い太線）',
              'shadow' => 'シャドー（薄い影）',
              'shadow_paper' => 'シャドー（ペーパー）',
            );
            generate_radiobox_tag(OP_IMAGE_WRAP_EFFECT, $options, get_image_wrap_effect());
            generate_tips_tag(__( '画像の枠線の設定です。有効にすると白系の画像でも画像と認識しやすくなります。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!--  画像の拡大効果 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_IMAGE_ZOOM_EFFECT, __( '画像の拡大効果', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'none' => 'なし',
              'baguettebox' => __( 'baguetteBox（スマホ向け）', THEME_NAME ),
              'lity' => __( 'Lity（単機能・軽量）', THEME_NAME ),
              'lightbox' => __( 'Lightbox', THEME_NAME ),
            );
            generate_radiobox_tag(OP_IMAGE_ZOOM_EFFECT, $options, get_image_zoom_effect());
            generate_tips_tag(__( 'リンク画像をクリックしたときの拡大効果の設定です。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


<!-- 全体画像 -->
<div id="no-image-page" class="postbox">
  <h2 class="hndle"><?php _e( '全体画像設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '全てのページで共通して利用する画像の設定です。', THEME_NAME ) ?></p>
    <table class="form-table">
      <tbody>

        <!-- サムネイル画像 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_THUMBNAIL_IMAGE_TYPE, __('サムネイル画像', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'wide' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/wide.png', __( 'テレビ（地デジ）やYouTubeと同じ比率。', THEME_NAME ), THUMB320WIDTH).
                __( '9:16, 1:1.777..（地デジ・YouTube比）デフォルト', THEME_NAME ),

              'golden_ratio' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/golden.png', __( '人間が最も美しいと感じる比率とされています。', THEME_NAME ), THUMB320WIDTH).
                __( '約5:8, 1:1.618..（黄金比）', THEME_NAME ),

                'postcard' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/postcard.png', __( '一眼レフのアスペクト比。ハガキの縦横比に近いです。', THEME_NAME ), THUMB320WIDTH).
                  __( '2:3, 1:1.5（一眼レフ比）', THEME_NAME ),

              'silver_ratio' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/silver.png', __( '日本の木造建築に古くから使われている比率。大和比とも呼ばれています。', THEME_NAME ), THUMB320WIDTH).
                __( '約5:7, 1:1.414..（白銀比）', THEME_NAME ),

              'standard' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/standard.png', __( 'アナログテレビやデジタルカメラ、PowerPointのスライドと同じ比率。', THEME_NAME ), THUMB320WIDTH).
                __( '3:4, 1:1.333..（アナログテレビ・デジカメ比）', THEME_NAME ),

              'square' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/square.png', __( '単なる正方形。', THEME_NAME ), THUMB320WIDTH).
                __( '1:1（正方形）', THEME_NAME ),
            );
            generate_radiobox_tag(OP_THUMBNAIL_IMAGE_TYPE, $options, get_thumbnail_image_type());
            generate_tips_tag(__( 'インデックス等で使われるサムネイル画像の縦横比率を変更します。※「インデックスカードタイプ」の「大きなカード」と「タイルカード」には適用されません。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/thumbnail-aspect-ratio/'));
            ?>

          </td>
        </tr>

        <!-- Retinaディスプレイ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_RETINA_THUMBNAIL_ENABLE, __('Retinaディスプレイ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_RETINA_THUMBNAIL_ENABLE , is_retina_thumbnail_enable(), __( 'サムネイルをRetinaディスプレイ対応にする', THEME_NAME ));
            generate_tips_tag(__( 'サムネイルをRetinaディスプレイ対応端末で見ても綺麗に表示されるようにします。※「インデックスカードタイプ」の「大きなカード」には適用されません。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/retina-thumbnail/'));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

    <p><?php _e( 'これらの設定で変更されるサムネイル部分はこちら。', THEME_NAME ); ?></p>
    <ol>
      <li><?php echo get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/index.jpg', __( 'インデックス．アーカイブ、検索結果ページのエントリーカードのサムネイル。', THEME_NAME )).__('インデックスカード', THEME_NAME); ?></li>
      <li><?php echo get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/new.png', __( '新着記事ウィジェットもサムネイル。', THEME_NAME ), THUMB320WIDTH).__('新着記事', THEME_NAME); ?></li>
      <li><?php echo get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/popular.png', __( '人気記事ウィジェットのサムネイル。', THEME_NAME ), THUMB320WIDTH).__('人気記事', THEME_NAME); ?></li>
      <li><?php echo get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/related.png', __( '関連記事のエントリーカードのサムネイル。', THEME_NAME )).__('関連記事', THEME_NAME); ?></li>
      <li><?php echo get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/carousel.png', __( 'カルーセルカードのサムネイル。', THEME_NAME )).__('カルーセル', THEME_NAME); ?></li>
      <li><?php echo get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/page-navi.png', __( 'ページ送りナビのサムネイル。※デフォルトのみ）', THEME_NAME )).__('ページ送りナビ', THEME_NAME); ?></li>
      <li><?php echo get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/blogcard.png', __( '内部ブログカード、外部ブログカードのサムネイル。', THEME_NAME )).__('ブログカード', THEME_NAME); ?></li>
    </ol>
  </div>
</div>


<!-- NO IMAGE -->
<div id="no-image-page" class="postbox">
  <h2 class="hndle"><?php _e( 'NO IMAGE設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'アイキャッチの存在しない投稿のサムネイル画像設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- NO IMAGE画像 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_NO_IMAGE_URL, __('NO IMAGE画像', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_upload_image_tag(OP_NO_IMAGE_URL, get_no_image_url());
            generate_tips_tag(__( 'アイキャッチが存在しない投稿・固定ページのサムネイルに利用される画像ファイルを指定してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/no-image/'));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>



</div><!-- /.metabox-holder -->
