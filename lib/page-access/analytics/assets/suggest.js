/* eslint-env browser, node */
(function (root, factory) {
  'use strict';
  const api = factory(root);
  if (typeof module === 'object' && module.exports) {
    module.exports = api;
  }
  if (root && root.document) {
    const start = function () {
      api.init(root.document, root.CocoonAnalytics || {});
    };
    if (root.document.readyState === 'loading') {
      root.document.addEventListener('DOMContentLoaded', start, {once: true});
    } else {
      start();
    }
  }
})(typeof window !== 'undefined' ? window : global, (root) => {
  'use strict';
  let generatedListId = 0;

  function normalizeKeyword(value) {
    return String(value || '').trim().toLocaleLowerCase();
  }

  function filterLocalItems(items, keyword, limit) {
    const normalized = normalizeKeyword(keyword);
    return (Array.isArray(items) ? items : []).filter((item) => {
      return normalizeKeyword(item.name).indexOf(normalized) !== -1;
    }).slice(0, limit || 50);
  }

  function buildPostSearchUrl(baseUrl, nonce, keyword) {
    const fallback = root && root.location ? root.location.href : 'http://localhost/';
    const url = new URL(baseUrl, fallback);
    url.searchParams.set('action', 'cocoon_analytics_search_posts');
    url.searchParams.set('nonce', nonce);
    url.searchParams.set('q', String(keyword || '').trim());
    return url.toString();
  }

  function init(documentObject, config) {
    const suggestData = config && config.suggest ? config.suggest : {};
    const ajax = config && config.ajax ? config.ajax : {};
    const i18n = config && config.i18n ? config.i18n : {};
    const inputs = documentObject.querySelectorAll('.cocoon-analytics-suggest-input');

    inputs.forEach((input) => {
      if (input.getAttribute('data-cocoon-suggest-ready') === 'true') {return;}
      const container = input.closest('.cocoon-analytics-suggest-container');
      if (!container) {return;}
      const hiddenInput = container.querySelector('.cocoon-analytics-suggest-hidden');
      const dropdown = container.querySelector('.cocoon-analytics-suggest-dropdown');
      const clearButton = container.querySelector('.cocoon-analytics-suggest-clear');
      if (!hiddenInput || !dropdown) {return;}
      input.setAttribute('data-cocoon-suggest-ready', 'true');
      if (!dropdown.id) {
        generatedListId++;
        dropdown.id = 'cocoon-analytics-suggest-list-' + generatedListId;
      }
      input.setAttribute('role', 'combobox');
      input.setAttribute('aria-autocomplete', 'list');
      input.setAttribute('aria-expanded', 'false');
      input.setAttribute('aria-controls', dropdown.id);
      dropdown.setAttribute('role', 'listbox');

      const type = input.getAttribute('data-type') || '';
      const isRemotePost = type === 'post';
      const sourceItems = type === 'author' ? suggestData.authors : (type === 'category' ? suggestData.categories : []);
      const minimumCharacters = Math.max(0, parseInt(input.getAttribute('data-min-chars') || '0', 10));
      const requiresSelection = input.getAttribute('data-require-selection') === 'true';
      let activeIndex = -1;
      let currentItems = [];
      let requestSequence = 0;
      let debounceTimer = null;
      let abortController = null;

      function setExpanded(expanded) {
        input.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        dropdown.style.display = expanded ? 'block' : 'none';
        if (!expanded) {
          input.removeAttribute('aria-activedescendant');
          activeIndex = -1;
        }
      }

      function updateClearButton() {
        if (clearButton) {clearButton.hidden = input.value.trim() === '';}
      }

      function renderMessage(message, className) {
        dropdown.innerHTML = '';
        const status = documentObject.createElement('span');
        status.className = className;
        status.setAttribute('role', 'status');
        status.textContent = message;
        dropdown.appendChild(status);
        currentItems = [];
        setExpanded(true);
      }

      function setActive(index) {
        const options = dropdown.querySelectorAll('[role="option"]');
        if (!options.length) {return;}
        activeIndex = (index + options.length) % options.length;
        options.forEach((option, optionIndex) => {
          const selected = optionIndex === activeIndex;
          option.classList.toggle('is-selected', selected);
          option.setAttribute('aria-selected', selected ? 'true' : 'false');
          if (selected) {
            input.setAttribute('aria-activedescendant', option.id);
            option.scrollIntoView({block: 'nearest'});
          }
        });
      }

      function selectItem(item) {
        input.value = item.name;
        input.setAttribute('data-selected-name', item.name);
        hiddenInput.value = item.id;
        input.setCustomValidity('');
        updateClearButton();
        setExpanded(false);
      }

      function renderItems(items) {
        dropdown.innerHTML = '';
        currentItems = items;
        if (!items.length) {
          renderMessage(i18n.no_matching_items || 'No matching items', 'cocoon-analytics-suggest-no-results');
          return;
        }
        items.forEach((item, index) => {
          const option = documentObject.createElement('button');
          option.type = 'button';
          option.id = dropdown.id + '-option-' + index;
          option.className = 'cocoon-analytics-suggest-item';
          option.setAttribute('role', 'option');
          option.setAttribute('aria-selected', 'false');
          option.setAttribute('data-id', item.id);

          const name = documentObject.createElement('span');
          name.className = 'cocoon-analytics-suggest-item-name';
          name.textContent = item.name;
          option.appendChild(name);
          if (item.meta) {
            const meta = documentObject.createElement('small');
            meta.className = 'cocoon-analytics-suggest-item-meta';
            meta.textContent = item.meta;
            option.appendChild(meta);
          }
          option.addEventListener('mousedown', (event) => {
            event.preventDefault();
          });
          option.addEventListener('click', () => {
            selectItem(item);
          });
          dropdown.appendChild(option);
        });
        setExpanded(true);
      }

      async function loadItems(keyword) {
        const sequence = ++requestSequence;
        if (!isRemotePost) {
          renderItems(filterLocalItems(sourceItems, keyword, Array.isArray(sourceItems) ? sourceItems.length : 50));
          return;
        }
        if (normalizeKeyword(keyword).length < minimumCharacters) {
          setExpanded(false);
          return;
        }
        if (!ajax.url || !ajax.post_picker_nonce || typeof root.fetch !== 'function') {
          renderMessage(i18n.connection_error || 'Connection error', 'cocoon-analytics-suggest-no-results');
          return;
        }
        if (abortController) {abortController.abort();}
        abortController = typeof root.AbortController === 'function' ? new root.AbortController() : null;
        renderMessage(i18n.search_posts || 'Searching...', 'cocoon-analytics-suggest-no-results');
        try {
          const response = await root.fetch(buildPostSearchUrl(ajax.url, ajax.post_picker_nonce, keyword), {
            credentials: 'same-origin',
            signal: abortController ? abortController.signal : undefined,
          });
          const payload = await response.json();
          if (sequence !== requestSequence) {return;}
          const items = payload && payload.success && payload.data && Array.isArray(payload.data.items) ? payload.data.items : [];
          renderItems(items);
        } catch (error) {
          if (error && error.name === 'AbortError') {return;}
          if (sequence !== requestSequence) {return;}
          renderMessage(i18n.connection_error || 'Connection error', 'cocoon-analytics-suggest-no-results');
        }
      }

      function scheduleLoad(keyword) {
        if (debounceTimer) {root.clearTimeout(debounceTimer);}
        debounceTimer = root.setTimeout(() => {
          loadItems(keyword);
        }, isRemotePost ? 250 : 0);
      }

      input.addEventListener('input', () => {
        requestSequence++;
        if (abortController) {abortController.abort();}
        hiddenInput.value = '0';
        input.removeAttribute('data-selected-name');
        input.setCustomValidity('');
        updateClearButton();
        if (input.value.trim() === '') {
          setExpanded(false);
          return;
        }
        scheduleLoad(input.value);
      });

      input.addEventListener('focus', () => {
        if (!isRemotePost || (hiddenInput.value === '0' && input.value.trim() !== '')) {
          scheduleLoad(input.value);
        }
      });

      input.addEventListener('keydown', (event) => {
        const options = dropdown.querySelectorAll('[role="option"]');
        if (event.key === 'ArrowDown' && options.length) {
          event.preventDefault();
          setActive(activeIndex + 1);
        } else if (event.key === 'ArrowUp' && options.length) {
          event.preventDefault();
          setActive(activeIndex - 1);
        } else if (event.key === 'Enter' && activeIndex >= 0 && currentItems[activeIndex]) {
          event.preventDefault();
          selectItem(currentItems[activeIndex]);
        } else if (event.key === 'Escape') {
          setExpanded(false);
        }
      });

      input.addEventListener('blur', () => {
        root.setTimeout(() => {
          setExpanded(false);
          if (isRemotePost) {return;}
          const keyword = normalizeKeyword(input.value);
          const match = Array.isArray(sourceItems) && sourceItems.find((item) => {
            return normalizeKeyword(item.name) === keyword;
          });
          if (match) {
            selectItem(match);
          } else if (keyword === '' || hiddenInput.value === '0') {
            input.value = '';
            hiddenInput.value = '0';
            updateClearButton();
          }
        }, 200);
      });

      if (clearButton) {
        clearButton.addEventListener('click', () => {
          requestSequence++;
          if (abortController) {abortController.abort();}
          input.value = '';
          hiddenInput.value = '0';
          input.removeAttribute('data-selected-name');
          input.setCustomValidity('');
          updateClearButton();
          setExpanded(false);
          input.focus();
        });
      }

      const form = input.closest('form');
      if (form && requiresSelection) {
        form.addEventListener('submit', (event) => {
          if (input.value.trim() !== '' && parseInt(hiddenInput.value || '0', 10) === 0) {
            event.preventDefault();
            input.setCustomValidity(i18n.select_post_result || 'Select an item from the results.');
            input.reportValidity();
            scheduleLoad(input.value);
          }
        });
      }
      updateClearButton();
    });
  }

  return {
    buildPostSearchUrl: buildPostSearchUrl,
    filterLocalItems: filterLocalItems,
    init: init,
    normalizeKeyword: normalizeKeyword,
  };
});
