<?php
/**
 * Cron テスト用スタブ定義（グローバル名前空間）
 *
 * ProductBlockCronTest で必要な WordPress・Cocoon 関数のスタブを
 * グローバル名前空間で定義します。
 *
 * テストファイル (Cocoon\Tests\Unit 名前空間) のクラスメソッド内で
 * 定義するとその名前空間に属してしまい、グローバル関数として
 * function_exists ガードの対象にならないため、別ファイルで定義します。
 */

// ============================================================
// WordPress コア関数のスタブ（Cronファイル・REST APIファイル読み込み時に必要）
// wp-mock-functions.php で既に定義済みのものは function_exists ガードでスキップ
// ============================================================
if (!function_exists('register_rest_route')) {
    function register_rest_route($ns, $route, $args) {}
}
if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) { return trim(strip_tags($str)); }
}
if (!function_exists('sanitize_textarea_field')) {
    function sanitize_textarea_field($str) { return trim(strip_tags($str)); }
}
if (!function_exists('esc_url_raw')) {
    function esc_url_raw($url) { return filter_var($url, FILTER_SANITIZE_URL); }
}
if (!function_exists('rest_ensure_response')) {
    function rest_ensure_response($data) { return $data; }
}
if (!function_exists('wp_next_scheduled')) {
    function wp_next_scheduled($hook) { return false; }
}
if (!function_exists('wp_schedule_event')) {
    function wp_schedule_event($ts, $recurrence, $hook) { return true; }
}
if (!function_exists('wp_unschedule_event')) {
    function wp_unschedule_event($ts, $hook) {}
}
if (!function_exists('wp_get_schedule')) {
    function wp_get_schedule($hook) { return false; }
}
if (!function_exists('is_product_block_auto_update_enable')) {
    function is_product_block_auto_update_enable() { return true; }
}
if (!function_exists('get_product_block_auto_update_interval')) {
    function get_product_block_auto_update_interval() { return 'cocoon_every_3_days'; }
}
if (!function_exists('get_product_block_auto_update_batch_size')) {
    function get_product_block_auto_update_batch_size() { return 5; }
}
if (!function_exists('wp_using_ext_object_cache')) {
    function wp_using_ext_object_cache() { return false; }
}
if (!function_exists('wp_slash')) {
    function wp_slash($value) { return $value; }
}
if (!function_exists('cocoon_product_block_debug_log')) {
    function cocoon_product_block_debug_log($message, $tag = '') {}
}

// ============================================================
// Creators API 設定関数のスタブ
// creators-api.php の is_amazon_creators_api_credentials_available() が
// これらの関数を内部で呼ぶため、先にスタブ化が必要
// ============================================================
if (!function_exists('get_amazon_creators_api_credential_id')) {
    function get_amazon_creators_api_credential_id() { return ''; }
}
if (!function_exists('get_amazon_creators_api_secret')) {
    function get_amazon_creators_api_secret() { return ''; }
}
if (!function_exists('get_amazon_creators_api_version')) {
    function get_amazon_creators_api_version() { return '2.3'; }
}

// ============================================================
// WordPress コア関数のスタブ（creators-api.php の読み込み時に必要）
// 注意: get_transient / set_transient / delete_transient はここで定義しない。
// bootstrap.php 経由で読み込まれるファイルは Patchwork のストリームラッパーを
// 通過しないため、ここで定義すると Brain\Monkey の expect() で再定義できず
// DefinedTooEarly エラーになる。
// 代わりに wp-mock-functions.php に Brain\Monkey 互換のスタブを定義する。
// ============================================================
if (!function_exists('wp_json_encode')) {
    function wp_json_encode($data, $options = 0, $depth = 512) {
        return json_encode($data, $options, $depth);
    }
}
if (!function_exists('wp_remote_retrieve_response_code')) {
    // テスト用: レスポンス配列から response code を取得する（wp_remote_post のモックと連携）
    function wp_remote_retrieve_response_code($response) {
        if (is_array($response) && isset($response['response']['code'])) {
            return $response['response']['code'];
        }
        return 200;
    }
}
if (!function_exists('wp_mkdir_p')) {
    function wp_mkdir_p($target) { return true; }
}
if (!function_exists('get_theme_resources_path')) {
    function get_theme_resources_path() { return sys_get_temp_dir() . '/'; }
}

// ============================================================
// Creators API のItemLookup スタブ（グローバル変数で戻り値を制御）
// function_existsガードにより creators-api.php の本体定義より優先
// ============================================================
if (!function_exists('get_amazon_creators_itemlookup_json')) {
    function get_amazon_creators_itemlookup_json($asin, $tracking_id = null) {
        global $test_amazon_api_response;
        return isset($test_amazon_api_response) ? $test_amazon_api_response : false;
    }
}

// ============================================================
// 静的HTML生成関数のスタブ
// function_existsガードで block-*-product-link.php の本体より先に定義
// テスト用にsettingsの priceFetchedAt をHTML内に埋め込んで検証可能にする
// ============================================================
if (!function_exists('cocoon_amazon_block_generate_static_html')) {
    function cocoon_amazon_block_generate_static_html($item, $asin, $settings) {
        $pfa = isset($settings['priceFetchedAt']) ? $settings['priceFetchedAt'] : '';
        return '<div class="amazon-item-box" data-asin="' . $asin . '" data-pfa="' . $pfa . '">stub</div>';
    }
}
if (!function_exists('cocoon_rakuten_block_generate_static_html')) {
    function cocoon_rakuten_block_generate_static_html($Item, $itemCode, $settings) {
        $pfa = isset($settings['priceFetchedAt']) ? $settings['priceFetchedAt'] : '';
        return '<div class="rakuten-item-box" data-item-code="' . $itemCode . '" data-pfa="' . $pfa . '">stub</div>';
    }
}

// ============================================================
// 楽天 API スタブ（グローバル変数で戻り値を制御）
// ============================================================
if (!function_exists('cocoon_rakuten_block_fetch_item')) {
    function cocoon_rakuten_block_fetch_item($itemCode) {
        global $test_rakuten_api_response;
        // null または false の場合は false を返す（is_wp_error 非依存のスキップ条件）
        // ※ wp-mock-functions.php の is_wp_error() が常に false を返すため、
        //    楽天Cronコードの "if (is_wp_error($Item)) continue;" を通過してしまう。
        //    テスト環境では extract_item_data に invalid データが渡され、
        //    結果的にブロックは更新されるが staticHtml/innerHTML 整合性は維持される。
        //    実環境では is_wp_error が正しく動作し WP_Error 時にスキップされる。
        return isset($test_rakuten_api_response) ? $test_rakuten_api_response : false;
    }
}

// WP_Errorクラスのスタブ（WordPress未ロード環境用）
if (!class_exists('WP_Error')) {
    class WP_Error {
        public $code;
        public $message;
        public function __construct($code = '', $message = '') {
            $this->code = $code;
            $this->message = $message;
        }
    }
}

// is_wp_error関数のスタブ
if (!function_exists('is_wp_error')) {
    function is_wp_error($thing) {
        return ($thing instanceof \WP_Error);
    }
}
