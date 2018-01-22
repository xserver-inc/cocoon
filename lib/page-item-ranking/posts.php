<?php //内容の保存
if (!empty($_POST['title']) &&
    !empty($_POST['item_ranking']) &&
    !empty($_POST['action'])) {
_v($_POST);
  if ($_POST['action'] == 'new') {
    _v('new');
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
    _v('edit');
    $id = isset($_POST['id']) ? intval($_POST['id']) : '';
    if ($id) {
      $result = update_item_ranking_record($id, $_POST);
      //_v($_POST);
      if ($result) {
        generate_notice_message_tag(__( 'ランキングを更新しました。', THEME_NAME ));
      } else {
        generate_error_message_tag(__( 'ランキングを更新できませんでした。', THEME_NAME ));
      }
    }
  }
} else {
  $message = '';
  if (empty($_POST['title'])) {
    $message .= __( 'タイトルが入力されていません。', THEME_NAME ).'<br>';
  }
  if (empty($_POST['item_ranking'])) {
    $message .= __( 'ランキングアイテムが作成されていません。', THEME_NAME ).'<br>';
  }
  if (empty($_POST['action'])) {
    $message .= __( '入力内容が不正です。', THEME_NAME ).'<br>';
  }
  generate_error_message_tag($message);
}