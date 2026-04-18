# エディター用リンク無効化クラス導入の修正案

## 目的
- エディタープレビューでの意図しない遷移を確実に防止する
- 既存表示やスタイルへの影響を最小化する
- 将来のテンプレート変更でも壊れにくくする

## 背景
現状は [lib/utils.php](lib/utils.php#L3815) の add_editor_no_link_click_class で最外部にクラスを付与し、[scss/gutenberg-editor.scss](scss/gutenberg-editor.scss#L1526) と [css/gutenberg-editor.css](css/gutenberg-editor.css#L6246) で a 要素の pointer-events を無効化している。

この方式はクリック防止には有効だが、次の弱点がある。
- キーボード操作でリンク遷移できる余地がある
- 先頭がHTMLタグでない場合にクラス付与が失敗する余地がある
- 同じクラスが重複挿入される余地がある

## 推奨方針
段階的に適用しやすいよう、最小修正案と堅牢化案を分ける。

---

## 案A: 影響最小の実装改善（推奨）

### A-1. クラス付与ロジックの堅牢化
対象: [lib/utils.php](lib/utils.php#L3815)

実施内容:
- 既存クラスがある場合に重複挿入しない
- 先頭がコメントや空白でも最初の要素タグを対象にできるよう改善
- 失敗時は入力HTMLをそのまま返す

実装イメージ（処理要件）:
1. 入力が空文字ならそのまま返す
2. 最初の開始タグを抽出
3. class 属性の有無を判定
4. 既に cocoon-editor-no-link-click が含まれていれば変更しない
5. class 属性ありなら先頭へ追加
6. class 属性なしなら class 属性を追加

### A-2. エディター内でのキーボード遷移も抑止
対象: [scss/gutenberg-editor.scss](scss/gutenberg-editor.scss#L1526)

実施内容:
- クリック抑止に加え、フォーカス時の遷移を抑える補助ルールを追加
- 例: text-decoration と outline の見た目調整、必要なら tabindex 制御をJS側で補助

注意:
- CSSだけで Enter 押下遷移を完全制御するのは難しい
- 完全抑止が必要なら、エディター専用JSで click と keydown を抑止する

### A-3. テストケースの追加
対象: [tests/Unit/UtilsTest.php](tests/Unit/UtilsTest.php#L446)

追加推奨テスト:
- 既に cocoon-editor-no-link-click を含む class がある場合
- 先頭が空白やHTMLコメントの場合
- class がシングルクォートの場合
- 空文字入力

受け入れ条件:
- 既存3テスト + 追加テストが全件成功
- 既存機能の出力が変わらない

---

## 案B: より堅牢な方式（将来拡張向け）

### B-1. 文字列置換からDOMパースへ移行
対象: [lib/utils.php](lib/utils.php#L3815)

実施内容:
- 最外部要素の class 操作を DOMDocument で実施
- 不正HTMLでも失敗時フォールバックで元文字列を返す

利点:
- 正規表現より壊れにくい
- 属性順序やクォート種別の差異に強い

懸念:
- 実装コストが上がる
- HTML断片の扱いに注意が必要

---

## 変更対象一覧（案Aベース）
- [lib/utils.php](lib/utils.php)
- [scss/gutenberg-editor.scss](scss/gutenberg-editor.scss)
- [css/gutenberg-editor.css](css/gutenberg-editor.css)
- [tests/Unit/UtilsTest.php](tests/Unit/UtilsTest.php)

## 影響範囲
- ServerSideRender を使うブロックのエディタープレビュー
- フロント表示への影響は is_rest 条件下のみのため限定的

## リスクと回避策
- リスク: エディター内リンク無効化の副作用で操作感が変わる
- 回避策: エディター限定クラススコープを維持し、フロント適用を避ける

- リスク: 正規表現修正で想定外HTMLに影響
- 回避策: テスト拡張と、変換不能時は無変更で返す方針

## 実施順序
1. add_editor_no_link_click_class の重複防止と先頭タグ対応
2. ユニットテスト追加
3. SCSS追記
4. スタイルビルド反映
5. エディター実機確認

## 完了判定
- ユニットテスト成功
- エディター上でマウスクリック遷移しない
- フロント表示に回帰がない
- 既存のブロック出力HTML構造が維持される
