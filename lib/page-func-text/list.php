<?php //

$records = get_function_texts();
//var_dump($records);
?>
<div class="ft-list">
  <?php foreach ($records as $record):
  //var_dump($record);
   ?>
    <div class="ft-wrap">
      <div class="ft-title">
        <a href="<?php echo add_query_arg(array('action' => 'edit', 'id' => $record->id)); ?>"><?php echo esc_html($record->title); ?></a>
      </div>
      <div class="ft-short-code">
        <?php _e( 'ショートコード：', THEME_NAME ) ?><input type="text" name="" value="[ft=<?php echo esc_html($record->id); ?>]">
      </div>
      <div class="ft-content"><?php echo strip_tags($record->text); ?></div>
    </div>
  <?php endforeach ?>
</div>