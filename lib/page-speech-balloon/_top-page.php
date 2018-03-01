
<?php //オリジナル設定ページ
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
  if( isset($_POST[HIDDEN_DELETE_FIELD_NAME]) &&
    $_POST[HIDDEN_DELETE_FIELD_NAME] == 'Y' ){

    ///////////////////////////////////////
    // 内容の削除
    ///////////////////////////////////////
    require_once 'posts-delete.php';
  }
} else {
  if( isset($_POST[HIDDEN_FIELD_NAME]) &&
    $_POST[HIDDEN_FIELD_NAME] == 'Y' ){

    ///////////////////////////////////////
    // 内容の保存
    ///////////////////////////////////////
    require_once 'posts.php';
  }
}



///////////////////////////////////////
// 入力フォーム
///////////////////////////////////////
?>
<div class="wrap admin-settings">
<h1><?php _e( '吹き出し', THEME_NAME ) ?></h1>
    <!-- 使いまわしテキスト（関数テキスト） -->
    <div class="speech-balloon metabox-holder">
      <div class="operation-buttons">
        <a href="<?php echo SB_LIST_URL; ?>"><?php _e( '一覧ページへ', THEME_NAME ) ?></a>
        <a href="<?php echo SB_NEW_URL; ?>"><?php _e( '新規追加', THEME_NAME ) ?></a>
      </div>

      <?php //一覧リストの表示
      $action = isset($_GET['action']) ? $_GET['action'] : null;
      if ($action == 'delete') {
        require_once 'form-delete.php';
      } else {
        if (!isset($action)) {
          require_once 'list.php';
        } else {//入力フォームの表示
          require_once 'form.php';
        }
      }


      ?>
    </div><!-- /.metabox-holder -->
</div>
