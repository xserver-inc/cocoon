# Cocoon設定のタブ拡張

Cocoon設定では、子テーマやプラグインから独自タブと入力欄を追加できます。標準タブのID・HTML構造・保存処理の順序は維持しています。

フックは子テーマやプラグインの読み込み時に登録してください。スキンの `functions.php` は設定保存後に読み込むため、スキンからの登録や、スキンPHPで定義される関数・フックに依存したタブ登録には対応していません。

## 利用できるフック

| フック | 用途 | 実行タイミング |
| --- | --- | --- |
| `cocoon_settings_tabs` | 表示名の変更、並べ替え、独自タブの追加 | タブ構成を作るとき。保存時は保存前と保存後に呼ばれる |
| `cocoon_settings_tab_content_{タブID}` | 標準フォームの後ろ、または独自タブに入力欄を出力 | 各タブの内容を描画するとき |
| `cocoon_settings_save_{タブID}` | 拡張で追加した設定を保存 | 標準設定の保存後、スキン設定の再読み込み・CSSキャッシュ・ads.txt生成の前 |

タブ別アクションに引数はありません。保存アクションは管理者権限とCocoon設定のnonceを確認した通常の保存で実行します。選択中のタブだけでなく、登録されたすべてのタブが対象です。

既存の `cocoon_settings_before_save` と `cocoon_settings_after_save` は従来の位置で実行します。

## タブ定義のルール

`cocoon_settings_tabs` は、タブIDをキー、`array('label' => '表示名')` を値とする連想配列を受け取り、同じ形式の配列を返します。

- タブIDは半角英小文字から始め、半角英小文字・数字・ハイフン・アンダースコアで構成します。拡張固有の接頭辞を付けて、他の拡張との衝突を避けてください。
- `label` は空でない文字列にします。出力時にHTMLをエスケープするため、装飾用のHTMLは使用できません。
- 配列の順序がタブの表示順になります。選択値を復元できないときは先頭のタブを選択します。
- 標準タブを省略した場合や不正な定義にした場合は、そのタブを標準の定義で末尾に補完します。標準入力欄がなくなると、未送信の値が空として保存されるためです。
- フィルター全体が配列でない場合は標準構成に戻します。不正な独自タブは無視します。
- 無効なAMP・PWAは表示しません。`amp` と `pwa` は予約済みのIDです。
- `file`・`forms`・`posts` などのファイル指定は受け付けません。標準フォームの読み込み先は本体で管理し、拡張内容はアクションから出力します。

フィルターは保存前後に呼ばれるため、設定の更新などの副作用を持たせず、タブ定義を返す処理だけを記述してください。

タブ定義は入力欄と同じく、スキン制御変数 `$_THEME_OPTIONS` の固定値を一時的に外して評価します。`get_theme_option()` による登録条件はデータベースの保存値を参照するため、通常表示と保存時でスキンの読み込み順に左右されません。フィルターの処理後にはスキン制御変数を復元します。

## 独自タブを追加する例

子テーマの `functions.php` またはプラグインに追加します。既存のPHPファイルに追記する場合は、先頭の `<?php` は不要です。

```php
<?php
// 独自のタブIDと表示名を登録する。
add_filter('cocoon_settings_tabs', function ($tabs) {
  $tabs['my-child-settings'] = array('label' => '独自設定');
  return $tabs;
});

// Cocoon設定のフォーム内に入力欄を出力する。
add_action('cocoon_settings_tab_content_my-child-settings', function () {
  $message = get_theme_mod('my_child_message', '');
  ?>
  <h2>独自設定</h2>
  <p>
    <label for="my-child-message">メッセージ</label>
    <input type="text" id="my-child-message" name="my_child_message"
      value="<?php echo esc_attr($message); ?>">
  </p>
  <?php
});

// 不正な型を除外し、入力内容を文字列として整えてから保存する。
add_action('cocoon_settings_save_my-child-settings', function () {
  if (isset($_POST['my_child_message']) && is_string($_POST['my_child_message'])) {
    set_theme_mod('my_child_message', sanitize_text_field(wp_unslash($_POST['my_child_message'])));
  }
});
```

独自タブもCocoon設定全体と同じフォーム・保存ボタンを使います。別の `<form>` を入れたり、Cocoonのnonceや既存設定と同じ入力名を使ったりしないでください。追加CSSなしでタブを切り替えられます。

既存タブに入力欄を追加する場合は、たとえば `cocoon_settings_tab_content_widget-area` に登録します。内容の描画は、標準フォームと同じくスキン制御変数を一時的に外した状態で実行します。

## 保存とリセット

入力値の型の確認、サニタイズ、保存先の管理は拡張側が担当します。チェックボックスなど、未送信を「オフ」として扱う項目も拡張側で処理してください。

上の例のように `set_theme_mod()` で保存する値は、Cocoon設定の全リセットの対象に含まれます。リセット時は標準処理で画面を移動するため、タブ別の保存アクションには到達しません。

`update_option()` などで独立した場所に保存する拡張は、必要に応じて既存の `cocoon_settings_before_save` で `OP_RESET_ALL_SETTINGS` と `OP_CONFIRM_RESET_ALL_SETTINGS` の両方が送信されたことを確認し、自分が管理する設定だけをリセットしてください。

タブ構成から保存ファイルを決めることはありません。ナビ設定や、画面に出ていないAMP・PWAの既存処理も従来どおり実行します。
