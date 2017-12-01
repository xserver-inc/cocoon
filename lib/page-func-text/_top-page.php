
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
      <div class="ft-input">
        <a href="<?php echo add_query_arg(array('action' => false, 'id' => false)); ?>"><?php _e( '一覧ページへ', THEME_NAME ) ?></a>
        <a href="<?php echo add_query_arg(array('action' => 'new', 'id' => false)); ?>"><?php _e( '新規追加', THEME_NAME ) ?></a>
      </div>

      <?php //一覧リストの表示
      if (!isset($_GET['action'])) {
        require_once 'list.php';
      } else {//入力フォームの表示
        require_once 'form.php';
      }?>
    </div><!-- /.metabox-holder -->
</div>
