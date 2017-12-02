<?php //

$records = get_function_texts();
//var_dump($records);
?>
<?php if ($records): ?>
  <p class="ft-message"><?php _e( 'ショートコードをコピーして本文の表示したい部分に貼り付けてください。', THEME_NAME ) ?></p>
<?php else: ?>
  <p class="ft-message"><?php _e( '「使いまわしテキスト」を作成するには「新規作成」リンクをクリックしてください。', THEME_NAME ) ?></p>
<?php endif ?>

<div class="ft-list">
  <?php foreach ($records as $record):
  //var_dump($record);
  $edit_url   = add_query_arg(array('action' => 'edit',   'id' => $record->id));
  $delete_url = add_query_arg(array('action' => 'delete', 'id' => $record->id));
   ?>
    <div class="ft-wrap">
      <div class="ft-title">
        <a href="<?php echo $edit_url; ?>"><?php echo esc_html($record->title); ?></a>
      </div>
      <div class="ft-short-code">
        <?php _e( 'ショートコード：', THEME_NAME ) ?><input type="text" name="" value="[ft id=<?php echo esc_html($record->id); ?>]">
      </div>
      <div class="ft-content">
        <?php echo strip_tags($record->text); ?>
      </div>
      <div class="ft-menu">
        <a href="<?php echo $edit_url; ?>"><?php _e( '編集', THEME_NAME ) ?></a>
        <a href="<?php echo $delete_url; ?>"><?php _e( '削除', THEME_NAME ) ?></a>
      </div>
    </div>
  <?php endforeach ?>
</div>