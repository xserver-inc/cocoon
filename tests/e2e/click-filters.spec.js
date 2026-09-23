const fs = require('fs');
const path = require('path');
const {execFile} = require('child_process');
const {promisify} = require('util');
const {test, expect} = require('@playwright/test');

const execFileAsync = promisify(execFile);
const root = path.resolve(__dirname, '../..');
const css = fs.readFileSync(path.join(root, 'lib/page-access/analytics/assets/analytics.css'), 'utf8');
const script = fs.readFileSync(path.join(root, 'lib/page-access/analytics/assets/click-filters.js'), 'utf8');
const suggestScript = fs.readFileSync(path.join(root, 'lib/page-access/analytics/assets/suggest.js'), 'utf8');
const mapScript = fs.readFileSync(path.join(root, 'lib/page-access/analytics/assets/map-post-picker.js'), 'utf8');

test.beforeEach(async ({page}, testInfo) => {
  await page.route('https://filters.test/admin-ajax.php**', route => route.fulfill({json: {success: true, data: {
    items: [{id: 84, name: '選択したページ', pv: '100', rank: 1, has_clicks: true}],
    from: '2026-09-01', to: '2026-09-23',
  }}}));
  // 本番フォームを使用した、送信内容と条件解除後の表示の検証
  await page.route('https://filters.test/admin.php**', async route => {
    // ブラウザー処理を妨げない非同期生成と、テスト全体に合わせたPHPの待機上限
    const {stdout: html} = await execFileAsync('php', [path.join(root, 'tests/fixtures/click-filters.php'), new URL(route.request().url()).search.slice(1)], {encoding: 'utf8', timeout: testInfo.timeout});
    return route.fulfill({contentType: 'text/html', body: `<!doctype html><html lang="ja"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><style>
      body{margin:16px;font:14px sans-serif} ${css}</style></head><body>${html}
      <script>window.CocoonAnalytics={ajax:{url:'https://filters.test/admin-ajax.php',post_picker_nonce:'test'}};</script>
      <script>${script}</script><script>${suggestScript}</script><script>${mapScript}</script></body></html>`});
  });
});

for (const width of [1280, 390, 320]) {
  test(`折りたたみ・入力条件の保持・集計切り替え 幅${width}`, async ({page}) => {
    await page.setViewportSize({width, height: 800});
    await page.goto('https://filters.test/admin.php?click_view=internal&period=all&group=occurrence&order=ctr&direction=asc&paged=3&source_post_id=42');
    await expect(page.locator('[name="device"]')).toBeHidden();
    await expect(page.locator('[name="link_type"]')).toHaveCount(0);
    await expect(page.locator('[name="from"]')).toBeHidden();
    await page.locator('summary').press('Enter');
    await expect(page.locator('[name="device"]')).toBeVisible();
    await page.locator('[name="device"]').selectOption('mobile');
    await page.locator('[name="area"]').selectOption('content');
    await page.getByRole('button', {name: 'リンク先ごと', exact: true}).click();
    const query = new URL(page.url()).searchParams;
    expect(query.getAll('group')).toEqual(['destination']);
    expect(query.get('device')).toBe('mobile');
    expect(query.get('area')).toBe('content');
    expect(query.get('source_post_id')).toBe('42');
    expect(query.get('order')).toBe('ctr');
    expect(query.get('direction')).toBe('asc');
    expect(query.has('paged')).toBe(false);
    expect(query.has('from')).toBe(false);
    await expect(page.getByRole('button', {name: 'リンク先ごと', exact: true})).toHaveAttribute('aria-pressed', 'true');
    await expect(page.locator('.cocoon-click-filter-chip')).toHaveCount(2);
    await expect(page.locator('[name="device"]')).toBeHidden();
    await page.locator('summary').press('Enter');
    await expect(page.locator('[name="device"]')).toHaveValue('mobile');
    await page.getByRole('button', {name: '結果を表示', exact: true}).click();
    expect(new URL(page.url()).searchParams.getAll('group')).toEqual(['destination']);
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
  });
}

test('条件の個別解除と一括解除で期間・記事・まとめ方を保持', async ({page}) => {
  await page.goto('https://filters.test/admin.php?click_view=external&period=custom&from=2026-08-01&to=2026-09-20&device=desktop&area=content&link_type=official&source_post_id=42&group=domain&order=unique&direction=asc&paged=5');
  await expect(page.locator('.cocoon-click-filter-chip')).toHaveCount(3);
  await page.getByRole('link', {name: '端末: PCを解除', exact: true}).click();
  let query = new URL(page.url()).searchParams;
  expect(query.get('device')).toBe('all');
  expect(query.get('link_type')).toBe('official');
  expect(query.has('paged')).toBe(false);
  await expect(page.locator('.cocoon-click-filter-chip')).toHaveCount(2);
  await page.getByRole('link', {name: '詳細条件をクリア', exact: true}).click();
  query = new URL(page.url()).searchParams;
  for (const key of ['device', 'area', 'link_type']) {expect(query.get(key)).toBe('all');}
  for (const [key, value] of Object.entries({period:'custom', from:'2026-08-01', to:'2026-09-20', source_post_id:'42', group:'domain', order:'unique', direction:'asc'})) {expect(query.get(key)).toBe(value);}
  await expect(page.locator('.cocoon-click-filter-chip')).toHaveCount(0);
});

test('カスタム期間を変更してまとめ方を切り替えても日付を保持', async ({page}) => {
  await page.goto('https://filters.test/admin.php?click_view=external&period=all');
  await page.locator('[name="period"]').selectOption('custom');
  await page.getByLabel('開始日', {exact: true}).fill('2026-08-01');
  await page.getByLabel('終了日', {exact: true}).fill('2026-08-31');
  await page.getByRole('button', {name: 'ドメインごと', exact: true}).click();
  expect(new URL(page.url()).searchParams.get('from')).toBe('2026-08-01');
  expect(new URL(page.url()).searchParams.get('to')).toBe('2026-08-31');
  await expect(page.getByLabel('開始日', {exact: true})).toBeVisible();
  await page.locator('summary').click();
  const types = await page.locator('[name="link_type"] option').evaluateAll(options => options.map(option => option.value));
  expect(types).not.toContain('internal');
  expect(types).not.toContain('anchor');
  expect(types).toContain('official');
});

test('概要は全種別を選択でき、マップは端末選択を常時表示', async ({page}) => {
  await page.goto('https://filters.test/admin.php?click_view=overview');
  expect(await page.locator('[name="link_type"] option').count()).toBe(12);
  await expect(page.locator('.cocoon-click-group-switcher')).toHaveCount(0);
  await page.goto('https://filters.test/admin.php?click_view=map');
  await expect(page.locator('[name="device"]')).toBeVisible();
  await expect(page.locator('details')).toHaveCount(0);
  await expect(page.locator('.cocoon-map-picker-trigger')).toBeVisible();
});

test('SMSリンクの絞り込み・集計切り替え・解除', async ({page}) => {
  await page.goto('https://filters.test/admin.php?click_view=external&period=all&link_type=sms&group=destination');
  await expect(page.getByRole('link', {name: 'リンク種別: SMSを解除', exact: true})).toBeVisible();
  await page.locator('summary').click();
  await expect(page.locator('[name="link_type"]')).toHaveValue('sms');
  await page.getByRole('button', {name: 'ドメインごと', exact: true}).click();
  expect(new URL(page.url()).searchParams.get('link_type')).toBe('sms');
  await page.getByRole('link', {name: 'リンク種別: SMSを解除', exact: true}).click();
  expect(new URL(page.url()).searchParams.get('link_type')).toBe('all');
  expect(new URL(page.url()).searchParams.get('group')).toBe('domain');
});

test('記事の未確定入力は送信を防ぎ、選択後のEnter送信では集計条件を保持', async ({page}) => {
  await page.goto('https://filters.test/admin.php?click_view=external&period=all&group=domain&order=unique&direction=asc');
  const input = page.getByRole('combobox', {name: 'クリック元ページ:', exact: true});
  await input.fill('選択');
  await expect(page.getByRole('option', {name: '選択したページ', exact: true})).toBeVisible();
  const initialUrl = page.url();
  await page.getByRole('button', {name: 'リンク先ごと', exact: true}).click();
  expect(page.url()).toBe(initialUrl);
  await expect(input).toBeFocused();
  await input.press('ArrowDown');
  await input.press('Enter');
  await expect(page.locator('[name="source_post_id"]')).toHaveValue('84');
  await input.press('Enter');
  const query = new URL(page.url()).searchParams;
  expect(query.getAll('group')).toEqual(['domain']);
  expect(query.get('source_post_id')).toBe('84');
  expect(query.get('order')).toBe('unique');
  expect(query.get('direction')).toBe('asc');
});

test('空のカスタム日付は送信を防ぎ、固定期間への変更後は送信可能', async ({page}) => {
  await page.goto('https://filters.test/admin.php?click_view=internal&period=custom&group=destination');
  const initialUrl = page.url();
  await page.getByLabel('開始日', {exact: true}).fill('');
  await page.getByRole('button', {name: '掲載箇所ごと', exact: true}).click();
  expect(page.url()).toBe(initialUrl);
  await expect(page.getByLabel('開始日', {exact: true})).toBeFocused();
  await page.locator('[name="period"]').selectOption('7days');
  await page.getByRole('button', {name: '掲載箇所ごと', exact: true}).click();
  const query = new URL(page.url()).searchParams;
  expect(query.get('period')).toBe('7days');
  expect(query.has('from')).toBe(false);
  expect(query.has('to')).toBe(false);
});

for (const view of ['overview', 'internal', 'external', 'map']) {
  test(`カスタム日付・詳細条件・記事候補が狭い画面に収まる ${view}`, async ({page}) => {
    await page.setViewportSize({width: 320, height: 640});
    await page.goto(`https://filters.test/admin.php?click_view=${view}&period=custom&device=mobile&area=mobile_menu&link_type=reference`);
    await expect(page.getByLabel('開始日', {exact: true})).toBeVisible();
    if (view === 'map') {
      await page.locator('.cocoon-map-picker-trigger').click();
      await expect(page.locator('.cocoon-map-picker-item')).toHaveCount(1);
      const panel = await page.getByRole('dialog').boundingBox();
      expect(panel.x).toBeGreaterThanOrEqual(0);
      expect(panel.x + panel.width).toBeLessThanOrEqual(320);
      await page.locator('.cocoon-map-picker-item').click();
      await expect(page.locator('[name="source_post_id"]')).toHaveValue('84');
    } else {
      await page.locator('summary').click();
      await expect(page.locator('[name="device"]')).toBeVisible();
      await page.getByRole('combobox', {name: 'クリック元ページ:', exact: true}).fill('選択');
      await expect(page.getByRole('option', {name: '選択したページ', exact: true})).toBeVisible();
    }
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
  });
}

test.describe('JavaScript無効', () => {
  test.use({javaScriptEnabled: false});
  test('条件入力と集計切り替えが利用可能', async ({page}) => {
    await page.goto('https://filters.test/admin.php?click_view=internal&period=custom');
    await page.locator('summary').click();
    await page.locator('[name="device"]').selectOption('tablet');
    await page.getByRole('button', {name: 'リンク先ごと', exact: true}).click();
    expect(new URL(page.url()).searchParams.get('device')).toBe('tablet');
    expect(new URL(page.url()).searchParams.get('group')).toBe('destination');
  });
});
