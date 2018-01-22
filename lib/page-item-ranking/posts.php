<?php //内容の保存
$title = !empty($_POST['title']) ? $_POST['title'] : null;
$items = !empty($_POST['item_ranking']) ? $_POST['item_ranking'] : null;
$count = !empty($_POST['count']) ? $_POST['count'] : null;
$last_item = $items[$count];

if (!empty($title) &&
    !empty($items) &&
    !empty($count) &&
    !empty($_POST['action'])
   ) {

  var_dump($_POST);
  if ($_POST['action'] == 'new') {
    //_v('new');
    $result = insert_item_ranking_record($_POST);
    //_v($_POST);
    //_v($result);
    //編集モードに変更
    if ($result) {
      global $wpdb;
      $_GET['action'] = 'edit';
      $_GET['id'] = $wpdb->insert_id;
      generate_notice_message_tag(__( 'ランキングを新規作成しました。', THEME_NAME ));
    } else {
      generate_error_message_tag(__( 'ランキングを新規作成できませんでした。', THEME_NAME ));
    }
    //_v($result);
  } else {
    //_v('edit');
    $id = isset($_POST['id']) ? intval($_POST['id']) : '';
    if ($id) {
      $result = null;
      //最後尾のアイテムが有効でなかったら配列を削除しカウント数を一つマイナスする
      if (is_ranking_item_available($last_item)) {
        unset($_POST['item_ranking'][$count]);
        $_POST['count'] = intval($_POST['count']) - 1;
        generate_notice_message_tag(__( '新規アイテム追加は行っていません。', THEME_NAME ));
        echo '<br>';
      }
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