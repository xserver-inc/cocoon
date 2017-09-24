<?php //画像用の関数

//Lightboxのようなギャラリー系のjQueryプラグインが動作しているか
if ( !function_exists( 'is_lightbox_plugin_exist' ) ):
function is_lightbox_plugin_exist($content){
  //lity
  if ( false !== strpos( $content, 'data-lity="' ) )
    return true;
  //Lightbox
  if ( false !== strpos( $content, 'data-lightbox="image-set"' ) )
    return true;

  return false;
}
endif;


//画像リンクのAタグをLightboxに対応するように付け替え
if ( is_lightbox_effect_enable() ) {
  add_filter( 'the_content', 'add_lightbox_property', 9 );
}
if ( !function_exists( 'add_lightbox_property' ) ):
function add_lightbox_property( $content ) {
  //プレビューやフィードで表示しない
  if( is_feed() )
    return $content;

  //既に適用させているところは処理しない
  //if ( false !== strpos( $content, 'data-lightbox="image-set"' ) )
  if ( is_lightbox_plugin_exist($content) )
    return $content;

  //Aタグを正規表現で置換
  $content = preg_replace(
    '/<a([^>]+?(\.jpe?g|\.png|\.gif)[\'\"][^>]*?)>([\s\w\W\d]+?)<\/a>/i',//Aタグの正規表現
    '<a${1} data-lightbox="image-set">${3}</a>',//置換する
    $content );//投稿本文（置換する文章）

  return $content;
}

endif;
