'use strict';

const assert = require('assert');
const fs = require('fs');
const path = require('path');
const postcss = require('postcss');

const root = path.resolve(__dirname, '../..');
const skinRoot = path.join(root, 'skins');
const unscopedTableElement = /^(?:table|thead|tbody|tfoot|tr|th|td)(?=[\s>+~.#:]|\[|$)/u;
const genericContainerTable = /(?:^|[\s>+~])\.container\s+(?:table|thead|tbody|tfoot|tr|th|td)(?=[\s>+~.#:]|\[|$)/u;
const violations = [];
let inspectedStyles = 0;

// 全スキンCSSの再帰的な確認
function inspectDirectory(directory) {
  for (const entry of fs.readdirSync(directory, {withFileTypes: true})) {
    const fullPath = path.join(directory, entry.name);
    const relativePath = path.relative(skinRoot, fullPath).replace(/\\/gu, '/');
    if (entry.isDirectory()) {
      inspectDirectory(fullPath);
    } else if (entry.name === 'style.css') {
      inspectedStyles++;
      postcss.parse(fs.readFileSync(fullPath, 'utf8'), {from: fullPath}).walkRules(rule => {
        for (const selector of rule.selectors) {
          if (unscopedTableElement.test(selector.trim()) ||
              (genericContainerTable.test(selector.trim()) && !/(?:^|[\s>+~])\.body\b/u.test(selector))) {
            violations.push(`${relativePath}:${rule.source.start.line} ${selector.trim()}`);
          }
        }
      });
    }
  }
}

inspectDirectory(skinRoot);
assert.ok(inspectedStyles > 30, 'スキンのスタイルが十分に検査されていません。');
assert.deepStrictEqual(violations, [], '管理画面の表に適用される無制限のセレクターがあります。');

// SCSSからの再ビルド時も表セレクターの限定を維持
for (const source of [
  'skins/_scss/_simples_base.scss',
  'skins/natural-blue/style.scss',
  'skins/natural-green/style.scss',
  'skins/simple-darkmode/scss/style.scss',
  'skins/_scss/_skin_dark_base.scss',
]) {
  const lines = fs.readFileSync(path.join(root, source), 'utf8').split(/\r?\n/u);
  for (const [index, line] of lines.entries()) {
    for (const selector of line.split(',')) {
      assert.ok(!unscopedTableElement.test(selector.trim()), `${source}:${index + 1} ${selector.trim()}`);
    }
  }
}

process.stdout.write(`${inspectedStyles}件のスキンCSSで表セレクターの限定を確認\n`);
