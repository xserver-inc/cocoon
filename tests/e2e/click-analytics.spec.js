const path = require('path');
const {test, expect} = require('@playwright/test');

const collectorPath = path.resolve(__dirname, '../../js/click-analytics.js');

function testHtml() {
  return `<!doctype html><html><head><meta charset="utf-8"></head><body>
    <main class="entry-content">
      <h2>リンク見出し</h2>
      <a id="anchor" href="#target">アンカー</a>
      <a id="internal" href="https://analytics.test/next" target="_blank">内部リンク</a>
      <a id="external" href="https://outside.test/offer" target="_blank">外部リンク</a>
      <a id="download" href="https://outside.test/report.pdf" target="_blank" download>資料</a>
      <a id="mail" href="mailto:test@example.com">メール</a>
      <a id="tel" href="tel:0312345678">電話</a>
      <div class="wp-block-button"><a id="button" href="https://outside.test/button" target="_blank">ボタン</a></div>
      <div class="external-blogcard-wrap"><a id="blogcard" href="https://outside.test/card" target="_blank"><img alt="カード画像" src="https://analytics.test/placeholder.png" data-src="https://analytics.test/card-thumb.png?id=123&amp;w=320&amp;h=180"></a></div>
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
    if (options.omitInitialConsent) {delete window.CocoonClickAnalyticsConfig.initialConsent;}
    // 送信の検証では外部アプリや不要なタブを開かず、別タブのケースだけ実際に遷移します。
    if (!options.allowPopups) {
      const preventPopup = event => {
        const anchor = event.target.closest && event.target.closest('a');
        if (anchor && (anchor.target === '_blank' || /^(mailto|tel):/.test(anchor.getAttribute('href')))) {event.preventDefault();}
      };
      document.addEventListener('click', preventPopup);
      document.addEventListener('auxclick', preventPopup);
    }
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
  expect(clicks.find((event) => event.href.includes('/card')).image_url).toBe('https://analytics.test/card-thumb.png?id=123&w=320&h=180');
  expect(clicks.find((event) => event.href.includes('/card')).label).toBe('カード画像');
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

// 同意・掲載変更・送信タイミングの境界条件を実ブラウザーで確認します。
for (const initialConsent of [false, '', 'false', null]) {
  test('同意が未確定なら識別子も送信も作らない: ' + typeof initialConsent + ':' + String(initialConsent), async ({page, context}) => {
    const payloads = await setupCollector(page, context, initialConsent === null ? {omitInitialConsent: true, impressions: true} : {initialConsent, impressions: true});
    await page.click('#anchor');
    await page.waitForTimeout(1200);
    expect(payloads).toHaveLength(0);
    expect(await page.evaluate(() => Object.keys(sessionStorage).filter(key => key.startsWith('cocoon_click_analytics_')))).toEqual([]);
    await page.evaluate(() => window.CocoonClickAnalytics.setConsent(true));
    await page.click('#external');
    await expect.poll(() => events(payloads).filter(event => event.type === 'click').length).toBe(1);
    await page.evaluate(() => window.CocoonClickAnalytics.setConsent(false));
    expect(await page.evaluate(() => Object.keys(sessionStorage).filter(key => key.startsWith('cocoon_click_analytics_')))).toEqual([]);
  });
}

test('GPCが有効な場合は識別子を保存しない', async ({page, context}) => {
  await page.addInitScript(() => Object.defineProperty(navigator, 'globalPrivacyControl', {value: true}));
  const payloads = await setupCollector(page, context, {impressions: true});
  await page.evaluate(() => window.CocoonClickAnalytics.setConsent(true));
  await page.click('#anchor');
  expect(payloads).toHaveLength(0);
  expect(await page.evaluate(() => Object.keys(sessionStorage))).toEqual([]);
});

test('リンク先とラベルの変更をクリックに反映する', async ({page, context}) => {
  const payloads = await setupCollector(page, context, {impressions: true});
  await page.waitForTimeout(1200);
  await page.evaluate(() => {
    const anchor = document.querySelector('#external');
    anchor.href = 'https://outside.test/changed';
    anchor.textContent = '変更後';
  });
  await page.click('#external');
  await expect.poll(() => events(payloads).filter(event => event.type === 'click').map(event => [event.href, event.label])).toEqual([['https://outside.test/changed', '変更後']]);
});

test('同じ表示の繰り返しクリックはCTRの成功数を増やさない', async ({page, context}) => {
  const payloads = await setupCollector(page, context, {impressions: true});
  await page.waitForTimeout(1200);
  await page.click('#anchor');
  await page.waitForTimeout(2100);
  await page.click('#anchor');
  await expect.poll(() => events(payloads).filter(event => event.type === 'click').length).toBe(2);
  const clicks = events(payloads).filter(event => event.type === 'click');
  expect(clicks.filter(event => event.sampled)).toHaveLength(1);
  expect(events(payloads).filter(event => event.type === 'impression' && event.href === '#target')).toHaveLength(1);
});

test('ページを閉じなくても表示回数を定期送信する', async ({page, context}) => {
  const payloads = await setupCollector(page, context, {impressions: true});
  await expect.poll(() => events(payloads).filter(event => event.type === 'impression').length, {timeout: 8000}).toBeGreaterThan(0);
});

test('noopenerを維持した別タブで到着を一度だけ計測する', async ({page, context}) => {
  const payloads = await setupCollector(page, context, {outcomes: true, allowPopups: true});
  const config = await page.evaluate(() => window.CocoonClickAnalyticsConfig);
  await page.evaluate(() => document.querySelector('#internal').rel = 'noopener');
  const popupPromise = context.waitForEvent('page');
  await page.click('#internal');
  const popup = await popupPromise;
  await popup.waitForLoadState();
  await popup.evaluate(config => {window.CocoonClickAnalyticsConfig = config;}, {...config, sourcePostId: 20});
  await popup.addScriptTag({path: collectorPath});
  expect(await popup.evaluate(() => window.opener === null)).toBeTruthy();
  await expect.poll(() => popup.evaluate(() => Object.keys(localStorage).filter(key => key.includes('cross_tab_')).length)).toBe(0);
  await popup.evaluate(() => window.dispatchEvent(new Event('scroll')));
  await expect.poll(() => events(payloads).filter(event => event.type === 'internal_outcome').length).toBe(1);
  await popup.reload();
  await popup.evaluate(config => {window.CocoonClickAnalyticsConfig = config;}, {...config, sourcePostId: 20});
  await popup.addScriptTag({path: collectorPath});
  await popup.evaluate(() => window.dispatchEvent(new Event('scroll')));
  expect(events(payloads).filter(event => event.type === 'internal_outcome')).toHaveLength(1);
});

test('保存エラーを同じバッチIDで再送する', async ({page, context}) => {
  const payloads = await setupCollector(page, context);
  const attempts = [];
  await context.route('https://analytics.test/wp-json/cocoon/v1/click-events', async route => {
    attempts.push(JSON.parse(route.request().postData()));
    await route.fulfill({status: attempts.length === 1 ? 503 : 204, body: ''});
  });
  await page.click('#anchor');
  await expect.poll(() => attempts.length).toBe(2);
  expect(attempts[0]).toEqual(attempts[1]);
});

// 取り消し済みタイマーが遅れて発火しても、撤回前のデータを再送しません。
test('再同意後も撤回前の再送を復活させない', async ({page, context}) => {
  await setupCollector(page, context);
  const requests = [];
  await context.route('https://analytics.test/wp-json/cocoon/v1/click-events', async route => {
    requests.push(JSON.parse(route.request().postData()));
    await route.fulfill({status: requests.length === 1 ? 503 : 204, body: ''});
  });
  await page.evaluate(() => {
    const nativeTimeout = window.setTimeout.bind(window);
    window.setTimeout = (callback, delay, ...args) => {
      if (delay === 1000) {window.__cancelledRetry = () => callback(...args); return -100;}
      return nativeTimeout(callback, delay, ...args);
    };
  });
  await page.click('#anchor');
  await expect.poll(() => page.evaluate(() => Boolean(window.__cancelledRetry))).toBe(true);
  const before = requests[0];
  const session = await page.evaluate(() => {
    window.CocoonClickAnalytics.setConsent(false);
    const removed = sessionStorage.getItem('cocoon_click_analytics_session') === null;
    window.CocoonClickAnalytics.setConsent(true);
    window.__cancelledRetry();
    return {removed, current: sessionStorage.getItem('cocoon_click_analytics_session')};
  });
  expect(session.removed).toBe(true);
  expect(session.current).not.toBe(before.session_id);
  await page.click('#external');
  await expect.poll(() => requests.length).toBe(2);
  expect(requests[1].session_id).toBe(session.current);
  expect(requests[1].batch_id).not.toBe(before.batch_id);
});

async function setVisibility(page, hidden) {
  await page.evaluate(value => {
    window.__testHidden = value;
    document.dispatchEvent(new Event('visibilitychange'));
  }, hidden);
}

async function mockVisibility(page, hidden) {
  // 実際の非表示と同じ2つのプロパティを同期させて検証します。
  await page.addInitScript(value => {
    window.__testHidden = value;
    Object.defineProperty(document, 'hidden', {configurable: true, get: () => window.__testHidden});
    Object.defineProperty(document, 'visibilityState', {configurable: true, get: () => window.__testHidden ? 'hidden' : 'visible'});
  }, hidden);
}

test('背景で開いたページは表示を数えず、可視化後に数える', async ({page, context}) => {
  await mockVisibility(page, true);
  const payloads = await setupCollector(page, context, {impressions: true});
  await page.waitForTimeout(1400);
  await page.evaluate(() => window.dispatchEvent(new Event('pagehide')));
  expect(events(payloads).filter(event => event.type === 'impression')).toHaveLength(0);
  await setVisibility(page, false);
  await page.waitForTimeout(1400);
  await page.evaluate(() => window.dispatchEvent(new Event('pagehide')));
  await expect.poll(() => events(payloads).filter(event => event.type === 'impression').length).toBeGreaterThan(0);
});

test('表示待ちの途中で非表示になると1秒の判定をやり直す', async ({page, context}) => {
  await mockVisibility(page, false);
  const payloads = await setupCollector(page, context, {impressions: true});
  await page.waitForTimeout(350);
  await setVisibility(page, true);
  await page.waitForTimeout(1100);
  await page.evaluate(() => window.dispatchEvent(new Event('pagehide')));
  expect(events(payloads).filter(event => event.type === 'impression')).toHaveLength(0);
  await setVisibility(page, false);
  await page.waitForTimeout(1400);
  await page.evaluate(() => window.dispatchEvent(new Event('pagehide')));
  await expect.poll(() => events(payloads).filter(event => event.type === 'impression').length).toBeGreaterThan(0);
});

test('エンゲージは非表示時間を除いて可視時間を累積する', async ({page, context}) => {
  await mockVisibility(page, true);
  await page.addInitScript(() => {
    window.__visibleClock = Date.now();
    Date.now = () => window.__visibleClock;
    Object.defineProperty(performance, 'now', {configurable: true, value: () => window.__visibleClock});
    window.__outcomeTimers = [];
    const nativeTimeout = window.setTimeout.bind(window);
    window.setTimeout = (callback, delay, ...args) => {
      if (delay >= 5000) {window.__outcomeTimers.push({callback: () => callback(...args), delay}); return -window.__outcomeTimers.length;}
      return nativeTimeout(callback, delay, ...args);
    };
    sessionStorage.setItem('cocoon_click_analytics_pending_internal', JSON.stringify({expires:Date.now()+600000,target:'https://analytics.test/article',sourcePostId:9,layoutRevision:'a'.repeat(64),samplingRate:100,token:'b'.repeat(64),sessionId:'visible-source-session-001',device:'desktop',meta:{href:'https://analytics.test/article',kind:'internal',label:'内部リンク',area:'content',occurrence:0,element_type:'text'}}));
  });
  const payloads = await setupCollector(page, context, {outcomes: true});
  expect(await page.evaluate(() => window.__outcomeTimers.length)).toBe(0);
  await setVisibility(page, false);
  expect(await page.evaluate(() => window.__outcomeTimers.at(-1).delay)).toBe(10000);
  await page.evaluate(() => {window.__visibleClock += 4000;});
  await setVisibility(page, true);
  await page.evaluate(() => {window.__visibleClock += 20000; window.__outcomeTimers[0].callback();});
  expect(events(payloads).filter(event => event.type === 'internal_outcome')).toHaveLength(0);
  await setVisibility(page, false);
  expect(await page.evaluate(() => window.__outcomeTimers.at(-1).delay)).toBe(6000);
  await page.evaluate(() => {window.__visibleClock += 6000; window.__outcomeTimers.at(-1).callback();});
  await expect.poll(() => events(payloads).filter(event => event.type === 'internal_outcome').length).toBe(1);
  expect(events(payloads).find(event => event.type === 'internal_outcome').engaged).toBe(true);
});
