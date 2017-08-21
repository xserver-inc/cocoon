<?php

//リンクのないカテゴリーの取得（複数）
function get_the_nolink_categories(){
  $categories = null;
  foreach((get_the_category()) as $category){
    $categories .= '<span class="category">'.$category->cat_name.'</span>';
  }
  return $categories;
}

//リンクのないカテゴリーの出力（複数）
function the_nolink_categories(){
  echo get_the_nolink_categories();
}


//リンクのないカテゴリーの取得
function get_the_nolink_category(){
  $categories = get_the_category();
  $category = $categories[0];
  return '<span class="category-label">'.$category->cat_name.'</span>';
}

//リンクのないカテゴリーの出力
function the_nolink_category(){
  echo get_the_nolink_category();
}