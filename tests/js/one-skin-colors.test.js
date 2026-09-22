'use strict';

const assert = require('assert');
const fs = require('fs');
const path = require('path');
const postcss = require('postcss');
const {execFileSync} = require('child_process');

const root = path.resolve(__dirname, '../..');
const styles = JSON.parse(execFileSync('php', [path.join(root, 'tests/fixtures/one-skin-css.php')], {encoding: 'utf8'}));

// 実際のPHP出力による、新着情報と色指定のないカレンダーへの設定文字色の反映確認
for (const [name, color] of Object.entries({default: 'rgb(61,61,61,1)', custom: '#123456', 'light-text': '#f4eedd'})) {
  const colors = new Map();
  postcss.parse(styles[name]).walkRules(rule => {
    rule.walkDecls('color', declaration => {
      for (const selector of rule.selectors) {colors.set(selector, declaration.value);}
    });
  });
  assert.strictEqual(colors.get('.info-list-item-content-link'), color, `${name}: 新着情報のタイトル文字色`);
  assert.strictEqual(colors.get('.info-list-item-content-link:hover'), color, `${name}: ホバー時のタイトル文字色`);
  assert.strictEqual(colors.get('.wp-calendar-table:not(.has-text-color):not(.has-background)'), color, `${name}: カレンダーの設定文字色`);
  assert.ok(!colors.has('.wp-calendar-table'), 'ブロックの独自文字色の維持');
  assert.ok(!colors.has('.wp-calendar-table:not(.has-text-color)'), '背景色のみを設定したブロックの配色の維持');
}

// 明るい背景のセルに限定した濃色文字と、ブロックの独自配色の除外確認
const rules = [];
postcss.parse(fs.readFileSync(path.join(root, 'skins/one/style.css'), 'utf8')).walkRules(rule => {
  if (rule.selector.includes('.wp-calendar-table')) {
    rules.push([rule.selectors, rule.nodes.filter(node => node.type === 'decl').map(node => [node.prop, node.value, Boolean(node.important)])]);
  }
});
assert.deepStrictEqual(rules, [[[
  '.wp-calendar-table:not(.has-text-color):not(.has-background) th',
  '.wp-calendar-table [id$="today"]'
], [['color', '#333', false]]]]);

process.stdout.write('ONEの新着情報・カレンダーの配色テスト成功\n');
