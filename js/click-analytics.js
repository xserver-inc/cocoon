/**
 * Cocoon Click Analytics - lightweight front-end collector.
 */
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

  // サンプリング率を0〜100%へ丸め、ページごとに一度だけ抽出します。
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
      return url.protocol.toLowerCase() + '//' + url.hostname.toLowerCase() + url.pathname.replace(/\/+$/, '') + url.search;
    } catch (error) {
      return '';
    }
  }

  // 日本語を含むJSONでも32KB制限を超えないようUTF-8の実バイト数を求めます。
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
  const metadataCache = new WeakMap();
  let occurrenceCache = new WeakMap();
  let headingCache = new WeakMap();
  const impressed = new WeakSet();
  const impressionBuffer = [];
  const recentClicks = new Map();
  const pageStartedAt = win.performance && typeof win.performance.now === 'function' ? win.performance.now() : Date.now();
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
    try {win.sessionStorage.setItem(key, value);} catch (error) { /* ストレージを使えないブラウザーでは送信だけ継続します。 */ }
  }

  function removeSessionValue(key) {
    try {win.sessionStorage.removeItem(key);} catch (error) { /* ストレージを使えないブラウザーでは送信だけ継続します。 */ }
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

  const currentSessionId = sessionId();
  const sampledPage = Boolean(config.impressions) && shouldSample(config.samplingRate, Math.random());
  const privacySignal = privacySignalEnabled(config.respectPrivacy, win.navigator);
  let consentGranted = !privacySignal && config.initialConsent !== false;

  function baseEnvelope(context) {
    const source = context || config;
    return {
      batch_id: randomId(),
      session_id: currentSessionId,
      source_post_id: Number(source.sourcePostId || source.source_post_id) || 0,
      layout_revision: source.layoutRevision || source.layout_revision || '',
      sampling_rate: Number(source.samplingRate || source.sampling_rate) || 10,
      token: source.token || '',
      device: deviceType(win.innerWidth || doc.documentElement.clientWidth || 1024)
    };
  }

  function transmit(events, context) {
    if (!consentGranted || !events || !events.length) {return;}
    const envelope = baseEnvelope(context);
    splitBatches(events, 50, 30000, envelope).forEach(function (batch) {
      const payload = Object.assign({}, envelope, {batch_id: randomId(), events: batch});
      const body = JSON.stringify(payload);
      let queued = false;
      if (win.navigator.sendBeacon) {
        try {queued = win.navigator.sendBeacon(config.endpoint, new Blob([body], {type: 'text/plain;charset=UTF-8'}));} catch (error) {queued = false;}
      }
      if (!queued && win.fetch) {
        win.fetch(config.endpoint, {method: 'POST', body: body, credentials: 'same-origin', keepalive: true, headers: {'Content-Type': 'text/plain;charset=UTF-8'}}).catch(function () {});
      }
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
    if (metadataCache.has(anchor)) {return metadataCache.get(anchor);}
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
      is_affiliate: Boolean(anchor.closest('.affiliate-tag,.amazon-item-box,.rakuten-item-box') || /affiliate|ref=|tag=/i.test(rawHref))
    };
    metadataCache.set(anchor, meta);
    return meta;
  }

  function isExcluded(meta) {
    if (meta.kind === 'invalid') {return true;}
    if (meta.kind === 'internal' && !config.trackInternal) {return true;}
    if (meta.kind === 'external' && !config.trackExternal) {return true;}
    if (['anchor', 'download', 'mailto', 'tel', 'sms'].indexOf(meta.kind) >= 0 && !config.trackSpecial) {return true;}
    const absolute = normalizeComparableUrl(meta.href, win.location.href);
    if ((config.excludedUrls || []).some(function (value) {return value && absolute.indexOf(value) >= 0;})) {return true;}
    try {
      const host = new URL(meta.href, win.location.href).hostname.toLowerCase();
      if ((config.excludedDomains || []).some(function (value) {const excluded = String(value).toLowerCase().replace(/^\./, ''); return host === excluded || host.endsWith('.' + excluded);})) {return true;}
    } catch (error) { /* URLとして解釈できない値はサーバーへ送りません。 */ }
    return false;
  }

  function markPageSample(events) {
    if (sampledPage && !pageSampleSent) {
      events.unshift({type: 'page_sample'});
      pageSampleSent = true;
    }
    return events;
  }

  function queueImpression(anchor) {
    if (impressed.has(anchor) || !consentGranted) {return;}
    const meta = linkMetadata(anchor);
    if (isExcluded(meta)) {return;}
    impressed.add(anchor);
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
      target: normalizeComparableUrl(meta.href, win.location.href),
      sourcePostId: config.sourcePostId,
      layoutRevision: config.layoutRevision,
      samplingRate: config.samplingRate,
      token: config.token,
      meta: meta
    };
  }

  function rememberInternalOutcome(meta, opensNewTab) {
    if (!config.outcomes || meta.kind !== 'internal') {return;}
    setSessionValue(pendingKey, JSON.stringify(pendingContext(meta)));
    if (opensNewTab) {
      // 新しいタブへsessionStorageが複製された後、元タブ側だけに残る到着情報を消します。
      win.setTimeout(function () {removeSessionValue(pendingKey);}, 1500);
    }
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
    const meta = linkMetadata(anchor);
    if (isExcluded(meta)) {return;}
    const key = meta.href + '|' + meta.area + '|' + meta.occurrence;
    const now = Date.now();
    if (recentClicks.has(key) && now - recentClicks.get(key) < 2000) {return;}
    recentClicks.set(key, now);
    const sampled = sampledPage;
    const forcedImpression = sampled && !impressed.has(anchor);
    if (forcedImpression) {impressed.add(anchor);}
    const elapsed = win.performance && typeof win.performance.now === 'function' ? win.performance.now() - pageStartedAt : Date.now() - pageStartedAt;
    const eventData = Object.assign({
      type: 'click',
      sampled: sampled,
      forced_impression: forcedImpression,
      time_to_click_ms: Math.max(0, Math.round(elapsed))
    }, meta, config.heatmap ? clickPosition(event, anchor) : {});
    const events = [eventData];
    markPageSample(events);
    transmit(events);
    rememberInternalOutcome(meta, meta.target_blank || event.button === 1 || event.ctrlKey || event.metaKey);
  }

  function initObserver() {
    if (!sampledPage || !config.impressions || observer || !('IntersectionObserver' in win)) {return;}
    // リンクが50%以上見えた状態を1秒保ったときだけ表示回数を成立させます。
    observer = new win.IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (impressed.has(entry.target)) {return;}
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
    // 初心者向け: 後から追加されたリンクだけを監視へ足し、ページ全体の再走査を避けます。
    if ('MutationObserver' in win) {
      mutationObserver = new win.MutationObserver(function (records) {
        records.forEach(function (record) {
          record.addedNodes.forEach(function (node) {
            if (!node || node.nodeType !== 1) {return;}
            const anchors = node.matches && node.matches('a[href]') ? [node] : Array.prototype.slice.call(node.querySelectorAll ? node.querySelectorAll('a[href]') : []);
            anchors.forEach(function (anchor) {
              const meta = linkMetadata(anchor);
              if (observer && !isExcluded(meta)) {observer.observe(anchor);}
            });
          });
        });
      });
      mutationObserver.observe(doc.body, {childList: true, subtree: true});
    }
  }

  function runWhenIdle(callback) {
    if (typeof win.requestIdleCallback === 'function') {win.requestIdleCallback(callback, {timeout: 2000});}
    else {win.setTimeout(callback, 0);}
  }

  function sendOutcome(engaged) {
    if (!outcomeState || outcomeState.sent || !consentGranted) {return;}
    outcomeState.sent = true;
    const eventData = Object.assign({}, outcomeState.pending.meta, {type: 'internal_outcome', engaged: Boolean(engaged)});
    transmit([eventData], outcomeState.pending);
    removeSessionValue(pendingKey);
  }

  function initInternalOutcome() {
    let pending = null;
    try {pending = JSON.parse(sessionValue(pendingKey) || 'null');} catch (error) {pending = null;}
    if (!pending || !pending.meta || pending.expires < Date.now() || pending.target !== normalizeComparableUrl(win.location.href, win.location.href)) {
      if (pending) {removeSessionValue(pendingKey);}
      return;
    }
    // 遷移先で10秒滞在または25%スクロールしたときだけエンゲージ済みとします。
    outcomeState = {pending: pending, sent: false};
    win.setTimeout(function () {sendOutcome(true);}, 10000);
    win.addEventListener('scroll', function () {
      const height = Math.max(doc.documentElement.scrollHeight, doc.body ? doc.body.scrollHeight : 0, 1);
      if ((win.scrollY + win.innerHeight) / height >= 0.25) {sendOutcome(true);}
    }, {passive: true});
  }

  function start() {
    if (!consentGranted || !config.endpoint || !config.sourcePostId) {return;}
    if (started) {runWhenIdle(initObserver); return;}
    started = true;
    doc.addEventListener('click', handleClick, true);
    doc.addEventListener('auxclick', handleClick, true);
    initInternalOutcome();
    const afterLoad = function () {runWhenIdle(initObserver);};
    if (doc.readyState === 'complete') {afterLoad();} else {win.addEventListener('load', afterLoad, {once: true});}
    win.addEventListener('pagehide', function () {
      flushImpressions();
      if (outcomeState && !outcomeState.sent) {sendOutcome(false);}
    });
  }

  const publicApi = {
    setConsent: function (granted) {
      consentGranted = Boolean(granted) && !privacySignal;
      if (consentGranted) {start();}
      else {
        if (observer) {observer.disconnect(); observer = null;}
        if (mutationObserver) {mutationObserver.disconnect(); mutationObserver = null;}
        timers.forEach(function (timer) {win.clearTimeout(timer);});
        timers.clear();
        impressionBuffer.length = 0;
      }
    }
  };

  start();
  return {test: test, publicApi: publicApi};
});
