const fs = require('fs');
const path = require('path');
const {test, expect} = require('@playwright/test');

const script = fs.readFileSync(path.resolve(__dirname, '../../lib/page-access/analytics/assets/map-post-picker.js'), 'utf8');
const style = fs.readFileSync(path.resolve(__dirname, '../../lib/page-access/analytics/assets/analytics.css'), 'utf8');

for (const viewport of [{width: 1280, height: 800}, {width: 1024, height: 600}, {width: 390, height: 844}, {width: 320, height: 568}]) {
  test(`記事候補パネルの配置とキーボード操作 ${viewport.width}x${viewport.height}`, async ({page}) => {
    await page.setViewportSize(viewport);
    await page.route('https://picker.test/admin-ajax.php**', route => route.fulfill({json: {success: true, data: {
      from: '2026-09-01', to: '2026-09-22', no_clicks: 'PCのクリック記録なし',
      items: Array.from({length: 10}, (_, index) => ({id: index + 1, rank: index + 1, name: `記事 ${index + 1} 長い記事タイトルの折り返しと読みやすさの確認`, pv: '12,345', has_clicks: index !== 0})),
    }}}));
    await page.route('https://picker.test/dashboard', route => route.fulfill({contentType: 'text/html', body: `<!doctype html><html lang="ja"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><style>${style}
      body {margin:20px;font:13px sans-serif} .cocoon-analytics-filter-bar {margin-top:160px}
      </style></head><body><form class="cocoon-analytics-filter-bar cocoon-click-filter-bar">
      <select name="period"><option value="30days">直近30日</option></select>
      <select name="device"><option value="all">すべて</option></select>
      <div class="cocoon-analytics-post-picker-field"><label for="picker-trigger">クリック元記事:</label>
      <div class="cocoon-map-picker" data-placeholder="アクセスの多い記事から選ぶ">
      <button type="button" id="picker-trigger" class="cocoon-map-picker-trigger" aria-haspopup="dialog" aria-expanded="false"><span class="cocoon-map-picker-value">アクセスの多い記事から選ぶ</span><span>▾</span></button>
      <input type="hidden" name="source_post_id" value="0"><button type="button" class="cocoon-map-picker-clear" hidden>×</button>
      <div class="cocoon-map-picker-panel" role="dialog" aria-label="記事の選択" tabindex="-1" hidden>
      <div class="cocoon-map-picker-heading"><strong class="cocoon-map-picker-heading-text"></strong><button type="button" class="cocoon-map-picker-close" aria-label="閉じる">×</button></div>
      <input type="search" aria-label="ほかの記事を検索"><p class="cocoon-map-picker-context"></p><ol class="cocoon-map-picker-list"></ol>
      <p class="cocoon-map-picker-status" role="status"></p><button type="button" class="cocoon-map-picker-retry" hidden>再試行</button>
      </div></div></div><button type="submit">表示</button></form><div id="map">クリックマップ</div>
      <script>window.CocoonAnalytics={ajax:{url:'https://picker.test/admin-ajax.php',post_picker_nonce:'test'}};</script><script>${script}</script></body></html>`}));
    await page.goto('https://picker.test/dashboard');
    const trigger = page.locator('.cocoon-map-picker-trigger');
    const mapBefore = await page.locator('#map').boundingBox();
    await trigger.click();
    const panel = page.getByRole('dialog');
    await expect(page.locator('.cocoon-map-picker-item')).toHaveCount(10);
    await expect(panel).toBeFocused();
    const bounds = await panel.boundingBox();
    expect(bounds.x).toBeGreaterThanOrEqual(0);
    expect(bounds.x + bounds.width).toBeLessThanOrEqual(viewport.width);
    expect(bounds.y).toBeGreaterThanOrEqual(0);
    expect(bounds.y + bounds.height).toBeLessThanOrEqual(viewport.height);
    expect((await page.locator('#map').boundingBox()).y).toBe(mapBefore.y);
    expect(await page.locator('.cocoon-map-picker-list').evaluate(list => list.scrollHeight > list.clientHeight)).toBe(true);
    await panel.press('ArrowDown');
    await expect(page.locator('.cocoon-map-picker-item').first()).toBeFocused();
    await page.locator('.cocoon-map-picker-item').first().press('Enter');
    await expect(panel).toBeHidden();
    await expect(trigger).toBeFocused();
    await expect(page.locator('[name="source_post_id"]')).toHaveValue('1');
    await trigger.click();
    await page.getByRole('searchbox').fill('ほかの記事');
    await page.getByRole('searchbox').press('Escape');
    await expect(panel).toBeHidden();
    await expect(page.locator('[name="source_post_id"]')).toHaveValue('1');
    await trigger.click();
    await panel.press('Shift+Tab');
    await expect(page.locator('.cocoon-map-picker-clear')).toBeFocused();
    await page.locator('.cocoon-map-picker-clear').press('Escape');
    await expect(panel).toBeHidden();
    await expect(trigger).toBeFocused();
  });
}
