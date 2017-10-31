<?php //ツールチップ用のHTML置き場


//色選択のツールチップ
if ( !function_exists( 'genelate_select_colortooltip_tag' ) ):
function genelate_select_colortooltip_tag(){
  ob_start();?>
    <img src="https://simplicity.sample.mixh.jp/wp-content/uploads/2017/10/color-sample.png" alt="カラーサンプルサイト" />
    <p><?php _e( '良い色を選択するにはカラーサンプルサイトの利用をおすすめします。サイトから好みの色を見つけ出し、カラーコードをカラーピッカーにコピペすると設定できます。', THEME_NAME ) ?><br>
    <a href="http://www.color-sample.com/" target="_blank">color-sample.com</a></p>
  <?php
  $content = ob_get_clean();
  genelate_tooltip_tag($content);
}
endif;
if ( !function_exists( 'get_select_colortooltip_tag' ) ):
function get_select_colortooltip_tag(){
  ob_start();
  genelate_select_colortooltip_tag();
  $content = ob_get_clean();
  return $content;
}
endif;

