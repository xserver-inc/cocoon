[CmdletBinding()]
param(
    [Parameter(Position = 0)]
    [ValidateSet('up', 'down', 'status', 'logs', 'pma', 'test', 'check', 'config', 'loop', 'help')]
    [string]$Action = 'status',

    [Parameter(Position = 1)]
    [string]$EnvFile = ''
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

# Docker Composeの同時イメージ処理を1件に制限
if ([string]::IsNullOrWhiteSpace($env:COMPOSE_PARALLEL_LIMIT)) {
    $env:COMPOSE_PARALLEL_LIMIT = '1'
}

$script:ProjectRoot = Split-Path -Parent $PSScriptRoot
$script:ComposeFile = Join-Path $PSScriptRoot 'docker-compose.yml'

function Find-DockerExecutable {
    $dockerCommand = Get-Command docker -CommandType Application -ErrorAction SilentlyContinue |
        Select-Object -First 1
    if ($null -ne $dockerCommand) {
        return $dockerCommand.Source
    }

    # 標準インストール先とユーザー単位インストール先の候補
    $candidates = @(
        (Join-Path $env:LOCALAPPDATA 'Programs\DockerDesktop\resources\bin\docker.exe'),
        (Join-Path $env:LOCALAPPDATA 'Docker\resources\bin\docker.exe'),
        'C:\Program Files\Docker\Docker\resources\bin\docker.exe'
    )

    foreach ($candidate in $candidates) {
        if (Test-Path -LiteralPath $candidate) {
            return $candidate
        }
    }

    throw 'docker.exeが見つかりません。Docker Desktopをインストールしてから再実行してください。'
}

function Resolve-DevelopmentEnvFile {
    if ([string]::IsNullOrWhiteSpace($EnvFile)) {
        return $null
    }

    # dockerディレクトリからの相対パス解決
    $candidate = if ([System.IO.Path]::IsPathRooted($EnvFile)) {
        $EnvFile
    } else {
        Join-Path $PSScriptRoot $EnvFile
    }

    if (-not (Test-Path -LiteralPath $candidate)) {
        throw "envファイルが見つかりません: $candidate"
    }

    return (Resolve-Path -LiteralPath $candidate).Path
}

function Invoke-Docker {
    param(
        [Parameter(Mandatory = $true)]
        [string[]]$Arguments
    )

    & $script:DockerExecutable @Arguments
    if ($LASTEXITCODE -ne 0) {
        throw "Dockerコマンドが終了コード $LASTEXITCODE で失敗しました。"
    }
}

function Invoke-Compose {
    param(
        [Parameter(Mandatory = $true)]
        [string[]]$Arguments
    )

    Invoke-Docker -Arguments ($script:ComposeArguments + $Arguments)
}

function Assert-DockerEngine {
    # 高負荷直後の一時的な遅延を考慮したDockerエンジン確認
    $maximumAttempts = 3
    $lastErrorOutput = ''

    for ($attempt = 1; $attempt -le $maximumAttempts; $attempt++) {
        $startInfo = [System.Diagnostics.ProcessStartInfo]::new()
        $startInfo.FileName = $script:DockerExecutable
        $startInfo.UseShellExecute = $false
        $startInfo.CreateNoWindow = $true
        $startInfo.RedirectStandardOutput = $true
        $startInfo.RedirectStandardError = $true
        $startInfo.ArgumentList.Add('info')
        $startInfo.ArgumentList.Add('--format')
        $startInfo.ArgumentList.Add('{{.ServerVersion}}')

        $process = [System.Diagnostics.Process]::Start($startInfo)
        if ($null -eq $process) {
            throw 'Dockerエンジンの確認プロセスを開始できませんでした。'
        }

        if ($process.WaitForExit(15000)) {
            $serverVersion = $process.StandardOutput.ReadToEnd().Trim()
            $lastErrorOutput = $process.StandardError.ReadToEnd().Trim()
            if ($process.ExitCode -eq 0 -and -not [string]::IsNullOrWhiteSpace($serverVersion)) {
                Write-Host "Docker Engine $serverVersion"
                return
            }
        } else {
            # タイムアウトしたDocker CLI子プロセスの終了
            $process.Kill($true)
            $process.WaitForExit()
            $lastErrorOutput = '15秒以内に応答しませんでした。'
        }

        if ($attempt -lt $maximumAttempts) {
            Write-Warning "Dockerエンジンの応答待ちです（$attempt/$maximumAttempts）。5秒後に再試行します。"
            Start-Sleep -Seconds 5
        }
    }

    throw "Dockerエンジンを3回確認しましたが応答しませんでした。Docker Desktopの画面でエンジンの状態を確認してください。`n$lastErrorOutput"
}

function Invoke-UnitTests {
    Write-Host 'PHPUnitユニットテストを実行します。'
    Invoke-Compose -Arguments @('run', '--build', '--rm', 'phpunit')
}

function Invoke-WordPressSmokeTest {
    # 起動済みサービスに限定したWordPress確認
    $runningServices = @(& $script:DockerExecutable @script:ComposeArguments 'ps' '--status' 'running' '--services')
    if ($LASTEXITCODE -ne 0) {
        throw '起動済みサービスの確認に失敗しました。'
    }

    if ($runningServices -contains 'wordpress') {
        Write-Host 'WordPress内のテーマ構文を確認します。'
        Invoke-Compose -Arguments @(
            'exec', '-T', 'wordpress', 'php', '-l',
            '/var/www/html/wp-content/themes/cocoon/functions.php'
        )
    } else {
        Write-Host 'WordPressが未起動のため、テーマのスモークテストを省略します。'
    }
}

function Show-DevelopmentHelp {
    @'
使い方:
  pwsh -NoProfile -File docker/dev.ps1 <操作> [envファイル]

操作:
  up      WordPress環境の起動と初期セットアップ
  down    WordPress環境の停止（データ保持）
  status  コンテナ状態の表示
  logs    直近100行のログ表示
    pma     phpMyAdminを必要なときだけ起動
  test    Docker内でPHPUnitユニットテストを実行
  check   Compose構文、PHPUnit、起動済みWordPressの一括確認
  config  Compose設定の構文確認
  loop    ファイル保存を監視してPHPUnitを反復実行
  help    このヘルプの表示

例:
  pwsh -NoProfile -File docker/dev.ps1 up
    pwsh -NoProfile -File docker/dev.ps1 pma
  pwsh -NoProfile -File docker/dev.ps1 test
  pwsh -NoProfile -File docker/dev.ps1 up env/wp7.0-php8.4.env
  pwsh -NoProfile -File docker/dev.ps1 loop
'@ | Write-Host
}

$script:DockerExecutable = Find-DockerExecutable
$resolvedEnvFile = Resolve-DevelopmentEnvFile
$script:ComposeArguments = @('compose', '--file', $script:ComposeFile)
if ($null -ne $resolvedEnvFile) {
    $script:ComposeArguments += @('--env-file', $resolvedEnvFile)
}

switch ($Action) {
    'up' {
        Invoke-Compose -Arguments @('config', '--quiet')
        Assert-DockerEngine
        Invoke-Compose -Arguments @('up', '--detach')
        Invoke-Compose -Arguments @('wait', 'wp-cli')
        Invoke-Compose -Arguments @('ps')
    }
    'down' {
        Assert-DockerEngine
        Invoke-Compose -Arguments @('--profile', 'admin', 'down')
    }
    'status' {
        Assert-DockerEngine
        Invoke-Compose -Arguments @('ps')
    }
    'logs' {
        Assert-DockerEngine
        Invoke-Compose -Arguments @('logs', '--tail', '100')
    }
    'pma' {
        Assert-DockerEngine
        Invoke-Compose -Arguments @('--profile', 'admin', 'up', '--detach', 'phpmyadmin')
    }
    'test' {
        Assert-DockerEngine
        Invoke-UnitTests
    }
    'check' {
        Invoke-Compose -Arguments @('config', '--quiet')
        Assert-DockerEngine
        Invoke-UnitTests
        Invoke-WordPressSmokeTest
    }
    'config' {
        Invoke-Compose -Arguments @('config', '--quiet')
        Write-Host 'Compose設定は有効です。'
    }
    'loop' {
        $nodeCommand = Get-Command node -CommandType Application -ErrorAction SilentlyContinue |
            Select-Object -First 1
        if ($null -eq $nodeCommand) {
            throw 'nodeが見つかりません。Node.jsをインストールしてから再実行してください。'
        }

        $loopScript = Join-Path $script:ProjectRoot 'scripts\dev-loop.js'
        $loopArguments = @($loopScript)
        if ($null -ne $resolvedEnvFile) {
            $loopArguments += $resolvedEnvFile
        }

        $previousPowerShellPath = $env:COCOON_PWSH_PATH
        try {
            $env:COCOON_PWSH_PATH = (Get-Process -Id $PID).Path
            & $nodeCommand.Source @loopArguments
            if ($LASTEXITCODE -ne 0) {
                throw "監視ループが終了コード $LASTEXITCODE で停止しました。"
            }
        } finally {
            $env:COCOON_PWSH_PATH = $previousPowerShellPath
        }
    }
    'help' {
        Show-DevelopmentHelp
    }
}
