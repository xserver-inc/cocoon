<?php //ランキングフォーム
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<p><?php _e( 'ランキングを作成します。次のランキングを入力するには保存ボタンを押してください。', THEME_NAME ) ?></p>
<?php //IDがある場合はIDの取得（編集モードの場合）
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$action = isset($_GET['action']) ? intval($_GET['action']) : null;
//アイテムの移動
if ( $id && ($action == 'move') && isset($_GET['from']) && isset($_GET['to']) ) {
  $from = $_GET['from'];
  $to = $_GET['to'];
  move_ranking_item($id, $from, $to);
}
//アイテムの削除
if ( $id && ($action == 'item_delete') && isset($_GET['del_no']) && isset($_GET['conf_no']) && ($_GET['del_no'] == $_GET['conf_no']) ) {
  $del_no = $_GET['del_no'];
  delete_ranking_item($id, $del_no);
}
 ?>
<?php if ($id): ?>
  <p class="preview-label" style="max-width: 1000px;"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
  <div class="demo" style="width: 1000px; height: 460px;margin-bottom: 2em;resize: both;">
        <?php
        ob_start();
        generate_item_ranking_tag($id);
        $tag = change_fa(ob_get_clean());
        echo $tag; ?>
  </div>
<?php endif ?>



<form name="form1" id="ranking-items" method="post" action="">
  <?php

  if ($id) {
    $action = 'edit';

    $record = get_item_ranking($id);
    $title = $record->title;
    $items = isset($record->item_ranking) ? $record->item_ranking : array();
    $count = isset($record->count) ? intval($record->count) + 1 : 1;
    //var_dump($record);
    $visible = $record->visible;
    //_v($items);
  // _v($action);
  // _v($count);
    //var_dump($record);


  } else {
    $action = 'new';
    $id = '';
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $item_ranking = isset($_POST['item_ranking']) ? $_POST['item_ranking'] : '';
    $items = $item_ranking;
    $count = 1;
    $visible = 1;
  }
  ?>


<div class="postbox">
  <div class="inside">

  <div class="ranking-title">
    <?php
    echo '<h2>';
    generate_label_tag('title', __('タイトル（※必須）', THEME_NAME) );
    echo '</h2>';
    generate_textbox_tag('title', $title,  __('タイトルを入力',THEME_NAME ));
    generate_tips_tag(__( 'ランキングを識別するためのタイトルを入力してください。', THEME_NAME ));
    echo '<br>';

    //TinyMCE表示
    generate_checkbox_tag('visible' , $visible, __( 'ビジュアルエディターのリストに表示', THEME_NAME ));
    generate_tips_tag(__( 'エディターのドロップダウンリストに表示しなくて良い場合は、無効にしてください。', THEME_NAME ));
    echo '<br>';

    submit_button('保存');
    echo '<br>';
    ?>
  </div>

  <?php
  // var_dump('$count:'.$count);
  // var_dump('$action:'.$action);
  // var_dump('$id:'.$id);
  for ($i = 1; $i <= $count; $i++):

    //var_dump($i);
    //$index = $i - 1;
    $name = isset($items[$i]['name']) ? esc_attr($items[$i]['name']) : '';
    $rating = !empty($items[$i]['rating']) ? $items[$i]['rating'] : 'none';
    $image_tag = isset($items[$i]['image_tag']) ? $items[$i]['image_tag'] : '';
    $description = isset($items[$i]['description']) ? $items[$i]['description'] : '';
    $detail_url = isset($items[$i]['detail_url']) ? $items[$i]['detail_url'] : '';
    $link_url = isset($items[$i]['link_url']) ? $items[$i]['link_url'] : '';
    $link_tag = isset($items[$i]['link_tag']) ? $items[$i]['link_tag'] : '';
    $submit_text = ($i == $count) ? __( '追加', THEME_NAME ) : __( '変更を保存', THEME_NAME );

    //var_dump($items);
   ?>

    <div class="ranking-item demo">
      <div class="ranking-item-name">
        <div class="ranking-item-name-crown">
          <?php generate_ranking_crown_tag($i); ?>
        </div>
        <div class="ranking-item-name-text">
        <?php
        //generate_label_tag('', __('名前：', THEME_NAME) );
        generate_textbox_tag('item_ranking['.$i.'][name]', $name,  __('商品名等、見出しとなる名前を入力してください（※必須）',THEME_NAME ));
        generate_tips_tag(__( '項目の名前です。タグ・ショートコード入力も可能です。', THEME_NAME ));
         ?>
        </div>

      </div>
      <div class="ranking-item-rating">
      <label style="float:left;"><?php _e('評価：', THEME_NAME); ?></label>
        <?php
        $options = array(
          'none' => __( 'なし', THEME_NAME ),
          '0.0'    => __( '0',    THEME_NAME ),
          '0.5'  => __( '0.5',  THEME_NAME ),
          '1.0'    => __( '1',    THEME_NAME ),
          '1.5'  => __( '1.5',  THEME_NAME ),
          '2.0'    => __( '2',    THEME_NAME ),
          '2.5'  => __( '2.5',  THEME_NAME ),
          '3.0'    => __( '3',    THEME_NAME ),
          '3.5'  => __( '3.5',  THEME_NAME ),
          '4.0'    => __( '4',    THEME_NAME ),
          '4.5'  => __( '4.5',  THEME_NAME ),
          '5.0'    => __( '5',    THEME_NAME ),
        );
        //_v($rating);
        generate_radiobox_tag('item_ranking['.$i.'][rating]', $options, $rating);
        generate_tips_tag(__( '商品等を星の数で評価します。「なし」を選択した場合は表示されません。', THEME_NAME ));
        ?>
      </div>
      <div class="ranking-item-img-desc">
        <div class="ranking-item-image-tag">
          <?php
          generate_label_tag('', __('画像・バナータグ', THEME_NAME) );
          echo '<br>';
          generate_textarea_tag('item_ranking['.$i.'][image_tag]', $image_tag, __( '商品画像、もしくはバナーのタグを入力してください。ショートコード入力も可能です。', THEME_NAME ), 5) ;
          generate_tips_tag(__( 'イメージ画像のタグを入力してください。※入力していない場合は表示されません。', THEME_NAME ));
          ?>
        </div>
        <div class="ranking-item-description">
          <?php
          generate_label_tag('', __('説明文（※必須）', THEME_NAME) );
          echo '<br>';
          generate_textarea_tag('item_ranking['.$i.'][description]', $description,  '商品等の説明文を入力してください。', 5);
          generate_tips_tag(__( '紹介文を入力してください。タグ・ショートコード入力も可能です。', THEME_NAME ));
           ?>
        </div>
      </div>
      <div class="ranking-item-link-buttons">
        <div class="ranking-item-detail-url">
          <?php
          generate_label_tag('', __('詳細ページURL', THEME_NAME) );
          echo '<br>';
          generate_textbox_tag('item_ranking['.$i.'][detail_url]', $detail_url,  __('http://',THEME_NAME ));
          generate_tips_tag(__( '詳細ページへリンクするためのURLを入力してください。', THEME_NAME ));
          ?>
        </div>
        <div class="ranking-item-link-tag">
          <?php
          generate_label_tag('', __('ページURL', THEME_NAME) );
          echo '<br>';
          generate_textbox_tag('item_ranking['.$i.'][link_url]', $link_url,  __('http://',THEME_NAME ));
          generate_tips_tag(__( '公式ページ等のURLを入力してください。「リンクタグ」と双方入力されている場合はこちらが優先されます。', THEME_NAME ));

          $style = $link_tag ? ' style="display: block;"' : '';
          echo '<span class="toggle"><span class="toggle-link">'.__( 'タグで入力', THEME_NAME ).'</span><div class="toggle-content"'.$style.'>';

          generate_label_tag('', __('リンクタグ', THEME_NAME) );
          echo '<br>';
          generate_textarea_tag('item_ranking['.$i.'][link_tag]', $link_tag, __( '公式ページ等のリンク（アフィリエイト）タグを入力してください。', THEME_NAME ), 3) ;
          generate_tips_tag(__( 'アフィリエイトタグを直接入力する場合。タグ変更が無効なASP用設定です。ショートコードも入力できます。', THEME_NAME ));

          echo '</div></span>'
          ?>
        </div>
      </div>
      <div class="opration-area">
        <div class="opration-submit">
          <?php submit_button($submit_text); ?>
        </div>

        <div class="opration-menu-links">
          <?php //アイテム移動リンクなど
          //先頭と最後にはアイテム移動リンクを表示しないための判別
          $is_move_link_visible = ($i != 1) && ($i != $count);
          if ($is_move_link_visible):
            $move_url = add_query_arg(
              array(
                'action' => 'move',
                'id' => $id,
                'from' => $i,
                'to' => $i - 1,
              )
            );
           ?>
            <a href="<?php echo $move_url; ?>"><?php _e( 'アイテムを上に移動', THEME_NAME ) ?></a>
          <?php endif ?>
          <?php //削除リンク
          //最後は削除しないための判別
          $is_delete_link_visible = ($i != $count);
          if ($is_delete_link_visible):
            $delete_url = add_query_arg(
              array(
                'action' => 'item_delete',
                'id' => $id,
                'del_no' => $i,
                'conf_no' => $i,
              )
            ); ?>
            <a href="<?php echo $delete_url; ?>" onclick="if(!confirm('本当に削除してもいいですか？'))return false"><?php _e( '削除', THEME_NAME ) ?></a>
          <?php endif ?>
        </div>

      </div>

    </div>

  <?php endfor ?>

  <input type="hidden" name="count" value="<?php echo $count; ?>">
  <input type="hidden" name="action" value="<?php echo $action; ?>">
  <input type="hidden" name="id" value="<?php echo $id; ?>">
  <input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="<?php echo wp_create_nonce('item-ranking');?>">

  </div>
</div>
</form>
