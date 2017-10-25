<div class="metabox-holder">

<!-- トップへ戻るボタン -->
<div id="toc" class="postbox">
  <h2 class="hndle"><?php _e( 'トップへ戻るボタン設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ページトップにスクロール移動するかボタンの設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo go-to-top" style="">
              <?php get_template_part('tmp/button-go-to-top') ?>
            </div>
            <?php genelate_tips_tag(__( 'デモは動作しません。', THEME_NAME )); ?>
          </td>
        </tr>

        <!-- トップへ戻るボタンの表示 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_GO_TO_TOP_BUTTON_VISIBLE, __('トップへ戻るボタンの表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_checkbox_tag(OP_GO_TO_TOP_BUTTON_VISIBLE , is_go_to_top_button_visible(), __( 'トップへ戻るボタンを表示する', THEME_NAME ));
            genelate_tips_tag(__( 'トップへスクロール移動するボタンを表示するかどうか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ボタンのアイコンフォント -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_GO_TO_TOP_BUTTON_ICON_FONT, __('ボタンのアイコンフォント', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'fa-angle-double-up' => __( 'fa-angle-double-up', THEME_NAME ),
              'fa-angle-up' => __( 'fa-angle-up', THEME_NAME ),
              'fa-arrow-circle-up' => __( 'fa-arrow-circle-up', THEME_NAME ),
              'fa-arrow-up' => __( 'fa-arrow-up', THEME_NAME ),
              'fa-caret-up' => __( 'fa-caret-up', THEME_NAME ),
              'fa-caret-square-o-up' => __( 'fa-caret-square-o-up', THEME_NAME ),
              'fa-chevron-circle-up' => __( 'fa-chevron-circle-up', THEME_NAME ),
              'fa-chevron-up' => __( 'fa-chevron-up', THEME_NAME ),
              'fa-hand-o-up' => __( 'fa-hand-o-up', THEME_NAME ),
              'fa-long-arrow-up' => __( 'fa-long-arrow-up', THEME_NAME ),
              'fa-caret-square-o-up' => __( 'fa-caret-square-o-up', THEME_NAME ),
            );
            genelate_selectbox_tag(OP_GO_TO_TOP_BUTTON_ICON_FONT, $options, get_go_to_top_button_icon_font(), true);
            genelate_tips_tag(__( 'トップへ戻るボタンを示すアイコンフォントを選択します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 色 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_GO_TO_TOP_BACKGROUND_COLOR, __('ボタン色', THEME_NAM) ); ?>
          </th>
          <td>
            <?php
            genelate_color_picker_tag(OP_GO_TO_TOP_BACKGROUND_COLOR,  get_go_to_top_background_color(), '背景色');
            genelate_tips_tag(__( 'ボタンが背景色を設定します。', THEME_NAME ));

            genelate_color_picker_tag(OP_GO_TO_TOP_TEXT_COLOR,  get_go_to_top_text_color(), '文字色');
            genelate_tips_tag(__( 'ボタンの文字色を設定します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ボタン画像 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_GO_TO_TOP_BUTTON_IMAGE_URL, __('ボタン画像', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_upload_image_tag(OP_GO_TO_TOP_BUTTON_IMAGE_URL, get_go_to_top_button_image_url());
            genelate_tips_tag(__( 'トップへ戻るボタンのアイコンフォント代わりに表示する画像を選択します。こちらに画像を設定するとアイコンフォントボタンは表示されません。最大横幅は120pxになります。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>
<?php

    hierarchical_category_tree( 0 ); // the function call; 0 for all categories; or cat ID

function hierarchical_category_tree( $cat ) {
    // wpse-41548 // alchymyth // a hierarchical list of all categories //

  $next = get_categories('hide_empty=false&orderby=name&order=ASC&parent=' . $cat);

  if( $next ) :
    foreach( $next as $cat ) :
    echo '<ul><li><strong>' . $cat->name . '</strong>';
    echo ' / <a href="' . get_category_link( $cat->term_id ) . '" title="' . sprintf( __( "View all posts in %s" ), $cat->name ) . '" ' . '>View ( '. $cat->count . ' posts )</a>  ';
    echo ' / <a href="'. get_admin_url().'edit-tags.php?action=edit&taxonomy=category&tag_ID='.$cat->term_id.'&post_type=post" title="Edit Category">Edit</a>';
    hierarchical_category_tree( $cat->term_id );
    endforeach;
  endif;

  echo '</li></ul>'; echo "\n";
}
?>
</div><!-- /.metabox-holder -->