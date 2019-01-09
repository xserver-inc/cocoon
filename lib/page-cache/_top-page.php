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
if( isset($_GET['cache']) && isset($_GET[HIDDEN_DELETE_FIELD_NAME]) && wp_verify_nonce($_GET[HIDDEN_DELETE_FIELD_NAME], 'delete-cache') ){
  //var_dump($_POST[OP_RESET_ALL_SETTINGS]);

  ///////////////////////////////////////
  // 設定の保存
  ///////////////////////////////////////
  // //キャッシュ削除
  // require_once abspath(__FILE__).'cache-posts.php';
  //関数の呼び出し
  require_once abspath(__FILE__).'cache-func.php';
  $res = delete_theme_storaged_caches();

  $message = __( 'キャッシュを削除しました。', THEME_NAME );

  //Amazon個別商品情報を削除する場合
  $asin = isset($_GET['asin']) ? trim($_GET['asin']) : null;
  if ($asin) {
    $asin_before = sprintf(__( 'ASIN:%sの', THEME_NAME ), $asin);
    $asin_after = __( '該当の商品リンクページをリロードしてご確認ください。', THEME_NAME );
    $message = $asin_before.$message.$asin_after;
  }

  //楽天個別商品情報を削除する場合
  $id = isset($_GET['id']) ? trim($_GET['id']) : null;
  if ($id) {
    $id_before = sprintf(__( '楽天商品番号:%sの', THEME_NAME ), $id);
    $id_after = __( '該当の商品リンクページをリロードしてご確認ください。', THEME_NAME );
    $message = $id_before.$message.$id_after;
  }

//画面に「設定は保存されました」メッセージを表示

?>
<?php if ($res): ?>
<div class="updated">
  <p>
    <strong>
      <?php echo $message; ?>
    </strong>
  </p>
</div>
<?php endif ?>

<?php
}

///////////////////////////////////////
// 入力フォーム
///////////////////////////////////////
?>
<div class="wrap admin-settings">
<h1><?php _e( 'キャッシュ削除', THEME_NAME ) ?></h1><br>
  <!-- バックアップ -->
  <div class="cache metabox-holder">
    <?php require_once abspath(__FILE__).'cache-forms.php'; ?>
  </div><!-- /.metabox-holder -->
</div>
