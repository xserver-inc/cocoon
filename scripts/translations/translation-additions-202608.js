/* eslint-disable quotes, no-irregular-whitespace */
// 2026年8月の未翻訳監査で追加された共通メッセージ一覧
const keys = [
  `アイテムを下に移動`,
  `例：`,
  `ページが見つかりませんでした`,
  `サンプル記事タイトル`,
  `これはテーマ設定の見え方を確認するためのサンプル本文です。文字サイズ・行間・配色などの設定がどのように反映されるかをプレビューできます。`,
  `見出し2のサンプル`,
  `見出し2の装飾（背景色・ボーダー・キーカラー）がここに反映されます。本文の段落スタイルもあわせて確認できます。`,
  `見出し3のサンプル`,
  `見出し3の装飾を確認するためのサンプルテキストです。`,
  `これは引用ブロックのサンプルです。引用の装飾を確認できます。`,
  `リスト項目のサンプル1`,
  `リスト項目のサンプル2`,
  `リスト項目のサンプル3`,
  `リンクの色を確認するための`,
  `サンプルリンク`,
  `です。`,
  `Threads フォローボタンを表示`,
  `Reddit フォローボタンを表示`,
  `Redditをフォロー`,
  `コンテンツへスキップ`,
  `おすすめ記事カルーセル`,
  `Redditでシェア`,
  `リンク編集`,
  `リンク追加`,
  `リンクURL`,
  `設定するとグループブロック全体がリンク化されます。`,
  `リンク解除`,
  `※WordPress標準のマージンボトム設定よりこちらが優先されます。`,
  `プレビューを生成中…`,
  `※ 検索結果の価格は参考値です。実際の価格は販売ページでご確認ください。`,
  `期間設定`,
  `開始日時 (from)`,
  `日時を選択`,
  `終了日時 (to)`,
  `現在は表示期間内です`,
  `現在は表示期間外です`,
  `指定した期間中のみコンテンツを表示するブロックです。開始日時と終了日時を設定できます。`,
  `未ログインユーザーに表示するメッセージ`,
  `ログインしていないユーザーに対して、ここのメッセージが表示されます。`,
  `挿入するHTMLコードを入力します。[html]...[/html] ショートコードとして埋め込まれます。`,
  `キャンセル`,
  `選択した文字の上に表示する読み仮名を入力します。空欄の場合は選択文字がそのまま使われます。`,
  `ブログカード（埋め込み）`,
  `URLが設定されていません`,
  `すべてのユーザー`,
  `未更新`,
  `時`,
  `分`,
  `通知を表示しない`,
  `選択中の画像`,
  `カラーサンプルサイト`,
  `メインカラムトップの広告に注意`,
  `サイドバートップの広告に注意`,
  `該当する記事が見つかりませんでした。`,
  `読み込み中...`,
  `エラーが発生しました`,
  `通信エラーが発生しました`,
  `一致する項目がありません`,
  `直近%1$d日のPVとその前%1$d日のPVの比較`,
  `%d位のアイテムは、「名前」や「説明文」が入力されていないため追加保存は行っていません。`,
  `利用する場合は、投稿本文のどこでも良いのでに%sと入力してください。投稿・固定ページのみで利用できます。`,
  `デモ画像`,
  `SEO設定のメタディスクリプション`,
  `ニュース`,
  `タグ（無い場合はカテゴリー表示）`,
  `本メールアドレスは送信専用のため、返信できません。`,
  `フッター固定CTA`,
  `※この機能についての`,
  `詳しい説明はこちら`,
  `アフィリエイトタグのショートコード（こちらが入力されていないとフッター固定CTAは表示されません）`,
  `必ずアフィリエイトショートコードを入力してください。`,
  `アフィリエイトのタグは＜a＞タグで囲まれたものに限られます。それ以外はボタンになりません。`,
  `アフィリエイトタグのショートコードをここへ入力してください`,
  `マイクロコピーをここへ入力してください`,
  `CTAボタンの色`,
  `赤系`,
  `青系`,
  `緑系`,
  `マイクロコピーとボタンのレイアウト`,
  `縦並び`,
  `横並び`,
  `設定の詳細は<a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-zentai/">こちら</a>`,
  `影をつけるか`,
  `影をつけない`,
  `ちょっぴり影をつける`,
  `くっきり影をつける（背景が暗い場合はこれがおすすめ）`,
  `丸みをつけるか`,
  `丸みをつけない`,
  `ちょっぴり丸みをつける`,
  `くっきり丸みをつける`,
  `薄グレーゾーンの色`,
  `<small>目次やシェアボタン、フォローボタン等の背景色です。薄グレーゾーンといってますが、何色でもいいです。<br>※35%の透過となります。</small>`,
  `サイドバーにもこの背景色をつける`,
  `SNSトップシェアボタンを画面左に固定する`,
  `<small>PC表示でのみ固定。Cocoon設定で「メインカラムトップシェアボタンを表示する」にチェックを入れる必要があります。</small>`,
  `カテゴリーウィジェットの子カテゴリーをアコーディオン形式で開閉する`,
  `フロントページのタブ一覧のデザインを変更したり、各タブにアイコンを設定できます</br>\n        詳しくは<a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-tab/">こちら</a>`,
  `フロントページのタブ一覧のデザインをオリジナルのものにする`,
  `<small>アイコンの設定が必須です。</small>`,
  `1つ目のタブのアイコン`,
  `<small>FontAwesomeのUnicodeを入力します。</br>（例　f15c　）</small>`,
  `2つ目のタブのアイコン`,
  `<small>FontAwesomeのUnicodeを入力します。</br>（例　f164　）</small>`,
  `3つ目のタブのアイコン`,
  `4つ目のタブのアイコン`,
  `タブの背景色`,
  `タブの文字色`,
  `アクティブなタブの背景色①`,
  `<small>①と②を違う色にすることでグラデーションになります</small>`,
  `アクティブなタブの背景色②`,
  `グーグルフォントを選択する`,
  `font-weight: 400 のみ`,
  `フッター固定CTAの背景色などを変更できます。</br>設定の詳細は<a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-fix-cta/">こちら</a>`,
  `フッター固定CTAの背景色`,
  `0から1まで0.01単位で設定できます<br>0に近くなるほど透明になります`,
  `フッター固定CTAの背景色の透明度`,
  `フッター固定CTAのマイクロコピーの色`,
  `赤系ボタン`,
  `青系ボタン`,
  `緑系ボタン`,
  `カルーセルの表示件数などを変更できます。詳しくは<a target="_blank" href="https://go-blogs.com/cocoon/carousel-setting/">こちら</a>`,
  `2～6枚で設定可`,
  `画面幅 1241px以上での表示枚数`,
  `チェックなしの場合は表示枚数と同じ枚数スライドします`,
  `1枚ずつスライドさせる`,
  `画面幅 1024px～1240pxでの表示枚数`,
  `画面幅 835px～1023pxでの表示枚数`,
  `2～4枚で設定可`,
  `画面幅 481px～834pxでの表示枚数`,
  `1～2枚で設定可`,
  `画面幅 480px以下での表示枚数`,
  `各項目を変更して、【公開】ボタンを押すことでデザインが反映されます。`,
  `左固定サイドバー`,
  `PCでの閲覧時左端に固定で表示されるサイドバーです。`,
  `影`,
  `パネル`,
  `比較表（アイコンリスト）`,
  `アコーディオン（トグルボックス）`,
  `切り取り線`,
  `中央寄せ`,
  `水平`,
  `リンク`,
  `横長`,
  `よくある質問`,
  `アイコンなし`,
  `再利用ブロック`,
  `再利用ブロック一覧`,
  `Cocoonスキン「SILK」`,
  `設定を追加しました。`,
  `JSONファイルを選択してください。`,
  `オプション設定`,
  `スキンのオプション設定を追加します。Cocoon設定が変更されるので、事前にバックアップファイルを取得してください。`,
  `JSONファイルをアップロード:`,
  `設定の追加`,
  `スキンのオプション設定が書かれたJSONファイルを選択し、「設定の追加」ボタンを押してください。JSONファイルの作成方法はファイル名を除き、スキン制御に従います。`,
  `コードをコピーしました`,
  `比較１`,
  `比較２`,
  `リスト`,
  `比較表`,
  `グループブロックとアイコンリストブロックを組み合わせた比較表が作成できます。`,
  `カラム１`,
  `カラム２`,
  `全幅内カラム`,
  `全幅設定のグループブロック内にカラムブロックを入れたパターンです。`,
  `関連リンク`,
  `テキストリンク`,
  `リンクリストボックス`,
  `タブ見出しボックスブロック内にリンクリストを入れたパターンです。`,
  `スキンから入力したタイトル`,
  `スキンから入力したアピールエリアメッセージです。`,
  `スキンボタンキャプション`,
  `角の丸さ調節`,
  `【スキン】日本語フォント設定`,
  `ロゴフォント設定`,
  `ロゴテキストや記事タイトルなどのロゴフォントを設定できます。「設定なし」にするとCocoon設定 > 全体設定 > サイトフォント の設定を継承します。`,
  `クレー（デフォルト）`,
  `ZEN紅道`,
  `ZEN角ゴシック`,
  `ZEN丸ゴシック`,
  `キウイ丸`,
  `解星デコール`,
  `【スキン】背景パターン設定`,
  `背景パターン設定`,
  `背景のパターンをお選びください。「設定なし」にすると背景のパターンは削除され色のみになります。`,
  `グリッド（デフォルト）`,
  `罫線`,
  `ドット`,
  `【スキン】ロゴテキストの傍点設定`,
  `ロゴテキストの傍点デザイン`,
  `ロゴテキストの傍点をお選びください。「設定なし」にすると傍点は削除されます。`,
  `点（デフォルト）`,
  `点 白抜き`,
  `丸 白抜き`,
  `二重丸`,
  `二重丸 白抜き`,
  `三角 白抜き`,
  `ゴマ`,
  `ゴマ 白抜き`,
  `ロゴテキストの傍点位置`,
  `ロゴテキストの傍点の位置をお選びいただけます。`,
  `下（デフォルト）`,
  `上`,
  `【スキン】グローバルナビ設定`,
  `グローバルナビ色を設定したらカード装飾を解除`,
  `チェックを入れると、グローバルナビの色（背景色・文字色）を設定した際に、スマホのカード型メニューを通常表示に切り替えます（文字が見えなくなるのを防ぎます）。カード型を維持したい場合はチェックを外してください。`,
  `ログインしているユーザーにのみコンテンツを表示します`,
  `タイムラインのタイトル`,
  `アコーディオン見出し`,
  `キャンペーン期間中のみ表示されるコンテンツです。`,
  `選択する`,
  `汎用`,
  `ショートコード設定`,
  `検索ワードを入力する`,
  `URL検索`,
  `<div class="blank-box bb-red">誹謗中傷は予告なく削除します</div>`,
  `スキンNAGIの設定`,
  `<a href="http://whatismyscreenresolution.net/multi-screen-test" target="_blank" rel="noopener">WhatIsMyScreenResolution</a>チェック用リンクの表示。`,
];

const translations = {
  en_US: [
    `Move item down`,
    `Example:`,
    `Page not found`,
    `Sample article title`,
    `This is sample body text for previewing the theme settings. You can preview how settings such as font size, line spacing, and colors are applied.`,
    `Sample heading 2`,
    `The heading 2 styling (background color, border, and key color) is shown here. You can also review the paragraph styling.`,
    `Sample heading 3`,
    `This is sample text for reviewing the heading 3 styling.`,
    `This is a sample quote block. You can review the quote styling.`,
    `Sample list item 1`,
    `Sample list item 2`,
    `Sample list item 3`,
    `To review the link color, see this `,
    `sample link`,
    `.`,
    `Show Threads follow button`,
    `Show Reddit follow button`,
    `Follow on Reddit`,
    `Skip to content`,
    `Featured posts carousel`,
    `Share on Reddit`,
    `Edit link`,
    `Add link`,
    `Link URL`,
    `When set, the entire group block becomes a link.`,
    `Remove link`,
    `*This setting overrides the standard WordPress bottom margin setting.`,
    `Generating preview…`,
    `* Prices in search results are for reference only. Check the sales page for the actual price.`,
    `Display period settings`,
    `Start date and time (from)`,
    `Select date and time`,
    `End date and time (to)`,
    `The content is currently within the display period`,
    `The content is currently outside the display period`,
    `This block displays content only during the specified period. You can set its start and end date and time.`,
    `Message shown to logged-out users`,
    `This message is shown to users who are not logged in.`,
    `Enter the HTML code to insert. It will be embedded as an [html]...[/html] shortcode.`,
    `Cancel`,
    `Enter the reading to display above the selected text. If left blank, the selected text is used as-is.`,
    `Blog card (embed)`,
    `No URL has been set`,
    `All users`,
    `Not updated`,
    `hour`,
    `minute`,
    `Do not show notification`,
    `Selected image`,
    `Color sample website`,
    `Notice about the ad at the top of the main column`,
    `Notice about the ad at the top of the sidebar`,
    `No matching posts were found.`,
    `Loading...`,
    `An error occurred`,
    `A connection error occurred`,
    `No matching items`,
    `Comparison of page views in the last %1$d days with the preceding %1$d days`,
    `Item ranked %d was not additionally saved because its name or description was not entered.`,
    `To use it, enter %s anywhere in the post content. It is available only for posts and pages.`,
    `Demo image`,
    `Meta description in SEO settings`,
    `News`,
    `Tags (show categories if none)`,
    `This email address is send-only and cannot receive replies.`,
    `Fixed footer CTA`,
    `*About this feature: `,
    `View detailed instructions`,
    `Affiliate tag shortcode (the fixed footer CTA is not shown if this is empty)`,
    `Be sure to enter an affiliate shortcode.`,
    `Affiliate tags must be enclosed in an &lt;a&gt; tag. Other content will not become a button.`,
    `Enter the affiliate tag shortcode here`,
    `Enter the microcopy here`,
    `CTA button color`,
    `Red`,
    `Blue`,
    `Green`,
    `Microcopy and button layout`,
    `Vertical`,
    `Horizontal`,
    `See <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-zentai/">this page</a> for configuration details`,
    `Add a shadow`,
    `No shadow`,
    `Subtle shadow`,
    `Strong shadow (recommended for dark backgrounds)`,
    `Round the corners`,
    `No rounded corners`,
    `Slightly rounded corners`,
    `Strongly rounded corners`,
    `Light gray area color`,
    `<small>This is the background color of the table of contents, share buttons, follow buttons, and similar elements. Although called the light gray area, you may use any color.<br>*The opacity is 35%.</small>`,
    `Apply this background color to the sidebar`,
    `Fix the top social share buttons to the left side of the screen`,
    `<small>Fixed only on desktop. You must select “Show social share buttons at the top of the main column” in Cocoon Settings.</small>`,
    `Expand and collapse child categories in the Categories widget as an accordion`,
    `You can change the design of the front-page tab list and assign an icon to each tab.</br>\n        See <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-tab/">this page</a> for details`,
    `Use the original design for the front-page tab list`,
    `<small>Icon configuration is required.</small>`,
    `First tab icon`,
    `<small>Enter a Font Awesome Unicode value.</br>(Example: f15c)</small>`,
    `Second tab icon`,
    `<small>Enter a Font Awesome Unicode value.</br>(Example: f164)</small>`,
    `Third tab icon`,
    `Fourth tab icon`,
    `Tab background color`,
    `Tab text color`,
    `Active tab background color 1`,
    `<small>Use different colors for 1 and 2 to create a gradient.</small>`,
    `Active tab background color 2`,
    `Select a Google Font`,
    `font-weight: 400 only`,
    `You can change the fixed footer CTA background color and other settings.</br>See <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-fix-cta/">this page</a> for configuration details`,
    `Fixed footer CTA background color`,
    `Set a value from 0 to 1 in increments of 0.01.<br>Values closer to 0 are more transparent.`,
    `Fixed footer CTA background opacity`,
    `Fixed footer CTA microcopy color`,
    `Red button`,
    `Blue button`,
    `Green button`,
    `You can change the number of carousel items displayed and other settings. See <a target="_blank" href="https://go-blogs.com/cocoon/carousel-setting/">this page</a> for details.`,
    `Set from 2 to 6 items`,
    `Number of items at screen widths of 1241px or more`,
    `If unchecked, the carousel advances by the number of displayed items`,
    `Advance one item at a time`,
    `Number of items at screen widths from 1024px to 1240px`,
    `Number of items at screen widths from 835px to 1023px`,
    `Set from 2 to 4 items`,
    `Number of items at screen widths from 481px to 834px`,
    `Set from 1 to 2 items`,
    `Number of items at screen widths of 480px or less`,
    `Change the settings and click Publish to apply the design.`,
    `Fixed left sidebar`,
    `A sidebar fixed to the left edge when viewed on desktop.`,
    `Shadow`,
    `Panel`,
    `Comparison table (icon list)`,
    `Accordion (toggle box)`,
    `Cut line`,
    `Centered`,
    `Horizontal`,
    `Link`,
    `Wide`,
    `Frequently asked questions`,
    `No icon`,
    `Reusable block`,
    `Reusable block list`,
    `Cocoon skin “SILK”`,
    `The settings were added.`,
    `Select a JSON file.`,
    `Option settings`,
    `Add the skin's option settings. This changes Cocoon Settings, so create a backup file first.`,
    `Upload JSON file:`,
    `Add settings`,
    `Select a JSON file containing the skin's option settings, then click Add Settings. Apart from the filename, the method for creating the JSON file follows Skin Control.`,
    `Code copied`,
    `Comparison 1`,
    `Comparison 2`,
    `List`,
    `Comparison table`,
    `Create a comparison table by combining Group and Icon List blocks.`,
    `Column 1`,
    `Column 2`,
    `Columns inside full width`,
    `A pattern with a Columns block inside a Group block configured as full width.`,
    `Related links`,
    `Text link`,
    `Link list box`,
    `A pattern with a link list inside a Tab Caption Box block.`,
    `Title entered from the skin`,
    `This is an appeal area message entered from the skin.`,
    `Skin button caption`,
    `Corner radius adjustment`,
    `[Skin] Japanese font settings`,
    `Logo font settings`,
    `Set the logo font used for logo text, article titles, and similar text. Select None to inherit Cocoon Settings > Global > Site Font.`,
    `Klee (default)`,
    `Zen Kurenaido`,
    `Zen Kaku Gothic`,
    `Zen Maru Gothic`,
    `Kiwi Maru`,
    `Kaisei Decol`,
    `[Skin] Background pattern settings`,
    `Background pattern settings`,
    `Select a background pattern. Select None to remove the pattern and use only the background color.`,
    `Grid (default)`,
    `Ruled lines`,
    `Dots`,
    `[Skin] Logo text emphasis mark settings`,
    `Logo text emphasis mark design`,
    `Select the logo text emphasis marks. Select None to remove them.`,
    `Dot (default)`,
    `Hollow dot`,
    `Hollow circle`,
    `Double circle`,
    `Hollow double circle`,
    `Hollow triangle`,
    `Sesame dot`,
    `Hollow sesame dot`,
    `Logo text emphasis mark position`,
    `Select the position of the logo text emphasis marks.`,
    `Below (default)`,
    `Above`,
    `[Skin] Global navigation settings`,
    `Remove card styling when global navigation colors are set`,
    `When selected, setting the global navigation colors (background and text) changes the mobile card menu to its standard display to keep the text visible. Clear this option to retain the card style.`,
    `Displays content only to logged-in users`,
    `Timeline title`,
    `Accordion heading`,
    `This content is displayed only during the campaign period.`,
    `Select`,
    `General`,
    `Shortcode settings`,
    `Enter a search term`,
    `Search URL`,
    `<div class="blank-box bb-red">Abusive comments will be removed without notice.</div>`,
    `Skin NAGI settings`,
    `<a href="http://whatismyscreenresolution.net/multi-screen-test" target="_blank" rel="noopener">WhatIsMyScreenResolution</a> Display the link for checking.`,
  ],
  de_DE: [
    `Element nach unten verschieben`, // 001
    `Beispiel:`, // 002
    `Seite nicht gefunden`, // 003
    `Beispiel-Artikeltitel`, // 004
    `Dies ist ein Beispieltext zur Vorschau der Theme-Einstellungen. Sie können prüfen, wie Schriftgröße, Zeilenabstand, Farben und andere Einstellungen angewendet werden.`, // 005
    `Beispielüberschrift 2`, // 006
    `Hier wird die Gestaltung der Überschrift 2 (Hintergrundfarbe, Rahmen und Schlüsselfarbe) angezeigt. Auch die Absatzgestaltung kann geprüft werden.`, // 007
    `Beispielüberschrift 3`, // 008
    `Dies ist ein Beispieltext zur Prüfung der Gestaltung von Überschrift 3.`, // 009
    `Dies ist ein Beispiel für einen Zitatblock. Sie können die Zitatgestaltung prüfen.`, // 010
    `Beispiel-Listenelement 1`, // 011
    `Beispiel-Listenelement 2`, // 012
    `Beispiel-Listenelement 3`, // 013
    `Zur Prüfung der Linkfarbe dient dieser `, // 014
    `Beispiellink`, // 015
    `.`, // 016
    `Threads-Folgen-Schaltfläche anzeigen`, // 017
    `Reddit-Folgen-Schaltfläche anzeigen`, // 018
    `Auf Reddit folgen`, // 019
    `Zum Inhalt springen`, // 020
    `Karussell empfohlener Beiträge`, // 021
    `Auf Reddit teilen`, // 022
    `Link bearbeiten`, // 023
    `Link hinzufügen`, // 024
    `Link-URL`, // 025
    `Wenn festgelegt, wird der gesamte Gruppenblock zu einem Link.`, // 026
    `Link entfernen`, // 027
    `*Diese Einstellung hat Vorrang vor der WordPress-Standardeinstellung für den unteren Außenabstand.`, // 028
    `Vorschau wird erstellt…`, // 029
    `* Die Preise in den Suchergebnissen dienen nur als Richtwert. Den tatsächlichen Preis finden Sie auf der Verkaufsseite.`, // 030
    `Einstellungen für den Anzeigezeitraum`, // 031
    `Startdatum und -uhrzeit (von)`, // 032
    `Datum und Uhrzeit auswählen`, // 033
    `Enddatum und -uhrzeit (bis)`, // 034
    `Der Inhalt befindet sich derzeit innerhalb des Anzeigezeitraums`, // 035
    `Der Inhalt befindet sich derzeit außerhalb des Anzeigezeitraums`, // 036
    `Dieser Block zeigt Inhalte nur während des angegebenen Zeitraums an. Start- und Enddatum sowie die Uhrzeit können festgelegt werden.`, // 037
    `Nachricht für abgemeldete Benutzer`, // 038
    `Diese Nachricht wird Benutzern angezeigt, die nicht angemeldet sind.`, // 039
    `Geben Sie den einzufügenden HTML-Code ein. Er wird als Shortcode [html]...[/html] eingebettet.`, // 040
    `Abbrechen`, // 041
    `Geben Sie die Lesung ein, die über dem ausgewählten Text angezeigt werden soll. Bleibt das Feld leer, wird der ausgewählte Text unverändert verwendet.`, // 042
    `Blogkarte (Einbettung)`, // 043
    `Es wurde keine URL festgelegt`, // 044
    `Alle Benutzer`, // 045
    `Nicht aktualisiert`, // 046
    `Stunde`, // 047
    `Minute`, // 048
    `Benachrichtigung nicht anzeigen`, // 049
    `Ausgewähltes Bild`, // 050
    `Website mit Farbbeispielen`, // 051
    `Hinweis zur Anzeige oben in der Hauptspalte`, // 052
    `Hinweis zur Anzeige oben in der Seitenleiste`, // 053
    `Keine passenden Beiträge gefunden.`, // 054
    `Wird geladen...`, // 055
    `Ein Fehler ist aufgetreten`, // 056
    `Ein Verbindungsfehler ist aufgetreten`, // 057
    `Keine passenden Elemente`, // 058
    `Vergleich der Seitenaufrufe der letzten %1$d Tage mit den vorherigen %1$d Tagen`, // 059
    `Das Element auf Platz %d wurde nicht zusätzlich gespeichert, da Name oder Beschreibung nicht eingegeben wurden.`, // 060
    `Geben Sie zur Verwendung %s an einer beliebigen Stelle im Beitragsinhalt ein. Dies ist nur für Beiträge und Seiten verfügbar.`, // 061
    `Demobild`, // 062
    `Meta-Beschreibung in den SEO-Einstellungen`, // 063
    `Neuigkeiten`, // 064
    `Schlagwörter (falls keine vorhanden sind, Kategorien anzeigen)`, // 065
    `Diese E-Mail-Adresse dient nur zum Senden; Antworten können nicht empfangen werden.`, // 066
    `Fixierter Footer-CTA`, // 067
    `*Informationen zu dieser Funktion: `, // 068
    `Ausführliche Anleitung anzeigen`, // 069
    `Shortcode für Affiliate-Tag (der fixierte Footer-CTA wird nicht angezeigt, wenn dieses Feld leer ist)`, // 070
    `Geben Sie unbedingt einen Affiliate-Shortcode ein.`, // 071
    `Affiliate-Tags müssen von einem &lt;a&gt;-Tag umschlossen sein. Andere Inhalte werden nicht als Schaltfläche dargestellt.`, // 072
    `Shortcode für das Affiliate-Tag hier eingeben`, // 073
    `Mikrotext hier eingeben`, // 074
    `Farbe der CTA-Schaltfläche`, // 075
    `Rot`, // 076
    `Blau`, // 077
    `Grün`, // 078
    `Layout von Mikrotext und Schaltfläche`, // 079
    `Vertikal`, // 080
    `Horizontal`, // 081
    `Konfigurationsdetails finden Sie auf <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-zentai/">dieser Seite</a>`, // 082
    `Schatten hinzufügen`, // 083
    `Kein Schatten`, // 084
    `Dezenter Schatten`, // 085
    `Deutlicher Schatten (für dunkle Hintergründe empfohlen)`, // 086
    `Ecken abrunden`, // 087
    `Keine abgerundeten Ecken`, // 088
    `Leicht abgerundete Ecken`, // 089
    `Stark abgerundete Ecken`, // 090
    `Farbe des hellgrauen Bereichs`, // 091
    `<small>Dies ist die Hintergrundfarbe des Inhaltsverzeichnisses, der Teilen- und Folgen-Schaltflächen sowie ähnlicher Elemente. Der Bereich heißt zwar hellgrau, Sie können jedoch jede Farbe verwenden.<br>*Die Deckkraft beträgt 35 %.</small>`, // 092
    `Diese Hintergrundfarbe auch auf die Seitenleiste anwenden`, // 093
    `Obere Social-Share-Schaltflächen links am Bildschirm fixieren`, // 094
    `<small>Nur auf Desktop-Geräten fixiert. In den Cocoon-Einstellungen muss „Social-Share-Schaltflächen oben in der Hauptspalte anzeigen“ aktiviert sein.</small>`, // 095
    `Unterkategorien im Kategorie-Widget als Akkordeon ein- und ausklappen`, // 096
    `Sie können das Design der Tab-Liste auf der Startseite ändern und jedem Tab ein Symbol zuweisen.</br>\n        Weitere Informationen finden Sie auf <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-tab/">dieser Seite</a>`, // 097
    `Eigenes Design für die Tab-Liste der Startseite verwenden`, // 098
    `<small>Die Symbolkonfiguration ist erforderlich.</small>`, // 099
    `Symbol des ersten Tabs`, // 100
    `<small>Geben Sie einen Unicode-Wert von Font Awesome ein.</br>(Beispiel: f15c)</small>`, // 101
    `Symbol des zweiten Tabs`, // 102
    `<small>Geben Sie einen Unicode-Wert von Font Awesome ein.</br>(Beispiel: f164)</small>`, // 103
    `Symbol des dritten Tabs`, // 104
    `Symbol des vierten Tabs`, // 105
    `Tab-Hintergrundfarbe`, // 106
    `Tab-Textfarbe`, // 107
    `Hintergrundfarbe des aktiven Tabs 1`, // 108
    `<small>Verwenden Sie für 1 und 2 unterschiedliche Farben, um einen Verlauf zu erzeugen.</small>`, // 109
    `Hintergrundfarbe des aktiven Tabs 2`, // 110
    `Google Font auswählen`, // 111
    `nur font-weight: 400`, // 112
    `Sie können die Hintergrundfarbe und weitere Einstellungen des fixierten Footer-CTA ändern.</br>Konfigurationsdetails finden Sie auf <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-fix-cta/">dieser Seite</a>`, // 113
    `Hintergrundfarbe des fixierten Footer-CTA`, // 114
    `Legen Sie einen Wert von 0 bis 1 in Schritten von 0,01 fest.<br>Je näher der Wert an 0 liegt, desto transparenter wird die Anzeige.`, // 115
    `Deckkraft des Hintergrunds des fixierten Footer-CTA`, // 116
    `Farbe des Mikrotexts des fixierten Footer-CTA`, // 117
    `Rote Schaltfläche`, // 118
    `Blaue Schaltfläche`, // 119
    `Grüne Schaltfläche`, // 120
    `Sie können die Anzahl der angezeigten Karussell-Elemente und weitere Einstellungen ändern. Weitere Informationen finden Sie auf <a target="_blank" href="https://go-blogs.com/cocoon/carousel-setting/">dieser Seite</a>.`, // 121
    `2 bis 6 Elemente einstellbar`, // 122
    `Anzahl der Elemente bei Bildschirmbreiten ab 1241 px`, // 123
    `Wenn deaktiviert, bewegt sich das Karussell um die Anzahl der angezeigten Elemente`, // 124
    `Jeweils um ein Element bewegen`, // 125
    `Anzahl der Elemente bei Bildschirmbreiten von 1024 px bis 1240 px`, // 126
    `Anzahl der Elemente bei Bildschirmbreiten von 835 px bis 1023 px`, // 127
    `2 bis 4 Elemente einstellbar`, // 128
    `Anzahl der Elemente bei Bildschirmbreiten von 481 px bis 834 px`, // 129
    `1 bis 2 Elemente einstellbar`, // 130
    `Anzahl der Elemente bei Bildschirmbreiten bis 480 px`, // 131
    `Ändern Sie die Einstellungen und klicken Sie auf „Veröffentlichen“, um das Design anzuwenden.`, // 132
    `Fixierte linke Seitenleiste`, // 133
    `Eine Seitenleiste, die bei der Desktop-Anzeige am linken Rand fixiert ist.`, // 134
    `Schatten`, // 135
    `Panel`, // 136
    `Vergleichstabelle (Symbolliste)`, // 137
    `Akkordeon (Umschaltfeld)`, // 138
    `Schnittlinie`, // 139
    `Zentriert`, // 140
    `Horizontal`, // 141
    `Link`, // 142
    `Breit`, // 143
    `Häufig gestellte Fragen`, // 144
    `Kein Symbol`, // 145
    `Wiederverwendbarer Block`, // 146
    `Liste wiederverwendbarer Blöcke`, // 147
    `Cocoon-Skin „SILK“`, // 148
    `Die Einstellungen wurden hinzugefügt.`, // 149
    `Wählen Sie eine JSON-Datei aus.`, // 150
    `Optionseinstellungen`, // 151
    `Fügen Sie die Optionseinstellungen des Skins hinzu. Dadurch werden die Cocoon-Einstellungen geändert; erstellen Sie daher vorher eine Sicherungsdatei.`, // 152
    `JSON-Datei hochladen:`, // 153
    `Einstellungen hinzufügen`, // 154
    `Wählen Sie eine JSON-Datei mit den Optionseinstellungen des Skins aus und klicken Sie auf „Einstellungen hinzufügen“. Abgesehen vom Dateinamen folgt die Erstellung der JSON-Datei der Skin-Steuerung.`, // 155
    `Code kopiert`, // 156
    `Vergleich 1`, // 157
    `Vergleich 2`, // 158
    `Liste`, // 159
    `Vergleichstabelle`, // 160
    `Erstellen Sie eine Vergleichstabelle, indem Sie Gruppen- und Symbollistenblöcke kombinieren.`, // 161
    `Spalte 1`, // 162
    `Spalte 2`, // 163
    `Spalten innerhalb der vollen Breite`, // 164
    `Ein Muster mit einem Spaltenblock innerhalb eines Gruppenblocks, der auf volle Breite eingestellt ist.`, // 165
    `Zugehörige Links`, // 166
    `Textlink`, // 167
    `Linklistenfeld`, // 168
    `Ein Muster mit einer Linkliste in einem Tab-Überschriftenfeldblock.`, // 169
    `Vom Skin eingegebener Titel`, // 170
    `Dies ist eine vom Skin eingegebene Nachricht für den Hervorhebungsbereich.`, // 171
    `Beschriftung der Skin-Schaltfläche`, // 172
    `Einstellung des Eckenradius`, // 174
    `[Skin] Einstellungen für japanische Schriftarten`, // 175
    `Einstellungen für die Logo-Schriftart`, // 176
    `Legen Sie die Logo-Schriftart für Logotext, Artikeltitel und ähnliche Texte fest. Wählen Sie „Keine“, um Cocoon-Einstellungen > Allgemein > Website-Schriftart zu übernehmen.`, // 177
    `Klee (Standard)`, // 178
    `Zen Kurenaido`, // 179
    `Zen Kaku Gothic`, // 180
    `Zen Maru Gothic`, // 181
    `Kiwi Maru`, // 182
    `Kaisei Decol`, // 183
    `[Skin] Einstellungen für Hintergrundmuster`, // 184
    `Einstellungen für Hintergrundmuster`, // 185
    `Wählen Sie ein Hintergrundmuster aus. Wählen Sie „Keine“, um das Muster zu entfernen und nur die Hintergrundfarbe zu verwenden.`, // 186
    `Raster (Standard)`, // 187
    `Linien`, // 188
    `Punkte`, // 189
    `[Skin] Einstellungen für Betonungszeichen im Logotext`, // 190
    `Design der Betonungszeichen im Logotext`, // 191
    `Wählen Sie die Betonungszeichen für den Logotext aus. Wählen Sie „Keine“, um sie zu entfernen.`, // 192
    `Punkt (Standard)`, // 193
    `Hohler Punkt`, // 194
    `Hohler Kreis`, // 195
    `Doppelkreis`, // 196
    `Hohler Doppelkreis`, // 197
    `Hohles Dreieck`, // 198
    `Sesampunkt`, // 199
    `Hohler Sesampunkt`, // 200
    `Position der Betonungszeichen im Logotext`, // 201
    `Wählen Sie die Position der Betonungszeichen im Logotext aus.`, // 202
    `Unten (Standard)`, // 203
    `Oben`, // 204
    `[Skin] Einstellungen für die globale Navigation`, // 205
    `Kartenstil entfernen, wenn Farben für die globale Navigation festgelegt sind`, // 206
    `Wenn diese Option aktiviert ist, wechselt das Kartenmenü auf Mobilgeräten zur Standardanzeige, sobald Farben für die globale Navigation (Hintergrund und Text) festgelegt werden, damit der Text sichtbar bleibt. Deaktivieren Sie die Option, um den Kartenstil beizubehalten.`, // 207
    `Zeigt Inhalte nur angemeldeten Benutzern an`, // 208
    `Zeitleistentitel`, // 209
    `Akkordeonüberschrift`, // 210
    `Dieser Inhalt wird nur während des Kampagnenzeitraums angezeigt.`, // 211
    `Auswählen`, // 212
    `Allgemein`, // 213
    `Shortcode-Einstellungen`, // 214
    `Suchbegriff eingeben`, // 215
    `URL-Suche`, // 216
    `<div class="blank-box bb-red">Beleidigende Kommentare werden ohne Vorankündigung gelöscht.</div>`, // 217
    `Einstellungen für Skin NAGI`, // 219
    `<a href="http://whatismyscreenresolution.net/multi-screen-test" target="_blank" rel="noopener">WhatIsMyScreenResolution</a> Link zur Überprüfung anzeigen.`, // 220
  ],
  es_ES: [
    `Mover el elemento hacia abajo`, // 001
    `Ejemplo:`, // 002
    `No se ha encontrado la página`, // 003
    `Título de artículo de ejemplo`, // 004
    `Este es un texto de ejemplo para previsualizar los ajustes del tema. Puedes comprobar cómo se aplican el tamaño de la fuente, el interlineado, los colores y otros ajustes.`, // 005
    `Encabezado 2 de ejemplo`, // 006
    `Aquí se muestra el estilo del encabezado 2 (color de fondo, borde y color principal). También puedes revisar el estilo de los párrafos.`, // 007
    `Encabezado 3 de ejemplo`, // 008
    `Este es un texto de ejemplo para revisar el estilo del encabezado 3.`, // 009
    `Este es un bloque de cita de ejemplo. Puedes revisar el estilo de las citas.`, // 010
    `Elemento de lista de ejemplo 1`, // 011
    `Elemento de lista de ejemplo 2`, // 012
    `Elemento de lista de ejemplo 3`, // 013
    `Para comprobar el color de los enlaces, consulta este `, // 014
    `enlace de ejemplo`, // 015
    `.`, // 016
    `Mostrar el botón para seguir en Threads`, // 017
    `Mostrar el botón para seguir en Reddit`, // 018
    `Seguir en Reddit`, // 019
    `Saltar al contenido`, // 020
    `Carrusel de artículos recomendados`, // 021
    `Compartir en Reddit`, // 022
    `Editar enlace`, // 023
    `Añadir enlace`, // 024
    `URL del enlace`, // 025
    `Al establecerlo, todo el bloque de grupo se convierte en un enlace.`, // 026
    `Quitar enlace`, // 027
    `*Este ajuste tiene prioridad sobre el margen inferior estándar de WordPress.`, // 028
    `Generando la vista previa…`, // 029
    `* Los precios de los resultados de búsqueda son orientativos. Comprueba el precio real en la página de venta.`, // 030
    `Ajustes del periodo de visualización`, // 031
    `Fecha y hora de inicio (desde)`, // 032
    `Seleccionar fecha y hora`, // 033
    `Fecha y hora de finalización (hasta)`, // 034
    `El contenido está actualmente dentro del periodo de visualización`, // 035
    `El contenido está actualmente fuera del periodo de visualización`, // 036
    `Este bloque muestra contenido solo durante el periodo indicado. Puedes establecer la fecha y hora de inicio y de finalización.`, // 037
    `Mensaje mostrado a los usuarios que no han iniciado sesión`, // 038
    `Este mensaje se muestra a los usuarios que no han iniciado sesión.`, // 039
    `Introduce el código HTML que se insertará. Se incrustará como shortcode [html]...[/html].`, // 040
    `Cancelar`, // 041
    `Introduce la lectura que se mostrará sobre el texto seleccionado. Si se deja en blanco, se usará el texto seleccionado sin cambios.`, // 042
    `Tarjeta de blog (incrustada)`, // 043
    `No se ha establecido ninguna URL`, // 044
    `Todos los usuarios`, // 045
    `Sin actualizar`, // 046
    `hora`, // 047
    `minuto`, // 048
    `No mostrar la notificación`, // 049
    `Imagen seleccionada`, // 050
    `Sitio web de muestras de color`, // 051
    `Aviso sobre el anuncio de la parte superior de la columna principal`, // 052
    `Aviso sobre el anuncio de la parte superior de la barra lateral`, // 053
    `No se han encontrado entradas coincidentes.`, // 054
    `Cargando...`, // 055
    `Se ha producido un error`, // 056
    `Se ha producido un error de conexión`, // 057
    `No hay elementos coincidentes`, // 058
    `Comparación de las visitas de los últimos %1$d días con las de los %1$d días anteriores`, // 059
    `El elemento situado en la posición %d no se guardó adicionalmente porque no se introdujo su nombre o descripción.`, // 060
    `Para utilizarlo, introduce %s en cualquier lugar del contenido. Solo está disponible para entradas y páginas.`, // 061
    `Imagen de demostración`, // 062
    `Meta description de los ajustes SEO`, // 063
    `Noticias`, // 064
    `Etiquetas (mostrar categorías si no hay ninguna)`, // 065
    `Esta dirección de correo electrónico es solo para envíos y no puede recibir respuestas.`, // 066
    `CTA fijo del pie de página`, // 067
    `*Acerca de esta función: `, // 068
    `Ver las instrucciones detalladas`, // 069
    `Shortcode de la etiqueta de afiliado (el CTA fijo del pie de página no se muestra si está vacío)`, // 070
    `Asegúrate de introducir un shortcode de afiliado.`, // 071
    `Las etiquetas de afiliado deben estar encerradas en una etiqueta &lt;a&gt;. Cualquier otro contenido no se convertirá en un botón.`, // 072
    `Introduce aquí el shortcode de la etiqueta de afiliado`, // 073
    `Introduce aquí el microcopy`, // 074
    `Color del botón CTA`, // 075
    `Rojo`, // 076
    `Azul`, // 077
    `Verde`, // 078
    `Diseño del microcopy y del botón`, // 079
    `Vertical`, // 080
    `Horizontal`, // 081
    `Consulta <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-zentai/">esta página</a> para obtener detalles de la configuración`, // 082
    `Añadir una sombra`, // 083
    `Sin sombra`, // 084
    `Sombra sutil`, // 085
    `Sombra definida (recomendada para fondos oscuros)`, // 086
    `Redondear las esquinas`, // 087
    `Sin esquinas redondeadas`, // 088
    `Esquinas ligeramente redondeadas`, // 089
    `Esquinas muy redondeadas`, // 090
    `Color de la zona gris clara`, // 091
    `<small>Es el color de fondo de la tabla de contenido, los botones para compartir y seguir, y otros elementos similares. Aunque se llama zona gris clara, puedes usar cualquier color.<br>*La opacidad es del 35 %.</small>`, // 092
    `Aplicar también este color de fondo a la barra lateral`, // 093
    `Fijar los botones superiores para compartir en redes sociales a la izquierda de la pantalla`, // 094
    `<small>Solo se fija en ordenadores. Debes marcar «Mostrar los botones para compartir en redes sociales en la parte superior de la columna principal» en los ajustes de Cocoon.</small>`, // 095
    `Expandir y contraer como acordeón las categorías secundarias del widget de categorías`, // 096
    `Puedes cambiar el diseño de la lista de pestañas de la página de inicio y asignar un icono a cada pestaña.</br>\n        Consulta <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-tab/">esta página</a> para obtener más información`, // 097
    `Usar el diseño original para la lista de pestañas de la página de inicio`, // 098
    `<small>Es necesario configurar los iconos.</small>`, // 099
    `Icono de la primera pestaña`, // 100
    `<small>Introduce un valor Unicode de Font Awesome.</br>(Ejemplo: f15c)</small>`, // 101
    `Icono de la segunda pestaña`, // 102
    `<small>Introduce un valor Unicode de Font Awesome.</br>(Ejemplo: f164)</small>`, // 103
    `Icono de la tercera pestaña`, // 104
    `Icono de la cuarta pestaña`, // 105
    `Color de fondo de las pestañas`, // 106
    `Color del texto de las pestañas`, // 107
    `Color de fondo de la pestaña activa 1`, // 108
    `<small>Usa colores distintos para 1 y 2 a fin de crear un degradado.</small>`, // 109
    `Color de fondo de la pestaña activa 2`, // 110
    `Seleccionar una fuente de Google`, // 111
    `solo font-weight: 400`, // 112
    `Puedes cambiar el color de fondo y otros ajustes del CTA fijo del pie de página.</br>Consulta <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-fix-cta/">esta página</a> para obtener detalles de la configuración`, // 113
    `Color de fondo del CTA fijo del pie de página`, // 114
    `Establece un valor de 0 a 1 en incrementos de 0,01.<br>Cuanto más se acerque a 0, más transparente será.`, // 115
    `Opacidad del fondo del CTA fijo del pie de página`, // 116
    `Color del microcopy del CTA fijo del pie de página`, // 117
    `Botón rojo`, // 118
    `Botón azul`, // 119
    `Botón verde`, // 120
    `Puedes cambiar el número de elementos que muestra el carrusel y otros ajustes. Consulta <a target="_blank" href="https://go-blogs.com/cocoon/carousel-setting/">esta página</a> para obtener más información.`, // 121
    `Se pueden establecer de 2 a 6 elementos`, // 122
    `Número de elementos con anchos de pantalla de 1241 px o más`, // 123
    `Si no se marca, el carrusel avanza tantos elementos como se muestran`, // 124
    `Avanzar un elemento cada vez`, // 125
    `Número de elementos con anchos de pantalla de 1024 px a 1240 px`, // 126
    `Número de elementos con anchos de pantalla de 835 px a 1023 px`, // 127
    `Se pueden establecer de 2 a 4 elementos`, // 128
    `Número de elementos con anchos de pantalla de 481 px a 834 px`, // 129
    `Se pueden establecer de 1 a 2 elementos`, // 130
    `Número de elementos con anchos de pantalla de 480 px o menos`, // 131
    `Cambia los ajustes y haz clic en Publicar para aplicar el diseño.`, // 132
    `Barra lateral izquierda fija`, // 133
    `Una barra lateral fijada al borde izquierdo cuando se visualiza en un ordenador.`, // 134
    `Sombra`, // 135
    `Panel`, // 136
    `Tabla comparativa (lista de iconos)`, // 137
    `Acordeón (cuadro conmutador)`, // 138
    `Línea de corte`, // 139
    `Centrado`, // 140
    `Horizontal`, // 141
    `Enlace`, // 142
    `Ancho`, // 143
    `Preguntas frecuentes`, // 144
    `Sin icono`, // 145
    `Bloque reutilizable`, // 146
    `Lista de bloques reutilizables`, // 147
    `Skin «SILK» de Cocoon`, // 148
    `Se han añadido los ajustes.`, // 149
    `Selecciona un archivo JSON.`, // 150
    `Ajustes de opciones`, // 151
    `Añade los ajustes de opciones del skin. Esto cambia los ajustes de Cocoon, por lo que debes crear antes un archivo de copia de seguridad.`, // 152
    `Subir archivo JSON:`, // 153
    `Añadir ajustes`, // 154
    `Selecciona un archivo JSON que contenga los ajustes de opciones del skin y haz clic en Añadir ajustes. Salvo por el nombre del archivo, el método de creación del archivo JSON sigue el control del skin.`, // 155
    `Código copiado`, // 156
    `Comparación 1`, // 157
    `Comparación 2`, // 158
    `Lista`, // 159
    `Tabla comparativa`, // 160
    `Crea una tabla comparativa combinando bloques de grupo y de lista de iconos.`, // 161
    `Columna 1`, // 162
    `Columna 2`, // 163
    `Columnas dentro del ancho completo`, // 164
    `Un patrón con un bloque de columnas dentro de un bloque de grupo configurado a ancho completo.`, // 165
    `Enlaces relacionados`, // 166
    `Enlace de texto`, // 167
    `Cuadro de lista de enlaces`, // 168
    `Un patrón con una lista de enlaces dentro de un bloque de cuadro de encabezado de pestaña.`, // 169
    `Título introducido desde el skin`, // 170
    `Este es un mensaje del área de presentación introducido desde el skin.`, // 171
    `Leyenda del botón del skin`, // 172
    `Ajuste del radio de las esquinas`, // 174
    `[Skin] Ajustes de fuentes japonesas`, // 175
    `Ajustes de la fuente del logotipo`, // 176
    `Configura la fuente del logotipo utilizada para el texto del logotipo, los títulos de los artículos y textos similares. Selecciona Ninguna para heredar Ajustes de Cocoon > General > Fuente del sitio.`, // 177
    `Klee (predeterminada)`, // 178
    `Zen Kurenaido`, // 179
    `Zen Kaku Gothic`, // 180
    `Zen Maru Gothic`, // 181
    `Kiwi Maru`, // 182
    `Kaisei Decol`, // 183
    `[Skin] Ajustes del patrón de fondo`, // 184
    `Ajustes del patrón de fondo`, // 185
    `Selecciona un patrón de fondo. Selecciona Ninguno para quitar el patrón y usar solo el color de fondo.`, // 186
    `Cuadrícula (predeterminada)`, // 187
    `Líneas`, // 188
    `Puntos`, // 189
    `[Skin] Ajustes de marcas de énfasis del texto del logotipo`, // 190
    `Diseño de las marcas de énfasis del texto del logotipo`, // 191
    `Selecciona las marcas de énfasis del texto del logotipo. Selecciona Ninguna para quitarlas.`, // 192
    `Punto (predeterminado)`, // 193
    `Punto hueco`, // 194
    `Círculo hueco`, // 195
    `Círculo doble`, // 196
    `Círculo doble hueco`, // 197
    `Triángulo hueco`, // 198
    `Punto sésamo`, // 199
    `Punto sésamo hueco`, // 200
    `Posición de las marcas de énfasis del texto del logotipo`, // 201
    `Selecciona la posición de las marcas de énfasis del texto del logotipo.`, // 202
    `Debajo (predeterminado)`, // 203
    `Encima`, // 204
    `[Skin] Ajustes de navegación global`, // 205
    `Quitar el estilo de tarjeta al establecer los colores de la navegación global`, // 206
    `Al marcar esta opción, establecer los colores de la navegación global (fondo y texto) cambia el menú de tarjetas del móvil a la visualización estándar para mantener el texto visible. Desmarca la opción para conservar el estilo de tarjeta.`, // 207
    `Muestra contenido solo a los usuarios que han iniciado sesión`, // 208
    `Título de la cronología`, // 209
    `Encabezado del acordeón`, // 210
    `Este contenido solo se muestra durante el periodo de la campaña.`, // 211
    `Seleccionar`, // 212
    `General`, // 213
    `Ajustes de shortcodes`, // 214
    `Introducir un término de búsqueda`, // 215
    `Buscar URL`, // 216
    `<div class="blank-box bb-red">Los comentarios ofensivos se eliminarán sin previo aviso.</div>`, // 217
    `Ajustes de Skin NAGI`, // 219
    `<a href="http://whatismyscreenresolution.net/multi-screen-test" target="_blank" rel="noopener">WhatIsMyScreenResolution</a> Mostrar el enlace de comprobación.`, // 220
  ],
  fr_FR: [
    `Déplacer l’élément vers le bas`, // 001
    `Exemple :`, // 002
    `Page introuvable`, // 003
    `Exemple de titre d’article`, // 004
    `Ceci est un exemple de texte permettant de prévisualiser les réglages du thème. Vous pouvez vérifier l’application de la taille de police, de l’interligne, des couleurs et des autres réglages.`, // 005
    `Exemple de titre 2`, // 006
    `Le style du titre 2 (couleur d’arrière-plan, bordure et couleur principale) apparaît ici. Vous pouvez également vérifier le style des paragraphes.`, // 007
    `Exemple de titre 3`, // 008
    `Ceci est un exemple de texte permettant de vérifier le style du titre 3.`, // 009
    `Ceci est un exemple de bloc de citation. Vous pouvez vérifier le style des citations.`, // 010
    `Exemple d’élément de liste 1`, // 011
    `Exemple d’élément de liste 2`, // 012
    `Exemple d’élément de liste 3`, // 013
    `Pour vérifier la couleur des liens, consultez ce `, // 014
    `lien d’exemple`, // 015
    `.`, // 016
    `Afficher le bouton de suivi Threads`, // 017
    `Afficher le bouton de suivi Reddit`, // 018
    `Suivre sur Reddit`, // 019
    `Aller au contenu`, // 020
    `Carrousel d’articles recommandés`, // 021
    `Partager sur Reddit`, // 022
    `Modifier le lien`, // 023
    `Ajouter un lien`, // 024
    `URL du lien`, // 025
    `Une fois définie, cette option transforme tout le bloc Groupe en lien.`, // 026
    `Supprimer le lien`, // 027
    `*Ce réglage est prioritaire sur la marge inférieure standard de WordPress.`, // 028
    `Génération de l’aperçu…`, // 029
    `* Les prix des résultats de recherche sont fournis à titre indicatif. Consultez la page de vente pour connaître le prix réel.`, // 030
    `Réglages de la période d’affichage`, // 031
    `Date et heure de début (à partir de)`, // 032
    `Sélectionner la date et l’heure`, // 033
    `Date et heure de fin (jusqu’à)`, // 034
    `Le contenu se trouve actuellement dans la période d’affichage`, // 035
    `Le contenu se trouve actuellement hors de la période d’affichage`, // 036
    `Ce bloc affiche le contenu uniquement pendant la période indiquée. Vous pouvez définir ses dates et heures de début et de fin.`, // 037
    `Message affiché aux utilisateurs déconnectés`, // 038
    `Ce message est affiché aux utilisateurs qui ne sont pas connectés.`, // 039
    `Saisissez le code HTML à insérer. Il sera intégré sous forme de code court [html]...[/html].`, // 040
    `Annuler`, // 041
    `Saisissez la lecture à afficher au-dessus du texte sélectionné. Si le champ reste vide, le texte sélectionné sera utilisé tel quel.`, // 042
    `Carte de blog (intégration)`, // 043
    `Aucune URL n’a été définie`, // 044
    `Tous les utilisateurs`, // 045
    `Non mis à jour`, // 046
    `heure`, // 047
    `minute`, // 048
    `Ne pas afficher la notification`, // 049
    `Image sélectionnée`, // 050
    `Site d’exemples de couleurs`, // 051
    `Avis concernant la publicité en haut de la colonne principale`, // 052
    `Avis concernant la publicité en haut de la colonne latérale`, // 053
    `Aucun article correspondant n’a été trouvé.`, // 054
    `Chargement...`, // 055
    `Une erreur est survenue`, // 056
    `Une erreur de connexion est survenue`, // 057
    `Aucun élément correspondant`, // 058
    `Comparaison des pages vues des %1$d derniers jours avec celles des %1$d jours précédents`, // 059
    `L’élément classé %d n’a pas été enregistré en plus, car son nom ou sa description n’a pas été saisi.`, // 060
    `Pour l’utiliser, saisissez %s n’importe où dans le contenu. Cette fonction est disponible uniquement pour les articles et les pages.`, // 061
    `Image de démonstration`, // 062
    `Méta-description dans les réglages SEO`, // 063
    `Actualités`, // 064
    `Étiquettes (afficher les catégories s’il n’y en a aucune)`, // 065
    `Cette adresse e-mail sert uniquement à l’envoi et ne peut pas recevoir de réponse.`, // 066
    `CTA fixe du pied de page`, // 067
    `*À propos de cette fonctionnalité : `, // 068
    `Afficher les instructions détaillées`, // 069
    `Code court de balise d’affiliation (le CTA fixe du pied de page n’est pas affiché si ce champ est vide)`, // 070
    `Veillez à saisir un code court d’affiliation.`, // 071
    `Les balises d’affiliation doivent être placées dans une balise &lt;a&gt;. Tout autre contenu ne deviendra pas un bouton.`, // 072
    `Saisissez ici le code court de la balise d’affiliation`, // 073
    `Saisissez ici le microtexte`, // 074
    `Couleur du bouton CTA`, // 075
    `Rouge`, // 076
    `Bleu`, // 077
    `Vert`, // 078
    `Disposition du microtexte et du bouton`, // 079
    `Verticale`, // 080
    `Horizontale`, // 081
    `Consultez <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-zentai/">cette page</a> pour plus de détails sur la configuration`, // 082
    `Ajouter une ombre`, // 083
    `Aucune ombre`, // 084
    `Ombre légère`, // 085
    `Ombre marquée (recommandée sur un arrière-plan sombre)`, // 086
    `Arrondir les angles`, // 087
    `Aucun angle arrondi`, // 088
    `Angles légèrement arrondis`, // 089
    `Angles très arrondis`, // 090
    `Couleur de la zone gris clair`, // 091
    `<small>Il s’agit de la couleur d’arrière-plan de la table des matières, des boutons de partage et de suivi, ainsi que des éléments similaires. Bien qu’elle soit appelée zone gris clair, vous pouvez utiliser n’importe quelle couleur.<br>*L’opacité est de 35 %.</small>`, // 092
    `Appliquer également cette couleur d’arrière-plan à la colonne latérale`, // 093
    `Fixer les boutons supérieurs de partage social à gauche de l’écran`, // 094
    `<small>Fixé uniquement sur ordinateur. Vous devez cocher « Afficher les boutons de partage social en haut de la colonne principale » dans les réglages de Cocoon.</small>`, // 095
    `Développer et réduire en accordéon les catégories enfants du widget Catégories`, // 096
    `Vous pouvez modifier le style de la liste d’onglets de la page d’accueil et attribuer une icône à chaque onglet.</br>\n        Consultez <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-tab/">cette page</a> pour plus de détails`, // 097
    `Utiliser le style original pour la liste d’onglets de la page d’accueil`, // 098
    `<small>La configuration des icônes est obligatoire.</small>`, // 099
    `Icône du premier onglet`, // 100
    `<small>Saisissez une valeur Unicode Font Awesome.</br>(Exemple : f15c)</small>`, // 101
    `Icône du deuxième onglet`, // 102
    `<small>Saisissez une valeur Unicode Font Awesome.</br>(Exemple : f164)</small>`, // 103
    `Icône du troisième onglet`, // 104
    `Icône du quatrième onglet`, // 105
    `Couleur d’arrière-plan des onglets`, // 106
    `Couleur du texte des onglets`, // 107
    `Couleur d’arrière-plan de l’onglet actif 1`, // 108
    `<small>Utilisez des couleurs différentes pour 1 et 2 afin de créer un dégradé.</small>`, // 109
    `Couleur d’arrière-plan de l’onglet actif 2`, // 110
    `Sélectionner une police Google`, // 111
    `font-weight: 400 uniquement`, // 112
    `Vous pouvez modifier la couleur d’arrière-plan et les autres réglages du CTA fixe du pied de page.</br>Consultez <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-fix-cta/">cette page</a> pour plus de détails sur la configuration`, // 113
    `Couleur d’arrière-plan du CTA fixe du pied de page`, // 114
    `Définissez une valeur comprise entre 0 et 1 par incréments de 0,01.<br>Plus la valeur est proche de 0, plus l’affichage est transparent.`, // 115
    `Opacité de l’arrière-plan du CTA fixe du pied de page`, // 116
    `Couleur du microtexte du CTA fixe du pied de page`, // 117
    `Bouton rouge`, // 118
    `Bouton bleu`, // 119
    `Bouton vert`, // 120
    `Vous pouvez modifier le nombre d’éléments affichés dans le carrousel et d’autres réglages. Consultez <a target="_blank" href="https://go-blogs.com/cocoon/carousel-setting/">cette page</a> pour plus de détails.`, // 121
    `2 à 6 éléments peuvent être définis`, // 122
    `Nombre d’éléments pour une largeur d’écran de 1241 px ou plus`, // 123
    `Si cette option n’est pas cochée, le carrousel avance du nombre d’éléments affichés`, // 124
    `Avancer d’un élément à la fois`, // 125
    `Nombre d’éléments pour une largeur d’écran de 1024 px à 1240 px`, // 126
    `Nombre d’éléments pour une largeur d’écran de 835 px à 1023 px`, // 127
    `2 à 4 éléments peuvent être définis`, // 128
    `Nombre d’éléments pour une largeur d’écran de 481 px à 834 px`, // 129
    `1 à 2 éléments peuvent être définis`, // 130
    `Nombre d’éléments pour une largeur d’écran de 480 px ou moins`, // 131
    `Modifiez les réglages et cliquez sur Publier pour appliquer le style.`, // 132
    `Colonne latérale gauche fixe`, // 133
    `Une colonne latérale fixée au bord gauche lors de l’affichage sur ordinateur.`, // 134
    `Ombre`, // 135
    `Panneau`, // 136
    `Tableau comparatif (liste d’icônes)`, // 137
    `Accordéon (boîte à bascule)`, // 138
    `Ligne de découpe`, // 139
    `Centré`, // 140
    `Horizontal`, // 141
    `Lien`, // 142
    `Large`, // 143
    `Questions fréquentes`, // 144
    `Aucune icône`, // 145
    `Bloc réutilisable`, // 146
    `Liste des blocs réutilisables`, // 147
    `Skin Cocoon « SILK »`, // 148
    `Les réglages ont été ajoutés.`, // 149
    `Sélectionnez un fichier JSON.`, // 150
    `Réglages des options`, // 151
    `Ajoutez les réglages d’options du skin. Cette opération modifie les réglages de Cocoon ; créez donc d’abord un fichier de sauvegarde.`, // 152
    `Téléverser le fichier JSON :`, // 153
    `Ajouter les réglages`, // 154
    `Sélectionnez un fichier JSON contenant les réglages d’options du skin, puis cliquez sur Ajouter les réglages. À l’exception du nom de fichier, la méthode de création du fichier JSON suit le contrôle du skin.`, // 155
    `Code copié`, // 156
    `Comparaison 1`, // 157
    `Comparaison 2`, // 158
    `Liste`, // 159
    `Tableau comparatif`, // 160
    `Créez un tableau comparatif en combinant des blocs Groupe et Liste d’icônes.`, // 161
    `Colonne 1`, // 162
    `Colonne 2`, // 163
    `Colonnes dans la pleine largeur`, // 164
    `Un modèle comprenant un bloc Colonnes dans un bloc Groupe configuré en pleine largeur.`, // 165
    `Liens associés`, // 166
    `Lien texte`, // 167
    `Boîte de liste de liens`, // 168
    `Un modèle contenant une liste de liens dans un bloc Boîte de titre d’onglet.`, // 169
    `Titre saisi depuis le skin`, // 170
    `Ceci est un message de zone d’appel saisi depuis le skin.`, // 171
    `Légende du bouton du skin`, // 172
    `Réglage du rayon des angles`, // 174
    `[Skin] Réglages des polices japonaises`, // 175
    `Réglages de la police du logo`, // 176
    `Définissez la police du logo utilisée pour le texte du logo, les titres d’articles et les textes similaires. Sélectionnez Aucune pour hériter de Réglages Cocoon > Général > Police du site.`, // 177
    `Klee (par défaut)`, // 178
    `Zen Kurenaido`, // 179
    `Zen Kaku Gothic`, // 180
    `Zen Maru Gothic`, // 181
    `Kiwi Maru`, // 182
    `Kaisei Decol`, // 183
    `[Skin] Réglages du motif d’arrière-plan`, // 184
    `Réglages du motif d’arrière-plan`, // 185
    `Sélectionnez un motif d’arrière-plan. Sélectionnez Aucun pour supprimer le motif et utiliser uniquement la couleur d’arrière-plan.`, // 186
    `Grille (par défaut)`, // 187
    `Lignes`, // 188
    `Points`, // 189
    `[Skin] Réglages des marques d’emphase du texte du logo`, // 190
    `Style des marques d’emphase du texte du logo`, // 191
    `Sélectionnez les marques d’emphase du texte du logo. Sélectionnez Aucune pour les supprimer.`, // 192
    `Point (par défaut)`, // 193
    `Point creux`, // 194
    `Cercle creux`, // 195
    `Double cercle`, // 196
    `Double cercle creux`, // 197
    `Triangle creux`, // 198
    `Point sésame`, // 199
    `Point sésame creux`, // 200
    `Position des marques d’emphase du texte du logo`, // 201
    `Sélectionnez la position des marques d’emphase du texte du logo.`, // 202
    `En dessous (par défaut)`, // 203
    `Au-dessus`, // 204
    `[Skin] Réglages de la navigation globale`, // 205
    `Supprimer le style de carte lorsque les couleurs de navigation globale sont définies`, // 206
    `Lorsque cette option est cochée, la définition des couleurs de navigation globale (arrière-plan et texte) fait passer le menu en cartes sur mobile à son affichage standard afin que le texte reste visible. Décochez l’option pour conserver le style de carte.`, // 207
    `Affiche le contenu uniquement aux utilisateurs connectés`, // 208
    `Titre de la chronologie`, // 209
    `Titre de l’accordéon`, // 210
    `Ce contenu est affiché uniquement pendant la période de la campagne.`, // 211
    `Sélectionner`, // 212
    `Général`, // 213
    `Réglages des codes courts`, // 214
    `Saisir un terme de recherche`, // 215
    `Rechercher une URL`, // 216
    `<div class="blank-box bb-red">Les commentaires injurieux seront supprimés sans préavis.</div>`, // 217
    `Réglages du skin NAGI`, // 219
    `<a href="http://whatismyscreenresolution.net/multi-screen-test" target="_blank" rel="noopener">WhatIsMyScreenResolution</a> Afficher le lien de vérification.`, // 220
  ],
  ko_KR: [
    `항목을 아래로 이동`, // 001
    `예:`, // 002
    `페이지를 찾을 수 없습니다`, // 003
    `샘플 글 제목`, // 004
    `테마 설정의 표시 상태를 확인하기 위한 샘플 본문입니다. 글자 크기, 줄 간격, 색상 등의 설정이 어떻게 적용되는지 미리 볼 수 있습니다.`, // 005
    `샘플 제목 2`, // 006
    `제목 2의 장식(배경색, 테두리, 주요 색상)이 여기에 적용됩니다. 본문 단락 스타일도 함께 확인할 수 있습니다.`, // 007
    `샘플 제목 3`, // 008
    `제목 3의 장식을 확인하기 위한 샘플 텍스트입니다.`, // 009
    `인용 블록의 샘플입니다. 인용 장식을 확인할 수 있습니다.`, // 010
    `샘플 목록 항목 1`, // 011
    `샘플 목록 항목 2`, // 012
    `샘플 목록 항목 3`, // 013
    `링크 색상을 확인하기 위한 `, // 014
    `샘플 링크`, // 015
    `입니다.`, // 016
    `Threads 팔로우 버튼 표시`, // 017
    `Reddit 팔로우 버튼 표시`, // 018
    `Reddit 팔로우`, // 019
    `콘텐츠로 건너뛰기`, // 020
    `추천 글 캐러셀`, // 021
    `Reddit에 공유`, // 022
    `링크 편집`, // 023
    `링크 추가`, // 024
    `링크 URL`, // 025
    `설정하면 그룹 블록 전체가 링크로 바뀝니다.`, // 026
    `링크 해제`, // 027
    `※이 설정은 WordPress 기본 아래쪽 여백 설정보다 우선합니다.`, // 028
    `미리보기 생성 중…`, // 029
    `※ 검색 결과의 가격은 참고용입니다. 실제 가격은 판매 페이지에서 확인하세요.`, // 030
    `표시 기간 설정`, // 031
    `시작 날짜 및 시간 (from)`, // 032
    `날짜 및 시간 선택`, // 033
    `종료 날짜 및 시간 (to)`, // 034
    `현재 표시 기간 내입니다`, // 035
    `현재 표시 기간 밖입니다`, // 036
    `지정한 기간에만 콘텐츠를 표시하는 블록입니다. 시작 및 종료 날짜와 시간을 설정할 수 있습니다.`, // 037
    `로그아웃 사용자에게 표시할 메시지`, // 038
    `로그인하지 않은 사용자에게 이 메시지가 표시됩니다.`, // 039
    `삽입할 HTML 코드를 입력합니다. [html]...[/html] 쇼트코드로 삽입됩니다.`, // 040
    `취소`, // 041
    `선택한 문자 위에 표시할 읽는 법을 입력합니다. 비워 두면 선택한 문자가 그대로 사용됩니다.`, // 042
    `블로그 카드(삽입)`, // 043
    `URL이 설정되지 않았습니다`, // 044
    `모든 사용자`, // 045
    `업데이트되지 않음`, // 046
    `시`, // 047
    `분`, // 048
    `알림 표시 안 함`, // 049
    `선택한 이미지`, // 050
    `색상 샘플 사이트`, // 051
    `메인 열 상단 광고 주의`, // 052
    `사이드바 상단 광고 주의`, // 053
    `해당하는 글을 찾을 수 없습니다.`, // 054
    `불러오는 중...`, // 055
    `오류가 발생했습니다`, // 056
    `통신 오류가 발생했습니다`, // 057
    `일치하는 항목이 없습니다`, // 058
    `최근 %1$d일의 페이지 조회수와 그 이전 %1$d일의 페이지 조회수 비교`, // 059
    `%d위 항목은 이름 또는 설명이 입력되지 않아 추가로 저장하지 않았습니다.`, // 060
    `사용하려면 글 본문의 아무 곳에나 %s을(를) 입력하세요. 글과 페이지만 사용할 수 있습니다.`, // 061
    `데모 이미지`, // 062
    `SEO 설정의 메타 설명`, // 063
    `뉴스`, // 064
    `태그(없으면 카테고리 표시)`, // 065
    `이 이메일 주소는 발신 전용이므로 회신할 수 없습니다.`, // 066
    `푸터 고정 CTA`, // 067
    `※이 기능에 대한 `, // 068
    `자세한 설명 보기`, // 069
    `제휴 태그 쇼트코드(입력하지 않으면 푸터 고정 CTA가 표시되지 않습니다)`, // 070
    `제휴 쇼트코드를 반드시 입력하세요.`, // 071
    `제휴 태그는 &lt;a&gt; 태그로 둘러싸인 것만 사용할 수 있습니다. 그 외의 내용은 버튼이 되지 않습니다.`, // 072
    `여기에 제휴 태그 쇼트코드를 입력하세요`, // 073
    `여기에 마이크로카피를 입력하세요`, // 074
    `CTA 버튼 색상`, // 075
    `빨간색 계열`, // 076
    `파란색 계열`, // 077
    `녹색 계열`, // 078
    `마이크로카피와 버튼 배치`, // 079
    `세로 배치`, // 080
    `가로 배치`, // 081
    `설정에 대한 자세한 내용은 <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-zentai/">여기</a>를 참조하세요`, // 082
    `그림자 적용`, // 083
    `그림자 없음`, // 084
    `약한 그림자`, // 085
    `선명한 그림자(배경이 어두울 때 권장)`, // 086
    `모서리 둥글게`, // 087
    `둥근 모서리 없음`, // 088
    `모서리를 약간 둥글게`, // 089
    `모서리를 많이 둥글게`, // 090
    `연한 회색 영역 색상`, // 091
    `<small>목차, 공유 버튼, 팔로우 버튼 등의 배경색입니다. 연한 회색 영역이라고 되어 있지만 어떤 색이든 사용할 수 있습니다.<br>※투명도는 35%입니다.</small>`, // 092
    `사이드바에도 이 배경색 적용`, // 093
    `상단 SNS 공유 버튼을 화면 왼쪽에 고정`, // 094
    `<small>PC에서만 고정됩니다. Cocoon 설정에서 “메인 열 상단 공유 버튼 표시”를 선택해야 합니다.</small>`, // 095
    `카테고리 위젯의 하위 카테고리를 아코디언 형식으로 열고 닫기`, // 096
    `첫 페이지 탭 목록의 디자인을 변경하고 각 탭에 아이콘을 설정할 수 있습니다.</br>\n        자세한 내용은 <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-tab/">여기</a>를 참조하세요`, // 097
    `첫 페이지 탭 목록에 독자적인 디자인 사용`, // 098
    `<small>아이콘 설정이 필요합니다.</small>`, // 099
    `첫 번째 탭 아이콘`, // 100
    `<small>Font Awesome Unicode를 입력하세요.</br>(예: f15c)</small>`, // 101
    `두 번째 탭 아이콘`, // 102
    `<small>Font Awesome Unicode를 입력하세요.</br>(예: f164)</small>`, // 103
    `세 번째 탭 아이콘`, // 104
    `네 번째 탭 아이콘`, // 105
    `탭 배경색`, // 106
    `탭 글자색`, // 107
    `활성 탭 배경색 1`, // 108
    `<small>1과 2를 서로 다른 색으로 설정하면 그라데이션이 됩니다.</small>`, // 109
    `활성 탭 배경색 2`, // 110
    `Google 글꼴 선택`, // 111
    `font-weight: 400만`, // 112
    `푸터 고정 CTA의 배경색 등을 변경할 수 있습니다.</br>설정에 대한 자세한 내용은 <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-fix-cta/">여기</a>를 참조하세요`, // 113
    `푸터 고정 CTA 배경색`, // 114
    `0부터 1까지 0.01 단위로 설정할 수 있습니다.<br>0에 가까울수록 투명해집니다.`, // 115
    `푸터 고정 CTA 배경 투명도`, // 116
    `푸터 고정 CTA 마이크로카피 색상`, // 117
    `빨간색 버튼`, // 118
    `파란색 버튼`, // 119
    `녹색 버튼`, // 120
    `캐러셀 표시 개수 등의 설정을 변경할 수 있습니다. 자세한 내용은 <a target="_blank" href="https://go-blogs.com/cocoon/carousel-setting/">여기</a>를 참조하세요.`, // 121
    `2~6개 설정 가능`, // 122
    `화면 너비 1241px 이상일 때 표시 개수`, // 123
    `선택하지 않으면 표시 개수만큼 이동합니다`, // 124
    `한 번에 한 개씩 이동`, // 125
    `화면 너비 1024px~1240px일 때 표시 개수`, // 126
    `화면 너비 835px~1023px일 때 표시 개수`, // 127
    `2~4개 설정 가능`, // 128
    `화면 너비 481px~834px일 때 표시 개수`, // 129
    `1~2개 설정 가능`, // 130
    `화면 너비 480px 이하일 때 표시 개수`, // 131
    `각 항목을 변경하고 공개 버튼을 누르면 디자인이 적용됩니다.`, // 132
    `왼쪽 고정 사이드바`, // 133
    `PC에서 볼 때 왼쪽 가장자리에 고정되는 사이드바입니다.`, // 134
    `그림자`, // 135
    `패널`, // 136
    `비교표(아이콘 목록)`, // 137
    `아코디언(토글 상자)`, // 138
    `절취선`, // 139
    `가운데 정렬`, // 140
    `가로`, // 141
    `링크`, // 142
    `가로로 긴 형태`, // 143
    `자주 묻는 질문`, // 144
    `아이콘 없음`, // 145
    `재사용 가능 블록`, // 146
    `재사용 가능 블록 목록`, // 147
    `Cocoon 스킨 “SILK”`, // 148
    `설정을 추가했습니다.`, // 149
    `JSON 파일을 선택하세요.`, // 150
    `옵션 설정`, // 151
    `스킨의 옵션 설정을 추가합니다. Cocoon 설정이 변경되므로 먼저 백업 파일을 만드세요.`, // 152
    `JSON 파일 업로드:`, // 153
    `설정 추가`, // 154
    `스킨 옵션 설정이 담긴 JSON 파일을 선택한 후 설정 추가 버튼을 누르세요. 파일 이름을 제외한 JSON 파일 작성 방법은 스킨 제어를 따릅니다.`, // 155
    `코드를 복사했습니다`, // 156
    `비교 1`, // 157
    `비교 2`, // 158
    `목록`, // 159
    `비교표`, // 160
    `그룹 블록과 아이콘 목록 블록을 조합하여 비교표를 만들 수 있습니다.`, // 161
    `열 1`, // 162
    `열 2`, // 163
    `전체 너비 안의 열`, // 164
    `전체 너비로 설정한 그룹 블록 안에 열 블록을 넣은 패턴입니다.`, // 165
    `관련 링크`, // 166
    `텍스트 링크`, // 167
    `링크 목록 상자`, // 168
    `탭 제목 상자 블록 안에 링크 목록을 넣은 패턴입니다.`, // 169
    `스킨에서 입력한 제목`, // 170
    `스킨에서 입력한 어필 영역 메시지입니다.`, // 171
    `스킨 버튼 캡션`, // 172
    `모서리 둥글기 조절`, // 174
    `[스킨] 일본어 글꼴 설정`, // 175
    `로고 글꼴 설정`, // 176
    `로고 텍스트와 글 제목 등에 사용할 로고 글꼴을 설정합니다. 없음을 선택하면 Cocoon 설정 > 전체 설정 > 사이트 글꼴 설정을 상속합니다.`, // 177
    `Klee(기본값)`, // 178
    `Zen Kurenaido`, // 179
    `Zen Kaku Gothic`, // 180
    `Zen Maru Gothic`, // 181
    `Kiwi Maru`, // 182
    `Kaisei Decol`, // 183
    `[스킨] 배경 패턴 설정`, // 184
    `배경 패턴 설정`, // 185
    `배경 패턴을 선택하세요. 없음을 선택하면 패턴이 제거되고 배경색만 사용됩니다.`, // 186
    `격자(기본값)`, // 187
    `괘선`, // 188
    `점`, // 189
    `[스킨] 로고 텍스트 강조점 설정`, // 190
    `로고 텍스트 강조점 디자인`, // 191
    `로고 텍스트의 강조점을 선택하세요. 없음을 선택하면 강조점이 제거됩니다.`, // 192
    `점(기본값)`, // 193
    `빈 점`, // 194
    `빈 원`, // 195
    `이중 원`, // 196
    `빈 이중 원`, // 197
    `빈 삼각형`, // 198
    `깨점`, // 199
    `빈 깨점`, // 200
    `로고 텍스트 강조점 위치`, // 201
    `로고 텍스트 강조점의 위치를 선택할 수 있습니다.`, // 202
    `아래(기본값)`, // 203
    `위`, // 204
    `[스킨] 전역 탐색 설정`, // 205
    `전역 탐색 색상을 설정하면 카드 장식 해제`, // 206
    `선택하면 전역 탐색 색상(배경색 및 글자색)을 설정할 때 모바일 카드형 메뉴가 일반 표시로 전환되어 글자가 보이지 않는 문제를 방지합니다. 카드형을 유지하려면 선택을 해제하세요.`, // 207
    `로그인한 사용자에게만 콘텐츠를 표시합니다`, // 208
    `타임라인 제목`, // 209
    `아코디언 제목`, // 210
    `캠페인 기간에만 표시되는 콘텐츠입니다.`, // 211
    `선택`, // 212
    `일반`, // 213
    `쇼트코드 설정`, // 214
    `검색어 입력`, // 215
    `URL 검색`, // 216
    `<div class="blank-box bb-red">비방 및 욕설은 예고 없이 삭제됩니다.</div>`, // 217
    `스킨 NAGI 설정`, // 219
    `<a href="http://whatismyscreenresolution.net/multi-screen-test" target="_blank" rel="noopener">WhatIsMyScreenResolution</a> 확인용 링크를 표시합니다.`, // 220
  ],
  pt_PT: [
    `Mover o item para baixo`, // 001
    `Exemplo:`, // 002
    `Página não encontrada`, // 003
    `Título de artigo de exemplo`, // 004
    `Este é um texto de exemplo para pré-visualizar as definições do tema. Pode verificar como são aplicados o tamanho do tipo de letra, o espaçamento entre linhas, as cores e outras definições.`, // 005
    `Cabeçalho 2 de exemplo`, // 006
    `O estilo do cabeçalho 2 (cor de fundo, contorno e cor principal) é apresentado aqui. Também pode verificar o estilo dos parágrafos.`, // 007
    `Cabeçalho 3 de exemplo`, // 008
    `Este é um texto de exemplo para verificar o estilo do cabeçalho 3.`, // 009
    `Este é um bloco de citação de exemplo. Pode verificar o estilo das citações.`, // 010
    `Item de lista de exemplo 1`, // 011
    `Item de lista de exemplo 2`, // 012
    `Item de lista de exemplo 3`, // 013
    `Para verificar a cor das ligações, consulte esta `, // 014
    `ligação de exemplo`, // 015
    `.`, // 016
    `Mostrar o botão para seguir no Threads`, // 017
    `Mostrar o botão para seguir no Reddit`, // 018
    `Seguir no Reddit`, // 019
    `Saltar para o conteúdo`, // 020
    `Carrossel de artigos recomendados`, // 021
    `Partilhar no Reddit`, // 022
    `Editar ligação`, // 023
    `Adicionar ligação`, // 024
    `URL da ligação`, // 025
    `Quando definida, esta opção transforma todo o bloco de grupo numa ligação.`, // 026
    `Remover ligação`, // 027
    `*Esta definição tem prioridade sobre a margem inferior padrão do WordPress.`, // 028
    `A gerar a pré-visualização…`, // 029
    `* Os preços nos resultados de pesquisa são apenas indicativos. Consulte a página de venda para confirmar o preço real.`, // 030
    `Definições do período de apresentação`, // 031
    `Data e hora de início (desde)`, // 032
    `Selecionar data e hora`, // 033
    `Data e hora de fim (até)`, // 034
    `O conteúdo encontra-se atualmente dentro do período de apresentação`, // 035
    `O conteúdo encontra-se atualmente fora do período de apresentação`, // 036
    `Este bloco apresenta conteúdo apenas durante o período especificado. Pode definir as datas e horas de início e de fim.`, // 037
    `Mensagem apresentada aos utilizadores com sessão terminada`, // 038
    `Esta mensagem é apresentada aos utilizadores que não têm sessão iniciada.`, // 039
    `Introduza o código HTML a inserir. Será incorporado como shortcode [html]...[/html].`, // 040
    `Cancelar`, // 041
    `Introduza a leitura a apresentar por cima do texto selecionado. Se deixar o campo vazio, o texto selecionado será utilizado sem alterações.`, // 042
    `Cartão de blogue (incorporação)`, // 043
    `Não foi definido nenhum URL`, // 044
    `Todos os utilizadores`, // 045
    `Não atualizado`, // 046
    `hora`, // 047
    `minuto`, // 048
    `Não mostrar a notificação`, // 049
    `Imagem selecionada`, // 050
    `Site de amostras de cores`, // 051
    `Aviso sobre o anúncio no topo da coluna principal`, // 052
    `Aviso sobre o anúncio no topo da barra lateral`, // 053
    `Não foram encontrados artigos correspondentes.`, // 054
    `A carregar...`, // 055
    `Ocorreu um erro`, // 056
    `Ocorreu um erro de comunicação`, // 057
    `Não existem itens correspondentes`, // 058
    `Comparação das visualizações dos últimos %1$d dias com as dos %1$d dias anteriores`, // 059
    `O item na posição %d não foi guardado adicionalmente porque não foi introduzido o respetivo nome ou descrição.`, // 060
    `Para utilizar, introduza %s em qualquer ponto do conteúdo. Está disponível apenas para artigos e páginas.`, // 061
    `Imagem de demonstração`, // 062
    `Meta descrição nas definições de SEO`, // 063
    `Notícias`, // 064
    `Etiquetas (mostrar categorias se não existirem)`, // 065
    `Este endereço de e-mail destina-se apenas ao envio e não pode receber respostas.`, // 066
    `CTA fixo no rodapé`, // 067
    `*Sobre esta funcionalidade: `, // 068
    `Ver instruções detalhadas`, // 069
    `Shortcode da etiqueta de afiliado (o CTA fixo no rodapé não é apresentado se este campo estiver vazio)`, // 070
    `Certifique-se de que introduz um shortcode de afiliado.`, // 071
    `As etiquetas de afiliado têm de estar dentro de uma etiqueta &lt;a&gt;. Qualquer outro conteúdo não será transformado num botão.`, // 072
    `Introduza aqui o shortcode da etiqueta de afiliado`, // 073
    `Introduza aqui o microtexto`, // 074
    `Cor do botão CTA`, // 075
    `Vermelho`, // 076
    `Azul`, // 077
    `Verde`, // 078
    `Disposição do microtexto e do botão`, // 079
    `Vertical`, // 080
    `Horizontal`, // 081
    `Consulte <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-zentai/">esta página</a> para obter detalhes da configuração`, // 082
    `Adicionar uma sombra`, // 083
    `Sem sombra`, // 084
    `Sombra discreta`, // 085
    `Sombra forte (recomendada para fundos escuros)`, // 086
    `Arredondar os cantos`, // 087
    `Sem cantos arredondados`, // 088
    `Cantos ligeiramente arredondados`, // 089
    `Cantos muito arredondados`, // 090
    `Cor da zona cinzenta clara`, // 091
    `<small>Esta é a cor de fundo do índice, dos botões de partilha e de seguir e de outros elementos semelhantes. Apesar de ser chamada zona cinzenta clara, pode utilizar qualquer cor.<br>*A opacidade é de 35%.</small>`, // 092
    `Aplicar também esta cor de fundo à barra lateral`, // 093
    `Fixar os botões superiores de partilha social à esquerda do ecrã`, // 094
    `<small>Fixo apenas no computador. Tem de selecionar “Mostrar os botões de partilha social no topo da coluna principal” nas definições do Cocoon.</small>`, // 095
    `Expandir e recolher como acordeão as categorias dependentes do widget de categorias`, // 096
    `Pode alterar o estilo da lista de separadores da página inicial e atribuir um ícone a cada separador.</br>\n        Consulte <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-tab/">esta página</a> para obter detalhes`, // 097
    `Utilizar o estilo original na lista de separadores da página inicial`, // 098
    `<small>A configuração dos ícones é obrigatória.</small>`, // 099
    `Ícone do primeiro separador`, // 100
    `<small>Introduza um valor Unicode do Font Awesome.</br>(Exemplo: f15c)</small>`, // 101
    `Ícone do segundo separador`, // 102
    `<small>Introduza um valor Unicode do Font Awesome.</br>(Exemplo: f164)</small>`, // 103
    `Ícone do terceiro separador`, // 104
    `Ícone do quarto separador`, // 105
    `Cor de fundo dos separadores`, // 106
    `Cor do texto dos separadores`, // 107
    `Cor de fundo do separador ativo 1`, // 108
    `<small>Utilize cores diferentes para 1 e 2 para criar um gradiente.</small>`, // 109
    `Cor de fundo do separador ativo 2`, // 110
    `Selecionar um tipo de letra Google`, // 111
    `apenas font-weight: 400`, // 112
    `Pode alterar a cor de fundo e outras definições do CTA fixo no rodapé.</br>Consulte <a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-fix-cta/">esta página</a> para obter detalhes da configuração`, // 113
    `Cor de fundo do CTA fixo no rodapé`, // 114
    `Defina um valor entre 0 e 1 em incrementos de 0,01.<br>Quanto mais próximo estiver de 0, mais transparente será.`, // 115
    `Opacidade do fundo do CTA fixo no rodapé`, // 116
    `Cor do microtexto do CTA fixo no rodapé`, // 117
    `Botão vermelho`, // 118
    `Botão azul`, // 119
    `Botão verde`, // 120
    `Pode alterar o número de itens apresentados no carrossel e outras definições. Consulte <a target="_blank" href="https://go-blogs.com/cocoon/carousel-setting/">esta página</a> para obter detalhes.`, // 121
    `É possível definir entre 2 e 6 itens`, // 122
    `Número de itens para larguras de ecrã iguais ou superiores a 1241 px`, // 123
    `Se não estiver selecionado, o carrossel avança o número de itens apresentados`, // 124
    `Avançar um item de cada vez`, // 125
    `Número de itens para larguras de ecrã entre 1024 px e 1240 px`, // 126
    `Número de itens para larguras de ecrã entre 835 px e 1023 px`, // 127
    `É possível definir entre 2 e 4 itens`, // 128
    `Número de itens para larguras de ecrã entre 481 px e 834 px`, // 129
    `É possível definir entre 1 e 2 itens`, // 130
    `Número de itens para larguras de ecrã iguais ou inferiores a 480 px`, // 131
    `Altere as definições e clique em Publicar para aplicar o estilo.`, // 132
    `Barra lateral esquerda fixa`, // 133
    `Uma barra lateral fixada à extremidade esquerda quando visualizada num computador.`, // 134
    `Sombra`, // 135
    `Painel`, // 136
    `Tabela comparativa (lista de ícones)`, // 137
    `Acordeão (caixa de alternância)`, // 138
    `Linha de corte`, // 139
    `Centrado`, // 140
    `Horizontal`, // 141
    `Ligação`, // 142
    `Largo`, // 143
    `Perguntas frequentes`, // 144
    `Sem ícone`, // 145
    `Bloco reutilizável`, // 146
    `Lista de blocos reutilizáveis`, // 147
    `Skin “SILK” do Cocoon`, // 148
    `As definições foram adicionadas.`, // 149
    `Selecione um ficheiro JSON.`, // 150
    `Definições das opções`, // 151
    `Adicione as definições das opções do skin. Isto altera as definições do Cocoon, por isso crie primeiro um ficheiro de cópia de segurança.`, // 152
    `Carregar ficheiro JSON:`, // 153
    `Adicionar definições`, // 154
    `Selecione um ficheiro JSON com as definições das opções do skin e clique em Adicionar definições. À exceção do nome do ficheiro, o método de criação do ficheiro JSON segue o controlo do skin.`, // 155
    `Código copiado`, // 156
    `Comparação 1`, // 157
    `Comparação 2`, // 158
    `Lista`, // 159
    `Tabela comparativa`, // 160
    `Crie uma tabela comparativa combinando blocos de grupo e de lista de ícones.`, // 161
    `Coluna 1`, // 162
    `Coluna 2`, // 163
    `Colunas dentro da largura total`, // 164
    `Um padrão com um bloco de colunas dentro de um bloco de grupo configurado para largura total.`, // 165
    `Ligações relacionadas`, // 166
    `Ligação de texto`, // 167
    `Caixa de lista de ligações`, // 168
    `Um padrão com uma lista de ligações dentro de um bloco de caixa de título de separador.`, // 169
    `Título introduzido pelo skin`, // 170
    `Esta é uma mensagem da área de destaque introduzida pelo skin.`, // 171
    `Legenda do botão do skin`, // 172
    `Ajuste do raio dos cantos`, // 174
    `[Skin] Definições de tipos de letra japoneses`, // 175
    `Definições do tipo de letra do logótipo`, // 176
    `Defina o tipo de letra do logótipo utilizado no texto do logótipo, nos títulos dos artigos e em textos semelhantes. Selecione Nenhum para herdar Definições do Cocoon > Geral > Tipo de letra do site.`, // 177
    `Klee (predefinido)`, // 178
    `Zen Kurenaido`, // 179
    `Zen Kaku Gothic`, // 180
    `Zen Maru Gothic`, // 181
    `Kiwi Maru`, // 182
    `Kaisei Decol`, // 183
    `[Skin] Definições do padrão de fundo`, // 184
    `Definições do padrão de fundo`, // 185
    `Selecione um padrão de fundo. Selecione Nenhum para remover o padrão e utilizar apenas a cor de fundo.`, // 186
    `Grelha (predefinida)`, // 187
    `Linhas`, // 188
    `Pontos`, // 189
    `[Skin] Definições das marcas de ênfase do texto do logótipo`, // 190
    `Estilo das marcas de ênfase do texto do logótipo`, // 191
    `Selecione as marcas de ênfase do texto do logótipo. Selecione Nenhuma para as remover.`, // 192
    `Ponto (predefinido)`, // 193
    `Ponto vazado`, // 194
    `Círculo vazado`, // 195
    `Círculo duplo`, // 196
    `Círculo duplo vazado`, // 197
    `Triângulo vazado`, // 198
    `Ponto de sésamo`, // 199
    `Ponto de sésamo vazado`, // 200
    `Posição das marcas de ênfase do texto do logótipo`, // 201
    `Selecione a posição das marcas de ênfase do texto do logótipo.`, // 202
    `Em baixo (predefinido)`, // 203
    `Em cima`, // 204
    `[Skin] Definições da navegação global`, // 205
    `Remover o estilo de cartão ao definir as cores da navegação global`, // 206
    `Quando selecionada, a definição das cores da navegação global (fundo e texto) altera o menu de cartões no telemóvel para a apresentação padrão, mantendo o texto visível. Desmarque a opção para manter o estilo de cartão.`, // 207
    `Apresenta conteúdo apenas a utilizadores com sessão iniciada`, // 208
    `Título da cronologia`, // 209
    `Cabeçalho do acordeão`, // 210
    `Este conteúdo é apresentado apenas durante o período da campanha.`, // 211
    `Selecionar`, // 212
    `Geral`, // 213
    `Definições dos shortcodes`, // 214
    `Introduzir um termo de pesquisa`, // 215
    `Pesquisar URL`, // 216
    `<div class="blank-box bb-red">Os comentários ofensivos serão removidos sem aviso prévio.</div>`, // 217
    `Definições do skin NAGI`, // 219
    `<a href="http://whatismyscreenresolution.net/multi-screen-test" target="_blank" rel="noopener">WhatIsMyScreenResolution</a> Mostrar a ligação de verificação.`, // 220
  ],
  zh_CN: [
    `将项目下移`, // 001
    `示例：`, // 002
    `未找到页面`, // 003
    `示例文章标题`, // 004
    `这是用于预览主题设置效果的示例正文。您可以预览字号、行距、配色等设置的应用效果。`, // 005
    `示例标题 2`, // 006
    `这里会显示标题 2 的样式（背景色、边框和主色）。您也可以同时查看正文段落的样式。`, // 007
    `示例标题 3`, // 008
    `这是用于查看标题 3 样式的示例文本。`, // 009
    `这是引用区块的示例。您可以查看引用样式。`, // 010
    `示例列表项目 1`, // 011
    `示例列表项目 2`, // 012
    `示例列表项目 3`, // 013
    `为了查看链接颜色，请参阅这个`, // 014
    `示例链接`, // 015
    `。`, // 016
    `显示 Threads 关注按钮`, // 017
    `显示 Reddit 关注按钮`, // 018
    `关注 Reddit`, // 019
    `跳至内容`, // 020
    `推荐文章轮播`, // 021
    `分享到 Reddit`, // 022
    `编辑链接`, // 023
    `添加链接`, // 024
    `链接 URL`, // 025
    `设置后，整个分组区块都会成为链接。`, // 026
    `取消链接`, // 027
    `※此设置优先于 WordPress 的标准下边距设置。`, // 028
    `正在生成预览…`, // 029
    `※ 搜索结果中的价格仅供参考。实际价格请以销售页面为准。`, // 030
    `显示期间设置`, // 031
    `开始日期和时间 (from)`, // 032
    `选择日期和时间`, // 033
    `结束日期和时间 (to)`, // 034
    `当前处于显示期间内`, // 035
    `当前处于显示期间外`, // 036
    `此区块仅在指定期间内显示内容。您可以设置开始和结束日期及时间。`, // 037
    `向未登录用户显示的消息`, // 038
    `此消息会向未登录的用户显示。`, // 039
    `请输入要插入的 HTML 代码。代码将以 [html]...[/html] 短代码的形式嵌入。`, // 040
    `取消`, // 041
    `请输入要显示在所选文字上方的读音。留空时将直接使用所选文字。`, // 042
    `博客卡片（嵌入）`, // 043
    `尚未设置 URL`, // 044
    `所有用户`, // 045
    `未更新`, // 046
    `时`, // 047
    `分`, // 048
    `不显示通知`, // 049
    `所选图片`, // 050
    `颜色示例网站`, // 051
    `注意主栏顶部的广告`, // 052
    `注意侧边栏顶部的广告`, // 053
    `未找到符合条件的文章。`, // 054
    `正在加载...`, // 055
    `发生错误`, // 056
    `发生通信错误`, // 057
    `没有符合条件的项目`, // 058
    `最近 %1$d 天的页面浏览量与之前 %1$d 天的页面浏览量比较`, // 059
    `排名第 %d 的项目因未填写名称或说明而没有另外保存。`, // 060
    `如需使用，请在文章内容的任意位置输入 %s。仅可用于文章和页面。`, // 061
    `演示图片`, // 062
    `SEO 设置中的元描述`, // 063
    `新闻`, // 064
    `标签（没有标签时显示分类）`, // 065
    `此邮箱仅用于发送邮件，无法接收回复。`, // 066
    `页脚固定 CTA`, // 067
    `※关于此功能：`, // 068
    `查看详细说明`, // 069
    `联盟标签短代码（未填写时不会显示页脚固定 CTA）`, // 070
    `请务必输入联盟短代码。`, // 071
    `联盟标签必须包含在 &lt;a&gt; 标签中。其他内容不会显示为按钮。`, // 072
    `请在此输入联盟标签短代码`, // 073
    `请在此输入微文案`, // 074
    `CTA 按钮颜色`, // 075
    `红色系`, // 076
    `蓝色系`, // 077
    `绿色系`, // 078
    `微文案和按钮的布局`, // 079
    `纵向排列`, // 080
    `横向排列`, // 081
    `有关设置的详细信息，请参阅<a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-zentai/">此页面</a>`, // 082
    `添加阴影`, // 083
    `无阴影`, // 084
    `轻微阴影`, // 085
    `明显阴影（建议用于深色背景）`, // 086
    `添加圆角`, // 087
    `无圆角`, // 088
    `轻微圆角`, // 089
    `明显圆角`, // 090
    `浅灰色区域颜色`, // 091
    `<small>这是目录、分享按钮、关注按钮等元素的背景色。虽然称为浅灰色区域，但可以使用任何颜色。<br>※透明度为 35%。</small>`, // 092
    `也将此背景色应用到侧边栏`, // 093
    `将顶部社交分享按钮固定在屏幕左侧`, // 094
    `<small>仅在电脑端固定。需要在 Cocoon 设置中勾选“显示主栏顶部的社交分享按钮”。</small>`, // 095
    `以折叠面板形式展开和收起分类小工具的子分类`, // 096
    `您可以更改首页标签列表的样式，并为每个标签设置图标。</br>\n        详情请参阅<a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-tab/">此页面</a>`, // 097
    `为首页标签列表使用原创样式`, // 098
    `<small>必须设置图标。</small>`, // 099
    `第一个标签的图标`, // 100
    `<small>请输入 Font Awesome 的 Unicode 值。</br>（示例：f15c）</small>`, // 101
    `第二个标签的图标`, // 102
    `<small>请输入 Font Awesome 的 Unicode 值。</br>（示例：f164）</small>`, // 103
    `第三个标签的图标`, // 104
    `第四个标签的图标`, // 105
    `标签背景色`, // 106
    `标签文字颜色`, // 107
    `活动标签背景色 1`, // 108
    `<small>为 1 和 2 使用不同的颜色可生成渐变效果。</small>`, // 109
    `活动标签背景色 2`, // 110
    `选择 Google 字体`, // 111
    `仅限 font-weight: 400`, // 112
    `您可以更改页脚固定 CTA 的背景色等设置。</br>有关设置的详细信息，请参阅<a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-fix-cta/">此页面</a>`, // 113
    `页脚固定 CTA 背景色`, // 114
    `可在 0 到 1 之间以 0.01 为单位设置。<br>数值越接近 0 越透明。`, // 115
    `页脚固定 CTA 背景透明度`, // 116
    `页脚固定 CTA 微文案颜色`, // 117
    `红色按钮`, // 118
    `蓝色按钮`, // 119
    `绿色按钮`, // 120
    `您可以更改轮播显示数量等设置。详情请参阅<a target="_blank" href="https://go-blogs.com/cocoon/carousel-setting/">此页面</a>。`, // 121
    `可设置 2～6 个`, // 122
    `屏幕宽度为 1241px 以上时的显示数量`, // 123
    `未勾选时，将按显示数量移动`, // 124
    `每次移动一个`, // 125
    `屏幕宽度为 1024px～1240px 时的显示数量`, // 126
    `屏幕宽度为 835px～1023px 时的显示数量`, // 127
    `可设置 2～4 个`, // 128
    `屏幕宽度为 481px～834px 时的显示数量`, // 129
    `可设置 1～2 个`, // 130
    `屏幕宽度为 480px 以下时的显示数量`, // 131
    `更改各项设置并点击“发布”按钮即可应用设计。`, // 132
    `左侧固定侧边栏`, // 133
    `在电脑端浏览时固定显示在左侧边缘的侧边栏。`, // 134
    `阴影`, // 135
    `面板`, // 136
    `比较表（图标列表）`, // 137
    `折叠面板（切换框）`, // 138
    `剪切线`, // 139
    `居中`, // 140
    `水平`, // 141
    `链接`, // 142
    `宽幅`, // 143
    `常见问题`, // 144
    `无图标`, // 145
    `可复用区块`, // 146
    `可复用区块列表`, // 147
    `Cocoon 皮肤“SILK”`, // 148
    `已添加设置。`, // 149
    `请选择 JSON 文件。`, // 150
    `选项设置`, // 151
    `添加皮肤的选项设置。此操作会更改 Cocoon 设置，请事先创建备份文件。`, // 152
    `上传 JSON 文件：`, // 153
    `添加设置`, // 154
    `请选择包含皮肤选项设置的 JSON 文件，然后点击“添加设置”按钮。除文件名外，JSON 文件的创建方法遵循皮肤控制。`, // 155
    `代码已复制`, // 156
    `比较 1`, // 157
    `比较 2`, // 158
    `列表`, // 159
    `比较表`, // 160
    `可以将分组区块和图标列表区块组合成比较表。`, // 161
    `列 1`, // 162
    `列 2`, // 163
    `全宽区域内的分栏`, // 164
    `这是在设置为全宽的分组区块内放置分栏区块的样板。`, // 165
    `相关链接`, // 166
    `文字链接`, // 167
    `链接列表框`, // 168
    `这是在标签标题框区块内放置链接列表的样板。`, // 169
    `从皮肤输入的标题`, // 170
    `这是从皮肤输入的展示区域消息。`, // 171
    `皮肤按钮说明文字`, // 172
    `调整圆角`, // 174
    `【皮肤】日文字体设置`, // 175
    `徽标字体设置`, // 176
    `设置用于徽标文字、文章标题等内容的徽标字体。选择“无”时，将继承 Cocoon 设置 > 全局设置 > 站点字体。`, // 177
    `Klee（默认）`, // 178
    `Zen Kurenaido`, // 179
    `Zen Kaku Gothic`, // 180
    `Zen Maru Gothic`, // 181
    `Kiwi Maru`, // 182
    `Kaisei Decol`, // 183
    `【皮肤】背景图案设置`, // 184
    `背景图案设置`, // 185
    `请选择背景图案。选择“无”时，将删除背景图案并仅使用背景色。`, // 186
    `网格（默认）`, // 187
    `横线`, // 188
    `圆点`, // 189
    `【皮肤】徽标文字着重号设置`, // 190
    `徽标文字着重号样式`, // 191
    `请选择徽标文字的着重号。选择“无”时，将删除着重号。`, // 192
    `点（默认）`, // 193
    `空心点`, // 194
    `空心圆`, // 195
    `双圆`, // 196
    `空心双圆`, // 197
    `空心三角形`, // 198
    `芝麻点`, // 199
    `空心芝麻点`, // 200
    `徽标文字着重号位置`, // 201
    `请选择徽标文字着重号的位置。`, // 202
    `下方（默认）`, // 203
    `上方`, // 204
    `【皮肤】全局导航设置`, // 205
    `设置全局导航颜色后取消卡片样式`, // 206
    `勾选后，在设置全局导航颜色（背景色和文字颜色）时，会将手机端的卡片式菜单切换为普通显示，以防文字不可见。如要保留卡片式样式，请取消勾选。`, // 207
    `仅向已登录用户显示内容`, // 208
    `时间线标题`, // 209
    `折叠面板标题`, // 210
    `这是仅在活动期间显示的内容。`, // 211
    `选择`, // 212
    `通用`, // 213
    `短代码设置`, // 214
    `输入搜索词`, // 215
    `URL 搜索`, // 216
    `<div class="blank-box bb-red">诽谤和辱骂内容将被删除，恕不另行通知。</div>`, // 217
    `NAGI 皮肤设置`, // 219
    `<a href="http://whatismyscreenresolution.net/multi-screen-test" target="_blank" rel="noopener">WhatIsMyScreenResolution</a> 显示检查用链接。`, // 220
  ],
  zh_TW: [
    `將項目下移`, // 001
    `範例：`, // 002
    `找不到頁面`, // 003
    `範例文章標題`, // 004
    `這是用來預覽佈景主題設定效果的範例內文。您可以預覽字型大小、行距、配色等設定的套用效果。`, // 005
    `範例標題 2`, // 006
    `這裡會顯示標題 2 的樣式（背景色、邊框和主要色彩）。您也可以同時查看內文段落的樣式。`, // 007
    `範例標題 3`, // 008
    `這是用來查看標題 3 樣式的範例文字。`, // 009
    `這是引文區塊的範例。您可以查看引文樣式。`, // 010
    `範例清單項目 1`, // 011
    `範例清單項目 2`, // 012
    `範例清單項目 3`, // 013
    `若要查看連結色彩，請參閱這個`, // 014
    `範例連結`, // 015
    `。`, // 016
    `顯示 Threads 追蹤按鈕`, // 017
    `顯示 Reddit 追蹤按鈕`, // 018
    `追蹤 Reddit`, // 019
    `跳至內容`, // 020
    `推薦文章輪播`, // 021
    `分享到 Reddit`, // 022
    `編輯連結`, // 023
    `新增連結`, // 024
    `連結 URL`, // 025
    `設定後，整個群組區塊都會成為連結。`, // 026
    `移除連結`, // 027
    `※此設定優先於 WordPress 的標準下邊界設定。`, // 028
    `正在產生預覽…`, // 029
    `※ 搜尋結果中的價格僅供參考。實際價格請以銷售頁面為準。`, // 030
    `顯示期間設定`, // 031
    `開始日期和時間 (from)`, // 032
    `選取日期和時間`, // 033
    `結束日期和時間 (to)`, // 034
    `目前在顯示期間內`, // 035
    `目前在顯示期間外`, // 036
    `此區塊僅在指定期間內顯示內容。您可以設定開始和結束日期及時間。`, // 037
    `向未登入使用者顯示的訊息`, // 038
    `此訊息會向未登入的使用者顯示。`, // 039
    `請輸入要插入的 HTML 程式碼。程式碼會以 [html]...[/html] 短代碼的形式嵌入。`, // 040
    `取消`, // 041
    `請輸入要顯示在所選文字上方的讀音。留空時會直接使用所選文字。`, // 042
    `部落格卡片（嵌入）`, // 043
    `尚未設定 URL`, // 044
    `所有使用者`, // 045
    `未更新`, // 046
    `時`, // 047
    `分`, // 048
    `不顯示通知`, // 049
    `所選圖片`, // 050
    `色彩範例網站`, // 051
    `注意主要欄頂端的廣告`, // 052
    `注意側邊欄頂端的廣告`, // 053
    `找不到符合條件的文章。`, // 054
    `正在載入...`, // 055
    `發生錯誤`, // 056
    `發生通訊錯誤`, // 057
    `沒有符合條件的項目`, // 058
    `最近 %1$d 天的頁面瀏覽量與之前 %1$d 天的頁面瀏覽量比較`, // 059
    `排名第 %d 的項目因未填寫名稱或說明而沒有另外儲存。`, // 060
    `如需使用，請在文章內容的任意位置輸入 %s。僅可用於文章和頁面。`, // 061
    `示範圖片`, // 062
    `SEO 設定中的中繼描述`, // 063
    `新聞`, // 064
    `標籤（沒有標籤時顯示分類）`, // 065
    `此電子郵件地址僅用於傳送郵件，無法接收回覆。`, // 066
    `頁尾固定 CTA`, // 067
    `※關於此功能：`, // 068
    `查看詳細說明`, // 069
    `聯盟標籤短代碼（未填寫時不會顯示頁尾固定 CTA）`, // 070
    `請務必輸入聯盟短代碼。`, // 071
    `聯盟標籤必須包含在 &lt;a&gt; 標籤中。其他內容不會顯示為按鈕。`, // 072
    `請在此輸入聯盟標籤短代碼`, // 073
    `請在此輸入微文案`, // 074
    `CTA 按鈕色彩`, // 075
    `紅色系`, // 076
    `藍色系`, // 077
    `綠色系`, // 078
    `微文案和按鈕的版面配置`, // 079
    `垂直排列`, // 080
    `水平排列`, // 081
    `如需設定的詳細資訊，請參閱<a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-zentai/">此頁面</a>`, // 082
    `新增陰影`, // 083
    `無陰影`, // 084
    `輕微陰影`, // 085
    `明顯陰影（建議用於深色背景）`, // 086
    `新增圓角`, // 087
    `無圓角`, // 088
    `輕微圓角`, // 089
    `明顯圓角`, // 090
    `淺灰色區域色彩`, // 091
    `<small>這是目錄、分享按鈕、追蹤按鈕等元素的背景色。雖然稱為淺灰色區域，但可以使用任何色彩。<br>※不透明度為 35%。</small>`, // 092
    `也將此背景色套用到側邊欄`, // 093
    `將頂端社交分享按鈕固定在畫面左側`, // 094
    `<small>僅在電腦版固定。需要在 Cocoon 設定中勾選「顯示主要欄頂端的社交分享按鈕」。</small>`, // 095
    `以折疊面板形式展開和收合分類小工具的子分類`, // 096
    `您可以變更首頁分頁清單的樣式，並為每個分頁設定圖示。</br>\n        詳情請參閱<a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-tab/">此頁面</a>`, // 097
    `為首頁分頁清單使用原創樣式`, // 098
    `<small>必須設定圖示。</small>`, // 099
    `第一個分頁的圖示`, // 100
    `<small>請輸入 Font Awesome 的 Unicode 值。</br>（範例：f15c）</small>`, // 101
    `第二個分頁的圖示`, // 102
    `<small>請輸入 Font Awesome 的 Unicode 值。</br>（範例：f164）</small>`, // 103
    `第三個分頁的圖示`, // 104
    `第四個分頁的圖示`, // 105
    `分頁背景色`, // 106
    `分頁文字色彩`, // 107
    `作用中分頁背景色 1`, // 108
    `<small>為 1 和 2 使用不同的色彩可產生漸層效果。</small>`, // 109
    `作用中分頁背景色 2`, // 110
    `選取 Google 字型`, // 111
    `僅限 font-weight: 400`, // 112
    `您可以變更頁尾固定 CTA 的背景色等設定。</br>如需設定的詳細資訊，請參閱<a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-fix-cta/">此頁面</a>`, // 113
    `頁尾固定 CTA 背景色`, // 114
    `可在 0 到 1 之間以 0.01 為單位設定。<br>數值越接近 0 越透明。`, // 115
    `頁尾固定 CTA 背景不透明度`, // 116
    `頁尾固定 CTA 微文案色彩`, // 117
    `紅色按鈕`, // 118
    `藍色按鈕`, // 119
    `綠色按鈕`, // 120
    `您可以變更輪播顯示數量等設定。詳情請參閱<a target="_blank" href="https://go-blogs.com/cocoon/carousel-setting/">此頁面</a>。`, // 121
    `可設定 2～6 個`, // 122
    `畫面寬度為 1241px 以上時的顯示數量`, // 123
    `未勾選時，會依顯示數量移動`, // 124
    `每次移動一個`, // 125
    `畫面寬度為 1024px～1240px 時的顯示數量`, // 126
    `畫面寬度為 835px～1023px 時的顯示數量`, // 127
    `可設定 2～4 個`, // 128
    `畫面寬度為 481px～834px 時的顯示數量`, // 129
    `可設定 1～2 個`, // 130
    `畫面寬度為 480px 以下時的顯示數量`, // 131
    `變更各項設定並按一下「發佈」按鈕即可套用設計。`, // 132
    `左側固定側邊欄`, // 133
    `在電腦版瀏覽時固定顯示在左側邊緣的側邊欄。`, // 134
    `陰影`, // 135
    `面板`, // 136
    `比較表（圖示清單）`, // 137
    `折疊面板（切換方塊）`, // 138
    `剪裁線`, // 139
    `置中`, // 140
    `水平`, // 141
    `連結`, // 142
    `寬幅`, // 143
    `常見問題`, // 144
    `無圖示`, // 145
    `可重複使用區塊`, // 146
    `可重複使用區塊清單`, // 147
    `Cocoon 外觀「SILK」`, // 148
    `已新增設定。`, // 149
    `請選取 JSON 檔案。`, // 150
    `選項設定`, // 151
    `新增外觀的選項設定。此操作會變更 Cocoon 設定，請事先建立備份檔案。`, // 152
    `上傳 JSON 檔案：`, // 153
    `新增設定`, // 154
    `請選取包含外觀選項設定的 JSON 檔案，然後按一下「新增設定」按鈕。除了檔名以外，JSON 檔案的建立方法遵循外觀控制。`, // 155
    `已複製程式碼`, // 156
    `比較 1`, // 157
    `比較 2`, // 158
    `清單`, // 159
    `比較表`, // 160
    `可以將群組區塊和圖示清單區塊組合成比較表。`, // 161
    `欄 1`, // 162
    `欄 2`, // 163
    `全寬區域內的欄`, // 164
    `這是在設定為全寬的群組區塊內放置多欄區塊的樣板。`, // 165
    `相關連結`, // 166
    `文字連結`, // 167
    `連結清單方塊`, // 168
    `這是在分頁標題方塊區塊內放置連結清單的樣板。`, // 169
    `從外觀輸入的標題`, // 170
    `這是從外觀輸入的訴求區域訊息。`, // 171
    `外觀按鈕說明文字`, // 172
    `調整圓角`, // 174
    `【外觀】日文字型設定`, // 175
    `標誌字型設定`, // 176
    `設定用於標誌文字、文章標題等內容的標誌字型。選取「無」時，會繼承 Cocoon 設定 > 全域設定 > 網站字型。`, // 177
    `Klee（預設）`, // 178
    `Zen Kurenaido`, // 179
    `Zen Kaku Gothic`, // 180
    `Zen Maru Gothic`, // 181
    `Kiwi Maru`, // 182
    `Kaisei Decol`, // 183
    `【外觀】背景圖樣設定`, // 184
    `背景圖樣設定`, // 185
    `請選取背景圖樣。選取「無」時，會移除背景圖樣並僅使用背景色。`, // 186
    `格線（預設）`, // 187
    `橫線`, // 188
    `圓點`, // 189
    `【外觀】標誌文字著重號設定`, // 190
    `標誌文字著重號樣式`, // 191
    `請選取標誌文字的著重號。選取「無」時，會移除著重號。`, // 192
    `點（預設）`, // 193
    `空心點`, // 194
    `空心圓`, // 195
    `雙圓`, // 196
    `空心雙圓`, // 197
    `空心三角形`, // 198
    `芝麻點`, // 199
    `空心芝麻點`, // 200
    `標誌文字著重號位置`, // 201
    `請選取標誌文字著重號的位置。`, // 202
    `下方（預設）`, // 203
    `上方`, // 204
    `【外觀】全域導覽設定`, // 205
    `設定全域導覽色彩後移除卡片樣式`, // 206
    `勾選後，在設定全域導覽色彩（背景色和文字色彩）時，會將手機版的卡片式選單切換為一般顯示，以防文字無法辨識。如要保留卡片式樣式，請取消勾選。`, // 207
    `僅向已登入使用者顯示內容`, // 208
    `時間軸標題`, // 209
    `折疊面板標題`, // 210
    `這是僅在活動期間顯示的內容。`, // 211
    `選取`, // 212
    `一般`, // 213
    `短代碼設定`, // 214
    `輸入搜尋字詞`, // 215
    `URL 搜尋`, // 216
    `<div class="blank-box bb-red">誹謗及辱罵內容將不另行通知直接刪除。</div>`, // 217
    `NAGI 外觀設定`, // 219
    `<a href="http://whatismyscreenresolution.net/multi-screen-test" target="_blank" rel="noopener">WhatIsMyScreenResolution</a> 顯示檢查用連結。`, // 220
  ],
};

// 翻訳件数と原文件数のずれを防ぐ辞書生成処理
const getTranslations = ( locale ) => {
  const values = translations[ locale ];

  if ( ! values || values.length !== keys.length ) {
    throw new Error( `翻訳件数が一致しません: ${ locale }` );
  }

  return Object.fromEntries( keys.map( ( key, index ) => [ key, values[ index ] ] ) );
};

getTranslations.keys = keys;
getTranslations.translations = translations;

module.exports = getTranslations;
