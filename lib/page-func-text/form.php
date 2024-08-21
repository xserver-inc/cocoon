<?php //テンプレートフォーム
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<form name="form1" method="post" action="">
  <?php

  if (isset($_GET['id'])) {
    $action = 'edit';
    $id = isset($_GET['id']) ? intval($_GET['id']) : '';
    //$query = "SELECT date, modified, title, text FROM {FUNCTION_TEXTS_TABLE_NAME} WHERE id = {$id}";
    $recode = get_function_text($id);
    $title = isset($recode->title) ? $recode->title : '';
    $text = isset($recode->text) ? stripslashes_deep($recode->text) : '';
    $visible = $recode->visible;

  } else {
    $action = 'new';
    $id = '';
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $text = isset($_POST['text']) ? stripslashes_deep($_POST['text']) : '';
    $visible = 1;
  }

  echo '<h2>'.__( 'タイトル', THEME_NAME ).'</h2>';
  generate_textbox_tag('title', $title, __( 'タイトルの入力（126文字まで）', THEME_NAME ));
  generate_tips_tag(__( '表示ラベルとなるタイトルを入力してください。タイトルは一覧表示用です。', THEME_NAME ));

  echo '<h2>'.__( '内容', THEME_NAME ).'</h2>';

  generate_visuel_editor_tag('text', $text,  'func-text');
  generate_tips_tag(__( '関数化するテキストを入力してください。', THEME_NAME ).__( '無限ループを避けるため、テンプレート内にtemp, tocショートコードが使用されている場合は、出力時に削除されます。', THEME_NAME ));

  //TinyMCE表示
  generate_checkbox_tag('visible' , $visible, __( 'エディターのリストに表示', THEME_NAME ));
  generate_tips_tag(__( 'エディターのドロップダウンリストに表示しなくて良い場合は、無効にしてください。', THEME_NAME )); ?>
  <input type="hidden" name="action" value="<?php echo $action; ?>">
  <input type="hidden" name="id" value="<?php echo $id; ?>">
  <input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="<?php echo wp_create_nonce('func-text');?>"><br>
  <?php submit_button(__( '保存', THEME_NAME )); ?>
</form>
