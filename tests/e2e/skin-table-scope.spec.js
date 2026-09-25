const fs = require('fs');
const path = require('path');
const {test, expect} = require('@playwright/test');

const root = path.resolve(__dirname, '../..');
const cases = [
  {skin: 'mil-light', target: 'td', property: 'borderTopWidth'},
  {skin: 'natural-blue', target: 'td', property: 'borderBottomWidth'},
  {skin: 'skin-season-autumn', target: 'td', property: 'borderTopWidth'},
  {skin: 'skin-fuwari-omeshicha', target: 'table', property: 'borderTopWidth'},
  {skin: 'skin-dark-enji', target: 'td', property: 'borderTopWidth', width: 800},
];

test('エディタールートにarticleが先に存在してもbodyクラスを補う', async ({page}) => {
  const script = fs.readFileSync(path.join(root, 'js/gutenberg-editor-classes.js'), 'utf8');
  await page.setContent(`<!doctype html><html lang="ja"><body>
    <div class="is-root-container article"><table><tr><td>本文</td></tr></table></div>
    <script>window.cocoonEditorClassConfig = {addClasses: ['cocoon-block-wrap', 'body', 'article', 'admin-page'], fontClasses: []};</script>
    <script>${script}</script></body></html>`);

  await expect(page.locator('.is-root-container')).toHaveClass(/\bbody\b/u);
});

for (const {skin, target, property, width} of cases) {
  test(`${skin}の表スタイルは本文内に限定`, async ({page}) => {
    const css = fs.readFileSync(path.join(root, 'skins', skin, 'style.css'), 'utf8');
    if (width) {
      await page.setViewportSize({width, height: 600});
    }
    await page.route('**/*', route => route.abort());
    await page.setContent(`<!doctype html><html lang="ja"><head><style>${css}</style></head>
      <body class="wp-admin"><table id="admin-table"><tr><td>管理画面</td></tr></table>
      <div class="body"><table id="content-table"><tr><td>本文</td></tr></table></div></body></html>`);

    // 同じ表構造を使った管理画面と本文内の罫線幅の比較
    const widths = await page.evaluate(({target, property}) => {
      const read = rootSelector => {
        const root = document.querySelector(rootSelector);
        const element = target === 'table' ? root : root.querySelector(target);
        return getComputedStyle(element)[property];
      };
      return {admin: read('body > #admin-table'), content: read('body > .body #content-table')};
    }, {target, property});

    expect(widths.admin).toBe('0px');
    expect(parseFloat(widths.content)).toBeGreaterThan(0);
  });
}

// 親テーマとの優先順位を含む公開ページ表装飾の維持
for (const {skin, markup, selector, property, expected} of [
  {
    skin: 'mil-light',
    markup: '<table id="calendar" class="wp-calendar-table"><tr><th>日</th><td>1</td></tr></table>',
    selector: '#calendar tr',
    property: 'backgroundColor',
    expected: 'rgba(0, 0, 0, 0)',
  },
  {
    skin: 'maple-alice',
    markup: '<div class="scrollable-table"><table id="scroll"><tr><th>見出し</th><td>本文</td></tr></table></div>',
    selector: '#scroll th',
    property: 'backgroundColor',
    expected: 'rgb(238, 238, 238)',
  },
]) {
  test(`${skin}の公開ページの表装飾を維持`, async ({page}) => {
    const themeCss = fs.readFileSync(path.join(root, 'style.css'), 'utf8');
    const skinCss = fs.readFileSync(path.join(root, 'skins', skin, 'style.css'), 'utf8');
    await page.route('**/*', route => route.abort());
    await page.setContent(`<!doctype html><html lang="ja"><head><style>${themeCss}\n${skinCss}</style></head>
      <body class="body"><div class="container"><main class="main"><article class="article entry-content">
      ${markup}</article></main></div></body></html>`);

    expect(await page.locator(selector).evaluate((element, name) => getComputedStyle(element)[name], property))
      .toBe(expected);
  });
}

// 共通コンテナー名が付いた管理画面側と本文内のTecurio表装飾の比較
for (const skin of [
  'skin-tecurio-earth',
  'skin-tecurio-grape',
  'skin-tecurio-lime',
  'skin-tecurio-mango',
  'skin-tecurio-moon',
  'skin-tecurio-peach',
  'skin-tecurio-sky',
  'skin-tecurio-soil',
  'skin-tecurio-sunset',
]) {
  test(`${skin}の表装飾は管理画面のコンテナーに及ばない`, async ({page}) => {
    const css = fs.readFileSync(path.join(root, 'skins', skin, 'style.css'), 'utf8');
    await page.route('**/*', route => route.abort());
    await page.setContent(`<!doctype html><html lang="ja"><head><style>${css}</style></head>
      <body class="wp-admin"><div class="container"><table id="admin-table"><tr><th>管理画面</th></tr></table></div>
      <div class="body"><table id="content-table"><tr><th>本文</th></tr></table></div></body></html>`);

    const backgrounds = await page.evaluate(() => ({
      admin: getComputedStyle(document.querySelector('#admin-table th')).backgroundColor,
      content: getComputedStyle(document.querySelector('#content-table th')).backgroundColor,
    }));

    expect(backgrounds.admin).toBe('rgba(0, 0, 0, 0)');
    expect(backgrounds.content).toBe('rgb(55, 71, 79)');
  });
}
