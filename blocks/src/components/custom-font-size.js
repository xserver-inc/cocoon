// 旧版の数値文字列をpx数値へそろえる文字サイズの正規化
export function normalizeCustomFontSize( value ) {
  if ( typeof value === 'string' && value.trim() !== '' && Number.isFinite( Number( value ) ) ) {
    return Number( value );
  }
  return value;
}
