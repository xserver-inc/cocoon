'use strict';

const assert = require('assert');
const analytics = require('../../js/click-analytics.js');

const previewImage = (currentSrc, attributes = {}) => ({currentSrc, getAttribute: key => attributes[key] || null});
assert.strictEqual(analytics.imagePreviewUrl(null, 'https://example.org/'), '');
assert.strictEqual(analytics.imagePreviewUrl(previewImage('https://example.org/selected.png?w=320&id=123#fragment', {src: '/fallback.png'}), 'https://example.org/'), 'https://example.org/selected.png?w=320&id=123#fragment');
assert.strictEqual(analytics.imagePreviewUrl(previewImage('data:image/png;base64,xxx', {'data-src': '/lazy.png?h=180'}), 'https://example.org/post'), 'https://example.org/lazy.png?h=180');
assert.strictEqual(analytics.imagePreviewUrl(previewImage('', {src: 'javascript:alert(1)'}), 'https://example.org/'), '');
assert.strictEqual(analytics.imagePreviewUrl(previewImage('', {src: 'https://user:secret@example.org/private.png'}), 'https://example.org/'), '');
assert.strictEqual(analytics.imagePreviewUrl(previewImage('https://example.org/placeholder.gif', {'data-src': '/actual.jpg?id=123'}), 'https://example.org/'), 'https://example.org/actual.jpg?id=123');
assert.strictEqual(analytics.imagePreviewUrl(previewImage('https://example.org/placeholder.gif', {'data-original': '/actual.jpg'}), 'https://example.org/'), 'https://example.org/actual.jpg');
assert.strictEqual(analytics.imagePreviewUrl(previewImage('', {'data-lazy-src': '/actual.jpg'}), 'https://example.org/'), 'https://example.org/actual.jpg');
for (const url of ['https://example.org/image.php?id=123&w=320', 'https://www.google.com/s2/favicons?domain=example.org', 'https://example.org/resize?url=https%3A%2F%2Fexample.org%2Fphoto.png&fit=contain']) {
  assert.strictEqual(analytics.imagePreviewUrl(previewImage(url), 'https://example.org/'), url);
}
for (const query of ['token=private', 'X-Amz-Signature=private', 'api_key=private', '%74oken=private', '%74oken=%FF', 'key[]=private', 'jwt=private', 'url=https%3A%2F%2Fuser%3Asecret%40example.org%2Fa.png', 'url=https%3A%2F%2Fexample.org%2Fa.png%3Ftoken%3Dprivate']) {
  assert.strictEqual(analytics.imagePreviewUrl(previewImage('https://example.org/image?' + query, {src: '/placeholder.png'}), 'https://example.org/'), '');
}


assert.strictEqual(analytics.coordinateBasisPoints(50, 100), 5000);
assert.strictEqual(analytics.utf8Length('abc'), 3);
assert.strictEqual(analytics.utf8Length('日本語'), 9);
assert.strictEqual(analytics.coordinateBasisPoints(150, 100), 10000);
assert.strictEqual(analytics.deviceType(375), 'mobile');
assert.strictEqual(analytics.deviceType(800), 'tablet');
assert.strictEqual(analytics.deviceType(1280), 'desktop');
assert.strictEqual(analytics.shouldSample(20, 0.19), true);
assert.strictEqual(analytics.shouldSample(20, 0.20), false);
assert.strictEqual(analytics.privacySignalEnabled(true, {doNotTrack: '1'}), true);
assert.strictEqual(analytics.privacySignalEnabled(true, {globalPrivacyControl: true}), true);
assert.strictEqual(analytics.privacySignalEnabled(false, {doNotTrack: '1'}), false);
assert.strictEqual(analytics.destinationKind('#section', ['example.com'], false), 'anchor');
assert.strictEqual(analytics.destinationKind('mailto:test@example.com', ['example.com'], false), 'mailto');
assert.strictEqual(analytics.destinationKind('https://example.com/a', ['example.com'], false), 'internal');
assert.strictEqual(analytics.destinationKind('https://example.com.evil.test/a', ['example.com'], false), 'external');
assert.strictEqual(analytics.destinationKind('https://example.com/page#details', ['example.com'], false, 'https://example.com/page'), 'anchor');
assert.strictEqual(analytics.destinationKind('https://files.test/report.pdf', ['example.com'], false), 'download');

const batches = analytics.splitBatches([{id: 1}, {id: 2}, {id: 3}], 2, 10000, {});
assert.deepStrictEqual(batches.map((batch) => batch.length), [2, 1]);
