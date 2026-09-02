<?php //オリジナル設定ページ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;
require_once __DIR__ . '/settings-tabs.php';

// 表示するタブを確定し、権限とnonceを検証してから設定を保存する。
$cocoon_settings_tabs = cocoon_get_settings_tabs();
$is_post_ok = cocoon_is_settings_post_valid();
if( $is_post_ok ):
  //var_dump($_POST[OP_RESET_ALL_SETTINGS]);

  do_action('cocoon_settings_before_save');
  ///////////////////////////////////////
  // 設定の保存
  ///////////////////////////////////////
  //リセット
  require_once abspath(__FILE__).'reset-posts.php';
  //全体
  require_once abspath(__FILE__).'all-posts.php';
  //ヘッダー
  require_once abspath(__FILE__).'header-posts.php';
  //スキン
  require_once abspath(__FILE__).'skin-posts.php';
  //グローバルナビ
  require_once abspath(__FILE__).'navi-posts.php';
  //広告
  require_once abspath(__FILE__).'ads-posts.php';
  //タイトル
  require_once abspath(__FILE__).'title-posts.php';
  //SEO
  require_once abspath(__FILE__).'seo-posts.php';
  //OGP
  require_once abspath(__FILE__).'ogp-posts.php';
  //アクセス解析
  require_once abspath(__FILE__).'analytics-posts.php';
  //カラム
  require_once abspath(__FILE__).'column-posts.php';
  //インデックス
  require_once abspath(__FILE__).'index-posts.php';
  //投稿
  require_once abspath(__FILE__).'single-posts.php';
  //固定ページ
  require_once abspath(__FILE__).'page-posts.php';
  //本文
  require_once abspath(__FILE__).'content-posts.php';
  //目次
  require_once abspath(__FILE__).'toc-posts.php';
  //SNSシェア
  require_once abspath(__FILE__).'sns-share-posts.php';
  //SNSフォロー
  require_once abspath(__FILE__).'sns-follow-posts.php';
  //画像
  require_once abspath(__FILE__).'image-posts.php';
  //内部ブログカード
  require_once abspath(__FILE__).'blogcard-in-posts.php';
  //外部ブログカード
  require_once abspath(__FILE__).'blogcard-out-posts.php';
  //ソースコード
  require_once abspath(__FILE__).'code-posts.php';
  //コメント
  require_once abspath(__FILE__).'comment-posts.php';
  //通知
  require_once abspath(__FILE__).'notice-posts.php';
  //アピールエリア
  require_once abspath(__FILE__).'appeal-posts.php';
  //おすすめカード
  require_once abspath(__FILE__).'recommended-posts.php';
  //カルーセル
  require_once abspath(__FILE__).'carousel-posts.php';
  //フッター
  require_once abspath(__FILE__).'footer-posts.php';
  //ボタン
  require_once abspath(__FILE__).'buttons-posts.php';
  //モバイルボタン
  require_once abspath(__FILE__).'mobile-buttons-posts.php';
  //404ページ
  require_once abspath(__FILE__).'404-posts.php';
  //AMP
  require_once abspath(__FILE__).'amp-posts.php';
  //PWA
  require_once abspath(__FILE__).'pwa-posts.php';
  //管理画面
  require_once abspath(__FILE__).'admin-posts.php';
  //ウィジェット
  require_once abspath(__FILE__).'widget-posts.php';
  //ウィジェットエリア
  require_once abspath(__FILE__).'widget-area-posts.php';
  //エディター
  require_once abspath(__FILE__).'editor-posts.php';
  //API
  require_once abspath(__FILE__).'apis-posts.php';
  //その他
  require_once abspath(__FILE__).'others-posts.php';

  // 標準の保存順序を保ち、拡張設定もキャッシュの生成前に保存する。
  cocoon_save_settings_tab_extensions($cocoon_settings_tabs);

  ///////////////////////////////////////////
  // テーマ設定ページではスキン設定の読み込みを保存後にするために遅らせる
  ///////////////////////////////////////////
  if (get_skin_url() && is_admin_php_page()) {
    cocoon_skin_settings();  //スキン設定
  }

  ///////////////////////////////////////
  // エディター用のカスタマイズCSS出力
  ///////////////////////////////////////
  put_theme_css_cache_file();

  ///////////////////////////////////////
  // ads.txtの出力
  ///////////////////////////////////////
  put_ads_txt_file();

  do_action('cocoon_settings_after_save');

endif;

// 保存後のスキン設定や機能の有効状態を、今回表示するタブにも反映する。
if ($is_post_ok) {
  $cocoon_settings_tabs = cocoon_get_settings_tabs();
}
$cocoon_selected_tab = cocoon_get_selected_settings_tab($cocoon_settings_tabs);

///////////////////////////////////////
// 入力フォーム
///////////////////////////////////////
?>
<div class="wrap admin-settings">
<h1><?php _e( 'Cocoon 設定', THEME_NAME ) ?></h1>
<?php
//画面に「設定は保存されました」メッセージを表示
$is_reset_ok = isset($_GET['reset']) && $_GET['reset'];
if ($is_post_ok || $is_reset_ok):
?>
<div class="notice notice-success is-dismissible">
  <p>
    <strong>
      <?php
      $reset_msg = __( '設定はリセットされました。', THEME_NAME );
      if ($is_post_ok) {
        if (isset($_POST[OP_RESET_ALL_SETTINGS]) && isset($_POST[OP_CONFIRM_RESET_ALL_SETTINGS])) {
           echo $reset_msg;
         } else {
           _e('設定は保存されました。', THEME_NAME );
         }
       }

       if ($is_reset_ok) {
         echo $reset_msg;
       }
        ?>
    </strong>
  </p>
</div>
<?php
endif;
?>
<p><?php _e( 'Cocoonの設定全般についてはマニュアルを参照してください。', THEME_NAME ) ?><a href="https://wp-cocoon.com/manual/" target="_blank" rel="noopener"><?php echo change_fa('<span class="fa fa-book" aria-hidden="true">'); ?></span>
<?php _e( 'テーマ利用マニュアル', THEME_NAME ) ?></a></p>
<?php //var_dump($_POST) ?>
<form name="form1" method="post" action="<?php echo add_query_arg(array('reset' => null)); ?>" class="admin-settings">

<?php submit_button(__( '変更をまとめて保存', THEME_NAME )); ?>

<!-- タブ機能の実装 -->
<style id="cocoon-settings-tabs-css">
  <?php echo cocoon_get_settings_tabs_css($cocoon_settings_tabs); ?>
</style>
<div id="tabs" class="tabs">
  <?php foreach ($cocoon_settings_tabs as $cocoon_tab_id => $cocoon_tab): ?>
    <input id="tab-<?php echo esc_attr($cocoon_tab_id); ?>-input" value="tab-<?php echo esc_attr($cocoon_tab_id); ?>-input" class="tab-input" type="radio" name="tab-input"<?php echo $cocoon_tab_id === $cocoon_selected_tab ? ' checked="checked"' : ''; ?>>
    <label for="tab-<?php echo esc_attr($cocoon_tab_id); ?>-input" id="tab-<?php echo esc_attr($cocoon_tab_id); ?>-label" class="tab-<?php echo esc_attr($cocoon_tab_id); ?>-label tab-label"><?php echo esc_html($cocoon_tab['label']); ?></label>
  <?php endforeach; ?>

  <?php // スキン制御変数を一時的に外し、保存されている設定値で入力欄を描画する。
  clear_global_skin_theme_options(); ?>

  <?php foreach ($cocoon_settings_tabs as $cocoon_tab_id => $cocoon_tab): ?>
    <div id="tab-<?php echo esc_attr($cocoon_tab_id); ?>-content" class="<?php echo esc_attr($cocoon_tab_id); ?> metabox-holder">
      <?php cocoon_render_settings_tab_content($cocoon_tab_id, $cocoon_tab); ?>
    </div><!-- /.metabox-holder -->
  <?php endforeach; ?>

  <?php // すべての入力欄を描画してからスキン制御変数を復元する。
  restore_global_skin_theme_options(); ?>

</div><!-- /#tabs -->
<input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="<?php echo wp_create_nonce('settings');?>">
<?php
// 旧タブ用の選択番号は非負の整数にそろえ、不正な配列やHTMLを出力しない。
$cocoon_select_index = isset($_POST[SELECT_INDEX_NAME]) && is_scalar($_POST[SELECT_INDEX_NAME])
  ? max(0, (int) $_POST[SELECT_INDEX_NAME]) : 0;
?>
<input type="hidden" id="<?php echo esc_attr(SELECT_INDEX_NAME); ?>" name="<?php echo esc_attr(SELECT_INDEX_NAME); ?>" value="<?php echo esc_attr($cocoon_select_index); ?>">

<?php submit_button(__( '変更をまとめて保存', THEME_NAME ), 'primary', "submit-2"); ?>


</form>
</div>

<style>
  <?php cocoon_template_part('tmp/css-custom'); ?>
</style>
