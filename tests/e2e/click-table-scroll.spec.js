const fs = require('fs');
const path = require('path');
const {execFileSync} = require('child_process');
const {test, expect} = require('@playwright/test');

const root = path.resolve(__dirname, '../..');
const css = fs.readFileSync(path.join(root, 'lib/page-access/analytics/assets/analytics.css'), 'utf8');
const script = fs.readFileSync(path.join(root, 'lib/page-access/analytics/assets/click-table-scroll.js'), 'utf8');
const fixture = path.join(root, 'tests/fixtures/click-sort-headers.php');
const cache = new Map();
function markup(view) {
  if (!cache.has(view)) {cache.set(view, execFileSync('php', [fixture, '--table', 'click_view=' + view], {encoding: 'utf8', timeout: 30000}));}
  return cache.get(view);
}
async function setup(page, view = 'internal', options = {}) {
  const errors = [];
  page.on('pageerror', error => errors.push(error.message));
  await page.emulateMedia({reducedMotion: options.motion ? 'no-preference' : 'reduce'});
  await page.setViewportSize({width: 640, height: 800});
  await page.route('https://scroll.test/**', route => route.fulfill({contentType: 'text/html', body: `<!doctype html><html lang="ja" dir="${options.rtl ? 'rtl' : 'ltr'}"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width"><style>
    body{margin:16px;font:14px sans-serif} table{width:100%;border-collapse:collapse} ${css}</style></head>
    <body>${markup(view)}<script>${options.fallback ? 'window.ResizeObserver = undefined;' : ''}${script}</script></body></html>`}));
  await page.goto('https://scroll.test/' + (options.hash || ''));
  return {errors, viewport: page.locator('.cocoon-click-table-scroll'), frame: page.locator('.cocoon-click-table-frame'),
    controls: page.locator('.cocoon-click-scroll-controls'), left: page.getByRole('button', {name: '表を左にスクロール'}), right: page.getByRole('button', {name: '表を右にスクロール'})};
}
for (const view of ['overview', 'internal', 'external', 'map']) {
  test(`横スクロールの案内と端の影 ${view}`, async ({page}) => {
    const {errors, viewport, frame, controls, left, right} = await setup(page, view);
    await expect(controls).toBeVisible();
    await expect(page.locator('.cocoon-click-sort-help, .cocoon-click-table-toolbar')).toHaveCount(0);
    await expect(page.locator('.cocoon-click-scroll-hint')).toContainText('横にスクロールできます');
    await expect(left).toHaveAttribute('aria-disabled', 'true');
    await expect(frame).toHaveClass(/can-scroll-right/);
    await expect(frame).not.toHaveClass(/can-scroll-left/);
    await right.focus();
    await right.press('Enter');
    await expect(frame).toHaveClass(/can-scroll-left/);
    await expect(frame).toHaveClass(/can-scroll-right/);
    await right.press('Space');
    await expect(right).toHaveAttribute('aria-disabled', 'true');
    await expect(right).toBeFocused();
    await expect(frame).not.toHaveClass(/can-scroll-right/);
    await expect(left).toHaveAttribute('aria-controls', await viewport.getAttribute('id'));
    await left.click();
    await expect(right).toHaveAttribute('aria-disabled', 'false');
    await page.setViewportSize({width: 1800, height: 800});
    await expect(controls).toBeHidden();
    await expect(viewport).toBeFocused();
    await expect(frame).not.toHaveClass(/can-scroll-/);
    await page.setViewportSize({width: 320, height: 568});
    await expect(controls).toBeVisible();
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    expect((await right.boundingBox()).width).toBeGreaterThanOrEqual(44);
    expect(await frame.evaluate(el => getComputedStyle(el, '::after').pointerEvents)).toBe('none');
    expect(errors).toEqual([]);
  });
}
test('ハッシュ移動・ネイティブ操作・コンテナー幅の変化', async ({page}) => {
  const {viewport, frame, controls, left} = await setup(page, 'internal', {hash: '#cocoon-click-sort-ctr'});
  await expect(frame).toHaveClass(/can-scroll-left/);
  await viewport.evaluate(el => {el.scrollLeft = 0;});
  await expect(left).toHaveAttribute('aria-disabled', 'true');
  await viewport.focus();
  await page.keyboard.press('ArrowRight');
  await expect(left).toHaveAttribute('aria-disabled', 'false');
  // ウィンドウリサイズを伴わない表幅変化の検証
  await page.locator('.cocoon-click-table-scroll table').evaluate(el => {el.style.minWidth = '0'; el.style.width = '100%';});
  await expect(controls).toBeHidden();
  await page.locator('.cocoon-click-table-scroll table').evaluate(el => {el.style.minWidth = '1400px';});
  await expect(controls).toBeVisible();
});
test('RTLでも矢印の物理方向と端の影の一致', async ({page}) => {
  const {frame, left, right} = await setup(page, 'internal', {rtl: true});
  await expect(right).toHaveAttribute('aria-disabled', 'true');
  await left.click();
  await expect(frame).toHaveClass(/can-scroll-left/);
  await expect(frame).toHaveClass(/can-scroll-right/);
  await left.click();
  await expect(left).toHaveAttribute('aria-disabled', 'true');
  await expect(frame).not.toHaveClass(/can-scroll-left/);
  await page.locator('tbody').evaluate(body => {
    for (let index = 0; index < 40; index++) {
      const row = body.insertRow();
      for (let column = 0; column < 10; column++) {row.insertCell().textContent = `${index}:${column}`;}
    }
  });
  await page.evaluate(() => {window.scrollTo(0, 600);});
  await expect(page.locator('.cocoon-click-sticky-header')).toBeVisible();
  expect(await page.evaluate(() => {
    const original = document.querySelector('.cocoon-click-table-scroll th').getBoundingClientRect();
    const mirror = document.querySelector('.cocoon-click-sticky-header th').getBoundingClientRect();
    return Math.abs(original.left - mirror.left);
  })).toBeLessThanOrEqual(2);
});
test('ResizeObserver非対応時の画面リサイズ', async ({page}) => {
  const {controls} = await setup(page, 'internal', {fallback: true});
  await expect(controls).toBeVisible();
  await page.setViewportSize({width: 1800, height: 800});
  await expect(controls).toBeHidden();
});
test('JavaScript無効時のネイティブ横スクロール', async ({browser}) => {
  const context = await browser.newContext({javaScriptEnabled: false});
  try {
    const page = await context.newPage();
    const {controls, viewport} = await setup(page);
    await expect(controls).toBeHidden();
    expect(await viewport.evaluate(el => el.scrollWidth > el.clientWidth)).toBe(true);
    await expect(page.locator('#cocoon-click-sort-ctr')).toHaveAttribute('href', /order=ctr/);
  } finally {await context.close();}
});

test('アニメーション中の連打・反転とリサイズ後の端判定', async ({page}) => {
  const {errors, viewport, left, right, controls} = await setup(page, 'internal', {motion: true});
  // イベントループをまたがない連打後の最終状態の確認
  await right.evaluate(el => {for (let index = 0; index < 8; index++) {el.click();}});
  await expect.poll(() => viewport.evaluate(el => el.scrollLeft)).toBeGreaterThan(300);
  await left.click();
  await expect(left).toHaveAttribute('aria-disabled', 'true');
  await right.click();
  await page.setViewportSize({width: 1800, height: 800});
  await expect(controls).toBeHidden();
  await page.setViewportSize({width: 640, height: 800});
  await expect(controls).toBeVisible();
  await expect(left).toHaveAttribute('aria-disabled', 'true');
  await expect(right).toHaveAttribute('aria-disabled', 'false');
  expect(errors).toEqual([]);
});

test('非表示からの復帰と100行の表の操作・案内更新', async ({page}) => {
  const {errors, viewport, controls, right, frame} = await setup(page);
  const panel = page.locator('.cocoon-click-table-panel');
  await panel.evaluate(el => {el.style.display = 'none';});
  await expect(controls).toBeHidden();
  await panel.evaluate(el => {el.style.display = '';});
  await expect(controls).toBeVisible();
  await page.locator('tbody').evaluate(el => {
    for (let index = 0; index < 100; index++) {
      const row = el.insertRow();
      for (let column = 0; column < 10; column++) {
        const button = document.createElement('button');
        button.textContent = `${index}:${column}`;
        button.addEventListener('click', () => {button.dataset.clicked = 'true';});
        row.insertCell().appendChild(button);
      }
    }
  });
  await right.click();
  await expect(frame).toHaveClass(/can-scroll-left/);
  // 最終行のセル操作とネイティブなスクロール操作の維持
  const button = page.getByRole('button', {name: '99:9', exact: true});
  await button.click();
  await expect(button).toHaveAttribute('data-clicked', 'true');
  await viewport.evaluate(el => {el.scrollLeft = el.scrollWidth;});
  await expect(right).toHaveAttribute('aria-disabled', 'true');
  expect(await frame.evaluate(el => getComputedStyle(el, '::after').opacity)).toBe('0');
  expect(errors).toEqual([]);
});

test('8言語のスクロール案内とボタン名の配置', async ({page}) => {
  const {errors, controls} = await setup(page);
  const gettext = require('gettext-parser');
  await page.setViewportSize({width: 320, height: 568});
  for (const locale of ['de_DE', 'en_US', 'es_ES', 'fr_FR', 'ko_KR', 'pt_PT', 'zh_CN', 'zh_TW']) {
    const messages = gettext.mo.parse(fs.readFileSync(path.join(root, `languages/${locale}.mo`))).translations[''];
    const hint = messages['横にスクロールできます'].msgstr[0];
    expect(hint).toBeTruthy();
    await page.locator('.cocoon-click-scroll-hint').evaluate((el, text) => {el.textContent = text;}, hint);
    for (const [direction, label] of [['left', '表を左にスクロール'], ['right', '表を右にスクロール']]) {
      const translation = messages[label].msgstr[0];
      expect(translation).toBeTruthy();
      const button = page.locator(`.cocoon-click-scroll-${direction}`);
      await button.evaluate((el, text) => {el.setAttribute('aria-label', text); el.title = text;}, translation);
      await expect(button).toHaveAccessibleName(translation);
    }
    await expect(controls).toBeVisible();
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    const bounds = await controls.boundingBox();
    expect(bounds.x).toBeGreaterThanOrEqual(0);
    expect(bounds.x + bounds.width).toBeLessThanOrEqual(320);
  }
  expect(errors).toEqual([]);
});

test('長い表の途中で操作バーと並べ替え見出しが追従', async ({page}) => {
  const {errors, viewport, controls, right} = await setup(page);
  const sticky = page.locator('.cocoon-click-sticky-header');
  await page.locator('tbody').evaluate(body => {
    for (let index = 0; index < 50; index++) {
      const row = body.insertRow();
      for (let column = 0; column < 10; column++) {row.insertCell().textContent = `${index}:${column}`;}
    }
    body.closest('.cocoon-click-table-panel').insertAdjacentHTML('afterend', '<div style="height:900px"></div>');
  });
  await expect(sticky).toBeHidden();
  const sourceSort = page.locator('.cocoon-click-table-scroll .cocoon-click-sort').first();
  await sourceSort.focus();
  await page.evaluate(() => {window.scrollTo(0, 600);});
  await expect(controls).toHaveClass(/is-stuck/);
  await expect(sticky).toBeVisible();
  await expect(sticky.locator('.cocoon-click-sort').first()).toBeFocused();
  await expect(page.getByRole('link', {name: /並べ替え/})).toHaveCount(4);
  await expect(sourceSort).toHaveAttribute('inert', '');
  await expect(sourceSort).toHaveAttribute('aria-hidden', 'true');
  await expect(page.locator('.cocoon-click-table-scroll th.cocoon-click-sortable').first()).toHaveAccessibleName('推定表示');
  const bounds = await page.evaluate(() => {
    const rect = selector => document.querySelector(selector).getBoundingClientRect();
    return {controls: rect('.cocoon-click-scroll-controls'), sticky: rect('.cocoon-click-sticky-header'), viewport: rect('.cocoon-click-table-scroll')};
  });
  expect(Math.abs(bounds.sticky.top - bounds.controls.bottom)).toBeLessThanOrEqual(2);
  expect(Math.abs(bounds.sticky.left - bounds.viewport.left)).toBeLessThanOrEqual(2);
  await expect(page.locator('.cocoon-click-table-scroll .cocoon-click-sort').first()).toHaveAttribute('tabindex', '-1');
  await expect(sticky.locator('[id]')).toHaveCount(0);
  await right.click();
  await expect.poll(() => page.evaluate(() => {
    const original = document.querySelector('.cocoon-click-table-scroll th').getBoundingClientRect();
    const mirror = document.querySelector('.cocoon-click-sticky-header th').getBoundingClientRect();
    return Math.abs(original.left - mirror.left);
  })).toBeLessThanOrEqual(2);
  await right.focus();
  await page.keyboard.press('Tab');
  await expect(sticky.locator('.cocoon-click-sort').first()).toBeFocused();
  await page.evaluate(() => {window.scrollTo(0, 0);});
  await expect(sticky).toBeHidden();
  await expect(sourceSort).toBeFocused();
  await expect(sourceSort).not.toHaveAttribute('inert');
  await page.evaluate(() => {window.scrollTo(0, 600);});
  await expect(sticky.locator('.cocoon-click-sort').first()).toBeFocused();
  await page.evaluate(() => {window.scrollTo(0, document.documentElement.scrollHeight);});
  await expect(sticky).toBeHidden();
  await expect(controls).not.toHaveClass(/is-stuck/);
  await expect(viewport).toBeFocused();
  await expect(sourceSort).not.toHaveAttribute('tabindex', '-1');
  await expect(sourceSort).not.toHaveAttribute('inert');
  await expect(sourceSort).not.toHaveAttribute('aria-hidden');
  await expect(page.getByRole('link', {name: /並べ替え/})).toHaveCount(4);
  await page.evaluate(() => {window.scrollTo(0, 600);});
  await expect(sticky).toBeVisible();
  await sticky.locator('.cocoon-click-sort').first().press('Enter');
  await expect(page).toHaveURL(/order=impressions/);
  expect(errors).toEqual([]);
});

test('管理バーの下で追従し、横スクロール不要時も見出しだけ追従', async ({page}) => {
  const {errors, controls} = await setup(page);
  await page.locator('tbody').evaluate(body => {
    for (let index = 0; index < 40; index++) {
      const row = body.insertRow();
      for (let column = 0; column < 10; column++) {row.insertCell().textContent = `${index}:${column}`;}
    }
    const bar = document.createElement('div');
    bar.id = 'wpadminbar';
    bar.style.cssText = 'position:fixed;top:0;left:0;height:32px;width:100%';
    document.body.appendChild(bar);
  });
  await page.evaluate(() => {window.scrollTo(0, 600);});
  await expect(controls).toHaveClass(/is-stuck/);
  expect((await controls.boundingBox()).y).toBeCloseTo(32, 0);
  await page.setViewportSize({width: 1800, height: 800});
  await expect(controls).toBeHidden();
  const sticky = page.locator('.cocoon-click-sticky-header');
  await expect(sticky).toBeVisible();
  expect((await sticky.boundingBox()).y).toBeCloseTo(32, 0);
  expect(errors).toEqual([]);
});
