/**
 * Amazon商品リンクブロック - 検索結果一覧
 */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import { THEME_NAME } from '../../../helpers';

// 検索結果一覧コンポーネント
export default function SearchResults( {
  items,
  onSelect,
  page,
  totalResults,
  onPageChange,
} ) {
  // 最大ページ数（API制限: 最大10ページ）
  const maxPage = Math.min( 10, Math.ceil( totalResults / 10 ) );

  return (
    <div className="cocoon-amazon-search-results">
      { /* 検索結果ヘッダー */ }
      <div style={ { fontSize: '12px', color: '#666', marginBottom: '8px' } }>
        { totalResults > 0 && (
          <>
            { totalResults }
            { __( '件の結果', THEME_NAME ) }
          </>
        ) }
      </div>

      { /* 検索結果リスト */ }
      <div style={ { maxHeight: '400px', overflowY: 'auto' } }>
        { items.map( ( item ) => (
          <div
            key={ item.asin }
            className="cocoon-amazon-search-item"
            style={ {
              display: 'flex',
              gap: '12px',
              padding: '12px',
              borderBottom: '1px solid #eee',
              cursor: 'pointer',
              transition: 'background-color 0.15s',
            } }
            onClick={ () => onSelect( item ) }
            onMouseEnter={ ( e ) => {
              e.currentTarget.style.backgroundColor = '#f0f7ff';
            } }
            onMouseLeave={ ( e ) => {
              e.currentTarget.style.backgroundColor = '';
            } }
            role="button"
            tabIndex={ 0 }
            onKeyDown={ ( e ) => {
              if ( e.key === 'Enter' ) onSelect( item );
            } }
          >
            { /* 商品画像 */ }
            <div style={ { flexShrink: 0, width: '60px', height: '60px' } }>
              { item.imageUrl ? (
                <img
                  src={ item.imageUrl }
                  alt={ item.title }
                  style={ {
                    maxWidth: '60px',
                    maxHeight: '60px',
                    objectFit: 'contain',
                  } }
                />
              ) : (
                <div
                  style={ {
                    width: '60px',
                    height: '60px',
                    backgroundColor: '#f0f0f0',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    fontSize: '10px',
                    color: '#999',
                  } }
                >
                  No Image
                </div>
              ) }
            </div>

            { /* 商品情報 */ }
            <div style={ { flex: 1, minWidth: 0 } }>
              <div
                style={ {
                  fontWeight: 'bold',
                  fontSize: '13px',
                  marginBottom: '4px',
                  overflow: 'hidden',
                  textOverflow: 'ellipsis',
                  whiteSpace: 'nowrap',
                } }
              >
                { item.title }
              </div>
              { item.maker && (
                <div style={ { fontSize: '11px', color: '#666' } }>
                  { item.maker }
                </div>
              ) }
              { item.price && (
                <div style={ { fontSize: '12px', color: '#b12704', marginTop: '2px' } }>
                  { item.price }
                </div>
              ) }
            </div>
          </div>
        ) ) }
      </div>

      { /* ページネーション */ }
      { maxPage > 1 && (
        <div
          style={ {
            display: 'flex',
            justifyContent: 'center',
            gap: '8px',
            marginTop: '16px',
            paddingTop: '12px',
            borderTop: '1px solid #eee',
          } }
        >
          <Button
            variant="secondary"
            disabled={ page <= 1 }
            onClick={ () => onPageChange( page - 1 ) }
            size="small"
          >
            { __( '前へ', THEME_NAME ) }
          </Button>
          <span style={ { lineHeight: '30px', fontSize: '13px' } }>
            { page } / { maxPage }
          </span>
          <Button
            variant="secondary"
            disabled={ page >= maxPage }
            onClick={ () => onPageChange( page + 1 ) }
            size="small"
          >
            { __( '次へ', THEME_NAME ) }
          </Button>
        </div>
      ) }
    </div>
  );
}
