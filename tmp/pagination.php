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
//1ページかどうか
$is_1_page = ($pages != 1);

//ページが1ページしかない場合は出力しない
if($is_1_page) {
  //次のページ番号
  if ( $pages == $paged ) {
    $next_page_num = $paged;
  } else {
    $next_page_num = $paged + 1;
  }

  //現在のページ番号が全ページ数よりも少ないときは「次のページ」タグを出力
  if ( $paged < $pages ) {
    $url = get_pagenum_link($next_page_num);
    //$url = get_query_removed_url($url);
    // var_dump($url);
    echo '<div class="pagination-next"><a href="'.esc_url($url).'" class="pagination-next-link key-btn">'.apply_filters('pagination_next_link_caption', __( '次のページ', THEME_NAME )).'</a></div>';
  }

}

//ページが1ページしかない場合は出力しない
if($is_1_page):
?>
<div class="pagination">
  <?php global $wp_rewrite;
  $paginate_base = get_pagenum_link(1);
  if(strpos($paginate_base, '?') || ! $wp_rewrite->using_permalinks()){
    $paginate_format = '';
    $paginate_base = add_query_arg('paged','%#%', get_requested_url());
  }
  else{
    $pagenum_link = html_entity_decode( get_pagenum_link() );
    $url = get_pagenum_link(2);
    $string = str_replace(trailingslashit($pagenum_link), '', $url);
    $string = str_replace(user_trailingslashit('/2'), '/%#%/', $string);
    $paginate_format = (substr($paginate_base,-1,1) == '/' ? '' : '/') .
    user_trailingslashit($string, 'paged');
    $paginate_base .= '%_%';
  }

  if (!function_exists('pagination_number_custom')):
  function pagination_number_custom($formatted_number, $number) {
    // 6桁以上の場合、span.six-digits-or-moreタグで囲む
    if ( $number >= 100000 ) {
      return '<span class="six-digits-or-more">' . $number . '</span>';
    }
    // 5桁以上の場合、span.five-digits-or-moreタグで囲む
    if ( $number >= 10000 ) {
      return '<span class="five-digits-or-more">' . $number . '</span>';
    }
    return (string) $number;
  }
  endif;

  // カンマ区切りを無効にするため、`number_format_i18n` フィルターを無効にします
  add_filter('number_format_i18n', 'pagination_number_custom', 10, 2);

  echo paginate_links(array(
    'base' => $paginate_base,
    'format' => $paginate_format,
    'total' => $wp_query->max_num_pages,
    'mid_size' => 1,
    'current' => ($paged ? $paged : 1),
    'prev_text' => '<span class="screen-reader-text">'.__( '前へ', THEME_NAME ).'</span><span class="fa fa-angle-left" aria-hidden="true"></span>',
'next_text' => '<span class="screen-reader-text">'.__( '次へ', THEME_NAME ).'</span><span class="fa fa-angle-right" aria-hidden="true"></span>',
  ));
  remove_filter('number_format_i18n', 'pagination_number_custom', 10);

  ?>
</div><!-- /.pagination -->
<?php
endif;
