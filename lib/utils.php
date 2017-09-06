<?php

//リンクのないカテゴリーの取得（複数）
function get_the_nolink_categories(){
  $categories = null;
  foreach((get_the_category()) as $category){
    $categories .= '<span class="entry-category">'.$category->cat_name.'</span>';
  }
  return $categories;
}

//リンクのないカテゴリーの出力（複数）
function the_nolink_categories(){
  echo get_the_nolink_categories();
}

//カテゴリリンクの取得
function get_the_category_links(){
  $categories = null;
  foreach((get_the_category()) as $category){
    $categories .= '<a class="catlink" href="'.get_category_link( $category->cat_ID ).'">'.$category->cat_name.'</a>';
  }
  return $categories;
}

//カテゴリリンクの出力
function the_category_links(){
  echo get_the_category_links();
}

//リンクのないカテゴリーの取得
function get_the_nolink_category(){
  $categories = get_the_category();
  //var_dump($categories);
  if ( isset($categories[0]) ) {
    $category = $categories[0];
    return '<span class="category-label">'.$category->cat_name.'</span>';
  }
}

//リンクのないカテゴリーの出力
function the_nolink_category(){
  echo get_the_nolink_category();
}


//タグリンクの取得
function get_the_tag_links(){
  $tags = null;
  $posttags = get_the_tags();
  if ( $posttags ) {
    foreach(get_the_tags() as $tag){
      $tags .= '<a class="taglink" href="'.get_tag_link( $tag->term_id ).'">'.$tag->name.'</a>';
    }
  }
  return $tags;
}

//タグリンクの出力
function the_tag_links(){
  echo get_the_tag_links();
}

//コメントが許可されているか
function is_comment_allow(){
  global $post;
  if ( isset($post->comment_status) ) {
    return $post->comment_status == 'open';
  }
  return false;
}

//現在のカテゴリをカンマ区切りテキストで取得する
if ( !function_exists( 'get_category_ids' ) ):
function get_category_ids(){
  if ( is_single() ) {//投稿ページでは全カテゴリー取得
    $categories = get_the_category();
    $category_IDs = array();
    foreach($categories as $category):
      array_push( $category_IDs, $category -> cat_ID);
    endforeach ;
    return $category_IDs;
  } elseif ( is_category() ) {//カテゴリページではトップカテゴリーのみ取得
    $categories = get_the_category();
    $cat_now = $categories[0];
    return array( $cat_now->cat_ID );
  }
  return null;
}
endif;