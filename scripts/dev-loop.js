/* eslint-disable no-console */
'use strict';

const { watch } = require('node:fs');
const path = require('node:path');
const { spawn } = require('node:child_process');

const projectRoot = path.resolve(__dirname, '..');
const devScript = path.join(projectRoot, 'docker', 'dev.ps1');
const envFile = process.argv[2] || null;
const powerShell = process.env.COCOON_PWSH_PATH || 'pwsh';
const targetExtensions = new Set([
  '.css',
  '.js',
  '.json',
  '.jsx',
  '.php',
  '.sass',
  '.scss',
  '.ts',
  '.tsx',
  '.yaml',
  '.yml',
]);
const ignoredPathPattern =
  /(^|\/)(\.git|\.phpunit\.cache|node_modules|vendor|tmp|tmp-user|tests\/coverage)(\/|$)/;

let debounceTimer = null;
let testProcess = null;
let rerunRequested = false;

function shouldRunTests(fileName) {
  if (!fileName) {
    return false;
  }

  // WindowsとLinuxで共通利用するためのパス区切り正規化
  const normalizedPath = fileName.replaceAll('\\', '/');
  if (ignoredPathPattern.test(normalizedPath)) {
    return false;
  }

  const baseName = path.basename(normalizedPath);
  return (
    targetExtensions.has(path.extname(normalizedPath).toLowerCase()) ||
    baseName === 'composer.json' ||
    baseName === 'Dockerfile'
  );
}

function runTests() {
  if (testProcess) {
    // 実行中に届いた変更の次回テスト予約
    rerunRequested = true;
    return;
  }

  console.log('\n変更を検出しました。PHPUnitを実行します。\n');
  // 監視開始時に指定されたenvファイルのテストプロセスへの引き継ぎ
  const testArguments = [
    '-NoProfile',
    '-NonInteractive',
    '-File',
    devScript,
    'test',
  ];
  if (envFile) {
    testArguments.push(envFile);
  }

  testProcess = spawn(
    powerShell,
    testArguments,
    {
      cwd: projectRoot,
      stdio: 'inherit',
      windowsHide: true,
    }
  );

  testProcess.on('exit', (exitCode) => {
    const succeeded = exitCode === 0;
    console.log(
      succeeded
        ? '\nテストに成功しました。次の変更を待機します。'
        : `\nテストに失敗しました（終了コード: ${exitCode ?? '不明'}）。`
    );
    testProcess = null;

    if (rerunRequested) {
      rerunRequested = false;
      runTests();
    }
  });

  testProcess.on('error', (error) => {
    console.error(`PowerShellの起動に失敗しました: ${error.message}`);
    testProcess = null;
    clearTimeout(debounceTimer);
    watcher.close();
    process.exit(1);
  });
}

function scheduleTests() {
  // 連続した変更通知の500ミリ秒単位での集約
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(runTests, 500);
}

const watcher = watch(
  projectRoot,
  { recursive: true },
  (_eventType, fileName) => {
    if (shouldRunTests(fileName)) {
      scheduleTests();
    }
  }
);

watcher.on('error', (error) => {
  console.error(`ファイル監視に失敗しました: ${error.message}`);
  process.exitCode = 1;
});

process.on('SIGINT', () => {
  watcher.close();
  if (testProcess) {
    testProcess.kill();
  }
  console.log('\n監視ループを終了しました。');
  process.exit(0);
});

console.log('Cocoonの変更監視を開始しました。Ctrl+Cで終了します。');
runTests();
