/* eslint-env browser, node */
(function (root, factory) {
  'use strict';
  const api = factory();
  if (typeof module === 'object' && module.exports) {module.exports = api;}
  if (root && root.document) {
    const start = () => api.init(root.document);
    if (root.document.readyState === 'loading') {root.document.addEventListener('DOMContentLoaded', start, {once: true});}
    else {start();}
  }
})(typeof window !== 'undefined' ? window : null, () => {
  'use strict';
  function init(doc) {
    const details = Array.from(doc.querySelectorAll('.cocoon-click-stat-details'));
    if (!details.length || doc.getElementById('cocoon-click-stat-dialog')) {return;}
    const dialog = doc.createElement('dialog');
    // 非対応ブラウザーでは通常のdetailsを残す段階的な拡張
    if (typeof dialog.showModal !== 'function') {return;}
    dialog.id = 'cocoon-click-stat-dialog';
    dialog.className = 'cocoon-click-stat-dialog';
    dialog.setAttribute('aria-labelledby', 'cocoon-click-stat-dialog-title');
    dialog.setAttribute('aria-describedby', 'cocoon-click-stat-dialog-link');
    doc.body.appendChild(dialog);
    let activeTrigger = null;
    let pressedOutside = false;
    function outside(event) {
      const rect = dialog.getBoundingClientRect();
      return event.target === dialog && (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom);
    }
    dialog.addEventListener('pointerdown', event => {pressedOutside = outside(event);});
    // 本文のドラッグ選択が枠外で終わった場合の意図しない終了の防止
    dialog.addEventListener('click', event => {
      if (pressedOutside && outside(event)) {dialog.close();}
      pressedOutside = false;
    });
    dialog.addEventListener('close', () => {
      if (activeTrigger) {
        activeTrigger.setAttribute('aria-expanded', 'false');
        if (activeTrigger.isConnected) {activeTrigger.focus({preventScroll: true});}
      }
      activeTrigger = null;
      pressedOutside = false;
      dialog.replaceChildren();
    });
    details.forEach(detail => {
      const summary = detail.querySelector('summary');
      const content = detail.querySelector('.cocoon-click-stat-content');
      if (!summary || !content) {return;}
      const trigger = doc.createElement('button');
      trigger.type = 'button';
      trigger.className = 'cocoon-click-stat-trigger';
      trigger.textContent = summary.textContent;
      trigger.setAttribute('aria-label', summary.getAttribute('aria-label') || summary.textContent);
      trigger.setAttribute('aria-haspopup', 'dialog');
      trigger.setAttribute('aria-expanded', 'false');
      trigger.setAttribute('aria-controls', dialog.id);
      detail.before(trigger);
      detail.open = false;
      detail.hidden = true;
      trigger.addEventListener('click', () => {
        if (dialog.open) {return;}
        // エスケープ済みの表示要素を複製し、HTML文字列の再解釈を回避
        const body = content.cloneNode(true);
        const close = body.querySelector('.cocoon-click-stat-close');
        body.querySelector('.cocoon-click-stat-title').id = 'cocoon-click-stat-dialog-title';
        body.querySelector('.cocoon-click-stat-link').id = 'cocoon-click-stat-dialog-link';
        close.hidden = false;
        close.addEventListener('click', () => dialog.close());
        dialog.replaceChildren(body);
        activeTrigger = trigger;
        trigger.setAttribute('aria-expanded', 'true');
        dialog.showModal();
        close.focus({preventScroll: true});
      });
    });
  }
  return {init: init};
});
