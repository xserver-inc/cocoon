<?php //内容の保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

//_v(isset($_POST['title'], $_POST['text'], $_POST['action']));
if (!empty($_POST['title']) && !empty($_POST['text']) && !empty($_POST['action'])) {
  global $wpdb;
  //var_dump($_POST);

  // $now = current_time('mysql');
  // $table = FUNCTION_TEXTS_TABLE_NAME;
  if ($_POST['action'] == 'new') {
    // $data = array(
    //   'date' => $now,
    //   'modified' => $now,
    //   'title' => $_POST['title'],
    //   'text' => $_POST['text'],
    // );
    // $format = array(
    //   '%s',
    //   '%s',
    //   '%s',
    //   '%s',
    // );
    // $result = $wpdb->insert( $table, $data, $format );
    $result = insert_function_text_record($_POST);

    //_v($wpdb->insert_id);
    //編集モードに変更
    if ($result) {
      global $wpdb;
      $_GET['action'] = 'edit';
      $_GET['id'] = $wpdb->insert_id;
      generate_notice_message_tag(__( 'テキストを新規作成しました。', THEME_NAME ));
    }
    //_v($result);
  } else {
    $id = isset($_POST['id']) ? intval($_POST['id']) : '';
    if ($id) {
      // $data = array(
      //   'modified' => $now,
      //   'title' => $_POST['title'],
      //   'text' => $_POST['text'],
      // );
      // $where = array('id' => $id);
      // $format = array(
      //   '%s',
      //   '%s',
      //   '%s',
      // );
      // $where_format = array('%d');
      // $result = $wpdb->update( $table, $data, $where, $format, $where_format );
      $result = update_function_text_record($id, $_POST);
      if ($result) {
        generate_notice_message_tag(__( 'テキストを更新しました。', THEME_NAME ));
      }
    }

  }
  //設定保存メッセージ
  if ($result) {
    //generate_notice_message_tag(__( '内容を保存しました。', THEME_NAME ));
  } else {
    generate_notice_message_tag(__( 'データベースに保存されませんでした。', THEME_NAME ));
  }
} else {
  $message = '';
  if (empty($_POST['title'])) {
    $message .= __( 'タイトルが入力されていません。', THEME_NAME ).'<br>';
  }
  if (empty($_POST['text'])) {
    $message .= __( '内容が入力されていません。', THEME_NAME ).'<br>';
  }
  if (empty($_POST['action'])) {
    $message .= __( '入力内容が不正です。', THEME_NAME ).'<br>';
  }
  generate_error_message_tag($message);
}