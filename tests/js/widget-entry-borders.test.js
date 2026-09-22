'use strict';

const assert = require('assert');
const fs = require('fs');
const path = require('path');
const {execFileSync} = require('child_process');
const sass = require('sass');
const postcss = require('postcss');
const gettext = require('gettext-parser');

const root = path.resolve(__dirname, '../..');
const read = file => fs.readFileSync(path.join(root, file), 'utf8');
const label = 'カード間に区切り線を入れる';
const description = 'カード間に区切り線が表示されます。記事が1件の場合は表示されません。';

// 配布CSSとSCSSの区切り線に関するセレクター・宣言・順序の照合
function partitionRules(css) {
  const rules = [];
  postcss.parse(css).walkRules(rule => {
    if (rule.selector.includes('.border-partition')) {
      rules.push([rule.selector, rule.nodes.filter(node => node.type === 'decl').map(node => [node.prop, node.value, Boolean(node.important)])]);
    }
  });
  return rules;
}

const expectedRules = partitionRules(sass.compile(path.join(root, 'scss/entry-content.scss'), {logger: sass.Logger.silent}).css);
assert.ok(expectedRules.length > 0);
assert.ok(expectedRules.some(([selector, declarations]) => selector === '.border-partition .a-wrap:last-child' && declarations.some(([prop, value]) => prop === 'border-bottom' && value === '0')));
assert.ok(!expectedRules.some(([selector]) => selector.includes(':first-of-type')));
assert.deepStrictEqual(partitionRules(read('css/entry-content.css')), expectedRules);
assert.deepStrictEqual(partitionRules(read('style.css')), expectedRules);
assert.deepStrictEqual(partitionRules(read('css/admin.css')), partitionRules(sass.compile(path.join(root, 'scss/admin.scss'), {logger: sass.Logger.silent}).css));

for (const skin of fs.readdirSync(path.join(root, 'skins')).filter(name => name.startsWith('veilnui-simplog-'))) {
  const css = sass.compile(path.join(root, `skins/${skin}/style.scss`), {logger: sass.Logger.silent}).css;
  assert.deepStrictEqual(partitionRules(read(`skins/${skin}/style.css`)), partitionRules(css), `${skin}の配布CSSとSCSSの不一致`);
}

// ブラウザーなしのリリース検証にも含む、Fuwari4色の無条件な線指定の再発防止
for (const [color, border] of Object.entries({ebicha: '#e6c7c0', kachiiro: '#d1d1db', mirucha: '#d6d3ce', omeshicha: '#cddee0'})) {
  const declarations = [];
  postcss.parse(read(`skins/skin-fuwari-${color}/style.css`)).walkRules(rule => {
    if (rule.selector.includes('.widget-entry-cards') && rule.selector.includes('.a-wrap')) {
      rule.walkDecls(/^border(?:-|$)/, decl => declarations.push([rule.selector, decl.prop, decl.value, Boolean(decl.important)]));
    }
  });
  assert.deepStrictEqual(declarations, [[
    '.widget-entry-cards.border-partition:not(.is-list-horizontal) > .a-wrap:not(:last-child)',
    'border-bottom', `solid 1px ${border}`, false
  ]], `Fuwari ${color}の線指定の適用範囲`);
}

for (const block of ['new-list', 'popular-list', 'navicard']) {
  const source = read(`blocks/src/block/${block}/edit.js`);
  assert.ok(source.includes(label), `${block}の設定文言の不一致`);
  assert.ok(source.includes("value: 'border_partition'"), `${block}の保存値の互換性`);
  assert.ok(!source.includes('カードの上下に区切り線を入れる'));
}
const settings = read('lib/settings.php');
assert.ok(settings.includes(label) && settings.includes(description));
assert.ok(settings.includes("get_cocoon_template_directory_uri().'/images/widget-border-partition.svg'"));
assert.ok(!settings.includes('2019/07/border_partition.png'));
assert.ok(read('images/widget-border-partition.svg').includes('M12 70H348 M12 140H348'));
assert.ok(read('blocks/dist/blocks.build.js').includes(label), 'ブロック配布JSの再ビルド漏れ');

// 実際に読み込まれる全翻訳形式と新しい設定文言の一致
const pot = gettext.po.parse(read('languages/cocoon.pot')).translations[''];
for (const id of [label, description]) {assert.ok(pot[id]);}
for (const locale of ['de_DE', 'en_US', 'es_ES', 'fr_FR', 'ko_KR', 'pt_PT', 'zh_CN', 'zh_TW']) {
  const po = gettext.po.parse(read(`languages/${locale}.po`)).translations[''];
  const mo = gettext.mo.parse(fs.readFileSync(path.join(root, `languages/${locale}.mo`))).translations[''];
  const jed = JSON.parse(read(`languages/cocoon-${locale}-cocoon-blocks-js.json`));
  const php = JSON.parse(execFileSync('php', ['-r', '$catalog = include $argv[1]; echo json_encode($catalog["messages"]);', path.join(root, `languages/${locale}.l10n.php`)], {encoding: 'utf8'}));
  for (const id of [label, description]) {
    const translated = po[id]?.msgstr[0];
    assert.ok(translated, `${locale}: ${id}の翻訳漏れ`);
    assert.strictEqual(mo[id]?.msgstr[0], translated, `${locale}のMOの不一致`);
    assert.strictEqual(php[id], translated, `${locale}のl10n.phpの不一致`);
    assert.strictEqual(jed.locale_data.messages[id]?.[0], translated, `${locale}のJED JSONの不一致`);
  }
}

process.stdout.write('カード間の区切り線・配布資産・翻訳の整合性テスト成功\n');
