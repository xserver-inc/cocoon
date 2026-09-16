import fs from 'node:fs';
import path from 'node:path';
import lighthouse from 'lighthouse';
import * as chromeLauncher from 'chrome-launcher';

const url = process.env.COCOON_LIGHTHOUSE_URL || 'http://localhost:8085/';
const defaultEdge = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';
const chromePath = process.env.COCOON_CHROME_PATH || (fs.existsSync(defaultEdge) ? defaultEdge : undefined);
const profileRoot = path.join(process.cwd(), '.lighthouse-profiles');
let profileSequence = 0;

function wait(milliseconds) {
  return new Promise((resolve) => setTimeout(resolve, milliseconds));
}

async function closeBrowser(launcher, profilePath) {
  const chromeProcess = launcher.chromeProcess;
  const closed = chromeProcess
    ? new Promise((resolve) => chromeProcess.once('close', resolve))
    : Promise.resolve();
  launcher.kill();
  // chrome-launcherが任意プロファイルのエラーログを閉じないため、明示的に閉じます。
  if (launcher.errFile !== undefined) {
    fs.closeSync(launcher.errFile);
    delete launcher.errFile;
  }
  await Promise.race([closed, wait(2000)]);
  if (chromeProcess && chromeProcess.exitCode === null) {
    try {
      chromeProcess.kill('SIGKILL');
    } catch {
      // 既に終了済みなら追加処理は不要です。
    }
  }
  await wait(250);
  fs.rmSync(profilePath, {recursive: true, force: true, maxRetries: 20, retryDelay: 250});
}

function median(values) {
  const sorted = [...values].sort((left, right) => left - right);
  return sorted[Math.floor(sorted.length / 2)];
}

async function measureOnce(blockCollector) {
    fs.mkdirSync(profileRoot, {recursive: true});
    const profilePath = path.join(profileRoot, `run-${process.pid}-${profileSequence}`);
    profileSequence += 1;
    fs.mkdirSync(profilePath, {recursive: true});
    const launcher = new chromeLauncher.Launcher({
      chromePath,
      chromeFlags: ['--headless=new', '--no-first-run', '--disable-gpu', '--no-sandbox', '--disable-dev-shm-usage'],
      userDataDir: profilePath,
      handleSIGINT: false
    });
    await launcher.launch();
    try {
      const config = {
        extends: 'lighthouse:default',
        settings: {
          onlyCategories: ['performance'],
          blockedUrlPatterns: blockCollector ? ['*click-analytics.js*'] : []
        }
      };
      const run = await lighthouse(url, {port: launcher.port, output: 'json', logLevel: 'error'}, config);
      return {
        score: run.lhr.categories.performance.score * 100,
        fcp: run.lhr.audits['first-contentful-paint'].numericValue,
        lcp: run.lhr.audits['largest-contentful-paint'].numericValue,
        cls: run.lhr.audits['cumulative-layout-shift'].numericValue,
        speedIndex: run.lhr.audits['speed-index'].numericValue,
        tbt: run.lhr.audits['total-blocking-time'].numericValue
      };
    } finally {
      await closeBrowser(launcher, profilePath);
    }
}

function summarize(results) {
  // 初心者向け: 一時的な揺れに左右されないよう5回の中央の値を採用します。
  return {
    score: median(results.map((result) => result.score)),
    fcp: median(results.map((result) => result.fcp)),
    lcp: median(results.map((result) => result.lcp)),
    cls: median(results.map((result) => result.cls)),
    speedIndex: median(results.map((result) => result.speedIndex)),
    tbt: median(results.map((result) => result.tbt))
  };
}

async function main() {
  try {
    const baselineResults = [];
    const trackingResults = [];
    for (let index = 0; index < 5; index += 1) {
      // サーバー温度の偏りを避けるため、基準側と計測側の実行順を交互に入れ替えます。
      const order = index % 2 === 0 ? [true, false] : [false, true];
      for (const blockCollector of order) {
        const result = await measureOnce(blockCollector);
        (blockCollector ? baselineResults : trackingResults).push(result);
      }
    }
    const baseline = summarize(baselineResults);
    const tracking = summarize(trackingResults);
    const summary = {
      url,
      baseline,
      tracking,
      difference: {
        score: tracking.score - baseline.score,
        fcp: tracking.fcp - baseline.fcp,
        lcp: tracking.lcp - baseline.lcp,
        cls: tracking.cls - baseline.cls,
        speedIndex: tracking.speedIndex - baseline.speedIndex,
        tbt: tracking.tbt - baseline.tbt
      }
    };
    process.stdout.write(`${JSON.stringify(summary, null, 2)}\n`);

    // 初心者向け: 計画の合格基準を超えた場合は自動テストを失敗として終了します。
    if (tracking.score < baseline.score - 1 || tracking.lcp > baseline.lcp + 50 || Math.abs(tracking.cls - baseline.cls) > 0.001) {
      process.exitCode = 1;
    }
  } finally {
    fs.rmSync(profileRoot, {recursive: true, force: true, maxRetries: 20, retryDelay: 250});
  }
}

main().catch((error) => {
  process.stderr.write(`${error.stack || error.message}\n`);
  process.exitCode = 1;
});
