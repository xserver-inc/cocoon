<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- ブラウザキャッシュ -->
<div id="speed-up" class="postbox">
  <h2 class="hndle"><?php _e( 'ブラウザキャッシュ', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ブラウザキャッシュを設定します。ブラウザキャッシュを設定することで、次回からサーバーではなくローカルのリソースファイルが読み込まれることになるので高速化が図れます。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- ブラウザキャッシュ  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_BROWSER_CACHE_ENABLE, __( 'キャッシュの有効化', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_BROWSER_CACHE_ENABLE , is_browser_cache_enable(), __( 'キャッシュを有効にする', THEME_NAME ));
            generate_tips_tag(__( 'ブラウザキャッシュを有効化することで、訪問者が2回目以降リソースファイルをサーバーから読み込む時間を軽減できます。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

<!-- 縮小化 -->
<div id="minify" class="postbox">
  <h2 class="hndle"><?php _e( '縮小化', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'CSS、JavaScriptの縮小化を行うことにより転送サイズを減らし高速化を図ります。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

      <?php if (is_html_minify_enable()): ?>
        <!-- HTML縮小化  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_HTML_MINIFY_ENABLE, __( 'HTML縮小化', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_HTML_MINIFY_ENABLE , is_html_minify_enable(), __( 'HTMLを縮小化する', THEME_NAME ));
            generate_tips_tag(__( 'HTMLの余分な改行や余白を削除することによりソースコードのサイズを減らします。', THEME_NAME ));
            ?>

            <?php if (is_amp_enable()): ?>
            <!-- <div class="indent">
              <?php
              generate_checkbox_tag(OP_HTML_MINIFY_AMP_ENABLE , is_html_minify_amp_enable(), __( 'AMPページのHTMLも縮小化', THEME_NAME ));
              generate_tips_tag(__( 'AMPページのソースコードを縮小化します。', THEME_NAME ));
               ?>
            </div> -->
            <?php endif; ?>

          </td>
        </tr>
      <?php endif; ?>


        <!-- CSS縮小化  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_CSS_MINIFY_ENABLE, __( 'CSS縮小化', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_CSS_MINIFY_ENABLE , is_css_minify_enable(), __( 'CSSを縮小化する', THEME_NAME ));
            generate_tips_tag(__( 'CSSの余分な改行や余白を削除することによりソースコードのサイズを減らします。', THEME_NAME ));

            generate_textarea_tag(OP_CSS_MINIFY_EXCLUDE_LIST, get_css_minify_exclude_list(), __( '縮小化除外CSSファイルの文字列を入力', THEME_NAME ) , 3);
            generate_tips_tag(__( '縮小化しないCSSファイルのパス、もしくはパスの一部を改行で区切って入力してください。', THEME_NAME ));
            generate_tips_tag(__( 'プラグインCSSを除外する例：/plugins/plugin-folder-name/', THEME_NAME ));

            ?>
          </td>
        </tr>

        <!-- JavaScript縮小化  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_JS_MINIFY_ENABLE, __( 'JavaScript縮小化', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_JS_MINIFY_ENABLE , is_js_minify_enable(), __( 'JavaScriptを縮小化する', THEME_NAME ));
            generate_tips_tag(__( 'JavaScript（jQuery）の余分な改行や余白を削除することによりソースコードのサイズを減らします。', THEME_NAME ));

            generate_textarea_tag(OP_JS_MINIFY_EXCLUDE_LIST, get_js_minify_exclude_list(), __( '縮小化除外JavaScriptファイルの文字列を入力', THEME_NAME ) , 3);
            generate_tips_tag(__( '縮小化しないJavaScriptファイルのパス、もしくはパスの一部を改行で区切って入力してください。', THEME_NAME ));
            generate_tips_tag(__( 'プラグインJavaScriptを除外する例：/plugins/plugin-folder-name/', THEME_NAME ));

            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


<!-- Lazy Load -->
<div id="lazy-load" class="postbox">
  <h2 class="hndle"><?php _e( 'Lazy Load設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'サイトの画像を遅延読み込み表示する設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- Lazy Load画像 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_LAZY_LOAD_ENABLE, __('遅延読み込み', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_LAZY_LOAD_ENABLE , is_lazy_load_enable(), __( 'Lazy Loadを有効にする', THEME_NAME ));
            generate_tips_tag(__( 'WordPress 5.5からLazy Loadが標準機能になりました。', THEME_NAME ).__('この機能を有効化すると標準Lazy Loadではカバーしていないエリアの画像の対応をします。', THEME_NAME ).__('例：新着・人気・カルーセル・おすすめカード・ナビカードのNO IMAGEサムネイル等', THEME_NAME ));


            generate_textarea_tag(OP_LAZY_LOAD_EXCLUDE_LIST, get_lazy_load_exclude_list(), __( '除外文字列を入力', THEME_NAME ) , 3);
            generate_tips_tag(__( 'Lazy Loadを行いたくない場合は、該当するimgタグに含まれている文字列を改行区切りで入力してください。', THEME_NAME ).__( 'ただしWordPress標準のLazy Loadの除外には対応していません。', THEME_NAME ));
            generate_tips_tag(__( '今後のWordPressの動向次第では、CocoonのLazy Load機能は廃止するかもしれません。', THEME_NAME ));
            ?>
          </td>
        </tr>


        <!-- Lazy Load Googleフォント -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_GOOGLE_FONT_LAZY_LOAD_ENABLE, __('Googleフォント', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_GOOGLE_FONT_LAZY_LOAD_ENABLE , is_google_font_lazy_load_enable(), __( 'Googleフォントの非同期読み込みを有効にする', THEME_NAME ));
            generate_tips_tag(__( 'サイズが大きくなりがちなGoogleフォントを非同期読み込みしてページ表示を高速化します。ただし、JavaScript（Web Font Loader）によりフォントが正常に読み込まれるまでは、多少タイムラグがあります。', THEME_NAME ));

            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>


<?php if(0): ?>
<!-- スクリプト読み込み -->
<div id="script-load" class="postbox">
  <h2 class="hndle"><?php _e( 'スクリプト読み込み設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'JavaScriptの読み込み方式を変更する設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>


        <!-- Lazy Load画像 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_FOOTER_JAVASCRIPT_ENABLE, __('スクリプト', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_FOOTER_JAVASCRIPT_ENABLE , is_footer_javascript_enable(), __( 'JavaScriptをフッターで読み込む', THEME_NAME ));
            generate_tips_tag(__( 'JavaScriptファイルのレンダリングブロックを避けるためにフッターでjsファイルを読み込みます。プラグインが正常に動作しない場合は、無効にしてください。ただしレンダリングブロックが発生するので、速さは犠牲になります。', THEME_NAME ));


            // generate_textarea_tag(OP_FOOTER_JAVASCRIPT_EXCLUDE_LIST, get_footer_javascript_exclude_list(), __( '除外文字列を入力', THEME_NAME ) , 3);
            // generate_tips_tag(__( '「JavaScriptのフッター読み込み」を行いたくない場合は、該当するスクリプトに含まれている文字列を改行区切りで入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>
<?php endif; ?>


<!-- 事前読み込み -->
<div id="pre-acquisition" class="postbox">
  <h2 class="hndle"><?php _e( '事前読み込み設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '画像やスクリプト等のリソースの事前読み込み設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- 事前読み込み -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_PRE_ACQUISITION_LIST, __('事前読み込み', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textarea_tag(OP_PRE_ACQUISITION_LIST, get_pre_acquisition_list(), __( '事前読込ドメインを入力', THEME_NAME ) , 6);
            generate_tips_tag(__( 'linkタグでdns-prefetchとpreconnectを用いて、外部リソース読み込みの高速化を行います。', THEME_NAME ).__( 'よくわからない場合は、デフォルトのままご利用ください。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>



</div><!-- /.metabox-holder -->
