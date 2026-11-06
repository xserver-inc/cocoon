<?php
/**
 * 検索フォームの入力形式と検索語の保持に関する統合テスト
 */

namespace Cocoon\Tests\Integration;

class SearchFormIntegrationTest extends IntegrationTestCase
{
    /**
     * 未入力・日本語・引用符を含む検索語の検証データ
     */
    public static function searchQueries(): array
    {
        return [
            '未入力' => [null, ''],
            '通常の検索語' => ['Cocoon', 'Cocoon'],
            '数字のゼロ' => ['0', '0'],
            '日本語と特殊文字' => ['検索 "引用" \'単語\' & <見出し>', '検索 "引用" \'単語\' & <見出し>'],
        ];
    }

    /**
     * 実際のテンプレートでの検索入力欄・GET送信先・検索語の保持
     *
     * @dataProvider searchQueries
     */
    public function testSearchFormPreservesQueryAndSubmission(?string $query, string $expected): void
    {
        $originalGet = $_GET;

        try {
            unset($_GET['s']);
            if ($query !== null) {
                $_GET['s'] = wp_slash($query);
            }
            $html = get_search_form(false);
        } finally {
            $_GET = $originalGet;
        }

        // HTML属性の復号後の検索語と、実際のフォーム構造の照合
        $document = new \DOMDocument();
        $document->loadHTML('<!doctype html><meta charset="UTF-8">' . $html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new \DOMXPath($document);
        $inputs = $xpath->query('//form[@role="search"]//input[@name="s"]');
        $this->assertCount(1, $inputs);

        $input = $inputs->item(0);
        $this->assertSame('search', $input->getAttribute('type'));
        $this->assertSame($expected, $input->getAttribute('value'));

        $form = $input->parentNode;
        $this->assertSame('form', $form->nodeName);
        $this->assertSame('get', $form->getAttribute('method'));
        $this->assertSame(home_url('/'), $form->getAttribute('action'));
        $this->assertCount(1, $xpath->query('.//button[@type="submit"]', $form));
    }
}
