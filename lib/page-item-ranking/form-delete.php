<?php //ランキング削除フォーム
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//一覧ページへのURL
$list_url = IR_LIST_URL;
 ?>
<form name="form1" method="post" action="<?php echo $list_url; ?>" class="item-ranking-delete">
  <?php
  $id = isset($_GET['id']) ? $_GET['id'] : null;

  if ($id) {
    $record = get_item_ranking($id);
    if (!$record) {
      //指定IDの関数テキストが存在しない場合は一覧にリダイレクト
      redirect_to_url($list_url);
    }
    $edit_url = add_query_arg(array('action' => 'edit',   'id' => $id));
  }
  ?>
  <p><?php _e( '以下の内容を削除しますか？', THEME_NAME ) ?></p>

  <div id="sb-list" class="postbox" style="max-width: 800px; padding: 20px;">
    <a href="<?php echo $edit_url; ?>" class="ir-list-title"><?php echo $record->title; ?></a>
  </div>

  <div class="yes-back">
    <?php submit_button(__( '削除する', THEME_NAME )); ?>
    <p><a href="<?php echo $list_url; ?>"><?php _e( '削除しないで一覧に戻る', THEME_NAME ) ?></a></p>
  </div>

  <input type="hidden" name="action" value="delete">
  <input type="hidden" name="id" value="<?php echo $id; ?>">
  <input type="hidden" name="<?php echo HIDDEN_DELETE_FIELD_NAME; ?>" value="<?php echo wp_create_nonce('delete-item-ranking');?>">
</form>
