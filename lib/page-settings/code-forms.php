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

    <p><?php _e( 'ソースコードのハイライト表示の設定です。ハイライト表示には、<a href="https://highlightjs.org/" target="_blank" rel="noopener">highlight.js</a>を利用しています。', THEME_NAME );
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
            <div class="demo entry-content<?php echo get_additional_entry_content_classes(); ?>">
<pre id="highlight-demo" style="line-height: 1.4em;">/* コメント */
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
            echo '<br>';
            ?>
            <div class="indent">
              <?php
              generate_checkbox_tag( OP_CODE_ROW_NUMBER_ENABLE, is_code_row_number_enable(), __( '行番号表示', THEME_NAME ));
              generate_tips_tag(__( 'ソースコード左側に行番号を表示します。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/max-code-row-count/'));
              ?>
            </div>
          </td>
        </tr>

        <!-- ライブラリ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_CODE_HIGHLIGHT_PACKAGE, __( 'ライブラリ', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            $options = array(
              'light' => __( '軽量版（よく利用されている言語のみ）', THEME_NAME ),
              'all' => __( '全て（対応している言語全て）', THEME_NAME ),
            );
            generate_radiobox_tag(OP_CODE_HIGHLIGHT_PACKAGE, $options, get_code_highlight_package());
            generate_tips_tag(__( 'ソースコードの対応言語を増やすには「全て」を選択してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/highlight-js-library/'));
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
            generate_tips_tag(__( 'ソースコードのハイライトテーマです。スタイルについては、<a href="https://highlightjs.org/static/demo/" target="_blank" rel="noopener">highlight.js demo</a>を参照してください。', THEME_NAME ));
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

<?php if (DEBUG_MODE): ?>
<!-- 数式設定 -->
<div id="formula" class="postbox">
  <h2 class="hndle"><?php _e( '数式設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'MathJaxというスクリプトを利用して、LaTeXで記述された数式をウェブブラウザで表示することが出来るようになります。', THEME_NAME ); ?></p>

    <table class="form-table">
      <tbody>

        <!-- 数式 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_FORMULA_ENABLE, __( '数式', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag( OP_FORMULA_ENABLE, is_formula_enable(), __( '数式表示を有効にする', THEME_NAME ));
            generate_tips_tag(__( 'LaTeXで記述された数式表現を有効にします。', THEME_NAME ));
            generate_tips_tag(__( sprintf('利用する場合は、投稿本文のどこでも良いのでに%sと入力してください。投稿・固定ページのみで利用できます。', MATH_SHORTCODE), THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>

<?php endif; ?>


</div><!-- /.metabox-holder -->
