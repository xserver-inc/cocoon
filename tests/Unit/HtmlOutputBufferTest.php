<?php

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class HtmlOutputBufferTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once dirname(__DIR__, 2) . '/lib/page-speed-up/minify-html.php';
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_rest_requests_do_not_start_an_html_buffer(): void
    {
        define('REST_REQUEST', true);
        Functions\when('is_feed')->justReturn(false);

        $initial_level = ob_get_level();
        try {
            code_minify_buffer_start();
            $level = ob_get_level();
        } finally {
            while (ob_get_level() > $initial_level) {
                ob_end_clean();
            }
        }

        $this->assertSame($initial_level, $level);
    }

    public function test_shutdown_leaves_another_output_buffer_open(): void
    {
        $initial_level = ob_get_level();
        $json = json_encode(array('content' => str_repeat('REST response content. ', 4000)));

        // 誤った出力の漏れを受け止める外側のバッファ
        ob_start();
        ob_start();
        try {
            echo $json;
            code_minify_buffer_end();
            $level = ob_get_level();
            $body = ob_get_contents();
        } finally {
            while (ob_get_level() > $initial_level) {
                ob_end_clean();
            }
        }

        $this->assertSame($initial_level + 2, $level);
        $this->assertSame($json, $body);
    }

    public function test_shutdown_flushes_the_theme_html_into_the_outer_buffer(): void
    {
        Functions\when('is_feed')->justReturn(false);
        $initial_level = ob_get_level();
        $html = '<p>HTML response</p>';

        ob_start();
        try {
            code_minify_buffer_start();
            echo $html;
            code_minify_buffer_end();
            $level = ob_get_level();
            $body = ob_get_contents();
        } finally {
            while (ob_get_level() > $initial_level) {
                ob_end_clean();
            }
        }

        $this->assertSame($initial_level + 1, $level);
        $this->assertSame($html, $body);
    }
}
