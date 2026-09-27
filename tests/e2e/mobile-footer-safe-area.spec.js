const fs = require('fs');
const path = require('path');
const {test, expect} = require('@playwright/test');

const css = fs.readFileSync(path.resolve(__dirname, '../../style.css'), 'utf8');

async function preparePage(page, {bodyClass, width = 390, firefoxRules = true}) {
  await page.setViewportSize({width, height: 844});
  // Chromium上でのFirefox向け規則の有効化（Android実機の再現とは別の配置検証）
  const stylesheet = firefoxRules
    ? css.replaceAll('@supports (-moz-appearance: none)', '@supports (display: block)')
    : css;
  const menuType = bodyClass.includes('footer') ? 'footer' : 'header';
  await page.setContent(`<!doctype html><html lang="ja"><head>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <style>${stylesheet}</style></head><body class="body ${bodyClass}">
    <main style="height:1600px"></main>
    <footer class="footer"><div class="footer-in wrap cf" style="width:100%">
      <div class="footer-bottom fdt-up-and-down cf"><div class="footer-bottom-content">
        <div class="copyright"><a id="last-footer-link" href="#footer-link-target">ページ末尾のリンク</a></div>
      </div></div>
    </div></footer>
    <ul class="mobile-${menuType}-menu-buttons mobile-menu-buttons">
      <li class="menu-button"><a href="#"><span class="menu-icon">●</span><span class="menu-caption">ホーム</span></a></li>
    </ul></body></html>`);
  return page.context().newCDPSession(page);
}

async function setBottomInset(cdp, bottom) {
  // ブラウザーの環境変数による安全領域の再現
  await cdp.send('Emulation.setSafeAreaInsetsOverride', {
    insets: {top: 0, right: 0, bottom, left: 0}
  });
}

for (const {layout, width} of [
  {layout: 'footer_mobile_buttons', width: 390},
  {layout: 'header_and_footer_mobile_buttons', width: 390},
  {layout: 'footer_mobile_buttons', width: 1023}
]) {
  test(`固定フッターの末尾リンクが安全領域の変化で隠れない (${layout} / ${width}px)`, async ({page}) => {
    const cdp = await preparePage(page, {
      bodyClass: `mblt-${layout.replaceAll('_', '-')}`,
      width
    });
    try {
      for (const inset of [0, 24, 34, 0]) {
        await setBottomInset(cdp, inset);
        await page.evaluate(() => window.scrollTo(0, document.documentElement.scrollHeight));
        const result = await page.evaluate(() => {
          const link = document.querySelector('#last-footer-link');
          const linkRect = link.getBoundingClientRect();
          const menuRect = document.querySelector('.mobile-footer-menu-buttons').getBoundingClientRect();
          // リンク中央を押したときの実際のクリック対象の確認
          const hit = document.elementFromPoint(
            (linkRect.left + linkRect.right) / 2,
            (linkRect.top + linkRect.bottom) / 2
          );
          return {
            linkBottom: linkRect.bottom,
            menuTop: menuRect.top,
            menuHeight: menuRect.height,
            bottomGap: window.innerHeight - menuRect.bottom,
            linkReceivesClick: Boolean(hit && link.contains(hit))
          };
        });
        expect(result.bottomGap).toBeCloseTo(inset, 1);
        expect(result.menuHeight).toBeCloseTo(50, 1);
        expect(result.linkBottom).toBeLessThanOrEqual(result.menuTop);
        expect(result.linkReceivesClick).toBe(true);
      }
    } finally {
      await cdp.detach();
    }
  });
}

for (const scenario of [
  {name: 'スクロール表示', bodyClass: 'mblt-footer-mobile-buttons scrollable-mobile-buttons', margin: 0},
  {name: 'ヘッダーとフッターのスクロール表示', bodyClass: 'mblt-header-and-footer-mobile-buttons scrollable-mobile-buttons', margin: 0},
  {name: 'ヘッダーのみ', bodyClass: 'mblt-header-mobile-buttons', margin: 0},
  {name: 'PC幅', bodyClass: 'mblt-footer-mobile-buttons', width: 1024, margin: 0},
  {name: 'Firefox以外', bodyClass: 'mblt-footer-mobile-buttons', firefoxRules: false, margin: 50}
]) {
  test(`${scenario.name}には安全領域ぶんの下余白を追加しない`, async ({page}) => {
    const cdp = await preparePage(page, scenario);
    try {
      await setBottomInset(cdp, 34);
      const margin = await page.locator('body').evaluate(element => parseFloat(getComputedStyle(element).marginBottom));
      expect(margin).toBe(scenario.margin);
    } finally {
      await cdp.detach();
    }
  });
}
