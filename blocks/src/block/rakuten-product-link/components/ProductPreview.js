/**
 * 楽天商品リンクブロック - ライブプレビュー
 */
import { RawHTML } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Spinner } from '@wordpress/components';
import { THEME_NAME } from '../../../helpers';

// 商品プレビューコンポーネント
export default function ProductPreview( { staticHtml, isLoading } ) {
  // ローディング中の表示
  if ( isLoading ) {
    return (
      <div style={ { textAlign: 'center', padding: '40px' } }>
        <Spinner />
        <p style={ { marginTop: '8px', color: '#666', fontSize: '13px' } }>
          { __( 'プレビューを生成中...', THEME_NAME ) }
        </p>
      </div>
    );
  }

  // HTMLがない場合のプレースホルダー
  if ( ! staticHtml ) {
    return (
      <div style={ { textAlign: 'center', padding: '40px', color: '#999', border: '2px dashed #ddd', borderRadius: '4px' } }>
        <p>{ __( '商品を選択してください', THEME_NAME ) }</p>
      </div>
    );
  }

  // 静的HTMLをプレビュー表示
  return (
    <div className="cocoon-rakuten-block-preview">
      <RawHTML>{ staticHtml }</RawHTML>
    </div>
  );
}
