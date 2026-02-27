/**
 * Amazon商品リンクブロック - エディタコンポーネント
 */
import { useState, useEffect, useCallback, useRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Button, Placeholder } from '@wordpress/components';
import { useBlockProps } from '@wordpress/block-editor';
import { THEME_NAME } from '../../helpers';
import SearchModal from './components/SearchModal';
import ProductPreview from './components/ProductPreview';
import SettingsPanel from './components/SettingsPanel';
import { getAmazonItem, renderPreview } from './utils/api';

// エディタコンポーネント本体
export default function Edit( { attributes, setAttributes } ) {
  const blockProps = useBlockProps();
  const { asin, staticHtml } = attributes;
  // プレビュー生成のレースコンディション対策用カウンター
  const previewRequestId = useRef( 0 );
  // デバウンス用タイマーID（インスタンスごとに独立）
  const debounceTimerRef = useRef( null );

  // 検索モーダルの開閉状態
  const [ isModalOpen, setIsModalOpen ] = useState( false );
  // プレビュー生成中のローディング状態
  const [ isPreviewLoading, setIsPreviewLoading ] = useState( false );

  // ブロック初回追加時にCocoon設定の初期値を適用
  useEffect( () => {
    if ( ! asin ) {
      setIsModalOpen( true );
      // staticHtmlも未設定 = 新規追加時のみCocoon設定を適用（再編集時は上書きしない）
      if (
        ! staticHtml &&
        typeof window.gbAmazonBlockDefaults !== 'undefined'
      ) {
        const defaults = window.gbAmazonBlockDefaults;
        setAttributes( {
          showBorder: !! Number( defaults.showBorder ),
          showLogo: !! Number( defaults.showLogo ),
          showCatalogImages: !! Number( defaults.showCatalogImages ),
          showPrice: !! Number( defaults.showPrice ),
          showDescription: !! Number( defaults.showDescription ),
          showReview: !! Number( defaults.showReview ),
          showAmazonButton: !! Number( defaults.showAmazonButton ),
          showRakutenButton: !! Number( defaults.showRakutenButton ),
          showYahooButton: !! Number( defaults.showYahooButton ),
          showMercariButton: !! Number( defaults.showMercariButton ),
          useMoshimoAffiliate: !! Number( defaults.useMoshimoAffiliate ),
        } );
      }
    }
  }, [] );

  // コンポーネント破棄時にデバウンスタイマーをクリア
  useEffect( () => {
    return () => clearTimeout( debounceTimerRef.current );
  }, [] );

  // プレビューを再生成する関数（レースコンディション対策付き）
  const regeneratePreview = useCallback( async ( currentAttrs ) => {
    if ( ! currentAttrs.asin ) return;
    // リクエストIDをインクリメントして最新のリクエストを追跡
    const requestId = ++previewRequestId.current;
    setIsPreviewLoading( true );
    try {
      // REST APIでプレビューHTMLを取得
      const res = await renderPreview( currentAttrs.asin, {
        size: currentAttrs.size,
        displayMode: currentAttrs.displayMode,
        showPrice: currentAttrs.showPrice,
        showReview: currentAttrs.showReview,
        showDescription: currentAttrs.showDescription,
        showLogo: currentAttrs.showLogo,
        showBorder: currentAttrs.showBorder,
        showCatalogImages: currentAttrs.showCatalogImages,
        showAmazonButton: currentAttrs.showAmazonButton,
        showRakutenButton: currentAttrs.showRakutenButton,
        showYahooButton: currentAttrs.showYahooButton,
        showMercariButton: currentAttrs.showMercariButton,
        customTitle: currentAttrs.customTitle,
        customDescription: currentAttrs.customDescription,
        searchKeyword: currentAttrs.searchKeyword,
        btn1Url: currentAttrs.btn1Url,
        btn1Text: currentAttrs.btn1Text,
        btn1Tag: currentAttrs.btn1Tag,
        btn2Url: currentAttrs.btn2Url,
        btn2Text: currentAttrs.btn2Text,
        btn2Tag: currentAttrs.btn2Tag,
        btn3Url: currentAttrs.btn3Url,
        btn3Text: currentAttrs.btn3Text,
        btn3Tag: currentAttrs.btn3Tag,
        useMoshimoAffiliate: currentAttrs.useMoshimoAffiliate,
        // 価格取得時刻を渡す（既存あればそのまま、なければ現在時刻で新規記録）
        priceFetchedAt: currentAttrs.priceFetchedAt || new Date().toISOString(),
      } );
      // 最新のリクエストでなければ結果を破棄（レースコンディション防止）
      if ( requestId !== previewRequestId.current ) return;
      if ( res.success ) {
        // staticHtmlとイテムデータを一括で更新（再レンダリング削減）
        const updateAttrs = {
          staticHtml: res.html,
          // priceFetchedAtが未設定の場合は現在時刻で初期化（次回以降変わらない）
          ...( ! currentAttrs.priceFetchedAt && {
            priceFetchedAt: new Date().toISOString(),
          } ),
        };
        if ( res.itemData ) {
          // APIの元タイトルを常に保存（customTitleは別属性で管理）
          updateAttrs.title = res.itemData.title;
          updateAttrs.maker = res.itemData.maker;
          updateAttrs.productGroup = res.itemData.productGroup;
          updateAttrs.description = res.itemData.description;
          updateAttrs.detailPageUrl = res.itemData.detailPageUrl;
          updateAttrs.imageSmallUrl = res.itemData.imageSmallUrl;
          updateAttrs.imageSmallWidth = res.itemData.imageSmallWidth;
          updateAttrs.imageSmallHeight = res.itemData.imageSmallHeight;
          updateAttrs.imageUrl = res.itemData.imageUrl;
          updateAttrs.imageWidth = res.itemData.imageWidth;
          updateAttrs.imageHeight = res.itemData.imageHeight;
          updateAttrs.imageLargeUrl = res.itemData.imageLargeUrl;
          updateAttrs.imageLargeWidth = res.itemData.imageLargeWidth;
          updateAttrs.imageLargeHeight = res.itemData.imageLargeHeight;
          updateAttrs.variantImages = res.itemData.variantImages;
        }
        setAttributes( updateAttrs );
      }
    } catch ( err ) {
      // 古いリクエストのエラーは無視
      if ( requestId !== previewRequestId.current ) return;
      console.error( 'Amazon block preview error:', err );
    }
    // 最新のリクエストの場合のみローディング解除
    if ( requestId === previewRequestId.current ) {
      setIsPreviewLoading( false );
    }
  }, [] );

  // 商品選択時の処理
  const handleProductSelect = useCallback(
    async ( item ) => {
      // まず検索結果のASINで即座にブロック状態を更新（プレースホルダーを解除）
      const selectedAsin = item.asin;
      const baseAttrs = {
        ...attributes,
        asin: selectedAsin,
        title: item.title || '',
        maker: item.maker || '',
        imageUrl: item.imageUrl || '',
      };
      setAttributes( baseAttrs );
      setIsPreviewLoading( true );

      // 商品詳細情報をAPIで取得してプレビューを生成
      try {
        const res = await getAmazonItem( selectedAsin );
        if ( res.success && res.item ) {
          const itemData = res.item;
          // ブロックの全属性を更新（ASINは検索結果のものを確実に使う）
          const newAttrs = {
            ...attributes,
            asin: selectedAsin,
            title: itemData.title || item.title || '',
            maker: itemData.maker || item.maker || '',
            productGroup: itemData.productGroup || '',
            description: itemData.description || '',
            detailPageUrl: itemData.detailPageUrl || '',
            imageSmallUrl: itemData.imageSmallUrl || '',
            imageSmallWidth: itemData.imageSmallWidth || 75,
            imageSmallHeight: itemData.imageSmallHeight || 75,
            imageUrl: itemData.imageUrl || item.imageUrl || '',
            imageWidth: itemData.imageWidth || 160,
            imageHeight: itemData.imageHeight || 160,
            imageLargeUrl: itemData.imageLargeUrl || '',
            imageLargeWidth: itemData.imageLargeWidth || 500,
            imageLargeHeight: itemData.imageLargeHeight || 500,
            variantImages: itemData.variantImages || [],
          };
          setAttributes( newAttrs );
          // プレビューHTMLを生成
          await regeneratePreview( newAttrs );
        } else {
          // レスポンスはあるが成功でない場合もプレビュー試行
          await regeneratePreview( baseAttrs );
        }
      } catch ( err ) {
        console.error( 'Amazon block get-item error:', err );
        // API取得失敗でも検索結果の基本情報でプレビューを試行
        await regeneratePreview( baseAttrs );
      }
    },
    [ attributes, regeneratePreview, setAttributes ]
  );

  // 設定変更時にプレビューを再生成（デバウンス付き）
  const handleSetAttributes = useCallback(
    ( newAttrs ) => {
      const merged = { ...attributes, ...newAttrs };
      setAttributes( newAttrs );
      // 表示に影響する属性が変更された場合のみ再生成
      const displayAttrs = [
        'size',
        'displayMode',
        'showPrice',
        'showReview',
        'showDescription',
        'showLogo',
        'showBorder',
        'showCatalogImages',
        'showAmazonButton',
        'showRakutenButton',
        'showYahooButton',
        'showMercariButton',
        'customTitle',
        'customDescription',
        'searchKeyword',
        'btn1Url',
        'btn1Text',
        'btn1Tag',
        'btn2Url',
        'btn2Text',
        'btn2Tag',
        'btn3Url',
        'btn3Text',
        'btn3Tag',
        'useMoshimoAffiliate',
      ];
      const needsRegen = displayAttrs.some( ( key ) => key in newAttrs );
      if ( needsRegen && merged.asin ) {
        // デバウンス: 500ms以内の連続変更をまとめて1回のAPI呼び出しに
        clearTimeout( debounceTimerRef.current );
        debounceTimerRef.current = setTimeout( () => {
          regeneratePreview( merged );
        }, 500 );
      }
    },
    [ attributes, regeneratePreview, setAttributes ]
  );

  // ASINが未選択の場合: プレースホルダーを表示
  if ( ! asin ) {
    return (
      <div { ...blockProps }>
        <Placeholder
          icon="cart"
          label={ __( 'Amazon商品リンク', THEME_NAME ) }
          instructions={ __( '商品を検索して選択してください。', THEME_NAME ) }
        >
          <Button variant="primary" onClick={ () => setIsModalOpen( true ) }>
            { __( '商品を検索', THEME_NAME ) }
          </Button>
        </Placeholder>
        { /* 検索モーダル */ }
        <SearchModal
          isOpen={ isModalOpen }
          onClose={ () => setIsModalOpen( false ) }
          onSelect={ handleProductSelect }
        />
      </div>
    );
  }

  // ASINが選択済み: プレビュー + 設定パネルを表示
  return (
    <div { ...blockProps }>
      { /* サイドバー設定パネル */ }
      <SettingsPanel
        attributes={ attributes }
        setAttributes={ handleSetAttributes }
      />

      { /* ツールバー領域 */ }
      <div
        style={ {
          display: 'flex',
          justifyContent: 'space-between',
          alignItems: 'center',
          marginBottom: '8px',
          fontSize: '12px',
          color: '#666',
        } }
      >
        <span>ASIN: { asin }</span>
        <div style={ { display: 'flex', gap: '8px' } }>
          <Button
            variant="secondary"
            size="small"
            onClick={ () => regeneratePreview( attributes ) }
          >
            { __( 'プレビュー更新', THEME_NAME ) }
          </Button>
          <Button
            variant="secondary"
            size="small"
            onClick={ () => setIsModalOpen( true ) }
          >
            { __( '商品を変更', THEME_NAME ) }
          </Button>
        </div>
      </div>

      { /* ライブプレビュー */ }
      <ProductPreview
        staticHtml={ staticHtml }
        isLoading={ isPreviewLoading }
      />

      { /* 検索モーダル */ }
      <SearchModal
        isOpen={ isModalOpen }
        onClose={ () => setIsModalOpen( false ) }
        onSelect={ handleProductSelect }
      />
    </div>
  );
}
