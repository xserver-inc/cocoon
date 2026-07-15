-- WordPress 統合テスト用データベースの作成
-- （WP テストスイートは指定 DB の全テーブルを破棄するため、本番用とは別の専用 DB を用意する）
CREATE DATABASE IF NOT EXISTS `wordpress_test` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON `wordpress_test`.* TO 'wordpress'@'%';
FLUSH PRIVILEGES;
