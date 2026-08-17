const path = require('path');
const {test, expect} = require('@playwright/test');

const collectorPath = path.resolve(__dirname, '../../js/click-analytics.js');

function testHtml() {
  return `<!doctype html><html><body>
    <main class="entry-content">
      <h2>リンク見出し</h2>
      <a id="anchor" href="#target">アンカー</a>
      <a id="internal" href="https://analytics.test/next" target="_blank">内部リンク</a>
      <a id="external" href="https://outside.test/offer" target="_blank">外部リンク</a>
      <a id="download" href="https://outside.test/report.pdf" target="_blank" download>資料</a>
      <a id="mail" href="mailto:test@example.com">メール</a>
      <a id="tel" href="tel:0312345678">電話</a>
      <div class="wp-block-button"><a id="button" href="https://outside.test/button" target="_blank">ボタン</a></div>
      <div class="external-blogcard-wrap"><a id="blogcard" href="https://outside.test/card" target="_blank"><img alt="カード画像"></a></div>
      <blockquote><a id="reference" href="https://example.com/source" target="_blank">引用元</a></blockquote>
      <div id="target" style="margin-top:20px">到着点</div>
    </main>
  </body></html>`;
}

async function setupCollector(page, context, overrides = {}) {
  const payloads = [];
  await context.route('https://analytics.test/**', async (route) => {
    const request = route.request();
    if (new URL(request.url()).pathname === '/wp-json/cocoon/v1/click-events') {
      payloads.push(JSON.parse(request.postData() || '{}'));
      await route.fulfill({status: 204, body: ''});
      return;
    }
    await route.fulfill({status: 200, contentType: 'text/html', body: testHtml()});
  });
  await context.route('https://outside.test/**', (route) => route.fulfill({status: 200, contentType: 'text/html', body: '<p>outside</p>'}));
  await page.addInitScript((options) => {
    Math.random = () => 0;
    Object.defineProperty(navigator, 'sendBeacon', {configurable: true, value: () => false});
    window.CocoonClickAnalyticsConfig = Object.assign({
      endpoint: 'https://analytics.test/wp-json/cocoon/v1/click-events',
      sourcePostId: 10,
      layoutRevision: 'a'.repeat(64),
      samplingRate: 100,
      token: 'b'.repeat(64),
      siteHosts: ['analytics.test'],
      trackInternal: true,
      trackExternal: true,
      trackSpecial: true,
      impressions: false,
      heatmap: true,
      outcomes: false,
      respectPrivacy: true,
      initialConsent: true
    }, options);
  }, overrides);
  await page.goto('https://analytics.test/article');
  await page.addScriptTag({path: collectorPath});
  return payloads;
}

function events(payloads) {
  return payloads.flatMap((payload) => payload.events || []);
}

test('通常・修飾・中クリックと各リンク形式を非同期送信する', async ({page, context}) => {
  const payloads = await setupCollector(page, context);
  await page.click('#anchor');
  await expect(page).toHaveURL(/#target$/);
  await page.click('#internal', {modifiers: ['Control']});
  await page.click('#external', {button: 'middle'});
  await page.click('#download', {noWaitAfter: true});
  await page.click('#button');
  await page.click('#blogcard');
  await page.evaluate(() => {
    const delayed = document.createElement('a');
    delayed.id = 'delayed';
    delayed.href = 'https://outside.test/delayed';
    delayed.target = '_blank';
    delayed.textContent = '遅延リンク';
    document.body.appendChild(delayed);
  });
  await page.click('#delayed');
  await page.click('#mail', {noWaitAfter: true});
  await page.click('#tel', {noWaitAfter: true});

  await expect.poll(() => events(payloads).filter((event) => event.type === 'click').map((event) => event.href)).toEqual([
    '#target',
    'https://analytics.test/next',
    'https://outside.test/offer',
    'https://outside.test/report.pdf',
    'https://outside.test/button',
    'https://outside.test/card',
    'https://outside.test/delayed',
    'mailto:test@example.com',
    'tel:0312345678'
  ]);
  const clicks = events(payloads).filter((event) => event.type === 'click');
  expect(clicks.find((event) => event.href.includes('/button')).element_type).toBe('button');
  expect(clicks.find((event) => event.href.includes('/card')).element_type).toBe('blogcard');
  expect(clicks.every((event) => Number.isInteger(event.x_bp) && Number.isInteger(event.y_bp))).toBeTruthy();
});

test('未信頼イベントと2秒以内の連打を除外する', async ({page, context}) => {
  const payloads = await setupCollector(page, context);
  await page.evaluate(() => document.querySelector('#anchor').dispatchEvent(new MouseEvent('click', {bubbles: true})));
  await page.click('#anchor');
  await page.click('#anchor');
  await expect.poll(() => events(payloads).filter((event) => event.type === 'click').length).toBe(1);
});

test('50%以上を1秒表示したリンクにインプレッションを付ける', async ({page, context}) => {
  const payloads = await setupCollector(page, context, {impressions: true, heatmap: false});
  await page.waitForTimeout(1300);
  await page.evaluate(() => window.dispatchEvent(new Event('pagehide')));
  await expect.poll(() => events(payloads).filter((event) => event.type === 'impression').length).toBeGreaterThan(0);
  expect(events(payloads).some((event) => event.type === 'page_sample')).toBeTruthy();
});

test('初期同意がない場合はsetConsent後だけ計測する', async ({page, context}) => {
  const payloads = await setupCollector(page, context, {initialConsent: false});
  await page.click('#anchor');
  await page.waitForTimeout(100);
  expect(payloads).toHaveLength(0);
  await page.evaluate(() => window.CocoonClickAnalytics.setConsent(true));
  await page.click('#external');
  await expect.poll(() => events(payloads).filter((event) => event.type === 'click').length).toBe(1);
});

test('計測無効相当でもリンク遷移は壊れない', async ({page, context}) => {
  await context.route('https://analytics.test/**', (route) => route.fulfill({status: 200, contentType: 'text/html', body: testHtml()}));
  await page.goto('https://analytics.test/article');
  await page.click('#anchor');
  await expect(page).toHaveURL(/#target$/);
});
