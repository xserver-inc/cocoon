<p><?php _e( 'ランキングを作成します。次のランキングを入力するには保存ボタンを押してください。', THEME_NAME ) ?></p>
<form name="form1" id="ranking-items" method="post" action="">
  <?php

  if (isset($_GET['id'])) {
    $action = 'edit';
    $id = isset($_GET['id']) ? intval($_GET['id']) : '';
    $record = get_item_ranking($id);
    $title = $record->title;
    $ranking = $record->ranking;
    $items = isset($ranking['items']) ? $ranking['items'] : array();
    $count = isset($ranking['count']) ? intval($ranking['count'] + 1) : 1;

    //吹き出しデモの表示
    require_once 'demo.php';

  } else {
    $action = 'new';
    $id = '';
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $ranking = isset($_POST['ranking']) ? $_POST['ranking'] : '';
    $items = isset($ranking['items']) ? $ranking['items'] : array();
    $count = 1;
  }?>

  <div class="ranking-title">
    <?php
    echo '<h2>';
    generate_label_tag('title', __('タイトル', THEME_NAME) );
    echo '</h2>';
    generate_textbox_tag('title', $title,  __('タイトルを入力',THEME_NAME ));
    generate_tips_tag(__( 'ランキングを識別するためのタイトルを入力してください。', THEME_NAME ));echo '<br>';
    ?>
  </div>

  <?php
  for ($i = 1; $i <= $count; $i++):
    $name = isset($items[$i]['name']) ? $items[$i]['name'] : '';
    $rating = isset($items[$i]['rating']) ? $items[$i]['rating'] : 'none';
    $image_tag = isset($items[$i]['image_tag']) ? $items[$i]['image_tag'] : '';
    $description = isset($items[$i]['description']) ? $items[$i]['description'] : '';
    $detail_url = isset($items[$i]['detail_url']) ? $items[$i]['detail_url'] : '';
    $link_tag = isset($items[$i]['link_tag']) ? $items[$i]['link_tag'] : '';
   ?>
  <div class="postbox">
    <div class="inside">

      <div class="ranking-item demo">
        <div class="ranking-item-name">
          <div class="g-crown"><div class="g-crown-circle"></div></div>
          <div class="ranking-item-name-text">
          <?php
          //generate_label_tag('', __('名前：', THEME_NAME) );
          generate_textbox_tag('ranking['.$i.'][name]', $name,  __('商品名等、見出しとなる名前を入力してください',THEME_NAME ));
           ?>
          </div>

        </div>
        <div class="ranking-item-rating">
          <?php
          generate_label_tag('', __('評価：', THEME_NAME) );
          $options = array(
            'none' => __( 'なし', THEME_NAME ),
            '0.0' => __( '0', THEME_NAME ),
            '0.5' => __( '0.5', THEME_NAME ),
            '1.0' => __( '1', THEME_NAME ),
            '1.5' => __( '1.5', THEME_NAME ),
            '2.0' => __( '2', THEME_NAME ),
            '2.5' => __( '2.5', THEME_NAME ),
            '3.0' => __( '3', THEME_NAME ),
            '3.5' => __( '3.5', THEME_NAME ),
            '4.0' => __( '4', THEME_NAME ),
            '4.5' => __( '4.5', THEME_NAME ),
            '5.0' => __( '5', THEME_NAME ),
          );
          //_v($rating);
          generate_radiobox_tag('ranking['.$i.'][rating]', $options, $rating);
          generate_tips_tag(__( '商品等を星の数で評価します。「なし」を選択した場合は表示されません。', THEME_NAME ));
          ?>
        </div>
        <div class="ranking-item-img-desc">
          <div class="ranking-item-image-tag">
            <?php
            generate_label_tag('', __('画像・バナータグ', THEME_NAME) );
            echo '<br>';
            generate_textarea_tag('ranking['.$i.'][image_tag]', $image_tag, __( '商品画像、もしくはバナーのタグ等を入力してください。', THEME_NAME ), 5) ;
            //generate_tips_tag(__( '', THEME_NAME ));
            ?>
          </div>
          <div class="ranking-item-description">
            <?php
            generate_label_tag('', __('説明文', THEME_NAME) );
            echo '<br>';
            generate_textarea_tag('ranking['.$i.'][description]', $description,  '商品等の説明文を入力してください。', 5);
             ?>
          </div>
        </div>
        <div class="ranking-item-link-buttons">
          <div class="ranking-item-detail-url">
            <?php
            generate_label_tag('', __('詳細ページURL', THEME_NAME) );
            echo '<br>';
            generate_textbox_tag('ranking['.$i.'][detail_url]', $detail_url,  __('http://',THEME_NAME ));
            generate_tips_tag(__( '詳細ページへリンクするためのURLを入力してください。', THEME_NAME ));
            ?>
          </div>
          <div class="ranking-item-link-tag">
            <?php
            generate_label_tag('', __('リンクタグ', THEME_NAME) );
            echo '<br>';
            generate_textarea_tag('ranking['.$i.'][link_tag]', $link_tag, __( '公式ページ等のリンク（アフィリエイト）タグを入力してください。', THEME_NAME ), 5) ;
            generate_tips_tag(__( '直接リンクを入力する場合はタグを入力してください。※入力しない場合はボタンが表示されません。', THEME_NAME ));
            ?>
          </div>
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