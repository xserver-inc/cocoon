# Contributing to Cocoon

> **Note**: このCONTRIBUTING.mdは、Claude Code を使用して生成されました。

Cocoonプロジェクトへの貢献にご興味をお持ちいただき、ありがとうございます。このガイドでは、プロジェクトに貢献するための手順とガイドラインをご説明します。

## 目次

- [貢献の種類](#貢献の種類)
- [プロジェクト構成](#プロジェクト構成)
- [開発環境のセットアップ](#開発環境のセットアップ)
- [コード貢献の流れ](#コード貢献の流れ)
- [コーディング規約](#コーディング規約)
- [テスト・検証方法](#テスト検証方法)
- [問題報告（Issue）ガイドライン](#問題報告issueガイドライン)
- [翻訳協力](#翻訳協力)
- [ライセンスについて](#ライセンスについて)

## 貢献の種類

以下のような形でプロジェクトに貢献していただけます：

### コード改善・バグ修正
- バグの修正
- パフォーマンスの改善
- セキュリティの向上
- コードの最適化

### 新機能の提案と実装
- 新しい機能の提案
- 機能の実装
- 既存機能の改善

### 翻訳協力
- 多言語化の支援
- 既存翻訳の改善
- 新しい言語の追加

### ドキュメント改善
- ドキュメントの更新
- 使用例の追加
- 説明の明確化

### 問題報告
- バグの報告
- 改善提案
- 使用感のフィードバック

## プロジェクト構成

Cocoonテーマのフォルダ構成を理解しておくことで、効率的な開発が可能になります。

### ルートディレクトリ

```
cocoon/
├── │ WordPressテーマのコアファイル
│   ├── functions.php      # メインの関数ファイル
│   ├── style.css          # コンパイル済みCSS
│   ├── index.php          # メインテンプレート
│   ├── header.php         # ヘッダーテンプレート
│   ├── footer.php         # フッターテンプレート
│   └── その他のテンプレートファイル
│
└── ビルド・開発環境
    ├── package.json        # Node.js依存関係
    ├── gulpfile.js         # ビルド設定
    └── README.md           # プロジェクト情報
```

### 主要ディレクトリの説明

#### コアファイル群

| ファイル/フォルダ | 役割 |
|----------------|------|
| `functions.php` | テーマのメイン機能を定義、lib/のファイルを読み込み |
| `style.css` | コンパイル済みのメインCSS（直接編集非推奨） |
| `amp.css` | AMP用のCSS（scss/amp.scssから生成） |
| `javascript.js` | メインのJavaScriptファイル |
| `*.php` | WordPressテンプレートファイル群 |

#### lib/
**機能別のPHPモジュール群** — テーマの核心機能

```
lib/
├── _imports.php           # モジュール読み込み管理
├── admin.php              # 管理画面機能
├── settings.php           # 設定パネル
├── seo.php                # SEO最適化
├── sns.php                # SNS連携機能
├── shortcodes.php         # ショートコード定義
├── widgets/               # カスタムウィジェット
├── page-settings/         # 設定ページのサブモジュール
└── その他各種機能モジュール
```

#### scss/
**SCSSソースファイル** — CSSの原始コード

```
scss/
├── style.scss             # メインスタイルシートのソース
├── amp.scss               # AMP用スタイル
├── admin.scss             # 管理画面用スタイル
├── _editor-style.scss     # エディタースタイル
└── その他のコンポーネントSCSS
```

#### skins/
**スキン群** — テーマのデザインバリエーション

```
skins/
├── simple-blue/          # シンプルブルースキン
├── nagi/                 # 凪スキン
├── natural-green/        # ナチュラルグリーン
└── その他100+のスキン
```

各スキンフォルダには以下が含まれます：
- `style.css`: コンパイル済みCSS
- `scss/style.scss`: SCSSソースファイル
- `screenshot.jpg`: スキンのプレビュー画像

#### blocks/
**Gutenbergブロック** — 独立したWebpackプロジェクト

```
blocks/
├── package.json           # ブロック用依存関係
├── webpack.config.js      # Webpack設定
├── src/                   # ブロックのソースコード
└── dist/                  # ビルド済みファイル
```

#### その他の重要なフォルダ

| フォルダ | 役割 |
|---------|------|
| `css/` | 管理画面、エディター用なCSSファイル群 |
| `js/` | JavaScriptライブラリ群（第三者製） |
| `images/` | テーマ内で使用する画像ファイル |
| `languages/` | 翻訳ファイル（.po, .mo, .json） |
| `templates/` | ページテンプレート |
| `webfonts/` | アイコンフォント |
| `configs/` | 設定ファイル |

### 開発時の注意事項

#### 直接編集するファイル
- `lib/` 内のPHPファイル
- `scss/` 内のSCSSファイル
- `javascript.js`
- WordPressテンプレートファイル（*.php）
- `blocks/src/` 内のGutenbergブロックソースコード
- 各スキンの `scss/style.scss`

#### 編集してはいけないファイル
- `style.css` — SCSSから自動生成
- `amp.css` — SCSSから自動生成
- `css/` 内のCSSファイル — SCSSから自動生成
- `skins/*/style.css` — 各スキンのSCSSから自動生成
- `blocks/dist/` — Webpackから自動生成されたビルド済みファイル

### 新しいスキンの作成

1. `skins/skin-template/` をコピー
2. フォルダ名を変更
3. `scss/style.scss` を編集
4. `npm run build` でコンパイル

## 開発環境のセットアップ

### 必要な環境

- **PHP**: 7.4以上
- **WordPress**: 5.0以上（最新版推奨）
- **Node.js**: 14.0.0以上
- **npm**: 6.9.0以上、7未満

### セットアップ手順

1. **リポジトリをフォーク**

   GitHub上でCocoonリポジトリをフォークしてください。

2. **ローカルにクローン**

   ```bash
   git clone https://github.com/[あなたのユーザー名]/cocoon.git
   cd cocoon
   ```

3. **依存関係のインストール**

   ```bash
   npm install
   ```

4. **WordPress環境へのテーマ配置**

   クローンしたフォルダを WordPress の `wp-content/themes/` ディレクトリに配置してください。

5. **子テーマの設置（推奨）**

   開発時は[Cocoon子テーマ](https://github.com/yhira/cocoon-child)の使用を推奨します。

## コード貢献の流れ

### 1. Issue確認

貢献する前に、[Issues](https://github.com/xserver-inc/cocoon/issues)で該当する問題が既に報告されていないか確認してください。

### 2. フォークとブランチ作成

```bash
# 上流リポジトリを追加
git remote add upstream https://github.com/xserver-inc/cocoon.git

# 最新の変更を取得
git fetch upstream
git checkout master
git merge upstream/master

# 新しいブランチを作成
git checkout -b feature/your-feature-name
```

### 3. 開発

- コードを変更する前に、該当部分のテストを実行してください
- 変更後は必ず動作確認を行ってください

### 4. ビルド

```bash
# CSS/SCSSファイルの変更の場合
npm run build

# 開発中の自動ビルド
npm run watch
```

### 5. コミット

コミットメッセージは以下の形式で記述してください：

```
種類: 簡潔な説明（50文字以内）

詳細な説明（必要な場合）
- 変更理由
- 影響範囲
- 追加情報
```

コミット種類の例：
- `feat`: 新機能
- `fix`: バグ修正
- `docs`: ドキュメント更新
- `style`: コードスタイル（機能に影響しない変更）
- `refactor`: リファクタリング
- `perf`: パフォーマンス改善
- `test`: テスト追加・修正

### 6. プルリクエスト作成

1. 変更をプッシュ：
   ```bash
   git push origin feature/your-feature-name
   ```

2. GitHub上でプルリクエストを作成

3. プルリクエストのテンプレートに従って詳細を記述

## コーディング規約

### PHP

- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)に準拠
- インデントはタブを使用
- 関数名やクラス名はWordPressの命名規則に従う

```php
// 良い例
function cocoon_get_user_data( $user_id ) {
    // 処理
}

// 悪い例
function getUserData($userId) {
    // 処理
}
```

### CSS/SCSS

- インデントは2スペース
- プロパティはアルファベット順に配置
- ベンダープレフィックスは必要に応じて追加

```scss
// 良い例
.example-class {
  background-color: #ffffff;
  color: #333333;
  display: flex;
  padding: 10px;
}
```

### JavaScript

- インデントは2スペース
- セミコロンを使用
- ES5で記述（WordPress互換性のため）

```javascript
// 良い例
function exampleFunction() {
  var element = document.getElementById('example');
  if (element) {
    element.style.display = 'block';
  }
}
```

## テスト・検証方法

### 基本的な動作確認

1. **WordPress環境での確認**
   - 異なるWordPressバージョンでのテスト
   - 各種ブラウザでの表示確認
   - モバイル端末での表示確認

2. **機能別テスト**
   - 管理画面での設定変更
   - フロントエンドでの表示確認
   - パフォーマンステスト

3. **互換性確認**
   - 子テーマとの互換性
   - 主要プラグインとの互換性
   - 異なるPHPバージョンでの動作

### ビルドプロセスの確認

```bash
# CSS/SCSSのビルド確認
npm run build

# エラーがないことを確認
npm run watch
```

## 問題報告（Issue）ガイドライン

### バグ報告

バグを見つけた場合は、以下の情報を含めて報告してください：

**必須情報**
- WordPress バージョン
- PHPバージョン
- ブラウザとバージョン
- Cocoonバージョン
- 問題の詳細な説明

**再現手順**
1. 具体的な操作手順
2. 期待される結果
3. 実際の結果

**環境情報**
- 使用中のプラグイン
- カスタマイズ内容
- エラーメッセージ（あれば）

### 機能要望

新機能の提案時は以下を含めてください：

- 機能の概要
- 必要性の説明
- 想定される実装方法
- 類似機能との比較

## 翻訳協力

CocoonはCrowdinを使用して多言語化を行っています。

### 翻訳への参加方法

1. [Cocoon Crowdinプロジェクト](https://crowdin.com/project/wp-cocoon)にアクセス
2. 参加申請（Join）を送信
3. 承認後、翻訳作業を開始

### 翻訳のガイドライン

- **一貫性**: 用語は統一して使用
- **自然さ**: その言語として自然な表現を使用
- **文脈**: UIの文脈を理解して翻訳
- **WordPress規約**: WordPressの翻訳規約に準拠

### 現在サポートされている言語

- 英語
- 中国(簡体語)
- 中国(繁体語)
- フランス語
- ドイツ語
- 韓国語
- ポルトガル語
- スペイン語

## ライセンスについて

Cocoonは**100% GPL**として公開されています。

### 貢献時の注意点

- 貢献されたコードはGPLライセンスとなります
- 第三者のコードを含む場合は、GPL互換であることを確認してください
- 著作権を侵害する内容は含めないでください

### ライセンス詳細

- ライセンス: [GNU General Public License v2.0](http://www.gnu.org/licenses/gpl-2.0.html)
- 再配布時は同じGPLライセンスを適用してください
- 商用・非商用を問わず自由に使用可能です

## コミュニティ

### サポート

- **公式サイト**: https://wp-cocoon.com/
- **GitHub Issues**: https://github.com/xserver-inc/cocoon/issues
- **フォーラム**: WordPress.orgコミュニティフォーラム

### 行動規範

- **敬意**: すべてのコントリビューターを尊重する
- **建設的**: 建設的なフィードバックを心がける
- **協力的**: チームワークを大切にする
- **包括的**: すべての人を歓迎する

## 謝辞

Cocoonプロジェクトへの貢献を検討していただき、ありがとうございます。皆様の協力により、Cocoonはより良いWordPressテーマになります。

質問がある場合は、[Issues](https://github.com/xserver-inc/cocoon/issues)で質問するか、[公式サイト](https://wp-cocoon.com/)をご確認ください。

---

**開発者**: わいひら ([yhira](https://github.com/yhira))
**最終更新**: 2025年1月
