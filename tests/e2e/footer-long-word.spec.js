const fs = require('fs');
const path = require('path');
const {test, expect} = require('@playwright/test');

const css = fs.readFileSync(path.resolve(__dirname, '../../style.css'), 'utf8');
const longWord = 'ContinuousEnglishCaptionWithoutSpaces'.repeat(9);

for (const width of [1024, 1280]) {
  test(`フッターの長い英字列が3列の幅を押し広げない ${width}px`, async ({page}) => {
    await page.setViewportSize({width, height: 900});
    await page.setContent(`<!doctype html><html lang="ja"><head><style>${css}</style></head><body><footer class="footer"><div class="footer-in wrap"><div class="footer-widgets"><div class="footer-left"><aside class="widget"><p>${longWord}</p></aside></div><div class="footer-center"><aside class="widget">中央</aside></div><div class="footer-right"><aside class="widget">右</aside></div></div></div></footer></body></html>`);

    const layout = await page.locator('.footer-widgets').evaluate(element => ({
      overflow: element.scrollWidth - element.clientWidth,
      columns: [...element.children].map(column => column.getBoundingClientRect().width)
    }));
    expect(layout.overflow).toBeLessThanOrEqual(1);
    expect(Math.max(...layout.columns) - Math.min(...layout.columns)).toBeLessThanOrEqual(1);
  });
}

for (const type of ['header', 'footer']) {
  for (const width of [320, 390]) {
    test(`モバイル${type}ボタンの長いキャプションが画面からはみ出さない ${width}px`, async ({page}) => {
      await page.setViewportSize({width, height: 844});
      await page.setContent(`<!doctype html><html lang="ja"><head><style>${css}</style></head><body><ul class="mobile-${type}-menu-buttons mobile-menu-buttons">${[longWord, 'ホーム', '検索', 'メニュー'].map(caption => `<li class="menu-button"><a class="menu-button-in" href="#"><span class="menu-caption">${caption}</span></a></li>`).join('')}</ul></body></html>`);

      const layout = await page.locator('.mobile-menu-buttons').evaluate(element => ({
        overflow: element.scrollWidth - element.clientWidth,
        height: element.getBoundingClientRect().height,
        buttons: [...element.children].map(button => button.getBoundingClientRect().width)
      }));
      expect(layout.overflow).toBeLessThanOrEqual(1);
      expect(layout.height).toBeLessThanOrEqual(100);
      expect(Math.max(...layout.buttons) - Math.min(...layout.buttons)).toBeLessThanOrEqual(1);
    });
  }
}

test('短いモバイルキャプションは1行のまま表示される', async ({page}) => {
  await page.setViewportSize({width: 390, height: 844});
  await page.setContent(`<!doctype html><html lang="ja"><head><style>${css}</style></head><body><ul class="mobile-footer-menu-buttons mobile-menu-buttons"><li class="menu-button"><a href="#"><span class="menu-caption">ホーム</span></a></li></ul></body></html>`);

  const caption = await page.locator('.menu-caption').evaluate(element => ({
    height: element.getBoundingClientRect().height,
    lineHeight: parseFloat(getComputedStyle(element).lineHeight)
  }));
  expect(caption.height).toBeLessThanOrEqual(caption.lineHeight + 1);
});

test('スキンがキャプションをblock表示にしても長い文字で高さが伸びない', async ({page}) => {
  await page.setViewportSize({width: 320, height: 844});
  await page.setContent(`<!doctype html><html lang="ja"><head><style>${css}</style><style>.skin-grayish .mobile-menu-buttons .menu-caption {display: block;}</style></head><body class="skin-grayish"><ul class="mobile-footer-menu-buttons mobile-menu-buttons"><li class="menu-button"><a href="#"><span class="menu-caption">${longWord}</span></a></li><li class="menu-button"><a href="#"><span class="menu-caption">ホーム</span></a></li></ul></body></html>`);

  const layout = await page.locator('.mobile-menu-buttons').evaluate(element => ({
    overflow: element.scrollWidth - element.clientWidth,
    height: element.getBoundingClientRect().height
  }));
  expect(layout.overflow).toBeLessThanOrEqual(1);
  expect(layout.height).toBeLessThanOrEqual(100);
});
