<?php
/**
 * 更新ZIP内の動作要件に基づくCocoonの更新前検証
 *
 * WordPressが展開した更新元のstyle.cssだけを読み、現在のWordPress・PHPと
 * 比較します。配信JSONの項目やインストール済みテーマの要件には依存しません。
 */
if ( !defined('ABSPATH') ) exit;

// 複数の参照パスからの読み込みによるクラス重複定義の防止
if (!class_exists('Cocoon_Theme_Update_Requirements', false)):
class Cocoon_Theme_Update_Requirements {
  private $directory;

  public function __construct($directory) {
    $this->directory = $directory;
    // 手動・一括・自動更新に共通するファイル置換前の検証
    add_filter('upgrader_source_selection', array($this, 'filter_source'), PHP_INT_MAX, 4);
  }

  public function filter_source($source, $remote_source, $upgrader, $hook_extra) {
    // 一括更新で省略されるtypeとactionを含む親テーマ更新の照合
    if (is_wp_error($source) || !is_array($hook_extra)
      || (isset($hook_extra['type']) && $hook_extra['type'] !== 'theme')
      || (isset($hook_extra['action']) && $hook_extra['action'] !== 'update')
      || ($hook_extra['theme'] ?? '') !== $this->directory) {
      return $source;
    }

    global $wp_filesystem, $wp_version;
    $contents = false;
    // FTP等の更新環境にも対応する展開元スタイルシートの読み取り
    if (is_string($source) && trim($source) !== '' && strpos($source, "\0") === false
      && is_object($wp_filesystem) && is_callable(array($wp_filesystem, 'get_contents'))) {
      $contents = $wp_filesystem->get_contents(rtrim($source, '/\\') . '/style.css');
    }
    $error = self::check_versions(self::parse_requirements($contents), $wp_version, PHP_VERSION);
    if (!is_wp_error($error)) {
      return $source;
    }

    // 標準画面の配列参照と自動更新の失敗記録を両立する結果の設定
    if ($upgrader instanceof Theme_Upgrader && !$upgrader->bulk) {
      $upgrader->result = $upgrader->skin instanceof Automatic_Upgrader_Skin ? $error : null;
    }
    // 有効な親テーマの単独更新で開始されたメンテナンスモードの解除
    if ($upgrader instanceof Theme_Upgrader && !$upgrader->bulk && get_stylesheet() === $this->directory) {
      $upgrader->maintenance_mode(false);
    }
    return $error;
  }

  public static function parse_requirements($contents) {
    if (!is_string($contents)) {
      return null;
    }
    // WordPress標準と同じ先頭8KiBと改行形式に基づくヘッダーの取得
    $header = str_replace("\r", "\n", substr($contents, 0, 8192));
    // 版番号の途中切断による要件の過小評価を防ぐ最終行の除外
    if (strlen($contents) > 8192) {
      $last_newline = strrpos($header, "\n");
      $header = $last_newline === false ? '' : substr($header, 0, $last_newline + 1);
    }
    $requirements = array();
    foreach (array('requires' => 'Requires at least', 'requires_php' => 'Requires PHP') as $key => $label) {
      if (!preg_match('/^(?:[ \t]*<\?php)?[ \t\/*#@]*' . preg_quote($label, '/') . ':(.*)$/mi', $header, $matches)) {
        return null;
      }
      // 同じ行のコメント終端を除く必須バージョン文字列の抽出
      $value = trim(preg_replace('/\s*(?:\*\/|\?>).*/', '', $matches[1]));
      if (!self::is_requirement($value)) {
        return null;
      }
      $requirements[$key] = $value;
    }
    return $requirements;
  }

  private static function is_requirement($value) {
    // 全成分がゼロの値を除く比較可能な数値バージョンの検証
    return is_string($value) && strlen($value) <= 32
      && preg_match('/^[0-9]+(?:\.[0-9]+){0,3}$/D', $value) && preg_match('/[1-9]/', $value);
  }

  private static function normalize_version($version) {
    // 安定版の省略された末尾ゼロと開発版の接尾辞を区別した版の正規化
    if (is_string($version) && preg_match('/^([0-9]+(?:\.[0-9]+)*)(.*)$/D', $version, $parts)) {
      return preg_replace('/(?:\.0+)+$/D', '', $parts[1]) . $parts[2];
    }
    return $version;
  }

  public static function check_versions($requirements, $wp_version, $php_version) {
    if (!is_array($requirements)
      || !self::is_requirement($requirements['requires'] ?? null)
      || !self::is_requirement($requirements['requires_php'] ?? null)) {
      return new WP_Error('cocoon_update_requirements_missing', __('更新ZIP内の動作要件を確認できないため、Cocoonの更新を中止しました。', THEME_NAME));
    }
    if (version_compare(self::normalize_version($wp_version), self::normalize_version($requirements['requires']), '<')
      || version_compare(self::normalize_version($php_version), self::normalize_version($requirements['requires_php']), '<')) {
      return new WP_Error('cocoon_update_requirements_incompatible', sprintf(
        /* translators: 1: 必要なWordPressバージョン、2: 必要なPHPバージョン、3: 現在のWordPressバージョン、4: 現在のPHPバージョン */
        __('このCocoonへの更新にはWordPress %1$s以上・PHP %2$s以上が必要です。現在はWordPress %3$s・PHP %4$sのため、更新できません。', THEME_NAME),
        $requirements['requires'], $requirements['requires_php'], $wp_version, $php_version
      ));
    }
    return null;
  }
}
endif;
