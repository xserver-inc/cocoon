<?php
//一覧ページへのURL
$list_url = add_query_arg(array('action' => false, 'id' => false));
 ?>
<form name="form1" method="post" action="<?php echo $list_url; ?>" class="admin-settings">
  <?php
  $id = isset($_GET['id']) ? $_GET['id'] : null;

  if ($id) {
    $recode = get_function_text($id);
    if (!$recode) {
      //指定IDの関数テキストが存在しない場合は一覧にリダイレクト
      header( "HTTP/1.1 301 Moved Permanently" );
      header( "location: " . $list_url  );
      exit;
    }

  }
  ?>
  <p><?php _e( '以下の内容を削除しますか？', THEME_NAME ) ?></p>

  <div class="ft-confirm-wrap">
    <div class="ft-confirm-title">
      <?php echo esc_html($recode->title); ?>
    </div>
    <div class="ft-confirm-text">
      <pre>
      <?php echo esc_html($recode->text); ?>
      </pre>
    </div>
  </div>
  <div class="yes-back">
    <?php submit_button(__( '削除する', THEME_NAME )); ?>
    <p><a href="<?php echo $list_url; ?>"><?php _e( '削除しないで一覧に戻る', THEME_NAME ) ?></a></p>
  </div>

  <input type="hidden" name="action" value="delete">
  <input type="hidden" name="id" value="<?php echo $id; ?>">
  <input type="hidden" name="<?php echo HIDDEN_DELETE_FIELD_NAME; ?>" value="Y">
</form>