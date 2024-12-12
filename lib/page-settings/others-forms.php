<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- その他 -->
<div id="others" class="postbox">
  <h2 class="hndle"><?php _e( 'その他設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'その他の設定です。よくわからない場合は、変更しないことをおすすめします。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- 簡単SSL対応 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_EASY_SSL_ENABLE, __('簡単SSL対応', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_EASY_SSL_ENABLE , is_easy_ssl_enable(), __( '内部URLをSSL対応する（簡易版）', THEME_NAME ));
            generate_tips_tag(__( 'サイトの内部リンクや、非SSLの画像・URLなど、HTTPS化する必要があるURLをSSL対応させて表示させます（※全てのURLに対応しているわけではありません）。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/easy-ssl/'));
            ?>
          </td>
        </tr>

        <!-- ファイルシステム認証 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_REQUEST_FILESYSTEM_CREDENTIALS_ENABLE, __('ファイルシステム認証', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_REQUEST_FILESYSTEM_CREDENTIALS_ENABLE , is_request_filesystem_credentials_enable(), __( '認証を有効にする', THEME_NAME ));
            generate_tips_tag(__( 'KUSANAGI等のファイルシステム認証が必要なサーバの場合に有効にしてください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/kusanagi/'));
            ?>
          </td>
        </tr>

        <!-- Simplicity設定の移行 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_MIGRATE_FROM_SIMPLICITY, __('Simplicity設定', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_MIGRATE_FROM_SIMPLICITY , is_migrate_from_simplicity(), __( 'Simplicityから投稿設定を引き継ぐ', THEME_NAME ));
            generate_tips_tag(__( 'Simplicityから利用可能なPost meta情報を利用します。例えば投稿画面の「SEO設定」「広告除外」項目とか。※テーマカスタマイザーとかの設定は移行できません。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 日本語スラッグ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_AUTO_POST_SLUG_ENABLE, __('日本語スラッグ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_AUTO_POST_SLUG_ENABLE , is_auto_post_slug_enable(), __( '日本語スラッグを半角英数字にする', THEME_NAME ));
            generate_tips_tag(__( '日本語スラッグを投稿の場合は「post-XXXX」、固定ページの場合は「page-XXXX」のような短縮文字列に変更します。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

<?php if (false): ?>
<!-- JavaScriptライブラリ -->
<div id="others" class="postbox">
  <h2 class="hndle"><?php _e( 'JavaScriptライブラリ設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'JavaScriptライブラリのバージョンを設定できます。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- jQuery -->
        <tr>
          <th scope="row">
            <?php
            generate_label_tag(OP_JQUERY_VERSION, __('jQuery', THEME_NAME) );
            ?>
          </th>
          <td>
          <?php
            $options = array(
              '3' => __( 'jQuery Core 3.6.1', THEME_NAME ),
              '2' => __( 'jQuery Core 2.2.4', THEME_NAME ),
              '1' => __( 'jQuery Core 1.12.4', THEME_NAME ),
            );
            generate_radiobox_tag(OP_JQUERY_VERSION, $options, get_jquery_version());
            generate_tips_tag(__( 'jQueryのバージョン違いで動作しないプログラムがある場合は変更してください。', THEME_NAME ).__( 'jQuery 1、jQuery 2は古いバージョンなので脆弱性があります。できる限りjQuery 3をご利用ください。', THEME_NAME ).__( 'このjQuery選択機能は2022年12月末日をもって廃止いたします。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- jQuery Migrate -->
        <tr>
          <th scope="row">
            <?php
            generate_label_tag(OP_JQUERY_MIGRATE_VERSION, __('jQuery Migrate', THEME_NAME) );
            ?>
          </th>
          <td>
          <?php
            $options = array(
              '3' => __( 'jQuery Migrate 3.3.2', THEME_NAME ),
              '1' => __( 'jQuery Migrate 1.4.1', THEME_NAME ),
            );
            generate_radiobox_tag(OP_JQUERY_MIGRATE_VERSION, $options, get_jquery_migrate_version());
            generate_tips_tag(__( 'jQuery Migrateのバージョン違いで動作しないプログラムがある場合は変更してください。', THEME_NAME ).__( 'できる限りjQuery Migrate 3をご利用ください。', THEME_NAME ).__( 'このjQuery Migrate選択機能は2022年12月末日をもって廃止いたします。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>
<?php endif; ?>

</div><!-- /.metabox-holder -->
