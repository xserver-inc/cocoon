
<?php //オリジナル設定ページ

// ユーザーが何か情報を POST したかどうかを確認
// POST していれば、隠しフィールドに 'Y' が設定されている
if( isset($_POST[HIDDEN_FIELD_NAME]) &&
    $_POST[HIDDEN_FIELD_NAME] == 'Y' ){

  ///////////////////////////////////////
  // 設定の保存
  ///////////////////////////////////////
  require_once 'posts.php';

}

///////////////////////////////////////
// 入力フォーム
///////////////////////////////////////
?>
<div class="wrap">
<h1><?php _e( '使いまわしテキスト（関数テキスト）', THEME_NAME ) ?></h1>
    <!-- 使いまわしテキスト（関数テキスト） -->
    <div class="func-text metabox-holder">
      <?php require_once 'list.php'; ?>
      <?php require_once 'form.php'; ?>
    </div><!-- /.metabox-holder -->
</div>
