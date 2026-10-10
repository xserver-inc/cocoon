const fs = require('fs');
const path = require('path');
const {test, expect} = require('@playwright/test');

const settingsCss = fs.readFileSync(
  path.resolve(__dirname, '../../css/cocoon-settings.css'),
  'utf8'
);

for (const {mode, width, wide} of [
  {mode: 'responsive', width: 1920, wide: true},
  {mode: 'responsive', width: 1440, wide: true},
  {mode: 'responsive', width: 1024, wide: false},
  {mode: 'responsive', width: 390, wide: false},
  {mode: 'tabs', width: 1920, wide: false},
  {mode: 'tabs', width: 390, wide: false},
]) {
  test(`${mode}表示・${width}pxでウィジェット設定が本文幅いっぱいに表示`, async ({page}) => {
    await page.setViewportSize({width, height: 900});
    await page.route('**/*', route => route.abort());

    // WordPress標準の自動余白と内容の少ない設定パネルによる縮小条件の再現
    await page.setContent(`<!doctype html><html lang="ja"><head><style>
      .widget { margin: 0 auto 10px; position: relative; box-sizing: border-box; }
      ${settingsCss}
    </style></head><body class="wp-admin toplevel_page_theme-settings">
      <div class="wrap admin-settings">
        <div id="tabs" class="is-navigation-enhanced is-navigation-mode-${mode}${wide ? ' is-navigation-wide' : ''}">
          <div class="cocoon-settings-navigation">設定メニュー</div>
          <div id="tab-general-content" class="metabox-holder"><div class="postbox"><h2 class="hndle">全体設定</h2><div class="inside">設定内容</div></div></div>
          <div id="tab-widget-content" class="widget metabox-holder"><div class="postbox"><h2 class="hndle">ウィジェット設定</h2><div class="inside">設定内容</div></div></div>
        </div>
      </div>
    </body></html>`);

    // 同じ列に配置された通常パネルとウィジェットパネルの実寸比較
    const layout = await page.evaluate(() => {
      const tabs = document.querySelector('#tabs');
      const general = document.querySelector('#tab-general-content').getBoundingClientRect();
      const widget = document.querySelector('#tab-widget-content').getBoundingClientRect();
      return {
        grid: getComputedStyle(tabs).display === 'grid',
        generalWidth: general.width,
        widgetWidth: widget.width,
        generalLeft: general.left,
        widgetLeft: widget.left,
        right: widget.right,
        tabsRight: tabs.getBoundingClientRect().right,
        documentWidth: document.documentElement.scrollWidth,
      };
    });

    expect(layout.grid).toBe(wide);
    expect(Math.abs(layout.widgetWidth - layout.generalWidth)).toBeLessThan(1);
    expect(Math.abs(layout.widgetLeft - layout.generalLeft)).toBeLessThan(1);
    expect(Math.abs(layout.right - layout.tabsRight)).toBeLessThan(1);
    expect(layout.documentWidth).toBeLessThanOrEqual(width);
  });
}
