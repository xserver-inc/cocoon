/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

const { __ } = wp.i18n;

export const THEME_NAME = 'cocoon';
export const BLOCK_CLASS = ' block-box';
export const PARAGRAPH_CLASS = ' paragraph';

//日時をもとにしたID作成
export function getDateID(){
  //Dateオブジェクトを利用
  var d = new Date();
  var year  = d.getFullYear();
  var month = d.getMonth() + 1;
  var month = ( month          < 10 ) ? '0' + month          : month;
  var day   = ( d.getDate()    < 10 ) ? '0' + d.getDate()    : d.getDate();
  var hour  = ( d.getHours()   < 10 ) ? '0' + d.getHours()   : d.getHours();
  var min   = ( d.getMinutes() < 10 ) ? '0' + d.getMinutes() : d.getMinutes();
  var sec   = ( d.getSeconds() < 10 ) ? '0' + d.getSeconds() : d.getSeconds();
  var dateID = '' + year + month + day + hour + min + sec;
  return dateID;
}

//アイコンクラス文字列を取得
export function getIconClass(icon){
  return icon ? (' ' + icon) : '';
}

export const ICONS = [
  {
    value: '',
    label: 'fab-none',
  },
  {
    value: 'fab-info-circle',
    label: __( 'fab-info-circle', THEME_NAME ),
  },
  {
    value: 'fab-question-circle',
    label: __( 'fab-question-circle', THEME_NAME ),
  },
  {
    value: 'fab-exclamation-circle',
    label: __( 'fab-exclamation-circle', THEME_NAME ),
  },
  {
    value: 'fab-pencil',
    label: __( 'fab-pencil', THEME_NAME ),
  },
  {
    value: 'fab-edit',
    label: __( 'fab-edit', THEME_NAME ),
  },
  {
    value: 'fab-comment',
    label: __( 'fab-comment', THEME_NAME ),
  },
  {
    value: 'fab-ok',
    label: __( 'fab-ok', THEME_NAME ),
  },
  {
    value: 'fab-bad',
    label: __( 'fab-bad', THEME_NAME ),
  },
  {
    value: 'fab-thumbs-up',
    label: __( 'fab-thumbs-up', THEME_NAME ),
  },
  {
    value: 'fab-thumbs-down',
    label: __( 'fab-thumbs-down', THEME_NAME ),
  },
  {
    value: 'fab-check',
    label: __( 'fab-check', THEME_NAME ),
  },
  {
    value: 'fab-star',
    label: __( 'fab-star', THEME_NAME ),
  },
  {
    value: 'fab-bell',
    label: __( 'fab-bell', THEME_NAME ),
  },
  {
    value: 'fab-trophy',
    label: __( 'fab-trophy', THEME_NAME ),
  },
  {
    value: 'fab-lightbulb',
    label: __( 'fab-lightbulb', THEME_NAME ),
  },
  {
    value: 'fab-graduation-cap',
    label: __( 'fab-graduation-cap', THEME_NAME ),
  },
  {
    value: 'fab-bolt',
    label: __( 'fab-bolt', THEME_NAME ),
  },
  {
    value: 'fab-bookmark',
    label: __( 'fab-bookmark', THEME_NAME ),
  },
  {
    value: 'fab-book',
    label: __( 'fab-bookmark', THEME_NAME ),
  },
  {
    value: 'fab-download',
    label: __( 'fab-download', THEME_NAME ),
  },
  {
    value: 'fab-coffee',
    label: __( 'fab-coffee', THEME_NAME ),
  },
  {
    value: 'fab-amazon',
    label: __( 'fab-amazon', THEME_NAME ),
  },
  {
    value: 'fab-user',
    label: __( 'fab-user', THEME_NAME ),
  },
  {
    value: 'fab-envelope',
    label: __( 'fab-envelope', THEME_NAME ),
  },
  {
    value: 'fab-flag',
    label: __( 'fab-flag', THEME_NAME ),
  },
  {
    value: 'fab-ban',
    label: __( 'fab-ban', THEME_NAME ),
  },
  {
    value: 'fab-calendar',
    label: __( 'fab-calendar', THEME_NAME ),
  },
  {
    value: 'fab-clock',
    label: __( 'fab-clock', THEME_NAME ),
  },
  {
    value: 'fab-cutlery',
    label: __( 'fab-cutlery', THEME_NAME ),
  },
  {
    value: 'fab-heart',
    label: __( 'fab-heart', THEME_NAME ),
  },
  {
    value: 'fab-camera',
    label: __( 'fab-camera', THEME_NAME ),
  },
  {
    value: 'fab-search',
    label: __( 'fab-search', THEME_NAME ),
  },
  {
    value: 'fab-file-text',
    label: __( 'fab-file-text', THEME_NAME ),
  },
  {
    value: 'fab-folder',
    label: __( 'fab-folder', THEME_NAME ),
  },
  {
    value: 'fab-tag',
    label: __( 'fab-tag', THEME_NAME ),
  },
  {
    value: 'fab-car',
    label: __( 'fab-car', THEME_NAME ),
  },
  {
    value: 'fab-truck',
    label: __( 'fab-truck', THEME_NAME ),
  },
  {
    value: 'fab-bicycle',
    label: __( 'fab-bicycle', THEME_NAME ),
  },
  {
    value: 'fab-motorcycle',
    label: __( 'fab-motorcycle', THEME_NAME ),
  },
  {
    value: 'fab-bus',
    label: __( 'fab-bus', THEME_NAME ),
  },
  {
    value: 'fab-plane',
    label: __( 'fab-plane', THEME_NAME ),
  },
  {
    value: 'fab-train',
    label: __( 'fab-train', THEME_NAME ),
  },
  {
    value: 'fab-subway',
    label: __( 'fab-subway', THEME_NAME ),
  },
  {
    value: 'fab-taxi',
    label: __( 'fab-taxi', THEME_NAME ),
  },
  {
    value: 'fab-ship',
    label: __( 'fab-ship', THEME_NAME ),
  },
  {
    value: 'fab-jpy',
    label: __( 'fab-jpy', THEME_NAME ),
  },
  {
    value: 'fab-usd',
    label: __( 'fab-usd', THEME_NAME ),
  },
  {
    value: 'fab-eur',
    label: __( 'fab-eur', THEME_NAME ),
  },
  {
    value: 'fab-btc',
    label: __( 'fab-btc', THEME_NAME ),
  },
  {
    value: 'fab-apple',
    label: __( 'fab-apple', THEME_NAME ),
  },
  {
    value: 'fab-android',
    label: __( 'fab-android', THEME_NAME ),
  },
  {
    value: 'fab-wordpress',
    label: __( 'fab-wordpress', THEME_NAME ),
  },
];