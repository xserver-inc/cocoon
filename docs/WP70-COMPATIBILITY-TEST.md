# Cocoon テーマ WordPress 7.0 互換性テスト手順

WordPress 7.0 Beta 以降で Cocoon テーマの動作を確認するためのチェックリストです。

## 1. 管理画面カスタム列 (DataViews 互換性) のテスト

**対象:** [lib/admin.php](../lib/admin.php)、[skins/skin-made-in-heaven/lib/hook-wp.php](../skins/skin-made-in-heaven/lib/hook-wp.php)

WordPress 7.0 では管理画面のリストテーブルが DataViews に段階的に置き換えられます。以下のフィルターで追加しているカスタム列が正しく表示・動作するか確認してください。

**自動テスト:** 列コールバックの戻り値は [tests/Unit/AdminColumnsTest.php](../tests/Unit/AdminColumnsTest.php) で検証しています。`vendor/bin/phpunit tests/Unit/AdminColumnsTest.php` で実行可能です。

### テスト手順

1. **投稿一覧 (edit.php)**
   - [ ] カスタム列（ID、文字数、PV、アイキャッチ、メモ）が表示される
   - [ ] ソート可能列（投稿ID）がソートできる
   - [ ] クイック編集でメモ・メタディスクリプションが編集できる（skin-made-in-heaven 使用時）

2. **固定ページ一覧 (edit.php?post_type=page)**
   - [ ] カスタム列が表示される
   - [ ] スキン「Made in Heaven」使用時: スラッグ列が表示される
   - [ ] 「更新日クリア」ボタンが動作し、nonce エラーにならない（skin-made-in-heaven）

3. **カテゴリー・タグ一覧**
   - [ ] カスタム列（ID）が表示される
   - [ ] ソート可能である

4. **パターン / 再利用ブロック (edit.php?post_type=wp_block)**
   - [ ] カスタム列（パターン/ショートコード等）が表示される

5. **コメント一覧 (skin-made-in-heaven)**
   - [ ] コメントアイコン列が表示される

### 注意事項

- DataViews 移行は段階的なため、WP 7.0 では「投稿・固定ページ」は従来のリストテーブルのままの可能性があります。サイトエディタ内のテンプレート/ページ一覧が先に DataViews になる場合があります。
- 不具合が出た場合は [Make WordPress Core - 7.0](https://make.wordpress.org/core/7-0/) の Dev Notes および Field Guide で DataViews の拡張方法を確認してください。

---

## 2. 管理画面ビジュアルリフレッシュのテスト

**対象:** [lib/admin.php](../lib/admin.php)、[lib/dashboard-message.php](../lib/dashboard-message.php)、[skins/skin-neumorphism/functions.php](../skins/skin-neumorphism/functions.php)

WordPress 7.0 では管理画面に新しいタイポグラフィ、カラースキーム、View Transitions が導入されます。テーマが注入しているカスタム CSS がレイアウトを崩していないか確認してください。

**自動テスト:** カスタム管理 CSS の出力（`dashboard_message_css`・`add_head_post_custum`）は [tests/Unit/AdminVisualRefreshTest.php](../tests/Unit/AdminVisualRefreshTest.php) で検証しています。`vendor/bin/phpunit tests/Unit/AdminVisualRefreshTest.php` で実行可能です。

### テスト手順

1. **ダッシュボード**
   - [ ] ダッシュボードメッセージ（Cocoon の案内）のスタイルが崩れていない
   - [ ] 既存のウィジェット・ブロックが正しく表示される

2. **投稿・固定ページの編集画面**
   - [ ] カスタムメタボックス（目次、SEO、広告等）のレイアウトが崩れていない
   - [ ] アイキャッチ画像エリアのチェックボックス等が表示される
   - [ ] 管理用インラインスタイル（admin_head で出力している CSS）が新デザインと衝突していない

3. **外観・カスタマイズ (スキン: Neumorphism 等)**
   - [ ] カスタマイザーでカスタムコントロールが表示・動作する
   - [ ] プレビューが正しく更新される

4. **ウィジェット画面**
   - [ ] ウィジェット一覧・編集エリアが崩れていない

5. **テーマ設定ページ（Cocoon 設定）**
   - [ ] 各タブ・フォームが読みやすく表示される

### 注意事項

- 管理画面の「モダン」カラースキームがデフォルトになる可能性があります。色のコントラストやフォントサイズの変更の影響を確認してください。
- `admin_head` で出力している CSS が、新しい View Transitions やレイアウトと競合する場合は、セレクタの詳細度や `!important` の使用を見直してください。

---

## 実施記録

| 実施日 | WordPress バージョン | テスト1 (DataViews) | テスト2 (Visual Refresh) | 備考 |
|--------|----------------------|---------------------|---------------------------|------|
|        |                      |                     |                           |      |
