/* eslint-disable prefer-arrow-callback */
(function (root, factory) {
  'use strict';
  const result = factory(root);
  if (typeof module === 'object' && module.exports) {
    module.exports = result.test;
  }
  if (root) {
    root.CocoonClickAnalytics = result.publicApi;
  }
})(typeof window !== 'undefined' ? window : null, function (win) {
  'use strict';

  function clamp(value, min, max) {
    return Math.max(min, Math.min(max, value));
  }

  function coordinateBasisPoints(position, total) {
    if (!Number.isFinite(position) || !Number.isFinite(total) || total <= 0) {return 0;}
    return Math.round(clamp(position / total, 0, 1) * 10000);
  }

  function deviceType(width) {
    if (width <= 767) {return 'mobile';}
    if (width <= 1023) {return 'tablet';}
    return 'desktop';
  }

  // ページごとに表示を抽出します。
  function shouldSample(rate, randomValue) {
    return randomValue < clamp(Number(rate) || 0, 0, 100) / 100;
  }

  function privacySignalEnabled(respectPrivacy, navigatorObject) {
    const browser = navigatorObject || {};
    return Boolean(respectPrivacy && (browser.globalPrivacyControl === true || browser.doNotTrack === '1'));
  }

  function destinationKind(rawHref, siteHosts, download, currentUrl) {
    const value = String(rawHref || '').trim();
    if (!value) {return 'invalid';}
    if (value.charAt(0) === '#') {return 'anchor';}
    const lower = value.toLowerCase();
    if (/^(mailto|tel|sms):/.test(lower)) {return lower.split(':')[0];}
    if (/^(javascript|data|blob|file):/.test(lower)) {return 'invalid';}
    try {
      const parsed = new URL(value, win ? win.location.href : 'https://example.invalid/');
      const current = new URL(currentUrl || (win ? win.location.href : 'https://example.invalid/'));
      if (parsed.hash && parsed.origin === current.origin && parsed.pathname === current.pathname && parsed.search === current.search) {return 'anchor';}
      if (download || /\.(zip|pdf|docx?|xlsx?|pptx?|csv|epub|mp3|mp4|mov|webm)$/i.test(parsed.pathname)) {return 'download';}
      return (siteHosts || []).map(function (host) {return String(host).toLowerCase();}).indexOf(parsed.hostname.toLowerCase()) >= 0 ? 'internal' : 'external';
    } catch (error) {
      return 'invalid';
    }
  }

  function normalizeComparableUrl(value, base) {
    try {
      const url = new URL(value, base);
      return url.protocol.toLowerCase() + '//' + url.host.toLowerCase() + url.pathname.replace(/\/+$/, '') + url.search;
    } catch (error) {
      return '';
    }
  }

  // UTF-8の送信サイズを求めます。
  function utf8Length(value) {
    if (typeof TextEncoder !== 'undefined') {return new TextEncoder().encode(value).length;}
    let bytes = 0;
    for (let index = 0; index < value.length; index += 1) {
      const code = value.charCodeAt(index);
      if (code < 0x80) {bytes += 1;}
      else if (code < 0x800) {bytes += 2;}
      else if (code >= 0xD800 && code <= 0xDBFF && index + 1 < value.length) {bytes += 4; index += 1;}
      else {bytes += 3;}
    }
    return bytes;
  }

  // 1回の送信をイベント50件・約30KB以内へ分割します。
  function splitBatches(events, maxEvents, maxBytes, envelope) {
    const batches = [];
    let current = [];
    (events || []).forEach(function (event) {
      const candidate = current.concat([event]);
      const bytes = utf8Length(JSON.stringify(Object.assign({}, envelope || {}, {events: candidate})));
      if (current.length && (candidate.length > maxEvents || bytes > maxBytes)) {
        batches.push(current);
        current = [event];
      } else {
        current = candidate;
      }
    });
    if (current.length) {batches.push(current);}
    return batches;
  }

  const test = {
    clamp: clamp,
    coordinateBasisPoints: coordinateBasisPoints,
    deviceType: deviceType,
    shouldSample: shouldSample,
    privacySignalEnabled: privacySignalEnabled,
    destinationKind: destinationKind,
    normalizeComparableUrl: normalizeComparableUrl,
    utf8Length: utf8Length,
    splitBatches: splitBatches
  };

  if (!win || !win.document) {
    return {test: test, publicApi: {setConsent: function () {}}};
  }

  const doc = win.document;
  const config = win.CocoonClickAnalyticsConfig || {};
  const storagePrefix = 'cocoon_click_analytics_';
  const pendingKey = storagePrefix + 'pending_internal';
  const timers = new Map();
  const retries = new Set();
  let consentVersion = 0;
  let occurrenceDirty = true;
  let occurrenceCache = new WeakMap();
  let headingCache = new WeakMap();
  let impressed = new WeakMap();
  let sampledClicks = new WeakMap();
  let flushTimer = null;
  const impressionBuffer = [];
  const recentClicks = new Map();
  // 滞在時間には、端末時計の変更で逆戻りしない時計を使います。
  function elapsedTime() {
    return win.performance && typeof win.performance.now === 'function' ? win.performance.now() : Date.now();
  }
  const pageStartedAt = elapsedTime();
  let observer = null;
  let mutationObserver = null;
  let started = false;
  let pageSampleSent = false;
  let outcomeState = null;

  function randomId() {
    if (win.crypto && typeof win.crypto.getRandomValues === 'function') {
      const bytes = new Uint8Array(16);
      win.crypto.getRandomValues(bytes);
      return Array.prototype.map.call(bytes, function (byte) {return byte.toString(16).padStart(2, '0');}).join('');
    }
    return String(Date.now()) + Math.random().toString(36).slice(2) + Math.random().toString(36).slice(2);
  }

  function sessionValue(key) {
    try {return win.sessionStorage.getItem(key) || '';} catch (error) {return '';}
  }

  function setSessionValue(key, value) {
    try {win.sessionStorage.setItem(key, value);} catch (error) { /* 保存不可でも計測を続けます。 */ }
  }

  function removeSessionValue(key) {
    try {win.sessionStorage.removeItem(key);} catch (error) { /* 保存不可でも計測を続けます。 */ }
  }

  function sessionId() {
    const idKey = storagePrefix + 'session';
    const timeKey = storagePrefix + 'session_time';
    const now = Date.now();
    let id = sessionValue(idKey);
    const previous = Number(sessionValue(timeKey)) || 0;
    if (!id || now - previous > 30 * 60 * 1000) {id = randomId();}
    setSessionValue(idKey, id);
    setSessionValue(timeKey, String(now));
    return id;
  }

  let currentSessionId = null;
  const sampledPage = Boolean(config.impressions) && shouldSample(config.samplingRate, Math.random());
  const privacySignal = privacySignalEnabled(config.respectPrivacy, win.navigator);
  let consentGranted = !privacySignal && (config.initialConsent === true || config.initialConsent === '1');

  function baseEnvelope(context) {
    const source = context || config;
    return {
      batch_id: randomId(),
      session_id: source.sessionId || currentSessionId,
      source_post_id: Number(source.sourcePostId || source.source_post_id) || 0,
      layout_revision: source.layoutRevision || source.layout_revision || '',
      sampling_rate: Number(source.samplingRate || source.sampling_rate) || 10,
      token: source.token || '',
      device: source.device || deviceType(win.innerWidth || doc.documentElement.clientWidth || 1024)
    };
  }

  function transmit(events, context) {
    if (!consentGranted || !events || !events.length) {return;}
    const envelope = baseEnvelope(context);
    const version = consentVersion;
    splitBatches(events, 50, 30000, envelope).forEach(function (batch) {
      const payload = Object.assign({}, envelope, {batch_id: randomId(), events: batch});
      const body = JSON.stringify(payload);
      // 同じバッチIDで再送し、重複を防ぎます。
      function send(attempt) {
        if (!consentGranted || version !== consentVersion) {return;}
        if (win.fetch) {
          win.fetch(config.endpoint, {method: 'POST', body: body, credentials: 'same-origin', keepalive: true, headers: {'Content-Type': 'text/plain;charset=UTF-8'}})
            .then(function (response) {if (response.status >= 500 || response.status === 429) {throw new Error('retry');}})
            .catch(function () {
              if (attempt >= 2 || !consentGranted || version !== consentVersion || doc.hidden) {return;}
              const timer = win.setTimeout(function () {retries.delete(timer); send(attempt + 1);}, 1000 * Math.pow(2, attempt));
              retries.add(timer);
            });
        } else if (win.navigator.sendBeacon) {
          try {win.navigator.sendBeacon(config.endpoint, new Blob([body], {type: 'text/plain;charset=UTF-8'}));} catch (error) { /* 送信失敗を無視します。 */ }
        }
      }
      send(0);
    });
  }

  function areaFor(anchor) {
    const override = anchor.closest('[data-cocoon-click-area]');
    if (override) {return String(override.getAttribute('data-cocoon-click-area') || 'other').toLowerCase();}
    if (anchor.closest('.toc')) {return 'toc';}
    if (anchor.closest('.internal-blogcard-wrap,.external-blogcard-wrap')) {return 'blogcard';}
    if (anchor.closest('.related-entry-card-wrap,.related-list')) {return 'related';}
    if (anchor.closest('.wp-block-button,.btn-wrap,.ranking-item-link-buttons,.cta-box')) {return 'cta';}
    if (anchor.closest('#navi,.navi')) {return 'navi';}
    if (anchor.closest('#header-container,#header')) {return 'header';}
    if (anchor.closest('#sidebar,.sidebar')) {return 'sidebar';}
    if (anchor.closest('#footer,.footer')) {return 'footer';}
    if (anchor.closest('.mobile-menu-buttons')) {return 'mobile_menu';}
    if (anchor.closest('.entry-content')) {return 'content';}
    return 'other';
  }

  function headingFor(anchor) {
    if (!headingCache.has(anchor)) {indexOccurrences();}
    return headingCache.get(anchor) || '';
  }

  function indexOccurrences() {
    occurrenceDirty = false;
    const counts = {};
    let currentHeading = '';
    occurrenceCache = new WeakMap();
    headingCache = new WeakMap();
    doc.querySelectorAll('.entry-content h2,.entry-content h3,.entry-content h4,.entry-content h5,.entry-content h6,a[href]').forEach(function (candidate) {
      if (candidate.matches && candidate.matches('h2,h3,h4,h5,h6')) {
        currentHeading = String(candidate.textContent || '').trim().slice(0, 191);
        return;
      }
      const candidateArea = areaFor(candidate);
      const occurrence = counts[candidateArea] || 0;
      occurrenceCache.set(candidate, occurrence);
      headingCache.set(candidate, candidate.closest('.entry-content') ? currentHeading : '');
      counts[candidateArea] = occurrence + 1;
    });
  }

  function occurrenceFor(anchor) {
    if (!occurrenceCache.has(anchor)) {indexOccurrences();}
    return occurrenceCache.get(anchor) || 0;
  }

  function classificationHint(anchor) {
    const override = anchor.closest('[data-cocoon-click-type]');
    const hinted = override ? String(override.getAttribute('data-cocoon-click-type') || '').toLowerCase() : '';
    if (hinted === 'official' || hinted === 'reference') {return hinted;}
    if (anchor.closest('blockquote,cite,.citation,.reference,.references,.source-link,.footnote')) {return 'reference';}
    if (anchor.closest('.official-link,.official-site')) {return 'official';}
    return '';
  }

  function linkMetadata(anchor) {
    // 変更後の属性と掲載場所を使います。
    if (occurrenceDirty) {indexOccurrences();}
    const rawHref = anchor.getAttribute('href') || '';
    const kind = destinationKind(rawHref, config.siteHosts || [], anchor.hasAttribute('download'), win.location.href);
    const area = areaFor(anchor);
    const customLabel = anchor.closest('[data-cocoon-click-label]');
    const image = anchor.querySelector('img');
    let label = customLabel ? customLabel.getAttribute('data-cocoon-click-label') : (anchor.getAttribute('aria-label') || anchor.textContent || (image ? image.getAttribute('alt') : '') || '');
    label = String(label).replace(/\s+/g, ' ').trim().slice(0, 191);
    let element = 'text';
    if (anchor.closest('.internal-blogcard-wrap,.external-blogcard-wrap')) {element = 'blogcard';}
    else if (anchor.closest('.wp-block-button,.btn-wrap,.ranking-item-link-buttons,.cta-box') || anchor.classList.contains('btn')) {element = 'button';}
    else if (image) {element = 'image';}
    const meta = {
      href: rawHref,
      kind: kind,
      area: area,
      heading: headingFor(anchor),
      occurrence: occurrenceFor(anchor),
      element_type: element,
      label: label,
      rel: anchor.getAttribute('rel') || '',
      target_blank: anchor.getAttribute('target') === '_blank',
      download: anchor.hasAttribute('download'),
      classification_hint: classificationHint(anchor),
      is_affiliate: Boolean(anchor.closest('.affiliate-tag,.amazon-item-box,.rakuten-item-box') || /affiliate/i.test(rawHref) || /[?&](tag|ref|aff|affid|af_id|associate_id)=/i.test(rawHref))
    };
    return meta;
  }

  function isExcluded(meta) {
    if (meta.kind === 'invalid') {return true;}
    if (meta.kind === 'internal' && !config.trackInternal) {return true;}
    if (meta.kind === 'external' && !config.trackExternal) {return true;}
    if (['anchor', 'download', 'mailto', 'tel', 'sms'].indexOf(meta.kind) >= 0 && !config.trackSpecial) {return true;}
    // 末尾スラッシュを落とさない絶対URLでの照合による、サーバー側除外判定との一致
    let parsed = null;
    try {
      parsed = new URL(meta.href, win.location.href);
    } catch (error) {
      return true;
    }
    if ((config.excludedUrls || []).some(function (value) {return value && parsed.href.indexOf(value) >= 0;})) {return true;}
    const host = parsed.hostname.toLowerCase();
    if ((config.excludedDomains || []).some(function (value) {const excluded = String(value).toLowerCase().replace(/^\./, ''); return host === excluded || host.endsWith('.' + excluded);})) {return true;}
    return false;
  }

  function markPageSample(events) {
    if (sampledPage && !pageSampleSent) {
      events.unshift({type: 'page_sample'});
      pageSampleSent = true;
    }
    return events;
  }

  // 掲載情報の変更を別の表示として扱います。
  function impressionKey(meta) {
    return JSON.stringify([meta.href, meta.area, meta.heading, meta.occurrence, meta.label, meta.element_type]);
  }

  function queueImpression(anchor) {
    if (!consentGranted || doc.hidden) {return;}
    const meta = linkMetadata(anchor);
    const key = impressionKey(meta);
    if (impressed.get(anchor) === key || isExcluded(meta)) {return;}
    impressed.set(anchor, key);
    impressionBuffer.push(Object.assign({type: 'impression'}, meta));
    if (impressionBuffer.length >= 49) {flushImpressions();}
  }

  function flushImpressions() {
    if (!sampledPage || !consentGranted) {return;}
    const events = impressionBuffer.splice(0, impressionBuffer.length);
    markPageSample(events);
    if (events.length) {transmit(events);}
  }

  function pendingContext(meta) {
    return {
      expires: Date.now() + 10 * 60 * 1000,
      source: normalizeComparableUrl(win.location.href, win.location.href),
      sessionId: currentSessionId,
      device: deviceType(win.innerWidth),
      target: normalizeComparableUrl(meta.href, win.location.href),
      sourcePostId: config.sourcePostId,
      layoutRevision: config.layoutRevision,
      samplingRate: config.samplingRate,
      token: config.token,
      meta: meta
    };
  }

  const crossTabPrefix = storagePrefix + 'cross_tab_';

  // 別タブへ期限付きの到着情報を渡します。
  function crossTabEntries() {
    const entries = [];
    try {
      Object.keys(win.localStorage).filter(function (key) {return key.indexOf(crossTabPrefix) === 0;}).forEach(function (key) {
        let pending = null;
        try {pending = JSON.parse(win.localStorage.getItem(key));} catch (error) { /* 壊れた値は削除します。 */ }
        if (!pending || pending.expires < Date.now()) {win.localStorage.removeItem(key);}
        else {entries.push({key: key, pending: pending});}
      });
    } catch (error) { /* 保存不可でも計測を続けます。 */ }
    return entries.sort(function (a, b) {return a.pending.expires - b.pending.expires;});
  }

  function rememberInternalOutcome(meta, opensNewTab) {
    if (!config.outcomes || meta.kind !== 'internal') {return;}
    const pending = pendingContext(meta);
    if (!opensNewTab) {setSessionValue(pendingKey, JSON.stringify(pending)); return;}
    try {
      const entries = crossTabEntries();
      entries.slice(0, Math.max(0, entries.length - 19)).forEach(function (entry) {win.localStorage.removeItem(entry.key);});
      win.localStorage.setItem(crossTabPrefix + randomId(), JSON.stringify(pending));
    } catch (error) { /* 保存不可でも遷移を続けます。 */ }
  }

  function clickPosition(event, anchor) {
    const root = doc.documentElement;
    const body = doc.body;
    const width = Math.max(root.scrollWidth, body ? body.scrollWidth : 0, win.innerWidth || 0, 1);
    const height = Math.max(root.scrollHeight, body ? body.scrollHeight : 0, win.innerHeight || 0, 1);
    let x = Number(event.pageX);
    let y = Number(event.pageY);
    if (!x && !y) {
      const rect = anchor.getBoundingClientRect();
      x = rect.left + (rect.width / 2) + win.scrollX;
      y = rect.top + (rect.height / 2) + win.scrollY;
    }
    return {x_bp: coordinateBasisPoints(x, width), y_bp: coordinateBasisPoints(y, height)};
  }

  function handleClick(event) {
    if (!consentGranted || event.isTrusted === false || (typeof event.button === 'number' && event.button === 2)) {return;}
    const anchor = event.target && event.target.closest ? event.target.closest('a[href]') : null;
    if (!anchor) {return;}
    // 未抽出ページも掲載順を更新します。
    if (!mutationObserver) {occurrenceDirty = true;}
    const meta = linkMetadata(anchor);
    if (isExcluded(meta)) {return;}
    const key = meta.href + '|' + meta.area + '|' + meta.occurrence;
    const now = Date.now();
    if (recentClicks.has(key) && now - recentClicks.get(key) < 2000) {return;}
    recentClicks.set(key, now);
    // 長時間滞在するページでの連打抑止記録の際限ない増加を防ぐ期限切れ削除
    recentClicks.forEach(function (time, existing) {
      if (now - time >= 2000) {recentClicks.delete(existing);}
    });
    const exposureKey = impressionKey(meta);
    // CTRの成功は1表示につき最大1回です。
    const sampled = sampledPage && sampledClicks.get(anchor) !== exposureKey;
    const forcedImpression = sampled && impressed.get(anchor) !== exposureKey;
    if (sampled) {sampledClicks.set(anchor, exposureKey);}
    if (forcedImpression) {impressed.set(anchor, exposureKey);}
    const elapsed = elapsedTime() - pageStartedAt;
    const eventData = Object.assign({
      type: 'click',
      sampled: sampled,
      forced_impression: forcedImpression,
      time_to_click_ms: Math.max(0, Math.round(elapsed))
    }, meta, config.heatmap ? clickPosition(event, anchor) : {});
    // 成立済みの表示をクリックと一緒に送ります。
    const events = impressionBuffer.splice(0, impressionBuffer.length).concat([eventData]);
    markPageSample(events);
    transmit(events);
    rememberInternalOutcome(meta, meta.target_blank || event.button === 1 || event.ctrlKey || event.metaKey);
  }

  function initObserver() {
    if (!consentGranted || doc.hidden || !sampledPage || !config.impressions || observer || !('IntersectionObserver' in win)) {return;}
    // 50%以上の可視状態を1秒保つと表示を数えます。
    observer = new win.IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (impressed.get(entry.target) === impressionKey(linkMetadata(entry.target))) {return;}
        if (entry.intersectionRatio >= 0.5) {
          if (!timers.has(entry.target)) {
            timers.set(entry.target, win.setTimeout(function () {
              timers.delete(entry.target);
              if (entry.target.isConnected) {queueImpression(entry.target);}
            }, 1000));
          }
        } else if (timers.has(entry.target)) {
          win.clearTimeout(timers.get(entry.target));
          timers.delete(entry.target);
        }
      });
    }, {threshold: [0, 0.5, 1]});
    indexOccurrences();
    doc.querySelectorAll('a[href]').forEach(function (anchor) {
      const meta = linkMetadata(anchor);
      if (!isExcluded(meta)) {observer.observe(anchor);}
    });
    // 追加・変更したリンクを監視し直します。
    if ('MutationObserver' in win) {
      mutationObserver = new win.MutationObserver(function (records) {
        occurrenceDirty = true;
        const anchors = new Set();
        records.forEach(function (record) {
          const node = record.target.nodeType === 1 ? record.target : record.target.parentElement;
          if (!node) {return;}
          const parent = node.closest('a[href]');
          if (parent) {anchors.add(parent);}
          const candidates = record.type === 'childList' ? Array.from(record.addedNodes) : [node];
          candidates.forEach(function (candidate) {
            if (candidate.nodeType !== 1) {return;}
            if (candidate.matches('a[href]')) {anchors.add(candidate);}
            candidate.querySelectorAll('a[href]').forEach(function (anchor) {anchors.add(anchor);});
          });
        });
        anchors.forEach(function (anchor) {
          if (timers.has(anchor)) {win.clearTimeout(timers.get(anchor)); timers.delete(anchor);}
          observer.unobserve(anchor);
          if (!isExcluded(linkMetadata(anchor))) {observer.observe(anchor);}
        });
      });
      mutationObserver.observe(doc.body, {childList: true, subtree: true, characterData: true, attributes: true, attributeFilter: ['href', 'rel', 'target', 'download', 'aria-label', 'alt', 'class', 'data-cocoon-click-label', 'data-cocoon-click-area', 'data-cocoon-click-type']});
    }
  }

  // 非表示・同意撤回で待機中の実表示を取り消します。
  function stopObservation() {
    if (observer) {observer.disconnect(); observer = null;}
    if (mutationObserver) {mutationObserver.disconnect(); mutationObserver = null;}
    timers.forEach(function (timer) {win.clearTimeout(timer);});
    timers.clear();
  }

  function runWhenIdle(callback) {
    if (typeof win.requestIdleCallback === 'function') {win.requestIdleCallback(callback, {timeout: 2000});}
    else {win.setTimeout(callback, 0);}
  }

  function sendOutcome(engaged) {
    if (!outcomeState || outcomeState.sent || !consentGranted) {return;}
    if (engaged && doc.hidden) {return;}
    outcomeState.sent = true;
    win.clearTimeout(outcomeState.timer);
    const eventData = Object.assign({}, outcomeState.pending.meta, {type: 'internal_outcome', engaged: Boolean(engaged)});
    transmit([eventData], outcomeState.pending);
  }

  function activateOutcome(pending) {
    if (!consentGranted || !config.outcomes || !config.trackInternal || !pending || !pending.meta || pending.expires < Date.now() || pending.target !== normalizeComparableUrl(win.location.href, win.location.href)) {return false;}
    outcomeState = {pending: pending, sent: false, visibleAt: null, visibleMs: 0};
    updateOutcomeVisibility();
    return true;
  }

  // エンゲージ判定には画面が見えている時間だけを足します。
  function updateOutcomeVisibility() {
    const state = outcomeState;
    if (!state || state.sent || !consentGranted) {return;}
    win.clearTimeout(state.timer);
    const now = elapsedTime();
    if (state.visibleAt !== null) {state.visibleMs += now - state.visibleAt;}
    state.visibleAt = doc.hidden ? null : now;
    if (!doc.hidden) {
      state.timer = win.setTimeout(function () {if (outcomeState === state) {sendOutcome(true);}}, Math.max(0, 10000 - state.visibleMs));
    }
  }

  function initInternalOutcome() {
    if (!config.outcomes || !config.trackInternal) {return;}
    let pending = null;
    try {pending = JSON.parse(sessionValue(pendingKey) || 'null');} catch (error) { /* 壊れた値は読み捨てます。 */ }
    removeSessionValue(pendingKey);
    if (activateOutcome(pending)) {return;}
    const claim = function () {
      if (!consentGranted) {return;}
      const source = normalizeComparableUrl(doc.referrer, win.location.href);
      const target = normalizeComparableUrl(win.location.href, win.location.href);
      if (!doc.referrer) {return;}
      const entry = crossTabEntries().find(function (item) {return item.pending.source === source && item.pending.target === target;});
      if (!entry) {return;}
      try {win.localStorage.removeItem(entry.key);} catch (error) {return;}
      activateOutcome(entry.pending);
    };
    // 到着情報をタブ間で排他制御します。
    if (win.navigator.locks) {win.navigator.locks.request(storagePrefix + 'arrival', claim).catch(function () {});}
    else {claim();}
  }

  function start() {
    if (!consentGranted || !config.endpoint || !config.sourcePostId) {return;}
    if (!currentSessionId) {currentSessionId = sessionId();}
    if (sampledPage && !flushTimer) {flushTimer = win.setInterval(flushImpressions, 5000);}
    if (started) {runWhenIdle(initObserver); return;}
    started = true;
    doc.addEventListener('click', handleClick, true);
    doc.addEventListener('auxclick', handleClick, true);
    initInternalOutcome();
    win.addEventListener('scroll', function () {
      const height = Math.max(doc.documentElement.scrollHeight, doc.body ? doc.body.scrollHeight : 0, 1);
      // ファーストビューで25%が見える短い記事を即エンゲージ扱いにしないためのスクロール距離条件
      if (win.scrollY < Math.min(200, Math.max(0, height - win.innerHeight))) {return;}
      if ((win.scrollY + win.innerHeight) / height >= 0.25) {sendOutcome(true);}
    }, {passive: true});
    doc.addEventListener('visibilitychange', function () {
      updateOutcomeVisibility();
      if (doc.hidden) {flushImpressions(); stopObservation();}
      else if (consentGranted) {runWhenIdle(initObserver);}
    });
    const afterLoad = function () {runWhenIdle(initObserver);};
    if (doc.readyState === 'complete') {afterLoad();} else {win.addEventListener('load', afterLoad, {once: true});}
    win.addEventListener('pagehide', function () {
      flushImpressions();
      if (outcomeState && !outcomeState.sent) {sendOutcome(false);}
    });
  }

  const publicApi = {
    setConsent: function (granted) {
      consentGranted = granted === true && !privacySignal;
      if (consentGranted) {start();}
      else {
        // 再同意しても、撤回前の送信待ちは復活させません。
        consentVersion += 1;
        retries.forEach(function (timer) {win.clearTimeout(timer);});
        retries.clear();
        if (outcomeState) {win.clearTimeout(outcomeState.timer);}
        stopObservation();
        impressionBuffer.length = 0;
        if (flushTimer) {win.clearInterval(flushTimer); flushTimer = null;}
        crossTabEntries().filter(function (entry) {return entry.pending.sessionId === currentSessionId;}).forEach(function (entry) {
          try {win.localStorage.removeItem(entry.key);} catch (error) { /* ストレージ制限を無視します。 */ }
        });
        removeSessionValue(pendingKey);
        removeSessionValue(storagePrefix + 'session');
        removeSessionValue(storagePrefix + 'session_time');
        currentSessionId = null;
        outcomeState = null;
        impressed = new WeakMap();
        sampledClicks = new WeakMap();
        recentClicks.clear();
        pageSampleSent = false;
      }
    }
  };

  start();
  return {test: test, publicApi: publicApi};
});
