<?php //オリジナル設定ページ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;
// _v($_POST);
// ユーザーが何か情報を POST したかどうかを確認
// POST していれば、隠しフィールドに 'Y' が設定されている
$is_post_ok = isset($_POST[HIDDEN_FIELD_NAME]) &&
              wp_verify_nonce($_POST[HIDDEN_FIELD_NAME], 'settings');
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
  // //寄付
  // require_once abspath(__FILE__).'donation-posts.php';

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
<div class="updated">
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
<div id="tabs" class="tabs">
  <input id="tab-skin-input" value="tab-skin-input" class="tab-input" type="radio" name="tab-input" checked="checked">
  <label for="tab-skin-input" id="tab-skin-label" class="tab-skin-label tab-label"><?php _e( 'スキン', THEME_NAME ) ?></label>

  <input id="tab-all-input" value="tab-all-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-all-input" id="tab-all-label" class="tab-all-label tab-label"><?php _e( '全体', THEME_NAME ) ?></label>

  <input id="tab-theme-header-input" value="tab-theme-header-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-theme-header-input" id="tab-theme-header-label" class="tab-theme-header-label tab-label"><?php _e( 'ヘッダー', THEME_NAME ) ?></label>

  <input id="tab-ads-input" value="tab-ads-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-ads-input" id="tab-ads-label" class="tab-ads-label tab-label"><?php _e( '広告', THEME_NAME ) ?></label>

  <input id="tab-title-input" value="tab-title-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-title-input" id="tab-title-label" class="tab-title-label tab-label"><?php _e( 'タイトル', THEME_NAME ) ?></label>

  <input id="tab-seo-input" value="tab-seo-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-seo-input" id="tab-seo-label" class="tab-seo-label tab-label"><?php _e( 'SEO', THEME_NAME ) ?></label>

  <input id="tab-ogp-input" value="tab-ogp-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-ogp-input" id="tab-ogp-label" class="tab-ogp-label tab-label"><?php _e( 'OGP', THEME_NAME ) ?></label>

  <input id="tab-analytics-input" value="tab-analytics-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-analytics-input" id="tab-analytics-label" class="tab-analytics-label tab-label"><?php _e( 'アクセス解析・認証', THEME_NAME ) ?></label>

  <input id="tab-column-input" value="tab-column-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-column-input" id="tab-column-label" class="tab-column-label tab-label"><?php _e( 'カラム', THEME_NAME ) ?></label>

  <input id="tab-index-page-input" value="tab-index-page-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-index-page-input" id="tab-index-page-label" class="tab-index-page-label tab-label"><?php _e( 'インデックス', THEME_NAME ) ?></label>

  <input id="tab-single-page-input" value="tab-single-page-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-single-page-input" id="tab-single-page-label" class="tab-single-page-label tab-label"><?php _e( '投稿', THEME_NAME ) ?></label>

  <input id="tab-page-page-input" value="tab-page-page-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-page-page-input" id="tab-page-page-label" class="tab-page-page-label tab-label"><?php _e( '固定ページ', THEME_NAME ) ?></label>

  <input id="tab-content-page-input" value="tab-content-page-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-content-page-input" id="tab-content-page-label" class="tab-content-page-label tab-label"><?php _e( '本文', THEME_NAME ) ?></label>

  <input id="tab-toc-page-input" value="tab-toc-page-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-toc-page-input" id="tab-toc-page-label" class="tab-toc-page-label tab-label"><?php _e( '目次', THEME_NAME ) ?></label>

  <input id="tab-sns-share-input" value="tab-sns-share-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-sns-share-input" id="tab-sns-share-label" class="tab-sns-share-label tab-label"><?php _e( 'SNSシェア', THEME_NAME ) ?></label>

  <input id="tab-sns-follow-input" value="tab-sns-follow-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-sns-follow-input" id="tab-sns-follow-label" class="tab-sns-follow-label tab-label"><?php _e( 'SNSフォロー', THEME_NAME ) ?></label>

  <input id="tab-image-input" value="tab-image-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-image-input" id="tab-image-label" class="tab-image-label tab-label"><?php _e( '画像', THEME_NAME ) ?></label>

  <input id="tab-blog-card-input" value="tab-blog-card-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-blog-card-input" id="tab-blog-card-label" class="tab-blog-card-label tab-label"><?php _e( 'ブログカード', THEME_NAME ) ?></label>

  <input id="tab-code-highlight-input" value="tab-code-highlight-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-code-highlight-input" id="tab-code-highlight-label" class="tab-code-highlight-label tab-label"><?php _e( 'コード', THEME_NAME ) ?></label>

  <input id="tab-comment-input" value="tab-comment-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-comment-input" id="tab-comment-label" class="tab-comment-label tab-label"><?php _e( 'コメント', THEME_NAME ) ?></label>

  <input id="tab-notice-area-input" value="tab-notice-area-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-notice-area-input" id="tab-notice-area-label" class="tab-notice-area-label tab-label"><?php _e( '通知', THEME_NAME ) ?></label>

  <input id="tab-appeal-area-input" value="tab-appeal-area-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-appeal-area-input" id="tab-appeal-area-label" class="tab-appeal-area-label tab-label"><?php _e( 'アピールエリア', THEME_NAME ) ?></label>

  <input id="tab-recommended-input" value="tab-recommended-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-recommended-input" id="tab-recommended-label" class="tab-recommended-label tab-label"><?php _e( 'おすすめカード', THEME_NAME ) ?></label>

  <input id="tab-carousel-input" value="tab-carousel-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-carousel-input" id="tab-carousel-label" class="tab-carousel-label tab-label"><?php _e( 'カルーセル', THEME_NAME ) ?></label>

  <input id="tab-footer-input" value="tab-footer-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-footer-input" id="tab-footer-label" class="tab-footer-label tab-label"><?php _e( 'フッター', THEME_NAME ) ?></label>

  <input id="tab-buttons-input" value="tab-buttons-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-buttons-input" id="tab-buttons-label" class="tab-buttons-label tab-label"><?php _e( 'ボタン', THEME_NAME ) ?></label>

  <input id="tab-mobile-buttons-input" value="tab-mobile-buttons-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-mobile-buttons-input" id="tab-mobile-buttons-label" class="tab-mobile-buttons-label tab-label"><?php _e( 'モバイル', THEME_NAME ) ?></label>

  <input id="tab-page-404-input" value="tab-page-404-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-page-404-input" id="tab-page-404-label" class="tab-page-404-label tab-label"><?php _e( '404ページ', THEME_NAME ) ?></label>

  <?php if (is_amp_enable()): ?>
  <input id="tab-amp-input" value="tab-amp-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-amp-input" id="tab-amp-label" class="tab-amp-label tab-label"><?php _e( 'AMP', THEME_NAME ) ?></label>
  <?php endif; ?>

  <?php if (is_pwa_enable()): ?>
    <input id="tab-pwa-input" value="tab-pwa-input" class="tab-input" type="radio" name="tab-input">
    <label for="tab-pwa-input" id="tab-pwa-label" class="tab-pwa-label tab-label"><?php _e( 'PWA', THEME_NAME ) ?></label>
  <?php endif; ?>

  <input id="tab-admin-input" value="tab-admin-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-admin-input" id="tab-admin-label" class="tab-admin-label tab-label"><?php _e( '管理者画面', THEME_NAME ) ?></label>

  <input id="tab-widget-input" value="tab-widget-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-widget-input" id="tab-widget-label" class="tab-widget-label tab-label"><?php _e( 'ウィジェット', THEME_NAME ) ?></label>

  <input id="tab-widget-area-input" value="tab-widget-area-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-widget-area-input" id="tab-widget-area-label" class="tab-widget-area-label tab-label"><?php _e( 'ウィジェットエリア', THEME_NAME ) ?></label>

  <input id="tab-editor-input" value="tab-editor-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-editor-input" id="tab-editor-label" class="tab-editor-label tab-label"><?php _e( 'エディター', THEME_NAME ) ?></label>

  <input id="tab-apis-input" value="tab-apis-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-apis-input" id="tab-apis-label" class="tab-apis-label tab-label"><?php _e( 'API', THEME_NAME ) ?></label>

  <input id="tab-others-input" value="tab-others-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-others-input" id="tab-others-label" class="tab-others-label tab-label"><?php _e( 'その他', THEME_NAME ) ?></label>

  <input id="tab-reset-input" value="tab-reset-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-reset-input" id="tab-reset-label" class="tab-reset-label tab-label"><?php _e( 'リセット', THEME_NAME ) ?></label>

  <input id="tab-about-input" value="tab-about-input" class="tab-input" type="radio" name="tab-input">
  <label for="tab-about-input" id="tab-about-label" class="tab-about-label tab-label"><?php _e( 'テーマ情報', THEME_NAME ) ?></label>

  <?php //デフォルト状態の場合は何も処理せず最初のスキンタブを選択する
  if ($_POST && isset($_POST['tab-input'])): ?>
  <script>
    //できるだけ早くタブを選択するためにDOMの読み込みを待つのをコメントアウト
    // document.addEventListener("DOMContentLoaded", function() {
      //前回選択していたタブを選択
      document.getElementById('<?php echo esc_html($_POST['tab-input']); ?>').checked = true;
    // });
  </script>
  <?php endif; ?>


  <?php //スキン制御変数のクリアとバックアップ
  clear_global_skin_theme_options(); ?>

  <!-- スキン -->
  <div id="tab-skin-content" class="skin metabox-holder">
    <?php require_once abspath(__FILE__).'skin-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 全体タブ -->
  <div id="tab-all-content" class="all metabox-holder">
    <?php require_once abspath(__FILE__).'all-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- ヘッダータブ -->
  <div id="tab-theme-header-content" class="theme-header metabox-holder">
    <?php require_once abspath(__FILE__).'header-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 広告タブ -->
  <div id="tab-ads-content" class="ads metabox-holder">
    <?php require_once abspath(__FILE__).'ads-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- タイトルタブ -->
  <div id="tab-title-content" class="title metabox-holder">
    <?php require_once abspath(__FILE__).'title-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- SEOタブ -->
  <div id="tab-seo-content" class="seo metabox-holder">
    <?php require_once abspath(__FILE__).'seo-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- OGP -->
  <div id="tab-ogp-content" class="ogp metabox-holder">
    <?php require_once abspath(__FILE__).'ogp-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- アクセス解析 -->
  <div id="tab-analytics-content" class="analytics metabox-holder">
    <?php require_once abspath(__FILE__).'analytics-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- カラム -->
  <div id="tab-column-content" class="column metabox-holder">
    <?php require_once abspath(__FILE__).'column-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- インデックス -->
  <div id="tab-index-page-content" class="index-page metabox-holder">
    <?php require_once abspath(__FILE__).'index-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 投稿 -->
  <div id="tab-single-page-content" class="single-page metabox-holder">
    <?php require_once abspath(__FILE__).'single-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 固定ページ -->
  <div id="tab-page-page-content" class="page-page metabox-holder">
    <?php require_once abspath(__FILE__).'page-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 本文 -->
  <div id="tab-content-page-content" class="content-page metabox-holder">
    <?php require_once abspath(__FILE__).'content-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 目次 -->
  <div id="tab-toc-page-content" class="toc-page metabox-holder">
    <?php require_once abspath(__FILE__).'toc-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- SNSシェアタブ -->
  <div id="tab-sns-share-content" class="sns-share metabox-holder">
    <?php require_once abspath(__FILE__).'sns-share-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- SNSフォロータブ -->
  <div id="tab-sns-follow-content" class="sns-follow metabox-holder">
    <?php require_once abspath(__FILE__).'sns-follow-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 画像タブ -->
  <div id="tab-image-content" class="image metabox-holder">
    <?php require_once abspath(__FILE__).'image-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 内部・外部ブログカード -->
  <div id="tab-blog-card-content" class="blog-card metabox-holder">
    <?php require_once abspath(__FILE__).'blogcard-in-forms.php'; ?>
    <?php require_once abspath(__FILE__).'blogcard-out-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- コードタブ -->
  <div id="tab-code-highlight-content" class="code-highlight metabox-holder">
    <?php require_once abspath(__FILE__).'code-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- コメントタブ -->
  <div id="tab-comment-content" class="comment metabox-holder">
    <?php require_once abspath(__FILE__).'comment-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 通知エリア -->
  <div id="tab-notice-area-content" class="notice-area metabox-holder">
    <?php require_once abspath(__FILE__).'notice-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- アピールエリア -->
  <div id="tab-appeal-area-content" class="appeal-area metabox-holder">
    <?php require_once abspath(__FILE__).'appeal-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- おすすめカード -->
  <div id="tab-recommended-content" class="recommended metabox-holder">
    <?php require_once abspath(__FILE__).'recommended-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- カルーセル -->
  <div id="tab-carousel-content" class="carousel metabox-holder">
    <?php require_once abspath(__FILE__).'carousel-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- フッター -->
  <div id="tab-footer-content" class="footer metabox-holder">
    <?php require_once abspath(__FILE__).'footer-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- ボタン -->
  <div id="tab-buttons-content" class="buttons metabox-holder">
    <?php require_once abspath(__FILE__).'buttons-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- モバイルボタン -->
  <div id="tab-mobile-buttons-content" class="mobile-buttons metabox-holder">
    <?php require_once abspath(__FILE__).'mobile-buttons-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 404ページ -->
  <div id="tab-page-404-content" class="page-404 metabox-holder">
    <?php require_once abspath(__FILE__).'404-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <?php if (is_amp_enable()): ?>
   <!-- AMP -->
  <div id="tab-amp-content" class="amp metabox-holder">
    <?php require_once abspath(__FILE__).'amp-forms.php'; ?>
  </div><!-- /.metabox-holder -->
  <?php endif; ?>

  <?php if (is_pwa_enable()): ?>
  <!-- PWA -->
  <div id="tab-pwa-content" class="pwa metabox-holder">
    <?php require_once abspath(__FILE__).'pwa-forms.php'; ?>
  </div><!-- /.metabox-holder -->
  <?php endif; ?>

  <!-- 管理画面 -->
  <div id="tab-admin-content" class="admin metabox-holder">
    <?php require_once abspath(__FILE__).'admin-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- ウィジェット -->
  <div id="tab-widget-content" class="widget metabox-holder">
    <?php require_once abspath(__FILE__).'widget-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- ウィジェットエリア -->
  <div id="tab-widget-area-content" class="widget-area metabox-holder">
    <?php require_once abspath(__FILE__).'widget-area-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- エディター -->
  <div id="tab-editor-content" class="editor metabox-holder">
    <?php require_once abspath(__FILE__).'editor-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- API -->
  <div id="tab-apis-content" class="apis metabox-holder">
    <?php require_once abspath(__FILE__).'apis-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- その他 -->
  <div id="tab-others-content" class="others metabox-holder">
    <?php require_once abspath(__FILE__).'others-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- リセット -->
  <div id="tab-reset-content" class="reset metabox-holder">
    <?php require_once abspath(__FILE__).'reset-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- テーマ情報 -->
  <div id="tab-about-content" class="about metabox-holder">
    <?php require_once abspath(__FILE__).'about-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <?php //スキン制御変数の復元
  restore_global_skin_theme_options(); ?>

</div><!-- /#tabs -->
<input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="<?php echo wp_create_nonce('settings');?>">
<input type="hidden" id="<?php echo SELECT_INDEX_NAME; ?>" name="<?php echo SELECT_INDEX_NAME; ?>" value="<?php echo ($_POST && $_POST[SELECT_INDEX_NAME] ? $_POST[SELECT_INDEX_NAME] : 0);
// _v($_POST); ?>">

<?php submit_button(__( '変更をまとめて保存', THEME_NAME ), 'primary', "submit-2"); ?>


</form>
</div>

<style>
  <?php cocoon_template_part('tmp/css-custom'); ?>
</style>
