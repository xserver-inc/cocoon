#!/usr/bin/env sh
# POSIXシェル向けのLF改行の維持
set -eu

# WordPress公式テスト基盤に合わせた統合テスト用PHPUnitの分離
integration_tools=/tmp/cocoon-integration-tools
mkdir -p "$integration_tools"
cp /workspace/docker/phpunit/composer.integration.json "$integration_tools/composer.json"
composer install --working-dir="$integration_tools" --prefer-dist --no-interaction --no-progress

test_root=/tmp/wordpress-develop
tests_dir="$test_root/tests/phpunit"
if [ ! -f "$tests_dir/includes/functions.php" ]; then
  wp_version=$(php -r 'require "/tmp/wordpress/wp-includes/version.php"; echo $wp_version;')
  # 初版で省略される末尾.0の公式開発リポジトリ用タグ名への補完
  wp_tag=$(php -r '$version = $argv[1]; echo preg_match("/^\d+\.\d+$/", $version) ? $version . ".0" : $version;' "$wp_version")
  mkdir -p "$test_root"
  git -C "$test_root" init
  # 取得失敗後の再実行でも同じ公式リポジトリを参照する設定
  if git -C "$test_root" remote get-url origin >/dev/null 2>&1; then
    git -C "$test_root" remote set-url origin https://github.com/WordPress/wordpress-develop.git
  else
    git -C "$test_root" remote add origin https://github.com/WordPress/wordpress-develop.git
  fi
  git -C "$test_root" config core.sparseCheckout true
  git -C "$test_root" sparse-checkout set tests/phpunit/includes tests/phpunit/data
  if ! git -C "$test_root" fetch --depth=1 origin "refs/tags/$wp_tag"; then
    wp_series=$(printf '%s' "$wp_version" | awk -F. '{print $1 "." $2}')
    git -C "$test_root" fetch --depth=1 origin "refs/tags/$wp_series"
  fi
  git -C "$test_root" checkout --detach FETCH_HEAD
fi

cp /workspace/docker/phpunit/wp-tests-config.php "$tests_dir/wp-tests-config.php"
WP_TESTS_PHPUNIT_POLYFILLS_PATH="$integration_tools/vendor/yoast/phpunit-polyfills" \
  WP_TESTS_DIR="$tests_dir" \
  "$integration_tools/vendor/bin/phpunit" -c /workspace/phpunit.integration.xml --testsuite integration
