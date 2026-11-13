const fs = require('fs');
const path = require('path');
const {test, expect} = require('@playwright/test');

const baseUrl = process.env.COCOON_TOC_BASE_URL;
test.skip(!baseUrl, 'COCOON_TOC_BASE_URLでDockerローカル環境を指定した場合だけ実行');
test.setTimeout(120000);

if (baseUrl) {
  const url = new URL(baseUrl);
  if (url.protocol !== 'http:' || !['localhost', '127.0.0.1'].includes(url.hostname) ||
    !['8085', '8090'].includes(url.port) || url.pathname !== '/' || url.username || url.password) {
    throw new Error('目次の実機確認先にはプロジェクトのDockerローカル環境を指定してください。');
  }
}

const pageErrors = new WeakMap();

// ブラウザー起動前にDockerで生成済みの検証HTMLの読み込み
function fixtureHtml(mode) {
  if (!process.env.COCOON_TOC_FIXTURES || !['fixed', 'static', 'logged'].includes(mode)) {
    throw new Error('目次の検証はnpm run test:toc-wordpressから実行してください。');
  }
  return fs.readFileSync(path.join(process.env.COCOON_TOC_FIXTURES, mode + '.html'), 'utf8');
}

test.beforeEach(async ({page}) => {
  const errors = [];
  pageErrors.set(page, errors);
  page.on('pageerror', error => errors.push(error.message));
  await page.route('**/__cocoon-toc-test__/**', route => {
    const url = new URL(route.request().url());
    return route.fulfill({contentType: 'text/html; charset=utf-8', body: fixtureHtml(url.searchParams.get('mode') || 'fixed')});
  });
});

test.afterEach(async ({page}) => {
  expect(pageErrors.get(page), '検証ページのJavaScript例外').toEqual([]);
});

async function openFixture(page, {mode = 'fixed', width = 1280, hash = ''} = {}) {
  await page.setViewportSize({width, height: 900});
  await page.goto(`${baseUrl}__cocoon-toc-test__/?mode=${mode}${hash}`, {waitUntil: 'load', timeout: 90000});
  await page.evaluate(() => document.fonts.ready);
}

// 固定化・表示アニメーション完了後の、実際の重なりと見出し全体の位置の照合
async function expectTargetVisible(page, id, delay = 650) {
  await page.waitForTimeout(delay);
  let measurement;
  await expect(async () => {
    measurement = await page.evaluate(targetId => {
      const anchor = document.getElementById(targetId);
      const target = anchor.closest('h1,h2,h3,h4,h5,h6') || anchor;
      const top = target.getBoundingClientRect().top;
      const visibleBottom = Math.max(0, ...['#header-container', '#wpadminbar', '.mobile-header-menu-buttons'].map(selector => {
        const element = document.querySelector(selector);
        if (!element || getComputedStyle(element).position !== 'fixed') {
          return 0;
        }
        const rect = element.getBoundingClientRect();
        return rect.height > 0 ? rect.bottom : 0;
      }));
      return {top, visibleBottom, hash: location.hash, scrollY};
    }, id);
    expect(measurement.top, '移動先の上端と実際に表示された固定要素の下端').toBeGreaterThanOrEqual(measurement.visibleBottom + 6.5);
    expect(measurement.top, '移動先が画面内に表示').toBeLessThan(300);
  }).toPass({timeout: 3000});
  test.info().annotations.push({type: 'toc-position', description: JSON.stringify(measurement)});
  if (process.env.COCOON_TOC_SCREENSHOTS) {
    fs.mkdirSync(process.env.COCOON_TOC_SCREENSHOTS, {recursive: true});
    await page.screenshot({path: path.join(process.env.COCOON_TOC_SCREENSHOTS,
      `${test.info().project.name}-${test.info().title.replace(/[^a-zA-Z0-9\u3040-\u9fff-]/gu, '_')}.png`)});
  }
}

test('PCの初回目次クリックと同じハッシュへの再クリック', async ({page}) => {
  await openFixture(page);
  await expect(page.locator('#header-container')).not.toHaveClass(/fixed-header/);
  const link = page.locator('.entry-content .toc a[href="#toc2"]').first();
  await link.click();
  await expect(page).toHaveURL(/#toc2$/);
  await expect(page.locator('#header-container')).toHaveClass(/fixed-header/);
  await expectTargetVisible(page, 'toc2');
  await link.click();
  await expectTargetVisible(page, 'toc2');
});

for (const mode of ['fixed', 'static', 'logged']) {
  test(`見出しURLへの初回アクセスと再読み込み ${mode}`, async ({page}) => {
    await openFixture(page, {mode, hash: '#toc2'});
    if (mode === 'logged') {
      await expect(page.locator('#wpadminbar')).toBeVisible();
    }
    await expectTargetVisible(page, 'toc2');
    await page.reload({waitUntil: 'load'});
    await expectTargetVisible(page, 'toc2');
  });
}

for (const width of [390, 700, 1023, 1024]) {
  test(`目次リンクの初回移動と上向き移動 ${width}px`, async ({page}) => {
    await openFixture(page, {width});
    await page.locator('.entry-content .toc a[href="#toc3"]').first().click();
    await expectTargetVisible(page, 'toc3');
    await page.evaluate(() => { location.hash = 'toc1'; });
    await expectTargetVisible(page, 'toc1');
  });
}

test('通常の目次へのリンクで枠の寸法を維持', async ({page}) => {
  await openFixture(page);
  const toc = page.locator('.entry-content #toc');
  const before = await toc.evaluate(element => ({height: element.offsetHeight, padding: getComputedStyle(element).padding}));
  await page.locator('#jump-toc').click();
  await expectTargetVisible(page, 'toc');
  expect(await toc.evaluate(element => ({height: element.offsetHeight, padding: getComputedStyle(element).padding}))).toEqual(before);
});

test('WordPress標準の見出しアンカーとキーボード操作', async ({page}) => {
  await openFixture(page);
  await page.locator('#jump-native').focus();
  await page.locator('#jump-native').press('Enter');
  await expect(page).toHaveURL(/#native-heading$/);
  await expectTargetVisible(page, 'native-heading');
});

for (const [width, mode] of [[390, 'fixed'], [700, 'logged']]) {
  test(`標準モバイル目次ボタンと通常アンカー ${width}px ${mode}`, async ({page}) => {
    await openFixture(page, {width, mode});
    await page.locator('.entry-content .toc a[href="#toc3"]').first().click();
    await expectTargetVisible(page, 'toc3');
    await page.evaluate(() => scrollBy(0, -80));
    await page.waitForTimeout(350);
    await page.locator('.mobile-footer-menu-buttons .go-to-toc-common').click();
    await expectTargetVisible(page, 'toc', 1100);
    await page.locator('.entry-content .toc a[href="#toc3"]').first().click();
    await expectTargetVisible(page, 'toc3');
    await page.evaluate(() => { location.hash = 'toc'; });
    await expectTargetVisible(page, 'toc');
  });
}

test('戻る・進むで見出しの表示位置を補正', async ({page}) => {
  await openFixture(page, {hash: '#toc2'});
  await expectTargetVisible(page, 'toc2');
  await page.evaluate(() => { location.hash = 'toc3'; });
  await expectTargetVisible(page, 'toc3');
  await page.goBack();
  await expectTargetVisible(page, 'toc2');
  await page.goForward();
  await expectTargetVisible(page, 'toc3');
});

test('動きを減らす設定での標準モバイル目次ボタン', async ({page}) => {
  await page.emulateMedia({reducedMotion: 'reduce'});
  await openFixture(page, {width: 390, hash: '#toc3'});
  await expectTargetVisible(page, 'toc3');
  await page.evaluate(() => scrollBy(0, -80));
  await page.waitForTimeout(350);
  await page.locator('.mobile-footer-menu-buttons .go-to-toc-common').click();
  await expectTargetVisible(page, 'toc');
});

test('目次移動の直後でも手動のホイール操作を維持', async ({page}) => {
  await openFixture(page);
  await page.locator('.entry-content .toc a[href="#toc2"]').first().click();
  const before = await page.evaluate(() => scrollY);
  await page.mouse.wheel(0, 200);
  await expect.poll(() => page.evaluate(() => scrollY)).toBeGreaterThan(before + 100);
  await page.waitForTimeout(350);
  expect(await page.evaluate(() => scrollY)).toBeGreaterThan(before + 100);
});
