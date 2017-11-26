
<?php //オリジナル設定ページ

// ユーザーが何か情報を POST したかどうかを確認
// POST していれば、隠しフィールドに 'Y' が設定されている
if( isset($_POST[HIDDEN_FIELD_NAME]) &&
    $_POST[HIDDEN_FIELD_NAME] == 'Y' ){

  ///////////////////////////////////////
  // 設定の保存
  ///////////////////////////////////////
  //バックアップ
  require_once 'func-text-posts.php';

//画面に「設定は保存されました」メッセージを表示
?>
<div class="updated">
  <p>
    <strong>
      <?php _e('設定をレストアしました。', THEME_NAME ); ?>
    </strong>
  </p>
</div>
<?php
}

///////////////////////////////////////
// 入力フォーム
///////////////////////////////////////
?>
<div class="wrap">
<h1><?php _e( '使いまわしテキスト（関数テキスト）', THEME_NAME ) ?></h1>
  <form name="form1" method="post" action="" class="admin-settings">
    <!-- 使いまわしテキスト（関数テキスト） -->
    <div class="func-text metabox-holder">
      <?php require_once 'func-text-forms.php'; ?>
    </div><!-- /.metabox-holder -->
    <input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="Y">
  </form>
</div>