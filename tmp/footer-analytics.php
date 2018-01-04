<?php //フッターのアクセス解析
if (!is_user_administrator() && !is_amp()) : //ログインしていないユーザーのみ適用 ?>
<?php //その他フッター様の解析コード
if (get_other_analytics_footer_tags()) {
  echo '<!-- Other Analytics -->'.PHP_EOL;
  echo get_other_analytics_footer_tags().PHP_EOL;
  echo '<!-- /Other Analytics -->'.PHP_EOL;
}?>
<?php endif; //!is_user_administrator() ?>