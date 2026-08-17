const fs = require('fs');
const path = require('path');
const zlib = require('zlib');
const {test, expect} = require('@playwright/test');

const collectorPath = path.resolve(__dirname, '../../js/click-analytics.js');
const collector = fs.readFileSync(collectorPath, 'utf8');

test('計測JavaScriptは8KB以下で100リンク初期処理にLong Taskを発生させない', async ({page}) => {
  expect(zlib.gzipSync(Buffer.from(collector)).length).toBeLessThanOrEqual(8192);
  const links = Array.from({length: 100}, (_, index) => `<a href="#link-${index}">link ${index}</a>`).join('');
  await page.setContent(`<!doctype html><html><body><main class="entry-content">${links}</main></body></html>`);
  await page.evaluate(() => {
    window.__cocoonLongTasks = [];
    window.__cocoonObservationStarted = performance.now();
    if ('PerformanceObserver' in window) {
      const observer = new PerformanceObserver((list) => window.__cocoonLongTasks.push(...list.getEntries().filter((entry) => entry.startTime >= window.__cocoonObservationStarted).map((entry) => entry.duration)));
      try {observer.observe({type: 'longtask'});} catch (error) { /* 非対応ブラウザーでは空配列のまま評価します。 */ }
    }
    window.CocoonClickAnalyticsConfig = {
      endpoint: '/wp-json/cocoon/v1/click-events', sourcePostId: 10, layoutRevision: 'a'.repeat(64), samplingRate: 10,
      token: 'b'.repeat(64), siteHosts: ['example.test'], trackInternal: true, trackExternal: true, trackSpecial: true,
      impressions: false, heatmap: false, outcomes: false, respectPrivacy: true, initialConsent: true
    };
  });
  const duration = await page.evaluate((source) => {
    const script = document.createElement('script');
    script.textContent = source;
    const started = performance.now();
    document.head.appendChild(script);
    return performance.now() - started;
  }, collector);
  await page.waitForTimeout(100);
  const longTasks = await page.evaluate(() => window.__cocoonLongTasks);
  expect(duration).toBeLessThanOrEqual(10);
  expect(longTasks.filter((value) => value >= 50)).toHaveLength(0);
});
