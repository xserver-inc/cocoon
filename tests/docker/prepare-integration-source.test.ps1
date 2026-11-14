#Requires -Version 7.0
$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest
$projectRoot = Split-Path (Split-Path $PSScriptRoot -Parent) -Parent
$testRoot = Join-Path $projectRoot ('scratch/integration-archive-test-' + [guid]::NewGuid().ToString('N'))
$sourceRoot = Join-Path $testRoot 'source'
$archiveRoot = Join-Path $testRoot 'archives'
$preparer = Join-Path $projectRoot 'scripts/prepare-integration-source.ps1'
$tarExecutable = (Get-Command tar -CommandType Application | Select-Object -First 1).Source

try {
    # ルートの依存関係と組み込みライブラリを区別した、実ファイルによる検証用データの準備
    $fixtures = @{
        'functions.php' = 'first'
        '.htaccess' = 'hidden'
        'lib/vendor/library.php' = 'included'
        '.github/workflows/check.yml' = 'included'
        'vendor/dependency.php' = 'excluded'
        'blocks/node_modules/dependency.js' = 'excluded'
        'node_modules/dependency.js' = 'excluded'
        'scratch/private.txt' = 'excluded'
        '.git/config' = 'excluded'
        '.github/docs/report.md' = 'excluded'
    }
    foreach ($entry in $fixtures.GetEnumerator()) {
        $filePath = Join-Path $sourceRoot $entry.Key
        New-Item -ItemType Directory -Path (Split-Path $filePath -Parent) -Force | Out-Null
        Set-Content -LiteralPath $filePath -Value $entry.Value -Encoding utf8NoBOM
    }
    $firstArchive = & $preparer -SourceDirectory $sourceRoot -ArchiveDirectory $archiveRoot
    $files = @(& $tarExecutable --list --file $firstArchive)
    if ($LASTEXITCODE -ne 0) { throw 'アーカイブを読み込めませんでした。' }
    foreach ($expected in @('./functions.php', './.htaccess', './lib/vendor/library.php', './.github/workflows/check.yml')) {
        if ($files -notcontains $expected) { throw "必要なファイルがありません: $expected" }
    }
    if ($files -match '(^|/)node_modules(/|$)|^\./vendor/|^\./scratch/|^\./\.git/|^\./\.github/docs/') {
        throw '除外対象がアーカイブへ含まれています。'
    }
    Write-Host '成功: 必要なファイルの収録と開発用データ・依存関係の除外'

    # 更新後の内容と、同時実行でも重複しない保存先の検証
    Set-Content -LiteralPath (Join-Path $sourceRoot 'functions.php') -Value 'second' -Encoding utf8NoBOM
    $secondArchive = & $preparer -SourceDirectory $sourceRoot -ArchiveDirectory $archiveRoot
    if ($firstArchive -eq $secondArchive) { throw 'アーカイブの保存先が重複しています。' }
    $content = & $tarExecutable --extract --to-stdout --file $secondArchive ./functions.php
    if ($LASTEXITCODE -ne 0 -or $content -ne 'second') { throw '最新のソースが収録されていません。' }
    Write-Host '成功: 最新の内容の収録と一意な保存先'

    # プロジェクト外への出力と、未存在の同期元の拒否の検証
    foreach ($parameters in @(
        @{SourceDirectory = (Join-Path $testRoot 'missing'); ArchiveDirectory = $archiveRoot},
        @{SourceDirectory = $sourceRoot; ArchiveDirectory = [IO.Path]::GetPathRoot($projectRoot)}
    )) {
        $rejected = $false
        try { & $preparer @parameters | Out-Null } catch { $rejected = $true }
        if (-not $rejected) { throw '不正な入力元または保存先を拒否できませんでした。' }
    }
    Write-Host '成功: 未存在の入力元とプロジェクト外の保存先の拒否'
} finally {
    # プロジェクト内の今回の一時ディレクトリに限定した後片付け
    if (Test-Path -LiteralPath $testRoot) {
        $resolvedRoot = (Resolve-Path -LiteralPath $testRoot).Path
        $allowedPrefix = (Join-Path $projectRoot 'scratch/integration-archive-test-')
        if (-not $resolvedRoot.StartsWith($allowedPrefix, [StringComparison]::OrdinalIgnoreCase)) { throw '一時ディレクトリの保存先が一致しません。' }
        Remove-Item -LiteralPath $resolvedRoot -Recurse -Force
    }
}
