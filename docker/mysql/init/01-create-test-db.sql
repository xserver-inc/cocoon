-- WordPress 統合テスト用データベースの作成
-- （WP テストスイートは指定 DB の全テーブルを破棄するため、本番用とは別の専用 DB を用意する）
-- 注意: GRANT 先のユーザー名は既定値 'wordpress' 固定。WORDPRESS_DB_USER を変更した場合は
--       このファイルのユーザー名も合わせて変更すること（.sql では環境変数を参照できないため）。
CREATE DATABASE IF NOT EXISTS `wordpress_test` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON `wordpress_test`.* TO 'wordpress'@'%';
FLUSH PRIVILEGES;
