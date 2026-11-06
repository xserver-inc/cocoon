const {test, expect} = require('@playwright/test');
const path = require('path');

const baseUrl = process.env.COCOON_SEARCH_BASE_URL;
test.skip(!baseUrl, 'Dockerローカルの実機確認先をCOCOON_SEARCH_BASE_URLで指定した場合だけ実行');
test.use({trace: 'off', screenshot: 'only-on-failure', colorScheme: process.env.COCOON_SEARCH_COLOR_SCHEME || 'light'});
test.setTimeout(60000);

if (baseUrl) {
  const target = new URL(baseUrl);
  if (target.protocol !== 'http:' || !['localhost', '127.0.0.1'].includes(target.hostname) || !['8085', '8090'].includes(target.port) || target.pathname !== '/' || target.username || target.password) {
    throw new Error('実機確認先にはプロジェクトのDockerローカル環境を指定してください。');
  }
}

const pageErrors = new WeakMap();
test.beforeEach(async ({page}) => {
  const errors = [];
  pageErrors.set(page, errors);
  page.on('pageerror', error => errors.push(error.message));
});
test.afterEach(async ({page}) => {
  expect(pageErrors.get(page), 'WordPress実画面のJavaScriptエラー').toEqual([]);
});

// 実際のページでの余白の上限・重なり防止・検索ボタンの操作領域の照合
async function expectLiveLayout(input) {
  let layout;
  await expect(async () => {
    layout = await input.evaluate((element, skinLabel) => {
      const rect = element.getBoundingClientRect();
      const button = element.form.querySelector('.search-submit').getBoundingClientRect();
      const form = element.form.getBoundingClientRect();
      const style = getComputedStyle(element);
      return {
        gap: button.left - (rect.right - parseFloat(style.paddingRight) - parseFloat(style.borderRightWidth)),
        paddingRight: parseFloat(style.paddingRight),
        buttonWidth: button.width,
        buttonHeight: button.height,
        centerOffset: (button.top + button.bottom - form.top - form.bottom) / 2,
        skinLoaded: !skinLabel || Array.from(element.ownerDocument.querySelectorAll('link[rel="stylesheet"]')).some(link => link.href.includes(`/skins/${skinLabel}/style.css`))
      };
    }, process.env.COCOON_SEARCH_SKIN_LABEL || '');
    expect(layout.gap).toBeGreaterThanOrEqual(3.8);
    expect(layout.gap).toBeLessThanOrEqual(8);
    expect(layout.buttonWidth).toBeGreaterThanOrEqual(44);
    expect(layout.buttonHeight).toBeGreaterThanOrEqual(44);
    expect(Math.abs(layout.centerOffset)).toBeLessThan(0.1);
    expect(layout.skinLoaded, '選択したスキンの配布CSSの読み込み').toBe(true);
  }).toPass({timeout: 5000});
  test.info().annotations.push({type: 'search-layout', description: JSON.stringify(layout)});
}

async function saveSearchScreenshot(page, suffix) {
  if (!process.env.COCOON_SEARCH_SCREENSHOTS_DIR) return;
  const label = process.env.COCOON_SEARCH_SKIN_LABEL || 'current';
  const file = `${label}-${test.info().project.name}-${suffix}.png`;
  await page.screenshot({path: path.join(process.env.COCOON_SEARCH_SCREENSHOTS_DIR, file), animations: 'disabled'});
}

// 表示と位置の安定を待った、ブラウザー標準の消去ボタンへの実クリック
async function clearNativeInput(input, browserName) {
  await expect(input).toHaveAttribute('type', 'search');
  if (browserName === 'firefox') {
    await input.selectText();
    await input.press('Backspace');
    await expect(input).toHaveValue('');
    await expect(input).toBeFocused();
    return;
  }
  const position = await input.evaluate(element => {
    const style = getComputedStyle(element);
    const rect = element.getBoundingClientRect();
    return {
      x: rect.width - parseFloat(style.paddingRight) - parseFloat(style.borderRightWidth) - parseFloat(style.fontSize) / 2,
      y: rect.height / 2
    };
  });
  await input.click({position});
  await expect(input).toHaveValue('');
  await expect(input).toBeFocused();
}

for (const width of [1280, 390]) {
  test(`WordPress実機の検索結果フォーム ${width}px`, async ({page, browserName}) => {
    await page.setViewportSize({width, height: 900});
    await page.goto(`${baseUrl}?s=Hello+world`, {waitUntil: 'domcontentloaded'});
    await page.evaluate(() => document.fonts.ready);
    const input = page.locator('main .search-edit');
    await expect(input).toHaveValue('Hello world');
    await input.fill('Cocoon');
    await expectLiveLayout(input);
    if (width === 1280) await saveSearchScreenshot(page, 'pc');
    await clearNativeInput(input, browserName);
    await input.fill('Cocoon');
    await Promise.all([
      page.waitForURL(url => url.searchParams.get('s') === 'Cocoon', {waitUntil: 'domcontentloaded', timeout: 30000}),
      page.locator('main .search-submit').click()
    ]);
    await expect(page).toHaveURL(/[?&]s=Cocoon(?:&|$)/);
    await expect(page.locator('#archive-title')).toContainText('Cocoon');
    await expect(page.locator('main .search-edit')).toHaveValue('Cocoon');
  });
}

test('WordPress実機のモバイル検索メニューの消去と検索送信', async ({page, browserName}) => {
  await page.setViewportSize({width: 390, height: 844});
  await page.goto(`${baseUrl}?s=Hello+world`, {waitUntil: 'domcontentloaded'});
  await page.evaluate(() => document.fonts.ready);
  await page.locator('#search-menu-open').click();
  await expect(page.locator('#search-menu-content')).toHaveCSS('opacity', '1');
  const input = page.locator('#search-menu-content .search-edit');
  await expect(input).toBeInViewport();
  await expect(input).toBeFocused();
  await input.fill('Cocoon');
  await expectLiveLayout(input);
  await saveSearchScreenshot(page, 'mobile');
  await clearNativeInput(input, browserName);
  await expect(page.locator('#search-menu-input')).toBeChecked();
  expect(new URL(page.url()).searchParams.get('s')).toBe('Hello world');
  // 閉じる操作後のフォーカス解除と、再表示時のフォーカス復元の照合
  await page.locator('#search-menu-close').click({position: {x: 10, y: 10}});
  await expect(page.locator('#search-menu-input')).not.toBeChecked();
  await expect(page.locator('#search-menu-content')).toHaveCSS('opacity', '0');
  await expect(input).not.toBeFocused();
  await page.locator('#search-menu-open').click();
  await expect(page.locator('#search-menu-content')).toHaveCSS('opacity', '1');
  await expect(input).toBeFocused();
  await expect(input).toHaveValue('');
  await input.fill('Cocoon');
  await Promise.all([
    page.waitForURL(url => url.searchParams.get('s') === 'Cocoon', {waitUntil: 'domcontentloaded', timeout: 30000}),
    page.locator('#search-menu-content .search-submit').click()
  ]);
  await expect(page).toHaveURL(/[?&]s=Cocoon(?:&|$)/);
  await expect(page.locator('#archive-title')).toContainText('Cocoon');
});

test('WordPress実機の管理画面モバイルプレビュー', async ({page, browserName}) => {
  // ログイン・設定画面・プレビューの複数遷移に対応した待機時間
  test.setTimeout(120000);
  test.skip(!process.env.COCOON_SEARCH_ADMIN_USER || !process.env.COCOON_SEARCH_ADMIN_PASSWORD, 'Docker管理画面の確認用認証情報の指定時だけ実行');
  await page.setViewportSize({width: 1280, height: 1000});
  await page.goto(`${baseUrl}wp-login.php`, {waitUntil: 'domcontentloaded'});
  await page.locator('#user_login').fill(process.env.COCOON_SEARCH_ADMIN_USER);
  await page.locator('#user_pass').fill(process.env.COCOON_SEARCH_ADMIN_PASSWORD);
  await page.locator('#wp-submit').click();
  await page.goto(`${baseUrl}wp-admin/admin.php?page=theme-settings#mobile-buttons`, {waitUntil: 'domcontentloaded'});
  // 表示モードに応じた、画面上のモバイル設定メニューの選択
  await page.locator('button[data-tab-target="tab-mobile-buttons-input"]:visible, #tab-mobile-buttons-label:visible').click();
  await page.locator('#mobile-demo').scrollIntoViewIfNeeded();
  const preview = page.frameLocator('#mobile-demo');
  const openButton = preview.locator('#search-menu-open');
  await openButton.scrollIntoViewIfNeeded();
  const parentScroll = await page.evaluate(() => window.scrollY);
  await openButton.click();
  await expect(preview.locator('#search-menu-content')).toHaveCSS('opacity', '1');
  const input = preview.locator('#search-menu-content .search-edit');
  await expect(input).toBeInViewport();
  await expect(input).toBeFocused();
  expect(Math.abs(await page.evaluate(() => window.scrollY) - parentScroll), '検索欄へのフォーカス時の親画面のスクロール防止').toBeLessThan(1);
  await input.fill('Cocoon');
  await expectLiveLayout(input);
  await saveSearchScreenshot(page, 'admin');
  await clearNativeInput(input, browserName);
  await input.fill('Hello world');
  const previewFrame = await (await page.locator('#mobile-demo').elementHandle()).contentFrame();
  // iframeの検索送信と、検索結果の文書への遷移完了の照合
  await Promise.all([
    previewFrame.waitForURL(url => url.searchParams.get('s') === 'Hello world', {waitUntil: 'domcontentloaded', timeout: 30000}),
    preview.locator('#search-menu-content .search-submit').click()
  ]);
  await expect(preview.locator('#archive-title')).toContainText('Hello world');
  await expect(preview.locator('main .search-edit')).toHaveValue('Hello world');
});
