<?php //テンプレートリスト
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//var_dump($_POST);
// 検索キーワードとソート順をサニタイズ
$keyword = !empty($_POST['s']) ? sanitize_text_field($_POST['s']) : null;
$order_by = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : 'date DESC';
//var_dump($order_by);
//var_dump($order_by);

// 現在のページ番号を取得
$paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
// 1ページあたりの表示件数
$limit = 20; // 任意の数に変更可能
// オフセット計算
$offset = ($paged - 1) * $limit;

// レコード取得（ページネーション対応）
$records = get_function_texts($keyword, $order_by, $limit, $offset);

// 総レコード数取得
$total = get_function_texts_count($keyword);
// 総ページ数計算
$pages = ceil($total / $limit);

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

<!-- ページネーション -->
<div class="tablenav top">
  <div class="tablenav-pages">
    <?php
    $pagination_args = array(
      'base' => add_query_arg('paged', '%#%'),
      'format' => '',
      'total' => $pages,
      'current' => $paged,
      'prev_text' => __('&laquo;'),
      'next_text' => __('&raquo;'),
    );
    echo paginate_links($pagination_args);
    ?>
  </div>
</div>

<div class="snippet-list">
  <?php foreach ($records as $record):
  //var_dump($record);
  // URLをエスケープ
  $edit_url   = esc_url(add_query_arg(array('action' => 'edit',   'id' => $record->id)));
  $delete_url = esc_url(add_query_arg(array('action' => 'delete', 'id' => $record->id)));
   ?>
    <div class="snippet-wrap">
      <div class="snippet-title">
        <a href="<?php echo $edit_url; ?>"><?php echo esc_html(stripslashes_deep($record->title)); ?></a>
      </div>
      <div class="snippet-short-code">
        <?php _e( 'ショートコード：', THEME_NAME ) ?><input type="text" name="" value="<?php echo get_function_text_shortcode($record->id); ?>">
      </div>
      <div class="snippet-content">
        <?php
        $text = strip_tags(stripslashes_deep($record->text));
        $text = mb_substr($text, 0, 200);;
        echo $text; ?>
      </div>
      <div class="snippet-menu">
        <?php if (!$record->visible): ?>
          <div class="snippet-menu-left">[<?php _e( '非表示', THEME_NAME ) ?>]</div>
        <?php endif ?>

        <a href="<?php echo $edit_url; ?>"><?php _e( '編集', THEME_NAME ) ?></a>
        <a href="<?php echo $delete_url; ?>"><?php _e( '削除', THEME_NAME ) ?></a>
      </div>
    </div>
  <?php endforeach ?>

</div>

<!-- ページネーション (下部) -->
<div class="tablenav bottom">
  <div class="tablenav-pages">
    <?php
    echo paginate_links($pagination_args);
    ?>
  </div>
</div>
