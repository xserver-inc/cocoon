const fs = require('fs');
const path = require('path');
const {test, expect} = require('@playwright/test');

const script = fs.readFileSync(path.resolve(__dirname, '../../lib/page-access/analytics/assets/click-stat-tips.js'), 'utf8');
const style = fs.readFileSync(path.resolve(__dirname, '../../lib/page-access/analytics/assets/analytics.css'), 'utf8');
const rows = [1, 2].map(index => `<tr><td>リンク ${index}</td><td class="cocoon-click-number"><span>75.0%</span><small>データ不足</small>
  <details class="cocoon-click-stat-details"><summary aria-label="リンク ${index} の詳細">詳細</summary>
  <div class="cocoon-click-stat-content"><div class="cocoon-click-stat-header"><h3 class="cocoon-click-stat-title">推定CTRの詳細</h3>
  <button class="cocoon-click-stat-close" type="button" aria-label="閉じる" hidden>×</button></div>
  <p class="cocoon-click-stat-link">リンク ${index} ${'非常に長いリンクタイトル'.repeat(18)}</p>
  <dl class="cocoon-click-stat-metrics"><div><dt>推定CTR</dt><dd class="cocoon-click-stat-rate">75.0%</dd></div>
  <div><dt>95%信頼区間</dt><dd>56.6% 〜 87.3%</dd></div><div><dt>有効標本数（n）</dt><dd>28.0<small>比較の目安: 100以上</small></dd></div>
  <div><dt>サンプルクリック</dt><dd>21<small>比較の目安: 10以上</small></dd></div></dl>
  <p class="cocoon-click-stat-note"><strong>データ不足</strong>両方の目安を満たすまでは、参考値としてご覧ください。</p>
  <div class="cocoon-click-stat-explanation"><p>抽出率を補正した表示回数とクリック数から算出した推定値です。</p>
  <p>信頼区間は推定CTRの不確かさを示します。幅が広いほど、比較は慎重に行ってください。</p>
  <p>有効標本数（n）は、抽出率の違いを考慮した標本数の目安です。</p></div></div></details></td></tr>`).join('');

for (const viewport of [{width: 1280, height: 800}, {width: 390, height: 844}, {width: 320, height: 568}]) {
  test(`推定CTRのTips表示と開閉 ${viewport.width}x${viewport.height}`, async ({page}) => {
    test.setTimeout(60000);
    await page.setViewportSize(viewport);
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    await page.route('https://tips.test/dashboard', route => route.fulfill({contentType: 'text/html', body: `<!doctype html><html lang="ja"><head>
      <meta charset="utf-8"><meta name="viewport" content="width=device-width"><style>${style}
      body {margin:16px;font:14px sans-serif} table {width:700px;table-layout:fixed} td:last-child {width:100px} small {display:block}
      </style></head><body><div class="cocoon-analytics-table-scroll"><table><tbody>${rows}</tbody></table></div>
      <button id="background">背景のボタン</button><script>${script}</script></body></html>`}));
    await page.goto('https://tips.test/dashboard');
    const trigger = page.locator('.cocoon-click-stat-trigger').first();
    const tableHeight = (await page.locator('table').boundingBox()).height;
    await trigger.click();
    const dialog = page.getByRole('dialog', {name: '推定CTRの詳細'});
    const close = dialog.getByRole('button', {name: '閉じる'});
    await expect(close).toBeFocused();
    await expect(dialog).toContainText('56.6% 〜 87.3%');
    const bounds = await dialog.boundingBox();
    expect(bounds.x).toBeGreaterThanOrEqual(0);
    expect(bounds.x + bounds.width).toBeLessThanOrEqual(viewport.width);
    expect(bounds.y).toBeGreaterThanOrEqual(0);
    expect(bounds.y + bounds.height).toBeLessThanOrEqual(viewport.height);
    expect(await dialog.evaluate(el => el.scrollWidth <= el.clientWidth)).toBe(true);
    expect((await page.locator('table').boundingBox()).height).toBe(tableHeight);
    await dialog.locator('h3').click();
    await expect(dialog).toBeVisible();
    await close.click();
    await expect(dialog).toBeHidden();
    await expect(trigger).toBeFocused();
    await expect(trigger).toHaveAttribute('aria-expanded', 'false');
    await trigger.press('Enter');
    await expect(dialog).toBeVisible();
    await page.keyboard.press('Tab');
    expect(await page.evaluate(() => document.querySelector('dialog').contains(document.activeElement))).toBe(true);
    await page.keyboard.press('Escape');
    await expect(dialog).toBeHidden();
    await expect(trigger).toBeFocused();
    await page.locator('.cocoon-click-stat-trigger').nth(1).click();
    await expect(dialog.locator('.cocoon-click-stat-link')).toContainText('リンク 2');
    await page.mouse.click(2, 2);
    await expect(dialog).toBeHidden();
    await expect(page.locator('.cocoon-click-stat-trigger').nth(1)).toBeFocused();
    expect(errors).toEqual([]);
  });
}
