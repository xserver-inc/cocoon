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

  <div class="ranking-title">
    <?php
    generate_textbox_tag('title', $title,  __('タイトルを入力',THEME_NAME ));
    generate_tips_tag(__( 'ランキングを識別するためのタイトルを入力してください。', THEME_NAME ));
    ?>
  </div>

  <?php
  for ($i = 1; $i <= $count; $i++):
    $name = isset($items[$i]['name']) ? $items[$i]['name'] : '';
    $rating = isset($items[$i]['rating']) ? $items[$i]['rating'] : 'none';
    $image_tag = isset($items[$i]['image_tag']) ? $items[$i]['image_tag'] : '';
    $description = isset($items[$i]['description']) : $items[$i]['description'] : '';
    $detail_url = isset($items[$i]['detail_url']) ? $items[$i]['detail_url'] : '';
    $link_tag = isset($items[$i]['link_tag']) ? $items[$i]['link_tag'] : '';
   ?>
  <div id="ranking-items" class="postbox">
    <div class="inside">

      <div id="ranking-item-<?php echo $i; ?>">
        <div class="ranking-item-name">
          <?php generate_textbox_tag('ranking[$i][name]', $name,  __('商品名等、見出しとなる名前を入力してください',THEME_NAME )); ?>
        </div>
        <div class="ranking-item-rating">
          <?php
          $options = array(
            'none' => __( 'なし', THEME_NAME ),
            '0' => __( '0', THEME_NAME ),
            '0.5' => __( '0.5', THEME_NAME ),
            '1' => __( '1', THEME_NAME ),
            '1.5' => __( '1.5', THEME_NAME ),
            '2' => __( '2', THEME_NAME ),
            '2.5' => __( '2.5', THEME_NAME ),
            '3' => __( '3', THEME_NAME ),
            '3.5' => __( '3.5', THEME_NAME ),
            '4' => __( '4', THEME_NAME ),
            '4.5' => __( '4.5', THEME_NAME ),
            '5' => __( '5', THEME_NAME ),
          );
          generate_radiobox_tag('ranking[$i][rating]', $options, $rating);
          generate_tips_tag(__( '商品等を星の数で評価します。「なし」を選択した場合は表示されません。', THEME_NAME ));
          ?>
        </div>
        <div class="ranking-img-desc">
          <div class="ranking-item-image-tag">
            <?php

             ?>
          </div>
          <div class="ranking-item-description">

          </div>
        </div>
        <div class="ranking-item-detail-url">

        </div>
        <div class="ranking-item-link-tag">

        </div>
      </div>

    </div>
  </div>
  <?php endfor ?>

  <input type="hidden" name="ranking[count]" value="<?php echo $count; ?>">
  <input type="hidden" name="action" value="<?php echo $action; ?>">
  <input type="hidden" name="id" value="<?php echo $id; ?>">
  <input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="Y">
  <?php submit_button(__( '保存', THEME_NAME )); ?>
</form>