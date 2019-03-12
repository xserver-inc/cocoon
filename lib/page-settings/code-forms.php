<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- ソースコード設定 -->
<div id="code-highlight" class="postbox">
  <h2 class="hndle"><?php _e( 'ソースコード設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ソースコードのハイライト表示の設定です。ハイライト表示には、<a href="https://highlightjs.org/" target="_blank">highlight.js</a>を利用しています。', THEME_NAME );
    echo get_help_page_tag('https://wp-cocoon.com/highlight-js/'); ?></p>

    <table class="form-table">
      <tbody>

      <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_code', true)): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <?php
            if (is_code_highlight_enable()): ?>
              <link rel="stylesheet" href="<?php echo get_highlight_js_css_url(); ?>">
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
        <?php endif; ?>

        <!-- ソースコードのハイライト表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_CODE_HIGHLIGHT_ENABLE, __( 'ハイライト表示', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag( OP_CODE_HIGHLIGHT_ENABLE, is_code_highlight_enable(), __( 'ソースコードをハイライト表示', THEME_NAME ));
            generate_tips_tag(__( '本文中に表示されているpreタグ中のソースコードをハイライト表示します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ハイライトスタイル -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_CODE_HIGHLIGHT_STYLE, __( 'ハイライトスタイル', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            global $_HIGHLIGHT_STYLES;
            generate_selectbox_tag(OP_CODE_HIGHLIGHT_STYLE, $_HIGHLIGHT_STYLES, get_code_highlight_style());
            generate_tips_tag(__( 'ソースコードのハイライトテーマです。スタイルについては、<a href="https://highlightjs.org/static/demo/" target="_blank">highlight.js demo</a>を参照してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ハイライト表示するCSSセレクタ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_CODE_HIGHLIGHT_CSS_SELECTOR, __( 'ハイライト表示するCSSセレクタ', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag( OP_CODE_HIGHLIGHT_CSS_SELECTOR, get_code_highlight_css_selector(), __( '.entry-content pre', THEME_NAME ));
            generate_tips_tag(__( 'ソースコードをハイライトするCSSセレクターを細かく設定できます。よくわからない場合は変更しないでください。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>



</div><!-- /.metabox-holder -->
