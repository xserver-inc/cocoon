<?php //オリジナル設定ページ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

// ユーザーが何か情報を POST したかどうかを確認
// POST していれば、隠しフィールドに 'Y' が設定されている
if( isset($_POST[HIDDEN_FIELD_NAME]) &&
    is_string($_POST[HIDDEN_FIELD_NAME]) &&
    wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[HIDDEN_FIELD_NAME])), 'backup') &&
    isset($_FILES['settings']) ){
  //var_dump($_POST[OP_RESET_ALL_SETTINGS]);

  ///////////////////////////////////////
  // 設定の保存
  ///////////////////////////////////////
  //バックアップ
  require_once abspath(__FILE__).'backup-posts.php';

//画面に「設定は保存されました」メッセージを表示
?>
<div class="notice <?php echo $restore_success ? 'notice-success' : 'notice-error'; ?> is-dismissible">
  <p>
    <strong>
      <?php $restore_success ? _e('設定をリストアしました。', THEME_NAME ) : _e('設定をリストアできませんでした。ファイル形式とサイズを確認してください。', THEME_NAME ); ?>
    </strong>
  </p>
</div>
<?php
}

///////////////////////////////////////
// 入力フォーム
///////////////////////////////////////
?>
<div class="wrap admin-settings">
<h1><?php _e( 'バックアップ', THEME_NAME ) ?></h1><br>
  <!-- バックアップ -->
  <div class="backup metabox-holder">
    <?php require_once abspath(__FILE__).'backup-forms.php'; ?>
  </div><!-- /.metabox-holder -->
</div>
