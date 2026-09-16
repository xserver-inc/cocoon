'use strict';

const assert = require('assert');
const analytics = require('../../js/click-analytics.js');

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
