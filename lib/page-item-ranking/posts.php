<?php //内容の保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

$title = !empty($_POST['title']) ? $_POST['title'] : null;
$items = !empty($_POST['item_ranking']) ? $_POST['item_ranking'] : null;
$count = !empty($_POST['count']) ? $_POST['count'] : null;
$last_item = $items[$count];

if (!empty($title) &&
    !empty($items) &&
    !empty($count) &&
    !empty($_POST['action'])
   ) {

  //var_dump($_POST);
  if ($_POST['action'] == 'new') {
    $result = null;
    //_v('new');
    //var_dump($last_item);
    //var_dump(is_ranking_item_available($last_item));
    if (is_ranking_item_available($last_item)) {
      $result = insert_item_ranking_record($_POST);
    }

    //_v($_POST);
    //_v($result);
    //編集モードに変更
    if ($result) {
      global $wpdb;
      $_GET['action'] = 'edit';
      $_GET['id'] = $wpdb->insert_id;
      generate_notice_message_tag(__( 'ランキングを新規作成しました。', THEME_NAME ));
    } else {
      generate_error_message_tag(__( '「名前」と「説明文」は必須入力項目です。', THEME_NAME ));
      echo '<br>';
      generate_error_message_tag(__( 'ランキングを新規作成できませんでした。', THEME_NAME ));
    }
    //_v($result);
  } else {
    //_v('edit');
    $id = isset($_POST['id']) ? intval($_POST['id']) : '';
    if ($id) {
      $result = null;
      $tmp_item = null;

      //最後尾のアイテムが有効でなかったら配列を削除しカウント数を一つマイナスする
      if (!is_ranking_item_available($last_item)) {
        $tmp_item = $_POST['item_ranking'][$count];
        unset($tmp_item);
        $_POST['count'] = intval($_POST['count']) - 1;
        generate_notice_message_tag(__( sprintf('%d位のアイテムは、「名前」や「説明文」が入力されていないため追加保存は行っていません。', $count), THEME_NAME ));
        echo '<br>';
        if (isset($tmp_item)) {
          $_POST['item_ranking'][$count] = $tmp_item;
        }
      }

      //データベースのアップデート処理
      $result = update_item_ranking_record($id, $_POST);
      //_v($_POST);
      if ($result) {
        $_GET['id'] = $id;
        generate_notice_message_tag(__( 'ランキングを更新しました。', THEME_NAME ));
      } else {
        generate_error_message_tag(__( 'ランキングを更新できませんでした。', THEME_NAME ));
      }
    }
  }
} else {
  $message = '';
  $_POST['title'] = !empty($_POST['title']) ? stripslashes_deep($_POST['title']) : null;
  $_POST['item_ranking'] = !empty($_POST['item_ranking']) ? stripslashes_deep($_POST['item_ranking']) : null;
  if (empty($title)) {
    $message .= __( 'タイトルが入力されていません。', THEME_NAME ).'<br>';
  }
  if (empty($items)) {
    $message .= __( 'ランキングアイテムが作成されていません。', THEME_NAME ).'<br>';
  }
  if (empty($count)) {
    $message .= __( 'アイテムカウントが設定されていません。', THEME_NAME ).'<br>';
  }
  if (empty($_POST['action'])) {
    $message .= __( '入力内容が不正です。', THEME_NAME ).'<br>';
  }
  generate_error_message_tag($message);
}
