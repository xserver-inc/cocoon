<div class="metabox-holder">

<!-- 本文画像設定 -->
<div id="content-image" class="postbox">
  <h2 class="hndle"><?php _e( '本文画像設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '投稿・固定ページの本文部分に関する画像の設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo">

            </div>
          </td>
        </tr>

        <!-- アイキャッチの自動設定  -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_EYECATCH_VISIBLE, __( 'アイキャッチの表示', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php

            genelate_checkbox_tag(OP_EYECATCH_VISIBLE, is_eyecatch_visible(), __( '本文上にアイキャッチを表示する', THEME_NAME ));
            genelate_tips_tag(__( '投稿・固定ページトップにアイキャッチを表示します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- アイキャッチの自動設定  -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_AUTO_POST_THUMBNAIL_ENABLE, __( 'アイキャッチの自動設定', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php

            genelate_checkbox_tag(OP_AUTO_POST_THUMBNAIL_ENABLE, is_auto_post_thumbnail_enable(), __( 'アイキャッチ自動設定を有効にする', THEME_NAME ));
            genelate_tips_tag(__( '記事を保存したり後悔したりするときに、本文中に最初に出てくる画像をアイキャッチにします。※プレビューには反映されません。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!--  画像の囲み効果 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_IMAGE_WRAP_EFFECT, __( '画像の囲み効果', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'none' => 'なし',
              'border' => 'ボーダー',
              'border_bold' => 'ボーダー',
              'shadow' => 'シャドー',
            );
            genelate_radiobox_tag(OP_IMAGE_WRAP_EFFECT, $options, get_image_wrap_effect());
            genelate_tips_tag(__( '画像の枠線の設定です。有効にすると白系の画像でも画像と認識しやすくなります。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!--  画像の拡大効果 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_IMAGE_ZOOM_EFFECT, __( '画像の拡大効果', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'none' => 'なし',
              'lightbox' => 'Lightbox',
              'lity' => 'Lity',
              'shadow' => 'シャドー',
            );
            genelate_radiobox_tag(OP_IMAGE_ZOOM_EFFECT, $options, get_image_zoom_effect());
            genelate_tips_tag(__( 'リンク画像をクリックしたときの拡大効果の設定です。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


</div><!-- /.metabox-holder -->