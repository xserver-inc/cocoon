<?php //オリジナル設定ページ

//管理画面の設定の隠しフィールド名
define('HIDDEN_FIELD_NAME', strtolower(THEME_NAME).'_submit_hidden');
//入力ボックスの横幅
define('DEFAULT_INPUT_COLS', 60);
//入力ボックスの縦幅
define('DEFAULT_INPUT_ROWS', 10);

//広告設定用の定数と関数の呼び出し
require_once 'original-pages/ads-funcs.php';
//アクセス解析設定用の定数と関数の呼び出し
require_once 'original-pages/analytics-funcs.php';


// ユーザーが何か情報を POST したかどうかを確認
// POST していれば、隠しフィールドに 'Y' が設定されている
if( isset($_POST[HIDDEN_FIELD_NAME]) &&
    $_POST[HIDDEN_FIELD_NAME] == 'Y' ):

  //広告設定用の保存
  require_once 'original-pages/ads-posts.php';
  //アクセス解析設定用の保存
  require_once 'original-pages/analytics-posts.php';


//画面に「設定は保存されました」メッセージを表示
?>
<div class="updated">
  <p>
    <strong>
      <?php _e('設定は保存されました。', THEME_NAME ); ?>
    </strong>
  </p>
</div>
<?php
endif;
?>
<div class="wrap">
<h1><?php _e( SETTING_NAME_TOP, THEME_NAME ) ?></h1>
<?php var_dump($_POST) ?>
<form name="form1" method="post" action="" class="admin-settings">

<!-- タブ機能の実装 -->
<div id="tabs">
  <ul>
    <li class="ads"><?php _e( '広告', THEME_NAME ) ?></li>
    <li class="title"><?php _e( 'タイトル', THEME_NAME ) ?></li>
    <li class="seo"><?php _e( 'SEO', THEME_NAME ) ?></li>
    <li class="analytics"><?php _e( 'アクセス解析', THEME_NAME ) ?></li>
    <li class="image"><?php _e( '画像', THEME_NAME ) ?></li>
    <li class="code"><?php _e( 'ブログカード（内部）', THEME_NAME ) ?></li>
    <li class="code"><?php _e( 'ブログカード（外部）', THEME_NAME ) ?></li>
    <li class="code"><?php _e( 'コード', THEME_NAME ) ?></li>
    <li class="amp"><?php _e( 'AMP', THEME_NAME ) ?></li>
    <li class="amp"><?php _e( '高速化', THEME_NAME ) ?></li>
    <li class="admin"><?php _e( '管理者画面', THEME_NAME ) ?></li>
    <li class="other"><?php _e( 'その他', THEME_NAME ) ?></li>
  </ul>

  <!-- 広告タブ -->
  <div class="ads metabox-holder">
    <?php require_once 'original-pages/ads-forms.php'; ?>
  </div><!-- /.metabox-holder -->

  <!-- タイトルタブ -->
  <div class="title metabox-holder">

  </div><!-- /.metabox-holder -->

  <!-- SEOタブ -->
  <div class="seo metabox-holder">

  </div><!-- /.metabox-holder -->

  <!-- アクセス解析 -->
  <div class="analytics metabox-holder">
    <?php require_once 'original-pages/analytics-forms.php'; ?>
  </div><!-- /.metabox-holder -->

</div><!-- /#tabs -->

<input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="Y">

<?php submit_button(); ?>


</form>
</div>
