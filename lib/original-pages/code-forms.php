<div class="metabox-holder">

<!-- ソースコード設定 -->
<div id="code" class="postbox">
  <h2 class="hndle"><?php _e( 'ソースコード設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ソースコードのハイライト表示の設定です。ハイライト表示には、<a href="https://highlightjs.org/" target="_blank">highlight.js</a>を利用しています。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <?php $
            if (is_code_highlight_enable()): ?>
              <link rel="stylesheet" type="text/css" href="<?php echo get_highlight_js_css_url(); ?>">
            <?php endif ?>
            <div class="demo entry-content">
<pre id="highlight-demo">/* コメント */
#container {
  float: left;
  margin: 0 -240px 0 0;
  width: 100%;
}</pre>
            </div>
          </td>
        </tr>

        <!-- ソースコードのハイライト表示 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_CODE_HIGHLIGHT_ENABLE, __( 'ハイライト表示', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            genelate_checkbox_tag( OP_CODE_HIGHLIGHT_ENABLE, is_code_highlight_enable(), __( 'ソースコードをハイライト表示', THEME_NAME ));
            genelate_tips_tag(__( '本文中に表示されているpreタグ中のソースコードをハイライト表示します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ハイライトスタイル -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_CODE_HIGHLIGHT_STYLE, __( 'ハイライトスタイル', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            genelate_selectbox_tag(OP_CODE_HIGHLIGHT_STYLE, HIGHLIGHT_STYLES, get_code_highlight_style());
            genelate_tips_tag(__( 'ソースコードのハイライトテーマです。スタイルについては、<a href="https://highlightjs.org/static/demo/" target="_blank">highlight.js demo</a>を参照してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ハイライト表示するCSSセレクタ -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_CODE_HIGHLIGHT_CSS_SELECTOR, __( 'ハイライト表示するCSSセレクタ', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            genelate_textbox_tag( OP_CODE_HIGHLIGHT_CSS_SELECTOR, get_code_highlight_css_selector(), __( '.entry-content pre', THEME_NAME ));
            genelate_tips_tag(__( 'ソースコードをハイライトするCSSセレクターを細かく設定できます。よくわからない場合は変更しないでください。', THEME_NAME ).'<br>'.__( '※変更すると当設定ページのプレビュー機能は利用できなくなる可能性があります。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>



</div><!-- /.metabox-holder -->