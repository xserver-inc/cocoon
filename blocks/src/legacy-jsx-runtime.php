<?php
/**
 * JSXランタイムが未登録の旧WordPress向けの補完処理
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!function_exists('cocoon_register_legacy_jsx_runtime')):
function cocoon_register_legacy_jsx_runtime(array $dependencies) {
  // ランタイムが必要で、WordPress側の登録がない場合だけの補完
  if (!in_array('react-jsx-runtime', $dependencies, true) || wp_script_is('react-jsx-runtime', 'registered')) {
    return;
  }

  $asset_file = require get_cocoon_template_directory() . '/blocks/dist/legacy-jsx-runtime.asset.php';
  wp_register_script(
    'react-jsx-runtime',
    get_cocoon_template_directory_uri() . '/blocks/dist/legacy-jsx-runtime.js',
    $asset_file['dependencies'],
    $asset_file['version']
  );
}
endif;
