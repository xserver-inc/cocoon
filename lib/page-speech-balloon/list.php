<?php //
//var_dump($_POST);
$keyword = !empty($_POST['s']) ? $_POST['s'] : null;
$order_by = isset($_POST['order']) ? $_POST['order'] : 'date DESC';
//var_dump($order_by);
$records = get_speech_balloons($keyword, $order_by);
//var_dump($records);
//並び替えオプション
generate_sort_options_tag($keyword, $order_by);
?>
<!-- メッセージ -->
<?php if ($records): ?>
  <p class="op-message"><?php _e( 'ショートコードをコピーして本文の表示したい部分に貼り付けてください。', THEME_NAME ) ?></p>
<?php else: ?>
  <p class="op-message"><?php _e( '「吹き出し」を作成するには「新規作成」リンクをクリックしてください。', THEME_NAME ) ?></p>
<?php endif ?>

<table class="sb-list">
  <?php foreach ($records as $record):
  //var_dump($record);
  $edit_url   = add_query_arg(array('action' => 'edit',   'id' => $record->id));
  $delete_url = add_query_arg(array('action' => 'delete', 'id' => $record->id));
   ?>
  <tr>
    <td>
      <?php //吹き出しの表示
      generate_speech_balloon_tag(
        $record->name,
        $record->icon,
        $record->style,
        $record->position,
        'あああああああああああああああああああああああああああああああ'
      ); ?>
    </td>
    <td class="sb-list-option">
      <p><a href="<?php echo $edit_url; ?>"><?php _e( '編集', THEME_NAME ) ?></a></p>
      <p><a href="<?php echo $delete_url; ?>"><?php _e( '削除', THEME_NAME ) ?></a></p>
    </td>
  </tr>
  <?php endforeach ?>
</table>
