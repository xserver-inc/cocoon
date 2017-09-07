<?php //オリジナル設定ページ

//管理画面の設定の隠しフィールド名
define('HIDDEN_FIELD_NAME', strtolower(THEME_NAME).'_submit_hidden');

//広告を全て表示するか
define('AD_ADS_VISIBLE_NAME', 'ads_visible');
$ads_visible_val = get_option(AD_ADS_VISIBLE_NAME);
//広告コード
define('AD_AD_CODE_NAME', 'ad_code');
$ad_code_val = stripslashes_deep(get_option(AD_AD_CODE_NAME));
//var_dump(htmlspecialchars($ad_code_val));


// ユーザーが何か情報を POST したかどうかを確認
// POST していれば、隠しフィールドに 'Y' が設定されている
if( isset($_POST[HIDDEN_FIELD_NAME]) &&
    $_POST[HIDDEN_FIELD_NAME] == 'Y' ) {

    //広告表示設定
    $ads_visible_val = isset($_POST[AD_ADS_VISIBLE_NAME]) ? $_POST[AD_ADS_VISIBLE_NAME] : null;
    update_option(AD_ADS_VISIBLE_NAME, $ads_visible_val);
    //広告コード
    $ad_code_val = $_POST[AD_AD_CODE_NAME];
    update_option(AD_AD_CODE_NAME, $ad_code_val);

//画面に「設定は保存されました」メッセージを表示
?>
<div class="updated"><p><strong><?php _e('設定は保存されました。', THEME_NAME ); ?></strong></p></div>
<?php
}
?>
<div class="wrap">
<h1><?php _e( SETTING_NAME_TOP, THEME_NAME ) ?></h1>
<?php var_dump($_POST) ?>
<form name="form1" method="post" action="" class="admin-settings">
<div id="tabs">
<ul>
  <li class="ads"><?php _e( '広告', THEME_NAME ) ?></li>
  <li><?php _e( 'タイトル', THEME_NAME ) ?></li>
  <li><?php _e( 'SEO', THEME_NAME ) ?></li>
</ul>

<div class="ads">
  <?php require_once 'original-pages/ads.php'; ?>
</div>

<div>
JavaScript is a prototype-based scripting language with dynamic typing and
has first-class functions. Its syntax was influenced by C.
JavaScript copies many names and naming conventions from Java,
but the two languages are otherwise unrelated and have very different semantics.
The key design principles within JavaScript are taken from the Self and Scheme programming languages.
It is a multi-paradigm language, supporting object-oriented, imperative,
and functional programming styles.
</div>

<div>
The application of JavaScript to uses outside of web pages—for example,
in PDF documents, site-specific browsers, and desktop widgets—is also significant.
Newer and faster JavaScript VMs and frameworks built upon them (notably Node.js)
have also increased the popularity of JavaScript for server-side web applications.
</div>
</div>



<input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="Y">
<p class="submit">
<?php submit_button(); ?>
</p>

</form>
</div>
