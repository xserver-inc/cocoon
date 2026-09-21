const fs = require('fs');
const path = require('path');
const {test, expect} = require('@playwright/test');

const script = path.resolve(__dirname, '../../lib/page-access/analytics/assets/click-previews.js');
const stylesheet = fs.readFileSync(path.resolve(__dirname, '../../lib/page-access/analytics/assets/analytics.css'), 'utf8');
const image = fs.readFileSync(path.resolve(__dirname, '../../images/no-image-160.png'));

async function openPreview(page, failFirst = false) {
  const requests = [];
  await page.route('https://images.test/**', async route => {
    requests.push({url: route.request().url(), headers: route.request().headers()});
    if (failFirst && requests.length === 1) {await route.abort(); return;}
    await route.fulfill({contentType: 'image/png', body: image});
  });
  await page.route('https://analytics.test/dashboard', route => route.fulfill({
    contentType: 'text/html',
    body: `<!doctype html><html><head><meta charset="utf-8"><style>${stylesheet}</style></head><body>
      <div class="cocoon-click-link-preview" style="width:340px">
        <span class="cocoon-click-image-consent">
          <button type="button" class="cocoon-click-load-image" data-image-url="https://images.test/image.php?id=123&amp;w=320">画像を読み込む</button>
          <small>読み込み先: images.test</small>
          <small class="cocoon-click-image-error" role="status" hidden>画像を読み込めませんでした。</small>
        </span>
        <strong class="cocoon-click-link-caption" tabindex="0">バナー</strong>
      </div>
    </body></html>`
  }));
  await page.goto('https://analytics.test/dashboard');
  await page.addScriptTag({path: script});
  return requests;
}

test('未確認画像は明示操作後だけ読み込み、クエリ・比率・キーボードフォーカスを維持', async ({page}) => {
  const requests = await openPreview(page);
  expect(requests).toHaveLength(0);
  await expect(page.locator('img')).toHaveCount(0);
  await page.getByRole('button', {name: '画像を読み込む'}).press('Enter');
  const thumbnail = page.locator('.cocoon-click-thumbnail');
  await expect(thumbnail).toBeVisible();
  expect(requests).toHaveLength(1);
  expect(requests[0].url).toBe('https://images.test/image.php?id=123&w=320');
  expect(requests[0].headers.referer).toBeUndefined();
  await expect(page.locator('.cocoon-click-link-caption')).toBeFocused();
  const dimensions = await thumbnail.evaluate(element => ({width: element.width, height: element.height, naturalWidth: element.naturalWidth, naturalHeight: element.naturalHeight}));
  expect(dimensions.width).toBeLessThanOrEqual(64);
  expect(dimensions.height).toBeLessThanOrEqual(48);
  expect(dimensions.width / dimensions.height).toBeCloseTo(dimensions.naturalWidth / dimensions.naturalHeight, 1);
});

test('画像の読み込み失敗時はテキストを維持し再試行が可能', async ({page}) => {
  const requests = await openPreview(page, true);
  const button = page.getByRole('button', {name: '画像を読み込む'});
  await button.click();
  await expect(page.getByRole('status')).toHaveText('画像を読み込めませんでした。');
  await expect(page.getByRole('status')).toBeVisible();
  await expect(button).toBeEnabled();
  await expect(page.locator('.cocoon-click-link-caption')).toHaveText('バナー');
  await button.click();
  await expect(page.locator('.cocoon-click-thumbnail')).toBeVisible();
  expect(requests).toHaveLength(2);
});
