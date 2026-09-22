const fs = require('fs');
const path = require('path');
const {test, expect} = require('@playwright/test');

test.describe.configure({mode: 'parallel'});

const root = path.resolve(__dirname, '../..');
const read = file => fs.readFileSync(path.join(root, file), 'utf8');
const themeCss = read('style.css') + '\n' + read('css/entry-content.css');
// 区切り線を上書きする全スキンの自動抽出による検証漏れの防止
const skinNames = ['', ...fs.readdirSync(path.join(root, 'skins')).filter(name => {
  const file = path.join(root, `skins/${name}/style.css`);
  return fs.existsSync(file) && fs.readFileSync(file, 'utf8').includes('.border-partition');
})];
const cardTypes = ['new', 'popular', 'related', 'navi', 'rss'];

// 実際のカードと横並び用ラッパーに合わせた、件数・表示形式ごとの検証用HTML
function fixture(type, count, mode) {
  const cards = Array.from({length: count}, (_, index) => `<a class="${type}-entry-card-link widget-entry-card-link a-wrap" href="#card-${index}"><div class="${type}-entry-card widget-entry-card e-card cf"><div class="${type}-entry-card-content widget-entry-card-content card-content"><div class="${type}-entry-card-title widget-entry-card-title card-title">記事 ${index + 1}</div></div></div></a>`).join('\n');
  const horizontal = mode === 'horizontal';
  const className = mode === 'square' ? 'border-square' : mode === 'default' ? '' : 'border-partition';
  return `<section class="widget_${type}_entries"><div data-case="${type}-${count}-${mode}" class="${type}-entry-cards widget-entry-cards no-icon cf ${className} ${horizontal ? 'is-list-horizontal swiper' : ''}">${horizontal ? `<div class="swiper-wrapper">${cards}</div><div class="swiper-button-prev"></div><div class="swiper-button-next"></div>` : cards}</div></section>`;
}

for (const skin of skinNames) {
  for (const width of [1280, 390]) {
    test(`カード間の区切り線 ${skin || '標準'} 幅${width}`, async ({page}) => {
      await page.setViewportSize({width, height: 900});
      // スキンの外部フォント・画像への通信を伴わない表示検証
      await page.route('**/*', route => route.abort());
      const cases = [];
      for (const type of cardTypes) {
        for (const count of [0, 1, 2, 5]) {
          for (const mode of ['partition', 'horizontal', 'square', 'default']) {
            cases.push(fixture(type, count, mode));
          }
        }
      }
      await page.setContent(`<!doctype html><html lang="ja"><head><meta charset="utf-8"><style>${themeCss}\n${skin ? read(`skins/${skin}/style.css`) : ''}</style></head><body class="body"><div class="content-in"><main id="main" class="main"><article class="entry-content">${cases.join('')}</article></main><aside id="sidebar" class="sidebar">${cases.join('')}</aside><aside id="slide-in-sidebar">${cases.join('')}</aside></div></body></html>`);
      const results = await page.locator('[data-case]').evaluateAll(lists => lists.map(list => ({
        id: `${list.closest('main, aside').id}/${list.dataset.case}`,
        mode: list.dataset.case.split('-').pop(),
        count: Number(list.dataset.case.split('-').at(-2)),
        cards: Array.from(list.querySelectorAll('.a-wrap'), card => {
          const css = getComputedStyle(card);
          const innerCss = getComputedStyle(card.querySelector('.e-card'));
          return {top: parseFloat(css.borderTopWidth), bottom: parseFloat(css.borderBottomWidth), innerTop: parseFloat(innerCss.borderTopWidth), innerBottom: parseFloat(innerCss.borderBottomWidth)};
        })
      })));
      // カード内部へ枠線を移すスキンを含む、外枠表示の維持確認
      const actual = results.map(result => ({
        id: result.id,
        cards: result.cards.map(card => ({
          top: (result.mode === 'square' ? card.top + card.innerTop : card.top) > 0,
          bottom: (result.mode === 'square' ? card.bottom + card.innerBottom : card.bottom) > 0
        }))
      }));
      const expected = results.map(result => ({
        id: result.id,
        cards: Array.from({length: result.count}, (_, index) => ({
          top: result.mode === 'square',
          bottom: result.mode === 'square' || (result.mode === 'partition' && index < result.count - 1)
        }))
      }));
      // 全件の一括比較による、判定ごとのトレース記録負荷の削減
      expect(actual).toEqual(expected);
    });
  }
}

test('新着情報も1件は線なし、複数件は項目間のみで外枠は維持', async ({page}) => {
  await page.route('**/*', route => route.abort());
  for (const count of [1, 2, 5]) {
    await page.setContent(`<style>${themeCss}</style><div class="info-list is-style-divider-line is-style-frame-border"><div class="info-list-caption">新着情報</div>${'<div class="info-list-item">記事</div>'.repeat(count)}</div>`);
    const borders = await page.locator('.info-list-item').evaluateAll(items => items.map(item => {
      const css = getComputedStyle(item);
      return {top: parseFloat(css.borderTopWidth), bottom: parseFloat(css.borderBottomWidth)};
    }));
    borders.forEach((border, index) => {
      expect(border.top).toBe(0);
      expect(border.bottom).toBe(index < count - 1 ? 1 : 0);
    });
    await expect(page.locator('.info-list')).toHaveCSS('border-top-width', '1px');
    await expect(page.locator('.info-list')).toHaveCSS('border-bottom-width', '1px');
  }
});

test('Dockerの実際のブロック描画とフロント用CSSで新着記事・新着情報を確認', async ({page}, testInfo) => {
  const base = process.env.COCOON_TEST_URL;
  test.skip(!base, 'Docker環境のCOCOON_TEST_URL指定時のみ実行');
  test.setTimeout(180000);
  // ローカル開発用ログイン情報の送信先制限
  expect(base).toMatch(/^http:\/\/(?:localhost|127\.0\.0\.1):\d+$/);
  const errors = [];
  page.on('pageerror', error => errors.push(error.message));
  await page.goto(`${base}/wp-login.php`);
  await page.locator('#user_login').fill('admin');
  await page.locator('#user_pass').fill('admin123');
  await Promise.all([page.waitForURL('**/wp-admin/**'), page.locator('#wp-submit').click()]);
  const nonceResponse = await page.request.get(`${base}/wp-admin/admin-ajax.php?action=rest-nonce`);
  expect(nonceResponse.ok()).toBe(true);
  const nonce = (await nonceResponse.text()).trim();
  const previews = [];
  for (const block of ['new-list', 'info-list']) {
    for (const count of [1, 5]) {
      const params = {context: 'edit', 'attributes[count]': String(count)};
      if (block === 'new-list') {
        params['attributes[type]'] = 'border_partition';
        // 先頭固定記事の追加による表示件数の増加防止
        params['attributes[sticky]'] = 'false';
      }
      const response = await page.request.get(`${base}/?rest_route=/wp/v2/block-renderer/cocoon-blocks/${block}`, {headers: {'X-WP-Nonce': nonce}, params});
      expect(response.ok()).toBe(true);
      previews.push({block, count, html: (await response.json()).rendered});
    }
  }
  // 記事や設定を保存せず、実際のフロント画面内だけにプレビューを配置
  await page.goto(base);
  // 通常ページの読み込み時に発生する既存エラーと、検証操作による追加エラーの区別
  const initialErrors = errors.slice();
  if (initialErrors.length) {
    await testInfo.attach('読み込み時のJavaScriptエラー', {body: Buffer.from(JSON.stringify(initialErrors)), contentType: 'application/json'});
  }
  await page.locator('#main').evaluate((main, entries) => {
    main.innerHTML = `<article id="border-check" class="entry-content">${entries.map(({block, count, html}) => `<section data-preview="${block}-${count}"><h2>${block === 'new-list' ? '新着記事' : '新着情報'}（上限${count}件）</h2>${html}</section>`).join('')}</article>`;
  }, previews);
  for (const width of [1280, 390]) {
    await page.setViewportSize({width, height: 900});
    for (const {block, count} of previews) {
      const items = page.locator(`[data-preview="${block}-${count}"] ${block === 'new-list' ? '.a-wrap' : '.info-list-item'}`);
      // 上限より記事が少ない場合を含む、実際の表示件数による線の判定
      const actualCount = await items.count();
      if (count === 1) {expect(actualCount).toBe(1);}
      else {expect(actualCount).toBeGreaterThan(1);}
      expect(actualCount).toBeLessThanOrEqual(count);
      const borders = await items.evaluateAll(elements => elements.map(element => {
        const css = getComputedStyle(element);
        return {top: parseFloat(css.borderTopWidth), bottom: parseFloat(css.borderBottomWidth)};
      }));
      borders.forEach((border, index) => {
        expect(border.top).toBe(0);
        if (index === actualCount - 1) {expect(border.bottom).toBe(0);}
        else {expect(border.bottom).toBeGreaterThan(0);}
      });
    }
    await page.locator('#border-check').screenshot({path: testInfo.outputPath(`wordpress-borders-${width}.png`)});
  }
  const previewImage = await page.request.get(`${base}/wp-content/themes/cocoon/images/widget-border-partition.svg`);
  expect(previewImage.ok()).toBe(true);
  expect(await previewImage.text()).toContain('M12 70H348 M12 140H348');
  expect(errors).toEqual(initialErrors);
});
