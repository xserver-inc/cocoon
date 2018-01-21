<p><?php _e( '吹き出し用の設定です。', THEME_NAME ) ?></p>
<form name="form1" method="post" action="">
  <?php

  if (isset($_GET['id'])) {
    $action = 'edit';
    $id = isset($_GET['id']) ? intval($_GET['id']) : '';
    $record = get_item_ranking($id);
    $title = $record->title;
    $ranking = $record->ranking;
    $items = $ranking['items'];
    $count = isset($ranking['count']) ? intval($ranking['count'] + 1) : 1;

    //吹き出しデモの表示
    require_once 'demo.php';

  } else {
    $action = 'new';
    $id = '';
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $ranking = isset($_POST['ranking']) ? $_POST['ranking'] : '';
    $items = $ranking['items'];
    $count = 1;
  }?>

  <?php for ($i = 1; $i <= $count; $i++):
  $name = $items[$i]['name'];
  $rating = $items[$i]['rating'];
  $image_tag = $items[$i]['image_tag'];
  $description = $items[$i]['description'];
   ?>
  <div id="ranking-" class="postbox">
    <div class="inside">
      <table>

        <tr>
          <td>
            <?php generate_label_tag('title', __( 'タイトル', THEME_NAME )); ?>
          </td>
          <td>
            <?php
            generate_textbox_tag('title', $title,  __('未入力でも可',THEME_NAME ));
            generate_tips_tag(__( '吹き出しのタイトルを入力してください。タイトルは管理画面（検索など）でしか利用されません。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <tr>
          <th>
            <?php generate_label_tag('ranking[$i][name]', __( '名前', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag('ranking[$i][name]', $name,  __('商品名等を入力してください',THEME_NAME ));
            generate_tips_tag(__( '個々のランキングの見出しとなる名前を入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>


        <tr>
          <th>
            <?php generate_label_tag('ranking[$i][rating]', __( '評価', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag('ranking[$i][rating]', $rating,  __('商品名等を入力してください',THEME_NAME ));
            generate_tips_tag(__( '表示する星の数を選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </table>
    </div>
  </div>
  <?php endfor ?>

  <input type="hidden" name="ranking[count]" value="<?php echo $count; ?>">
  <input type="hidden" name="action" value="<?php echo $action; ?>">
  <input type="hidden" name="id" value="<?php echo $id; ?>">
  <input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="Y">
  <?php submit_button(__( '保存', THEME_NAME )); ?>
</form>