# Cocoon テーマ 多言語翻訳ワークフロー 手順書

このドキュメントは、Cocoon テーマの翻訳ファイルを更新・コンパイルする全手順を記載したものです。
次回 AI に同じ作業を依頼する際にこのファイルを読み込ませてください。

---

## 前提条件（環境）

| ツール | バージョン | 備考 |
|--------|-----------|------|
| Node.js | ≥14 | `node --version` で確認 |
| npm | ≥6 | `npm --version` で確認 |
| PHP | ≥8.0 | `php --version` で確認 |
| WP-CLI | 2.12.0 | `vendor/bin/wp-cli.phar` として自動取得 |

> **注意**: WP-CLI は `npm run make-pot` を初回実行時に `vendor/bin/wp-cli.phar` へ自動ダウンロードされます。

---

## ファイル構成

```
languages/
├── cocoon.pot                          # 翻訳テンプレート（原文一覧）
├── {locale}.po                         # 各言語の翻訳ファイル（テキスト形式）
├── {locale}.mo                         # .po をコンパイルしたバイナリ（PHP用）
├── {locale}.l10n.php                   # PHP最適化翻訳（WordPress 6.5以降）
└── cocoon-{locale}-cocoon-blocks-js.json  # Gutenbergブロック用翻訳（JED形式）

scripts/
├── i18n-translation-guide.md           # 本手順書
├── setup-wpcli.js                      # WP-CLI取得 + make-pot 実行
├── update-po.js                        # cocoon.pot → 全言語 .po にマージ
├── apply-translations.js               # 翻訳辞書 → .po ファイルに適用
├── apply-additions.js                  # 追加漏れ翻訳の補完適用
├── compile-po.js                       # .po → .mo コンパイル
├── compile-blocks-json.js              # .po → -cocoon-blocks-js.json 生成
├── add-blocks-translations.js          # 翻訳辞書 → -cocoon-blocks-js.json に追記（ブロックUI用）
├── translations-dict.js                # 全言語辞書のエントリーポイント
└── translations/
    ├── en_US.js                        # 英語 翻訳辞書（PHPテーマ + ブロックUI共通）
    ├── de_DE.js                        # ドイツ語 翻訳辞書
    ├── fr_FR.js                        # フランス語 翻訳辞書
    ├── es_ES.js                        # スペイン語 翻訳辞書
    ├── ko_KR.js                        # 韓国語 翻訳辞書
    ├── pt_PT.js                        # ポルトガル語 翻訳辞書
    ├── zh_CN.js                        # 簡体字中国語 翻訳辞書
    └── zh_TW.js                        # 繁体字中国語 翻訳辞書
```

---

## npmスクリプト一覧

```bash
npm run make-pot                 # POTファイルを最新ソースから更新
npm run update-po                # POTの新規文字列を全言語 .po にマージ
npm run compile-mo               # .po → .mo にコンパイル
npm run compile-l10n-php         # .po → .l10n.php にコンパイル
npm run compile-blocks-json      # .po → -cocoon-blocks-js.json に生成
npm run add-blocks-translations  # 翻訳辞書 → -cocoon-blocks-js.json にブロックUI翻訳を追記
npm run compile-all              # .mo + .l10n.php + .json（ブロックUI含む）を一括コンパイル
```

---

## 翻訳ワークフロー（全手順）

### STEP 1: POTファイルの更新

ソースコード（PHP）から翻訳文字列を抽出して `languages/cocoon.pot` を更新します。

```bash
npm run make-pot
```

**内部動作:**
- `scripts/setup-wpcli.js` が `vendor/bin/wp-cli.phar` の存在を確認
- 未存在なら GitHub から WP-CLI Phar を自動ダウンロード
- `wp i18n make-pot . languages/cocoon.pot --slug=cocoon --domain=cocoon --ignore-domain --exclude="node_modules,vendor,tests,scripts,tmp/css-custom.php,plugins,fonts,icomoon" --skip-js` を実行
- `--skip-js` は PHP 8.5 と WP-CLI 同梱の `mck89/peast` ライブラリの互換性問題のため必須

---

### STEP 2: 全言語 .po ファイルへのマージ

更新された POT の新規文字列を各言語の `.po` ファイルにマージします。
**既存の翻訳は保持されます。新規文字列のみ空欄で追加されます。**

```bash
npm run update-po
```

**内部動作:**
- `scripts/update-po.js` が `wp i18n update-po languages/cocoon.pot languages/{locale}.po` を各言語で実行
- 対象言語: `de_DE`, `en_US`, `es_ES`, `fr_FR`, `ko_KR`, `pt_PT`, `zh_CN`, `zh_TW`

---

### STEP 3: 未翻訳文字列の確認

どの文字列が未翻訳か確認します。

```bash
node -e "
const fs = require('fs'), path = require('path'), gp = require('./node_modules/gettext-parser');
const langDir = path.join(__dirname, 'languages');
fs.readdirSync(langDir).filter(f=>f.endsWith('.po')).forEach(f=>{
  const p = gp.po.parse(fs.readFileSync(path.join(langDir,f)));
  const ng = Object.values(p.translations['']).filter(x=>x.msgid!==''&&(!x.msgstr||x.msgstr[0]===''));
  if(ng.length>0){ console.log('['+f+']: '+ng.length+'件未翻訳'); ng.forEach(e=>console.log('  '+JSON.stringify(e.msgid))); }
});
"
```

---

### STEP 4: 翻訳辞書への追加と適用

新規文字列の翻訳を `scripts/translations/{locale}.js` に追加してから適用します。

#### 4-1. 翻訳辞書ファイルを編集する

各言語の辞書ファイルに未翻訳の `msgid` をキー、翻訳文を値として追加します。

```javascript
// scripts/translations/en_US.js の例
module.exports = {
  // 既存の翻訳...
  '新しい文字列': 'New string',  // ← 追加
};
```

対象ファイル（各言語のロケールに合わせて編集）:
- `scripts/translations/en_US.js` → 英語
- `scripts/translations/de_DE.js` → ドイツ語
- `scripts/translations/fr_FR.js` → フランス語
- `scripts/translations/es_ES.js` → スペイン語
- `scripts/translations/ko_KR.js` → 韓国語
- `scripts/translations/pt_PT.js` → ポルトガル語
- `scripts/translations/zh_CN.js` → 簡体字中国語
- `scripts/translations/zh_TW.js` → 繁体字中国語

#### 4-2. 翻訳を .po ファイルに適用する

```bash
node scripts/apply-translations.js
```

**内部動作:**
- `scripts/translations-dict.js` 経由で全言語辞書を読み込む
- **msgctxt（メッセージコンテキスト）付きエントリも含む**全コンテキストを走査して適用
- 各 `.po` ファイルの `msgstr` が空のエントリに辞書から翻訳を適用
- 既存の翻訳（`msgstr` が空でないもの）は上書きしない

> **⚠️ msgctxt（メッセージコンテキスト）について**
>
> `.po` ファイルには `msgctxt` 付きエントリ（例: `block title`, `block description`, `block keyword`）が存在します。
> これらは `translations['']`（デフォルト）ではなく `translations['block title']` 等の別コンテキストに格納されます。
> スクリプトはすべてのコンテキストを走査するため、辞書に登録した `msgid` さえ一致すれば自動適用されます。

#### 4-3. コンテキスト付き専用エントリの補完（必要な場合）

block.json 由来の `block title` / `block description` / `block keyword` は専用スクリプトに別定義することを推奨します：

```bash
node scripts/apply-ctx-translations.js
```

`scripts/apply-ctx-translations.js` には各言語の block title/description/keyword の翻訳が格納されています。新しいブロックを追加した場合はこのファイルに追加してください。

---

### STEP 5: 翻訳結果の確認

適用後の残り未翻訳数を確認します。

```bash
node -e "
const fs = require('fs'), path = require('path'), gp = require('./node_modules/gettext-parser');
const langDir = path.join(__dirname, 'languages');
console.log('言語      | 翻訳済み | 残り未翻訳');
fs.readdirSync(langDir).filter(f=>f.endsWith('.po')).forEach(f=>{
  const p = gp.po.parse(fs.readFileSync(path.join(langDir,f)));
  const e = Object.values(p.translations['']).filter(x=>x.msgid!=='');
  const ok = e.filter(x=>x.msgstr&&x.msgstr[0]!=='').length;
  const ng = e.length - ok;
  console.log(f.replace('.po','').padEnd(9)+'| '+ok+' | '+ng);
});
"
```

---

### STEP 6: ブロックエディター UI 翻訳の追加

> **⚠️ ブロックUI文字列（JSブロックの全コンポーネント）について**
>
> `make-pot` は `--skip-js` オプションのため、JSファイル内の翻訳文字列（`__('...', 'cocoon')`）を収集しません。
> そのため、Amazon/楽天ブロック内の以下のコンポーネントで使われる文字列は `.po` ファイルに存在せず、
> `compile-blocks-json` だけでは JSON に翻訳が含まれません。
>
> | 対象ファイル | 翻訳文字列の例 |
> |---|---|
> | `index.js` | ブロック説明文（description）、ブロックタイトル |
> | `edit.js` | 「プレビュー更新」「商品を変更」「商品を検索」等 |
> | `components/SettingsPanel.js` | 「表示設定」「画像サイズ」「枠線を表示」等 |
> | `components/SearchModal.js` | 「キーワードを入力…」「検索」「検索に失敗しました。」等 |
> | `components/SearchResults.js` | 「件の結果」「前へ」「次へ」 |
> | `components/ProductPreview.js` | 「プレビューを生成中...」「商品を選択してください」 |
>
> これらは `scripts/translations/{locale}.js` に追加・管理してください。

> **⚠️ `block.json` の `description` について**
>
> POファイルには `msgctxt "block description"` 付きで翻訳が存在しますが、
> `index.js` 内の `__('...', THEME_NAME)` は **コンテキストなし** で参照します。
> そのため、辞書JSにもコンテキストなしキーとして説明文を追加する必要があります。

新しいブロックUI文字列を追加する場合：

1. `scripts/translations/{locale}.js` にキーと翻訳を追加
2. 以下のコマンドを実行

```bash
npm run add-blocks-translations
```

**内部動作:**
- `scripts/add-blocks-translations.js` が各言語の翻訳辞書を読み込む
- `cocoon-{locale}-cocoon-blocks-js.json` に未登録のエントリを追記する（既存翻訳は上書きしない）

---

### STEP 7: 全形式へのコンパイル

翻訳が完了したら全形式のファイルをコンパイルします。

```bash
npm run compile-all
```

このコマンドは以下の4つを順番に実行します：

| コマンド | 生成ファイル | 用途 |
|---------|------------|------|
| `npm run compile-mo` | `{locale}.mo` | WordPress PHP翻訳（全バージョン対応） |
| `npm run compile-l10n-php` | `{locale}.l10n.php` | PHP最適化翻訳（WP 6.5以降、高速） |
| `npm run compile-blocks-json` | `cocoon-{locale}-cocoon-blocks-js.json` | Gutenbergエディター翻訳（POベース） |
| `npm run add-blocks-translations` | 同上（追記） | ブロックUI文字列の翻訳辞書を JSON に反映 |

---

### STEP 8: コンパイル結果の検証

`.mo` ファイルが正常にコンパイルされたか確認します。

```bash
node -e "
const fs = require('fs'), path = require('path'), gp = require('./node_modules/gettext-parser');
const langDir = path.join(__dirname, 'languages');
const moFiles = fs.readdirSync(langDir).filter(f => f.endsWith('.mo'));
for (const moFile of moFiles) {
  const moPath = path.join(langDir, moFile);
  const poPath = path.join(langDir, moFile.replace('.mo', '.po'));
  const moKeys = Object.keys(gp.mo.parse(fs.readFileSync(moPath)).translations['']).filter(k => k !== '');
  const poKeys = Object.keys(gp.po.parse(fs.readFileSync(poPath)).translations['']).filter(k => k !== '');
  const ok = moKeys.length === poKeys.length ? '✅' : '⚠️';
  console.log(ok + ' ' + moFile + ': PO=' + poKeys.length + ' / MO=' + moKeys.length);
}
"
```

---

## 翻訳の注意事項

### 翻訳すべきでない文字列

以下の文字列は原文のまま（翻訳不要）にしてください：

- **API名**: `Amazon`, `PA-API`, `Creators API`, `Rakuten`, `WP-Cron`
- **技術用語**: `ASIN`, `canonical`, `noindex`, `AdSense`, `WP_DEBUG`
- **記号類**: `&laquo;`, `&raquo;`, `-`, `|`, `...`
- **SNS名**: `TikTok`, `SoundCloud`
- **フォント名**: `Microsoft JhengHei`, `Noto Sans TC`, `Microsoft YaHei`, `Noto Sans SC`

### PHP書式文字列

`%s` や `%1$s` などの書式指定子は翻訳文中でも**必ず保持**してください。

```
# 原文
msgid "Amazonで「%s」に関する詳細を見る"

# 英語訳（%s を保持）
msgstr "View details about \"%s\" on Amazon"
```

### WordPressロケール別言語対応表

| ファイル名 | 言語 | 文字の特徴 |
|-----------|------|-----------|
| `en_US.po` | 英語（米国） | 英語 |
| `de_DE.po` | ドイツ語 | ウムラウト（ä, ö, ü）あり |
| `fr_FR.po` | フランス語 | アクサン（é, è, ê）あり |
| `es_ES.po` | スペイン語（スペイン） | アクセント（á, é, ñ）あり |
| `ko_KR.po` | 韓国語 | ハングル文字 |
| `pt_PT.po` | ポルトガル語（ポルトガル） | ※ブラジルは pt_BR（別ファイル）|
| `zh_CN.po` | 中国語（簡体字） | 簡体字（大陸系） |
| `zh_TW.po` | 中国語（繁体字） | 繁体字（台湾・香港系） |

---

## トラブルシューティング

### WP-CLI でエラーが出る

PHP 8.5 と WP-CLI 2.12 内の `mck89/peast` ライブラリの互換性問題により、`Deprecated` 警告が大量に出ることがありますが、`Success:` が表示されれば正常です。

### make-pot が失敗する

`--skip-js` オプションが有効になっているか `scripts/setup-wpcli.js` を確認してください。

### .mo コンパイル後に WordPress が翻訳を読み込まない

`.l10n.php` も合わせて更新されているか確認してください。WordPress 6.5 以降は `.l10n.php` が優先的に読み込まれます：

```bash
npm run compile-l10n-php
```

### ブロックエディターの設定パネルが翻訳されない

`compile-blocks-json` だけでは JSブロックUI文字列（設定パネルのラベル等）が JSON に含まれません。
以下を必ず実行してください：

```bash
npm run add-blocks-translations
# または一括で
npm run compile-all
```

ブラウザキャッシュが残っている場合は **Ctrl+Shift+R**（強制リロード）で翻訳が反映されます。

### ブロックUI文字列を新たに追加したい

1. JS ソース（`edit.js`, `SettingsPanel.js`, `SearchModal.js` 等）に `__('新しい文字列', THEME_NAME)` を追加
2. 全8言語の `scripts/translations/{locale}.js` にキーと翻訳を追加
3. `npm run add-blocks-translations` を実行（または `npm run compile-all` で一括）
