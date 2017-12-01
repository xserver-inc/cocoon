<?php //

$records = get_function_texts();
//var_dump($records);
?>
<div class="ft-list">
  <?php foreach ($records as $record):
  //var_dump($record);
   ?>
    <div class="function-text">
      <div class="ft-title">
        <a href="<?php echo add_query_arg(array('action' => 'edit', 'post' => $record->id)); ?>"><?php echo $record->title; ?></a>
      </div>
      <div class="ft-short-code">
        <input type="text" name="" value="[ft=<?php echo $record->id; ?>]">
      </div>
      <div class="ft-content"><?php echo strip_tags($record->text); ?></div>
    </div>
  <?php endforeach ?>
</div>