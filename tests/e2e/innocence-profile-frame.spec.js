const {readFileSync} = require('node:fs');
const path = require('node:path');
const {test, expect} = require('@playwright/test');

const root = path.resolve(__dirname, '../..');
const styles = ['style.css', 'css/entry-content.css', 'skins/skin-innocence/style.css']
  .map(file => readFileSync(path.join(root, file), 'utf8'))
  .join('\n');

const profiles = [
  {id: 'square', round: false, size: [200, 200], height: 100},
  {id: 'circle', round: true, size: [200, 200], height: 100},
  {id: 'landscape', round: true, size: [240, 120], height: 50},
  {id: 'portrait', round: false, size: [120, 240], height: 200},
];

function avatarImage([width, height]) {
  // 実物の縦横比と透過部分を持つテスト画像
  const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${width}" height="${height}">
    <circle cx="50%" cy="50%" r="${Math.min(width, height) / 4}" fill="teal"/>
  </svg>`;
  return `<img class="avatar avatar-200 photo" src="data:image/svg+xml,${encodeURIComponent(svg)}" width="200" height="200" alt="">`;
}

function profile({id, round, size}) {
  return `<section id="${id}" class="widget widget_author_box">
    <div class="author-box border-element no-icon cf">
      <figure class="author-thumb${round ? ' circle-image' : ''}">${size ? avatarImage(size) : ''}</figure>
      <div class="author-content"><div class="author-name">プロフィール</div></div>
    </div>
  </section>`;
}

async function samplePixels(page, points) {
  // CSS座標での画素比較に使用するスクリーンショット
  const png = await page.screenshot({scale: 'css'});
  return page.evaluate(async ({src, points}) => {
    const image = new Image();
    image.src = src;
    await image.decode();
    const canvas = document.createElement('canvas');
    canvas.width = image.naturalWidth;
    canvas.height = image.naturalHeight;
    const context = canvas.getContext('2d');
    context.drawImage(image, 0, 0);
    return points.map(([x, y]) => Array.from(context.getImageData(Math.floor(x), Math.floor(y), 1, 1).data));
  }, {src: `data:image/png;base64,${png.toString('base64')}`, points});
}

for (const viewportWidth of [1280, 320]) {
  test(`イノセンスのプロフィール画像と白い縁の描画 幅${viewportWidth}px`, async ({page}) => {
    await page.setViewportSize({width: viewportWidth, height: 1200});
    await page.route(/^https?:/, route => route.abort());
    await page.setContent(`<!doctype html><style>${styles}</style>
      <aside class="nwa" style="max-width:336px">${profiles.map(profile).join('')}${profile({id: 'empty'})}</aside>
      <main class="entry-content iwe-shadow-paper">${profile({id: 'wide', size: [200, 200]})}</main>`);

    const points = [];
    const colors = [];
    const white = [255, 255, 255, 255];
    const boxBackground = [245, 245, 245, 255];
    const avatarColor = [0, 128, 128, 255];
    for (const {id, round, size, height} of profiles) {
      const image = page.locator(`#${id} img`);
      await expect(image).toHaveJSProperty('naturalWidth', size[0]);
      await expect(image).toHaveJSProperty('naturalHeight', size[1]);
      await expect(image).toHaveCSS('border-radius', round ? '50%' : '0px');
      const {x, y, width, height: renderedHeight} = await image.boundingBox();
      const box = await page.locator(`#${id} .author-box`).boundingBox();
      expect(width).toBeCloseTo(100, 1);
      expect(renderedHeight).toBeCloseTo(height, 1);
      // 画像とプロフィール欄の中心位置の照合
      const center = x + width / 2;
      expect(center).toBeCloseTo(box.x + box.width / 2, 1);

      // 縁の先端と厚み、左上の形、画像の下地と中心色の照合
      points.push([center, y - 5], [center, y - 12], [x - 5, y - 5], [center, y + 5], [center, y + height / 2]);
      colors.push(white, boxBackground, round ? boxBackground : white, white, avatarColor);
    }
    expect(await samplePixels(page, points)).toEqual(colors);

    const emptyFigure = page.locator('#empty .author-thumb');
    await expect(emptyFigure).toHaveCSS('height', '0px');
    expect(await emptyFigure.evaluate(figure => getComputedStyle(figure, '::before').content)).toBe('none');
    const wideImage = page.locator('#wide img');
    await expect(wideImage).toHaveCSS('background-color', 'rgba(0, 0, 0, 0)');
    await expect(wideImage).toHaveCSS('box-shadow', 'rgba(0, 0, 0, 0.16) 0px 2px 2px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px');
  });
}
