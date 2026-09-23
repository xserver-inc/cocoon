const fs = require('fs');
const path = require('path');
const postcss = require('postcss');
const {test, expect} = require('@playwright/test');

const style = fs.readFileSync(path.resolve(__dirname, '../../lib/page-access/analytics/assets/analytics.css'), 'utf8');
const adminStyle = postcss.parse(fs.readFileSync(path.resolve(__dirname, '../../css/admin.css'), 'utf8'));
const paginationRules = [];
// 実際の管理CSSから抽出した、競合の原因となるページ番号用スタイル
adminStyle.walkRules(rule => {if (rule.selector.includes('.tablenav-pages')) {paginationRules.push(rule.toString());}});
const coreStyle = `.tablenav {clear:both;height:32px;margin:6px 0 4px}
  .tablenav .tablenav-pages {float:right;margin:0 0 9px}
  @media (max-width:782px) {.tablenav .tablenav-pages {width:100%;text-align:center;margin:0 0 25px}.tablenav.bottom .tablenav-pages {margin-top:25px}}`;
const scenarios = [{width: 1280, current: 1}, {width: 1280, current: 13}, {width: 1280, current: 25}, {width: 390, current: 13}, {width: 320, current: 13}];

for (const {width, current} of scenarios) {
  test(`クリック解析のページ番号配置 幅${width} ページ${current}`, async ({page}) => {
    test.setTimeout(60000);
    await page.setViewportSize({width, height: 720});
    const link = (number, label = String(number), extra = '') => `<a class="${extra} page-numbers" href="https://pagination.test/dashboard?paged=${number}">${label}</a>`;
    const links = [];
    if (current > 1) {links.push(link(current - 1, '« 前へ', 'prev'));}
    let last = 0;
    for (let number = 1; number <= 25; number++) {
      if (number !== 1 && number !== 25 && Math.abs(number - current) > 2) {continue;}
      if (last && number - last > 1) {links.push('<span class="page-numbers dots">…</span>');}
      links.push(number === current ? `<span class="page-numbers current" aria-current="page">${number}</span>` : link(number));
      last = number;
    }
    if (current < 25) {links.push(link(current + 1, '次へ »', 'next'));}
    await page.route('https://pagination.test/dashboard**', route => route.fulfill({contentType: 'text/html', body: `<!doctype html><html lang="ja"><head><meta charset="utf-8">
      <meta name="viewport" content="width=device-width"><style>body {margin:20px;font:14px sans-serif;background:#f0f0f1} ${coreStyle}
      ${paginationRules.join('\n')} ${style}</style></head><body><div class="cocoon-analytics-wrap">
      <div class="cocoon-click-table-panel"><div class="cocoon-click-table-frame"><div class="cocoon-analytics-table-scroll"><table style="width:1400px;height:100px"><tr><td>クリック解析テーブル</td></tr></table></div></div></div>
      <div class="cocoon-click-results-footer"><p class="description cocoon-click-estimate-note">リンク総数とページ数は概算です。</p>
      <div class="tablenav bottom cocoon-click-pagination"><div class="tablenav-pages"><span class="pagination-links">${links.join('\n')}</span></div></div></div>
      <aside class="cocoon-click-causality-note"><p class="cocoon-click-causality-title">データの見方</p><p id="note">掲載位置による差は観察データです。</p></aside></div></body></html>`}));
    await page.goto('https://pagination.test/dashboard');
    const boxes = await page.locator('.pagination-links > *').evaluateAll(elements => elements.map(el => {
      const rect = el.getBoundingClientRect();
      return {x: rect.x, centerY: (rect.top + rect.bottom) / 2, right: rect.right, bottom: rect.bottom};
    }));
    const rows = new Set(boxes.map(box => Math.round(box.centerY)));
    expect(rows.size).toBeLessThanOrEqual(width > 782 ? 1 : 3);
    if (width <= 390) {expect(rows.size).toBeGreaterThan(1);}
    for (const box of boxes) {
      expect(box.x).toBeGreaterThanOrEqual(20);
      expect(box.right).toBeLessThanOrEqual(width - 20);
    }
    const footer = await page.locator('.cocoon-click-results-footer').boundingBox();
    const estimate = await page.locator('.cocoon-click-estimate-note').boundingBox();
    const pagination = await page.locator('.cocoon-click-pagination').boundingBox();
    const causal = await page.locator('.cocoon-click-causality-note').boundingBox();
    expect(await page.locator('.cocoon-click-results-footer').evaluate(el => getComputedStyle(el).borderTopWidth)).toBe('0px');
    expect(causal.y).toBeGreaterThanOrEqual(footer.y + footer.height);
    if (width <= 782) {expect(pagination.y).toBeGreaterThanOrEqual(estimate.y + estimate.height);}
    else {expect(estimate.y).toBeLessThan(pagination.y + pagination.height);}
    expect((await page.locator('#note').boundingBox()).y).toBeGreaterThan(Math.max(...boxes.map(box => box.bottom)));
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= window.innerWidth)).toBe(true);
    await expect(page.locator('[aria-current="page"]')).toHaveText(String(current));
    await expect(page.locator('.pagination-links a.prev')).toHaveCount(current > 1 ? 1 : 0);
    await expect(page.locator('.pagination-links a.next')).toHaveCount(current < 25 ? 1 : 0);
    if (current === 1) {
      await page.getByRole('link', {name: '次へ »', exact:true}).click();
      await expect(page).toHaveURL('https://pagination.test/dashboard?paged=2');
    }
  });
}

test('画像読み込みの注意書きが入る場合も結果フッターの枠を維持', async ({page}) => {
  await page.route('https://pagination.test/notice', route => route.fulfill({contentType: 'text/html', body: `<!doctype html><html lang="ja"><head><meta charset="utf-8"><style>${style}</style></head><body><div class="cocoon-analytics-wrap"><div class="cocoon-click-table-panel">表</div><p class="cocoon-click-image-notice">画像を読み込む際の注意</p><div class="cocoon-click-results-footer"><p class="cocoon-click-estimate-note">件数は概算です</p></div></div></body></html>`}));
  await page.goto('https://pagination.test/notice');
  await expect(page.locator('.cocoon-click-results-footer')).toHaveCSS('border-top-width', '1px');
  await expect(page.locator('.cocoon-click-results-footer')).toHaveCSS('margin-top', '12px');
});

test('概算の説明がない場合もページ番号を右端に配置', async ({page}) => {
  await page.setViewportSize({width: 1280, height: 720});
  await page.route('https://pagination.test/exact', route => route.fulfill({contentType: 'text/html', body: `<!doctype html><html lang="ja"><head><meta charset="utf-8"><style>body{margin:20px}${coreStyle}${paginationRules.join('\n')}${style}</style></head><body><div class="cocoon-analytics-wrap"><div class="cocoon-click-table-panel">表</div><div class="cocoon-click-results-footer"><div class="tablenav bottom cocoon-click-pagination"><div class="tablenav-pages"><span class="pagination-links"><a class="page-numbers" href="?paged=2">2</a></span></div></div></div></div></body></html>`}));
  await page.goto('https://pagination.test/exact');
  await expect(page.locator('.cocoon-click-estimate-note')).toHaveCount(0);
  const footer = await page.locator('.cocoon-click-results-footer').boundingBox();
  const pagination = await page.locator('.cocoon-click-pagination').boundingBox();
  expect(pagination.x + pagination.width).toBeGreaterThan(footer.x + footer.width - 25);
});
