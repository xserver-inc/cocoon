#Requires -Version 7.0
param(
    [string]$SourceDirectory,
    [string]$ArchiveDirectory
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest
$projectRoot = [IO.Path]::GetFullPath((Split-Path $PSScriptRoot -Parent))
if (-not $SourceDirectory) { $SourceDirectory = $projectRoot }
if (-not $ArchiveDirectory) { $ArchiveDirectory = Join-Path $projectRoot 'scratch/docker-integration' }
$sourcePath = [IO.Path]::GetFullPath($SourceDirectory)
$archiveRoot = [IO.Path]::GetFullPath($ArchiveDirectory)

# プロジェクトの外への読み書きを避けるための、入力元と保存先の確認
foreach ($candidate in @($sourcePath, $archiveRoot)) {
    if ($candidate -ne $projectRoot -and -not $candidate.StartsWith($projectRoot + [IO.Path]::DirectorySeparatorChar, [StringComparison]::OrdinalIgnoreCase)) {
        throw '入力元と保存先はプロジェクト内に指定してください。'
    }
}
if (-not (Test-Path -LiteralPath $sourcePath -PathType Container)) { throw '入力元のディレクトリがありません。' }
$tarExecutable = (Get-Command tar -CommandType Application -ErrorAction Stop | Select-Object -First 1).Source
$tarVersion = (& $tarExecutable --version | Select-Object -First 1)
New-Item -ItemType Directory -Path $archiveRoot -Force | Out-Null
$archivePath = Join-Path $archiveRoot ('source-' + [guid]::NewGuid().ToString('N') + '.tar')

# 同時実行でも混ざらない一意な保存先と、開発用データ・依存関係の除外
$arguments = @('--create', '--file', $archivePath, '--directory', $sourcePath)
# 組み込みライブラリを除外しないための、tarの実装に応じたルート直下の指定
$excludePrefix = if ($tarVersion -match 'bsdtar') { '^./' } else { './' }
if ($tarVersion -notmatch 'bsdtar') { $arguments += '--anchored' }
foreach ($excluded in @('.git', '.agents', '.agent', '.codex', '.aws', '.cursor', '.vscode', '.github/docs', '.phpunit.cache', '.phpunit.result.cache', 'vendor', 'scratch', 'docker', 'test-results')) {
    $arguments += '--exclude=' + $excludePrefix + $excluded
}
if ($tarVersion -notmatch 'bsdtar') { $arguments += '--no-anchored' }
$arguments += @('--exclude=node_modules', '.')
Write-Host '統合テスト用ソースのアーカイブを作成します。'
try {
    & $tarExecutable @arguments
    if ($LASTEXITCODE -ne 0) { throw "ソースのアーカイブ作成が終了コード $LASTEXITCODE で失敗しました。" }
} catch {
    if (Test-Path -LiteralPath $archivePath) { Remove-Item -LiteralPath $archivePath -Force }
    throw
}
Write-Output $archivePath
