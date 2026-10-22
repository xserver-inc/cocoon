<?php
/**
 * ファイル書き出しの整数精度・順位・破損時のリソース解放の検証
 */
namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

require_once dirname(__DIR__, 2) . '/lib/page-access/analytics/export-stream-func.php';

class AccessExportStreamTest extends TestCase
{
    public function testAdjacentHugePvValuesRemainDistinctBeyondIntegerAndFloatPrecision(): void
    {
        $largest = str_repeat('9', 65);
        $adjacent = str_repeat('9', 64) . '8';
        // floatで同値になる隣接値と、PHP整数を超える65桁までの大小の確認
        foreach (array(
            array('9007199254740993', '9007199254740992'),
            array('9223372036854775808', '9223372036854775807'),
            array($largest, $adjacent),
            array('-9007199254740992', '-9007199254740993'),
            array('-9223372036854775807', '-9223372036854775808'),
            array('-' . $adjacent, '-' . $largest),
            array('100000000000000000000', '99999999999999999999'),
            array('-99999999999999999999', '-100000000000000000000'),
        ) as $pair) {
            $greater = $this->record(1, $pair[0]);
            $lesser = $this->record(1, $pair[1]);
            $this->assertLessThan(0, cocoon_analytics_export_compare_records($greater, $lesser), implode(' > ', $pair));
            $this->assertGreaterThan(0, cocoon_analytics_export_compare_records($lesser, $greater), implode(' > ', $pair));
        }
    }

    public function testSignsLeadingZerosAndNullFollowNumericDescendingOrder(): void
    {
        $records = array(
            $this->record(1, null),
            $this->record(2, '-100'),
            $this->record(3, '0'),
            $this->record(4, '+0002'),
            $this->record(5, '-1'),
            $this->record(6, '-000'),
            $this->record(7, str_repeat('9', 65)),
            $this->record(8, '2'),
            $this->record(9, '-9'),
        );
        usort($records, 'cocoon_analytics_export_compare_records');
        $this->assertSame(array(7, 8, 4, 6, 3, 5, 9, 2, 1), array_column($records, 'post_id'));
        $this->assertSame(0, cocoon_analytics_export_compare_records($this->record(1, '+00012'), $this->record(1, '12')));
        $this->assertSame(0, cocoon_analytics_export_compare_records($this->record(1, '-000'), $this->record(1, '0')));
        $this->assertGreaterThan(0, cocoon_analytics_export_compare_records($this->record(999, null), $this->record(1, '-' . str_repeat('9', 65))));
    }

    public function testEqualPvUsesPublicationDateAndThenPostIdInDescendingOrder(): void
    {
        $records = array(
            $this->record(3, '7', '2026-10-21 23:59:59'),
            $this->record(1, '+0007', '2026-10-22 00:00:00'),
            $this->record(9, '7', '2025-12-31 23:59:59'),
            $this->record(2, 7, '2026-10-22 00:00:00'),
        );
        usort($records, 'cocoon_analytics_export_compare_records');
        $this->assertSame(array(2, 1, 3, 9), array_column($records, 'post_id'));
        // NULL同士にも適用する、DBの第2・第3ソートキーの確認
        $this->assertLessThan(0, cocoon_analytics_export_compare_records($this->record(1, null, '2026-10-22 00:00:00'), $this->record(9, null, '2026-10-21 23:59:59')));
        $this->assertLessThan(0, cocoon_analytics_export_compare_records($this->record(9, null), $this->record(1, null)));
        $this->assertSame(0, cocoon_analytics_export_compare_records($this->record(9, null), $this->record(9, null)));
    }

    public function testMergePreservesEveryRecordAndItsExactTypesAcrossBothRuns(): void
    {
        $leftRecords = array(
            $this->record(101, str_repeat('9', 65)),
            $this->record(110, '7', '2026-09-01 00:00:00'),
            $this->record(130, '-2'),
            $this->record(150, null, '2026-09-01 00:00:00'),
        );
        $rightRecords = array(
            $this->record(102, str_repeat('9', 64) . '8'),
            $this->record(120, '7', '2026-10-01 00:00:00'),
            $this->record(140, '-10'),
            $this->record(160, null, '2026-10-01 00:00:00'),
        );
        $left = $this->stream($leftRecords);
        $right = $this->stream($rightRecords);
        $output = cocoon_analytics_export_merge_streams($left, $right);
        try {
            $this->assertIsResource($output);
            $this->assertFalse(is_resource($left));
            $this->assertFalse(is_resource($right));
            $this->assertSame(0, ftell($output));
            // 本体の比較関数や読取関数に依存しない、決定済みの全件順序との照合
            $expected = array($leftRecords[0], $rightRecords[0], $rightRecords[1], $leftRecords[1], $leftRecords[2], $rightRecords[2], $rightRecords[3], $leftRecords[3]);
            $this->assertSame($expected, $this->readJsonLines($output));
        } finally {
            foreach (array($left, $right, $output) as $stream) if (is_resource($stream)) fclose($stream);
        }
    }

    public function testMergeSupportsEmptyRunsAndKeepsDuplicateRecords(): void
    {
        $record = $this->record(1, '9007199254740993');
        foreach (array(array(array(), array()), array(array(), array($record)), array(array($record), array()), array(array($record), array($record))) as $inputs) {
            $left = $this->stream($inputs[0]);
            $right = $this->stream($inputs[1]);
            $output = cocoon_analytics_export_merge_streams($left, $right);
            try {
                $this->assertIsResource($output);
                $this->assertFalse(is_resource($left));
                $this->assertFalse(is_resource($right));
                $this->assertSame(array_merge($inputs[0], $inputs[1]), $this->readJsonLines($output));
            } finally {
                foreach (array($left, $right, $output) as $stream) if (is_resource($stream)) fclose($stream);
            }
        }
    }

    public function testCorruptRunFailsWithoutReturningPartialDataOrLeakingStreams(): void
    {
        foreach (array("{broken-json}\n", "{\"post_id\":1,\"post_type\":\"post\",\"post_date\":\"2026-10-22 00:00:00\"}\n") as $corruption) {
            $before = array_keys(get_resources('stream'));
            $left = $this->stream(array($this->record(1, '10')));
            $right = $this->stream(array($this->record(2, '1')));
            fseek($left, 0, SEEK_END);
            fwrite($left, $corruption);
            rewind($left);
            $output = null;
            try {
                $output = cocoon_analytics_export_merge_streams($left, $right);
                $this->assertFalse($output);
                $this->assertFalse(is_resource($left));
                $this->assertFalse(is_resource($right));
                $this->assertSame(array(), array_values(array_diff(array_keys(get_resources('stream')), $before)));
            } finally {
                foreach (array($left, $right, $output) as $stream) if (is_resource($stream)) fclose($stream);
            }
        }
    }

    public function testWriteFailureIsReportedForAnUnwritableDestination(): void
    {
        $stream = fopen(__FILE__, 'rb');
        try {
            $this->assertIsResource($stream);
            $this->assertFalse(cocoon_analytics_export_write_record($stream, $this->record(1, '10')));
        } finally {
            if (is_resource($stream)) fclose($stream);
        }
    }

    private function record(int $id, $pv, string $date = '2026-10-22 00:00:00'): array
    {
        return array('post_id' => $id, 'post_type' => 'post', 'post_date' => $date, 'pv' => $pv);
    }

    private function stream(array $records)
    {
        $stream = fopen('php://temp', 'w+b');
        $this->assertIsResource($stream);
        foreach ($records as $record) fwrite($stream, json_encode($record) . "\n");
        rewind($stream);
        return $stream;
    }

    private function readJsonLines($stream): array
    {
        $records = array();
        while (($line = fgets($stream)) !== false) {
            $record = json_decode($line, true);
            $this->assertIsArray($record);
            $records[] = $record;
        }
        $this->assertTrue(feof($stream));
        return $records;
    }
}
