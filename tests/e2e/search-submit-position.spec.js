const fs = require('fs');
const path = require('path');
const {test, expect} = require('@playwright/test');

test.describe.configure({mode: 'parallel'});
// 配布CSSと埋め込みフォントの重複保存を避けるトレース記録の無効化
test.use({trace: 'off'});

const root = path.resolve(__dirname, '../..');
const read = file => fs.readFileSync(path.join(root, file), 'utf8');
const frontCss = read('style.css');
const adminCss = read('css/admin.css');
const iconCss = read('css/fontawesome.min.css');
const iconFont = fs.readFileSync(path.join(root, 'webfonts/fontawesome/fonts/fontawesome-webfont.woff2')).toString('base64');
const skins = [
  'hygiene', 'lightning-skin', 'maple-alice', 'maple-cheshire-cat',
  'maple-citrus', 'maple-rose', 'maple-unicorn', 'maple-white-rabbit',
  'mil-light', 'silk', 'simple-darkmode', 'simple-darkmode-always',
  'skin-innocence', 'skin-tegakinote-blue-coral', 'skin-tegakinote-dark-sky',
  'skin-tegakinote-green-orange', 'skin-tegakinote-pink-lemon', 'skin-tegakinote-white-banana'
];

// 実際の検索フォーム構造と、フォーム・ボタンの高さを変えた表示条件
function searchForms() {
  return [
    {id: 'default'},
    {id: 'large-icon', iconSize: 32},
    {id: 'tall-form', inputHeight: 72},
    {id: 'tall-form-large-icon', iconSize: 32, inputHeight: 72}
  ].map(({id, iconSize, inputHeight}) => `<form id="${id}" class="search-box input-box" method="get" action="https://search.example.test/" role="search">
    <input type="text" name="s" class="search-edit" aria-label="検索語" placeholder="サイト内を検索" ${inputHeight ? `style="height:${inputHeight}px"` : ''}>
    <button type="submit" class="search-submit" aria-label="検索" ${iconSize ? `style="font-size:${iconSize}px"` : ''}><span class="fa fa-search" aria-hidden="true"></span></button>
  </form>`).join('\n');
}

// 配布CSSと同梱アイコンフォントによる、外部通信のない表示検証
async function preparePage(page, {width, skin = '', admin = false}) {
  await page.setViewportSize({width, height: 900});
  await page.route('**/*', route => route.abort());
  const skinCss = skin ? read(`skins/${skin}/${skin.startsWith('simple-darkmode') ? 'css/' : ''}style.css`) : '';
  const forms = `<aside id="sidebar"><div class="widget widget_search">${searchForms()}</div></aside>`;
  await page.setContent(`<!doctype html><html lang="ja"><head><meta charset="utf-8">
    <style>${frontCss}\n${admin ? adminCss : ''}\n${skinCss}\n${iconCss}
    @font-face {font-family: FontAwesome; src: url(data:font/woff2;base64,${iconFont}) format('woff2'); font-weight: normal; font-style: normal;}
    </style></head><body class="body${admin ? ' wp-admin' : ''}">
    ${admin ? `<div class="admin-settings"><div class="demo">${searchForms()}</div></div>` : forms}
    </body></html>`);
  await page.evaluate(() => document.fonts.ready);
}

// 固定ピクセル値に依存しない、フォームと検索ボタンの中心座標の照合
async function expectCentered(page) {
  const results = await page.locator('form.search-box').evaluateAll(forms => forms.map(form => {
    const formRect = form.getBoundingClientRect();
    const buttonRect = form.querySelector('.search-submit').getBoundingClientRect();
    return {id: form.id, formHeight: formRect.height, buttonHeight: buttonRect.height, offset: (buttonRect.top + buttonRect.bottom - formRect.top - formRect.bottom) / 2};
  }));
  expect(results).toHaveLength(4);
  for (const result of results) {
    expect(result.formHeight, `${result.id}のフォームの表示`).toBeGreaterThan(0);
    expect(result.buttonHeight, `${result.id}の検索ボタンの表示`).toBeGreaterThan(0);
    expect(Math.abs(result.offset), `${result.id}の縦中央からのずれ`).toBeLessThan(0.1);
  }
}

for (const width of [1280, 390]) {
  for (const admin of [false, true]) {
    test(`検索ボタンの中央配置と検索送信 ${admin ? '管理画面プレビュー' : 'フロント'} ${width}px`, async ({page}) => {
      await preparePage(page, {width, admin});
      await expectCentered(page);
      const form = page.locator('#default');
      await form.getByRole('textbox', {name: '検索語'}).fill('Cocoon 検索');
      await expectCentered(page);
      // クリックによる実際の検索クエリ送信の確認用の応答
      await page.route('https://search.example.test/**', route => route.fulfill({contentType: 'text/html', body: '<title>検索結果</title>'}));
      await form.getByRole('button', {name: '検索', exact: true}).click();
      await expect(page).toHaveURL(/search\.example\.test\/\?s=/);
      expect(new URL(page.url()).searchParams.get('s')).toBe('Cocoon 検索');
    });
  }

  for (const skin of skins) {
    test(`標準スキンの検索ボタンの中央配置 ${skin} ${width}px`, async ({page}) => {
      await preparePage(page, {width, skin});
      await expectCentered(page);
      await page.locator('#default .search-edit').focus();
      await expectCentered(page);
    });
  }
}
