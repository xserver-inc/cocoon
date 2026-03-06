/**
 * Amazon商品リンクブロック - 静的HTML出力（save）
 * ブロック属性のstaticHtmlをそのまま出力する
 */
import { RawHTML } from '@wordpress/element';

// save関数: 保存時に静的HTMLをそのまま出力
export default function save( { attributes } ) {
  const { staticHtml, asin } = attributes;

  // ASINが未選択の場合は何も出力しない
  if ( ! asin ) {
    return null;
  }

  // staticHtmlをそのまま出力（PHP側で生成済みの完全なHTML）
  return <RawHTML>{ staticHtml }</RawHTML>;
}
