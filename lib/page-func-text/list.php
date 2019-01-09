<?php //テンプレートリスト
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//var_dump($_POST);
$keyword = !empty($_POST['s']) ? $_POST['s'] : null;
$order_by = isset($_POST['order']) ? $_POST['order'] : 'date DESC';
//var_dump($order_by);
$records = get_function_texts($keyword, $order_by);
//var_dump($records);
//並び替えオプション
generate_sort_options_tag($keyword, $order_by);
?>
<!-- メッセージ -->
<?php if ($records): ?>
  <p><?php _e( 'ショートコードをコピーして本文の表示したい部分に貼り付けてください。', THEME_NAME );
  echo get_help_page_tag('https://wp-cocoon.com/how-to-use-template-text/'); ?></p>
<?php else: ?>
  <p><?php _e( '「使いまわしテキスト」を作成するには「新規作成」リンクをクリックしてください。', THEME_NAME );
  echo get_help_page_tag('https://wp-cocoon.com/how-to-use-template-text/'); ?></p>
<?php endif ?>

<div class="snipet-list">
  <?php foreach ($records as $record):
  //var_dump($record);
  $edit_url   = add_query_arg(array('action' => 'edit',   'id' => $record->id));
  $delete_url = add_query_arg(array('action' => 'delete', 'id' => $record->id));
   ?>
    <div class="snipet-wrap">
      <div class="snipet-title">
        <a href="<?php echo $edit_url; ?>"><?php echo esc_html(stripslashes_deep($record->title)); ?></a>
      </div>
      <div class="snipet-short-code">
        <?php _e( 'ショートコード：', THEME_NAME ) ?><input type="text" name="" value="<?php echo get_function_text_shortcode($record->id); ?>">
      </div>
      <div class="snipet-content">
        <?php
        $text = strip_tags(stripslashes_deep($record->text));
        $text = mb_substr($text, 0, 200);;
        echo $text; ?>
      </div>
      <div class="snipet-menu">
        <?php if (!$record->visible): ?>
          <div class="snipet-menu-left">[<?php _e( '非表示', THEME_NAME ) ?>]</div>
        <?php endif ?>

        <a href="<?php echo $edit_url; ?>"><?php _e( '編集', THEME_NAME ) ?></a>
        <a href="<?php echo $delete_url; ?>"><?php _e( '削除', THEME_NAME ) ?></a>
      </div>
    </div>
  <?php endforeach ?>
</div>
