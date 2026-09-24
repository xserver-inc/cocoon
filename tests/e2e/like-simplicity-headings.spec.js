const fs = require('fs');
const path = require('path');
const {test, expect} = require('@playwright/test');

const root = path.resolve(__dirname, '../..');
const read = file => fs.readFileSync(path.join(root, file), 'utf8');
const themeCss = read('style.css').match(/\.article h2 \{[^}]*\}/)[0];
const skinCss = read('skins/skin-simplicity/style.css');

// WordPressの制約付きグループが指定する左右の自動マージンと最大幅
const groupCss = `
  .is-layout-constrained > :where(:not(.alignleft):not(.alignright):not(.alignfull)) {
    max-width: 840px;
    margin-left: auto !important;
    margin-right: auto !important;
  }
`;

test('Like Simplicity グループ内外のH2位置と角丸', async ({page}) => {
  await page.setContent(`<!doctype html><html lang="ja"><head><meta charset="utf-8"><style>
    ${themeCss}\n${groupCss}\n${skinCss}
    body { margin: 0; }
    .article { box-sizing: border-box; width: min(900px, 100%); padding: 29px; }
  </style></head><body class="body"><article class="article">
    <h2 id="plain">グループなし</h2>
    <div class="wp-block-group is-layout-constrained"><h2 id="grouped">グループあり</h2></div>
    <div class="wp-block-group is-layout-constrained has-background" style="padding: 20px; background: #eee"><h2 id="decorated">装飾付きグループ</h2></div>
    <div class="wp-block-group is-layout-constrained"><h2 id="aligned" class="alignwide">幅広見出し</h2></div>
    <div class="wp-block-group is-layout-constrained"><h2 id="custom" style="color: navy">個別指定のある見出し</h2></div>
  </article></body></html>`);

  for (const width of [390, 834, 835, 1024, 1280]) {
    await page.setViewportSize({width, height: 900});
    const headings = await page.locator('#plain, #grouped').evaluateAll(elements => elements.map(element => {
      const rect = element.getBoundingClientRect();
      return {left: rect.left, right: rect.right, radius: getComputedStyle(element).borderTopLeftRadius};
    }));
    expect.soft(headings[1].left, `${width}pxの左端`).toBeCloseTo(headings[0].left, 1);
    expect.soft(headings[1].right, `${width}pxの右端`).toBeCloseTo(headings[0].right, 1);
    expect.soft(headings.map(heading => heading.radius), `${width}pxの角丸`).toEqual(['0px', '0px']);

    // 装飾付きグループ・幅広見出し・個別指定見出しの標準余白の維持
    const excluded = await page.locator('#decorated, #aligned, #custom').evaluateAll(elements => elements.map(element => {
      const style = getComputedStyle(element);
      return {leftMargin: style.marginLeft, rightMargin: style.marginRight, maxWidth: style.maxWidth};
    }));
    for (const [index, heading] of excluded.entries()) {
      expect.soft(heading.leftMargin, `${width}pxの対象外見出し${index + 1}の左余白`).not.toBe('-29px');
      expect.soft(heading.rightMargin, `${width}pxの対象外見出し${index + 1}の右余白`).not.toBe('-29px');
      expect.soft(heading.maxWidth, `${width}pxの対象外見出し${index + 1}の最大幅`).toBe('840px');
    }
  }
});
