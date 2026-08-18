'use strict';

const assert = require('assert');
const suggest = require('../../lib/page-access/analytics/assets/suggest.js');

assert.strictEqual(suggest.normalizeKeyword('  記事タイトル  '), '記事タイトル');

const items = [
  {id: 1, name: 'WordPress高速化ガイド'},
  {id: 2, name: 'クリック解析の始め方'},
  {id: 3, name: 'アクセス解析ガイド'},
];
assert.deepStrictEqual(
  suggest.filterLocalItems(items, '解析', 20).map((item) => item.id),
  [2, 3]
);

const url = new URL(suggest.buildPostSearchUrl(
  'https://example.com/wp-admin/admin-ajax.php',
  'nonce-value',
  ' クリック 解析 '
));
assert.strictEqual(url.searchParams.get('action'), 'cocoon_analytics_search_posts');
assert.strictEqual(url.searchParams.get('nonce'), 'nonce-value');
assert.strictEqual(url.searchParams.get('q'), 'クリック 解析');
