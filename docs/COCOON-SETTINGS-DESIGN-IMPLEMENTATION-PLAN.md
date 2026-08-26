# Cocoon設定画面 UI刷新 実装計画書

## 文書情報

- 対象ブランチ: `feature/cocoon-settings-design`
- 対象画面: WordPress管理画面 `admin.php?page=theme-settings`
- 対象テーマ: Cocoon親テーマ
- 計画の主目的: アクセス解析画面と共通する洗練された視覚言語を、Cocoon本体の設定保存・読込仕様を変更せずCocoon設定画面へ導入する
- 実装方針: 表示層を段階的に変更し、表示モードだけを独立したユーザー表示設定として扱い、各段階でフォーム契約・保存回帰・レスポンシブ表示を検証する
- データ移行: なし
- データベーススキーマ変更: なし
- 追加する表示設定: サイト別・管理者別の`cocoon_settings_navigation_mode` 1項目だけ

## 決定事項

未確定だったデザイン判断は、初回実装では次の推奨値を採用する。

- 既存フォーム構造は変更せず、JavaScriptは表示専用ナビゲーションと表示モード保存だけに限定する
- 配色はアクセス解析画面と同系統の白、薄いグレー、WordPress管理画面アクセント色を使用する
- モバイルでは既存の下部保存ボタンをfixedアクションバーとして追従表示する
- 設定項目の大分類、検索、モバイル用選択UIは既存radioを状態源とする段階的強化として実装する
- チェックボックスやradioは初回に独自スイッチへ置き換えず、WordPress標準の操作感を維持する
- Dockerは静的検証だけでは判断できないレスポンシブ表示、操作、保存回帰の確認に使用する
- 各ファイル変更とCSS生成の直前に`git branch --show-current`を実行し、完全一致しなければ即時停止する
- 初期値は後方互換を優先した「従来の表示」とし、利用者が明示的に「おすすめ表示」を選んだ場合だけ、画面幅に応じたナビゲーションへ切り替える
- 表示モードはWordPress標準の`get_user_option()`と`update_user_option()`でサイト別・管理者別に保存する
- 表示モード用のAJAX、nonce、ユーザー設定はCocoon本体の設定フォーム、theme_mod、バックアップ、リセットから完全に分離する

## 表示モードの段階導入仕様

設定インターフェースの大幅な変更では、既存利用者の操作記憶を保ちながら新UIへ任意移行できる方式を採用する。

- `従来の表示`: 元のDOM順を維持した横型タブ。新しい配色、カード、選択表現は維持し、PCでは旧UIに近い文字サイズと余白へ圧縮する
- `おすすめ表示`: 広い設定領域では分類サイドナビ、中程度では折り返しタブ、狭い領域では分類付き選択欄を表示する推奨モード
- 表示モードの選択UIは、隣接した2択セグメントとして上部保存ボタンと設定ナビゲーションの間へ置き、どちらの表示でも見失わないようにする
- セグメント全体を`role="radiogroup"`、各選択肢を`type="button"`かつ`role="radio"`で構成し、選択状態は`aria-checked`で表す。各選択肢に`name`を付けず、既存フォームの成功コントロールへ加えない
- 表示モード切替ではCSSクラスと表示専用UIだけを変更し、既存タブradio、未保存入力、選択中の設定項目、URL、履歴を変更しない
- Tabキーで選択中の表示モードへ入り、矢印キー、Home、End、Space、Enter、クリックで1つだけを選択できるようにする
- 保存可能値は`responsive`と`tabs`の2値だけとし、不正値と未保存値は`tabs`へ戻す
- 専用AJAXは`manage_options`、専用nonce、文字列確認、`sanitize_key()`、厳密なホワイトリスト、現在ユーザーIDを順に検証する
- 保存後に同じユーザー設定を再読込し、要求値との厳密比較に失敗した場合はUIを直前の保存済みモードへ戻す
- JavaScriptが利用できない場合は従来タブをそのまま利用できる段階的強化とする
- 既存サイトと新規サイトを推測で分類するDBマーカーは追加せず、未選択者へ同じ安全な初期値を適用する

## 成果物

初回実装で作成・更新する成果物は次に限定する。

- 本実装計画書
- Cocoon設定データ互換性監査報告書と再現用トークン解析スクリプト
- Cocoon設定画面専用のSCSS partial
- 専用partialを読み込む`scss/cocoon-settings.scss`
- Cocoon設定画面でだけ読み込む生成物`css/cocoon-settings.css`
- 表示専用ナビゲーション、実行動作テスト、多言語カタログ
- `tests/Unit/CocoonSettingsDesignContractTest.php`によるフォーム契約と画面限定スコープのユニットテスト
- Dockerローカル環境で取得する検証結果

実装では、次の成果物を作らない。

- Cocoon本体の新しい設定値（独立した表示モード用ユーザー設定だけを例外とする）
- 新しいDBテーブル、option、theme_mod、usermeta
- 自動保存処理
- Cocoon本体設定を扱う新しいAJAX保存処理
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

新しいSCSS partialを`scss/`配下へ追加し、専用エントリ`scss/cocoon-settings.scss`からだけ読み込む。生成した`css/cocoon-settings.css`はCocoon設定画面でだけenqueueし、共通`admin.css`へ設定専用規則を含めない。

すべてのルールは次のページ限定ルートを起点とする。

```scss
:where(.toplevel_page_theme-settings) :where(.wrap.admin-settings) {
  // Cocoon設定画面専用スタイル
}
```

この方式により、設定専用CSS約36KBをアクセス解析、投稿一覧、ウィジェットなどで読み込まない。さらにルートも厳格に限定し、誤enqueue時にも他画面へ規則が漏れない二重の境界を設ける。

既存DOMにはinlineの`min-width`や固定幅があるため、`!important`を完全禁止とはしない。ページ、部品、breakpointを限定し、inline最小幅を解除する`min-width: 0 !important`など、必要性をコメントで説明できる例外だけを許可する。汎用的な`!important`は使用しない。

最上位ラッパーへ`container-type`、`contain`、`transform`など固定配置の基準を変える指定は置かない。広い設定領域の判定は既存JavaScriptの`ResizeObserver`でクラスを切り替え、モバイル保存バー、ツールチップ、TinyMCE全画面表示をviewport基準のまま保つ。

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

- タブレット表示では既存radio groupのネイティブセマンティクスを維持し、Tabキーと矢印キーで選択できるようにする
- PC表示では各項目を`type="button"`と`aria-pressed`で表し、Tabキー、Enterキー、Spaceキーで操作できるようにする
- モバイル表示では分類付きのネイティブselectを使用する
- 画面幅変更で操作中のナビゲーションが消える場合は、同じ選択項目の表示中UIへフォーカスを引き継ぐ
- 安易に`role="tablist"`や`aria-selected`を追加しない
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
- `scss/cocoon-settings.scss`を固定Sass版で一時ファイルへコンパイルし、`css/cocoon-settings.css`との完全一致を確認する
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
- 設定画面専用CSSを正規手順でビルドする
- stylelintと生成差分を確認する。既存設定に残る現行Stylelint廃止済み2ルールは既知の基盤問題として分離する

### 静的契約テスト

- 許可ファイル以外に差分がないことを確認する
- `_top-page.php`の保存領域に差分がないことを確認する
- 新SCSS partialのトップレベルが単一の設定画面ルートであることを検証する
- ソース上の38組のタブradio、value、隣接label、content対応と、nonce、select_index、上下submit名を検証する
- タブ操作領域にanchor、button、href、formaction、onclickがなく、全contentがchecked radioのCSSで切り替わることを検証する
- partialと生成`css/cocoon-settings.css`の全トップレベル規則がページスコープから始まることを検証する
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
- 各反復でブラウザーを強制再読込するかキャッシュを無効化し、`cocoon-settings.css`の古いキャッシュを避ける
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
- 設定画面専用CSSビルド
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
.\node_modules\.bin\sass.cmd scss/cocoon-settings.scss css/cocoon-settings.css --style=expanded --no-source-map
npm run lint:settings
npm run test:settings-navigation
npm run test:settings-css
npm run test:i18n
php scripts/audit-cocoon-settings-data-contract.php --check
.\vendor\bin\phpunit --do-not-cache-result tests\Unit\CocoonSettingsDesignContractTest.php tests\Unit\CocoonSettingsNavigationModeTest.php
.\vendor\bin\phpunit --do-not-cache-result --testsuite unit --exclude-group distribution
.\vendor\bin\phpunit --do-not-cache-result --group distribution
git diff --check
git status --short
git diff --name-only
```

`npm run lint:settings`は設定画面専用JavaScriptとその実行型テストへESLintを、専用SCSS partialへStylelintを実行する。全リポジトリ向けlintとは分け、今回の変更範囲を同じ条件で再現できるようにする。

設定専用CSSは専用entryから単独生成し、同じコマンドによる再生成で`css/cocoon-settings.css`が文字単位で一致することを確認する。共通`admin.css`にはモダンUIのセレクターやトークンが含まれないことも検証する。

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

- Cocoon本体の設定フォーム、nonce、POST、theme_mod、バックアップ、リセット処理に差分がない
- DBスキーマとtheme_modの新設がなく、DB差分は表示モード専用のユーザー設定1項目だけである
- 表示モード保存が専用AJAX、専用nonce、現在ユーザーの`user_option`だけに限定される
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

既存ルールを削除せず、専用partialを`cocoon-settings.scss`から読み込む。`!important`へ依存せず、ページルートの詳細度で上書きする。

### タブ操作不能

radio、label、contentのDOMとIDを変更しない。radioはvisually-hiddenにし、focus-visibleを対応labelへ表示する。

### モバイルfixed保存による遮蔽

下部の既存submitをfixed表示し、safe area、WordPress管理UI、フォーム末尾余白を考慮して320pxから782pxで実測する。

### 固定幅部品による横溢れ

ツールチップ、プレビュー、`.col-2`、range、メディア選択を代表部品として、各viewportでscroll widthを計測する。

### 保存回帰

フォーム契約スナップショットとDockerローカルの代表保存テストを両方実施する。公開テストサーバーでは保存回帰を行わない。

### 生成CSSの不要差分

対象ビルドと全体ビルドの両方を確認し、SCSS変更に由来しない既存生成ドリフトは採用しない。専用entryの生成結果と`css/cocoon-settings.css`が文字単位で一致し、共通`admin.css`に設定専用規則が混入しないことを必須とする。

## ロールバック

DBスキーマ変更とデータ移行はないため、ロールバックは表示層と専用表示モード処理の差分を戻すだけで完了する。保存済みのユーザー表示設定はCocoon本体設定から独立しているため、コードを戻した後も動作へ影響しない。

- 専用SCSS partialを戻す
- 専用SCSS entryと`cocoon-settings.css`を戻す
- 設定画面限定のCSS enqueueを戻す
- 表示モード用JavaScript、AJAX、ローカライズを戻す
- 表示契約テストを戻す

Dockerのデータボリュームは保持し、ロールバックのために`down -v`を使用しない。

## コミット分割案

コミットを作成する場合は、次の単位へ分ける。

- `docs: Cocoon設定画面の刷新計画を追加`
- `test: Cocoon設定フォームの表示契約を追加`
- `feat: Cocoon設定画面のデザインを刷新`
- `feat: Cocoon設定ナビゲーションの表示モード切替を追加`

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

この節は実装過程の履歴である。2026年8月25日の初回CSS実装から順に記録し、途中のテスト件数は当時のスナップショットとして残す。現行作業ツリーの再現コマンドと最新記録値は末尾の「現行作業ツリーの再検証記録」を正とする。

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

- 専用entryから生成したモダンUI CSSと`css/cocoon-settings.css`が文字単位で一致した
- 共通`css/admin.css`にCocoon設定専用セレクターが含まれないことを確認した
- 既存設定の非互換2ルールだけを除外したStylelintでエラー・警告ゼロを確認した
- 実行型DOMテストでスキン非表示、フォーム境界、幅変更、IME、キーボード、focus時点のARIA、スクロール正負経路、通信停止、nonce失効を確認した
- 現在の作業内容全体から実際のGit配布tarを生成し、専用JS・CSSと8言語のPO・MO・l10n.phpが含まれ、テスト・内部計画書・開発用packageが除外されることを確認した
- 専用SCSSの再コンパイル結果と配布CSS全体をCIで文字単位に比較し、翻訳22文言は8言語すべてでPO・MO・l10n.phpの一致を検証する
- 重い配布tar検査を`distribution`グループへ分け、通常Unit行列とcoverageから除外してCIで1回だけ実行する
- 初回CSS実装時点では、ホストPHP 8.5とDocker PHP 8.3の対象テストが各6件・869アサーション、ホストPHP 8.5の全Unitテストが1295件・10737アサーションで成功した
- Cocoon本体theme_modの保存・読込PHP、既存管理JavaScript、バックアップ処理の保護対象ファイルに差分がないことを確認した。表示モード専用user_optionだけは許可した例外として分離した

### 未実施と既知事項

- 2026年8月26日に上部保存ボタンで変更なし保存を実行し、保存成功通知と再読込後フォーム状態の一致を確認した。下部ボタンは同一formを送信する既存契約を静的テストで確認している
- raw DBシリアライズ文字列とリセット操作は、機密保護と現在DBの破壊防止のため取得・実行していない。代わりに全保存・読込ソースの完全比較と実画面の再読込結果を組み合わせた
- Cocoon子テーマの`javascript.js`がHTMLへリダイレクトされる既存エラーがプレビューiframe内にある。親テーマの今回差分とは無関係で、別課題として扱う

### ナビゲーション改善

2026年8月25日、設定数の多さと各画面幅での操作性を両立するため、既存タブを状態源として使う表示専用ナビゲーションを追加した。

- 広い設定領域では、38項目を6分類へ整理した縦型サイドバー、設定名検索、現在位置の強調表示を採用した
- 中程度の設定領域では、一覧性を保ちやすい既存の折り返し横型タブを維持した
- 狭い設定領域では、画面上部へ追従するネイティブの分類付き選択欄へ切り替えた
- 設定領域の実幅は`ResizeObserver`で監視し、1040px以上の場合だけ`is-navigation-wide`クラスを付与する。固定配置を壊し得るCSS containmentは使用しない
- スキンCSSで元ラベルが非表示の設定は、強化前の可視性を記録して新しいナビゲーションと選択欄から除外し、値とcheckedを保持したままradioも視覚・focus・読み上げ対象に戻さない
- 分類メニューはroving tabindexと上下矢印、Home、Endに対応し、本文へのTab移動を38回要求しない
- 通信停止には15秒タイムアウトを設け、nonce期限切れでは再読込を案内し、成功・失敗の全経路で操作不能状態を解除する
- 検索や幅変更で選択項目が非表示になる場合は、ARIAを先に復元してから可視要素へフォーカスを戻し、切替先が画面外の場合だけパネル先頭へスクロールする
- 検索欄ではIME変換確定中のEnterを妨げず、通常Enterによる設定フォームの誤送信だけを防ぐ
- 新規UI文言はPOTと8言語のPO、MO、l10n.phpへ反映し、実行型DOMテストと翻訳整合性テストで保護する
- 表示用ボタンはすべて`type="button"`かつ`name`なしとし、クリック時は対応する既存radioの`.click()`だけを呼び出す
- radioの`change`を監視し、サイドバー、モバイル選択欄、表示中パネルを同じ状態へ同期する
- JavaScriptを利用できない場合や初期化に失敗した場合は、既存タブがそのまま利用できる構成にした
- submit、FormData、URL、history、Web Storageは操作せず、通信は後述する表示モード専用AJAXだけに限定した
- ナビゲーション用スクリプトはCocoon設定画面だけで読み込み、既存の保存・読込・DB関連ファイルは変更していない

PC 1440px、タブレット1024px、モバイル390pxを中心に確認し、タブ切替でURLが変化せず、常に1つの設定パネルだけが表示されること、画面全体に横スクロールが発生しないことを確認した。保存対象の名前付きコントロールは切替前後で同一であり、追加ナビゲーション自身は保存対象の値を持たない。

2026年8月26日のナビゲーション改善時点では、ローカル環境で有効な36項目すべてをPC用ナビゲーションから切り替え、選択radio、`aria-pressed`、表示パネルが全件一致することを確認した。AMP・PWAを含む全38項目の対応は契約テストで固定している。PC、タブレット、モバイル間で操作中ナビゲーションのフォーカスが同じ項目へ引き継がれ、本文入力中はフォーカスが移動しないことも確認した。この段階の履歴値は、配布検査を含む全PHPUnit 1338件・11791アサーションであり、実行型DOM、SCSS/CSS完全同期、8言語のi18n整合性も合格していた。

### 表示モード切替

2026年8月26日、既存利用者の操作記憶を保ちながら「おすすめ表示」へ任意移行できる表示モード選択を追加した。

- 未保存時は元の順番を維持した「従来の表示」を選択し、画面幅に応じて最適化する選択肢を「おすすめ表示」とした
- 「従来の表示」は新しい色、カード、細い選択境界を維持しながら、PCでは旧UIに近い`4px 9px 3px`の内側余白へ戻した
- 782px以下と粗いポインターでは、「従来の表示」でも44px以上の操作領域を維持した
- 切替UIは単一の2択セグメントとして、コンテナに`role="radiogroup"`、2個の`type="button"`に`role="radio"`と`aria-checked`を付与し、選択中の項目だけをTab停止にした
- 独立した「おすすめ」バッジと丸印をなくし、短いラベル、共有外枠、選択中の面だけの境界・背景・太字で横幅を削減しながら排他選択を明示した
- 切替時はCSSクラスと表示ナビゲーションだけを変更し、既存radioの`.click()`、フォーム送信、ページ遷移を発生させない
- 表示モードだけを専用AJAXで現在管理者のサイト別`user_option`へ保存し、失敗時は保存済みモードへ戻して通知する
- 権限、nonce、許可値、配列入力、現在ユーザー、保存後再読込を専用Unitテストで検証した
- 元の38タブ順を配列完全一致で契約テストへ固定した
- 実行型DOMテストでは「おすすめ表示」と「従来の表示」の排他選択、矢印キー操作、フォームデータ不変を確認し、CSS契約では782px以下でも横並び2択と44px以上の操作領域を固定した。実ブラウザーでの最終目視は、最新ファイル反映後の再読み込み時に行う
- 874個の名前付きフォームコントロール、未保存入力値、選択radio、表示パネル、URLがモード往復前後で一致することを確認した
- 保存中は`aria-disabled`で多重操作だけを防ぎ、保存の前後で選択ボタンのキーボードフォーカスが維持されることを実ブラウザーで確認した
- 表示モード切替実装時点のテスト結果は直前のナビゲーション改善記録と同じであり、現行結果ではない。現行値は後述の再検証表へ集約する

### データ互換性全件監査

2026年8月26日、改修前の基準コミット`5ac3b6b1e4c01a842774d89ed9ff7fa722e1d0f9`と現在の作業ツリーを、コメントを除外するPHPトークン解析で比較した。詳細と全542件の結果は[データ互換性監査報告書](./COCOON-SETTINGS-DATA-COMPATIBILITY-REPORT.md)に記録する。

- `_top-page.php`から保存時の`require_once`を再帰追跡し、SNSシェアのtop・bottom子ファイルを含む41ファイルを監査した
- 実行可能な`update_theme_option()`518箇所と、SNS動的定義を展開した542保存単位が改修前後で一致した
- 542個の保存定数を542個の一意theme_modキーへ全件解決し、未解決と重複はいずれも0件だった
- 設定フォーム43ファイル、保存42ファイル、読込43ファイルはパスと内容が完全一致した
- `get_theme_option()`535呼出は引数と既定値を含めて一致し、既存の直接`set_theme_mod()`1件とリセット呼出1件も一致した
- ローカル実画面ではnonceと`select_index`を除く872個の名前付き要素、374件のPOST成功コントロールが、変更なし保存と再読込の前後で順序・型・値・状態まで一致した
- 新ナビゲーション内の名前付き要素とsubmit要素は0件であり、既存POSTへ値を追加しないことを確認した
- 例外は利用者が許可した`cocoon_settings_navigation_mode`だけで、現在管理者のサイト別user_optionとしてCocoon本体theme_modから分離されている
- 全件監査の初回追加時点では対象PHPUnit 435テスト・10729アサーション、実配布tar検査1テスト・41アサーションで成功した。この件数は履歴値であり、現行値ではない
- ナビゲーション実行型DOMテストとSCSS/CSS完全同期テストも、この時点で成功した

### 翻訳生成物の正規化記録

2026年8月26日、8言語のPOと辞書JavaScriptに大きな行単位差分が生じたが、正規生成・整形コマンドによる一回限りの正規化である。整形前後をロケールごとの翻訳キーと値の対応で比較し、今回追加するCocoon設定UI文言を除く既存翻訳値の変更は0件だった。生成物を手作業で旧整形へ戻さず、次のコマンドで再現する。

```powershell
npm run update-po
.\node_modules\.bin\prettier.cmd --write "scripts/translations/{de_DE,en_US,es_ES,fr_FR,ko_KR,pt_PT,zh_CN,zh_TW}.js"
npm run compile-all
npm run test:i18n
```

`npm run test:i18n`はPOT、8言語のPO・MO・l10n.phpと辞書ソースの対応を検証する。大きな整形差分のレビューでは行数ではなく、翻訳キーと値の意味的差分、新規UI文言、生成形式の一致を確認する。

### 現行作業ツリーの再検証記録

以下は2026年8月26日の記録値であり、自動生成されない。コードまたはテストを変更した場合は同じコマンドを再実行し、件数と結果を更新する。

| 範囲 | 再現コマンド | 2026年8月26日の記録結果 |
| --- | --- | --- |
| 全Unitテスト（distribution除外） | `.\vendor\bin\phpunit --do-not-cache-result --testsuite unit --exclude-group distribution` | 合格。1316テスト、12198アサーション |
| 設定UI対象PHPUnit | `.\vendor\bin\phpunit --do-not-cache-result tests\Unit\CocoonSettingsDesignContractTest.php tests\Unit\CocoonSettingsNavigationModeTest.php` | 合格。24テスト、1094アサーション |
| 実配布tar検査 | `.\vendor\bin\phpunit --do-not-cache-result --group distribution` | 合格。1テスト、41アサーション |
| データ契約と報告書鮮度 | `php scripts/audit-cocoon-settings-data-contract.php --check` | 合格。542設定、未解決0、重複0、差分0 |
| ナビゲーション実行型DOM | `npm run test:settings-navigation` | 合格 |
| SCSS/CSS完全同期 | `npm run test:settings-css` | 合格 |
| 8言語i18n整合 | `npm run test:i18n` | 合格 |
| 設定UI lint | `npm run lint:settings` | 合格 |
