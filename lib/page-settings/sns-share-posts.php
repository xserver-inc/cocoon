<?php //SNSシェア設定をデータベースに保存

//トップシェアボタンの設定保存
require_once 'sns-share-posts-top.php';
//ボトムシェアボタンの設定保存
require_once 'sns-share-posts-bottom.php';

//ツイートにメンションを含める
update_theme_option(OP_TWITTER_ID_INCLUDE);
//ツイート後にフォローを促す
update_theme_option(OP_TWITTER_RELATED_FOLLOW_ENABLE);
//ツイートに含めるハッシュタグ
update_theme_option(OP_TWITTER_HASH_TAG);

//写真をPinterestで共有する
update_theme_option(OP_PINTEREST_SHARE_BUTTON_VISIBLE);

//SNSシェア数キャッシュ有効
update_theme_option(OP_SNS_SHARE_COUNT_CACHE_ENABLE);
//SNSシェア数キャッシュ取得間隔
update_theme_option(OP_SNS_SHARE_COUNT_CACHE_INTERVAL);

