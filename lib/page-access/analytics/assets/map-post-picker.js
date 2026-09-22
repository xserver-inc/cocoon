/* eslint-env browser, node */
(function (root, factory) {
  'use strict';
  const api = factory();
  if (typeof module === 'object' && module.exports) {module.exports = api;}
  if (root && root.document) {
    const start = () => api.init(root.document, root.CocoonAnalytics || {});
    if (root.document.readyState === 'loading') {
      root.document.addEventListener('DOMContentLoaded', start, {once: true});
    } else {start();}
  }
})(typeof window !== 'undefined' ? window : null, () => {
  'use strict';

  function buildUrl(base, nonce, filters) {
    const url = new URL(base);
    url.searchParams.set('action', 'cocoon_analytics_map_post_candidates');
    url.searchParams.set('nonce', nonce);
    ['period', 'from', 'to', 'device', 'q'].forEach((key) => {
      url.searchParams.set(key, String(filters[key] || ''));
    });
    return url.toString();
  }

  function init(documentObject, config) {
    const root = documentObject.defaultView;
    const ajax = config.ajax || {};
    const i18n = config.i18n || {};
    documentObject.querySelectorAll('.cocoon-map-picker').forEach((picker) => {
      if (picker.dataset.ready === 'true') {return;}
      const form = picker.closest('form');
      if (!form) {return;}
      picker.dataset.ready = 'true';
      const trigger = picker.querySelector('.cocoon-map-picker-trigger');
      const value = picker.querySelector('.cocoon-map-picker-value');
      const hidden = picker.querySelector('input[type="hidden"]');
      const clear = picker.querySelector('.cocoon-map-picker-clear');
      const panel = picker.querySelector('.cocoon-map-picker-panel');
      const search = picker.querySelector('input[type="search"]');
      const list = picker.querySelector('.cocoon-map-picker-list');
      const heading = picker.querySelector('.cocoon-map-picker-heading-text');
      const context = picker.querySelector('.cocoon-map-picker-context');
      const status = picker.querySelector('.cocoon-map-picker-status');
      const retry = picker.querySelector('.cocoon-map-picker-retry');
      let sequence = 0;
      let debounce = null;
      let controller = null;

      // 閉じたパネルや古い検索への応答を無効化する世代番号
      function cancel() {
        sequence++;
        root.clearTimeout(debounce);
        if (controller) {controller.abort();}
        controller = null;
      }

      function close(restoreFocus) {
        cancel();
        panel.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
        list.setAttribute('aria-busy', 'false');
        if (restoreFocus) {trigger.focus();}
      }

      function message(text, failed) {
        list.replaceChildren();
        status.textContent = text;
        retry.hidden = !failed;
      }

      function filters() {
        const result = {q: search.value.trim()};
        ['period', 'from', 'to', 'device'].forEach((key) => {
          const field = form.elements.namedItem(key);
          result[key] = field ? field.value : '';
        });
        return result;
      }

      function choose(item) {
        hidden.value = String(item.id);
        value.textContent = item.name;
        clear.hidden = false;
        close(true);
      }

      // 画面端からはみ出さない表示方向と利用可能な高さの算出
      function positionPanel() {
        const rect = trigger.getBoundingClientRect();
        const below = root.innerHeight - rect.bottom - 20;
        const above = rect.top - 20;
        const useAbove = root.innerWidth > 782 && below < 260 && above > below;
        panel.classList.toggle('opens-above', useAbove);
        panel.style.setProperty('--cocoon-map-picker-available-height', Math.max(140, Math.floor(useAbove ? above : below)) + 'px');
      }

      function render(items, noClicks) {
        list.replaceChildren();
        items.forEach((item) => {
          const row = documentObject.createElement('li');
          const button = documentObject.createElement('button');
          button.type = 'button';
          button.className = 'cocoon-map-picker-item';
          button.title = item.name;
          if (String(item.id) === hidden.value) {
            button.classList.add('is-current');
            button.setAttribute('aria-current', 'true');
          }
          if (item.rank) {
            const rank = documentObject.createElement('span');
            rank.className = 'cocoon-map-picker-rank';
            rank.textContent = String(item.rank);
            button.appendChild(rank);
          }
          const body = documentObject.createElement('span');
          body.className = 'cocoon-map-picker-item-body';
          const title = documentObject.createElement('span');
          title.className = 'cocoon-map-picker-title';
          title.textContent = item.name;
          body.appendChild(title);
          if (!item.rank && item.meta) {
            const meta = documentObject.createElement('small');
            meta.textContent = item.meta;
            body.appendChild(meta);
          }
          if (item.has_clicks === false) {
            const note = documentObject.createElement('small');
            note.className = 'cocoon-map-picker-no-clicks';
            note.textContent = noClicks;
            body.appendChild(note);
          }
          button.appendChild(body);
          if (item.pv !== undefined) {
            const pv = documentObject.createElement('span');
            pv.className = 'cocoon-map-picker-pv';
            pv.textContent = String(item.pv) + ' ' + (i18n.pv || 'PV');
            button.appendChild(pv);
          }
          button.addEventListener('click', () => choose(item));
          row.appendChild(button);
          list.appendChild(row);
        });
        status.textContent = items.length
          ? (i18n.post_candidate_count || '%s results').replace('%s', String(items.length))
          : (search.value.trim() ? (i18n.no_matching_items || 'No matching items') : (i18n.no_popular_posts || 'No views in this period. Search for an article.'));
      }

      async function load() {
        cancel();
        if (panel.hidden) {return;}
        const request = sequence;
        const params = filters();
        heading.textContent = params.q ? (i18n.post_results || 'Search results') : (i18n.popular_posts || 'Top 10 articles');
        context.textContent = '';
        message(i18n.loading || 'Loading...', false);
        list.setAttribute('aria-busy', 'true');
        controller = typeof root.AbortController === 'function' ? new root.AbortController() : null;
        const currentController = controller;
        let timeout;
        try {
          if (!ajax.url || !ajax.post_picker_nonce || typeof root.fetch !== 'function') {throw new Error('unavailable');}
          // 応答しない通信も再試行へ戻すための制限時間
          const response = await Promise.race([
            root.fetch(buildUrl(ajax.url, ajax.post_picker_nonce, params), {
              credentials: 'same-origin', signal: currentController ? currentController.signal : undefined,
            }).then(async (res) => {
              const payload = await res.json();
              if (!res.ok || !payload.success || !payload.data || !Array.isArray(payload.data.items)) {
                throw new Error(payload.data && payload.data.message || 'response');
              }
              return payload.data;
            }),
            new Promise((resolve, reject) => {
              timeout = root.setTimeout(() => {
                reject(new Error('timeout'));
                if (currentController) {currentController.abort();}
              }, 15000);
            }),
          ]);
          if (request !== sequence || panel.hidden) {return;}
          const items = response.items.filter((item) => Number.isInteger(item.id) && item.id > 0 && typeof item.name === 'string').slice(0, params.q ? 20 : 10);
          context.textContent = response.from + ' 〜 ' + response.to + (params.q ? '' : ' · ' + (i18n.pv_all_devices || 'PV (all devices)'));
          render(items, response.no_clicks || '');
        } catch (error) {
          if (request !== sequence || panel.hidden) {return;}
          message(error.message === 'invalid_period'
            ? (i18n.invalid_period || 'Enter a valid date range.')
            : (i18n.connection_error || 'Connection error'), true);
        } finally {
          root.clearTimeout(timeout);
          if (request === sequence) {list.setAttribute('aria-busy', 'false');}
        }
      }

      function open() {
        panel.hidden = false;
        trigger.setAttribute('aria-expanded', 'true');
        search.value = '';
        positionPanel();
        // 検索を選ぶまで画面内キーボードを開かないためのパネルへのフォーカス
        panel.focus();
        load();
      }

      trigger.addEventListener('click', () => panel.hidden ? open() : close(true));
      trigger.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowDown' && panel.hidden) {event.preventDefault(); open();}
      });
      picker.querySelector('.cocoon-map-picker-close').addEventListener('click', () => close(true));
      retry.addEventListener('click', load);
      clear.addEventListener('click', () => {
        hidden.value = '0';
        value.textContent = picker.dataset.placeholder;
        clear.hidden = true;
        close(true);
      });
      search.addEventListener('input', () => {
        cancel();
        message(i18n.loading || 'Loading...', false);
        debounce = root.setTimeout(load, 250);
      });
      picker.addEventListener('keydown', (event) => {
        // 日本語入力の変換確定や候補移動を妨げないためのIME操作の除外
        if (panel.hidden || event.isComposing) {return;}
        if (event.key === 'Escape') {event.preventDefault(); close(true); return;}
        if (event.key === 'Enter' && event.target === search) {event.preventDefault(); return;}
        if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp') {return;}
        const buttons = Array.from(list.querySelectorAll('button'));
        if (!buttons.length) {return;}
        event.preventDefault();
        const index = buttons.indexOf(documentObject.activeElement);
        const next = index < 0 ? (event.key === 'ArrowDown' ? 0 : buttons.length - 1) : (index + (event.key === 'ArrowDown' ? 1 : -1) + buttons.length) % buttons.length;
        buttons[next].focus();
        buttons[next].scrollIntoView({block: 'nearest'});
      });
      form.addEventListener('change', (event) => {
        if (['period', 'from', 'to', 'device'].indexOf(event.target.name) !== -1) {
          cancel();
          if (!panel.hidden) {load();}
        }
      });
      documentObject.addEventListener('pointerdown', (event) => {
        if (!panel.hidden && !picker.contains(event.target)) {close(false);}
      });
      documentObject.addEventListener('focusin', (event) => {
        if (!panel.hidden && !picker.contains(event.target)) {close(false);}
      });
      root.addEventListener('resize', () => {
        if (!panel.hidden) {positionPanel();}
      });
    });
  }
  return {init: init, buildUrl: buildUrl};
});
