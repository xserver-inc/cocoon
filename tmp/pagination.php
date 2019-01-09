<?php //「次のページ」ページネーション
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//現在のページ番号
global $paged;
if(empty($paged)) $paged = 1;

//ページ情報の取得
global $wp_query;
//全ページ数
$pages = $wp_query->max_num_pages;
if(!$pages){
  $pages = 1;
}

//ページが1ページしかない場合は出力しない
if($pages != 1) {
  //次のページ番号
  if ( $pages == $paged ) {
    $next_page_num = $paged;
  } else {
    $next_page_num = $paged + 1;
  }

  //現在のページ番号が全ページ数よりも少ないときは「次のページ」タグを出力
  if ( $paged < $pages ) {
    echo '<div class="pagination-next"><a href="'.get_pagenum_link($next_page_num).'" class="pagination-next-link key-btn">'.__( '次のページ', THEME_NAME ).'</a></div>';
  }

}
?>


<div class="pagination">
  <?php global $wp_rewrite;
  $paginate_base = get_pagenum_link(1);
  if(strpos($paginate_base, '?') || ! $wp_rewrite->using_permalinks()){
    $paginate_format = '';
    $paginate_base = add_query_arg('paged','%#%');
  }
  else{
    $paginate_format = (substr($paginate_base,-1,1) == '/' ? '' : '/') .
    user_trailingslashit('page/%#%/','paged');;
    $paginate_base .= '%_%';
  }

  echo paginate_links(array(
    'base' => $paginate_base,
    'format' => $paginate_format,
    'total' => $wp_query->max_num_pages,
    'mid_size' => 2,
    'current' => ($paged ? $paged : 1),
    'prev_text' => '',
    'next_text' => '',
  )); ?>
</div><!-- /.pagination -->
