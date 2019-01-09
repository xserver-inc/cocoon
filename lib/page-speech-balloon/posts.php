<?php //内容の保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (//!empty($_POST['name']) &&
    !empty($_POST['icon']) &&
    !empty($_POST['action'])) {
  if ($_POST['action'] == 'new') {
    $result = insert_speech_balloon_record($_POST);
    //編集モードに変更
    if ($result) {
      global $wpdb;
      $_GET['action'] = 'edit';
      $_GET['id'] = $wpdb->insert_id;
      generate_notice_message_tag(__( '吹き出しを新規作成しました。', THEME_NAME ));
    } else {
      generate_error_message_tag(__( '吹き出しを新規作成できませんでした。', THEME_NAME ));
    }
    //_v($result);
  } else {
    $id = isset($_POST['id']) ? intval($_POST['id']) : '';
    if ($id) {
      $result = update_speech_balloon_record($id, $_POST);
      if ($result) {
        generate_notice_message_tag(__( '吹き出しを更新しました。', THEME_NAME ));
      } else {
        generate_error_message_tag(__( '吹き出しを更新できませんでした。', THEME_NAME ));
      }
    }

  }
} else {
  $message = '';
  // if (empty($_POST['name'])) {
  //   $message .= __( '名前が入力されていません。', THEME_NAME ).'<br>';
  // }
  if (empty($_POST['icon'])) {
    $message .= __( 'アイコン画像が入力されていません。', THEME_NAME ).'<br>';
  }
  if (empty($_POST['action'])) {
    $message .= __( '入力内容が不正です。', THEME_NAME ).'<br>';
  }
  generate_error_message_tag($message);
}
