<div class="metabox-holder">

<!-- ソースコード設定 -->
<div id="code" class="postbox">
  <h2 class="hndle"><?php _e( 'ソースコード設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ソースコードのハイライト表示の設定です。ハイライト表示には、<a href="https://highlightjs.org/" target="_blank">highlight.js</a>を利用しています。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- ソースコードのハイライト表示 -->
        <tr>
          <th scope="row">
            <label for="<?php echo OP_SOURCE_CODE_HIGHLIGHT_ENABLE; ?>"><?php _e( 'ハイライト表示', THEME_NAME ) ?></label>
          </th>
          <td>
          <input type="checkbox" name="<?php echo OP_SOURCE_CODE_HIGHLIGHT_ENABLE; ?>" value="1"<?php the_checkbox_checked(is_source_code_highlight_enable()); ?>><?php _e("ソースコードをハイライト表示",THEME_NAME ); ?>
          <p class="tips"><?php _e( '本文中に表示されているpreタグ中のソースコードをハイライト表示します。', THEME_NAME ) ?></p>
          </td>
        </tr>

        <!-- ハイライトスタイル -->
        <tr>
          <th scope="row">
            <label for="<?php echo OP_SOURCE_CODE_HIGHLIGHT_STYLE; ?>"><?php _e( 'ハイライトスタイル', THEME_NAME ) ?></label>
          </th>
          <td>
          <?php genelate_selectbox_tag(OP_SOURCE_CODE_HIGHLIGHT_STYLE, HIGHLIGHT_STYLES, get_source_code_highlight_style()) ?>
          <p class="tips"><?php _e( 'ソースコードのハイライトテーマです。スタイルについては、<a href="https://highlightjs.org/static/demo/" target="_blank">highlight.js demo</a>を参照してください。。', THEME_NAME ) ?></p>
          </td>
        </tr>

        <!-- ハイライト表示するCSSセレクタ -->
        <tr>
          <th scope="row">
            <label for="<?php echo OP_SOURCE_CODE_HIGHLIGHT_CSS_SELECTOR; ?>"><?php _e( 'ハイライト表示するCSSセレクタ', THEME_NAME ) ?></label>
          </th>
          <td>
            <input type="text" name="<?php echo OP_SOURCE_CODE_HIGHLIGHT_CSS_SELECTOR; ?>" size="<?php echo DEFAULT_INPUT_COLS; ?>" value="<?php echo get_source_code_highlight_css_selector(); ?>" placeholder="<?php _e( '.entry-content pre', THEME_NAME ); ?>">
            <p class="tips"><?php _e( 'ソースコードをハイライトするCSSセレクターを細かく設定できます。よくわからない場合は変更しないでください。', THEME_NAME ) ?></p>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>



</div><!-- /.metabox-holder -->