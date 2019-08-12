<?php //吹き出しデモ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div id="sb-list" class="postbox" style="max-width: 800px; margin-top: 20px;">
  <h2 class="hndle"><?php _e( 'デモ', THEME_NAME ) ?></h2>

  <div class="inside balloon-content">
    <div class="demo">
    <?php
      generate_speech_balloon_tag(
        $record,
        __( 'ここに入力したテキストが表示されます。', THEME_NAME )
      );
    ?>
    </div>

  </div>
  <?php if (is_icon_irasutoya($record)): ?>
  <div class="balloon-demo-credit">
    <?php _e( 'このアイコンは「いらすとや」さんの許可の下、当テーマのCDNサーバで配信中のデモです。アクセスが増えると、表示されなくなる可能性もあるので、自前で画像を用意するか、「<a href="http://www.irasutoya.com/" target="_blank" rel="noopener">いらすとや</a>」さんの豊富なイラストの中から好みのアイコンを探すなどして、自サーバーにアップして利用することをおすすめします。アクセス集中によりCDN上の画像が表示されなくなっても保証はできませんのでご了承ください。', THEME_NAME ) ?>
  </div>
  <?php endif ?>
</div>
