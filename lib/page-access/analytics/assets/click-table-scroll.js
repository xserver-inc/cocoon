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
    const win = doc.defaultView;
    doc.querySelectorAll('.cocoon-click-table-panel').forEach((panel, index) => {
      const viewport = panel.querySelector('.cocoon-click-table-scroll');
      const table = panel.querySelector('.cocoon-click-table');
      const frame = panel.querySelector('.cocoon-click-table-frame');
      const controls = panel.querySelector('.cocoon-click-scroll-controls');
      const hint = panel.querySelector('.cocoon-click-scroll-hint');
      const left = panel.querySelector('.cocoon-click-scroll-left');
      const right = panel.querySelector('.cocoon-click-scroll-right');
      if (!viewport || !table || !frame || !controls || !hint || !left || !right || panel.dataset.scrollReady) {return;}
      panel.dataset.scrollReady = 'true';
      if (!viewport.id) {viewport.id = 'cocoon-click-scroll-' + index;}
      hint.id = viewport.id + '-hint';
      left.setAttribute('aria-controls', viewport.id);
      right.setAttribute('aria-controls', viewport.id);

      // 横スクロール領域の外に配置する見出し複製と重複IDの除去
      const sourceHead = table.tHead;
      let stickyHeader = null;
      let stickyTrack = null;
      let stickyTable = null;
      let sourceLinks = [];
      let stickyLinks = [];
      let originalTabIndexes = [];
      let originalAriaHidden = [];
      let originalInert = [];
      let sourceHeaders = [];
      let originalHeaderLabels = [];
      let headerVisible = false;
      if (sourceHead && sourceHead.rows.length) {
        stickyHeader = doc.createElement('div');
        stickyHeader.className = 'cocoon-click-sticky-header';
        stickyHeader.hidden = true;
        stickyTrack = doc.createElement('div');
        stickyTrack.className = 'cocoon-click-sticky-track';
        stickyTable = table.cloneNode(false);
        stickyTable.setAttribute('role', 'presentation');
        const colgroup = table.querySelector('colgroup');
        if (colgroup) {stickyTable.appendChild(colgroup.cloneNode(true));}
        stickyTable.appendChild(sourceHead.cloneNode(true));
        stickyTable.querySelectorAll('[id]').forEach(element => element.removeAttribute('id'));
        stickyTrack.appendChild(stickyTable);
        stickyHeader.appendChild(stickyTrack);
        panel.insertBefore(stickyHeader, frame);
        sourceLinks = [...sourceHead.querySelectorAll('.cocoon-click-sort')];
        stickyLinks = [...stickyHeader.querySelectorAll('.cocoon-click-sort')];
        originalTabIndexes = sourceLinks.map(link => link.getAttribute('tabindex'));
        originalAriaHidden = sourceLinks.map(link => link.getAttribute('aria-hidden'));
        originalInert = sourceLinks.map(link => link.hasAttribute('inert'));
        sourceHeaders = sourceLinks.map(link => link.closest('th'));
        originalHeaderLabels = sourceHeaders.map(header => header.getAttribute('aria-label'));
      }

      function adminTop() {
        const bar = doc.getElementById('wpadminbar');
        if (!bar || !['fixed', 'sticky'].includes(win.getComputedStyle(bar).position)) {return 0;}
        return Math.max(0, bar.getBoundingClientRect().bottom);
      }

      // 見出し表示の切り替えに伴う並べ替えリンクのフォーカス経路
      function showHeader(visible, originalInView) {
        if (!stickyHeader || visible === headerVisible) {return;}
        headerVisible = visible;
        if (visible) {
          stickyHeader.hidden = false;
          const focused = sourceLinks.indexOf(doc.activeElement);
          if (focused !== -1) {stickyLinks[focused].focus({preventScroll: true});}
          // 複製側への操作移動後も残す元の列見出し名と、読み上げ対象から外す元リンク
          sourceLinks.forEach((link, linkIndex) => {
            const label = link.querySelector('span');
            sourceHeaders[linkIndex].setAttribute('aria-label', (label ? label.textContent : link.textContent).trim());
            link.setAttribute('tabindex', '-1');
            link.setAttribute('aria-hidden', 'true');
            link.setAttribute('inert', '');
          });
        } else {
          sourceLinks.forEach((link, linkIndex) => {
            if (!originalInert[linkIndex]) {link.removeAttribute('inert');}
            if (originalAriaHidden[linkIndex] === null) {link.removeAttribute('aria-hidden');}
            else {link.setAttribute('aria-hidden', originalAriaHidden[linkIndex]);}
            if (originalTabIndexes[linkIndex] === null) {link.removeAttribute('tabindex');}
            else {link.setAttribute('tabindex', originalTabIndexes[linkIndex]);}
            if (originalHeaderLabels[linkIndex] === null) {sourceHeaders[linkIndex].removeAttribute('aria-label');}
            else {sourceHeaders[linkIndex].setAttribute('aria-label', originalHeaderLabels[linkIndex]);}
          });
          const focused = stickyLinks.indexOf(doc.activeElement);
          if (focused !== -1) {(originalInView ? sourceLinks[focused] : viewport).focus({preventScroll: true});}
          stickyHeader.hidden = true;
        }
      }

      function update() {
        const overflow = viewport.clientWidth > 0 && viewport.scrollWidth > viewport.clientWidth + 1;
        // 表全体が収まる幅へ変わった際の進行中アニメーションと位置のリセット
        if (!overflow) {viewport.scrollLeft = 0;}
        // RTLのscrollLeftの符号に依存しない、表と表示領域の座標比較
        const bounds = viewport.getBoundingClientRect();
        const content = table.getBoundingClientRect();
        const edge = bounds.left + viewport.clientLeft;
        const canLeft = overflow && content.left < edge - 1;
        const canRight = overflow && content.right > edge + viewport.clientWidth + 1;
        // リサイズによる操作部の非表示時のフォーカス退避
        if (!overflow && controls.contains(doc.activeElement)) {viewport.focus({preventScroll: true});}
        controls.hidden = !overflow;
        if (overflow) {viewport.setAttribute('aria-describedby', hint.id);}
        else {viewport.removeAttribute('aria-describedby');}
        frame.classList.toggle('can-scroll-left', canLeft);
        frame.classList.toggle('can-scroll-right', canRight);
        left.setAttribute('aria-disabled', String(!canLeft));
        right.setAttribute('aria-disabled', String(!canRight));

        // WordPress管理バーと表の両端を基準にした追従表示の範囲
        const top = adminTop();
        controls.style.top = top + 'px';
        const panelBounds = panel.getBoundingClientRect();
        const frameBounds = frame.getBoundingClientRect();
        const controlsBounds = controls.getBoundingClientRect();
        const controlBottom = overflow ? controlsBounds.bottom : top;
        const stickyTop = Math.max(top, controlBottom);
        const stuck = overflow && panelBounds.top < top - 1 && controlsBounds.top <= top + 1 && frameBounds.bottom > top + controlsBounds.height;
        controls.classList.toggle('is-stuck', stuck);
        if (stickyHeader) {
          const headBounds = sourceHead.getBoundingClientRect();
          const visible = headBounds.bottom <= stickyTop + 1 && frameBounds.bottom > stickyTop + headBounds.height && bounds.width > 0;
          showHeader(visible, headBounds.bottom > stickyTop);
          if (visible) {
            // 元の表と複製見出しの列幅・横位置の同期
            const tableBounds = table.getBoundingClientRect();
            stickyHeader.style.top = stickyTop + 'px';
            stickyHeader.style.left = bounds.left + 'px';
            stickyHeader.style.width = bounds.width + 'px';
            stickyHeader.style.height = Math.ceil(headBounds.height + 2) + 'px';
            stickyTrack.style.width = tableBounds.width + 'px';
            stickyTrack.style.transform = 'translateX(' + (tableBounds.left - bounds.left) + 'px)';
            stickyTable.style.width = tableBounds.width + 'px';
          }
        }
      }

      // 連続するスクロールイベントの描画フレーム単位での集約
      let scheduled = false;
      function schedule() {
        if (scheduled) {return;}
        scheduled = true;
        win.requestAnimationFrame(() => {scheduled = false; update();});
      }
      function move(button, direction) {
        button.addEventListener('click', () => {
          update();
          if (button.getAttribute('aria-disabled') === 'true') {return;}
          const reduced = win.matchMedia && win.matchMedia('(prefers-reduced-motion: reduce)').matches;
          viewport.scrollBy({left: direction * viewport.clientWidth * 0.8, behavior: reduced ? 'auto' : 'smooth'});
        });
      }
      move(left, -1);
      move(right, 1);
      viewport.addEventListener('scroll', schedule, {passive: true});
      win.addEventListener('scroll', schedule, {passive: true});
      win.addEventListener('resize', schedule);
      win.addEventListener('pageshow', schedule);
      // サイドバー開閉や非表示パネルの展開も含む幅変化の検出
      if (win.ResizeObserver) {
        const observer = new win.ResizeObserver(schedule);
        observer.observe(viewport);
        observer.observe(table);
        observer.observe(controls);
        if (sourceHead) {observer.observe(sourceHead);}
      }
      update();
    });
  }
  return {init: init};
});
