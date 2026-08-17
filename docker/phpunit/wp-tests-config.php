<?php
define('ABSPATH', '/tmp/wordpress/');
define('DB_NAME', 'wordpress_test');
define('DB_USER', getenv('WORDPRESS_DB_USER') ?: 'wordpress');
define('DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD') ?: 'wordpress');
define('DB_HOST', getenv('WORDPRESS_DB_HOST') ?: 'mysql:3306');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');
$table_prefix = 'wptests_';
define('WP_TESTS_DOMAIN', 'example.org');
define('WP_TESTS_EMAIL', 'admin@example.org');
define('WP_TESTS_TITLE', 'Cocoon Integration Test');
define('WP_PHP_BINARY', 'php');
define('WPLANG', '');

// WordPress公式テスト基盤がPHPUnitの版差を吸収できるよう、互換ライブラリの場所を渡します。
define('WP_TESTS_PHPUNIT_POLYFILLS_PATH', getenv('WP_TESTS_PHPUNIT_POLYFILLS_PATH'));
