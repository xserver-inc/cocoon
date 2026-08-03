const additions = require( './translations/translation-additions-202608' );
const skinMetadata = require( './translations/skin-metadata-translations' );

// 全言語の既存辞書を優先する統合翻訳辞書
module.exports = {
  en_US: { ...skinMetadata( 'en_US' ), ...additions( 'en_US' ), ...require( './translations/en_US' ) },
  de_DE: { ...skinMetadata( 'de_DE' ), ...additions( 'de_DE' ), ...require( './translations/de_DE' ) },
  fr_FR: { ...skinMetadata( 'fr_FR' ), ...additions( 'fr_FR' ), ...require( './translations/fr_FR' ) },
  es_ES: { ...skinMetadata( 'es_ES' ), ...additions( 'es_ES' ), ...require( './translations/es_ES' ) },
  ko_KR: { ...skinMetadata( 'ko_KR' ), ...additions( 'ko_KR' ), ...require( './translations/ko_KR' ) },
  pt_PT: { ...skinMetadata( 'pt_PT' ), ...additions( 'pt_PT' ), ...require( './translations/pt_PT' ) },
  zh_CN: { ...skinMetadata( 'zh_CN' ), ...additions( 'zh_CN' ), ...require( './translations/zh_CN' ) },
  zh_TW: { ...skinMetadata( 'zh_TW' ), ...additions( 'zh_TW' ), ...require( './translations/zh_TW' ) },
};
