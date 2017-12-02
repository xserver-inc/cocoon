<?php //
//var_dump($_POST);
$keyword = !empty($_POST['s']) ? $_POST['s'] : null;
$order_by = isset($_POST['order']) ? $_POST['order'] : 'title';
//var_dump($order_by);
$records = get_function_texts($keyword, $order_by);
//var_dump($records);
?>

<!-- 抽出フォーム -->
<div class="ft-option">
  <form method="post" action="">
  <input type="text" name="s" value="<?php echo $keyword; ?>" placeholder="<?php _e( 'タイトル検索', THEME_NAME ) ?>">
  <?php

    $options = array(
      'title' => __( 'タイトル昇順', THEME_NAME ),
      'title DESC' => __( 'タイトル降順', THEME_NAME ),
      'date' => __( '作成日昇順', THEME_NAME ),
      'date DESC' => __( '作成日降順', THEME_NAME ),
      'modified' => __( '編集日昇順', THEME_NAME ),
      'modified DESC' => __( '編集日降順', THEME_NAME ),
    );
    generate_selectbox_tag('order', $options, $order_by);
   ?>
   <input type="submit" name="" value="<?php _e( '抽出', THEME_NAME ) ?>">
   </form>
</div>

<!-- メッセージ -->
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
        <a href="<?php echo $edit_url; ?>"><?php echo esc_html(stripslashes_deep($record->title)); ?></a>
      </div>
      <div class="ft-short-code">
        <?php _e( 'ショートコード：', THEME_NAME ) ?><input type="text" name="" value="[ft id=<?php echo esc_html($record->id); ?>]">
      </div>
      <div class="ft-content">
        <?php
        $text = strip_tags(stripslashes_deep($record->text));
        $text = mb_substr($text, 0, 200);;
        echo $text; ?>
      </div>
      <div class="ft-menu">
        <a href="<?php echo $edit_url; ?>"><?php _e( '編集', THEME_NAME ) ?></a>
        <a href="<?php echo $delete_url; ?>"><?php _e( '削除', THEME_NAME ) ?></a>
      </div>
    </div>
  <?php endforeach ?>
</div>