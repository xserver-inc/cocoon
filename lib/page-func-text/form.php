<form name="form1" method="post" action="" class="admin-settings">
  <?php

  echo '<h2>'.__( 'タイトル', THEME_NAME ).'</h2>';
  $title = isset($_POST['title']) ? $_POST['title'] : '';
  generate_textbox_tag('title', $title, __( '', THEME_NAME ));
  generate_tips_tag(__( '表示ラベルとなるタイトルを入力してください。', THEME_NAME ));

  _v($_POST);
  $content = isset($_POST['text']) ? stripslashes_deep($_POST['text']) : '';
  $editor_id = 'original'; //エディターを区別するために、IDを指定する
  $settings = array( 'textarea_name' => 'text' ); //配列としてデータを渡すためname属性を指定する
   echo '<h2>'.__( '内容', THEME_NAME ).'</h2>';
   wp_editor( $content, $editor_id, $settings );
   generate_tips_tag(__( '関数化するテキストを入力してください。', THEME_NAME )); ?>
  <input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="Y">
   <?php submit_button(__( '保存', THEME_NAME )); ?>
</form>