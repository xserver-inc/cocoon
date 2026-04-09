<?php
/**
 * block-amazon-product-link-cron.php / block-rakuten-product-link-cron.php のユニットテスト
 *
 * Cron再帰更新処理（cocoon_*_block_update_blocks_recursive）をテストします。
 * 今回の修正ポイント:
 * - priceFetchedAt がISO 8601 UTC形式で属性・settingsの両方にセットされること
 * - APIエラー時にブロックがスキップ（クラッシュしない）されること
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class ProductBlockCronTest extends TestCase
{
    /**
     * テスト実行前のクラス初期化（一度だけ実行）
     *
     * Cronファイルの読み込みに必要なスタブ・定数を定義し、
     * テスト対象関数を利用可能にする
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Cron定数のスタブ（実環境ではapis-funcs.phpで定義）
        if (!defined('PRODUCT_BLOCK_CRON_API_SLEEP_SECONDS')) {
            define('PRODUCT_BLOCK_CRON_API_SLEEP_SECONDS', 0);
        }
        if (!defined('PRODUCT_BLOCK_CRON_POST_SLEEP_SECONDS')) {
            define('PRODUCT_BLOCK_CRON_POST_SLEEP_SECONDS', 0);
        }
        if (!defined('PRODUCT_BLOCK_AUTO_UPDATE_BATCH_SIZE_DEFAULT')) {
            define('PRODUCT_BLOCK_AUTO_UPDATE_BATCH_SIZE_DEFAULT', 5);
        }
        if (!defined('HOUR_IN_SECONDS')) {
            define('HOUR_IN_SECONDS', 3600);
        }
        if (!defined('DAY_IN_SECONDS')) {
            define('DAY_IN_SECONDS', 86400);
        }
        if (!defined('DEBUG_CACHE_ENABLE')) {
            define('DEBUG_CACHE_ENABLE', false);
        }
        if (!defined('AMAZON_DOMAIN')) {
            define('AMAZON_DOMAIN', 'www.amazon.co.jp');
        }

        // トランジェント定数
        if (!defined('TRANSIENT_AMAZON_API_PREFIX')) {
            define('TRANSIENT_AMAZON_API_PREFIX', THEME_NAME . '_amazon_paapi_v5_asin_');
        }
        if (!defined('TRANSIENT_BACKUP_AMAZON_API_PREFIX')) {
            define('TRANSIENT_BACKUP_AMAZON_API_PREFIX', THEME_NAME . '_backup_amazon_paapi_v5_asin_');
        }
        if (!defined('TRANSIENT_RAKUTEN_API_PREFIX')) {
            define('TRANSIENT_RAKUTEN_API_PREFIX', THEME_NAME . '_rakuten_api_id_');
        }
        if (!defined('TRANSIENT_BACKUP_RAKUTEN_API_PREFIX')) {
            define('TRANSIENT_BACKUP_RAKUTEN_API_PREFIX', THEME_NAME . '_backup_rakuten_api_id_');
        }

        // block-amazon-product-link.php の依存関数読み込み
        require_once dirname(__DIR__, 2) . '/lib/shortcodes-product-func.php';
        require_once dirname(__DIR__, 2) . '/lib/shortcodes-amazon.php';
        require_once dirname(__DIR__, 2) . '/lib/block-amazon-product-link.php';

        // block-rakuten-product-link.php の依存関数読み込み
        require_once dirname(__DIR__, 2) . '/lib/rakuten-api-helpers.php';
        require_once dirname(__DIR__, 2) . '/lib/block-rakuten-product-link.php';

        // Cronファイル読み込み（update_blocks_recursive関数が定義される）
        require_once dirname(__DIR__, 2) . '/lib/block-amazon-product-link-cron.php';
        require_once dirname(__DIR__, 2) . '/lib/block-rakuten-product-link-cron.php';

    }

    /**
     * 各テストの前にAPIレスポンスのグローバル変数をリセット
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Amazon/楽天の両APIレスポンスを各テスト前にリセット
        global $test_amazon_api_response, $test_rakuten_api_response;
        $test_amazon_api_response = false;
        $test_rakuten_api_response = null;
    }

    // ========================================================================
    // Amazon Cron: cocoon_amazon_block_update_blocks_recursive() のテスト
    // ========================================================================

    /**
     * Cron更新時にpriceFetchedAtがISO 8601 UTC形式でattrsにセットされる
     */
    public function test_amazon_cron_priceFetchedAtがattrsに正しいフォーマットでセットされる(): void
    {
        // APIの返却スタブ（完全なItemsResult構造）
        global $test_amazon_api_response;
        $test_amazon_api_response = json_encode([
            'ItemsResult' => [
                'Items' => [[
                    'ASIN' => 'B09TEST001',
                    'ItemInfo' => [
                        'Title' => ['DisplayValue' => 'テスト商品'],
                        'ByLineInfo' => ['Brand' => ['DisplayValue' => 'テストブランド']],
                    ],
                    'Images' => [
                        'Primary' => [
                            'Medium' => ['URL' => 'https://img/m.jpg', 'Width' => 160, 'Height' => 160],
                        ],
                    ],
                ]],
            ],
        ]);

        // テスト用ブロック配列（Cron再帰更新の入力形式）
        $blocks = [[
            'blockName' => 'cocoon-blocks/amazon-product-link',
            'attrs' => [
                'asin' => 'B09TEST001',
                'title' => '古いタイトル',
                // priceFetchedAtは古い値(もしくは未設定)
                'priceFetchedAt' => '2025-01-01T00:00:00.000Z',
                'size' => 'm',
                'displayMode' => 'normal',
                'showPrice' => true,
            ],
            'innerHTML' => '<div>old</div>',
            'innerContent' => ['<div>old</div>'],
            'innerBlocks' => [],
        ]];

        // テスト実行前の時刻を記録
        $beforeTime = gmdate('Y-m-d\TH:i');

        $updated = false;
        $result = cocoon_amazon_block_update_blocks_recursive($blocks, $updated);

        // ブロックが更新されたことの確認
        $this->assertTrue($updated, 'updated フラグが true になるはず');

        // priceFetchedAtが更新されていることの確認
        $pfa = $result[0]['attrs']['priceFetchedAt'];
        $this->assertNotEquals('2025-01-01T00:00:00.000Z', $pfa, '古いpriceFetchedAtから更新されているはず');

        // ISO 8601 UTC形式（JSのtoISOString()互換）であることの検証
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.000Z$/',
            $pfa,
            'priceFetchedAtがISO 8601 UTC形式（YYYY-MM-DDTHH:MM:SS.000Z）であること'
        );

        // staticHtml内にもpriceFetchedAtが反映されていること（スタブのdata-pfa属性で検証）
        $html = $result[0]['attrs']['staticHtml'];
        $this->assertStringContainsString($pfa, $html, 'staticHtmlにpriceFetchedAtが反映されている');

        // innerHTMLとinnerContentも更新されていること
        $this->assertSame($html, $result[0]['innerHTML']);
        $this->assertSame([$html], $result[0]['innerContent']);
    }

    /**
     * Amazon APIが失敗（false）した場合、ブロックはスキップされる
     */
    public function test_amazon_cron_API失敗時にブロックがスキップされる(): void
    {
        global $test_amazon_api_response;
        $test_amazon_api_response = false;

        $blocks = [[
            'blockName' => 'cocoon-blocks/amazon-product-link',
            'attrs' => [
                'asin' => 'B09FAIL001',
                'priceFetchedAt' => '2025-06-01T00:00:00.000Z',
            ],
            'innerHTML' => '<div>original</div>',
            'innerContent' => ['<div>original</div>'],
            'innerBlocks' => [],
        ]];

        $updated = false;
        $result = cocoon_amazon_block_update_blocks_recursive($blocks, $updated);

        // 更新されないこと
        $this->assertFalse($updated, 'API失敗時はupdatedがfalseのまま');
        // priceFetchedAtが変更されないこと
        $this->assertSame('2025-06-01T00:00:00.000Z', $result[0]['attrs']['priceFetchedAt']);
        // innerHTMLが変更されないこと
        $this->assertSame('<div>original</div>', $result[0]['innerHTML']);
    }

    /**
     * ASINが空のブロックはスキップされる
     */
    public function test_amazon_cron_ASINが空のブロックはスキップされる(): void
    {
        $blocks = [[
            'blockName' => 'cocoon-blocks/amazon-product-link',
            'attrs' => [
                'asin' => '',
                'priceFetchedAt' => '2025-01-01T00:00:00.000Z',
            ],
            'innerHTML' => '<div>empty asin</div>',
            'innerContent' => ['<div>empty asin</div>'],
            'innerBlocks' => [],
        ]];

        $updated = false;
        $result = cocoon_amazon_block_update_blocks_recursive($blocks, $updated);

        $this->assertFalse($updated, 'ASINが空の場合はスキップ');
    }

    /**
     * 非Amazon商品リンクブロック（例：段落ブロック）はスキップされる
     */
    public function test_amazon_cron_非対象ブロックはスキップされる(): void
    {
        $blocks = [[
            'blockName' => 'core/paragraph',
            'attrs' => [],
            'innerHTML' => '<p>テスト</p>',
            'innerContent' => ['<p>テスト</p>'],
            'innerBlocks' => [],
        ]];

        $updated = false;
        $result = cocoon_amazon_block_update_blocks_recursive($blocks, $updated);

        $this->assertFalse($updated);
        $this->assertSame('<p>テスト</p>', $result[0]['innerHTML']);
    }

    /**
     * ネストされたインナーブロック内のAmazonブロックも再帰的に更新される
     */
    public function test_amazon_cron_インナーブロック内のブロックも再帰的に更新される(): void
    {
        global $test_amazon_api_response;
        $test_amazon_api_response = json_encode([
            'ItemsResult' => [
                'Items' => [[
                    'ASIN' => 'B09INNER01',
                    'ItemInfo' => [
                        'Title' => ['DisplayValue' => 'ネスト商品'],
                    ],
                    'Images' => ['Primary' => ['Medium' => ['URL' => 'https://img/m.jpg', 'Width' => 160, 'Height' => 160]]],
                ]],
            ],
        ]);

        // カラムブロックの中にAmazonブロックがある構造
        $blocks = [[
            'blockName' => 'core/column',
            'attrs' => [],
            'innerHTML' => '',
            'innerContent' => [],
            'innerBlocks' => [[
                'blockName' => 'cocoon-blocks/amazon-product-link',
                'attrs' => [
                    'asin' => 'B09INNER01',
                    'priceFetchedAt' => '2025-01-01T00:00:00.000Z',
                    'size' => 'm',
                    'displayMode' => 'normal',
                    'showPrice' => true,
                ],
                'innerHTML' => '<div>old nested</div>',
                'innerContent' => ['<div>old nested</div>'],
                'innerBlocks' => [],
            ]],
        ]];

        $updated = false;
        $result = cocoon_amazon_block_update_blocks_recursive($blocks, $updated);

        $this->assertTrue($updated, 'インナーブロック内の更新でフラグが立つ');
        // インナーブロックのpriceFetchedAtが更新されている
        $innerPfa = $result[0]['innerBlocks'][0]['attrs']['priceFetchedAt'];
        $this->assertNotEquals('2025-01-01T00:00:00.000Z', $innerPfa);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.000Z$/', $innerPfa);
    }

    // ========================================================================
    // 楽天 Cron: cocoon_rakuten_block_update_blocks_recursive() のテスト
    // ========================================================================

    /**
     * 楽天Cron: itemCodeが空のブロックはスキップされる
     */
    public function test_rakuten_cron_itemCodeが空のブロックはスキップされる(): void
    {
        $blocks = [[
            'blockName' => 'cocoon-blocks/rakuten-product-link',
            'attrs' => [
                'itemCode' => '',
                'priceFetchedAt' => '2025-01-01T00:00:00.000Z',
            ],
            'innerHTML' => '<div>empty</div>',
            'innerContent' => ['<div>empty</div>'],
            'innerBlocks' => [],
        ]];

        $updated = false;
        $result = cocoon_rakuten_block_update_blocks_recursive($blocks, $updated);

        $this->assertFalse($updated, 'itemCodeが空の場合はスキップ');
    }

    /**
     * 非楽天ブロックはスキップされる
     */
    public function test_rakuten_cron_非対象ブロックはスキップされる(): void
    {
        $blocks = [[
            'blockName' => 'core/heading',
            'attrs' => [],
            'innerHTML' => '<h2>見出し</h2>',
            'innerContent' => ['<h2>見出し</h2>'],
            'innerBlocks' => [],
        ]];

        $updated = false;
        $result = cocoon_rakuten_block_update_blocks_recursive($blocks, $updated);

        $this->assertFalse($updated);
    }

    // ========================================================================
    // バリデーション安全性テスト（staticHtml / innerHTML / innerContent 一致保証）
    // ========================================================================

    /**
     * Cron更新後、staticHtml・innerHTML・innerContent[0]が厳密に一致すること
     * （この3つが不一致だとエディタ起動時にブロックバリデーションエラーになる）
     */
    public function test_amazon_cron_更新後のstaticHtmlとinnerHTMLとinnerContentが厳密に一致する(): void
    {
        global $test_amazon_api_response;
        $test_amazon_api_response = json_encode([
            'ItemsResult' => [
                'Items' => [[
                    'ASIN' => 'B09VALID01',
                    'ItemInfo' => [
                        'Title' => ['DisplayValue' => 'バリデーションテスト商品'],
                        'ByLineInfo' => ['Brand' => ['DisplayValue' => 'テストメーカー']],
                    ],
                    'Images' => [
                        'Primary' => [
                            'Medium' => ['URL' => 'https://img/valid.jpg', 'Width' => 160, 'Height' => 160],
                        ],
                    ],
                ]],
            ],
        ]);

        $blocks = [[
            'blockName' => 'cocoon-blocks/amazon-product-link',
            'attrs' => [
                'asin' => 'B09VALID01',
                'title' => '旧タイトル',
                'priceFetchedAt' => '2025-06-01T00:00:00.000Z',
                'size' => 'm',
                'displayMode' => 'normal',
                'showPrice' => true,
            ],
            'innerHTML' => '<div>old</div>',
            'innerContent' => ['<div>old</div>'],
            'innerBlocks' => [],
        ]];

        $updated = false;
        $result = cocoon_amazon_block_update_blocks_recursive($blocks, $updated);

        $this->assertTrue($updated);

        // バリデーション安全性の核心: 3箇所が厳密に同一であること
        $staticHtml = $result[0]['attrs']['staticHtml'];
        $innerHTML = $result[0]['innerHTML'];
        $innerContent0 = $result[0]['innerContent'][0];

        $this->assertSame($staticHtml, $innerHTML,
            'staticHtml と innerHTML が厳密に一致すること（バリデーション安全性の保証）');
        $this->assertSame($staticHtml, $innerContent0,
            'staticHtml と innerContent[0] が厳密に一致すること');

        // 旧HTMLとは異なることの確認（実際に更新が行われた）
        $this->assertNotSame('<div>old</div>', $innerHTML,
            '旧HTMLから更新されていること');
    }

    /**
     * priceFetchedAtが未設定のブロックにCronが初めて値をセットしても
     * staticHtml/innerHTML/innerContentの整合性が保たれること
     */
    public function test_amazon_cron_priceFetchedAt未設定からの初回セットでも整合性が保たれる(): void
    {
        global $test_amazon_api_response;
        $test_amazon_api_response = json_encode([
            'ItemsResult' => [
                'Items' => [[
                    'ASIN' => 'B09FIRST01',
                    'ItemInfo' => [
                        'Title' => ['DisplayValue' => '初回セットテスト'],
                    ],
                    'Images' => [
                        'Primary' => [
                            'Medium' => ['URL' => 'https://img/first.jpg', 'Width' => 160, 'Height' => 160],
                        ],
                    ],
                ]],
            ],
        ]);

        // priceFetchedAt が存在しないブロック（Cron導入前に作成された投稿）
        $blocks = [[
            'blockName' => 'cocoon-blocks/amazon-product-link',
            'attrs' => [
                'asin' => 'B09FIRST01',
                'title' => 'タイトル',
                'size' => 'm',
                'displayMode' => 'normal',
                'showPrice' => true,
                // priceFetchedAt なし（意図的に未定義）
            ],
            'innerHTML' => '<div>old without pfa</div>',
            'innerContent' => ['<div>old without pfa</div>'],
            'innerBlocks' => [],
        ]];

        $updated = false;
        $result = cocoon_amazon_block_update_blocks_recursive($blocks, $updated);

        $this->assertTrue($updated);

        // priceFetchedAt が新しくセットされていること
        $pfa = $result[0]['attrs']['priceFetchedAt'];
        $this->assertNotEmpty($pfa, 'priceFetchedAtが初めてセットされること');
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.000Z$/', $pfa
        );

        // 3箇所一致の検証（priceFetchedAt初回セット時にも整合性が保たれる）
        $staticHtml = $result[0]['attrs']['staticHtml'];
        $innerHTML = $result[0]['innerHTML'];
        $innerContent0 = $result[0]['innerContent'][0];

        $this->assertSame($staticHtml, $innerHTML,
            'priceFetchedAt初回セット時もstaticHtmlとinnerHTMLが一致');
        $this->assertSame($staticHtml, $innerContent0,
            'priceFetchedAt初回セット時もstaticHtmlとinnerContent[0]が一致');
    }

    /**
     * ネストブロック内のAmazonブロックでもstaticHtml/innerHTML/innerContentが一致すること
     */
    public function test_amazon_cron_ネストブロック内でもバリデーション整合性が保たれる(): void
    {
        global $test_amazon_api_response;
        $test_amazon_api_response = json_encode([
            'ItemsResult' => [
                'Items' => [[
                    'ASIN' => 'B09NEST01',
                    'ItemInfo' => [
                        'Title' => ['DisplayValue' => 'ネスト整合性テスト'],
                    ],
                    'Images' => [
                        'Primary' => [
                            'Medium' => ['URL' => 'https://img/nest.jpg', 'Width' => 160, 'Height' => 160],
                        ],
                    ],
                ]],
            ],
        ]);

        $blocks = [[
            'blockName' => 'core/columns',
            'attrs' => [],
            'innerHTML' => '',
            'innerContent' => [],
            'innerBlocks' => [[
                'blockName' => 'core/column',
                'attrs' => [],
                'innerHTML' => '',
                'innerContent' => [],
                'innerBlocks' => [[
                    'blockName' => 'cocoon-blocks/amazon-product-link',
                    'attrs' => [
                        'asin' => 'B09NEST01',
                        'priceFetchedAt' => '2025-01-01T00:00:00.000Z',
                        'size' => 'm',
                        'displayMode' => 'normal',
                        'showPrice' => true,
                    ],
                    'innerHTML' => '<div>old nested</div>',
                    'innerContent' => ['<div>old nested</div>'],
                    'innerBlocks' => [],
                ]],
            ]],
        ]];

        $updated = false;
        $result = cocoon_amazon_block_update_blocks_recursive($blocks, $updated);

        $this->assertTrue($updated);

        // 2階層下のネストブロックで3箇所一致を検証
        $nestedBlock = $result[0]['innerBlocks'][0]['innerBlocks'][0];
        $staticHtml = $nestedBlock['attrs']['staticHtml'];
        $innerHTML = $nestedBlock['innerHTML'];
        $innerContent0 = $nestedBlock['innerContent'][0];

        $this->assertSame($staticHtml, $innerHTML,
            'ネストブロック内でもstaticHtmlとinnerHTMLが一致');
        $this->assertSame($staticHtml, $innerContent0,
            'ネストブロック内でもstaticHtmlとinnerContent[0]が一致');
    }

    // ========================================================================
    // ISO 8601 UTC フォーマット互換性のテスト
    // ========================================================================

    /**
     * gmdate()で生成されるUTC時刻がJavaScriptのnew Date().toISOString()と互換であることを確認
     */
    public function test_gmdate形式がJSのtoISOStringと互換である(): void
    {
        // PHPでの生成方法（Cronコード内で使用する形式）
        $phpFormat = gmdate('Y-m-d\TH:i:s.000\Z');

        // ISO 8601 UTC形式: "2026-03-29T00:30:00.000Z"
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.000Z$/',
            $phpFormat,
            'gmdate()出力がISO 8601 UTC形式（YYYY-MM-DDTHH:MM:SS.000Z）に一致する'
        );

        // strtotime()で正しくパースできること（サーバーサイドでの逆変換の互換性）
        $ts = strtotime($phpFormat);
        $this->assertIsInt($ts, 'strtotime()でパースできる');
        $this->assertGreaterThan(0, $ts, 'パース結果が正のタイムスタンプ');
    }

    /**
     * priceFetchedAtの形式がwp_date()のstrtotime()で正しくパースされる
     * （block-amazon-product-link.phpのrender_preview内での使用を検証）
     */
    public function test_priceFetchedAtがstrtotime経由でwp_dateに正しく変換される(): void
    {
        $priceFetchedAt = '2026-03-29T10:30:00.000Z';

        $ts = strtotime($priceFetchedAt);
        $this->assertIsInt($ts);

        // UTC 10:30 の日付が正しく取れること
        $formatted = gmdate('Y/m/d H:i', $ts);
        $this->assertSame('2026/03/29 10:30', $formatted);
    }

    // ========================================================================
    // 楽天 Cron: バリデーション安全性テスト
    // ========================================================================

    /**
     * 楽天Cron更新時にstaticHtml/innerHTML/innerContent[0]が厳密に一致すること
     */
    public function test_rakuten_cron_更新後のstaticHtmlとinnerHTMLとinnerContentが厳密に一致する(): void
    {
        global $test_rakuten_api_response;
        // 楽天APIのレスポンスはオブジェクト形式
        $test_rakuten_api_response = (object) [
            'itemCode' => 'shop:test-item-001',
            'itemName' => '楽天テスト商品',
            'shopName' => 'テストショップ',
            'shopCode' => 'testshop',
            'itemPrice' => 1980,
            'itemCaption' => 'テスト説明文',
            'affiliateUrl' => 'https://example.com/aff',
            'affiliateRate' => 5.0,
        ];

        $blocks = [[
            'blockName' => 'cocoon-blocks/rakuten-product-link',
            'attrs' => [
                'itemCode' => 'shop:test-item-001',
                'title' => '旧タイトル',
                'priceFetchedAt' => '2025-06-01T00:00:00.000Z',
                'size' => 'm',
                'displayMode' => 'normal',
                'showPrice' => true,
            ],
            'innerHTML' => '<div>old</div>',
            'innerContent' => ['<div>old</div>'],
            'innerBlocks' => [],
        ]];

        $updated = false;
        $result = cocoon_rakuten_block_update_blocks_recursive($blocks, $updated);

        $this->assertTrue($updated);

        // バリデーション安全性: 3箇所が厳密に同一であること
        $staticHtml = $result[0]['attrs']['staticHtml'];
        $innerHTML = $result[0]['innerHTML'];
        $innerContent0 = $result[0]['innerContent'][0];

        $this->assertSame($staticHtml, $innerHTML,
            '楽天: staticHtml と innerHTML が厳密に一致すること');
        $this->assertSame($staticHtml, $innerContent0,
            '楽天: staticHtml と innerContent[0] が厳密に一致すること');
        $this->assertNotSame('<div>old</div>', $innerHTML,
            '楽天: 旧HTMLから更新されていること');
    }

    /**
     * 楽天Cron: API応答が不完全なデータでも staticHtml/innerHTML/innerContent の整合性が保たれる
     *
     * ※ テスト環境では wp-mock-functions.php の is_wp_error() が常に false を返すため、
     *    WP_Error によるスキップは再現できません。代わりに、不完全なオブジェクトが渡されても
     *    バリデーション整合性が壊れないことを検証します。
     *    実環境では is_wp_error が正しく動作し、WP_Error 時は continue でスキップされます。
     */
    public function test_rakuten_cron_API応答が不完全でもバリデーション整合性が保たれる(): void
    {
        global $test_rakuten_api_response;
        // 最小限のフィールドしか持たないオブジェクト（不完全応答）
        $test_rakuten_api_response = (object) [
            'itemCode' => 'shop:partial-item',
            // itemName, shopName, itemPrice 等が欠落
        ];

        $blocks = [[
            'blockName' => 'cocoon-blocks/rakuten-product-link',
            'attrs' => [
                'itemCode' => 'shop:partial-item',
                'priceFetchedAt' => '2025-06-01T00:00:00.000Z',
                'size' => 'm',
                'displayMode' => 'normal',
                'showPrice' => true,
            ],
            'innerHTML' => '<div>old</div>',
            'innerContent' => ['<div>old</div>'],
            'innerBlocks' => [],
        ]];

        $updated = false;
        $result = cocoon_rakuten_block_update_blocks_recursive($blocks, $updated);

        // 不完全応答でも更新は行われる（is_wp_error がスキップしないため）
        $this->assertTrue($updated);

        // 重要: 不完全データでも staticHtml/innerHTML/innerContent の整合性が保たれること
        $staticHtml = $result[0]['attrs']['staticHtml'];
        $innerHTML = $result[0]['innerHTML'];
        $innerContent0 = $result[0]['innerContent'][0];

        $this->assertSame($staticHtml, $innerHTML,
            '不完全応答でも staticHtml と innerHTML が一致（バリデーション安全性）');
        $this->assertSame($staticHtml, $innerContent0,
            '不完全応答でも staticHtml と innerContent[0] が一致');
    }

    /**
     * 楽天Cron: priceFetchedAt未設定からの初回セットでも整合性が保たれる
     */
    public function test_rakuten_cron_priceFetchedAt未設定からの初回セットでも整合性が保たれる(): void
    {
        global $test_rakuten_api_response;
        $test_rakuten_api_response = (object) [
            'itemCode' => 'shop:first-item',
            'itemName' => '楽天初回テスト',
            'shopName' => 'テストショップ',
            'shopCode' => 'testshop',
            'itemPrice' => 2980,
        ];

        // priceFetchedAtが存在しないブロック
        $blocks = [[
            'blockName' => 'cocoon-blocks/rakuten-product-link',
            'attrs' => [
                'itemCode' => 'shop:first-item',
                'size' => 'm',
                'displayMode' => 'normal',
                'showPrice' => true,
            ],
            'innerHTML' => '<div>old</div>',
            'innerContent' => ['<div>old</div>'],
            'innerBlocks' => [],
        ]];

        $updated = false;
        $result = cocoon_rakuten_block_update_blocks_recursive($blocks, $updated);

        $this->assertTrue($updated);

        // priceFetchedAtがセットされていること
        $pfa = $result[0]['attrs']['priceFetchedAt'];
        $this->assertNotEmpty($pfa);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.000Z$/', $pfa);

        // 3箇所一致
        $staticHtml = $result[0]['attrs']['staticHtml'];
        $this->assertSame($staticHtml, $result[0]['innerHTML']);
        $this->assertSame($staticHtml, $result[0]['innerContent'][0]);
    }

    /**
     * ネストブロック内の楽天ブロックでもstaticHtml/innerHTML/innerContentが一致すること
     */
    public function test_rakuten_cron_ネストブロック内でもバリデーション整合性が保たれる(): void
    {
        global $test_rakuten_api_response;
        $test_rakuten_api_response = (object) [
            'itemCode' => 'shop:nest-item',
            'itemName' => '楽天ネスト整合性テスト',
            'shopName' => 'テストショップ',
            'shopCode' => 'testshop',
            'itemPrice' => 2980,
        ];

        // 2階層ネストされた構造（columns > column > rakuten-product-link）
        $blocks = [[
            'blockName' => 'core/columns',
            'attrs' => [],
            'innerHTML' => '<div>columns before<div class="column"></div>columns after</div>',
            'innerContent' => ['<div>columns before', null, 'columns after</div>'],
            'innerBlocks' => [[
                'blockName' => 'core/column',
                'attrs' => [],
                'innerHTML' => '<div class="column">column before column after</div>',
                'innerContent' => ['<div class="column">column before ', null, ' column after</div>'],
                'innerBlocks' => [[
                    'blockName' => 'cocoon-blocks/rakuten-product-link',
                    'attrs' => [
                        'itemCode' => 'shop:nest-item',
                        'priceFetchedAt' => '2025-06-01T00:00:00.000Z',
                        'size' => 'm',
                        'displayMode' => 'normal',
                        'showPrice' => true,
                    ],
                    'innerHTML' => '<div>nest old</div>',
                    'innerContent' => ['<div>nest old</div>'],
                    'innerBlocks' => [],
                ]],
            ]],
        ]];

        $updated = false;
        $result = cocoon_rakuten_block_update_blocks_recursive($blocks, $updated);

        $this->assertTrue($updated);

        // ネストされたRakutenブロックの参照を取得
        $rakutenBlock = $result[0]['innerBlocks'][0]['innerBlocks'][0];

        $staticHtml = $rakutenBlock['attrs']['staticHtml'];
        $innerHTML = $rakutenBlock['innerHTML'];
        $innerContent0 = $rakutenBlock['innerContent'][0];

        $this->assertSame($staticHtml, $innerHTML,
            '楽天(ネスト): staticHtml と innerHTML が一致');
        $this->assertSame($staticHtml, $innerContent0,
            '楽天(ネスト): staticHtml と innerContent[0] が一致');
    }

    /**
     * 同一投稿内の複数楽天ブロックが全て正しく更新される
     */
    public function test_rakuten_cron_複数ブロックが全て個別に正しく更新される(): void
    {
        global $test_rakuten_api_response;
        $test_rakuten_api_response = (object) [
            'itemCode' => 'shop:multi-item',
            'itemName' => '楽天複数テスト商品',
            'shopName' => 'テストショップ',
            'shopCode' => 'testshop',
            'itemPrice' => 4980,
        ];

        // 2つの楽天ブロックが並んでいる構造
        $blocks = [
            [
                'blockName' => 'cocoon-blocks/rakuten-product-link',
                'attrs' => [
                    'itemCode' => 'shop:multi-item',
                    'priceFetchedAt' => '2025-01-01T00:00:00.000Z',
                    'size' => 'm',
                    'displayMode' => 'normal',
                    'showPrice' => true,
                ],
                'innerHTML' => '<div>block1 old</div>',
                'innerContent' => ['<div>block1 old</div>'],
                'innerBlocks' => [],
            ],
            [
                'blockName' => 'cocoon-blocks/rakuten-product-link',
                'attrs' => [
                    'itemCode' => 'shop:multi-item',
                    'priceFetchedAt' => '2024-12-01T00:00:00.000Z',
                    'size' => 'm',
                    'displayMode' => 'normal',
                    'showPrice' => true,
                ],
                'innerHTML' => '<div>block2 old</div>',
                'innerContent' => ['<div>block2 old</div>'],
                'innerBlocks' => [],
            ],
        ];

        $updated = false;
        $result = cocoon_rakuten_block_update_blocks_recursive($blocks, $updated);

        $this->assertTrue($updated);

        // 両ブロックともバリデーション安全性が保たれること
        foreach ([0, 1] as $i) {
            $staticHtml = $result[$i]['attrs']['staticHtml'];
            $innerHTML = $result[$i]['innerHTML'];
            $innerContent0 = $result[$i]['innerContent'][0];

            $this->assertSame($staticHtml, $innerHTML,
                "楽天ブロック{$i}: staticHtml と innerHTML が一致");
            $this->assertSame($staticHtml, $innerContent0,
                "楽天ブロック{$i}: staticHtml と innerContent[0] が一致");

            // priceFetchedAtが更新されていること
            $pfa = $result[$i]['attrs']['priceFetchedAt'];
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.000Z$/', $pfa);
        }
    }

    // ========================================================================
    // Amazon Cron: 追加エッジケーステスト
    // ========================================================================

    /**
     * API応答がJSONだが商品リストが空の場合、ブロックはスキップされる
     */
    public function test_amazon_cron_API応答に商品が含まれない場合スキップされる(): void
    {
        global $test_amazon_api_response;
        // ItemsResultはあるがItemsが空
        $test_amazon_api_response = json_encode([
            'ItemsResult' => [
                'Items' => [],
            ],
        ]);

        $blocks = [[
            'blockName' => 'cocoon-blocks/amazon-product-link',
            'attrs' => [
                'asin' => 'B09EMPTY01',
                'priceFetchedAt' => '2025-01-01T00:00:00.000Z',
                'staticHtml' => '<div>existing</div>',
            ],
            'innerHTML' => '<div>existing</div>',
            'innerContent' => ['<div>existing</div>'],
            'innerBlocks' => [],
        ]];

        $updated = false;
        $result = cocoon_amazon_block_update_blocks_recursive($blocks, $updated);

        $this->assertFalse($updated, '空のItems配列の場合はスキップ');
        // 既存のstaticHtmlが保持されること（バリデーション安全性）
        $this->assertSame('<div>existing</div>', $result[0]['attrs']['staticHtml']);
        $this->assertSame('<div>existing</div>', $result[0]['innerHTML']);
    }

    /**
     * 同一投稿内の複数Amazonブロックが全て正しく更新される
     */
    public function test_amazon_cron_複数ブロックが全て個別に正しく更新される(): void
    {
        global $test_amazon_api_response;
        $test_amazon_api_response = json_encode([
            'ItemsResult' => [
                'Items' => [[
                    'ASIN' => 'B09MULTI01',
                    'ItemInfo' => [
                        'Title' => ['DisplayValue' => '複数テスト商品'],
                    ],
                    'Images' => [
                        'Primary' => [
                            'Medium' => ['URL' => 'https://img/multi.jpg', 'Width' => 160, 'Height' => 160],
                        ],
                    ],
                ]],
            ],
        ]);

        // 2つのAmazonブロックが並んでいる構造
        $blocks = [
            [
                'blockName' => 'cocoon-blocks/amazon-product-link',
                'attrs' => [
                    'asin' => 'B09MULTI01',
                    'priceFetchedAt' => '2025-01-01T00:00:00.000Z',
                    'size' => 'm',
                    'displayMode' => 'normal',
                    'showPrice' => true,
                ],
                'innerHTML' => '<div>block1 old</div>',
                'innerContent' => ['<div>block1 old</div>'],
                'innerBlocks' => [],
            ],
            [
                'blockName' => 'cocoon-blocks/amazon-product-link',
                'attrs' => [
                    'asin' => 'B09MULTI01',
                    'priceFetchedAt' => '2024-12-01T00:00:00.000Z',
                    'size' => 'm',
                    'displayMode' => 'normal',
                    'showPrice' => true,
                ],
                'innerHTML' => '<div>block2 old</div>',
                'innerContent' => ['<div>block2 old</div>'],
                'innerBlocks' => [],
            ],
        ];

        $updated = false;
        $result = cocoon_amazon_block_update_blocks_recursive($blocks, $updated);

        $this->assertTrue($updated);

        // 両ブロックともバリデーション安全性が保たれること
        foreach ([0, 1] as $i) {
            $staticHtml = $result[$i]['attrs']['staticHtml'];
            $innerHTML = $result[$i]['innerHTML'];
            $innerContent0 = $result[$i]['innerContent'][0];

            $this->assertSame($staticHtml, $innerHTML,
                "ブロック{$i}: staticHtml と innerHTML が一致");
            $this->assertSame($staticHtml, $innerContent0,
                "ブロック{$i}: staticHtml と innerContent[0] が一致");

            // priceFetchedAtが更新されていること
            $pfa = $result[$i]['attrs']['priceFetchedAt'];
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.000Z$/', $pfa);
        }
    }

    /**
     * Amazon + 楽天ブロックが混在する投稿でも各ブロックが正しく更新される
     */
    public function test_amazon_rakuten混在ブロックで各ブロックが独立して正しく更新される(): void
    {
        global $test_amazon_api_response, $test_rakuten_api_response;

        $test_amazon_api_response = json_encode([
            'ItemsResult' => [
                'Items' => [[
                    'ASIN' => 'B09MIX001',
                    'ItemInfo' => [
                        'Title' => ['DisplayValue' => 'Amazon混在テスト'],
                    ],
                    'Images' => [
                        'Primary' => [
                            'Medium' => ['URL' => 'https://img/mix.jpg', 'Width' => 160, 'Height' => 160],
                        ],
                    ],
                ]],
            ],
        ]);

        $test_rakuten_api_response = (object) [
            'itemCode' => 'shop:mix-item',
            'itemName' => '楽天混在テスト',
            'shopName' => 'テストショップ',
            'shopCode' => 'testshop',
            'itemPrice' => 3980,
        ];

        // AmazonブロックをAmazon Cronで処理
        $amazonBlocks = [[
            'blockName' => 'cocoon-blocks/amazon-product-link',
            'attrs' => [
                'asin' => 'B09MIX001',
                'priceFetchedAt' => '2025-01-01T00:00:00.000Z',
                'size' => 'm',
                'displayMode' => 'normal',
                'showPrice' => true,
            ],
            'innerHTML' => '<div>amazon old</div>',
            'innerContent' => ['<div>amazon old</div>'],
            'innerBlocks' => [],
        ]];

        $updated = false;
        $amazonResult = cocoon_amazon_block_update_blocks_recursive($amazonBlocks, $updated);
        $this->assertTrue($updated);
        $this->assertSame(
            $amazonResult[0]['attrs']['staticHtml'],
            $amazonResult[0]['innerHTML'],
            'Amazon: staticHtml と innerHTML が一致'
        );

        // 楽天ブロックを楽天Cronで処理
        $rakutenBlocks = [[
            'blockName' => 'cocoon-blocks/rakuten-product-link',
            'attrs' => [
                'itemCode' => 'shop:mix-item',
                'priceFetchedAt' => '2025-01-01T00:00:00.000Z',
                'size' => 'm',
                'displayMode' => 'normal',
                'showPrice' => true,
            ],
            'innerHTML' => '<div>rakuten old</div>',
            'innerContent' => ['<div>rakuten old</div>'],
            'innerBlocks' => [],
        ]];

        $updated = false;
        $rakutenResult = cocoon_rakuten_block_update_blocks_recursive($rakutenBlocks, $updated);
        $this->assertTrue($updated);
        $this->assertSame(
            $rakutenResult[0]['attrs']['staticHtml'],
            $rakutenResult[0]['innerHTML'],
            '楽天: staticHtml と innerHTML が一致'
        );
    }
}
