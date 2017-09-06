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
<form name="form1" method="post" action="">

<table class="form-table">
  <tbody>
    <tr>
      <th scope="row">
        <label for="<?php echo AD_ADS_VISIBLE_NAME; ?>">広告の表示</label>
      </th>
      <td>
        <input type="checkbox" name="<?php echo AD_ADS_VISIBLE_NAME; ?>" id="<?php echo AD_ADS_VISIBLE_NAME; ?>" value="1"<?php echo($ads_visible_val ? ' checked="checked"' : ''); ?>><?php _e("全ての広告を表示する",THEME_NAME ); ?>
        <p class="tips"><?php _e( '全ての広告の表示を切り替えます。', THEME_NAME ) ?></p>
      </td>

    </tr>
    <tr>
      <th scope="row">
        <label for="<?php echo AD_AD_CODE_NAME; ?>">広告コード</label>
      </th>
      <td>
        <textarea id="<?php echo AD_AD_CODE_NAME; ?>" name="<?php echo AD_AD_CODE_NAME; ?>" cols="46" rows="6" placeholder="AdSenseのレスポンシブコードを入力してください"><?php echo stripslashes_deep($ad_code_val); ?></textarea>
        <p class="tips"><?php _e( 'AdSenseのレスポンシブ広告コードを入力してください。', THEME_NAME ) ?></p>
      </td>

    </tr>
  </tbody>
</table>
<input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="Y">

<p class="submit">
<?php submit_button(); ?>
</p>

</form>
</div>
