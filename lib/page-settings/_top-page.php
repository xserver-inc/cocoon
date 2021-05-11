<?php //オリジナル設定ページ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

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

  ///////////////////////////////////////////
  // テーマ設定ページではスキン設定の読み込みを保存後にするために遅らせる
  ///////////////////////////////////////////
  if (get_skin_url() && is_admin_php_page()) {
    require_once get_template_directory().'/lib/skin.php';   //スキン
  }

  ///////////////////////////////////////
  // エディター用のカスタマイズCSS出力
  ///////////////////////////////////////
  put_theme_css_cache_file();

  do_action('cocoon_settings_after_save');

endif;

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


///////////////////////////////////////
// 入力フォーム
///////////////////////////////////////
?>
<div class="wrap admin-settings">
<h1><?php _e( SETTING_NAME_TOP, THEME_NAME ) ?></h1>
<p><?php _e( 'Cocoonの設定全般についてはマニュアルを参照してください。', THEME_NAME ) ?><a href="https://wp-cocoon.com/manual/" target="_blank" rel="noopener"><?php echo change_fa('<span class="fa fa-book" aria-hidden="true">'); ?></span>
<?php _e( 'テーマ利用マニュアル', THEME_NAME ) ?></a></p>
<?php //var_dump($_POST) ?>
<form name="form1" method="post" action="<?php echo add_query_arg(array('reset' => null)); ?>" class="admin-settings">

<!-- タブ機能の実装 -->
<div id="tabs" class="tabs">
  <ul>
    <li class="skin"><?php _e( 'スキン', THEME_NAME ) ?></li>
    <li class="all"><?php _e( '全体', THEME_NAME ) ?></li>
    <li class="theme-header"><?php _e( 'ヘッダー', THEME_NAME ) ?></li>
    <li class="ads"><?php _e( '広告', THEME_NAME ) ?></li>
    <li class="title"><?php _e( 'タイトル', THEME_NAME ) ?></li>
    <li class="seo"><?php _e( 'SEO', THEME_NAME ) ?></li>
    <li class="ogp"><?php _e( 'OGP', THEME_NAME ) ?></li>
    <li class="analytics"><?php _e( 'アクセス解析・認証', THEME_NAME ) ?></li>
    <li class="column"><?php _e( 'カラム', THEME_NAME ) ?></li>
    <li class="index-page"><?php _e( 'インデックス', THEME_NAME ) ?></li>
    <li class="single-page"><?php _e( '投稿', THEME_NAME ) ?></li>
    <li class="page-page"><?php _e( '固定ページ', THEME_NAME ) ?></li>
    <li class="content-page"><?php _e( '本文', THEME_NAME ) ?></li>
    <li class="toc-page"><?php _e( '目次', THEME_NAME ) ?></li>
    <li class="sns-share"><?php _e( 'SNSシェア', THEME_NAME ) ?></li>
    <li class="sns-follow"><?php _e( 'SNSフォロー', THEME_NAME ) ?></li>
    <li class="image"><?php _e( '画像', THEME_NAME ) ?></li>
    <li class="blog-card-in"><?php _e( 'ブログカード', THEME_NAME ) ?></li>
    <li class="code-highlight"><?php _e( 'コード', THEME_NAME ) ?></li>
    <li class="comment"><?php _e( 'コメント', THEME_NAME ) ?></li>
    <li class="notice-area"><?php _e( '通知', THEME_NAME ) ?></li>
    <li class="appeal-area"><?php _e( 'アピールエリア', THEME_NAME ) ?></li>
    <li class="recommended"><?php _e( 'おすすめカード', THEME_NAME ) ?></li>
    <li class="carousel"><?php _e( 'カルーセル', THEME_NAME ) ?></li>
    <li class="footer"><?php _e( 'フッター', THEME_NAME ) ?></li>
    <li class="buttons"><?php _e( 'ボタン', THEME_NAME ) ?></li>
    <li class="mobile-buttons"><?php _e( 'モバイル', THEME_NAME ) ?></li>
    <li class="page-404"><?php _e( '404ページ', THEME_NAME ) ?></li>
    <li class="amp"><?php _e( 'AMP', THEME_NAME ) ?></li>
    <li class="pwa"><?php _e( 'PWA', THEME_NAME ) ?></li>
    <li class="admin"><?php _e( '管理者画面', THEME_NAME ) ?></li>
    <li class="widget"><?php _e( 'ウィジェット', THEME_NAME ) ?></li>
    <li class="widget-area"><?php _e( 'ウィジェットエリア', THEME_NAME ) ?></li>
    <li class="editor"><?php _e( 'エディター', THEME_NAME ) ?></li>
    <li class="apis"><?php _e( 'API', THEME_NAME ) ?></li>
    <li class="others"><?php _e( 'その他', THEME_NAME ) ?></li>
    <li class="reset"><?php _e( 'リセット', THEME_NAME ) ?></li>
    <li class="about"><?php _e( 'テーマ情報', THEME_NAME ) ?></li>
  </ul>

  <?php submit_button(__( '変更をまとめて保存', THEME_NAME )); ?>

  <?php //スキン制御変数のクリアとバックアップ
  clear_global_skin_theme_options(); ?>

  <!-- スキン -->
  <div class="skin metabox-holder">
    <?php require_once abspath(__FILE__).'skin-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 全体タブ -->
  <div class="all metabox-holder">
    <?php require_once abspath(__FILE__).'all-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- ヘッダータブ -->
  <div class="theme-header metabox-holder">
    <?php require_once abspath(__FILE__).'header-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 広告タブ -->
  <div class="ads metabox-holder">
    <?php require_once abspath(__FILE__).'ads-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- タイトルタブ -->
  <div class="title metabox-holder">
    <?php require_once abspath(__FILE__).'title-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- SEOタブ -->
  <div class="seo metabox-holder">
    <?php require_once abspath(__FILE__).'seo-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- OGP -->
  <div class="ogp metabox-holder">
    <?php require_once abspath(__FILE__).'ogp-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- アクセス解析 -->
  <div class="analytics metabox-holder">
    <?php require_once abspath(__FILE__).'analytics-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- カラム -->
  <div class="column metabox-holder">
    <?php require_once abspath(__FILE__).'column-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- インデックス -->
  <div class="index-page metabox-holder">
    <?php require_once abspath(__FILE__).'index-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 投稿 -->
  <div class="single-page metabox-holder">
    <?php require_once abspath(__FILE__).'single-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 固定ページ -->
  <div class="page-page metabox-holder">
    <?php require_once abspath(__FILE__).'page-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 本文 -->
  <div class="content-page metabox-holder">
    <?php require_once abspath(__FILE__).'content-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 目次 -->
  <div class="toc-page metabox-holder">
    <?php require_once abspath(__FILE__).'toc-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- SNSシェアタブ -->
  <div class="sns-share metabox-holder">
    <?php require_once abspath(__FILE__).'sns-share-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- SNSフォロータブ -->
  <div class="sns-follow metabox-holder">
    <?php require_once abspath(__FILE__).'sns-follow-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 画像タブ -->
  <div class="image metabox-holder">
    <?php require_once abspath(__FILE__).'image-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 内部・外部ブログカード -->
  <div class="blog-card-in metabox-holder">
    <?php require_once abspath(__FILE__).'blogcard-in-forms.php'; ?>
    <?php require_once abspath(__FILE__).'blogcard-out-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- コードタブ -->
  <div class="code-highlight metabox-holder">
    <?php require_once abspath(__FILE__).'code-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- コメントタブ -->
  <div class="comment metabox-holder">
    <?php require_once abspath(__FILE__).'comment-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 通知エリア -->
  <div class="notice-area metabox-holder">
    <?php require_once abspath(__FILE__).'notice-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- アピールエリア -->
  <div class="appeal-area metabox-holder">
    <?php require_once abspath(__FILE__).'appeal-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- おすすめカード -->
  <div class="recommended metabox-holder">
    <?php require_once abspath(__FILE__).'recommended-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- カルーセル -->
  <div class="carousel-area metabox-holder">
    <?php require_once abspath(__FILE__).'carousel-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- フッター -->
  <div class="footer metabox-holder">
    <?php require_once abspath(__FILE__).'footer-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- ボタン -->
  <div class="buttons metabox-holder">
    <?php require_once abspath(__FILE__).'buttons-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- モバイルボタン -->
  <div class="mobile-buttons metabox-holder">
    <?php require_once abspath(__FILE__).'mobile-buttons-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 404ページ -->
  <div class="page-404 metabox-holder">
    <?php require_once abspath(__FILE__).'404-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- AMP -->
  <div class="amp metabox-holder">
    <?php require_once abspath(__FILE__).'amp-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- PWA -->
  <div class="pwa metabox-holder">
    <?php require_once abspath(__FILE__).'pwa-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- 管理画面 -->
  <div class="admin metabox-holder">
    <?php require_once abspath(__FILE__).'admin-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- ウィジェット -->
  <div class="widget metabox-holder">
    <?php require_once abspath(__FILE__).'widget-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- ウィジェットエリア -->
  <div class="widget-area metabox-holder">
    <?php require_once abspath(__FILE__).'widget-area-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- エディター -->
  <div class="editor metabox-holder">
    <?php require_once abspath(__FILE__).'editor-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- API -->
  <div class="apis metabox-holder">
    <?php require_once abspath(__FILE__).'apis-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- その他 -->
  <div class="others metabox-holder">
    <?php require_once abspath(__FILE__).'others-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- リセット -->
  <div class="reset metabox-holder">
    <?php require_once abspath(__FILE__).'reset-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- テーマ情報 -->
  <div class="theme-about metabox-holder">
    <?php require_once abspath(__FILE__).'about-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <?php //スキン制御変数の復元
  restore_global_skin_theme_options(); ?>

</div><!-- /#tabs -->
<input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="<?php echo wp_create_nonce('settings');?>">
<input type="hidden" id="<?php echo SELECT_INDEX_NAME; ?>" name="<?php echo SELECT_INDEX_NAME; ?>" value="<?php echo ($_POST && $_POST[SELECT_INDEX_NAME] ? $_POST[SELECT_INDEX_NAME] : 0); ?>">

<?php submit_button(__( '変更をまとめて保存', THEME_NAME )); ?>


</form>
</div>

<style>
  <?php get_template_part('tmp/css-custom'); ?>
</style>
