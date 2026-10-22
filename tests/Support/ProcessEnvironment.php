<?php
/**
 * 子プロセスへ渡す空文字の環境変数の保持
 */
namespace Cocoon\Tests\Support;

class ProcessEnvironment
{
    public static function preserveEmptyValues(array $environment): array
    {
        foreach ($environment as $name => $value) {
            if (!is_string($name) || $value !== '') continue;
            // PHPによる空文字の省略を避ける、名前付き配列からNAME=形式への変換
            unset($environment[$name]);
            $environment[] = $name . '=';
        }
        return $environment;
    }
}
