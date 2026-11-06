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
// 配布JavaScriptから取り出した、検索フォーム単独の初期化処理
const searchSpacingScript = read('javascript.js').match(/\( function cocoonSearchFormSpacing\(\) \{[\s\S]*?^\} \)\(\);/m)?.[0];
if (!searchSpacingScript) throw new Error('検索フォームの余白調整処理が見つかりません。');
// 選択可能な標準スキンの自動列挙と、分割された配布CSSの読み込み
const skins = fs.readdirSync(path.join(root, 'skins'), {withFileTypes: true})
  .filter(entry => entry.isDirectory() && !entry.name.startsWith('_'))
  .flatMap(entry => {
    const file = `skins/${entry.name}/style.css`;
    if (!fs.existsSync(path.join(root, file)) || !/(Skin )?Name:/i.test(read(file))) return [];
    let css = read(file).replace(/@import\s+(?:url\()?['"]?([^\s'";)]+)['"]?\)?\s*;/g, (rule, imported) => {
      const importedPath = path.join(path.dirname(file), imported);
      return !/^(https?:|\/\/)/.test(imported) && fs.existsSync(path.join(root, importedPath)) ? read(importedPath) : rule;
    });
    const separateStyle = `skins/${entry.name}/css/style.css`;
    if (fs.existsSync(path.join(root, separateStyle))) css += '\n' + read(separateStyle);
    return [{name: entry.name, css}];
  });

// 同じ装飾条件での旧入力欄と検索入力欄の比較用フォーム
function searchForms(type) {
  return [
    {id: 'default'},
    {id: 'large-icon', iconSize: 32},
    {id: 'tall-form', inputHeight: 72},
    {id: 'tall-form-large-icon', iconSize: 32, inputHeight: 72}
  ].map(({id, iconSize, inputHeight}) => `<form id="${type}-${id}" class="search-box input-box" method="get" action="https://search.example.test/" role="search">
    <input type="${type}" name="s" class="search-edit" aria-label="検索語" placeholder="サイト内を検索" ${inputHeight ? `style="height:${inputHeight}px"` : ''}>
    <button type="submit" class="search-submit" aria-label="検索" ${iconSize ? `style="font-size:${iconSize}px"` : ''}><span class="fa fa-search" aria-hidden="true"></span></button>
  </form>`).join('\n');
}

// スキン固有のbodyクラスを含む、外部通信のない配布CSSの検証
async function preparePage(page, {width, skin, admin = false}) {
  await page.setViewportSize({width, height: 900});
  await page.route('**/*', route => route.abort());
  // 既存のWordPress検索ブロックへの装飾波及を確認する比較用入力欄
  const blockControl = skin?.name.startsWith('bizarre-foods-') ? '<form class="wp-block-search"><input id="wordpress-search-control" type="search" class="wp-block-search__input"></form>' : '';
  const forms = searchForms('text') + searchForms('search') + blockControl;
  // 登場演出後の表示状態での、検索フォーム単体の検証
  const sidebarClass = skin?.name === 'raku-add-fadein' ? 'sidebar raku_fadeIn' : 'sidebar';
  const widget = `<div id="sidebar" class="${sidebarClass}"><aside class="widget widget_search">${forms}</aside></div>`;
  // サイドバー非表示のスキンでの、検索結果本文の表示状態
  const skinContent = skin?.name === 'skin-metal' ? `<main id="main" class="main">${forms}</main>` : widget;
  // 画面内に収めた検索フォーム検証用の外枠
  const content = admin
    ? `<div class="admin-settings"><div class="demo">${forms}</div></div>`
    : `<div id="container"><div id="content"><div id="content-in" class="content-in wrap" style="max-width:100%">${skinContent}</div></div></div>`;
  const skinClass = skin?.name === 'skin-grayish-topfull' ? 'skin-grayish' : '';
  await page.setContent(`<!doctype html><html lang="ja"><head><meta charset="utf-8">
    <style>${frontCss}\n${admin ? adminCss : ''}\n${skin?.css || ''}\n${iconCss}
    @font-face {font-family: FontAwesome; src: url(data:font/woff2;base64,${iconFont}) format('woff2'); font-weight: normal; font-style: normal;}
    *, *::before, *::after {animation-duration: 0s !important; transition-duration: 0s !important;}
    </style></head><body class="body public-page ${skinClass}${admin ? ' wp-admin' : ''}">${content}</body></html>`, {waitUntil: 'domcontentloaded'});
  await page.evaluate(() => document.fonts.ready);
  await page.addScriptTag({content: searchSpacingScript});
  await expect.poll(() => page.locator('#search-default input').evaluate(element => element.style.getPropertyValue('--cocoon-search-input-padding'))).not.toBe('');
}

// 幅・高さ・装飾の維持と、検索ボタンの中心座標の照合
async function expectLayout(page) {
  const results = await page.locator('input[type="search"].search-edit').evaluateAll(inputs => inputs.map(input => {
    const form = input.form;
    const previous = document.getElementById(form.id.replace(/^search-/, 'text-')).querySelector('input');
    const rect = input.getBoundingClientRect();
    const previousRect = previous.getBoundingClientRect();
    const formRect = form.getBoundingClientRect();
    const buttonRect = form.querySelector('button').getBoundingClientRect();
    const style = getComputedStyle(input);
    const previousStyle = getComputedStyle(previous);
    const properties = ['fontSize', 'borderTopLeftRadius', 'borderTopRightRadius', 'borderBottomLeftRadius', 'borderBottomRightRadius', 'borderTopWidth', 'borderBottomWidth', 'borderTopColor', 'backgroundColor'];
    return {
      id: form.id, width: rect.width, previousWidth: previousRect.width,
      height: rect.height, previousHeight: previousRect.height,
      buttonWidth: buttonRect.width, buttonHeight: buttonRect.height,
      clearButtonGap: buttonRect.left - (rect.right - parseFloat(style.paddingRight) - parseFloat(style.borderRightWidth)),
      offset: (buttonRect.top + buttonRect.bottom - formRect.top - formRect.bottom) / 2,
      styles: Object.fromEntries(properties.map(property => [property, style[property]])),
      previousStyles: Object.fromEntries(properties.map(property => [property, previousStyle[property]]))
    };
  }));
  expect(results).toHaveLength(4);
  for (const result of results) {
    expect(result.width, `${result.id}の入力欄の表示`).toBeGreaterThan(0);
    expect(Math.abs(result.width - result.previousWidth), `${result.id}の入力欄の幅の維持`).toBeLessThan(0.1);
    expect(Math.abs(result.height - result.previousHeight), `${result.id}の入力欄の高さの維持`).toBeLessThan(0.1);
    expect(result.styles, `${result.id}のスキンの装飾の維持`).toEqual(result.previousStyles);
    expect(result.clearButtonGap, `${result.id}の消去ボタンと検索ボタンの重なりの防止`).toBeGreaterThanOrEqual(3.9);
    expect(result.clearButtonGap, `${result.id}の消去ボタンと検索ボタンの間隔の上限`).toBeLessThanOrEqual(8);
    expect(result.buttonWidth, `${result.id}の検索ボタンの操作幅`).toBeGreaterThanOrEqual(44);
    expect(result.buttonHeight, `${result.id}の検索ボタンの操作高さ`).toBeGreaterThanOrEqual(44);
    expect(Math.abs(result.offset), `${result.id}の縦中央からのずれ`).toBeLessThan(0.1);
  }
}

// 標準消去ボタンの実クリックと、非表示の環境での通常の入力編集
async function expectClearAndSubmit(page, browserName) {
  const form = page.locator('#search-default');
  const input = form.getByRole('searchbox', {name: '検索語'});
  const previousInput = page.locator('#text-default input');
  const appearance = element => {
    const style = getComputedStyle(element);
    const rect = element.getBoundingClientRect();
    return {width: rect.width, height: rect.height, fontSize: style.fontSize, borderRadius: style.borderRadius, borderTopColor: style.borderTopColor, backgroundColor: style.backgroundColor};
  };
  // 同じ入力値・フォーカス状態での、従来の装飾との比較
  await previousInput.fill('検索語の消去');
  const previousAppearance = await previousInput.evaluate(appearance);
  await input.fill('検索語の消去');
  const currentAppearance = await input.evaluate(appearance);
  expect(currentAppearance, '入力フォーカス中の幅・高さ・スキン装飾の維持').toEqual(previousAppearance);

  if (browserName === 'firefox') {
    await input.selectText();
    await input.press('Backspace');
  } else {
    await input.scrollIntoViewIfNeeded();
    const position = await input.evaluate(element => {
      const rect = element.getBoundingClientRect();
      const style = getComputedStyle(element);
      return {
        x: rect.right - parseFloat(style.borderRightWidth) - parseFloat(style.paddingRight) - parseFloat(style.fontSize) / 2,
        y: (rect.top + rect.bottom) / 2
      };
    });
    await page.mouse.click(position.x, position.y);
  }

  await expect(input).toHaveValue('');
  await expect(input).toBeFocused();
  expect(page.url()).toBe('about:blank');
  await input.fill('Cocoon 検索 & "引用"');
  await page.route('https://search.example.test/**', route => route.fulfill({contentType: 'text/html', body: '<title>検索結果</title>'}));
  await form.getByRole('button', {name: '検索', exact: true}).click();
  await expect(page).toHaveURL(/search\.example\.test\/\?s=/);
  expect(new URL(page.url()).searchParams.get('s')).toBe('Cocoon 検索 & "引用"');
}

test('検索入力欄のリサイズと非表示からの再表示', async ({page}) => {
  await preparePage(page, {width: 1280, skin: skins.find(skin => skin.name === 'mil-light')});
  await page.setViewportSize({width: 390, height: 900});
  await expect(async () => expectLayout(page)).toPass({timeout: 5000});
  await page.locator('.search-box').evaluateAll(forms => forms.forEach(form => { form.style.display = 'none'; }));
  await page.locator('.search-submit').evaluateAll(buttons => buttons.forEach(button => { button.style.fontSize = '32px'; }));
  await page.locator('.search-box').evaluateAll(forms => forms.forEach(form => { form.style.display = ''; }));
  await expect(async () => expectLayout(page)).toPass({timeout: 5000});
});

for (const width of [1280, 390]) {
  for (const admin of [false, true]) {
    test(`検索入力欄の配置・消去・検索送信 ${admin ? '管理画面プレビュー' : 'フロント'} ${width}px`, async ({page, browserName}) => {
      await preparePage(page, {width, admin});
      await expectLayout(page);
      await expectClearAndSubmit(page, browserName);
    });
  }

  for (const skin of skins) {
    test(`標準スキンの検索入力欄 ${skin.name} ${width}px`, async ({page, browserName}) => {
      await preparePage(page, {width, skin});
      if (skin.name.startsWith('bizarre-foods-')) {
        const fonts = await page.locator('#wordpress-search-control').evaluate(element => ({actual: getComputedStyle(element).fontSize, inherited: getComputedStyle(element.parentElement).fontSize}));
        expect(fonts.actual, '既存のWordPress検索ブロックの文字サイズの維持').toBe(fonts.inherited);
      }
      await expectLayout(page);
      await expectClearAndSubmit(page, browserName);
    });
  }
}
