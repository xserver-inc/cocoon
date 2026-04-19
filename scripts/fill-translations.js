#!/usr/bin/env node
/**
 * 未翻訳の msgstr を各言語の翻訳で埋めるスクリプト
 * 使い方: node scripts/fill-translations.js
 */
const fs = require( 'fs' );
const path = require( 'path' );

const LANG_DIR = path.join( __dirname, '..', 'languages' );

// 翻訳データ: { locale: { msgid: translation } }
// msgctxt 付きは "msgid\x00msgctxt" をキーにする
const translations = {
  en_US: {
    '2日ごと': 'Every 2 days',
    '3日ごと': 'Every 3 days',
    '5日ごと': 'Every 5 days',
    '楽天側に登録されている説明文を表示します（情報がない場合は表示されません）。descオプションが設定されている場合は、オプション値が優先して表示されます。':
      'Displays the description registered on Rakuten (if no information is available, it will not be displayed). If the desc option is set, the option value will be displayed with priority.',
    '1日ごと': 'Every day',
    '3日ごと（推奨）': 'Every 3 days (recommended)',
    '1週間ごと': 'Every week',
    '更新間隔が短いほどAPIのリクエスト数が増えます。Amazon・楽天それぞれのAPIリクエスト制限にご注意ください。':
      'A shorter update interval increases the number of API requests. Please be mindful of the Amazon and Rakuten API request limits.',
    '5投稿': '5 posts',
    '※共有ホスティングをご利用の場合は30以下を推奨します。50以上に設定するとサーバー側のタイムアウト制限により処理が中断される場合があります。':
      '* If you are using shared hosting, we recommend a value of 30 or less. Setting it to 50 or more may cause processing to be interrupted due to server-side timeout limits.',
    '商品を取得できませんでした。Creators APIは正常に応答しましたが、商品情報が含まれていません。':
      'Could not retrieve product. The Creators API responded successfully, but no product information was included.',
    '考えられる原因：': 'Possible causes:',
    '存在しないASINを指定している': 'An invalid ASIN has been specified',
    'ASINが正しくない（半角英数字10文字）': 'The ASIN is incorrect (10 alphanumeric characters)',
    '商品が販売終了・取り下げされている': 'The product has been discontinued or removed',
    'トラッキングIDが正しくない': 'The tracking ID is incorrect',
    'Creators APIの認証情報が正しくない': 'The Creators API credentials are incorrect',
    '（%s時点）': '(as of %s)',
    '「新着記事」表示': '"New Articles" display',
    'NEWマーク（%d～%d日、0はオフ）': 'NEW mark (%d\u2013%d days, 0 = off)',
    'ダークモード': 'Dark Mode',
    'フォントサイズ（%d～%dpx）': 'Font Size (%d\u2013%dpx)',
    'Scrollボタンカラー': 'Scroll Button Color',
    '不透明度（opacity値%d～%d%%）': 'Opacity (opacity value %d\u2013%d%%)',
    // block keyword / title / description（msgctxt付き）
    'campaign\x00block keyword': 'campaign',
    'キャンペーン\x00block keyword': 'campaign',
    '期間\x00block keyword': 'period',
    'ログインユーザー限定\x00block title': 'Members Only',
    'ログインしているユーザーにのみコンテンツを表示します\x00block description':
      'Displays content only to logged-in users',
    'login\x00block keyword': 'login',
    'user\x00block keyword': 'user',
    'only\x00block keyword': 'only',
    'member\x00block keyword': 'member',
    '会員\x00block keyword': 'member',
    '楽天商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。\x00block description':
      'Search and select Rakuten products in the editor, then insert a static product link.',
    // js/blogcard-editor.js のハードコード文字列
    'ブログカード（埋め込み）': 'Blog Card (Embed)',
    'URLが設定されていません': 'URL is not set',
  },

  de_DE: {
    '2日ごと': 'Alle 2 Tage',
    '3日ごと': 'Alle 3 Tage',
    '5日ごと': 'Alle 5 Tage',
    '楽天側に登録されている説明文を表示します（情報がない場合は表示されません）。descオプションが設定されている場合は、オプション値が優先して表示されます。':
      'Zeigt die bei Rakuten registrierte Beschreibung an (wird nicht angezeigt, wenn keine Informationen vorhanden sind). Wenn die desc-Option gesetzt ist, wird der Optionswert bevorzugt angezeigt.',
    '1日ごと': 'Täglich',
    '3日ごと（推奨）': 'Alle 3 Tage (empfohlen)',
    '1週間ごと': 'Wöchentlich',
    '更新間隔が短いほどAPIのリクエスト数が増えます。Amazon・楽天それぞれのAPIリクエスト制限にご注意ください。':
      'Je kürzer das Aktualisierungsintervall, desto mehr API-Anfragen werden gestellt. Bitte beachten Sie die API-Anfragebeschränkungen von Amazon und Rakuten.',
    '5投稿': '5 Beiträge',
    '※共有ホスティングをご利用の場合は30以下を推奨します。50以上に設定するとサーバー側のタイムアウト制限により処理が中断される場合があります。':
      '* Bei Shared Hosting empfehlen wir einen Wert von maximal 30. Bei einem Wert von 50 oder mehr kann die Verarbeitung aufgrund serverseitiger Zeitlimits unterbrochen werden.',
    '商品を取得できませんでした。Creators APIは正常に応答しましたが、商品情報が含まれていません。':
      'Produkt konnte nicht abgerufen werden. Die Creators API hat erfolgreich geantwortet, aber keine Produktinformationen wurden zurückgegeben.',
    '考えられる原因：': 'Mögliche Ursachen:',
    '存在しないASINを指定している': 'Eine ungültige ASIN wurde angegeben',
    'ASINが正しくない（半角英数字10文字）': 'Die ASIN ist falsch (10 alphanumerische Zeichen)',
    '商品が販売終了・取り下げされている': 'Das Produkt wurde eingestellt oder zurückgezogen',
    'トラッキングIDが正しくない': 'Die Tracking-ID ist falsch',
    'Creators APIの認証情報が正しくない': 'Die Credentials der Creators API sind falsch',
    '（%s時点）': '(Stand: %s)',
    '「新着記事」表示': 'Anzeige "Neue Artikel"',
    'NEWマーク（%d～%d日、0はオフ）': 'NEU-Markierung (%d\u2013%d Tage, 0 = aus)',
    'ダークモード': 'Dunkelmodus',
    'フォントサイズ（%d～%dpx）': 'Schriftgröße (%d\u2013%dpx)',
    'Scrollボタンカラー': 'Scroll-Schaltflächenfarbe',
    '不透明度（opacity値%d～%d%%）': 'Deckkraft (opacity-Wert %d\u2013%d%%)',
    'campaign\x00block keyword': 'campaign',
    'キャンペーン\x00block keyword': 'Kampagne',
    '期間\x00block keyword': 'Zeitraum',
    'ログインユーザー限定\x00block title': 'Nur für eingeloggte Benutzer',
    'ログインしているユーザーにのみコンテンツを表示します\x00block description':
      'Zeigt Inhalte nur für eingeloggte Benutzer an',
    'login\x00block keyword': 'login',
    'user\x00block keyword': 'user',
    'only\x00block keyword': 'only',
    'member\x00block keyword': 'member',
    '会員\x00block keyword': 'Mitglied',
    '楽天商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。\x00block description':
      'Suchen und wählen Sie Rakuten-Produkte im Editor und fügen Sie einen statischen Produktlink ein.',
    'ブログカード（埋め込み）': 'Blogkarte (Eingebettet)',
    'URLが設定されていません': 'URL ist nicht festgelegt',
  },

  es_ES: {
    '2日ごと': 'Cada 2 días',
    '3日ごと': 'Cada 3 días',
    '5日ごと': 'Cada 5 días',
    '楽天側に登録されている説明文を表示します（情報がない場合は表示されません）。descオプションが設定されている場合は、オプション値が優先して表示されます。':
      'Muestra la descripción registrada en Rakuten (no se mostrará si no hay información). Si se ha configurado la opción desc, el valor de la opción se mostrará con prioridad.',
    '1日ごと': 'Cada día',
    '3日ごと（推奨）': 'Cada 3 días (recomendado)',
    '1週間ごと': 'Cada semana',
    '更新間隔が短いほどAPIのリクエスト数が増えます。Amazon・楽天それぞれのAPIリクエスト制限にご注意ください。':
      'Cuanto más corto sea el intervalo de actualización, mayor será el número de solicitudes a la API. Tenga en cuenta los límites de solicitudes de las API de Amazon y Rakuten.',
    '5投稿': '5 entradas',
    '※共有ホスティングをご利用の場合は30以下を推奨します。50以上に設定するとサーバー側のタイムアウト制限により処理が中断される場合があります。':
      '* Si utiliza hosting compartido, recomendamos un valor de 30 o menos. Establecer 50 o más puede interrumpir el procesamiento debido a los límites de tiempo del servidor.',
    '商品を取得できませんでした。Creators APIは正常に応答しましたが、商品情報が含まれていません。':
      'No se pudo obtener el producto. La API de Creators respondió correctamente, pero no se incluyó información del producto.',
    '考えられる原因：': 'Posibles causas:',
    '存在しないASINを指定している': 'Se ha especificado un ASIN no válido',
    'ASINが正しくない（半角英数字10文字）': 'El ASIN es incorrecto (10 caracteres alfanuméricos)',
    '商品が販売終了・取り下げされている': 'El producto ha sido descontinuado o retirado',
    'トラッキングIDが正しくない': 'El ID de seguimiento es incorrecto',
    'Creators APIの認証情報が正しくない': 'Las credenciales de la API de Creators son incorrectas',
    '（%s時点）': '(a partir de %s)',
    '「新着記事」表示': 'Mostrar "Artículos nuevos"',
    'NEWマーク（%d～%d日、0はオフ）': 'Marca NUEVO (%d\u2013%d días, 0 = desactivado)',
    'ダークモード': 'Modo oscuro',
    'フォントサイズ（%d～%dpx）': 'Tamaño de fuente (%d\u2013%dpx)',
    'Scrollボタンカラー': 'Color del botón de desplazamiento',
    '不透明度（opacity値%d～%d%%）': 'Opacidad (valor de opacidad %d\u2013%d%%)',
    'campaign\x00block keyword': 'campaign',
    'キャンペーン\x00block keyword': 'campaña',
    '期間\x00block keyword': 'período',
    'ログインユーザー限定\x00block title': 'Solo para usuarios registrados',
    'ログインしているユーザーにのみコンテンツを表示します\x00block description':
      'Muestra contenido solo a los usuarios que han iniciado sesión',
    'login\x00block keyword': 'login',
    'user\x00block keyword': 'user',
    'only\x00block keyword': 'only',
    'member\x00block keyword': 'member',
    '会員\x00block keyword': 'miembro',
    '楽天商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。\x00block description':
      'Busca y selecciona productos de Rakuten en el editor e inserta un enlace de producto estático.',
    'ブログカード（埋め込み）': 'Tarjeta de blog (insertada)',
    'URLが設定されていません': 'La URL no está configurada',
  },

  fr_FR: {
    '2日ごと': 'Tous les 2 jours',
    '3日ごと': 'Tous les 3 jours',
    '5日ごと': 'Tous les 5 jours',
    '楽天側に登録されている説明文を表示します（情報がない場合は表示されません）。descオプションが設定されている場合は、オプション値が優先して表示されます。':
      "Affiche la description enregistrée sur Rakuten (ne s'affiche pas si aucune information n'est disponible). Si l'option desc est configurée, la valeur de l'option sera affichée en priorité.",
    '1日ごと': 'Chaque jour',
    '3日ごと（推奨）': 'Tous les 3 jours (recommandé)',
    '1週間ごと': 'Chaque semaine',
    '更新間隔が短いほどAPIのリクエスト数が増えます。Amazon・楽天それぞれのAPIリクエスト制限にご注意ください。':
      "Plus l'intervalle de mise à jour est court, plus le nombre de requêtes API augmente. Veuillez respecter les limites de requêtes des API Amazon et Rakuten.",
    '5投稿': '5 articles',
    '※共有ホスティングをご利用の場合は30以下を推奨します。50以上に設定するとサーバー側のタイムアウト制限により処理が中断される場合があります。':
      "* Si vous utilisez un hébergement mutualisé, nous recommandons une valeur de 30 ou moins. Définir 50 ou plus peut interrompre le traitement en raison des délais d'expiration côté serveur.",
    '商品を取得できませんでした。Creators APIは正常に応答しましたが、商品情報が含まれていません。':
      "Impossible de récupérer le produit. L'API Creators a répondu avec succès, mais aucune information sur le produit n'a été incluse.",
    '考えられる原因：': 'Causes possibles :',
    '存在しないASINを指定している': 'Un ASIN invalide a été spécifié',
    'ASINが正しくない（半角英数字10文字）': "L'ASIN est incorrect (10 caractères alphanumériques)",
    '商品が販売終了・取り下げされている': 'Le produit a été arrêté ou retiré',
    'トラッキングIDが正しくない': "L'ID de suivi est incorrect",
    'Creators APIの認証情報が正しくない':
      "Les informations d'identification de l'API Creators sont incorrectes",
    '（%s時点）': '(au %s)',
    '「新着記事」表示': 'Affichage des "Nouveaux articles"',
    'NEWマーク（%d～%d日、0はオフ）': 'Marque NOUVEAU (%d\u2013%d jours, 0 = désactivé)',
    'ダークモード': 'Mode sombre',
    'フォントサイズ（%d～%dpx）': 'Taille de police (%d\u2013%dpx)',
    'Scrollボタンカラー': 'Couleur du bouton de défilement',
    '不透明度（opacity値%d～%d%%）': "Opacité (valeur d'opacité %d\u2013%d%%)",
    'campaign\x00block keyword': 'campaign',
    'キャンペーン\x00block keyword': 'campagne',
    '期間\x00block keyword': 'période',
    'ログインユーザー限定\x00block title': 'Réservé aux utilisateurs connectés',
    'ログインしているユーザーにのみコンテンツを表示します\x00block description':
      'Affiche le contenu uniquement aux utilisateurs connectés',
    'login\x00block keyword': 'login',
    'user\x00block keyword': 'user',
    'only\x00block keyword': 'only',
    'member\x00block keyword': 'member',
    '会員\x00block keyword': 'membre',
    '楽天商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。\x00block description':
      'Recherchez et sélectionnez des produits Rakuten dans l\'éditeur et insérez un lien de produit statique.',
    'ブログカード（埋め込み）': 'Carte de blog (intégrée)',
    'URLが設定されていません': "L'URL n'est pas configurée",
  },

  ko_KR: {
    '2日ごと': '2일마다',
    '3日ごと': '3일마다',
    '5日ごと': '5일마다',
    '楽天側に登録されている説明文を表示します（情報がない場合は表示されません）。descオプションが設定されている場合は、オプション値が優先して表示されます。':
      '라쿠텐에 등록된 설명을 표시합니다（정보가 없는 경우에는 표시되지 않습니다）. desc 옵션이 설정된 경우에는 옵션 값이 우선하여 표시됩니다.',
    '1日ごと': '매일',
    '3日ごと（推奨）': '3일마다（권장）',
    '1週間ごと': '매주',
    '更新間隔が短いほどAPIのリクエスト数が増えます。Amazon・楽天それぞれのAPIリクエスト制限にご注意ください。':
      '업데이트 간격이 짧을수록 API 요청 수가 증가합니다. Amazon・라쿠텐 각각의 API 요청 제한에 주의하시기 바랍니다.',
    '5投稿': '5개 게시물',
    '※共有ホスティングをご利用の場合は30以下を推奨します。50以上に設定するとサーバー側のタイムアウト制限により処理が中断される場合があります。':
      '※공유 호스팅을 사용하시는 경우 30 이하를 권장합니다. 50 이상으로 설정하면 서버 측 타임아웃 제한으로 인해 처리가 중단될 수 있습니다.',
    '商品を取得できませんでした。Creators APIは正常に応答しましたが、商品情報が含まれていません。':
      '상품을 가져올 수 없습니다. Creators API가 정상적으로 응답했지만 상품 정보가 포함되어 있지 않습니다.',
    '考えられる原因：': '가능한 원인:',
    '存在しないASINを指定している': '존재하지 않는 ASIN을 지정했습니다',
    'ASINが正しくない（半角英数字10文字）': 'ASIN이 올바르지 않습니다（영문자 및 숫자 10자리）',
    '商品が販売終了・取り下げされている': '상품이 판매 종료 또는 취하되었습니다',
    'トラッキングIDが正しくない': '트래킹 ID가 올바르지 않습니다',
    'Creators APIの認証情報が正しくない': 'Creators API 인증 정보가 올바르지 않습니다',
    '（%s時点）': '（%s 기준）',
    '「新着記事」表示': '최신 기사 표시',
    'NEWマーク（%d～%d日、0はオフ）': 'NEW 표시（%d～%d일, 0은 OFF）',
    'ダークモード': '다크 모드',
    'フォントサイズ（%d～%dpx）': '글꼴 크기（%d～%dpx）',
    'Scrollボタンカラー': '스크롤 버튼 색상',
    '不透明度（opacity値%d～%d%%）': '불투명도（opacity 값 %d～%d%%）',
    'campaign\x00block keyword': 'campaign',
    'キャンペーン\x00block keyword': '캠페인',
    '期間\x00block keyword': '기간',
    'ログインユーザー限定\x00block title': '로그인 사용자 전용',
    'ログインしているユーザーにのみコンテンツを表示します\x00block description':
      '로그인한 사용자에게만 콘텐츠를 표시합니다',
    'login\x00block keyword': 'login',
    'user\x00block keyword': 'user',
    'only\x00block keyword': 'only',
    'member\x00block keyword': 'member',
    '会員\x00block keyword': '회원',
    '楽天商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。\x00block description':
      '에디터에서 라쿠텐 상품을 검색・선택하여 정적 상품 링크를 삽입합니다.',
    'ブログカード（埋め込み）': '블로그 카드（임베드）',
    'URLが設定されていません': 'URL이 설정되지 않았습니다',
  },

  pt_PT: {
    '2日ごと': 'A cada 2 dias',
    '3日ごと': 'A cada 3 dias',
    '5日ごと': 'A cada 5 dias',
    '楽天側に登録されている説明文を表示します（情報がない場合は表示されません）。descオプションが設定されている場合は、オプション値が優先して表示されます。':
      'Exibe a descrição registada no Rakuten (não será exibida se não houver informações). Se a opção desc estiver configurada, o valor da opção será exibido com prioridade.',
    '1日ごと': 'Diariamente',
    '3日ごと（推奨）': 'A cada 3 dias (recomendado)',
    '1週間ごと': 'Semanalmente',
    '更新間隔が短いほどAPIのリクエスト数が増えます。Amazon・楽天それぞれのAPIリクエスト制限にご注意ください。':
      'Quanto mais curto for o intervalo de atualização, maior será o número de solicitações à API. Por favor, esteja atento aos limites de solicitações da API da Amazon e do Rakuten.',
    '5投稿': '5 publicações',
    '※共有ホスティングをご利用の場合は30以下を推奨します。50以上に設定するとサーバー側のタイムアウト制限により処理が中断される場合があります。':
      '* Se estiver a usar alojamento partilhado, recomendamos um valor de 30 ou menos. Definir 50 ou mais pode interromper o processamento devido aos limites de tempo do servidor.',
    '商品を取得できませんでした。Creators APIは正常に応答しましたが、商品情報が含まれていません。':
      'Não foi possível obter o produto. A API Creators respondeu com sucesso, mas nenhuma informação de produto foi incluída.',
    '考えられる原因：': 'Possíveis causas:',
    '存在しないASINを指定している': 'Um ASIN inválido foi especificado',
    'ASINが正しくない（半角英数字10文字）': 'O ASIN está incorreto (10 caracteres alfanuméricos)',
    '商品が販売終了・取り下げされている': 'O produto foi descontinuado ou removido',
    'トラッキングIDが正しくない': 'O ID de rastreamento está incorreto',
    'Creators APIの認証情報が正しくない': 'As credenciais da API Creators estão incorretas',
    '（%s時点）': '(em %s)',
    '「新着記事」表示': 'Exibir "Artigos recentes"',
    'NEWマーク（%d～%d日、0はオフ）': 'Marca NOVO (%d\u2013%d dias, 0 = desativado)',
    'ダークモード': 'Modo escuro',
    'フォントサイズ（%d～%dpx）': 'Tamanho da fonte (%d\u2013%dpx)',
    'Scrollボタンカラー': 'Cor do botão de deslocamento',
    '不透明度（opacity値%d～%d%%）': 'Opacidade (valor de opacidade %d\u2013%d%%)',
    'campaign\x00block keyword': 'campaign',
    'キャンペーン\x00block keyword': 'campanha',
    '期間\x00block keyword': 'período',
    'ログインユーザー限定\x00block title': 'Apenas para utilizadores com sessão iniciada',
    'ログインしているユーザーにのみコンテンツを表示します\x00block description':
      'Exibe conteúdo apenas para utilizadores com sessão iniciada',
    'login\x00block keyword': 'login',
    'user\x00block keyword': 'user',
    'only\x00block keyword': 'only',
    'member\x00block keyword': 'member',
    '会員\x00block keyword': 'membro',
    '楽天商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。\x00block description':
      'Pesquise e selecione produtos do Rakuten no editor e insira uma hiperligação de produto estática.',
    'ブログカード（埋め込み）': 'Cartão de blog (incorporado)',
    'URLが設定されていません': 'O URL não está configurado',
  },

  zh_CN: {
    '2日ごと': '每2天',
    '3日ごと': '每3天',
    '5日ごと': '每5天',
    '楽天側に登録されている説明文を表示します（情報がない場合は表示されません）。descオプションが設定されている場合は、オプション値が優先して表示されます。':
      '显示在乐天注册的说明（若无信息则不显示）。若设置了desc选项，则优先显示选项值。',
    '1日ごと': '每天',
    '3日ごと（推奨）': '每3天（推荐）',
    '1週間ごと': '每周',
    '更新間隔が短いほどAPIのリクエスト数が増えます。Amazon・楽天それぞれのAPIリクエスト制限にご注意ください。':
      '更新间隔越短，API请求次数越多。请注意Amazon・乐天各自的API请求限制。',
    '5投稿': '5篇文章',
    '※共有ホスティングをご利用の場合は30以下を推奨します。50以上に設定するとサーバー側のタイムアウト制限により処理が中断される場合があります。':
      '※如果您使用共享主机，建议设置为30以下。设置为50以上可能会因服务器端超时限制而导致处理中断。',
    '商品を取得できませんでした。Creators APIは正常に応答しましたが、商品情報が含まれていません。':
      '无法获取商品。Creators API已正常响应，但未包含商品信息。',
    '考えられる原因：': '可能的原因：',
    '存在しないASINを指定している': '指定了不存在的ASIN',
    'ASINが正しくない（半角英数字10文字）': 'ASIN不正确（10位英文字母和数字）',
    '商品が販売終了・取り下げされている': '商品已停售或下架',
    'トラッキングIDが正しくない': '跟踪ID不正确',
    'Creators APIの認証情報が正しくない': 'Creators API认证信息不正确',
    '（%s時点）': '（%s时）',
    '「新着記事」表示': '显示"最新文章"',
    'NEWマーク（%d～%d日、0はオフ）': 'NEW标记（%d～%d天，0为关闭）',
    'ダークモード': '深色模式',
    'フォントサイズ（%d～%dpx）': '字体大小（%d～%dpx）',
    'Scrollボタンカラー': '滚动按钮颜色',
    '不透明度（opacity値%d～%d%%）': '不透明度（opacity值%d～%d%%）',
    'campaign\x00block keyword': 'campaign',
    'キャンペーン\x00block keyword': '活动',
    '期間\x00block keyword': '期间',
    'ログインユーザー限定\x00block title': '仅限登录用户',
    'ログインしているユーザーにのみコンテンツを表示します\x00block description':
      '仅向已登录的用户显示内容',
    'login\x00block keyword': 'login',
    'user\x00block keyword': 'user',
    'only\x00block keyword': 'only',
    'member\x00block keyword': 'member',
    '会員\x00block keyword': '会员',
    '楽天商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。\x00block description':
      '在编辑器内搜索并选择乐天商品，插入静态商品链接。',
    'ブログカード（埋め込み）': '博客卡片（嵌入）',
    'URLが設定されていません': '未设置URL',
  },

  zh_TW: {
    '2日ごと': '每2天',
    '3日ごと': '每3天',
    '5日ごと': '每5天',
    '楽天側に登録されている説明文を表示します（情報がない場合は表示されません）。descオプションが設定されている場合は、オプション値が優先して表示されます。':
      '顯示在樂天註冊的說明（若無資訊則不顯示）。若設定了desc選項，則優先顯示選項值。',
    '1日ごと': '每天',
    '3日ごと（推奨）': '每3天（推薦）',
    '1週間ごと': '每週',
    '更新間隔が短いほどAPIのリクエスト数が増えます。Amazon・楽天それぞれのAPIリクエスト制限にご注意ください。':
      '更新間隔越短，API請求次數越多。請注意Amazon・樂天各自的API請求限制。',
    '5投稿': '5篇文章',
    '※共有ホスティングをご利用の場合は30以下を推奨します。50以上に設定するとサーバー側のタイムアウト制限により処理が中断される場合があります。':
      '※如果您使用共享主機，建議設置為30以下。設置為50以上可能因伺服器端逾時限制而導致處理中斷。',
    '商品を取得できませんでした。Creators APIは正常に応答しましたが、商品情報が含まれていません。':
      '無法取得商品。Creators API已正常回應，但未包含商品資訊。',
    '考えられる原因：': '可能的原因：',
    '存在しないASINを指定している': '指定了不存在的ASIN',
    'ASINが正しくない（半角英数字10文字）': 'ASIN不正確（10位英文字母和數字）',
    '商品が販売終了・取り下げされている': '商品已停售或下架',
    'トラッキングIDが正しくない': '追蹤ID不正確',
    'Creators APIの認証情報が正しくない': 'Creators API認證資訊不正確',
    '（%s時点）': '（%s時）',
    '「新着記事」表示': '顯示「最新文章」',
    'NEWマーク（%d～%d日、0はオフ）': 'NEW標記（%d～%d天，0為關閉）',
    'ダークモード': '深色模式',
    'フォントサイズ（%d～%dpx）': '字體大小（%d～%dpx）',
    'Scrollボタンカラー': '捲動按鈕顏色',
    '不透明度（opacity値%d～%d%%）': '不透明度（opacity值%d～%d%%）',
    'campaign\x00block keyword': 'campaign',
    'キャンペーン\x00block keyword': '活動',
    '期間\x00block keyword': '期間',
    'ログインユーザー限定\x00block title': '僅限登入使用者',
    'ログインしているユーザーにのみコンテンツを表示します\x00block description':
      '僅向已登入的使用者顯示內容',
    'login\x00block keyword': 'login',
    'user\x00block keyword': 'user',
    'only\x00block keyword': 'only',
    'member\x00block keyword': 'member',
    '会員\x00block keyword': '會員',
    '楽天商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。\x00block description':
      '在編輯器內搜尋並選擇樂天商品，插入靜態商品連結。',
    'ブログカード（埋め込み）': '部落格卡片（嵌入）',
    'URLが設定されていません': '未設定URL',
  },
};

/**
 * .po ファイルのテキストを解析して、空の msgstr を翻訳で埋める
 * msgctxt 付きエントリも対応
 */
function fillTranslations( content, langMap ) {
  // エントリを行単位で処理する
  const lines = content.split( '\n' );
  const result = [];
  let i = 0;
  // msgctxt はループをまたいで保持する必要があるため外側で宣言する
  let msgctxt = null;

  while ( i < lines.length ) {
    const line = lines[ i ];

    // msgctxt を検出
    if ( line.startsWith( 'msgctxt "' ) ) {
      msgctxt = line.slice( 9, -1 ); // "..." の中身を取り出す
      result.push( line );
      i++;
      continue;
    }

    // msgid を検出
    if ( line.startsWith( 'msgid "' ) && line !== 'msgid ""' ) {
      const msgid = line.slice( 7, -1 );

      // 次の行が msgstr "" かチェック
      let nextIdx = i + 1;
      // 複数行 msgid は今回の対象外（ほぼないが念のためスキップしない）
      if ( nextIdx < lines.length && lines[ nextIdx ] === 'msgstr ""' ) {
        // キーを構築: msgctxt 付きなら "msgid\x00msgctxt"
        const key = msgctxt ? `${ msgid }\x00${ msgctxt }` : msgid;
        const translation = langMap[ key ];

        if ( translation ) {
          // .po ファイルでは " や \ をエスケープする必要がある
          const escaped = translation
            .replace( /\\/g, '\\\\' )
            .replace( /"/g, '\\"' )
            .replace( /\n/g, '\\n' );
          result.push( line );
          result.push( `msgstr "${ escaped }"` );
          i += 2; // msgid と msgstr "" をスキップ
          msgctxt = null;
          continue;
        }
      }
      msgctxt = null; // msgid 処理後にリセット
    } else if ( line.startsWith( 'msgid ' ) ) {
      // msgid "" など、msgctxt をリセット
      msgctxt = null;
    }

    result.push( line );
    i++;
  }

  return result.join( '\n' );
}

let totalFilled = 0;

for ( const [ locale, langMap ] of Object.entries( translations ) ) {
  const filePath = path.join( LANG_DIR, `${ locale }.po` );
  if ( ! fs.existsSync( filePath ) ) {
    console.warn( `⚠️  ${ locale }.po が見つかりません` );
    continue;
  }

  const rawOriginal = fs.readFileSync( filePath, 'utf8' );
  // CRLF → LF に正規化してから処理し、最後に元の改行コードに戻す
  const isCRLF = rawOriginal.includes( '\r\n' );
  const original = isCRLF ? rawOriginal.replace( /\r\n/g, '\n' ) : rawOriginal;
  const updatedLF = fillTranslations( original, langMap );

  // 変更件数をカウント（msgstr "" → msgstr "..." の数）
  const before = ( original.match( /^msgstr ""$/gm ) || [] ).length;
  const after = ( updatedLF.match( /^msgstr ""$/gm ) || [] ).length;
  const filled = before - after;
  totalFilled += filled;

  const rawUpdated = isCRLF ? updatedLF.replace( /\n/g, '\r\n' ) : updatedLF;
  fs.writeFileSync( filePath, rawUpdated, 'utf8' );
  console.log( `✅ ${ locale }: ${ filled } 件翻訳追加（残り未翻訳: ${ after } 件）` );
}

console.log( `\n合計 ${ totalFilled } 件の翻訳を追加しました。` );
