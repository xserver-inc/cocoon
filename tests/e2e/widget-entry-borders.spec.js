const fs = require('fs');
const path = require('path');
const postcss = require('postcss');
const {execFileSync} = require('child_process');
const {test, expect} = require('@playwright/test');

test.describe.configure({mode: 'parallel'});

const root = path.resolve(__dirname, '../..');
const read = file => fs.readFileSync(path.join(root, file), 'utf8');
const themeCss = read('style.css') + '\n' + read('css/entry-content.css');
// 実際のPHPと色変換関数による、未設定・濃色・淡色の動的CSSの取得
const oneStyles = JSON.parse(execFileSync('php', [path.join(root, 'tests/fixtures/one-skin-css.php')], {encoding: 'utf8'}));
const oneLineColors = {default: 'rgba(0, 0, 0, 0.3)', custom: 'rgba(18, 52, 86, 0.3)', 'light-text': 'rgba(244, 238, 221, 0.3)'};
const fuwariLineColors = {
  'skin-fuwari-ebicha': 'rgb(230, 199, 192)',
  'skin-fuwari-kachiiro': 'rgb(209, 209, 219)',
  'skin-fuwari-mirucha': 'rgb(214, 211, 206)',
  'skin-fuwari-omeshicha': 'rgb(205, 222, 224)'
};
const additionalSkinStyles = {
  'simple-darkmode': ['css/style.css'],
  'simple-darkmode-always': ['css/style.css']
};

// スキンのPHPから追加読み込みされる配布CSSを含む、実際の装飾の取得
function readSkinCss(skin) {
  return ['style.css', ...(additionalSkinStyles[skin] || [])].map(file => read(`skins/${skin}/${file}`)).join('\n');
}

// 表示形式を限定しないカードの線指定も含む、スキンの検証対象判定
function overridesWidgetBorders(css) {
  let matched = false;
  postcss.parse(css).walkRules(rule => {
    const targetsCards = rule.selectors.some(selector => selector.includes('.border-partition') || (
      selector.includes('.widget-entry-cards') && selector.includes('.a-wrap')
    ));
    const hasBorder = rule.nodes.some(node => node.type === 'decl' && /^border(?:-(?:top|right|bottom|left|block|inline|width|style|color)(?:-.+)?)?$/.test(node.prop));
    if (targetsCards && hasBorder) {matched = true;}
  });
  return matched;
}

// 既知の不具合スキンの固定登録と、関連する線指定の自動抽出の併用
const skinNames = [...new Set(['', 'one', ...Object.keys(fuwariLineColors), ...Object.keys(additionalSkinStyles), ...fs.readdirSync(path.join(root, 'skins')).filter(name => {
  const file = path.join(root, `skins/${name}/style.css`);
  return fs.existsSync(file) && overridesWidgetBorders(readSkinCss(name));
})])];
const cardTypes = ['new', 'popular', 'related', 'navi', 'rss'];
// 今回の変更対象外であるMix3色の、通常表示における既存の下線装飾
const defaultBorderSkins = ['skin-mixblue', 'skin-mixgreen', 'skin-mixred'];

test('カード枠線のスキン抽出で無条件の下線指定も検出', () => {
  expect(overridesWidgetBorders('.widget-entry-cards .a-wrap { border-bottom: 1px solid red; }')).toBe(true);
  expect(overridesWidgetBorders('.widget-entry-cards .a-wrap:last-of-type { border-bottom: none; }')).toBe(true);
  expect(overridesWidgetBorders('.border-partition .a-wrap { border-bottom: 0; }')).toBe(true);
  expect(overridesWidgetBorders('.widget-entry-cards .a-wrap { border-radius: 3px; }')).toBe(false);
  expect(overridesWidgetBorders('.unrelated { border: 0; }')).toBe(false);
  expect(overridesWidgetBorders('/* .border-partition { border: 0; } */')).toBe(false);
  expect(skinNames).toEqual(expect.arrayContaining(Object.keys(fuwariLineColors)));
  expect(skinNames).toEqual(expect.arrayContaining(Object.keys(additionalSkinStyles)));
  expect(skinNames).toContain('one');
});

// 実際のカードと横並び用ラッパーに合わせた、件数・表示形式ごとの検証用HTML
function fixture(type, count, mode) {
  const cards = Array.from({length: count}, (_, index) => `<a class="${type}-entry-card-link widget-entry-card-link a-wrap" href="#card-${index}"><div class="${type}-entry-card widget-entry-card e-card cf"><div class="${type}-entry-card-content widget-entry-card-content card-content"><div class="${type}-entry-card-title widget-entry-card-title card-title">記事 ${index + 1}</div></div></div></a>`).join('\n');
  const horizontal = mode === 'horizontal' || mode.endsWith('Horizontal');
  const className = mode.startsWith('square') ? 'border-square' : mode.startsWith('default') ? '' : 'border-partition';
  return `<section class="widget_${type}_entries"><div data-case="${type}-${count}-${mode}" class="${type}-entry-cards widget-entry-cards no-icon cf ${className} ${horizontal ? 'is-list-horizontal swiper' : ''}">${horizontal ? `<div class="swiper-wrapper">${cards}</div><div class="swiper-button-prev"></div><div class="swiper-button-next"></div>` : cards}</div></section>`;
}

for (const skin of skinNames) {
  const colorSchemes = additionalSkinStyles[skin] ? ['light', 'dark'] : ['light'];
  const variations = [1280, 390].flatMap(width => colorSchemes.flatMap(colorScheme => (skin === 'one' ? Object.keys(oneStyles) : ['default']).map(textColor => ({width, colorScheme, textColor}))));
  for (const {width, colorScheme, textColor} of variations) {
    test(`カード間の区切り線 ${skin || '標準'} 幅${width}${additionalSkinStyles[skin] ? ` ${colorScheme}` : ''}${skin === 'one' ? ` ${textColor}` : ''}`, async ({page}) => {
      await page.setViewportSize({width, height: 900});
      await page.emulateMedia({colorScheme});
      // スキンの外部フォント・画像への通信を伴わない表示検証
      await page.route('**/*', route => route.abort());
      const cases = [];
      for (const type of cardTypes) {
        for (const count of [0, 1, 2, 5]) {
          for (const mode of ['partition', 'horizontal', 'square', 'default', ...(skin === 'one' || fuwariLineColors[skin] || additionalSkinStyles[skin] ? ['squareHorizontal', 'defaultHorizontal'] : [])]) {
            cases.push(fixture(type, count, mode));
          }
        }
      }
      // 動的CSSの後付けによる色の遷移を避けた、初期描画時のスタイル適用
      const dynamicCss = skin === 'one' ? oneStyles[textColor] : '';
      const bodyClasses = skin === 'one' ? `is-shadow-on is-border-0 ${textColor === 'light-text' ? 'is-dark-on' : ''}` : '';
      await page.setContent(`<!doctype html><html lang="ja"><head><meta charset="utf-8"><style>${themeCss}\n${skin ? readSkinCss(skin) : ''}\n${dynamicCss}</style></head><body class="body ${bodyClasses}"><div class="content-in"><main id="main" class="main"><article class="entry-content">${cases.join('')}</article><div class="list"><a data-shadow-control class="a-wrap" href="#archive">通常の記事一覧</a></div></main><aside id="sidebar" class="sidebar">${cases.join('')}</aside><aside id="slide-in-sidebar">${cases.join('')}</aside></div></body></html>`);
      // スキンのウィジェット指定外に置いた参照要素による、親テーマの立体的な四辺の色の取得
      const frameColors = fuwariLineColors[skin] ? await page.evaluate(() => {
        const reference = document.createElement('div');
        reference.className = 'border-square';
        reference.innerHTML = '<a class="a-wrap"></a>';
        document.querySelector('#main .entry-content').appendChild(reference);
        const css = getComputedStyle(reference.firstElementChild);
        const colors = ['Top', 'Right', 'Bottom', 'Left'].map(side => css[`border${side}Color`]);
        reference.remove();
        return colors;
      }) : null;
      const results = await page.locator('[data-case]').evaluateAll(lists => lists.map(list => ({
        id: `${list.closest('main, aside').id}/${list.dataset.case}`,
        mode: list.dataset.case.split('-').pop(),
        count: Number(list.dataset.case.split('-').at(-2)),
        cards: Array.from(list.querySelectorAll('.a-wrap'), card => {
          const css = getComputedStyle(card);
          const innerCss = getComputedStyle(card.querySelector('.e-card'));
          return {
            top: parseFloat(css.borderTopWidth), bottom: parseFloat(css.borderBottomWidth),
            shadow: css.boxShadow,
            innerTop: parseFloat(innerCss.borderTopWidth), innerBottom: parseFloat(innerCss.borderBottomWidth),
            edges: ['Top', 'Right', 'Bottom', 'Left'].map(edge => ({
              width: parseFloat(css[`border${edge}Width`]), style: css[`border${edge}Style`], color: css[`border${edge}Color`]
            }))
          };
        })
      })));
      // カード内部へ枠線を移すスキンを含む、外枠表示の維持確認
      const actual = results.map(result => ({
        id: result.id,
        cards: result.cards.map(card => ({
          top: (result.mode.startsWith('square') ? card.top + card.innerTop : card.top) > 0,
          bottom: (result.mode.startsWith('square') ? card.bottom + card.innerBottom : card.bottom) > 0
        }))
      }));
      const expected = results.map(result => ({
        id: result.id,
        cards: Array.from({length: result.count}, (_, index) => ({
          top: result.mode.startsWith('square'),
          bottom: result.mode.startsWith('square') || (result.mode === 'partition' && index < result.count - 1) || (result.mode === 'default' && defaultBorderSkins.includes(skin))
        }))
      }));
      // 全件の一括比較による、判定ごとのトレース記録負荷の削減
      expect(actual).toEqual(expected);
      if (skin === 'one') {
        // 区切り線の幅・線種・不透明度と、外枠の四辺・他形式の線なし表示の照合
        const edges = results.map(result => ({id: result.id, cards: result.cards.map(card => card.edges)}));
        const expectedEdges = results.map(result => ({
          id: result.id,
          cards: result.cards.map((card, index) => card.edges.map((edge, side) => {
            const divider = result.mode === 'partition' && index < result.count - 1 && side === 2;
            const square = result.mode.startsWith('square');
            return {width: divider || square ? 1 : 0, style: divider ? 'dashed' : square ? 'solid' : 'none', color: divider || square ? oneLineColors[textColor] : edge.color};
          }))
        }));
        expect(edges).toEqual(expectedEdges);
        // ホバー・フォーカス時の線色の維持と、最後のカードの下線再発防止
        const cards = page.locator('#main [data-case="new-2-partition"] .a-wrap');
        for (const card of [cards.first(), cards.last()]) {
          await card.hover();
          await expect(card).toHaveCSS('border-bottom-color', oneLineColors[textColor]);
          await card.focus();
          await expect(card).toHaveCSS('border-bottom-color', oneLineColors[textColor]);
        }
        await expect(cards.first()).toHaveCSS('border-bottom-width', '1px');
        await expect(cards.first()).toHaveCSS('border-bottom-style', 'dashed');
        await expect(cards.last()).toHaveCSS('border-bottom-width', '0px');
      }
      if (fuwariLineColors[skin]) {
        // 最終カードの四辺の維持と、区切り線の太さ・線種・スキン固有色の照合
        const edges = results.map(result => ({id: result.id, cards: result.cards.map(card => card.edges)}));
        const expectedEdges = results.map(result => ({
          id: result.id,
          cards: result.cards.map((card, index) => card.edges.map((edge, side) => {
            const divider = result.mode === 'partition' && index < result.count - 1 && side === 2;
            const visible = result.mode.startsWith('square') || divider;
            return {width: visible ? 1 : 0, style: visible ? 'solid' : 'none', color: divider ? fuwariLineColors[skin] : result.mode.startsWith('square') ? frameColors[side] : edge.color};
          }))
        }));
        expect(edges).toEqual(expectedEdges);
      }
      if (additionalSkinStyles[skin]) {
        const normalShadow = 'rgba(0, 0, 0, 0.16) 0px 2px 2px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px';
        const hoverShadow = 'rgba(0, 0, 0, 0.24) 0px 0px 8px 0px';
        const hasDivider = mode => mode === 'partition' || mode === 'horizontal';
        // 0件・1件・複数件と全配置における、区切り線だけの影除去と他形式の影の維持
        expect(results.map(result => ({id: result.id, shadows: result.cards.map(card => card.shadow)}))).toEqual(results.map(result => ({
          id: result.id, shadows: Array(result.count).fill(hasDivider(result.mode) ? 'none' : normalShadow)
        })));
        const defaultCard = page.locator('#main [data-case="new-2-default"] .a-wrap').first();
        await expect(defaultCard).toHaveCSS('background-color', skin === 'simple-darkmode-always' || colorScheme === 'dark' ? 'rgb(45, 52, 58)' : 'rgb(255, 255, 255)');
        // ホバーとフォーカスによる影の再発防止、移動アニメーションの維持
        for (const mode of ['partition', 'horizontal', 'square', 'default', 'squareHorizontal', 'defaultHorizontal']) {
          const card = page.locator(`#main [data-case="new-2-${mode}"] .a-wrap`).first();
          await card.hover();
          await expect(card).toHaveCSS('box-shadow', hasDivider(mode) ? 'none' : hoverShadow);
          await expect(card).toHaveCSS('transform', 'matrix(1, 0, 0, 1, 0, -4)');
          await page.mouse.move(0, 0);
          await card.focus();
          await expect(card).toHaveCSS('box-shadow', hasDivider(mode) ? 'none' : normalShadow);
        }
        // ウィジェット以外の記事一覧の影とホバー装飾の維持
        const control = page.locator('[data-shadow-control]');
        await expect(control).toHaveCSS('box-shadow', normalShadow);
        await control.hover();
        await expect(control).toHaveCSS('box-shadow', hoverShadow);
      }
    });
  }
}

for (const width of [1280, 390]) {
  test(`新着情報の親要素追加後も区切り線・外枠・余白を維持 幅${width}`, async ({page}) => {
    await page.setViewportSize({width, height: 900});
    await page.route('**/*', route => route.abort());
    const cases = [];
    for (const count of [0, 1, 2, 5]) {
      for (const caption of [false, true]) {
        for (const frame of [false, true]) {
          for (const divider of [false, true]) {
            cases.push({count, caption, frame, divider});
          }
        }
      }
    }
    // 実際の項目内構造に合わせた新旧HTMLの比較用フィクスチャー
    const content = wrapped => `<style>${themeCss}</style>${cases.map(({count, caption, frame, divider}, id) => {
      const items = Array.from({length: count}, (_, index) => `<div class="info-list-item"><div class="info-list-item-content"><a class="info-list-item-content-link" href="#item-${index}">折り返しを含む新着情報の記事タイトル ${index + 1}</a></div><div class="info-list-item-meta"><span class="info-list-item-date">2026/09/22</span></div></div>`).join('');
      const children = count ? `${caption ? '<div class="info-list-caption">新着情報</div>' : ''}${wrapped ? `<div class="info-list-items">${items}</div>` : items}` : '<p class="info-list-empty-message">記事は見つかりませんでした。</p>';
      return `<div data-case="${id}" class="info-list${count ? `${frame ? ' is-style-frame-border' : ''}${divider ? ' is-style-divider-line' : ''}` : ' is-empty'}">${children}</div>`;
    }).join('')}`;
    const measure = () => page.locator('[data-case]').evaluateAll(lists => lists.map(list => {
      const box = list.getBoundingClientRect();
      const css = getComputedStyle(list);
      return {
        width: box.width, height: box.height,
        frame: [parseFloat(css.borderTopWidth), parseFloat(css.borderBottomWidth)],
        items: Array.from(list.querySelectorAll('.info-list-item'), item => {
          const rect = item.getBoundingClientRect();
          const style = getComputedStyle(item);
          return {x: rect.left - box.left, y: rect.top - box.top, width: rect.width, height: rect.height, top: parseFloat(style.borderTopWidth), bottom: parseFloat(style.borderBottomWidth)};
        })
      };
    }));
    await page.setContent(content(false));
    const before = await measure();
    await page.setContent(content(true));
    const after = await measure();
    // 一括比較による外観維持と、1件・複数件・空状態の区切り線仕様の検証
    expect(after).toEqual(before);
    expect(after.map(({frame, items}) => ({frame, borders: items.map(({top, bottom}) => ({top, bottom}))}))).toEqual(cases.map(({count, frame, divider}) => ({
      frame: [count && frame ? 1 : 0, count && frame ? 1 : 0],
      borders: Array.from({length: count}, (_, index) => ({top: 0, bottom: divider && index < count - 1 ? 1 : 0}))
    })));
    await expect(page.locator('.info-list > .info-list-items')).toHaveCount(cases.filter(({count}) => count > 0).length);
    await expect(page.locator('.info-list-items > .info-list-item')).toHaveCount(cases.reduce((total, {count}) => total + count, 0));
    await expect(page.locator('.info-list-items .info-list-caption')).toHaveCount(0);
  });
}

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
      if (block === 'info-list') {
        const list = page.locator(`[data-preview="${block}-${count}"] .info-list`);
        await expect(list.locator(':scope > .info-list-items')).toHaveCount(1);
        await expect(list.locator(':scope > .info-list-items > .info-list-item')).toHaveCount(actualCount);
        await expect(list.locator('.info-list-items .info-list-caption')).toHaveCount(0);
      }
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
