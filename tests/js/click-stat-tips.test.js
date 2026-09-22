'use strict';
const assert = require('assert');
const {JSDOM} = require('jsdom');
const tips = require('../../lib/page-access/analytics/assets/click-stat-tips.js');

const markup = [1, 2].map(index => `<details class="cocoon-click-stat-details"><summary aria-label="リンク ${index} の詳細">詳細</summary>
  <div class="cocoon-click-stat-content"><div class="cocoon-click-stat-header"><h3 class="cocoon-click-stat-title">推定CTRの詳細</h3>
  <button class="cocoon-click-stat-close" type="button" hidden>×</button></div>
  <p class="cocoon-click-stat-link">リンク ${index} &lt;img src=x onerror=alert(1)&gt;</p><p>75.0%</p></div></details>`).join('');

const fallback = new JSDOM(markup);
tips.init(fallback.window.document);
assert.strictEqual(fallback.window.document.querySelector('.cocoon-click-stat-details').hidden, false);
assert.strictEqual(fallback.window.document.querySelector('dialog'), null, '非対応ブラウザーの代替表示');
fallback.window.close();

const dom = new JSDOM(markup);
const win = dom.window;
const doc = win.document;
// jsdomに未実装のdialog APIの代替と、状態管理に限定した単体検証
win.HTMLDialogElement.prototype.showModal = function () {this.open = true;};
win.HTMLDialogElement.prototype.close = function () {this.open = false; this.dispatchEvent(new win.Event('close'));};
try {
  tips.init(doc);
  tips.init(doc);
  const triggers = doc.querySelectorAll('.cocoon-click-stat-trigger');
  const dialog = doc.querySelector('dialog');
  assert.strictEqual(triggers.length, 2, '二重初期化の防止');
  assert.strictEqual(doc.querySelectorAll('dialog').length, 1, '複数行で共用するポップアップ');
  assert.strictEqual(doc.querySelector('.cocoon-click-stat-details').hidden, true);
  triggers[0].click();
  assert.strictEqual(dialog.open, true);
  assert.strictEqual(triggers[0].getAttribute('aria-expanded'), 'true');
  assert.match(dialog.textContent, /リンク 1/);
  assert.strictEqual(dialog.querySelector('img'), null, 'ラベル内HTMLの非実行');
  assert.strictEqual(doc.activeElement, dialog.querySelector('button'));
  dialog.querySelector('p').click();
  assert.strictEqual(dialog.open, true, '本文クリックで表示を維持');
  dialog.getBoundingClientRect = () => ({left: 20, right: 500, top: 20, bottom: 500});
  const pointer = (type, x) => dialog.dispatchEvent(new win.MouseEvent(type, {clientX: x, clientY: x, bubbles: true}));
  pointer('pointerdown', 50);
  pointer('click', 1);
  assert.strictEqual(dialog.open, true, '本文から枠外へのドラッグ終了時の表示維持');
  pointer('pointerdown', 1);
  pointer('click', 1);
  assert.strictEqual(dialog.open, false, '背景クリックで終了');
  assert.strictEqual(doc.activeElement, triggers[0]);
  assert.strictEqual(triggers[0].getAttribute('aria-expanded'), 'false');
  triggers[1].click();
  assert.match(dialog.textContent, /リンク 2/);
  assert.strictEqual(doc.querySelectorAll('#cocoon-click-stat-dialog-title').length, 1);
  dialog.querySelector('button').click();
  assert.strictEqual(dialog.open, false, '閉じるボタンで終了');
  assert.strictEqual(doc.activeElement, triggers[1]);
  assert.strictEqual(dialog.childElementCount, 0, '終了後の表示内容の片付け');
} finally {win.close();}
process.stdout.write('推定CTRのTips DOMテスト成功\n');
