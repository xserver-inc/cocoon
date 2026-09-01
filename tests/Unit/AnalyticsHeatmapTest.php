<?php
/**
 * アクセス解析ヒートマップの色分け基準に関するユニットテスト
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class AnalyticsHeatmapTest extends TestCase
{
    private static bool $analytics_functions_loaded = false;

    private bool $had_filter_callbacks;

    private mixed $previous_filter_callbacks;

    protected function setUp(): void
    {
        parent::setUp();

        if (!self::$analytics_functions_loaded) {
            require_once dirname(__DIR__, 2) . '/lib/page-access/analytics/render-func.php';
            self::$analytics_functions_loaded = true;
        }

        // 他のテストが登録したフィルターを終了時に復元できるよう保存する
        $this->had_filter_callbacks = array_key_exists('test_mock_apply_filters_callbacks', $GLOBALS);
        $this->previous_filter_callbacks = $GLOBALS['test_mock_apply_filters_callbacks'] ?? null;
        $GLOBALS['test_mock_apply_filters_callbacks'] = array();
    }

    protected function tearDown(): void
    {
        // テスト開始前のグローバルなフィルター状態を漏れなく復元する
        if ($this->had_filter_callbacks) {
            $GLOBALS['test_mock_apply_filters_callbacks'] = $this->previous_filter_callbacks;
        } else {
            unset($GLOBALS['test_mock_apply_filters_callbacks']);
        }

        parent::tearDown();
    }

    /**
     * 完了済みの非ゼロ日だけから線形補間した四分位数を求めることをテスト
     */
    public function test_scale_当日と未来日と0PVを除いて四分位数を求める(): void
    {
        $map = array(
            '2026-08-27' => 0,
            '2026-08-28' => 100,
            '2026-08-29' => 200,
            '2026-08-30' => 300,
            '2026-08-31' => 400,
            '2026-09-01' => 9999,
            '2026-09-02' => 8888,
        );

        $scale = cocoon_analytics_heatmap_scale($map, '2026-09-01');

        $this->assertSame(4, $scale['sample_count']);
        $this->assertEquals(array(175, 250, 325), $scale['thresholds']);
        $this->assertNull($scale['outlier_threshold']);
    }

    /**
     * 第3四分位数に四分位範囲の3倍を加えた値を突出日の境界にすることをテスト
     */
    public function test_scale_突出判定値は第3四分位数と3倍IQRから求める(): void
    {
        $map = array(
            '2026-08-23' => 100,
            '2026-08-24' => 200,
            '2026-08-25' => 300,
            '2026-08-26' => 400,
            '2026-08-27' => 500,
            '2026-08-28' => 600,
            '2026-08-29' => 700,
            '2026-08-30' => 10000,
        );

        $scale = cocoon_analytics_heatmap_scale($map, '2026-09-01');

        // Q1=275、Q3=625、IQR=350なので突出判定値は625+350×3になる
        $this->assertEquals(array(275, 450, 625), $scale['thresholds']);
        $this->assertEquals(1675, $scale['outlier_threshold']);
        $this->assertSame(1, cocoon_analytics_heatmap_level(100, $scale['thresholds']));
        $this->assertSame(2, cocoon_analytics_heatmap_level(300, $scale['thresholds']));
        $this->assertSame(3, cocoon_analytics_heatmap_level(500, $scale['thresholds']));
        $this->assertSame(4, cocoon_analytics_heatmap_level(700, $scale['thresholds']));
        $this->assertTrue(cocoon_analytics_heatmap_is_outlier(10000, $scale['outlier_threshold']));
    }

    /**
     * フォーラムで報告された通常500PVと2つの突出日の組み合わせをテスト
     */
    public function test_scale_フォーラム再現ケースのIQRが0でも突出値で色基準が崩れない(): void
    {
        $map = array();
        // 通常日の分布を再現するため、完了済みの30日へ500PVを設定する
        for ($day = 1; $day <= 30; $day++) {
            $map[sprintf('2026-07-%02d', $day)] = 500;
        }
        $map['2026-07-31'] = 10777;
        $map['2026-08-01'] = 36977;
        $map['2026-09-01'] = 999999;

        $scale = cocoon_analytics_heatmap_scale($map, '2026-09-01');

        // 当日の999999PVは除外されるため、完了済みの32日だけが基準になる
        $this->assertSame(32, $scale['sample_count']);
        $this->assertEquals(array(500, 500, 500), $scale['thresholds']);
        $this->assertNull($scale['outlier_threshold']);
        $this->assertSame(1, cocoon_analytics_heatmap_level(500, $scale['thresholds']));
        $this->assertSame(4, cocoon_analytics_heatmap_level(10777, $scale['thresholds']));
        $this->assertSame(4, cocoon_analytics_heatmap_level(36977, $scale['thresholds']));
        $this->assertFalse(cocoon_analytics_heatmap_is_outlier(10777, $scale['outlier_threshold']));
        $this->assertFalse(cocoon_analytics_heatmap_is_outlier(36977, $scale['outlier_threshold']));
    }

    /**
     * PVが同値に偏ってIQRが0になった場合は軽微な増加を突出扱いしないことをテスト
     */
    public function test_scale_IQRが0の場合は突出判定を無効化する(): void
    {
        $map = array();
        for ($day = 20; $day <= 27; $day++) {
            $map[sprintf('2026-08-%02d', $day)] = 500;
        }

        $scale = cocoon_analytics_heatmap_scale($map, '2026-09-01');

        $this->assertSame(8, $scale['sample_count']);
        $this->assertEquals(array(500, 500, 500), $scale['thresholds']);
        $this->assertNull($scale['outlier_threshold']);
        $this->assertFalse(cocoon_analytics_heatmap_is_outlier(501, $scale['outlier_threshold']));
    }

    /**
     * 4日未満では従来どおり最大値の25%・50%・75%を使うことをテスト
     */
    public function test_scale_3日以下は最大値比率へフォールバックする(): void
    {
        $map = array(
            '2026-08-29' => 40,
            '2026-08-30' => 80,
            '2026-08-31' => 120,
        );

        $scale = cocoon_analytics_heatmap_scale($map, '2026-09-01');

        $this->assertSame(3, $scale['sample_count']);
        $this->assertEquals(array(30, 60, 90), $scale['thresholds']);
        $this->assertNull($scale['outlier_threshold']);
    }

    /**
     * 集計対象がない場合は全境界を0にして突出判定を無効にすることをテスト
     */
    public function test_scale_完了済み非ゼロ日がなければ空の基準を返す(): void
    {
        $map = array(
            '2026-08-31' => 0,
            '2026-09-01' => 100,
            '2026-09-02' => 200,
        );

        $scale = cocoon_analytics_heatmap_scale($map, '2026-09-01');

        $this->assertSame(0, $scale['sample_count']);
        $this->assertEquals(array(0, 0, 0), $scale['thresholds']);
        $this->assertNull($scale['outlier_threshold']);
    }

    /**
     * 整数へ安全に変換できないPV値を警告なく集計対象から除外することをテスト
     */
    public function test_scale_不正または巨大なPV値は集計対象から除外する(): void
    {
        $map = array(
            '2026-08-23' => INF,
            '2026-08-24' => NAN,
            '2026-08-25' => PHP_FLOAT_MAX,
            '2026-08-26' => '1e309',
            '2026-08-27' => PHP_INT_MAX,
            '2026-08-28' => -1,
            '2026-08-29' => '-1',
            '2026-08-30' => 0,
            '2026-08-31' => '100',
        );

        $scale = cocoon_analytics_heatmap_scale($map, '2026-09-01');

        $this->assertSame(1, $scale['sample_count']);
        $this->assertEquals(array(25, 50, 75), $scale['thresholds']);
        $this->assertNull($scale['outlier_threshold']);
    }

    /**
     * パーセンタイル計算が不正値を除外し、有効な数値だけで結果を返すことをテスト
     */
    public function test_percentile_不正または巨大な数値を除外する(): void
    {
        $values = array(INF, NAN, PHP_FLOAT_MAX, '1e309', PHP_INT_MAX, -1, '-1', '100', 200);

        $this->assertSame(150.0, cocoon_analytics_heatmap_percentile($values, 0.5));
    }

    /**
     * パーセンタイル位置の不正値と無限大を、警告なく有効範囲の端へ揃えることをテスト
     */
    public function test_percentile_不正な位置を有効範囲へ揃える(): void
    {
        $values = array(100, 200);

        $this->assertSame(100.0, cocoon_analytics_heatmap_percentile($values, NAN));
        $this->assertSame(100.0, cocoon_analytics_heatmap_percentile($values, 'invalid'));
        $this->assertSame(100.0, cocoon_analytics_heatmap_percentile($values, -INF));
        $this->assertSame(200.0, cocoon_analytics_heatmap_percentile($values, INF));
        $this->assertSame(200.0, cocoon_analytics_heatmap_percentile($values, PHP_FLOAT_MAX));
        $this->assertSame(200.0, cocoon_analytics_heatmap_percentile($values, '1e309'));
    }

    /**
     * 各境界値を含むPVが想定した色レベルになることをテスト
     */
    #[DataProvider('heatmapLevelProvider')]
    public function test_level_境界値を含めて5段階へ分類する(mixed $pv, int $expected): void
    {
        $this->assertSame($expected, cocoon_analytics_heatmap_level($pv, array(100, 200, 300)));
    }

    public static function heatmapLevelProvider(): array
    {
        return array(
            '0PVは灰色' => array(0, 0),
            '第1境界より小さい' => array(1, 1),
            '第1境界と同じ' => array(100, 1),
            '第1境界を超える' => array(101, 2),
            '中央値と同じ' => array(200, 2),
            '中央値を超える' => array(201, 3),
            '第3境界と同じ' => array(300, 3),
            '第3境界を超える' => array(301, 4),
            '数値文字列' => array('101', 2),
            '負数' => array(-1, 0),
            '負の数値文字列' => array('-1', 0),
            '無限大' => array(INF, 0),
            '非数' => array(NAN, 0),
            'PHPの最大整数' => array(PHP_INT_MAX, 0),
            'PHPの最大浮動小数点数' => array(PHP_FLOAT_MAX, 0),
            '無限大になる数値文字列' => array('1e309', 0),
        );
    }

    /**
     * 四分位数が同値でも通常日と増加日を区別できることをテスト
     */
    public function test_level_重複する境界でも増加日は最濃色になる(): void
    {
        $thresholds = array(500, 500, 500);

        $this->assertSame(1, cocoon_analytics_heatmap_level(500, $thresholds));
        $this->assertSame(4, cocoon_analytics_heatmap_level(501, $thresholds));
    }

    /**
     * 突出判定は境界値を超えた場合だけ有効になることをテスト
     */
    public function test_is_outlier_境界値超過と無効値を判定する(): void
    {
        $this->assertFalse(cocoon_analytics_heatmap_is_outlier(1675, 1675));
        $this->assertTrue(cocoon_analytics_heatmap_is_outlier(1676, 1675));
        $this->assertTrue(cocoon_analytics_heatmap_is_outlier('101', '100'));
        $this->assertFalse(cocoon_analytics_heatmap_is_outlier(10000, null));
        $this->assertFalse(cocoon_analytics_heatmap_is_outlier(10000, false));

        // 整数変換できないPVや不正な判定値は、警告を出さず突出日ではないものとして扱います
        foreach (array(INF, NAN, PHP_FLOAT_MAX, '1e309', PHP_INT_MAX, -1) as $invalid_pv) {
            $this->assertFalse(cocoon_analytics_heatmap_is_outlier($invalid_pv, 100));
        }
        foreach (array(INF, NAN, PHP_FLOAT_MAX, '1e309', PHP_INT_MAX, -1) as $invalid_threshold) {
            $this->assertFalse(cocoon_analytics_heatmap_is_outlier(101, $invalid_threshold));
        }
    }

    /**
     * 色分け境界フィルターが対象PVと基準日を受け取り、3境界を変更できることをテスト
     */
    public function test_scale_色分け境界フィルターで3境界を変更できる(): void
    {
        $GLOBALS['test_mock_apply_filters_callbacks']['cocoon_analytics_heatmap_thresholds'] = function (
            array $thresholds,
            array $completed_pvs,
            string $today
        ): array {
            $this->assertEquals(array(175, 250, 325), $thresholds);
            $this->assertSame(array(100, 200, 300, 400), $completed_pvs);
            $this->assertSame('2026-09-01', $today);

            return array(10, 20, 30);
        };

        $scale = cocoon_analytics_heatmap_scale($this->fourDayMap(), '2026-09-01');

        $this->assertEquals(array(10, 20, 30), $scale['thresholds']);
    }

    /**
     * 突出判定値フィルターが対象PVと確定境界を受け取り、判定値を変更できることをテスト
     */
    public function test_scale_突出判定値フィルターで判定値を変更できる(): void
    {
        $this->setFilterCallback(
            'cocoon_analytics_heatmap_thresholds',
            static function (): array {
                return array(10, 20, 30);
            }
        );
        $GLOBALS['test_mock_apply_filters_callbacks']['cocoon_analytics_heatmap_outlier_threshold'] = function (
            mixed $outlier_threshold,
            array $completed_pvs,
            array $thresholds,
            string $today
        ): int {
            $this->assertNull($outlier_threshold);
            $this->assertSame(array(100, 200, 300, 400), $completed_pvs);
            $this->assertEquals(array(10, 20, 30), $thresholds);
            $this->assertSame('2026-09-01', $today);

            return 50;
        };

        $scale = cocoon_analytics_heatmap_scale($this->fourDayMap(), '2026-09-01');

        $this->assertEquals(50, $scale['outlier_threshold']);
    }

    /**
     * フィルターへfalseを返すと突出表示を明示的に無効化できることをテスト
     */
    public function test_scale_突出判定値フィルターで突出表示を無効化できる(): void
    {
        $this->setFilterCallback(
            'cocoon_analytics_heatmap_outlier_threshold',
            static function (): bool {
                return false;
            }
        );

        $scale = cocoon_analytics_heatmap_scale($this->eightDayMap(), '2026-09-01');

        $this->assertNull($scale['outlier_threshold']);
    }

    /**
     * 有効な数値文字列を返すフィルターは浮動小数点数へ正規化して採用することをテスト
     */
    public function test_scale_数値文字列のフィルター値を採用する(): void
    {
        $this->setFilterCallback(
            'cocoon_analytics_heatmap_thresholds',
            static function (): array {
                return array('10', '20', '30');
            }
        );
        $this->setFilterCallback(
            'cocoon_analytics_heatmap_outlier_threshold',
            static function (): string {
                return '50';
            }
        );

        $scale = cocoon_analytics_heatmap_scale($this->fourDayMap(), '2026-09-01');

        $this->assertSame(array(10.0, 20.0, 30.0), $scale['thresholds']);
        $this->assertSame(50.0, $scale['outlier_threshold']);
    }

    /**
     * 不正な色分け境界値を採用せず、自動計算した基準へ戻すことをテスト
     */
    #[DataProvider('invalidThresholdsProvider')]
    public function test_scale_不正な色分け境界フィルター値は自動計算した基準へ戻す(array $invalid): void
    {
        $this->setFilterCallback(
            'cocoon_analytics_heatmap_thresholds',
            static function () use ($invalid): array {
                return $invalid;
            }
        );

        $scale = cocoon_analytics_heatmap_scale($this->fourDayMap(), '2026-09-01');

        $this->assertEquals(array(175, 250, 325), $scale['thresholds']);
    }

    public static function invalidThresholdsProvider(): array
    {
        return array(
            '降順' => array(array(300, 200, 100)),
            '負数' => array(array(-1, 200, 300)),
            '無限大' => array(array(100, INF, 300)),
            '非数' => array(array(100, NAN, 300)),
            'PHPの最大整数' => array(array(100, 200, PHP_INT_MAX)),
            'PHPの最大浮動小数点数' => array(array(100, 200, PHP_FLOAT_MAX)),
            '無限大になる数値文字列' => array(array(100, 200, '1e309')),
        );
    }

    /**
     * 不正な突出判定値を採用せず、自動計算した基準へ戻すことをテスト
     */
    #[DataProvider('invalidOutlierThresholdProvider')]
    public function test_scale_不正な突出判定フィルター値は自動計算した基準へ戻す(mixed $invalid): void
    {
        $this->setFilterCallback(
            'cocoon_analytics_heatmap_outlier_threshold',
            static function () use ($invalid): mixed {
                return $invalid;
            }
        );

        $scale = cocoon_analytics_heatmap_scale($this->eightDayMap(), '2026-09-01');

        $this->assertEquals(1675, $scale['outlier_threshold']);
    }

    public static function invalidOutlierThresholdProvider(): array
    {
        return array(
            '文字列' => array('invalid'),
            '負数' => array(-1),
            '無限大' => array(INF),
            '非数' => array(NAN),
            'PHPの最大整数' => array(PHP_INT_MAX),
            'PHPの最大浮動小数点数' => array(PHP_FLOAT_MAX),
            '無限大になる数値文字列' => array('1e309'),
        );
    }

    /**
     * 通常の境界値では0PVとすべての色レベルを凡例項目にすることをテスト
     */
    public function test_legend_items_各色とPV範囲を一対一で返す(): void
    {
        $this->assertSame(
            array(
                array('level' => 0, 'min' => 0, 'max' => 0),
                array('level' => 1, 'min' => 1, 'max' => 100),
                array('level' => 2, 'min' => 101, 'max' => 200),
                array('level' => 3, 'min' => 201, 'max' => 300),
                array('level' => 4, 'min' => 301, 'max' => null),
            ),
            cocoon_analytics_heatmap_legend_items(array(100, 200, 300))
        );
    }

    /**
     * 重複境界によって空になる色レベルは凡例から省くことをテスト
     */
    public function test_legend_items_重複境界では空の色レベルを省く(): void
    {
        $this->assertSame(
            array(
                array('level' => 0, 'min' => 0, 'max' => 0),
                array('level' => 1, 'min' => 1, 'max' => 500),
                array('level' => 4, 'min' => 501, 'max' => null),
            ),
            cocoon_analytics_heatmap_legend_items(array(500, 500, 500))
        );
    }

    /**
     * 不正な境界値では途中までの凡例を返さず、0PVの項目だけへ戻すことをテスト
     */
    #[DataProvider('invalidLegendThresholdsProvider')]
    public function test_legend_items_不正な境界値は0PV項目だけへ戻す(array $thresholds): void
    {
        $this->assertSame(
            array(array('level' => 0, 'min' => 0, 'max' => 0)),
            cocoon_analytics_heatmap_legend_items($thresholds)
        );
    }

    public static function invalidLegendThresholdsProvider(): array
    {
        return array(
            '要素不足' => array(array(100, 200)),
            '降順' => array(array(300, 200, 100)),
            '負数' => array(array(-1, 200, 300)),
            '無限大' => array(array(100, INF, 300)),
            '非数' => array(array(100, NAN, 300)),
            'PHPの最大整数' => array(array(100, 200, PHP_INT_MAX)),
            'PHPの最大浮動小数点数' => array(array(100, 200, PHP_FLOAT_MAX)),
            '無限大になる数値文字列' => array(array(100, 200, '1e309')),
        );
    }

    /**
     * 描画時の不正なPV値を警告なく0PVへ揃え、集計基準と表示を一致させることをテスト
     */
    public function test_render_heatmap_不正または巨大なPV値は0PVとして表示する(): void
    {
        $today = current_time('Y-m-d');
        $invalid_values = array(INF, NAN, PHP_FLOAT_MAX, '1e309', PHP_INT_MAX, -1);
        $map = array();

        // 各不正値を別の日へ割り当て、すべての描画入口を1回のHTML出力で確認します
        foreach ($invalid_values as $index => $invalid_value) {
            $date = date('Y-m-d', strtotime($today . ' -' . ($index + 1) . ' days'));
            $map[$date] = $invalid_value;
        }

        $output = $this->renderHeatmap($map);

        foreach (array_keys($map) as $date) {
            $class = $this->heatmapCellClassByAriaLabel($output, $date . ' : 0 PV');
            $this->assertStringContainsString('is-level-0', $class);
            $this->assertStringNotContainsString('is-outlier', $class);
        }
        $this->assertStringNotContainsString('-1 PV', $output);
    }

    /**
     * 突出日は境界フックで通常色が薄くなっても最濃色と枠を併用することをテスト
     */
    public function test_render_heatmap_突出日は最濃色と枠と説明を出力する(): void
    {
        $today = current_time('Y-m-d');
        $past_date = date('Y-m-d', strtotime($today . ' -1 day'));
        $future_date = date('Y-m-d', strtotime($today . ' +1 day'));

        $this->setFilterCallback(
            'cocoon_analytics_heatmap_thresholds',
            static function (): array {
                return array(1000, 2000, 3000);
            }
        );
        $this->setFilterCallback(
            'cocoon_analytics_heatmap_outlier_threshold',
            static function (): int {
                return 50;
            }
        );

        $output = $this->renderHeatmap(
            array(
                $past_date => 800,
                $future_date => 9000,
            )
        );

        $past_label = $past_date . ' : 800 PV / 突出日';
        $future_label = $future_date . ' : 9,000 PV';
        $past_class = $this->heatmapCellClassByAriaLabel($output, $past_label);
        $future_class = $this->heatmapCellClassByTooltip($output, $future_label);

        $this->assertStringContainsString('is-level-4', $past_class);
        $this->assertStringContainsString('is-outlier', $past_class);
        $this->assertStringContainsString('is-future', $future_class);
        $this->assertStringNotContainsString('is-outlier', $future_class);
        $this->assertStringContainsString('data-tooltip="' . $past_label . '"', $output);
        $this->assertStringContainsString('aria-hidden="true" data-tooltip="' . $future_label . '"', $output);
        $this->assertStringContainsString('cocoon-analytics-heatmap-legend-outlier', $output);
        $this->assertStringNotContainsString('tabindex=', $output);
    }

    /**
     * 突出判定が無効なら突出セルと突出凡例を出力しないことをテスト
     */
    public function test_render_heatmap_突出判定が無効なら枠と凡例を出力しない(): void
    {
        $today = current_time('Y-m-d');
        $map = array();
        for ($days_ago = 10; $days_ago >= 3; $days_ago--) {
            $map[date('Y-m-d', strtotime($today . ' -' . $days_ago . ' days'))] = 500;
        }
        $past_date = date('Y-m-d', strtotime($today . ' -1 day'));
        $map[$past_date] = 501;

        $output = $this->renderHeatmap($map);

        $this->assertStringNotContainsString('is-outlier', $output);
        $this->assertStringNotContainsString('cocoon-analytics-heatmap-legend-outlier', $output);
        $this->assertStringNotContainsString('/ 突出日', $output);
        $this->assertStringContainsString('aria-label="' . $past_date . ' : 501 PV"', $output);
    }

    /**
     * フィルターテストで共通利用する4日分のPVを返す
     */
    private function fourDayMap(): array
    {
        return array(
            '2026-08-28' => 100,
            '2026-08-29' => 200,
            '2026-08-30' => 300,
            '2026-08-31' => 400,
        );
    }

    /**
     * 安定した突出判定値を得られる8日分のPVを返す
     */
    private function eightDayMap(): array
    {
        return array(
            '2026-08-23' => 100,
            '2026-08-24' => 200,
            '2026-08-25' => 300,
            '2026-08-26' => 400,
            '2026-08-27' => 500,
            '2026-08-28' => 600,
            '2026-08-29' => 700,
            '2026-08-30' => 10000,
        );
    }

    /**
     * テスト用のフィルターコールバックを登録する
     */
    private function setFilterCallback(string $tag, callable $callback): void
    {
        $GLOBALS['test_mock_apply_filters_callbacks'][$tag] = $callback;
    }

    /**
     * WordPressの日時・数値関数を固定し、ヒートマップHTMLを取得する
     */
    private function renderHeatmap(array $map): string
    {
        Functions\when('cocoon_analytics_daily_pv_map')->justReturn($map);
        Functions\when('date_i18n')->alias(static function (string $format, mixed $timestamp = false): string {
            return date($format, $timestamp === false ? time() : (int) $timestamp);
        });
        Functions\when('number_format_i18n')->alias(static function (mixed $number, int $decimals = 0): string {
            return number_format((float) $number, $decimals, '.', ',');
        });
        Functions\when('esc_html__')->alias(static function (string $text): string {
            return esc_html($text);
        });

        // 日付ラベルをテストしやすい形式へ固定し、終了後は元のオプション状態へ戻します
        $had_options = array_key_exists('test_mock_options', $GLOBALS);
        $previous_options = $GLOBALS['test_mock_options'] ?? null;
        $GLOBALS['test_mock_options'] = is_array($previous_options) ? $previous_options : array();
        $GLOBALS['test_mock_options']['date_format'] = 'Y-m-d';

        ob_start();
        try {
            cocoon_analytics_render_heatmap();
            $output = (string) ob_get_clean();
        } catch (\Throwable $error) {
            ob_end_clean();
            throw $error;
        } finally {
            if ($had_options) {
                $GLOBALS['test_mock_options'] = $previous_options;
            } else {
                unset($GLOBALS['test_mock_options']);
            }
        }

        return $output;
    }

    /**
     * aria-labelで対象セルを特定し、class属性を取得する
     */
    private function heatmapCellClassByAriaLabel(string $html, string $aria_label): string
    {
        // 属性値を正規表現用にエスケープし、同じdiv要素内のclass属性だけを取り出します
        $pattern = '/<div class="([^"]*)"[^>]*aria-label="' . preg_quote($aria_label, '/') . '"[^>]*><\/div>/';
        $matched = preg_match($pattern, $html, $matches);

        $this->assertSame(1, $matched, '対象のaria-labelを持つヒートマップセルが見つかりません。');
        return $matches[1] ?? '';
    }

    /**
     * data-tooltipで対象セルを特定し、class属性を取得する
     */
    private function heatmapCellClassByTooltip(string $html, string $tooltip): string
    {
        // 未来日は読み上げ対象外なので、表示用ツールチップを使って対象セルを特定します
        $pattern = '/<div class="([^"]*)"[^>]*data-tooltip="' . preg_quote($tooltip, '/') . '"[^>]*><\/div>/';
        $matched = preg_match($pattern, $html, $matches);

        $this->assertSame(1, $matched, '対象のdata-tooltipを持つヒートマップセルが見つかりません。');
        return $matches[1] ?? '';
    }
}
