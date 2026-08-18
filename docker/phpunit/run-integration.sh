#!/usr/bin/env sh
# POSIXシェルが解釈できるLF改行のまま実行します。
set -eu

# WordPress 6.8の公式テスト基盤に合わせ、統合テスト用PHPUnitを通常テストから分離します。
integration_tools=/tmp/cocoon-integration-tools
mkdir -p "$integration_tools"
cp /workspace/docker/phpunit/composer.integration.json "$integration_tools/composer.json"
composer install --working-dir="$integration_tools" --prefer-dist --no-interaction --no-progress

test_root=/tmp/wordpress-develop
tests_dir="$test_root/tests/phpunit"
if [ ! -f "$tests_dir/includes/functions.php" ]; then
  wp_version=$(php -r 'require "/tmp/wordpress/wp-includes/version.php"; echo $wp_version;')
  mkdir -p "$test_root"
  git -C "$test_root" init
  git -C "$test_root" remote add origin https://github.com/WordPress/wordpress-develop.git
  git -C "$test_root" config core.sparseCheckout true
  git -C "$test_root" sparse-checkout set tests/phpunit/includes tests/phpunit/data
  if ! git -C "$test_root" fetch --depth=1 origin "refs/tags/$wp_version"; then
    wp_series=$(printf '%s' "$wp_version" | awk -F. '{print $1 "." $2}')
    git -C "$test_root" fetch --depth=1 origin "refs/tags/$wp_series"
  fi
  git -C "$test_root" checkout --detach FETCH_HEAD
fi

cp /workspace/docker/phpunit/wp-tests-config.php "$tests_dir/wp-tests-config.php"
WP_TESTS_PHPUNIT_POLYFILLS_PATH="$integration_tools/vendor/yoast/phpunit-polyfills" \
  WP_TESTS_DIR="$tests_dir" \
  "$integration_tools/vendor/bin/phpunit" -c /workspace/phpunit.integration.xml --testsuite integration
