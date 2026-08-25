# Cocoon設定画面 UI刷新 実装計画書

## 文書情報

- 対象ブランチ: `feature/cocoon-settings-design`
- 対象画面: WordPress管理画面 `admin.php?page=theme-settings`
- 対象テーマ: Cocoon親テーマ
- 計画の主目的: アクセス解析画面と共通する洗練された視覚言語を、既存の保存・読込仕様を一切変更せずCocoon設定画面へ導入する
- 実装方針: 表示層を段階的に変更し、各段階でフォーム契約・保存回帰・レスポンシブ表示を検証する
- データ移行: なし
- データベーススキーマ変更: なし

## 決定事項

未確定だったデザイン判断は、初回実装では次の推奨値を採用する。

- 初回はSCSS/CSSだけを変更し、PHPのフォーム構造とJavaScriptは変更しない
- 配色はアクセス解析画面と同系統の白、薄いグレー、WordPress管理画面アクセント色を使用する
- モバイルでは既存の下部保存ボタンをfixedアクションバーとして追従表示する
- 設定項目の大分類、検索、モバイル用選択UIは初回の視覚検証後に別段階で判断する
- チェックボックスやradioは初回に独自スイッチへ置き換えず、WordPress標準の操作感を維持する
- Dockerは静的検証だけでは判断できないレスポンシブ表示、操作、保存回帰の確認に使用する
- 各ファイル変更とCSS生成の直前に`git branch --show-current`を実行し、完全一致しなければ即時停止する

## 成果物

初回実装で作成・更新する成果物は次に限定する。

- 本実装計画書
- Cocoon設定画面専用のSCSS partial
- `scss/admin.scss`から専用partialを読み込むimport
- 現行Sassで生成し、既存生成ドリフトを混ぜずに新規ブロックだけを反映した`css/admin.css`
- `tests/Unit/CocoonSettingsDesignContractTest.php`によるフォーム契約と画面限定スコープのユニットテスト
- Dockerローカル環境で取得する検証結果

初回実装では、次の成果物を作らない。

- 新しい設定値
- 新しいDBテーブル、option、theme_mod、usermeta
- 自動保存処理
- AJAX保存処理
- タブ状態を保存するlocalStorage処理
- Chart.js、SortableJSなどの新しい依存関係
- アクセス解析画面のJavaScript流用
- 設定フォームのReact化

## 現状構造

Cocoon設定はWordPress Settings APIではなく、同じ管理画面へ直接POSTする独自フォームである。

- `lib/page-settings/_top-page.php`でnonceを検証する
- 同ファイルが各`*-posts.php`を決められた順序で読み込む
- 各`*-posts.php`が既存入力名を使ってtheme_modを更新する
- 保存後にスキン設定、エディターCSSキャッシュ、`ads.txt`が更新される
- 画面上部と下部に既存の保存ボタンがある
- 通常36個、AMP/PWA有効時は最大38個のタブがradioとlabelで構成される
- タブ内容はCSSの一般兄弟セレクターで切り替えられる
- 各設定セクションは主に`.metabox-holder > .postbox > .hndle + .inside > .form-table`で構成される

アクセス解析画面から再利用するのは次の視覚パターンだけとする。

- 白いカード
- 薄い境界線
- 6pxから8pxの角丸
- ごく浅い影
- 12pxから24pxの一貫した余白
- GridとFlexを使った流動レイアウト
- WordPress管理画面アクセント色
- 表やプレビュー単位の内部スクロール

アクセス解析画面のタイル配置、AJAX、グラフ、ユーザー別レイアウト保存は再利用しない。

## 絶対に維持するフォーム契約

次の項目は、変更前後で完全一致させる。

- 画面スラッグ`theme-settings`
- 必要権限`manage_options`
- フォームの`name`、`method`、`action`、開始位置、終了位置
- すべての成功コントロールの`tagName`、`type`、`name`、`id`、`value`
- checkboxとradioの`checked`生成条件
- 入力の`disabled`生成条件
- nonceフィールド名、nonceアクション、検証順序
- `select_index`隠しフィールド
- 上部保存ボタンと下部保存ボタンの`name`、`id`、`value`
- タブradioの`name="tab-input"`
- 各タブradioの`id`と`value`
- 各タブラベルの`for`と`id`
- 各`tab-*-content`の`id`
- radio、label、contentの兄弟関係と、radioがcontentより前に存在する順序
- `cocoon_settings_before_save`と`cocoon_settings_after_save`
- 各`*-posts.php`の読込順序
- スキン設定の退避・復元処理
- エディターCSSキャッシュと`ads.txt`出力処理

## 変更禁止領域

初回実装では、次のファイルと処理へ差分を作らない。

- `lib/page-settings/*-posts.php`
- `lib/page-settings/*-funcs.php`
- `lib/page-settings/*-forms.php`
- `lib/page-settings/_top-page.php`
- `lib/settings.php`
- `lib/utils.php`の設定保存・読込関数
- `lib/page-backup/`
- `lib/html-forms.php`
- `js/admin-javascript.js`
- `lib/page-access/analytics/assets/analytics.js`
- `lib/page-access/analytics/layout-func.php`

初回実装のCSSでは、次の操作も禁止する。

- 入力要素へ`display:none`を追加し、キーボードや支援技術から操作不能にする
- 入力へ`disabled`や`pointer-events:none`を追加する
- 実際のフォーム値を疑似要素だけで表現する
- 保存ボタンを隠して別の代理ボタンへ置き換える
- `.postbox`、`.form-table`、`.button`などの裸セレクターで全管理画面を変更する
- `.toplevel_page_theme-settings`の外側へ設定画面用スタイルを漏らす
- iframe内のサイトプレビューへ親画面CSSを注入する
- `.demo`と`.demo *`へ管理画面用フォームスタイルを適用する

## スタイル実装方式

新しいSCSS partialを`scss/`配下へ追加し、`scss/admin.scss`の末尾から読み込む。

すべてのルールは次のページ限定ルートを起点とする。

```scss
:where(.toplevel_page_theme-settings) :where(.wrap.admin-settings) {
  // Cocoon設定画面専用スタイル
}
```

この方式により、初回はPHPとJavaScriptの差分をゼロにしたまま表示を変更できる。`admin.css`自体は管理画面全体で読み込まれるが、ルートを厳格に限定することでアクセス解析、投稿一覧、ウィジェットなどへ影響させない。

既存DOMにはinlineの`min-width`や固定幅があるため、`!important`を完全禁止とはしない。ページ、部品、breakpointを限定し、inline最小幅を解除する`min-width: 0 !important`など、必要性をコメントで説明できる例外だけを許可する。汎用的な`!important`は使用しない。

将来、設定画面専用CSSが大きくなった場合は、別タスクとして画面限定enqueueへ分離する。その際もenqueue以外のPHP処理は変更しない。

## デザイントークン

ページルート内にCocoon設定専用のCSS Custom Propertiesを定義する。

- surface: `#fff`
- canvas: WordPress管理画面の背景を維持
- subtle border: `#dcdcde`
- control border: `#8c8f94`
- text: `#1d2327`
- muted: `#646970`
- accent: `var(--wp-admin-theme-color, #2271b1)`
- accent hover: `var(--wp-admin-theme-color-darker-10, #135e96)`
- info background: `#f6f7f7`
- warning background / border: `#fcf9e8` / `#dba617`
- danger background / border: `#fcf0f1` / `#d63638`
- radius control: `4px`
- radius card: `6px`
- radius emphasis: `8px`
- shadow card: `0 1px 2px rgba(0, 0, 0, 0.04)`
- content max: `1280px`
- readable text max: `72ch`
- control height: デスクトップ36px、モバイル主要操作44px
- spacing: 4、8、12、16、20、24、32px

WordPress管理画面の配色へ追従させるため、主要操作色を固定の青だけで実装しない。

## 初回デザイン仕様

### ページヘッダー

- h1、説明文、マニュアルリンク、上部保存ボタンの視覚階層を整理する
- h1の下に十分な余白を設ける
- 説明文の行長を制限し、モバイルで不自然に詰めない
- 上部保存ボタンは既存ボタンを使用し、主要操作として明確にする

### タブ

- 既存radioとlabelを維持する
- タブクリックはradioのchecked状態とCSS表示だけを切り替え、URL、履歴、親documentを更新しない
- タブラベルをanchorやsubmit buttonへ置き換えず、タブ切替を起点とするform submit、AJAX、hash変更を追加しない
- `loading="lazy"`のプレビューiframeが初回表示時に子documentを読み込む場合も、親設定ページの遷移とは分離して扱う
- タブラベルをアクセス解析画面と調和するチップ型の外観へ変更する
- 選択中は背景色だけでなく、文字の太さ、境界、アクセント表示を併用する
- hover、active、focus-visibleの状態を定義する
- default、hover、active、checked、checked+hover、focus-visible、forced-colorsを状態マトリクスとして確認する
- タブradioは`display:none`ではなく、画面外へ飛ばさないvisually-hidden方式にする
- radioへフォーカスしたとき、対応labelへ明確なフォーカスリングを出す
- 初回はタブDOMを移動せず、既存の一般兄弟セレクターを維持する
- 初回はCSS-onlyの制約としてタブの折り返しを許容し、各viewportで占有高を計測する
- タブ占有高が設定内容の利用を著しく妨げる場合は、表示専用ラッパーを追加する次段階へ移す

### カード

- `.postbox`を白背景、1px枠、6px角丸、浅い影へ変更する
- 既存の過剰な内側paddingを見出しと本文へ適切に分配する
- `.hndle`をカード見出しとして整える
- `.inside`の余白を16pxから24pxで統一する
- カード間隔を16px前後で統一する
- メタボックスのドラッグ可能UIと誤解される装飾は加えない

### フォーム行

- デスクトップではラベル列を220pxから240px程度に揃える
- `.form-table`、`tbody`、`tr`を一括してGrid、Flex、`display:block`へ変更せず、WordPressコアの表セマンティクスと782px縦積みを維持する
- 入力列は`min-width: 0`を付け、利用可能幅へ収める
- input、select、textareaの高さ、境界、角丸、フォーカスを統一する
- 長いtextareaとURL入力は利用可能幅へ広げる
- 小さな数値入力や色入力は必要以上に全幅化しない
- 説明文、`.tips`、`.howto`を補助情報として整理する
- `.detail-area`を薄い背景の情報カードとして表示する
- validationエラーは色だけに依存せず、既存挙動を妨げない

### 複合レイアウト

- `.col-2`は広い画面で2列、狭い画面で1列へ切り替える
- Grid/Flexの子へ`min-width: 0`を設定する
- 画像、iframe、表、コード、プレビューがページ全体を横へ広げないようにする
- iframe型プレビューと直書き型`.demo`の双方を検証し、`.demo`内部は管理UIコントロールの装飾対象から除外する
- 設定カードは可能な限り`#tabs`直下の`.metabox-holder`など、構造が限定されたセレクターで対象を絞る
- 設定フォームに併存する「タブcontent直下のpostbox」と「内側metabox-holder配下のpostbox」の両方を明示的に対象化する
- 本当に横幅が必要な部品は、その部品の内部だけを`overflow-x:auto`にする
- ツールチップは固定680pxをやめ、782px以下ではviewport左右12pxへ収まる下部パネルとして実現性を検証する
- hover専用ツールチップの完全なキーボード対応は初回の既知課題とし、意味論改善段階で扱う

### 保存操作

- 上部と下部の既存保存ボタンを維持する
- 782px以下では下部の既存保存領域をfixed表示する
- `env(safe-area-inset-bottom)`を考慮する
- fixed保存領域とツールチップはWordPressレスポンシブ管理メニューの`z-index: 100`より下へ置く
- fixed領域に隠れないよう、フォーム末尾へ十分な余白を確保する
- 新しいsubmitイベント、代理クリック、自動保存は追加しない

### リセット領域

- リセットタブ内だけに危険操作の視覚表現を追加する
- 薄い赤背景、警告色の境界、明確な説明文を使用する
- 既存checkbox、確認条件、submit処理は変更しない
- 通常の設定カードと視覚的に明確に分離する

## レスポンシブ仕様

### 1280px以上

- コンテンツ最大幅と左右余白を整える
- フォームはラベル列と入力列を維持する
- 2カラム部品は内容に応じて維持する
- 1行が長くなりすぎないよう説明文の最大幅を設定する

### 960px以下

- 複雑な2カラム構成を1カラムへ切り替える
- カード内の固定幅を解除する
- タブの余白を少し縮小する
- プレビューと設定欄を縦へ並べる

### 782px以下

- WordPress管理画面のモバイル切替点へ合わせる
- WordPressコアが行う`.form-table`の縦積みを維持し、thとtdの幅、paddingだけを調整する
- ラベルと入力の間隔を明確にする
- すべての操作対象を24px以上に保ち、タブと主要ボタンを44px以上にする
- 下部保存領域をfixed表示する
- 管理バーと管理メニューの折りたたみ状態に対応する

### 600px以下

- `.col-2`を完全な1カラムにする
- 横並びの補助ボタンを折り返す
- メディア選択、色選択、range入力をviewport内へ収める

### 480px以下

- ページとカードの左右余白を12px程度へ縮小する
- textarea、長いtext input、selectを原則全幅にする
- 保存ボタンを押しやすい幅にする
- ツールチップとポップオーバーを画面幅内へ収める

breakpointは1281px以上、961pxから1280px、783pxから960px、601pxから782px、481pxから600px、320pxから480pxの排他的な区間として扱う。

### 320px

- viewport全体に横スクロールが発生しないことを必須条件とする
- 例外は内部スクロールが明示された表、プレビュー、コード領域だけとする

## アクセシビリティ仕様

- 既存radio groupのネイティブセマンティクスを維持する
- 安易に`role="tablist"`や`aria-selected`を追加しない
- Tabキーと矢印キーでタブ選択へ到達できるようにする
- `:focus-visible`で2px以上の明瞭なフォーカスリングを表示する
- 選択状態を色だけで表現しない
- 通常文字と背景のコントラスト比を4.5:1以上にする
- UI境界とフォーカス表示を3:1以上にする
- すべての操作対象をWCAG 2.2 AAの例外条件を含めて24px以上にし、モバイルのタブ、保存、メディア操作など主要操作を44px以上にする
- 200%と400%ズームで操作可能にする
- Windows強制色表示で選択状態とフォーカスが識別できるようにする
- `prefers-reduced-motion`で不要なアニメーションを停止する
- 見出し順序h1からh2を維持する
- 共通フォームhelperのlabel/id不整合は初回へ混ぜず、独立したアクセシビリティ変更として扱う
- hover専用ツールチップとspan製トグルは初回CSSだけでは完全なキーボード対応にできないため、初回は非退行を受入条件とし、意味論改善段階でbutton、aria、トリガー構造を扱う

## 実装フェーズ

### 基準化

- ブランチと作業ツリーを確認する
- 現行`scss/admin.scss`を同じSass版で一時ファイルへコンパイルし、既存`css/admin.css`との生成再現性を確認する
- 現行Sass 1.69.5では、変更前から既存margin shorthand 2行、コメント空白2行、末尾改行に生成ドリフトがあるため、新規ブロックの文字単位一致を正とし、既存部分は書き換えない
- 保護対象ファイルの差分がゼロであることを記録する
- 現行フォーム契約をテストへ固定する
- Docker Compose定義をdaemon不要の`config`で検証し、ライブ検証段階で既定環境の状態を確認する
- 現行画面を1440、1024、960、783、782、768、480、390、360、320pxで記録する
- スキン、全体、広告、アクセス解析・認証、API、リセットを代表タブとする

### CSS基盤

- 専用SCSS partialを追加する
- `.toplevel_page_theme-settings .wrap.admin-settings`で完全にスコープし、既存`#tabs`の!importantと競合するタブ部分だけ理由付きでID詳細度を許可する
- デザイントークンを定義する
- ページヘッダー、タブ、カード、フォーム、保存領域を実装する
- 960、782、600、480pxのresponsive rulesを追加する
- 管理画面CSSを正規手順でビルドする
- stylelintと生成差分を確認する。既存設定に残る現行Stylelint廃止済み2ルールは既知の基盤問題として分離する

### 静的契約テスト

- 許可ファイル以外に差分がないことを確認する
- `_top-page.php`の保存領域に差分がないことを確認する
- 新SCSS partialのトップレベルが単一の設定画面ルートであることを検証する
- ソース上の38組のタブradio、value、隣接label、content対応と、nonce、select_index、上下submit名を検証する
- タブ操作領域にanchor、button、href、formaction、onclickがなく、全contentがchecked radioのCSSで切り替わることを検証する
- partialと生成`css/admin.css`の全トップレベル規則がページスコープから始まることを検証する
- 直下型と入れ子型のカードDOM、リセットカード、inline幅付きツールチップのモバイル保護を検証する
- `git diff --check`を実行する
- 対象ユニットテストと全ユニットテストを実行する
- `npm run test:i18n`を実行する

### Docker視覚検証

- `docker/dev.ps1 status`で既存環境を確認する
- 未起動の場合だけ`docker/dev.ps1 up`を実行する
- `docker/dev.ps1 check`でCompose構文、Docker内PHPUnitユニット、起動中WordPressの`functions.php`構文を確認する
- `check`には統合テスト、全PHP構文、ブラウザー確認、保存回帰が含まれないことを前提とする
- ローカルWordPress管理画面でCocoon設定を確認する
- 各viewportでページ全体の横溢れ、カード、入力、タブ、fixed保存を確認する
- ブラウザーコンソールの新規エラーがゼロであることを確認する
- Cocoon設定以外のアクセス解析、投稿一覧、ウィジェットを比較し、視覚差分がないことを確認する
- 各反復でブラウザーを強制再読込するかキャッシュを無効化し、`admin.css`の古いキャッシュを避ける
- 色ピッカー開閉、画像選択とクリア、range、詳細トグル、カテゴリ・タグリスト、TinyMCE、スキン制御、ツールチップ端部、モバイルプレビューを確認する
- タブ切替後に表示中contentが常に1つで、非表示タブの入力も従来どおりFormDataへ含まれることを確認する
- 代表タブのクリック前後でURLと親windowの識別値が不変で、親documentのGET、POST、再読込が発生しないことを確認する
- プレビューiframeの子document読込は親documentのnavigationと区別して記録する

### 保存回帰

保存回帰は公開テストサーバーではなくDockerローカル環境だけで行う。

- CSS変更前の現行画面で一度正規化保存したDocker DBスナップショットを基準とする
- 同一DBスナップショットを複製し、CSS変更前後へ同一のフォームデータを与えられる状態にする
- checkbox、radio、select、text、textarea、number、color、画像を代表入力として選ぶ
- 上部保存ボタンから保存し、再読込後の値を確認する
- 下部保存ボタンから保存し、再読込後の値を確認する
- 選択タブが保存後に復元されることを確認する
- CSS変更前後でFormDataの成功コントロール集合とPOSTキー集合が一致することを確認する
- nonceの生成値は比較から除外し、フィールド名、アクション、存在を比較する
- FormDataだけではsubmitボタンが含まれないため、上部と下部の実クリック時POSTを別々に記録する
- CSS変更前後へ同一POSTを送った後のtheme_mod全体が一致することを確認する
- fresh DBでは未保存デフォルトが初回保存時に実体化し得るため、単純なキー増加を不合格条件にしない
- リセットは専用の使い捨てDocker環境でのみ確認する

### 反復改善

次の短いループを受入条件を満たすまで繰り返す。

- SCSS変更
- 管理画面CSSビルド
- stylelintとユニットテスト
- ローカル管理画面再読込
- デスクトップ、タブレット、モバイルの比較
- 横溢れ、密度、フォーカス、タップ領域を修正
- 保存契約の再確認

`docker/dev.ps1 loop`はSCSSをビルドせず、ファイル変更時にDocker PHPUnitユニットを再実行するだけである。CSS中心の反復では対象Sassビルドとブラウザー再読込を手動で行い、節目で`check`を実行する。停止時はデータを保持し、`down -v`は使用しない。

### ナビゲーション改善

初回CSS版を確認後、必要性とデザインを合意してから実施する。

- 設定名検索
- 大分類と小分類
- モバイル用の設定選択UI
- タブナビゲーション専用ラッパー

この段階でも、既存radioは`#tabs`直下かつcontentより前に維持する。新しい表示用ボタンを作る場合は`type="button"`、`name`なしとし、既存radioの`checked`を変更するだけにする。submit、AJAX、localStorage、DB更新は行わない。

### 意味論改善

共通フォームhelperのlabel/id、重複IDなどは、保存ロジックと影響範囲を専用テストで保護した別コミットとして扱う。デザイン変更へ混在させない。

## テストコマンド

ホスト側の静的検証では、リポジトリの既存Node、Composer依存関係を利用する。

```powershell
.\node_modules\.bin\sass.cmd scss/admin.scss css/admin.css --style=expanded --no-source-map
.\node_modules\.bin\stylelint.cmd scss/_cocoon-settings-modern.scss
composer test:unit
npm run test:i18n
git diff --check
git status --short
git diff --name-only
```

現行`.stylelintrc.js`にはStylelint 16で利用できない`order/properties-order`と`string-quotes`が残っているため、上記の標準Stylelintコマンドはこの既存2エラーで終了する。今回のSCSS品質判定では、既存設定を一時的に継承したうえでこの2キーだけを削除し、対象partialがエラー・警告ゼロになることを確認する。一時設定は成果物へ含めず、Stylelint設定自体の更新は別変更とする。

管理画面CSSの全体コンパイルでは、変更前から存在するmargin shorthand 2行、コメント空白2行、末尾改行の生成ドリフトが現れる。今回の成果物では既存行をHEADと同一に保ち、専用partialの生成結果と`admin.css`末尾のモダンUIブロックが文字単位で一致することを確認する。

最終の全体ビルド互換確認では次を実行する。`npm run prod`はフロントCSS、エディターCSS、全スキンも再生成するため、実行前後の差分を比較し、許可ファイル以外の生成差分は採用しない。

```powershell
npm run prod
git diff --name-only
```

Dockerローカル検証では次を使用する。

```powershell
pwsh -NoProfile -File docker/dev.ps1 config
pwsh -NoProfile -File docker/dev.ps1 status
pwsh -NoProfile -File docker/dev.ps1 up
pwsh -NoProfile -File docker/dev.ps1 check
pwsh -NoProfile -File docker/dev.ps1 loop
```

別環境の互換確認が必要な場合は、WordPress 7.0、PHP 8.4のenvを明示する。

```powershell
pwsh -NoProfile -File docker/dev.ps1 up env/wp7.0-php8.4.env
pwsh -NoProfile -File docker/dev.ps1 test env/wp7.0-php8.4.env
```

## 画面検証マトリクス

| 幅 | 主な確認内容 |
| --- | --- |
| 1440px | 最大幅、フォーム2列、カード密度、説明文の行長 |
| 1281px / 1280px | 最大幅と中間レイアウトの境界 |
| 1024px | タブレット横、2列解除、プレビュー幅 |
| 961px / 960px | レイアウト切替境界 |
| 783px / 782px | WordPress管理画面モバイル切替境界 |
| 768px | 一般的なタブレット縦 |
| 601px / 600px | 複合レイアウト1列化の境界 |
| 481px / 480px | 小画面の余白と入力全幅化境界 |
| 390px | 一般的なスマートフォン |
| 360px | 小型Android相当 |
| 320px | 最小受入幅、ページ横溢れゼロ |

各幅で次を確認する。

- h1と説明文
- 上部保存ボタン
- タブの選択、フォーカス、折り返し
- カード見出しと本文余白
- input、select、textarea、range、color、メディア選択
- `.col-2`
- ツールチップ
- スキンプレビュー
- 下部fixed保存領域
- iframe型と直書き型`.demo`のプレビュー
- ページ全体のscroll width
- `document.documentElement.scrollWidth <= document.documentElement.clientWidth + 1`を満たすこと
- 200%と400%ズーム、縦向きと横向き

## 受入基準

### 機能

- 保存・読込関連PHPとJavaScriptに差分がない
- DBスキーマ、option、theme_mod、usermetaの新設がない
- 全既存入力のフォーム契約が維持されている
- 上部と下部の保存が従来どおり動く
- 保存後のタブ復元が従来どおり動く
- スキン制御、色選択、画像選択、詳細設定が従来どおり動く
- AMP/PWA有効・無効の両方でタブが機能する

### 視覚

- アクセス解析画面とカード、余白、色、文字階層が調和している
- Cocoon設定以外の管理画面へ視覚差分がない
- 320px以上でページ全体の横スクロールがない
- 各カードの見出し、ラベル、入力、補足文の階層が明確である
- リセット領域が通常設定から明確に区別されている
- fixed保存領域がコンテンツやWordPress UIを覆わない
- 初回CSS版のタブ占有高が各viewportで記録され、次段階の判断材料になっている

### アクセシビリティ

- キーボードだけでタブ、入力、上下保存へ到達できる
- タブ選択状態とフォーカスが色以外でも識別できる
- すべての操作対象が例外条件を含めて24px以上で、モバイル主要操作が44px以上である
- 通常文字のコントラスト比が4.5:1以上である
- 200%と400%ズームでも主要操作が可能である
- reduced motionと強制色表示で利用不能にならない

### 品質

- SCSSビルドが成功する
- 既存の非互換2ルールだけを除外した対象stylelintがエラー・警告ゼロで成功する
- 対象ユニットテストが成功する
- 全ユニットテストが成功する
- Dockerの`check`が成功する
- ブラウザーコンソールに新規エラーがない
- `git diff --check`が成功する
- 許可ファイル以外に差分がない

## リスクと対策

### CSSの他画面への漏出

対策として、すべてのルールを`:where(.toplevel_page_theme-settings) :where(.wrap.admin-settings)`配下へ限定し、裸セレクターをテストで禁止する。

### 危険なCSSによる既存UI破損

`#tabs`や先祖への`overflow:hidden/auto`、`transform`、`filter`、`perspective`、`contain`、`content-visibility`、視覚順だけを変える`order`、全要素transition、checkbox/radioの独自appearanceを禁止する。`.wp-picker-container`、`.iris-picker`、`.wp-editor-wrap`、`.toggle`、`.skin-control`、`.not-allowed-form`、`.demo`への広範な上書きも禁止する。

### 既存SCSS詳細度との競合

既存ルールを削除せず、専用partialを`admin.scss`末尾で読み込む。`!important`へ依存せず、ページルートの詳細度で上書きする。

### タブ操作不能

radio、label、contentのDOMとIDを変更しない。radioはvisually-hiddenにし、focus-visibleを対応labelへ表示する。

### モバイルfixed保存による遮蔽

下部の既存submitをfixed表示し、safe area、WordPress管理UI、フォーム末尾余白を考慮して320pxから782pxで実測する。

### 固定幅部品による横溢れ

ツールチップ、プレビュー、`.col-2`、range、メディア選択を代表部品として、各viewportでscroll widthを計測する。

### 保存回帰

フォーム契約スナップショットとDockerローカルの代表保存テストを両方実施する。公開テストサーバーでは保存回帰を行わない。

### 生成CSSの不要差分

対象ビルドと全体ビルドの両方を確認し、SCSS変更に由来しない既存生成ドリフトは採用しない。専用partialの生成結果と`admin.css`末尾の追加ブロックが文字単位で一致することを必須とする。

## ロールバック

DB変更とデータ移行がないため、ロールバックは表示層の差分を戻すだけで完了する。

- 専用SCSS partialを戻す
- `admin.scss`のimportを戻す
- 専用partialを外して`admin.css`末尾の対応ブロックを戻す
- 表示契約テストを戻す

Dockerのデータボリュームは保持し、ロールバックのために`down -v`を使用しない。

## コミット分割案

コミットを作成する場合は、次の単位へ分ける。

- `docs: Cocoon設定画面の刷新計画を追加`
- `test: Cocoon設定フォームの表示契約を追加`
- `feat: Cocoon設定画面のデザインを刷新`

ナビゲーション改善と意味論改善は、初回CSS刷新とは別コミットにする。

## 完了条件

次のすべてを満たした時点で初回実装を完了とする。

- 本計画書の初回受入基準をすべて満たす
- 保存・読込関連ファイルに差分がない
- SCSS、生成CSS、テスト以外に意図しない差分がない
- Dockerローカル環境でPC、タブレット、モバイル表示を確認済みである
- Dockerローカル環境で代表設定の保存・再読込が成功する
- Cocoon設定以外の管理画面に視覚回帰がない
- ユーザー確認用に変更点と検証結果を日本語で整理できている

## 実装・検証記録

2026年8月25日時点の初回CSS実装と確認結果を記録する。

### 実装済み

- 設定画面専用partialを追加し、すべてのモダンUI規則を`.toplevel_page_theme-settings .wrap.admin-settings`配下へ限定した
- ページヘッダー、タブ、カード、フォーム、プレビュー、リセット領域、モバイル固定保存領域を視覚刷新した
- 960px、782px、600px、480pxのresponsive rulesを追加した
- radio、label、content、form、nonce、submit、POST、保存後タブ復元の既存DOMと処理を変更していない
- タブはradioとlabelによるCSS-only切替を維持し、anchor、submit、URL、hash、history、AJAXによる画面遷移を追加していない
- モバイルの`.form-table`、inline幅付き`.col-2`、直書き`.demo`をカード幅内へ収めた

### ブラウザー確認

- 1440px、1024px、960px、782px、480px、390px、320pxでページ全体の横溢れがないことを確認した
- 782px以下でタブ高44px、下部保存領域のfixed表示、`z-index: 90`を確認した
- 320pxで固定保存領域約64.7pxに対し、フォーム下部余白76pxが確保されていることを確認した
- スキン、全体、ヘッダー、広告、アクセス解析・認証、API、リセットを代表タブとして確認した
- 390pxと320pxの全体タブで`scrollWidth === clientWidth`となり、表、2カラム、直書きプレビューの横溢れがないことを確認した
- タブ往復前後でURLが完全一致し、表示contentが常に1つで、未保存の一時入力値が保持されることを確認した
- Dockerアクセスログでは、明示的な再読込時だけ親設定ページのGETがあり、タブクリック時はプレビューiframeのGETだけが発生することを確認した

### 静的検証

- 専用partialから生成したモダンUI CSSと`css/admin.css`末尾の追加ブロックが文字単位で一致した
- 既存設定の非互換2ルールだけを除外したStylelintでエラー・警告ゼロを確認した
- ホストPHP 8.5の対象テストは6件、869アサーションで成功した
- Docker PHP 8.3の対象テストは6件、869アサーションで成功した
- ホストPHP 8.5の全Unitテストは1295件、10737アサーションで成功した
- 保存・読込関連PHP、JavaScript、DB処理の保護対象ファイルに差分がないことを確認した

### 未実施と既知事項

- 現在のローカルDBを書き換えないため、上下保存ボタンの実クリックを伴う保存回帰は実行していない。必要な場合は使い捨てDB環境で別途実施する
- Cocoon子テーマの`javascript.js`がHTMLへリダイレクトされる既存エラーがプレビューiframe内にある。親テーマの今回差分とは無関係で、別課題として扱う
