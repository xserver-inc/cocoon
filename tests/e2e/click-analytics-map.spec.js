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

test('遅延挿入・リンク移動・記事の縮小への追従と再描画の収束', async ({page}) => {
  const errors = [];
  page.on('pageerror', error => errors.push(error.message));
  await openPreview(page, 1600);
  await page.addScriptTag({content: analytics});
  const overlay = page.locator('#cocoon-click-map-overlay');
  const marker = overlay.locator('.cocoon-click-link-marker');
  await expect(marker).toHaveCSS('top', '800px');
  const article = page.frames().find(frame => frame.url().endsWith('/article'));
  await article.evaluate(() => {
    document.querySelector('main').style.height = '3200px';
    document.querySelector('a').style.top = '1200px';
  });
  await expect(overlay).toHaveCSS('height', '3200px');
  await expect(marker).toHaveCSS('top', '1200px');
  await article.evaluate(() => {document.querySelector('a').style.top = '1400px';});
  await expect(marker).toHaveCSS('top', '1400px');
  await article.evaluate(() => {
    document.querySelector('main').style.height = '1000px';
    document.querySelector('a').style.top = '500px';
  });
  await expect(overlay).toHaveCSS('height', '1000px');
  await expect(marker).toHaveCSS('top', '500px');
  // 静止後にDOM書き換えが続かないことの実測
  const changes = await overlay.evaluate(element => new Promise(resolve => {
    let count = 0;
    const observer = new MutationObserver(() => {count++;});
    setTimeout(() => {
      observer.observe(element, {childList: true});
      setTimeout(() => {observer.disconnect(); resolve(count);}, 500);
    }, 300);
  }));
  expect(changes).toBe(0);
  expect(errors).toEqual([]);
});

test('非表示リンクによる掲載順の維持と異なるオリジンへの遷移時のバッジ消去', async ({page}) => {
  await openPreview(page, 1600);
  await page.evaluate(() => {
    window.CocoonAnalytics.clickMap.push({semantic_area: 'content', occurrence_no: 1, clicks: 8});
  });
  await page.addScriptTag({content: analytics});
  const marker = page.locator('.cocoon-click-link-marker');
  await expect(marker).toHaveText('5');
  const article = page.frames().find(frame => frame.url().endsWith('/article'));
  await article.evaluate(() => {
    const hidden = document.createElement('a');
    hidden.href = '/hidden';
    hidden.style.display = 'none';
    document.querySelector('main').prepend(hidden);
  });
  await expect(marker).toHaveCount(1);
  await expect(marker).toHaveText('8');
  await page.route('https://other.test/**', route => route.fulfill({body: '<html><body>other</body></html>'}));
  await page.locator('iframe').evaluate(frame => {frame.src = 'https://other.test/article';});
  await expect(page.locator('#cocoon-click-map-overlay')).toHaveClass(/cocoon-click-map-unavailable/);
  await expect(marker).toHaveCount(0);
  await page.locator('iframe').evaluate(frame => {frame.src = '/article';});
  await expect(marker).toHaveText('5');
  await expect(page.locator('#cocoon-click-map-overlay')).not.toHaveClass(/cocoon-click-map-unavailable/);
});
