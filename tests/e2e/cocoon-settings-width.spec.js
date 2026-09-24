const fs = require('fs');
const path = require('path');
const {test, expect} = require('@playwright/test');

const root = path.resolve(__dirname, '../..');
const css = ['css/admin.css', 'css/cocoon-settings.css']
  .map(file => fs.readFileSync(path.join(root, file), 'utf8'))
  .join('\n');

// WordPress管理画面の余白と設定フォームの幅検証用HTML
const fixture = `<!doctype html>
<html lang="ja"><head><meta charset="utf-8"><style>
  html, body { margin: 0; }
  #wpcontent { margin-left: 160px; padding: 20px; }
  table.form-table { width: 100%; }
  @media (max-width: 782px) {
    #wpcontent { margin: 0; padding: 0; }
    .form-table th, .form-table td { display: block; width: auto; }
  }
  ${css}
</style></head><body class="toplevel_page_theme-settings">
<main id="wpcontent"><div class="wrap admin-settings">
  <h1>Cocoon 設定</h1><p>画面の幅を使う説明文と設定画面の案内</p>
  <form class="admin-settings"><div id="tabs" class="is-navigation-enhanced is-navigation-mode-responsive">
    <div class="cocoon-settings-navigation"><div class="cocoon-settings-sidebar">設定メニュー</div></div>
    <div class="metabox-holder" style="display: block"><div class="postbox"><div class="inside">
      <table class="form-table"><tbody><tr><th>説明</th><td>
        <input type="text" size="60" value="Cocoon標準の文章入力欄">
        <input class="regular-text" type="text" value="標準クラスの文章入力欄">
        <input type="text" size="10" value="短い文字列">
        <input type="number" value="12">
        <textarea rows="3">長文入力欄</textarea>
        <p class="tips">説明文を設定欄の幅いっぱいに表示</p>
        <div class="demo"><iframe class="iframe-demo" title="プレビュー" width="1000" height="400"></iframe></div>
      </td></tr></tbody></table>
    </div></div></div>
  </div></form>
</div></main></body></html>`;

for (const width of [1920, 1040, 800, 783, 782, 390, 320]) {
  test(`設定画面の幅と入力欄 ${width}px`, async ({page}) => {
    await page.setViewportSize({width, height: 900});
    await page.route('**/*', route => route.abort());
    await page.setContent(fixture);
    // 実際の画面幅による2列表示クラスの切り替え
    await page.evaluate(() => {
      const tabs = document.querySelector('#tabs');
      tabs.classList.toggle('is-navigation-wide', tabs.clientWidth >= 1040);
    });
    const dimensions = await page.evaluate(() => {
      const rect = selector => document.querySelector(selector).getBoundingClientRect();
      const rootElement = document.querySelector('.wrap.admin-settings');
      return {
        rootWidth: rect('.wrap.admin-settings').width,
        introWidth: rect('.wrap.admin-settings > p').width,
        fieldWidth: rect('input[type="text"][size="60"]').width,
        regularWidth: rect('input.regular-text').width,
        shortTextWidth: rect('input[type="text"][size="10"]').width,
        numberWidth: rect('input[type="number"]').width,
        textareaWidth: rect('textarea').width,
        tipsWidth: rect('.tips').width,
        tdWidth: rect('.form-table td').width,
        previewWidth: rect('.iframe-demo').width,
        rootOverflow: rootElement.scrollWidth - rootElement.clientWidth,
      };
    });
    expect(dimensions.tdWidth).toBeGreaterThan(0);
    expect(dimensions.tdWidth).toBeGreaterThan(width < 500 ? 150 : 300);
    if (width === 1920) {
      expect(dimensions.rootWidth).toBeGreaterThan(1400);
      expect(dimensions.introWidth).toBeGreaterThan(1400);
      expect(dimensions.previewWidth).toBeGreaterThan(1000);
      expect(dimensions.numberWidth).toBeLessThan(dimensions.fieldWidth * 0.5);
      expect(dimensions.shortTextWidth).toBeLessThan(dimensions.fieldWidth * 0.5);
    }
    expect(dimensions.fieldWidth).toBeLessThanOrEqual(dimensions.tdWidth + 1);
    expect(dimensions.fieldWidth).toBeGreaterThan(dimensions.tdWidth * 0.95);
    expect(dimensions.regularWidth).toBeGreaterThan(dimensions.tdWidth * 0.95);
    expect(dimensions.textareaWidth).toBeLessThanOrEqual(dimensions.tdWidth + 1);
    expect(dimensions.textareaWidth).toBeGreaterThan(dimensions.tdWidth * 0.95);
    expect(dimensions.tipsWidth).toBeLessThanOrEqual(dimensions.tdWidth + 1);
    expect(dimensions.tipsWidth).toBeGreaterThan(dimensions.tdWidth * 0.95);
    expect(dimensions.previewWidth).toBeGreaterThanOrEqual(dimensions.tdWidth - 32);
    expect(dimensions.rootOverflow).toBeLessThanOrEqual(1);
  });
}
