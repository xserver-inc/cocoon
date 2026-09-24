const fs = require('fs');
const path = require('path');
const {test, expect} = require('@playwright/test');

const root = path.resolve(__dirname, '../..');
const read = file => fs.readFileSync(path.join(root, file), 'utf8');
const themeCss = read('style.css') + '\n' + read('css/entry-content.css');
const tabScript = read('blocks/src/block/tab/tab-frontend.js');
const skins = [
  {name: 'てがきノート ダークスカイ', css: 'skins/skin-tegakinote-dark-sky/style.css', scheme: 'light'},
  {name: 'Simple-Darkmode', css: 'skins/simple-darkmode/css/style.css', scheme: 'dark'},
  {name: 'Simple-Darkmode Always', css: 'skins/simple-darkmode-always/css/style.css', schemes: ['light', 'dark']},
  {name: 'ダーク エンジ', css: 'skins/skin-dark-enji/style.css', scheme: 'light'},
  {name: 'ダーク カモノハ', css: 'skins/skin-dark-kamonoha/style.css', scheme: 'light'},
  {name: 'ダーク ルリ', css: 'skins/skin-dark-ruri/style.css', scheme: 'light'}
];
const tabMarkup = '<div class="entry-content"><div class="tab-block cocoon-block-tab"><ul class="tab-label-group"><li class="tab-label">最初のタブ</li><li class="tab-label">次のタブ</li></ul><div class="tab-content-group"><div class="tab-content">最初の本文</div><div class="tab-content">次の本文</div></div></div></div>';

// 前景色と背景色から文字の読みやすさを数値化するための輝度計算
function luminance(color) {
  const channels = color.match(/[\d.]+/g).slice(0, 3).map(Number);
  const linear = channels.map(channel => {
    const value = channel / 255;
    return value <= 0.04045 ? value / 12.92 : ((value + 0.055) / 1.055) ** 2.4;
  });
  return 0.2126 * linear[0] + 0.7152 * linear[1] + 0.0722 * linear[2];
}

function contrast(text, background) {
  const values = [luminance(text), luminance(background)].sort((a, b) => b - a);
  return (values[0] + 0.05) / (values[1] + 0.05);
}

async function tabColors(page, skinCss = '') {
  // 保存済みブロックと公開ページ用スクリプトによるタブ切替の再現
  await page.setContent(`<!doctype html><html lang="ja"><head><style>${themeCss}\n${skinCss}</style><script>${tabScript}</script></head><body class="body"><main class="main">${tabMarkup}</main></body></html>`, {waitUntil: 'domcontentloaded'});
  return currentTabColors(page);
}

async function currentTabColors(page) {
  return page.locator('.tab-label').evaluateAll(labels => labels.map(label => {
    const style = getComputedStyle(label);
    return {color: style.color, background: style.backgroundColor};
  }));
}

for (const skin of skins) {
  for (const scheme of skin.schemes || [skin.scheme]) {
    for (const width of [390, 1280]) {
      test(`${skin.name} のタブは選択状態を区別でき、両方の文字を読める ${width}px ${scheme}`, async ({page}) => {
        await page.setViewportSize({width, height: 900});
        await page.emulateMedia({colorScheme: scheme});
        const [active, inactive] = await tabColors(page, read(skin.css));
        await expect(page.locator('.tab-label').first()).toHaveClass(/is-active/);
        expect(active.background).toMatch(/^rgb\(/);
        expect(inactive.background).toMatch(/^rgb\(/);
        expect(contrast(active.color, active.background)).toBeGreaterThanOrEqual(4.5);
        expect(contrast(inactive.color, inactive.background)).toBeGreaterThanOrEqual(4.5);
        expect(luminance(active.background)).toBeGreaterThan(luminance(inactive.background));
        await page.locator('.tab-label').nth(1).click();
        await expect(page.locator('.tab-label').nth(1)).toHaveClass(/is-active/);
        await expect(page.locator('.tab-content').nth(1)).toBeVisible();
        await expect(page.locator('.tab-content').first()).toBeHidden();
        expect(await currentTabColors(page)).toEqual([inactive, active]);
      });
    }
  }
}

test('Simple-Darkmode のライト表示は親テーマのタブ配色を維持', async ({page}) => {
  await page.emulateMedia({colorScheme: 'light'});
  const base = await tabColors(page);
  const light = await tabColors(page, read('skins/simple-darkmode/css/style.css'));
  expect(light.map(label => label.background)).toEqual(base.map(label => label.background));
  expect(light[0].color).toBe(base[0].color);
  expect(contrast(light[1].color, light[1].background)).toBeGreaterThanOrEqual(4.5);
});
