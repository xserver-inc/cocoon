/**
 * Amazon商品リンクブロック - 検索モーダル
 */
import { useState, useCallback, useRef, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
  Modal,
  TextControl,
  Button,
  Spinner,
  Notice,
} from '@wordpress/components';
import { THEME_NAME } from '../../../helpers';
import { searchAmazonProducts } from '../utils/api';
import SearchResults from './SearchResults';

// 検索モーダルコンポーネント
export default function SearchModal( { isOpen, onClose, onSelect } ) {
  // 検索キーワードの状態
  const [ keyword, setKeyword ] = useState( '' );
  // 検索結果の状態
  const [ results, setResults ] = useState( [] );
  // 検索中フラグ
  const [ isLoading, setIsLoading ] = useState( false );
  // エラーメッセージ
  const [ error, setError ] = useState( '' );
  // 現在のページ番号
  const [ page, setPage ] = useState( 1 );
  // 検索結果の総数
  const [ totalResults, setTotalResults ] = useState( 0 );
  // 検索実行済みフラグ
  const [ hasSearched, setHasSearched ] = useState( false );
  // 検索入力欄のDOM参照（自動フォーカス用）
  const inputRef = useRef( null );

  // モーダルが開くたびに検索欄へ自動フォーカス
  useEffect( () => {
    if ( ! isOpen ) return;
    // WordPressのModalがフォーカス制御を完了するのを待ってから実行
    const timer = setTimeout( () => {
      if ( inputRef.current ) {
        // TextControlが内部生成するinput要素を取得してフォーカス
        const input = inputRef.current.querySelector( 'input' );
        if ( input ) input.focus();
      }
    }, 100 );
    return () => clearTimeout( timer );
  }, [ isOpen ] );

  // 検索実行関数
  const doSearch = useCallback(
    async ( searchPage = 1 ) => {
      if ( ! keyword.trim() ) return;
      setIsLoading( true );
      setError( '' );
      try {
        const res = await searchAmazonProducts( keyword, searchPage );
        if ( res.success ) {
          setResults( res.items || [] );
          setTotalResults( res.totalResults || 0 );
          setPage( searchPage );
          setHasSearched( true );
        }
      } catch ( err ) {
        setError( err.message || __( '検索に失敗しました。', THEME_NAME ) );
        setResults( [] );
      }
      setIsLoading( false );
    },
    [ keyword ]
  );

  // Enterキーで検索
  const handleKeyDown = useCallback(
    ( e ) => {
      if ( e.key === 'Enter' ) {
        doSearch( 1 );
      }
    },
    [ doSearch ]
  );

  // 商品選択時の処理
  const handleSelect = useCallback(
    ( item ) => {
      onSelect( item );
      onClose();
    },
    [ onSelect, onClose ]
  );

  // モーダルが閉じている場合は何も表示しない
  if ( ! isOpen ) return null;

  return (
    <Modal
      title={ __( 'Amazon商品を検索', THEME_NAME ) }
      onRequestClose={ onClose }
      className="cocoon-amazon-search-modal"
      style={ { width: '700px', maxHeight: '80vh' } }
    >
      { /* 検索入力エリア */ }
      <div
        className="cocoon-amazon-search-input"
        style={ {
          display: 'flex',
          gap: '8px',
          marginBottom: '16px',
          alignItems: 'flex-end',
        } }
      >
        <div style={ { flex: 1 } } ref={ inputRef }>
          <TextControl
            placeholder={ __( 'キーワードを入力…', THEME_NAME ) }
            value={ keyword }
            onChange={ setKeyword }
            onKeyDown={ handleKeyDown }
            __nextHasNoMarginBottom
          />
        </div>
        <Button
          variant="primary"
          onClick={ () => doSearch( 1 ) }
          disabled={ isLoading || ! keyword.trim() }
          style={ { marginBottom: '0' } }
        >
          { __( '検索', THEME_NAME ) }
        </Button>
      </div>

      { /* エラー表示 */ }
      { error && (
        <Notice
          status="error"
          isDismissible={ false }
          style={ { marginBottom: '16px' } }
        >
          { error }
        </Notice>
      ) }

      { /* ローディング表示 */ }
      { isLoading && (
        <div style={ { textAlign: 'center', padding: '24px' } }>
          <Spinner />
        </div>
      ) }

      { /* 検索結果の表示 */ }
      { ! isLoading && results.length > 0 && (
        <SearchResults
          items={ results }
          onSelect={ handleSelect }
          page={ page }
          totalResults={ totalResults }
          onPageChange={ ( newPage ) => doSearch( newPage ) }
        />
      ) }

      { /* 検索結果が0件の場合（検索実行済みの場合のみ表示） */ }
      { ! isLoading && results.length === 0 && hasSearched && (
        <p style={ { textAlign: 'center', color: '#999', padding: '24px' } }>
          { __( '検索結果がありません。', THEME_NAME ) }
        </p>
      ) }

      { /* Amazon API規約に関する注意文 */ }
      <p
        style={ {
          fontSize: '11px',
          color: '#999',
          textAlign: 'center',
          marginTop: '16px',
          padding: '8px',
        } }
      >
        { __(
          '※ 検索結果の価格は参考値です。実際の価格は販売ページでご確認ください。',
          THEME_NAME
        ) }
      </p>
    </Modal>
  );
}
