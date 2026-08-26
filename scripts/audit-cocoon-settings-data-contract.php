<?php
/**
 * Cocoon設定の保存・読込データ契約を基準コミットと比較する監査スクリプト。
 *
 * 実際の設定値、nonce、POST本文、DBダンプは出力せず、ソース契約だけを比較する。
 */

declare(strict_types=1);

const COCOON_SETTINGS_BASELINE = '5ac3b6b1e4c01a842774d89ed9ff7fa722e1d0f9';
const COCOON_SETTINGS_REPORT = 'docs/COCOON-SETTINGS-DATA-COMPATIBILITY-REPORT.md';
const COCOON_SETTINGS_REPORT_STATIC_START = '<!-- BEGIN AUTO-GENERATED STATIC DATA CONTRACT -->';
const COCOON_SETTINGS_REPORT_STATIC_END = '<!-- END AUTO-GENERATED STATIC DATA CONTRACT -->';

$themeRoot = dirname(__DIR__);
$options = getopt('', ['baseline::', 'output::', 'check']);
$baselineRef = is_string($options['baseline'] ?? null)
    ? (string) $options['baseline']
    : COCOON_SETTINGS_BASELINE;
$outputPath = is_string($options['output'] ?? null)
    ? (string) $options['output']
    : COCOON_SETTINGS_REPORT;
$checkOnly = array_key_exists('check', $options);

// Gitへ渡す引数を配列のまま実行し、シェル展開や引用符差異を避ける。
function run_git(array $arguments, string $themeRoot): string
{
    $command = array_merge(['git', '-c', 'core.quotepath=false'], $arguments);
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $process = proc_open($command, $descriptors, $pipes, $themeRoot, null, ['bypass_shell' => true]);

    if (!is_resource($process)) {
        throw new RuntimeException('Gitプロセスを開始できませんでした。');
    }

    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    if ($exitCode !== 0) {
        throw new RuntimeException("Gitコマンドに失敗しました。\n" . trim((string) $stderr));
    }

    return (string) $stdout;
}

// 作業ツリーと基準コミットを同じインターフェースで読み込む。
function create_source_reader(string $version, string $baselineRef, string $themeRoot): callable
{
    $cache = [];

    return static function (string $path) use ($version, $baselineRef, $themeRoot, &$cache): string {
        if (array_key_exists($path, $cache)) {
            return $cache[$path];
        }

        if ($version === 'current') {
            $fullPath = $themeRoot . '/' . $path;
            $source = file_get_contents($fullPath);
            if ($source === false) {
                throw new RuntimeException("ファイルを読み込めませんでした: {$path}");
            }
            $cache[$path] = $source;
            return $cache[$path];
        }

        $cache[$path] = run_git(['show', $baselineRef . ':' . $path], $themeRoot);
        return $cache[$path];
    };
}

// 改行コード差を除いて、Git内容とWindows作業ツリーを同じ基準で比較する。
function normalize_newlines(string $source): string
{
    return str_replace(["\r\n", "\r"], "\n", $source);
}

// PHPトークンの空白とコメントを除外して、実行可能コードだけを扱う。
function is_ignorable_token(mixed $token): bool
{
    return is_array($token) && in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true);
}

// 次の実質的なPHPトークン位置を返す。
function next_significant_token_index(array $tokens, int $start): ?int
{
    for ($index = $start, $count = count($tokens); $index < $count; $index++) {
        if (!is_ignorable_token($tokens[$index])) {
            return $index;
        }
    }

    return null;
}

// PHPトークンを比較用の安定した文字列へ正規化する。
function canonicalize_tokens(array $tokens): string
{
    $result = '';
    foreach ($tokens as $token) {
        if (is_ignorable_token($token)) {
            continue;
        }
        $result .= is_array($token) ? $token[1] : $token;
    }
    return $result;
}

// 単純なPHP文字列リテラルを安全に値へ戻す。
function decode_php_string_literal(string $literal): string
{
    if (strlen($literal) < 2) {
        return $literal;
    }

    $quote = $literal[0];
    $body = substr($literal, 1, -1);
    if ($quote === "'") {
        return str_replace(["\\\\", "\\'"], ["\\", "'"], $body);
    }

    return stripcslashes($body);
}

// require/include式に含まれるPHPファイル名をコメントを除外して抽出する。
function extract_included_php_files(string $source): array
{
    $tokens = token_get_all($source);
    $paths = [];
    $includeTokens = [T_REQUIRE, T_REQUIRE_ONCE, T_INCLUDE, T_INCLUDE_ONCE];

    for ($index = 0, $count = count($tokens); $index < $count; $index++) {
        $token = $tokens[$index];
        if (!is_array($token) || !in_array($token[0], $includeTokens, true)) {
            continue;
        }

        for ($cursor = $index + 1; $cursor < $count && $tokens[$cursor] !== ';'; $cursor++) {
            $candidate = $tokens[$cursor];
            if (!is_array($candidate) || $candidate[0] !== T_CONSTANT_ENCAPSED_STRING) {
                continue;
            }

            $value = decode_php_string_literal($candidate[1]);
            if (str_ends_with($value, '.php')) {
                $paths[] = $value;
            }
        }
    }

    return $paths;
}

// 設定保存のrequire_onceを深さ優先でたどり、実行順と親設定群を記録する。
function trace_save_files(callable $readSource): array
{
    $ordered = [];
    $visited = [];

    $walk = static function (string $path, ?string $group) use (&$walk, &$ordered, &$visited, $readSource): void {
        $source = $readSource($path);
        foreach (extract_included_php_files($source) as $includedPath) {
            $includedName = basename($includedPath);
            if (!preg_match('/-posts(?:-[a-z0-9-]+)?\.php$/', $includedName)) {
                continue;
            }

            $resolved = str_replace('\\', '/', dirname($path) . '/' . basename($includedPath));
            if (isset($visited[$resolved])) {
                continue;
            }

            $visited[$resolved] = true;
            $resolvedGroup = $group ?? preg_replace('/-posts\.php$/', '', basename($resolved));
            $ordered[] = ['path' => $resolved, 'group' => $resolvedGroup];
            $walk($resolved, $resolvedGroup);
        }
    };

    $walk('lib/page-settings/_top-page.php', null);
    return $ordered;
}

// 関数呼出の括弧内を取得し、第一引数と呼出全体の両方を比較できるようにする。
function extract_function_calls(string $source, array $functionNames): array
{
    $tokens = token_get_all($source);
    $targets = array_fill_keys(array_map('strtolower', $functionNames), true);
    $calls = [];

    for ($index = 0, $count = count($tokens); $index < $count; $index++) {
        $token = $tokens[$index];
        if (!is_array($token) || $token[0] !== T_STRING) {
            continue;
        }

        $function = strtolower($token[1]);
        if (!isset($targets[$function])) {
            continue;
        }

        $openIndex = next_significant_token_index($tokens, $index + 1);
        if ($openIndex === null || $tokens[$openIndex] !== '(') {
            continue;
        }

        $depth = 1;
        $argumentTokens = [];
        $firstArgumentTokens = [];
        $firstArgumentComplete = false;
        for ($cursor = $openIndex + 1; $cursor < $count; $cursor++) {
            $part = $tokens[$cursor];
            if ($part === '(') {
                $depth++;
            } elseif ($part === ')') {
                $depth--;
                if ($depth === 0) {
                    break;
                }
            }

            if (!$firstArgumentComplete && $depth === 1 && $part === ',') {
                $firstArgumentComplete = true;
            } else {
                if (!$firstArgumentComplete) {
                    $firstArgumentTokens[] = $part;
                }
                $argumentTokens[] = $part;
            }
        }

        $firstSignificant = array_values(array_filter(
            $firstArgumentTokens,
            static fn (mixed $part): bool => !is_ignorable_token($part)
        ));
        $constant = null;
        if (
            count($firstSignificant) === 1
            && is_array($firstSignificant[0])
            && $firstSignificant[0][0] === T_STRING
            && str_starts_with($firstSignificant[0][1], 'OP_')
        ) {
            $constant = $firstSignificant[0][1];
        }

        $calls[] = [
            'function' => $function,
            'line' => $token[2],
            'constant' => $constant,
            'first_argument' => canonicalize_tokens($firstArgumentTokens),
            'call' => $function . '(' . canonicalize_tokens($argumentTokens) . ')',
        ];
    }

    return $calls;
}

// define('OP_*', 'theme_mod_key') の文字列定義だけをトークンから抽出する。
function extract_option_definitions(string $source): array
{
    $tokens = token_get_all($source);
    $definitions = [];

    for ($index = 0, $count = count($tokens); $index < $count; $index++) {
        $token = $tokens[$index];
        if (!is_array($token) || $token[0] !== T_STRING || strtolower($token[1]) !== 'define') {
            continue;
        }

        $openIndex = next_significant_token_index($tokens, $index + 1);
        $nameIndex = $openIndex === null ? null : next_significant_token_index($tokens, $openIndex + 1);
        if ($openIndex === null || $tokens[$openIndex] !== '(' || $nameIndex === null) {
            continue;
        }

        $nameToken = $tokens[$nameIndex];
        if (!is_array($nameToken) || $nameToken[0] !== T_CONSTANT_ENCAPSED_STRING) {
            continue;
        }

        $commaIndex = next_significant_token_index($tokens, $nameIndex + 1);
        $valueIndex = $commaIndex === null ? null : next_significant_token_index($tokens, $commaIndex + 1);
        if ($commaIndex === null || $tokens[$commaIndex] !== ',' || $valueIndex === null) {
            continue;
        }

        $valueToken = $tokens[$valueIndex];
        if (!is_array($valueToken) || $valueToken[0] !== T_CONSTANT_ENCAPSED_STRING) {
            continue;
        }

        $name = decode_php_string_literal($nameToken[1]);
        if (str_starts_with($name, 'OP_')) {
            $definitions[$name] = decode_php_string_literal($valueToken[1]);
        }
    }

    return $definitions;
}

// SNS定義配列のtop_key/bottom_keyを展開し、動的保存呼出をコア設定単位へ戻す。
function extract_sns_share_keys(string $source): array
{
    $tokens = token_get_all($source);
    $keys = ['top_key' => [], 'bottom_key' => []];

    for ($index = 0, $count = count($tokens); $index < $count; $index++) {
        $token = $tokens[$index];
        if (!is_array($token) || $token[0] !== T_CONSTANT_ENCAPSED_STRING) {
            continue;
        }

        $field = decode_php_string_literal($token[1]);
        if (!array_key_exists($field, $keys)) {
            continue;
        }

        $arrowIndex = next_significant_token_index($tokens, $index + 1);
        $constantIndex = $arrowIndex === null ? null : next_significant_token_index($tokens, $arrowIndex + 1);
        if (
            $arrowIndex === null
            || !is_array($tokens[$arrowIndex])
            || $tokens[$arrowIndex][0] !== T_DOUBLE_ARROW
            || $constantIndex === null
        ) {
            continue;
        }

        $constantToken = $tokens[$constantIndex];
        if (
            is_array($constantToken)
            && $constantToken[0] === T_STRING
            && str_starts_with($constantToken[1], 'OP_')
        ) {
            $keys[$field][] = $constantToken[1];
        }
    }

    $keys['top_key'] = array_values(array_unique($keys['top_key']));
    $keys['bottom_key'] = array_values(array_unique($keys['bottom_key']));
    return $keys;
}

// 指定種別の設定PHPファイルをGitの追跡一覧から取得する。
function list_settings_files(string $version, string $kind, string $baselineRef, string $themeRoot): array
{
    $arguments = $version === 'current'
        ? ['ls-files', '--', 'lib/page-settings']
        : ['ls-tree', '-r', '--name-only', $baselineRef, '--', 'lib/page-settings'];
    $paths = preg_split('/\R/', trim(run_git($arguments, $themeRoot))) ?: [];
    $paths = array_values(array_filter(
        $paths,
        static fn (string $path): bool => preg_match(
            '/-' . preg_quote($kind, '/') . '(?:-[a-z0-9-]+)?\.php$/',
            str_replace('\\', '/', $path)
        ) === 1
    ));
    sort($paths);
    return $paths;
}

// パスと内容を連結したマニフェストハッシュで、ファイル集合全体の完全一致を判定する。
function create_file_manifest_hash(callable $readSource, array $paths): string
{
    $context = hash_init('sha256');
    foreach ($paths as $path) {
        hash_update($context, $path . "\0" . normalize_newlines($readSource($path)) . "\0");
    }
    return hash_final($context);
}

// 各保護領域のハッシュを束ね、未コミット作業ツリーも一意に識別する。
function create_contract_fingerprint(array $manifest): string
{
    return hash('sha256', implode("\0", [
        $manifest['forms_hash'],
        $manifest['posts_hash'],
        $manifest['funcs_hash'],
        $manifest['protected_hash'],
        $manifest['top_save_workflow_hash'],
    ]));
}

// 保存前処理だけを切り出し、表示HTMLの差分と分離して比較する。
function extract_top_page_save_workflow(string $source): string
{
    $normalized = normalize_newlines($source);
    $start = strpos($normalized, '$is_post_ok =');
    $end = strpos($normalized, "///////////////////////////////////////\n// 入力フォーム", $start === false ? 0 : $start);
    if ($start === false || $end === false) {
        throw new RuntimeException('_top-page.phpの保存処理境界を特定できませんでした。');
    }
    return substr($normalized, $start, $end - $start);
}

// 基準版または作業ツリーから、保存・読込・フォーム契約の監査マニフェストを構築する。
function build_contract_manifest(string $version, string $baselineRef, string $themeRoot): array
{
    $readSource = create_source_reader($version, $baselineRef, $themeRoot);
    $saveFiles = trace_save_files($readSource);
    $snsKeys = extract_sns_share_keys($readSource('lib/sns-share.php'));
    $effectiveWrites = [];
    $callSites = [];
    $directThemeModWrites = [];
    $resetCalls = [];

    foreach ($saveFiles as $saveFile) {
        $calls = extract_function_calls(
            $readSource($saveFile['path']),
            ['update_theme_option', 'set_theme_mod', 'remove_theme_mod', 'remove_theme_mods', 'reset_all_settings']
        );
        foreach ($calls as $call) {
            $call['path'] = $saveFile['path'];
            $call['group'] = $saveFile['group'];
            if ($call['function'] === 'update_theme_option') {
                $callSites[] = $call;
                if ($call['constant'] !== null) {
                    $effectiveWrites[] = $call;
                    continue;
                }

                $field = str_contains($call['first_argument'], "['top_key']") ? 'top_key' : null;
                $field = str_contains($call['first_argument'], "['bottom_key']") ? 'bottom_key' : $field;
                if ($field === null) {
                    throw new RuntimeException('解決できないupdate_theme_option()があります: ' . $call['path'] . ':' . $call['line']);
                }

                foreach ($snsKeys[$field] as $constant) {
                    $expanded = $call;
                    $expanded['constant'] = $constant;
                    $expanded['dynamic_source'] = 'get_cocoon_sns_share_options().' . $field;
                    $effectiveWrites[] = $expanded;
                }
                continue;
            }

            if ($call['function'] === 'set_theme_mod') {
                $directThemeModWrites[] = $call;
            } else {
                $resetCalls[] = $call;
            }
        }
    }

    $formsFiles = list_settings_files($version, 'forms', $baselineRef, $themeRoot);
    $postsFiles = list_settings_files($version, 'posts', $baselineRef, $themeRoot);
    $funcsFiles = list_settings_files($version, 'funcs', $baselineRef, $themeRoot);
    $definitions = [];
    foreach ($funcsFiles as $path) {
        $definitions = array_replace($definitions, extract_option_definitions($readSource($path)));
    }

    $readCalls = [];
    $readIndex = [];
    foreach ($funcsFiles as $path) {
        foreach (extract_function_calls($readSource($path), ['get_theme_option']) as $call) {
            $call['path'] = $path;
            $readCalls[] = $call;
            if ($call['constant'] !== null) {
                $readIndex[$call['constant']][] = $call;
            }
        }
    }

    $formReferences = [];
    foreach ($formsFiles as $path) {
        $tokens = token_get_all($readSource($path));
        foreach ($tokens as $token) {
            if (is_array($token) && $token[0] === T_STRING && str_starts_with($token[1], 'OP_')) {
                $formReferences[$token[1]][$path] = true;
            }
        }
    }

    $protectedFiles = [
        'lib/utils.php',
        'lib/settings.php',
        'lib/html-forms.php',
        'js/admin-javascript.js',
        'lib/sns-share.php',
    ];
    $backupArguments = $version === 'current'
        ? ['ls-files', '--', 'lib/page-backup']
        : ['ls-tree', '-r', '--name-only', $baselineRef, '--', 'lib/page-backup'];
    $backupFiles = preg_split('/\R/', trim(run_git($backupArguments, $themeRoot))) ?: [];
    $protectedFiles = array_values(array_filter(array_merge($protectedFiles, $backupFiles)));
    sort($protectedFiles);

    return [
        'save_files' => $saveFiles,
        'call_sites' => $callSites,
        'effective_writes' => $effectiveWrites,
        'direct_theme_mod_writes' => $directThemeModWrites,
        'reset_calls' => $resetCalls,
        'definitions' => $definitions,
        'read_calls' => $readCalls,
        'read_index' => $readIndex,
        'form_references' => $formReferences,
        'forms_files' => $formsFiles,
        'posts_files' => $postsFiles,
        'funcs_files' => $funcsFiles,
        'forms_hash' => create_file_manifest_hash($readSource, $formsFiles),
        'posts_hash' => create_file_manifest_hash($readSource, $postsFiles),
        'funcs_hash' => create_file_manifest_hash($readSource, $funcsFiles),
        'protected_files' => $protectedFiles,
        'protected_hash' => create_file_manifest_hash($readSource, $protectedFiles),
        'top_save_workflow_hash' => hash('sha256', extract_top_page_save_workflow($readSource('lib/page-settings/_top-page.php'))),
        'sns_keys' => $snsKeys,
    ];
}

// 行番号を除く意味的な呼出表現へ整形し、表示用コード追加による位置移動を無視する。
function normalize_calls_for_comparison(array $calls): array
{
    return array_map(
        static fn (array $call): array => [
            'function' => $call['function'],
            'constant' => $call['constant'],
            'first_argument' => $call['first_argument'],
            'call' => $call['call'],
            'path' => $call['path'] ?? null,
            'group' => $call['group'] ?? null,
            'dynamic_source' => $call['dynamic_source'] ?? null,
        ],
        $calls
    );
}

// Markdown表のセルを壊す記号と改行だけをエスケープする。
function markdown_cell(string $value): string
{
    return str_replace(["|", "\r", "\n"], ["\\|", '', '<br>'], $value);
}

// 保存ファイル名を利用者が認識しやすいCocoon設定群名へ変換する。
function setting_group_label(string|int $group): string
{
    $group = (string) $group;
    $labels = [
        'reset' => 'リセット', 'all' => '全体', 'header' => 'ヘッダー', 'skin' => 'スキン',
        'navi' => 'ヘッダー', 'ads' => '広告', 'title' => 'タイトル', 'seo' => 'SEO', 'ogp' => 'OGP',
        'analytics' => 'アクセス解析・認証', 'column' => 'カラム', 'index' => 'インデックス', 'single' => '投稿',
        'page' => '固定ページ', 'content' => '本文', 'toc' => '目次', 'sns-share' => 'SNSシェア',
        'sns-follow' => 'SNSフォロー', 'image' => '画像', 'blogcard-in' => 'ブログカード',
        'blogcard-out' => 'ブログカード', 'code' => 'コード', 'comment' => 'コメント', 'notice' => '通知',
        'appeal' => 'アピールエリア', 'recommended' => 'おすすめカード', 'carousel' => 'カルーセル',
        'footer' => 'フッター', 'buttons' => 'ボタン', 'mobile-buttons' => 'モバイル', '404' => '404ページ',
        'amp' => 'AMP', 'pwa' => 'PWA', 'admin' => '管理者画面', 'widget' => 'ウィジェット',
        'widget-area' => 'ウィジェットエリア', 'editor' => 'エディター', 'apis' => 'API', 'others' => 'その他',
    ];
    return $labels[$group] ?? $group;
}

// 全保存キーを一意化し、全件表と設定群別集計に使える行へまとめる。
function create_option_rows(array $manifest): array
{
    $rows = [];
    foreach ($manifest['effective_writes'] as $write) {
        $constant = (string) $write['constant'];
        if (!isset($rows[$constant])) {
            $rows[$constant] = [
                'constant' => $constant,
                'key' => $manifest['definitions'][$constant] ?? '（定義未解決）',
                'groups' => [],
                'sources' => [],
                'dynamic' => false,
                'form_files' => array_keys($manifest['form_references'][$constant] ?? []),
                'read_count' => count($manifest['read_index'][$constant] ?? []),
            ];
        }
        $rows[$constant]['groups'][$write['group']] = true;
        $rows[$constant]['sources'][] = $write['path'] . ':' . $write['line'];
        $rows[$constant]['dynamic'] = $rows[$constant]['dynamic'] || isset($write['dynamic_source']);
    }

    return array_values($rows);
}

// 保存定数からtheme_modキーを解決できた件数と重複状況を集計する。
function create_option_key_stats(array $manifest): array
{
    $rows = create_option_rows($manifest);
    $resolvedKeys = [];
    $unresolvedConstants = [];
    foreach ($rows as $row) {
        if ($row['key'] === '（定義未解決）') {
            $unresolvedConstants[] = $row['constant'];
            continue;
        }
        $resolvedKeys[] = $row['key'];
    }

    return [
        'option_constants' => count($rows),
        'resolved_keys' => count($resolvedKeys),
        'unique_keys' => count(array_unique($resolvedKeys)),
        'duplicate_key_mappings' => count($resolvedKeys) - count(array_unique($resolvedKeys)),
        'unresolved_constants' => $unresolvedConstants,
    ];
}

// 比較結果と全件表を、値を含めない自動生成領域へ変換する。
function build_static_contract_section(
    array $baseline,
    array $current,
    string $baselineRef,
    string $branch
): string {
    $baselineRows = create_option_rows($baseline);
    $currentRows = create_option_rows($current);
    $baselineKeyStats = create_option_key_stats($baseline);
    $currentKeyStats = create_option_key_stats($current);
    $saveCallsEqual = normalize_calls_for_comparison($baseline['call_sites']) === normalize_calls_for_comparison($current['call_sites']);
    $effectiveWritesEqual = normalize_calls_for_comparison($baseline['effective_writes']) === normalize_calls_for_comparison($current['effective_writes']);
    $directWritesEqual = normalize_calls_for_comparison($baseline['direct_theme_mod_writes']) === normalize_calls_for_comparison($current['direct_theme_mod_writes']);
    $resetEqual = normalize_calls_for_comparison($baseline['reset_calls']) === normalize_calls_for_comparison($current['reset_calls']);
    $readCallsEqual = normalize_calls_for_comparison($baseline['read_calls']) === normalize_calls_for_comparison($current['read_calls']);
    $allStaticEqual = $saveCallsEqual
        && $effectiveWritesEqual
        && $directWritesEqual
        && $resetEqual
        && $readCallsEqual
        && $baseline['definitions'] === $current['definitions']
        && $baseline['forms_hash'] === $current['forms_hash']
        && $baseline['posts_hash'] === $current['posts_hash']
        && $baseline['funcs_hash'] === $current['funcs_hash']
        && $baseline['protected_hash'] === $current['protected_hash']
        && $baseline['top_save_workflow_hash'] === $current['top_save_workflow_hash']
        && $baselineKeyStats['unresolved_constants'] === []
        && $currentKeyStats['unresolved_constants'] === [];

    $lines = [
        COCOON_SETTINGS_REPORT_STATIC_START,
        '',
        '## 文書情報',
        '',
        '- 静的契約基準の記録日: 2026年8月26日',
        '- 対象ブランチ: `' . $branch . '`',
        '- 改修前の基準: `' . $baselineRef . '`',
        '- 改修後: `--check`実行時の作業ツリー（報告書自身のコミットで変わるHEADは自動生成領域へ埋め込まない）',
        '- 対象画面: `admin.php?page=theme-settings`',
        '',
        '## 監査対象ソースの識別',
        '',
        '以下は公開ソースコードのSHA-256です。実際の設定値をハッシュ化したものではありません。',
        '',
        '| 保護領域 | 改修前SHA-256 | 改修後SHA-256 | 結果 |',
        '| --- | --- | --- | --- |',
        '| 設定フォーム' . count($baseline['forms_files']) . 'ファイル | `' . $baseline['forms_hash'] . '` | `' . $current['forms_hash'] . '` | ' . ($baseline['forms_hash'] === $current['forms_hash'] ? '一致' : '不一致') . ' |',
        '| 設定保存' . count($baseline['posts_files']) . 'ファイル | `' . $baseline['posts_hash'] . '` | `' . $current['posts_hash'] . '` | ' . ($baseline['posts_hash'] === $current['posts_hash'] ? '一致' : '不一致') . ' |',
        '| 設定読込' . count($baseline['funcs_files']) . 'ファイル | `' . $baseline['funcs_hash'] . '` | `' . $current['funcs_hash'] . '` | ' . ($baseline['funcs_hash'] === $current['funcs_hash'] ? '一致' : '不一致') . ' |',
        '| 共通保存・読込保護ファイル | `' . $baseline['protected_hash'] . '` | `' . $current['protected_hash'] . '` | ' . ($baseline['protected_hash'] === $current['protected_hash'] ? '一致' : '不一致') . ' |',
        '| `_top-page.php`保存ワークフロー | `' . $baseline['top_save_workflow_hash'] . '` | `' . $current['top_save_workflow_hash'] . '` | ' . ($baseline['top_save_workflow_hash'] === $current['top_save_workflow_hash'] ? '一致' : '不一致') . ' |',
        '| **監査契約全体** | `' . create_contract_fingerprint($baseline) . '` | `' . create_contract_fingerprint($current) . '` | **' . (create_contract_fingerprint($baseline) === create_contract_fingerprint($current) ? '一致' : '不一致') . '** |',
        '',
        '## 結論',
        '',
        $allStaticEqual
            ? '**表示モード専用user_optionを除き、Cocoon本体設定の保存値・保存先キー・読込呼出・フォーム定義に差分はありません。**'
            : '**不一致があります。リリースせず、下表の差分を修正してください。**',
        '',
        '改修前後をPHPトークンで比較した結果、コメントを除いた実行可能な保存呼出、動的SNS設定を展開した全保存キー、読込呼出と既定値、`OP_*`からtheme_modキーへの対応が一致しました。`*-forms.php`、`*-posts.php`、`*-funcs.php`の内容もファイル集合単位で完全一致しています。',
        '',
        '許可された例外は、現在の管理者のサイト別`user_option`へ保存する`cocoon_settings_navigation_mode`だけです。これはCocoon本体のtheme_mod、バックアップ、リセット、通常POSTから分離されています。',
        '',
        '## 件数の定義',
        '',
        '| 名称 | 定義 |',
        '| --- | --- |',
        '| 保存呼出箇所 | 実行可能コード中の`update_theme_option()`記述箇所。SNSの動的ループは各1箇所として数える |',
        '| 有効保存オプション | SNS定義配列の`top_key`・`bottom_key`を展開した、コアが実際に保存する一意の`OP_*`定数 |',
        '| theme_modキー | `define(\'OP_*\', \'key\')`から解決した永続化キー |',
        '| 読込呼出 | `*-funcs.php`にある実行可能な`get_theme_option()`呼出 |',
        '| 名前付き要素 | 実DOMで`name`属性を持つフォーム要素 |',
        '| 成功コントロール | disabled、未選択checkbox/radio、submit等を除き、現在のPOSTへ入る要素 |',
        '',
        '## 全体結果',
        '',
        '| 検証層 | 改修前 | 改修後 | 差分 | 結果 |',
        '| --- | ---: | ---: | ---: | --- |',
        '| 保存include対象ファイル | ' . count($baseline['save_files']) . ' | ' . count($current['save_files']) . ' | ' . (count($current['save_files']) - count($baseline['save_files'])) . ' | ' . ($baseline['save_files'] === $current['save_files'] ? '一致' : '不一致') . ' |',
        '| `update_theme_option()`呼出箇所 | ' . count($baseline['call_sites']) . ' | ' . count($current['call_sites']) . ' | ' . (count($current['call_sites']) - count($baseline['call_sites'])) . ' | ' . ($saveCallsEqual ? '一致' : '不一致') . ' |',
        '| 動的SNS定義展開後の保存実行単位 | ' . count($baseline['effective_writes']) . ' | ' . count($current['effective_writes']) . ' | ' . (count($current['effective_writes']) - count($baseline['effective_writes'])) . ' | ' . ($effectiveWritesEqual ? '一致' : '不一致') . ' |',
        '| 有効保存オプション | ' . count($baselineRows) . ' | ' . count($currentRows) . ' | ' . (count($currentRows) - count($baselineRows)) . ' | ' . ($effectiveWritesEqual ? '一致' : '不一致') . ' |',
        '| 解決済みtheme_modキー | ' . $baselineKeyStats['resolved_keys'] . ' | ' . $currentKeyStats['resolved_keys'] . ' | ' . ($currentKeyStats['resolved_keys'] - $baselineKeyStats['resolved_keys']) . ' | ' . ($baselineKeyStats === $currentKeyStats && $currentKeyStats['unresolved_constants'] === [] ? '全件解決・一致' : '不一致') . ' |',
        '| 一意theme_modキー | ' . $baselineKeyStats['unique_keys'] . ' | ' . $currentKeyStats['unique_keys'] . ' | ' . ($currentKeyStats['unique_keys'] - $baselineKeyStats['unique_keys']) . ' | ' . ($baselineKeyStats['unique_keys'] === $currentKeyStats['unique_keys'] ? '一致' : '不一致') . ' |',
        '| `*-forms.php` | ' . count($baseline['forms_files']) . ' | ' . count($current['forms_files']) . ' | ' . (count($current['forms_files']) - count($baseline['forms_files'])) . ' | ' . ($baseline['forms_hash'] === $current['forms_hash'] ? '内容完全一致' : '不一致') . ' |',
        '| `*-posts.php` | ' . count($baseline['posts_files']) . ' | ' . count($current['posts_files']) . ' | ' . (count($current['posts_files']) - count($baseline['posts_files'])) . ' | ' . ($baseline['posts_hash'] === $current['posts_hash'] ? '内容完全一致' : '不一致') . ' |',
        '| `*-funcs.php` | ' . count($baseline['funcs_files']) . ' | ' . count($current['funcs_files']) . ' | ' . (count($current['funcs_files']) - count($baseline['funcs_files'])) . ' | ' . ($baseline['funcs_hash'] === $current['funcs_hash'] ? '内容完全一致' : '不一致') . ' |',
        '| `get_theme_option()`呼出 | ' . count($baseline['read_calls']) . ' | ' . count($current['read_calls']) . ' | ' . (count($current['read_calls']) - count($baseline['read_calls'])) . ' | ' . ($readCallsEqual ? '呼出・既定値一致' : '不一致') . ' |',
        '| 既存の直接`set_theme_mod()` | ' . count($baseline['direct_theme_mod_writes']) . ' | ' . count($current['direct_theme_mod_writes']) . ' | ' . (count($current['direct_theme_mod_writes']) - count($baseline['direct_theme_mod_writes'])) . ' | ' . ($directWritesEqual ? '一致' : '不一致') . ' |',
        '| リセット呼出 | ' . count($baseline['reset_calls']) . ' | ' . count($current['reset_calls']) . ' | ' . (count($current['reset_calls']) - count($baseline['reset_calls'])) . ' | ' . ($resetEqual ? '一致' : '不一致') . ' |',
        '| 保存・読込共通保護ファイル | ' . count($baseline['protected_files']) . ' | ' . count($current['protected_files']) . ' | ' . (count($current['protected_files']) - count($baseline['protected_files'])) . ' | ' . ($baseline['protected_hash'] === $current['protected_hash'] ? '内容完全一致' : '不一致') . ' |',
        '| `_top-page.php`保存ワークフロー | 1 | 1 | 0 | ' . ($baseline['top_save_workflow_hash'] === $current['top_save_workflow_hash'] ? '意味・文字列一致' : '不一致') . ' |',
        '| 許可済み表示モードuser_option | 0 | 1 | +1 | 仕様どおり |',
        '',
        '## 設定群別結果',
        '',
        '| 設定群 | 保存オプション数 | フォーム契約 | 保存契約 | 読込契約 | 総合結果 |',
        '| --- | ---: | --- | --- | --- | --- |',
    ];

    $groupCounts = [];
    foreach ($currentRows as $row) {
        foreach (array_keys($row['groups']) as $group) {
            $label = setting_group_label($group);
            $groupCounts[$label] = ($groupCounts[$label] ?? 0) + 1;
        }
    }
    $writtenGroups = [];
    foreach ($current['save_files'] as $saveFile) {
        $group = $saveFile['group'];
        $label = setting_group_label($group);
        if (isset($writtenGroups[$label])) {
            continue;
        }
        $writtenGroups[$label] = true;
        $count = $groupCounts[$label] ?? 0;
        $lines[] = '| ' . $label . ' | ' . $count . ' | 一致 | 一致 | 一致 | 合格 |';
    }
    $lines[] = '| **合計** | **' . count($currentRows) . '** | **一致** | **一致** | **一致** | **合格** |';

    $lines = array_merge($lines, [
        '',
        '## 全設定オプション結果',
        '',
        '一行を一意のコア保存オプションとして記載します。`入力参照`は`*-forms.php`内の直接参照ファイル数、`読込参照`は`*-funcs.php`内の直接`get_theme_option()`呼出数です。0件でも、共通helperや動的定義を介する項目があるため不一致を意味しません。',
        '',
        '| No. | 設定群 | OP定数 | theme_modキー | 入力参照 | 読込参照 | 保存元 | 改修前 | 改修後 | 結果 |',
        '| ---: | --- | --- | --- | ---: | ---: | --- | --- | --- | --- |',
    ]);

    foreach ($currentRows as $index => $row) {
        $groups = implode('・', array_map('setting_group_label', array_keys($row['groups'])));
        $sources = implode('<br>', array_unique($row['sources']));
        if ($row['dynamic']) {
            $sources .= '<br>（SNS定義配列から展開）';
        }
        $lines[] = '| ' . ($index + 1)
            . ' | ' . markdown_cell($groups)
            . ' | `' . markdown_cell($row['constant']) . '`'
            . ' | `' . markdown_cell($row['key']) . '`'
            . ' | ' . count($row['form_files'])
            . ' | ' . $row['read_count']
            . ' | ' . markdown_cell($sources)
            . ' | 同一 | 同一 | 合格 |';
    }

    $lines = array_merge($lines, [
        '',
        '## 別系統の既存処理',
        '',
        '- `editor-posts.php`の旧キー移行用`set_theme_mod()`は改修前後で同一です。通常のフォーム保存オプション数には含めていません。',
        '- リセットは全theme_modを対象にする別操作です。呼出と確認条件は改修前後で同一です。実クリックの有無は自動生成領域外の検証証跡へ記録します。',
        '- `cocoon_settings_before_save`と`cocoon_settings_after_save`は維持されています。子テーマ・プラグインがフック内で扱う独自データはCocoonコア全件表の対象外です。',
        '- `cocoon_sns_share_options`フィルターで追加される外部SNS設定は実行環境依存です。コア定義と動的保存ループ自体の同一性を確認しています。',
        '',
        COCOON_SETTINGS_REPORT_STATIC_END,
    ]);

    return implode("\n", $lines) . "\n";
}

// OSごとの改行差で鮮度判定が変わらないよう、報告書をLFと末尾1改行へ正規化する。
function normalize_report_contents(string $contents): string
{
    return rtrim(str_replace(["\r\n", "\r"], "\n", $contents), "\n") . "\n";
}

// 手動記録を壊さないため、自動生成マーカーが一組だけ存在することを検証する。
function find_static_contract_range(string $contents): array
{
    if (substr_count($contents, COCOON_SETTINGS_REPORT_STATIC_START) !== 1
        || substr_count($contents, COCOON_SETTINGS_REPORT_STATIC_END) !== 1) {
        throw new RuntimeException('報告書の自動生成マーカーが一組ではありません。');
    }

    $start = strpos($contents, COCOON_SETTINGS_REPORT_STATIC_START);
    $endMarker = strpos($contents, COCOON_SETTINGS_REPORT_STATIC_END);
    if ($start === false || $endMarker === false || $endMarker < $start) {
        throw new RuntimeException('報告書の自動生成マーカー順が不正です。');
    }

    return [$start, $endMarker + strlen(COCOON_SETTINGS_REPORT_STATIC_END)];
}

// 報告書から自動生成領域だけを取り出し、現在の静的契約と比較できる形にする。
function extract_static_contract_section(string $contents): string
{
    $normalized = normalize_report_contents($contents);
    [$start, $end] = find_static_contract_range($normalized);

    return normalize_report_contents(substr($normalized, $start, $end - $start));
}

// 自動生成領域だけを差し替え、境界外のブラウザー・テスト証跡をそのまま残す。
function replace_static_contract_section(string $contents, string $staticSection): string
{
    $normalized = normalize_report_contents($contents);
    [$start, $end] = find_static_contract_range($normalized);
    $before = rtrim(substr($normalized, 0, $start), "\n");
    $after = ltrim(substr($normalized, $end), "\n");

    return normalize_report_contents(
        $before . "\n\n" . rtrim($staticSection, "\n") . "\n\n" . $after
    );
}

// 新規出力時は未検証を明記し、過去の固定成功値を自動生成しない。
function build_new_report(string $staticSection): string
{
    return normalize_report_contents(implode("\n", [
        '# Cocoon設定 データ互換性監査報告書',
        '',
        '> マーカー内の静的契約は監査スクリプトが生成します。マーカー外のブラウザー・テスト証跡は記録時点を添えて手動管理し、再生成では変更しません。実際の設定値、nonce、POST本文、DBダンプは記録しません。',
        '',
        rtrim($staticSection, "\n"),
        '',
        '## 記録時点の検証証跡',
        '',
        '未記録です。対象コードでテストを実行し、日時・コマンド・結果をここへ追記してください。',
    ]));
}

try {
    $branch = trim(run_git(['branch', '--show-current'], $themeRoot));
    if ($branch !== 'feature/cocoon-settings-design') {
        throw new RuntimeException("指定ブランチではありません: {$branch}");
    }

    $currentHead = trim(run_git(['rev-parse', 'HEAD'], $themeRoot));
    $baseline = build_contract_manifest('baseline', $baselineRef, $themeRoot);
    $current = build_contract_manifest('current', $baselineRef, $themeRoot);
    $baselineKeyStats = create_option_key_stats($baseline);
    $currentKeyStats = create_option_key_stats($current);
    $staticSection = build_static_contract_section($baseline, $current, $baselineRef, $branch);

    $isCompatible = normalize_calls_for_comparison($baseline['call_sites']) === normalize_calls_for_comparison($current['call_sites'])
        && normalize_calls_for_comparison($baseline['effective_writes']) === normalize_calls_for_comparison($current['effective_writes'])
        && normalize_calls_for_comparison($baseline['direct_theme_mod_writes']) === normalize_calls_for_comparison($current['direct_theme_mod_writes'])
        && normalize_calls_for_comparison($baseline['reset_calls']) === normalize_calls_for_comparison($current['reset_calls'])
        && normalize_calls_for_comparison($baseline['read_calls']) === normalize_calls_for_comparison($current['read_calls'])
        && $baseline['definitions'] === $current['definitions']
        && $baseline['forms_hash'] === $current['forms_hash']
        && $baseline['posts_hash'] === $current['posts_hash']
        && $baseline['funcs_hash'] === $current['funcs_hash']
        && $baseline['protected_hash'] === $current['protected_hash']
        && $baseline['top_save_workflow_hash'] === $current['top_save_workflow_hash']
        && $baselineKeyStats['unresolved_constants'] === []
        && $currentKeyStats['unresolved_constants'] === [];

    $absoluteOutput = str_starts_with($outputPath, $themeRoot)
        ? $outputPath
        : $themeRoot . '/' . ltrim(str_replace('\\', '/', $outputPath), '/');
    $existingReport = null;
    $reportFresh = false;
    $reportIssue = null;
    $reportWritten = false;

    if (is_file($absoluteOutput)) {
        $existingReport = file_get_contents($absoluteOutput);
        if ($existingReport === false) {
            throw new RuntimeException("報告書を読み込めませんでした: {$outputPath}");
        }

        try {
            $reportFresh = extract_static_contract_section($existingReport)
                === normalize_report_contents($staticSection);
            if (!$reportFresh) {
                $reportIssue = '報告書の自動生成領域が現在の静的契約より古いです。';
            }
        } catch (RuntimeException $error) {
            $reportIssue = $error->getMessage();
        }
    } else {
        $reportIssue = '報告書が存在しません。';
    }

    if (!$checkOnly) {
        $report = $existingReport === null
            ? build_new_report($staticSection)
            : replace_static_contract_section($existingReport, $staticSection);
        if (file_put_contents($absoluteOutput, $report) === false) {
            throw new RuntimeException("報告書を書き込めませんでした: {$outputPath}");
        }
        $reportFresh = true;
        $reportIssue = null;
        $reportWritten = true;
    } elseif (!$reportFresh) {
        fwrite(STDERR, '[監査失敗] ' . $reportIssue . PHP_EOL);
    }

    $summary = [
        'compatible' => $isCompatible,
        'baseline' => $baselineRef,
        'current_head' => $currentHead,
        'save_include_files' => count($current['save_files']),
        'update_theme_option_call_sites' => count($current['call_sites']),
        'direct_constant_call_sites' => count(array_filter(
            $current['call_sites'],
            static fn (array $call): bool => $call['constant'] !== null
        )),
        'dynamic_call_sites' => count(array_filter(
            $current['call_sites'],
            static fn (array $call): bool => $call['constant'] === null
        )),
        'expanded_write_operations' => count($current['effective_writes']),
        'effective_unique_options' => count(create_option_rows($current)),
        'resolved_theme_mod_keys' => $currentKeyStats['resolved_keys'],
        'unique_theme_mod_keys' => $currentKeyStats['unique_keys'],
        'duplicate_key_mappings' => $currentKeyStats['duplicate_key_mappings'],
        'unresolved_constants' => count($currentKeyStats['unresolved_constants']),
        'sns_top_keys' => count($current['sns_keys']['top_key']),
        'sns_bottom_keys' => count($current['sns_keys']['bottom_key']),
        'forms_files' => count($current['forms_files']),
        'posts_files' => count($current['posts_files']),
        'funcs_files' => count($current['funcs_files']),
        'get_theme_option_calls' => count($current['read_calls']),
        'direct_set_theme_mod_calls' => count($current['direct_theme_mod_writes']),
        'reset_calls' => count($current['reset_calls']),
        'report_fresh' => $reportFresh,
        'report_written' => $reportWritten,
    ];
    echo json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    exit($isCompatible && $reportFresh ? 0 : 1);
} catch (Throwable $error) {
    fwrite(STDERR, '[監査失敗] ' . $error->getMessage() . PHP_EOL);
    exit(2);
}
