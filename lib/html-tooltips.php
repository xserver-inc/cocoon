<?php //ツールチップ用のHTML置き場
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//ツールチップの生成
if ( !function_exists( 'generate_tooltip_tag' ) ):
function generate_tooltip_tag($content){?>
  <span class="tooltip fa fa-exclamation-triangle">
    <span class="tip-content">
      <?php echo $content; ?>
    </span>
  </span>
  <?php
}
endif;


//プレビューツールチップの生成
if ( !function_exists( 'generate_preview_tooltip_tag' ) ):
function generate_preview_tooltip_tag($url, $description = null, $width = null){
  $style = null;
  if ($width) {
    $style = ' style="width: '.$width.'px;"';
  }
  ?>
  <span class="tooltip fa fa-exclamation-triangle">
    <span class="tip-content"<?php echo $style; ?>>
      <img src="<?php echo $url; ?>" alt="">
      <?php if (!empty($description)): ?>
        <p><?php echo $description; ?></p>
      <?php endif ?>
    </span>
  </span>
  <?php
}
endif;


//ツールチップの生成
if ( !function_exists( 'generate_skin_preview_tag' ) ):
function generate_skin_preview_tag($url, $description = null, $width = 680){?>
  <span class="tooltip fa fa-picture-o">
    <span class="tip-content" style="width: <?php echo $width; ?>px;">
      <img src="<?php echo $url; ?>" alt="">
      <?php if (!empty($description)): ?>
        <p><?php echo $description; ?></p>
      <?php endif ?>
    </span>
  </span>
  <?php
}
endif;
if ( !function_exists( 'get_skin_preview_tag' ) ):
function get_skin_preview_tag($url, $description = null, $width = 680){
  ob_start();
  generate_skin_preview_tag($url, $description, $width);
  return ob_get_clean();
}
endif;
if ( !function_exists( 'get_image_preview_tag' ) ):
function get_image_preview_tag($url, $description = null, $width = 680){
  return get_skin_preview_tag($url, $description, $width);
}
endif;

//色選択のツールチップ
if ( !function_exists( 'generate_select_color_tip_tag' ) ):
function generate_select_color_tip_tag(){
  ob_start();?>
    <img src="https://simplicity.sample.mixh.jp/wp-content/uploads/2017/10/color-sample.png" alt="カラーサンプルサイト" />
    <p><?php _e( '良い色を選択するにはカラーサンプルサイトの利用をおすすめします。サイトから好みの色を見つけ出し、カラーコードをカラーピッカーにコピペすると設定できます。', THEME_NAME ) ?><br>
    <a href="http://www.color-sample.com/" target="_blank">color-sample.com</a></p>
  <?php
  $content = ob_get_clean();
  generate_tooltip_tag($content);
}
endif;
if ( !function_exists( 'get_select_color_tip_tag' ) ):
function get_select_color_tip_tag(){
  ob_start();
  generate_select_color_tip_tag();
  $content = ob_get_clean();
  return $content;
}
endif;


//サイドバートップ広告のツールチップ
if ( !function_exists( 'generate_sidebar_top_ad_tip_tag' ) ):
function generate_sidebar_top_ad_tip_tag(){
  ob_start();?>
    <img src="https://wp-cocoon.com/wp-content/uploads/2018/02/main-top-ad.png" alt="メインカラムトップの広告に注意" />
    <p><?php _e( '広告にグローバルナビメニューが覆いかぶさっていませんか？', THEME_NAME ) ?><br>
    <?php _e( '広告設定の前にAdSenseポリシーをご確認ください。', THEME_NAME ) ?><br>
    <a href="https://support.google.com/adsense/answer/48182?hl=<?php _e( 'ja', THEME_NAME ) ?>" target="_blank"><?php _e( 'AdSense プログラム ポリシー', THEME_NAME ) ?></a></p>
  <?php
  $content = ob_get_clean();
  generate_tooltip_tag($content);
}
endif;

//メインカラムトップ広告のツールチップ
if ( !function_exists( 'generate_main_column_top_ad_tip_tag' ) ):
function generate_main_column_top_ad_tip_tag(){
  ob_start();?>
    <img src="https://wp-cocoon.com/wp-content/uploads/2018/02/sidebar-ad.png" alt="サイドバートップの広告に注意" />
    <p><?php _e( '広告にグローバルナビメニューが覆いかぶさっていませんか？', THEME_NAME ) ?><br>
    <?php _e( '広告設定の前にAdSenseポリシーをご確認ください。', THEME_NAME ) ?><br>
    <a href="https://support.google.com/adsense/answer/48182?hl=<?php _e( 'ja', THEME_NAME ) ?>" target="_blank"><?php _e( 'AdSense プログラム ポリシー', THEME_NAME ) ?></a></p>
  <?php
  $content = ob_get_clean();
  generate_tooltip_tag($content);
}
endif;

