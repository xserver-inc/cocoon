'use strict';
const assert = require('assert');
const {JSDOM} = require('jsdom');
const picker = require('../../lib/page-access/analytics/assets/map-post-picker.js');

const markup = `<form>
<select name="period"><option value="30days">30days</option><option value="7days">7days</option></select>
<input name="from" value="2026-09-01"><input name="to" value="2026-09-22">
<select name="device"><option value="all">all</option><option value="mobile">mobile</option></select>
<div class="cocoon-map-picker" data-placeholder="人気記事から選ぶ">
<button type="button" class="cocoon-map-picker-trigger" aria-expanded="false"><span class="cocoon-map-picker-value">選択済み</span></button>
<input type="hidden" name="source_post_id" value="42"><button type="button" class="cocoon-map-picker-clear">clear</button>
<div class="cocoon-map-picker-panel" tabindex="-1" hidden>
<strong class="cocoon-map-picker-heading-text"></strong><button type="button" class="cocoon-map-picker-close">close</button>
<input type="search"><p class="cocoon-map-picker-context"></p><ol class="cocoon-map-picker-list"></ol>
<p class="cocoon-map-picker-status"></p><button type="button" class="cocoon-map-picker-retry" hidden>retry</button>
</div></div><button id="outside">表示</button></form>`;
const config = {ajax: {url: 'https://example.org/wp-admin/admin-ajax.php', post_picker_nonce: 'test-nonce'}};
const items = Array.from({length: 10}, (_, index) => ({id: index + 1, name: '記事 ' + (index + 1), rank: index + 1, pv: String(100 - index), has_clicks: index !== 0}));
const payload = (data = items) => ({ok: true, json: async () => ({success: true, data: {items: data, from: '2026-09-01', to: '2026-09-22', no_clicks: 'PCのクリック記録なし'}})});
const tick = (ms = 10) => new Promise(resolve => setTimeout(resolve, ms));

async function run() {
  const dom = new JSDOM(markup, {url: 'https://example.org/wp-admin/admin.php'});
  const win = dom.window;
  const doc = win.document;
  win.HTMLElement.prototype.scrollIntoView = function () {};
  const requests = [];
  let responder = async () => payload();
  win.fetch = (url, options) => {requests.push({url: new URL(url), options}); return responder(url, options);};
  const get = (selector) => doc.querySelector(selector);
  const trigger = get('.cocoon-map-picker-trigger');
  const panel = get('.cocoon-map-picker-panel');
  const hidden = get('[name="source_post_id"]');
  const search = get('[type="search"]');
  const input = (text) => {search.value = text; search.dispatchEvent(new win.Event('input', {bubbles: true}));};
  const key = (target, name) => target.dispatchEvent(new win.KeyboardEvent('keydown', {key: name, bubbles: true, cancelable: true}));
  try {
    picker.init(doc, config);
    picker.init(doc, config);
    assert.strictEqual(requests.length, 0, '閉じている間の集計要求なし');
    trigger.click();
    await tick();
    assert.strictEqual(requests.length, 1, '二重初期化の防止');
    assert.strictEqual(doc.activeElement, panel, '検索欄を自動フォーカスしないことの確認');
    assert.strictEqual(requests[0].url.searchParams.get('q'), '');
    assert.strictEqual(requests[0].url.searchParams.get('period'), '30days');
    assert.strictEqual(requests[0].options.credentials, 'same-origin');
    assert.strictEqual(doc.querySelectorAll('.cocoon-map-picker-item').length, 10);
    assert.strictEqual(get('.cocoon-map-picker-no-clicks').textContent, 'PCのクリック記録なし');
    key(panel, 'ArrowDown');
    assert.strictEqual(doc.activeElement.textContent.includes('記事 1'), true);
    key(doc.activeElement, 'ArrowDown');
    assert.strictEqual(doc.activeElement.textContent.includes('記事 2'), true);
    doc.activeElement.click();
    assert.strictEqual(hidden.value, '2');
    assert.strictEqual(panel.hidden, true);
    assert.strictEqual(doc.activeElement, trigger);

    trigger.click();
    await tick();
    search.focus();
    responder = async () => payload([{id: 23, name: '<img src=x onerror=alert(1)>', meta: '<script>alert(1)</script>'}]);
    input('トップ10以外の記事');
    assert.strictEqual(hidden.value, '2', '検索途中の選択保持');
    assert.strictEqual(doc.querySelectorAll('.cocoon-map-picker-item').length, 0, '入力直後の古い候補除外');
    await tick(280);
    assert.strictEqual(requests[requests.length - 1].url.searchParams.get('q'), 'トップ10以外の記事');
    assert.strictEqual(get('.cocoon-map-picker-title').textContent, '<img src=x onerror=alert(1)>');
    assert.strictEqual(panel.querySelector('img,script'), null, '候補名のHTML非実行');
    assert.strictEqual(key(search, 'Enter'), false, '検索中の意図しないフォーム送信防止');
    assert.strictEqual(search.dispatchEvent(new win.KeyboardEvent('keydown', {key: 'Enter', isComposing: true, bubbles: true, cancelable: true})), true, 'IME変換確定の妨害防止');
    key(search, 'ArrowDown');
    assert.strictEqual(doc.activeElement, get('.cocoon-map-picker-item'));
    key(search, 'Escape');
    assert.strictEqual(hidden.value, '2');
    assert.strictEqual(panel.hidden, true);

    responder = async () => payload();
    trigger.click();
    await tick();
    get('[name="period"]').value = '7days';
    get('[name="period"]').dispatchEvent(new win.Event('change', {bubbles: true}));
    await tick();
    assert.strictEqual(requests[requests.length - 1].url.searchParams.get('period'), '7days');
    assert.strictEqual(hidden.value, '2');
    input('');
    await tick(280);
    assert.strictEqual(doc.querySelectorAll('.cocoon-map-picker-item').length, 10, '検索解除時のトップ10復帰');

    let finishOld;
    responder = () => new Promise(resolve => {finishOld = resolve;});
    input('古い検索');
    await tick(280);
    responder = async () => payload([{id: 99, name: '新しい検索'}]);
    input('新しい検索');
    await tick(280);
    finishOld(payload([{id: 98, name: '古い検索'}]));
    await tick();
    assert.strictEqual(get('.cocoon-map-picker-title').textContent, '新しい検索', '遅延応答の上書き防止');

    let finishClosed;
    responder = () => new Promise(resolve => {finishClosed = resolve;});
    input('閉じた検索');
    await tick(280);
    get('#outside').dispatchEvent(new win.MouseEvent('pointerdown', {bubbles: true}));
    finishClosed(payload());
    await tick();
    assert.strictEqual(panel.hidden, true, '閉じた後の応答で再表示しないことの確認');

    responder = async () => ({ok: false, json: async () => ({success: false, data: {message: 'invalid_period'}})});
    trigger.click();
    await tick();
    assert.strictEqual(get('.cocoon-map-picker-retry').hidden, false);
    assert.match(get('.cocoon-map-picker-status').textContent, /date range/);
    responder = async () => payload([]);
    get('.cocoon-map-picker-retry').click();
    await tick();
    assert.strictEqual(get('.cocoon-map-picker-retry').hidden, true);
    assert.match(get('.cocoon-map-picker-status').textContent, /No views/);
    get('.cocoon-map-picker-clear').click();
    assert.strictEqual(hidden.value, '0');
    assert.strictEqual(get('.cocoon-map-picker-value').textContent, '人気記事から選ぶ');
    assert.strictEqual(panel.hidden, true);

    trigger.click();
    await tick();
    trigger.focus();
    assert.strictEqual(panel.hidden, false);
    key(trigger, 'Escape');
    assert.strictEqual(panel.hidden, true, '開閉ボタンへフォーカスを戻した場合もEscで終了');
    assert.strictEqual(doc.activeElement, trigger);
  } finally {win.close();}
  process.stdout.write('クリックマップ記事セレクターDOMテスト成功\n');
}
run().catch(error => {process.stderr.write(String(error.stack || error) + '\n'); process.exitCode = 1;});
