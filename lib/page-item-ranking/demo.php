<?php //ランキングデモフォーム
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div id="sb-list" class="postbox" style="max-width: 800px; margin-top: 20px;">
  <h2 class="hndle"><?php _e( 'デモ', THEME_NAME ) ?></h2>
  <div class="inside sb-list">
    <div class="demo">
    <?php
      generate_speech_balloon_tag(
        $record,
        __( 'ここに入力したテキストが表示されます。', THEME_NAME )
      );
    ?>
    </div>
  </div>
</div>
