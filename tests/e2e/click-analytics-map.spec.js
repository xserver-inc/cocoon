const fs = require('fs');
const path = require('path');
const {test, expect} = require('@playwright/test');

const analytics = fs.readFileSync(path.resolve(__dirname, '../../lib/page-access/analytics/assets/analytics.js'), 'utf8');
const stylesheet = fs.readFileSync(path.resolve(__dirname, '../../lib/page-access/analytics/assets/analytics.css'), 'utf8');

async function openPreview(page, height, waitForArticle) {
  await page.route('https://analytics.test/dashboard', route => route.fulfill({
    contentType: 'text/html',
    body: `<!doctype html><html><head><style>${stylesheet}</style></head><body>
      <div class="cocoon-click-map-frame-wrap" style="width:1200px">
        <iframe id="cocoon-click-map-frame" src="/article"></iframe>
        <div id="cocoon-click-map-overlay" class="cocoon-click-map-overlay">
          <span class="cocoon-click-heat-cell" style="top:50%;left:0;width:10%;height:2%"></span>
        </div>
      </div></body></html>`
  }));
  await page.route('https://analytics.test/article', async route => {
    if (waitForArticle) {await waitForArticle;}
    await route.fulfill({contentType: 'text/html', body: `<!doctype html><html><body style="margin:0">
      <main class="entry-content" style="height:${height}px;position:relative">
        <a href="/target" style="position:absolute;top:${height / 2}px">リンク</a>
      </main></body></html>`});
  });
  await page.goto('https://analytics.test/dashboard', {waitUntil: waitForArticle ? 'domcontentloaded' : 'load'});
  await page.evaluate(() => {
    window.CocoonAnalytics = {clickMap: [{semantic_area: 'content', occurrence_no: 0, clicks: 5}]};
  });
}

for (const height of [1600, 40000]) {
  test(`先に読み込まれた高さ${height}pxの記事にも正しい位置でマップを描画`, async ({page}) => {
    await openPreview(page, height);
    await page.addScriptTag({content: analytics});
    const overlay = page.locator('#cocoon-click-map-overlay');
    await expect(overlay).toHaveCSS('height', `${height}px`);
    await expect(page.locator('#cocoon-click-map-frame')).toHaveCSS('height', `${height}px`);
    await expect(overlay.locator('.cocoon-click-link-marker')).toHaveText('5');
    // 割合で記録したクリック位置と、記事中央のリンク位置の照合
    await expect(overlay.locator('.cocoon-click-heat-cell')).toHaveCSS('top', `${height / 2}px`);
    await expect(overlay.locator('.cocoon-click-link-marker')).toHaveCSS('top', `${height / 2}px`);
  });
}

test('後から読み込まれるプレビューの初期化と再読み込みでもバッジが重複しないことの確認', async ({page}) => {
  let finishArticle;
  const articleReady = new Promise(resolve => {finishArticle = resolve;});
  await openPreview(page, 40000, articleReady);
  await page.addScriptTag({content: analytics});
  const overlay = page.locator('#cocoon-click-map-overlay');
  // 遷移前のabout:blankを記事として初期化しないことの確認
  expect(await overlay.getAttribute('style')).toBeNull();
  await expect(overlay.locator('.cocoon-click-link-marker')).toHaveCount(0);
  finishArticle();
  await expect(overlay).toHaveCSS('height', '40000px');
  await expect(overlay.locator('.cocoon-click-link-marker')).toHaveCount(1);
  const reloaded = page.waitForEvent('framenavigated', frame => frame.url() === 'https://analytics.test/article');
  await page.locator('#cocoon-click-map-frame').evaluate(frame => {frame.contentWindow.location.reload();});
  await (await reloaded).waitForLoadState('load');
  await expect(overlay.locator('.cocoon-click-link-marker')).toHaveCount(1);
  await expect(overlay.locator('.cocoon-click-heat-cell')).toHaveCount(1);
  await expect(overlay).toHaveCSS('height', '40000px');
});
