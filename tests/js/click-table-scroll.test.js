'use strict';
const assert = require('assert');
const {JSDOM} = require('jsdom');
const scroll = require('../../lib/page-access/analytics/assets/click-table-scroll.js');

const markup = `<div class="cocoon-click-table-panel">
  <div class="cocoon-click-scroll-controls" hidden><span class="cocoon-click-scroll-hint">横にスクロールできます</span>
  <button class="cocoon-click-scroll-left"></button><button class="cocoon-click-scroll-right"></button></div>
  <div class="cocoon-click-table-frame"><div class="cocoon-click-table-scroll" tabindex="0"><table class="cocoon-click-table"></table></div></div></div>`;
const dom = new JSDOM(markup + markup, {pretendToBeVisual: true});
const win = dom.window;
const doc = win.document;
const panels = [...doc.querySelectorAll('.cocoon-click-table-panel')];
const callbacks = [];
const observers = [];
win.requestAnimationFrame = callback => callbacks.push(callback);
win.ResizeObserver = class {
  constructor(callback) {this.callback = callback; this.targets = []; observers.push(this);}
  observe(target) {this.targets.push(target);}
};
let reduced = true;
win.matchMedia = () => ({matches: reduced});
function flush() {callbacks.splice(0).forEach(callback => callback());}
try {
  const viewport = panels[0].querySelector('.cocoon-click-table-scroll');
  const table = panels[0].querySelector('table');
  const left = panels[0].querySelector('.cocoon-click-scroll-left');
  const right = panels[0].querySelector('.cocoon-click-scroll-right');
  const controls = panels[0].querySelector('.cocoon-click-scroll-controls');
  let width = 500;
  let position = 0;
  const movements = [];
  Object.defineProperties(viewport, {clientWidth: {get: () => width}, scrollWidth: {get: () => 1400}});
  viewport.getBoundingClientRect = () => ({left: 20});
  table.getBoundingClientRect = () => ({left: 20 - position, right: 1420 - position});
  viewport.scrollBy = options => movements.push(options);
  scroll.init(doc);
  scroll.init(doc);
  assert.strictEqual(observers.length, 2, '二重初期化の防止');
  assert.deepStrictEqual(observers[0].targets, [viewport, table, controls]);
  assert.notStrictEqual(viewport.id, panels[1].querySelector('.cocoon-click-table-scroll').id);
  assert.strictEqual(controls.hidden, false);
  assert.strictEqual(panels[1].querySelector('.cocoon-click-scroll-controls').hidden, true);
  assert.strictEqual(left.getAttribute('aria-controls'), viewport.id);
  assert.strictEqual(left.getAttribute('aria-disabled'), 'true');
  left.click();
  assert.strictEqual(movements.length, 0);
  right.click();
  assert.deepStrictEqual(movements.pop(), {left: 400, behavior: 'auto'});
  reduced = false;
  right.click();
  assert.deepStrictEqual(movements.pop(), {left: 400, behavior: 'smooth'});
  position = 400;
  viewport.dispatchEvent(new win.Event('scroll'));
  viewport.dispatchEvent(new win.Event('scroll'));
  assert.strictEqual(callbacks.length, 1, 'フレーム単位での更新');
  flush();
  assert.strictEqual(left.getAttribute('aria-disabled'), 'false');
  assert.strictEqual(right.getAttribute('aria-disabled'), 'false');
  position = 900;
  observers[0].callback(); flush();
  assert.strictEqual(right.getAttribute('aria-disabled'), 'true');
  right.focus();
  width = 1400;
  position = 0;
  win.dispatchEvent(new win.Event('resize')); flush();
  assert.strictEqual(controls.hidden, true);
  assert.strictEqual(doc.activeElement, viewport, '非表示になるボタンからのフォーカス退避');
  assert.strictEqual(viewport.hasAttribute('aria-describedby'), false);
  assert.strictEqual(panels[0].querySelector('.can-scroll-left, .can-scroll-right'), null);
} finally {win.close();}
process.stdout.write('クリック解析の横スクロール案内 DOMテスト成功\n');
