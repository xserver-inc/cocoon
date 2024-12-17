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
    wp_verify_nonce($_POST[HIDDEN_FIELD_NAME], 'speed-up') ){
  //var_dump($_POST[OP_RESET_ALL_SETTINGS]);

  ///////////////////////////////////////
  // 設定の保存
  ///////////////////////////////////////
  //バックアップ
  require_once abspath(__FILE__).'speed-up-posts.php';

//画面に「設定は保存されました」メッセージを表示
?>
<div class="updated">
  <p>
    <strong>
      <?php _e('設定を変更しました。', THEME_NAME ); ?>
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
  <h1><?php _e( '高速化', THEME_NAME ) ?></h1>
  <form name="form1" method="post" action="" class="admin-settings">
    <p><?php _e( 'サイト表示スピードの高速化設定を行います。', THEME_NAME );
    echo get_help_page_tag('https://wp-cocoon.com/site-speed-up/'); ?></p>

    <!-- サイト高速化 -->
    <div class="backup metabox-holder">
      <?php require_once abspath(__FILE__).'speed-up-forms.php'; ?>
    </div><!-- /.metabox-holder -->

    <input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="<?php echo wp_create_nonce('speed-up');?>">

    <?php submit_button(__( '変更を保存', THEME_NAME )); ?>

  </form>

</div>
