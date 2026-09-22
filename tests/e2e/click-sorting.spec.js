const fs = require('fs');
const path = require('path');
const {execFileSync} = require('child_process');
const {test, expect} = require('@playwright/test');

const root = path.resolve(__dirname, '../..');
const stylesheet = fs.readFileSync(path.join(root, 'lib/page-access/analytics/assets/analytics.css'), 'utf8');
const fixture = path.join(root, 'tests/fixtures/click-sort-headers.php');
const headerCache = new Map();

function getHeaders(view) {
  if (headerCache.has(view)) return headerCache.get(view);
  const initial = `click_view=${view}&period=30days&device=mobile&group=destination&paged=3`;
  const queries = [initial];
  for (const column of ['clicks', 'unique', 'impressions', 'ctr']) {
    for (const direction of ['asc', 'desc']) {
      const query = new URLSearchParams(initial);
      query.delete('paged');
      query.set('order', column);
      query.set('direction', direction);
      queries.push(query.toString());
    }
  }
  // 画面遷移ごとのPHP起動を避ける、本番見出しHTMLの一括生成
  const headers = JSON.parse(execFileSync('php', [fixture, '--batch', JSON.stringify(queries)], {encoding: 'utf8', timeout: 30000}));
  headerCache.set(view, headers);
  return headers;
}

for (const view of ['overview', 'internal', 'external', 'map']) {
  for (const width of [1280, 390]) {
    test(`列見出しの並べ替え ${view} 幅${width}`, async ({page}) => {
      test.setTimeout(120000);
      await page.setViewportSize({width, height: 720});
      const errors = [];
      page.on('pageerror', error => errors.push(error.message));
      const headers = getHeaders(view);
      await page.route('https://sorting.test/admin.php**', async route => {
        const url = new URL(route.request().url());
        // 本番PHPから生成した見出しと本番CSSによる操作確認
        const query = url.search.slice(1);
        expect(Object.hasOwn(headers, query)).toBe(true);
        await route.fulfill({contentType: 'text/html', body: `<!doctype html><html lang="ja"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width">
          <style>body{margin:20px;font:14px sans-serif}table{width:100%;border-collapse:collapse} ${stylesheet}</style></head><body>
          <div style="height:200px">クリック解析</div><div class="cocoon-analytics-table-scroll cocoon-click-table-scroll">
          <table class="cocoon-click-table"><colgroup><col style="width:50%"><col class="cocoon-click-col-impressions"><col class="cocoon-click-col-clicks"><col class="cocoon-click-col-unique"><col class="cocoon-click-col-ctr"><col style="width:22%"></colgroup>
          <thead><tr><th>リンク</th>${headers[query]}<th>その他</th></tr></thead><tbody><tr><td>テストリンク</td><td>100</td><td>10</td><td>8</td><td>10.0%</td><td>—</td></tr></tbody></table></div></body></html>`});
      });
      await page.goto(`https://sorting.test/admin.php?click_view=${view}&period=30days&device=mobile&group=destination&paged=3`);
      await expect(page.locator('th[aria-sort]')).toHaveCount(1);
      for (const column of ['clicks', 'unique', 'impressions', 'ctr']) {
        const firstDirection = column === 'clicks' ? 'asc' : 'desc';
        for (const direction of [firstDirection, firstDirection === 'asc' ? 'desc' : 'asc']) {
          const link = page.locator(`#cocoon-click-sort-${column}`);
          await link.focus();
          await link.press('Enter');
          await expect(page.locator(`th.cocoon-click-cell-${column}`)).toHaveAttribute('aria-sort', direction === 'asc' ? 'ascending' : 'descending');
          await expect(page.locator('th[aria-sort]')).toHaveCount(1);
          const current = new URL(page.url());
          expect(current.searchParams.get('order')).toBe(column);
          expect(current.searchParams.get('direction')).toBe(direction);
          expect(current.searchParams.has('paged')).toBe(false);
          expect(current.searchParams.get('click_view')).toBe(view);
          expect(current.searchParams.get('device')).toBe('mobile');
          expect(current.searchParams.get('group')).toBe('destination');
          expect(current.hash).toBe(`#cocoon-click-sort-${column}`);
          await expect(link).toBeFocused();
          const box = await link.boundingBox();
          expect(box.x).toBeGreaterThanOrEqual(19);
          expect(box.x + box.width).toBeLessThanOrEqual(width - 19);
          expect(box.height).toBeGreaterThanOrEqual(44);
          expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
        }
      }
      expect(errors).toEqual([]);
    });
  }
}
