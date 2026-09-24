const fs = require('fs');
const path = require('path');
const {execFileSync} = require('child_process');
const {test, expect} = require('@playwright/test');

test.describe.configure({mode: 'parallel'});

const root = path.resolve(__dirname, '../..');
const read = file => fs.readFileSync(path.join(root, file), 'utf8');
const css = read('style.css') + '\n' + read('css/entry-content.css') + '\n' + read('skins/one/style.css');
// WordPressの標準カレンダーブロックによる文字色・背景色指定の再現
const blockCss = '.wp-block-calendar :where(table:not(.has-text-color)){color:#40464d} :where(.wp-block-calendar table:not(.has-background) th){background:#ddd} .wp-block-calendar table.has-background th{background-color:inherit} .wp-block-calendar table.has-text-color th{color:inherit}';
const palettes = [
  {name: '未設定', text: '', background: '', expected: 'rgb(61, 61, 61)'},
  {name: '濃色文字', text: '#123456', background: '#fff8ee', expected: 'rgb(18, 52, 86)'},
  {name: '白文字', text: '#ffffff', background: '#000000', expected: 'rgb(255, 255, 255)'},
  {name: '淡色文字', text: '#f4eedd', background: '#18232d', expected: 'rgb(244, 238, 221)'}
].map(palette => ({...palette, css: JSON.parse(execFileSync('php', [path.join(root, 'tests/fixtures/one-skin-css.php'), palette.text, palette.background], {encoding: 'utf8'})).requested}));

// 複数カレンダーの接尾辞付き当日IDと、リンク有無の両方の検証用HTML
function calendar(id, custom = false) {
  return `<table class="wp-calendar-table ${custom ? 'has-text-color has-background' : ''}" ${custom ? 'style="color:#f0f0f0;background-color:#224466"' : ''}>
    <caption>2026年9月</caption><thead><tr><th scope="col">月</th><th scope="col">火</th><th scope="col">水</th><th scope="col">木</th></tr></thead>
    <tbody><tr><td class="ordinary-day">21</td><td id="${id}-today">22</td><td><a href="#archive">23</a></td><td id="${id}-linked-today"><a href="#archive">24</a></td></tr></tbody>
  </table>`;
}

function fixture(id) {
  return `<section data-case="${id}">
    <div class="widget widget_info_list"><div class="info-list is-style-frame-border"><div class="info-list-item"><div class="info-list-item-content"><a class="info-list-item-content-link" href="#post">新着情報の記事タイトル</a></div></div></div></div>
    <div class="widget widget_calendar"><div class="calendar_wrap">${calendar(id + '-classic')}</div></div>
    <div class="wp-block-calendar">${calendar(id + '-block')}</div>
    <div class="wp-block-calendar custom-calendar">${calendar(id + '-custom', true)}</div>
  </section>`;
}

test('ONE カレンダーブロックの文字色のみ・背景色のみ・両方の独自配色を維持', async ({page}) => {
  const palette = palettes.find(item => item.name === '白文字');
  const variants = [
    {name: 'background', classes: 'has-background', style: 'background-color:#ffffff', color: 'rgb(64, 70, 77)'},
    {name: 'text', classes: 'has-text-color', style: 'color:#123456', color: 'rgb(18, 52, 86)'},
    {name: 'both', classes: 'has-text-color has-background', style: 'color:#f0f0f0;background-color:#224466', color: 'rgb(240, 240, 240)'}
  ];
  const content = variants.map(item => `<div data-variant="${item.name}" class="wp-block-calendar">${calendar(item.name).replace('class="wp-calendar-table "', `class="wp-calendar-table ${item.classes}" style="${item.style}"`)}</div>`).join('');
  await page.setContent(`<html><head><style>${blockCss}\n${css}\n${palette.css}</style></head><body class="body is-dark-on"><div class="content">${content}</div></body></html>`);
  for (const item of variants) {
    const table = page.locator(`[data-variant="${item.name}"]`);
    await expect.soft(table.locator('.ordinary-day')).toHaveCSS('color', item.color);
    await expect.soft(table.locator('th').first()).toHaveCSS('color', item.color);
  }
});

for (const palette of palettes) {
  for (const dark of [false, true]) {
    for (const width of [1280, 390]) {
      test(`ONE ${palette.name} ダーク${dark ? 'ON' : 'OFF'} ${width}px`, async ({page}) => {
        await page.setViewportSize({width, height: 900});
        await page.setContent(`<!doctype html><html lang="ja"><head><meta charset="utf-8"><style>${blockCss}\n${css}\n${palette.css}</style></head>
          <body class="body public-page ${dark ? 'is-dark-on' : ''}"><div class="content"><main id="main" class="main"><article class="entry-content">${fixture('main')}</article></main><aside id="sidebar" class="sidebar">${fixture('sidebar')}</aside><aside id="slide-in-sidebar">${fixture('drawer')}</aside></div></body></html>`);
        const actual = await page.locator('[data-case]').evaluateAll(cases => cases.map(section => {
          const color = selector => Array.from(section.querySelectorAll(selector), element => getComputedStyle(element).color);
          return {
            title: color('.info-list-item-content-link'),
            ordinary: color('.wp-calendar-table:not(.has-text-color) .ordinary-day'),
            headers: color('.wp-calendar-table:not(.has-text-color) th'),
            today: color('[id$="today"]'),
            links: color('.wp-calendar-table td a'),
            custom: color('.custom-calendar .ordinary-day, .custom-calendar th'),
            backgrounds: Array.from(section.querySelectorAll('[id$="today"]'), element => getComputedStyle(element).backgroundColor)
          };
        }));
        for (const result of actual) {
          expect.soft(result.title).toEqual([palette.expected]);
          expect.soft(result.ordinary).toEqual(Array(2).fill(palette.expected));
          expect.soft(result.headers).toEqual(Array(8).fill('rgb(51, 51, 51)'));
          expect.soft(result.today).toEqual(Array(6).fill('rgb(51, 51, 51)'));
          expect.soft(result.links).toEqual(Array(6).fill('rgb(51, 51, 51)'));
          expect.soft(result.custom).toEqual(Array(5).fill('rgb(240, 240, 240)'));
          expect.soft(result.backgrounds).toEqual(Array(6).fill('rgb(255, 230, 178)'));
        }
        // ホバー・キーボードフォーカス時にも設定文字色を保持したリンクの確認
        const title = page.locator('#main .info-list-item-content-link');
        await title.hover();
        await expect(title).toHaveCSS('color', palette.expected);
        await expect(title).toHaveCSS('text-decoration-line', 'underline');
        await page.mouse.move(0, 0);
        await title.focus();
        await expect(title).toHaveCSS('color', palette.expected);
        const dayLink = page.locator('#main .widget_calendar [id$="linked-today"] a');
        await dayLink.hover();
        await expect(dayLink).toHaveCSS('color', 'rgb(51, 51, 51)');
        await expect(dayLink).toHaveCSS('background-color', 'rgb(255, 214, 126)');
      });
    }
  }
}

for (const width of [390, 1023, 1024, 1280]) {
  test(`ONE モバイルメニューボタンの文字色と表示 ${width}px`, async ({page}) => {
    await page.setViewportSize({width, height: 900});
    await page.setContent(`<!doctype html><html lang="ja"><head><meta charset="utf-8"><style>${css}</style></head>
      <body class="body mblt-header-and-footer-mobile-buttons">
        <ul class="mobile-header-menu-buttons mobile-menu-buttons">
          <li class="home-menu-button menu-button"><a class="menu-button-in" href="#header">ヘッダー</a></li>
        </ul>
        <ul class="mobile-footer-menu-buttons mobile-menu-buttons">
          <li class="navi-menu-button menu-button"><a class="menu-button-in" href="#footer">フッター</a></li>
        </ul>
      </body></html>`);
    for (const pcMenu of [false, true]) {
      for (const dark of [false, true]) {
        // カスタマイザーの二つの設定によるbodyクラス切り替えの再現
        await page.locator('body').evaluate((body, settings) => {
          body.classList.toggle('is-pcmenu-on', settings.pcMenu);
          body.classList.toggle('is-dark-on', settings.dark);
        }, {pcMenu, dark});
        const expectedColor = dark ? 'rgb(255, 255, 255)' : 'rgb(51, 51, 51)';
        const footer = page.locator('.mobile-footer-menu-buttons');
        if (width < 1024 || pcMenu) {
          await expect(footer).toBeVisible();
        } else {
          await expect(footer).toBeHidden();
        }
        await expect(page.locator('.mobile-header-menu-buttons .menu-button > a')).toHaveCSS('color', expectedColor);
        await expect(footer.locator('.menu-button > a')).toHaveCSS('color', expectedColor);
      }
    }
  });
}
