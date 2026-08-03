/**
 * 翻訳監査で見つかった単数形・複数形・コンテキスト付き翻訳の補正辞書
 */

const createLocaleEntries = ( locale, values ) => [
  { msgid: 'Cocoon：', msgstr: [ values.cocoonLabel ] },
  { msgid: 'スキン制御', msgstr: [ values.skinControl ] },
  { msgid: '%1$s%2$s', msgstr: [ values.yearMonthJoin ] },
  { msgid: '%s min', msgidPlural: '%s mins', msgstr: values.minute },
  { msgid: '%s hour', msgidPlural: '%s hours', msgstr: values.hour },
  { msgid: '%s day', msgidPlural: '%s days', msgstr: values.day },
  { msgid: '%s週間', msgidPlural: '%s週間', msgstr: values.week },
  { msgid: '%sヶ月', msgidPlural: '%sヶ月', msgstr: values.month },
  { msgid: '%s年', msgidPlural: '%s年', msgstr: values.year },
  ...Object.entries( values.keywords ).map( ( [ msgid, msgstr ] ) => ( {
    context: 'block keyword',
    msgid,
    msgstr: [ msgstr ],
  } ) ),
  ...Object.entries( values.additional || {} ).map( ( [ msgid, msgstr ] ) => ( {
    msgid,
    msgstr: [ msgstr ],
  } ) ),
].map( ( entry ) => ( { ...entry, locale } ) );

const translations = {
  de_DE: {
    cocoonLabel: 'Cocoon:',
    skinControl: 'Skin-Steuerung',
    yearMonthJoin: '%1$s und %2$s',
    minute: [ '%s Min.', '%s Min.' ],
    hour: [ '%s Stunde', '%s Stunden' ],
    day: [ '%s Tag', '%s Tage' ],
    week: [ '%s Woche', '%s Wochen' ],
    month: [ '%s Monat', '%s Monate' ],
    year: [ '%s Jahr', '%s Jahre' ],
    keywords: {
      campaign: 'Kampagne',
      login: 'Anmeldung',
      user: 'Benutzer',
      only: 'nur',
      member: 'Mitglied',
    },
  },
  en_US: {
    cocoonLabel: 'Cocoon:',
    skinControl: 'Skin control',
    yearMonthJoin: '%1$s and %2$s',
    minute: [ '%s min', '%s mins' ],
    hour: [ '%s hour', '%s hours' ],
    day: [ '%s day', '%s days' ],
    week: [ '%s week', '%s weeks' ],
    month: [ '%s month', '%s months' ],
    year: [ '%s year', '%s years' ],
    keywords: {
      campaign: 'campaign',
      login: 'login',
      user: 'user',
      only: 'only',
      member: 'member',
    },
  },
  es_ES: {
    cocoonLabel: 'Cocoon:',
    skinControl: 'Control del skin',
    yearMonthJoin: '%1$s y %2$s',
    minute: [ '%s min', '%s min' ],
    hour: [ '%s hora', '%s horas' ],
    day: [ '%s día', '%s días' ],
    week: [ '%s semana', '%s semanas' ],
    month: [ '%s mes', '%s meses' ],
    year: [ '%s año', '%s años' ],
    keywords: {
      campaign: 'campaña',
      login: 'inicio de sesión',
      user: 'usuario',
      only: 'solo',
      member: 'miembro',
    },
  },
  fr_FR: {
    cocoonLabel: 'Cocoon :',
    skinControl: 'Contrôle du skin',
    yearMonthJoin: '%1$s et %2$s',
    minute: [ '%s min', '%s min' ],
    hour: [ '%s heure', '%s heures' ],
    day: [ '%s jour', '%s jours' ],
    week: [ '%s semaine', '%s semaines' ],
    month: [ '%s mois', '%s mois' ],
    year: [ '%s an', '%s ans' ],
    keywords: {
      campaign: 'campagne',
      login: 'connexion',
      user: 'utilisateur',
      only: 'uniquement',
      member: 'membre',
    },
  },
  ko_KR: {
    cocoonLabel: 'Cocoon:',
    skinControl: '스킨 제어',
    yearMonthJoin: '%1$s %2$s',
    minute: [ '%s분' ],
    hour: [ '%s시간' ],
    day: [ '%s일' ],
    week: [ '%s주' ],
    month: [ '%s개월' ],
    year: [ '%s년' ],
    keywords: {
      campaign: '캠페인',
      login: '로그인',
      user: '사용자',
      only: '전용',
      member: '회원',
    },
    additional: {
      '付箋風（黄色）': '포스트잇 스타일(노랑)',
      '日本の木造建築に古くから使われている比率。大和比とも呼ばれています。':
        '일본 목조 건축에서 오래전부터 사용된 비율로, 야마토 비율이라고도 합니다.',
    },
  },
  pt_PT: {
    cocoonLabel: 'Cocoon:',
    skinControl: 'Controlo do skin',
    yearMonthJoin: '%1$s e %2$s',
    minute: [ '%s min', '%s min' ],
    hour: [ '%s hora', '%s horas' ],
    day: [ '%s dia', '%s dias' ],
    week: [ '%s semana', '%s semanas' ],
    month: [ '%s mês', '%s meses' ],
    year: [ '%s ano', '%s anos' ],
    keywords: {
      campaign: 'campanha',
      login: 'início de sessão',
      user: 'utilizador',
      only: 'apenas',
      member: 'membro',
    },
  },
  zh_CN: {
    cocoonLabel: 'Cocoon：',
    skinControl: '外观控制',
    yearMonthJoin: '%1$s%2$s',
    minute: [ '%s分钟' ],
    hour: [ '%s小时' ],
    day: [ '%s天' ],
    week: [ '%s周' ],
    month: [ '%s个月' ],
    year: [ '%s年' ],
    keywords: {
      campaign: '活动',
      login: '登录',
      user: '用户',
      only: '仅限',
      member: '会员',
    },
  },
  zh_TW: {
    cocoonLabel: 'Cocoon：',
    skinControl: '外觀控制',
    yearMonthJoin: '%1$s%2$s',
    minute: [ '%s分鐘' ],
    hour: [ '%s小時' ],
    day: [ '%s天' ],
    week: [ '%s週' ],
    month: [ '%s個月' ],
    year: [ '%s年' ],
    keywords: {
      campaign: '活動',
      login: '登入',
      user: '使用者',
      only: '僅限',
      member: '會員',
    },
  },
};

//ロケールごとの補正値を、同期処理が扱いやすいエントリー配列へ変換する
const getTranslationCorrections = ( locale ) => {
  const values = translations[ locale ];

  if ( ! values ) {
    throw new Error( `補正翻訳がありません: ${ locale }` );
  }

  return createLocaleEntries( locale, values );
};

getTranslationCorrections.locales = Object.keys( translations );

module.exports = getTranslationCorrections;
