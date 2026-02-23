/**
 * Amazon商品リンクブロック - サイドバー設定パネル
 */
import { __ } from '@wordpress/i18n';
import {
  PanelBody,
  SelectControl,
  ToggleControl,
  TextControl,
  TextareaControl,
} from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
import { THEME_NAME } from '../../../helpers';

// 設定パネルコンポーネント
export default function SettingsPanel( { attributes, setAttributes } ) {
  const {
    size,
    displayMode,
    showPrice,
    showReview,
    showDescription,
    showLogo,
    showBorder,
    showCatalogImages,
    showAmazonButton,
    showRakutenButton,
    showYahooButton,
    showMercariButton,
    useMoshimoAffiliate,
    customTitle,
    customDescription,
    searchKeyword,
    btn1Url,
    btn1Text,
    btn2Url,
    btn2Text,
    btn3Url,
    btn3Text,
  } = attributes;

  return (
    <InspectorControls>
      { /* 表示設定パネル */ }
      <PanelBody title={ __( '表示設定', THEME_NAME ) } initialOpen={ true }>
        { /* 画像サイズ選択 */ }
        <SelectControl
          label={ __( '画像サイズ', THEME_NAME ) }
          value={ size }
          options={ [
            { label: __( '小（75px）', THEME_NAME ), value: 's' },
            { label: __( '中（160px）', THEME_NAME ), value: 'm' },
            { label: __( '大（500px）', THEME_NAME ), value: 'l' },
          ] }
          onChange={ ( val ) => setAttributes( { size: val } ) }
        />

        { /* 表示モード選択 */ }
        <SelectControl
          label={ __( '表示モード', THEME_NAME ) }
          value={ displayMode }
          options={ [
            { label: __( '通常', THEME_NAME ), value: 'normal' },
            { label: __( '画像のみ', THEME_NAME ), value: 'image_only' },
            { label: __( 'テキストのみ', THEME_NAME ), value: 'text_only' },
          ] }
          onChange={ ( val ) => setAttributes( { displayMode: val } ) }
        />

        { /* 外観トグル */ }
        <ToggleControl
          label={ __( '枠線を表示', THEME_NAME ) }
          checked={ showBorder }
          onChange={ ( val ) => setAttributes( { showBorder: val } ) }
        />
        <ToggleControl
          label={ __( 'Amazonロゴを表示', THEME_NAME ) }
          checked={ showLogo }
          onChange={ ( val ) => setAttributes( { showLogo: val } ) }
        />
        <ToggleControl
          label={ __( 'カタログ画像を表示', THEME_NAME ) }
          checked={ showCatalogImages }
          onChange={ ( val ) => setAttributes( { showCatalogImages: val } ) }
        />
        { /* 価格表示・説明文・レビュートグル */ }
        <ToggleControl
          label={ __( '価格を表示', THEME_NAME ) }
          checked={ showPrice }
          onChange={ ( val ) => setAttributes( { showPrice: val } ) }
        />
        <ToggleControl
          label={ __( '説明文を表示', THEME_NAME ) }
          checked={ showDescription }
          onChange={ ( val ) => setAttributes( { showDescription: val } ) }
        />
        <ToggleControl
          label={ __( 'レビューリンクを表示', THEME_NAME ) }
          checked={ showReview }
          onChange={ ( val ) => setAttributes( { showReview: val } ) }
        />
      </PanelBody>

      { /* カスタムコンテンツパネル */ }
      <PanelBody
        title={ __( 'カスタムコンテンツ', THEME_NAME ) }
        initialOpen={ false }
      >
        <TextControl
          label={ __( 'カスタムタイトル', THEME_NAME ) }
          value={ customTitle }
          onChange={ ( val ) => setAttributes( { customTitle: val } ) }
          help={ __( '空欄の場合はAPIから取得したタイトルを使用', THEME_NAME ) }
        />
        { /* カスタム説明文（説明文表示がOFFのときは無効化） */ }
        <TextareaControl
          label={ __( 'カスタム説明文', THEME_NAME ) }
          value={ customDescription }
          onChange={ ( val ) => setAttributes( { customDescription: val } ) }
          disabled={ ! showDescription }
          help={
            ! showDescription
              ? __(
                  '「説明文を表示」がOFFのため、カスタム説明文は表示されません',
                  THEME_NAME
                )
              : __( '空欄の場合はAPIから取得した説明文を使用', THEME_NAME )
          }
        />
        <TextControl
          label={ __( '検索キーワード', THEME_NAME ) }
          value={ searchKeyword }
          onChange={ ( val ) => setAttributes( { searchKeyword: val } ) }
          help={ __(
            'ボタンの検索リンクで使用。空欄の場合はタイトルを使用',
            THEME_NAME
          ) }
        />
      </PanelBody>

      { /* ストアボタン設定パネル */ }
      <PanelBody
        title={ __( 'ストアボタン', THEME_NAME ) }
        initialOpen={ false }
      >
        <ToggleControl
          label={ __( 'Amazonボタン', THEME_NAME ) }
          checked={ showAmazonButton }
          onChange={ ( val ) => setAttributes( { showAmazonButton: val } ) }
        />
        <ToggleControl
          label={ __( '楽天ボタン', THEME_NAME ) }
          checked={ showRakutenButton }
          onChange={ ( val ) => setAttributes( { showRakutenButton: val } ) }
        />
        <ToggleControl
          label={ __( 'Yahooボタン', THEME_NAME ) }
          checked={ showYahooButton }
          onChange={ ( val ) => setAttributes( { showYahooButton: val } ) }
        />
        <ToggleControl
          label={ __( 'メルカリボタン', THEME_NAME ) }
          checked={ showMercariButton }
          onChange={ ( val ) => setAttributes( { showMercariButton: val } ) }
        />
      </PanelBody>

      { /* もしもアフィリエイト設定パネル */ }
      <PanelBody
        title={ __( 'もしもアフィリエイト', THEME_NAME ) }
        initialOpen={ false }
      >
        { /* もしもIDが1つも設定されていない場合は無効表示 */ }
        { typeof window.gbAmazonBlockDefaults !== 'undefined' &&
        ! Number( window.gbAmazonBlockDefaults.hasMoshimoIds ) ? (
          <p
            style={ {
              fontSize: '12px',
              color: '#999',
              padding: '8px',
              backgroundColor: '#f9f9f9',
              borderLeft: '3px solid #ccc',
              lineHeight: '1.5',
            } }
          >
            { __(
              'Cocoon設定でもしもアフィリエイトのID（Amazon / 楽天 / Yahoo）が1つも設定されていないため、この機能は利用できません。',
              THEME_NAME
            ) }
          </p>
        ) : (
          <>
            <ToggleControl
              label={ __(
                'リンクをもしもアフィリエイト経由にする',
                THEME_NAME
              ) }
              checked={ useMoshimoAffiliate }
              onChange={ ( val ) =>
                setAttributes( { useMoshimoAffiliate: val } )
              }
            />
            <p
              style={ {
                fontSize: '12px',
                color: '#757575',
                marginTop: '4px',
                padding: '8px',
                backgroundColor: '#f9f9f9',
                borderLeft: '3px solid #f0b849',
                lineHeight: '1.5',
              } }
            >
              { __(
                '※ もしもアフィリエイト経由の場合は、Cocoon設定で各ストアのもしもIDが設定されている必要があります。',
                THEME_NAME
              ) }
            </p>
          </>
        ) }
      </PanelBody>

      { /* カスタムボタン設定パネル */ }
      <PanelBody
        title={ __( 'カスタムボタン', THEME_NAME ) }
        initialOpen={ false }
      >
        { /* ボタン1: テキスト → URL の順 */ }
        <TextControl
          label={ __( 'ボタン1 テキスト', THEME_NAME ) }
          value={ btn1Text }
          onChange={ ( val ) => setAttributes( { btn1Text: val } ) }
        />
        <TextControl
          label={ __( 'ボタン1 URL', THEME_NAME ) }
          value={ btn1Url }
          onChange={ ( val ) => setAttributes( { btn1Url: val } ) }
        />
        <hr />
        { /* ボタン2: テキスト → URL の順 */ }
        <TextControl
          label={ __( 'ボタン2 テキスト', THEME_NAME ) }
          value={ btn2Text }
          onChange={ ( val ) => setAttributes( { btn2Text: val } ) }
        />
        <TextControl
          label={ __( 'ボタン2 URL', THEME_NAME ) }
          value={ btn2Url }
          onChange={ ( val ) => setAttributes( { btn2Url: val } ) }
        />
        <hr />
        { /* ボタン3: テキスト → URL の順 */ }
        <TextControl
          label={ __( 'ボタン3 テキスト', THEME_NAME ) }
          value={ btn3Text }
          onChange={ ( val ) => setAttributes( { btn3Text: val } ) }
        />
        <TextControl
          label={ __( 'ボタン3 URL', THEME_NAME ) }
          value={ btn3Url }
          onChange={ ( val ) => setAttributes( { btn3Url: val } ) }
        />
      </PanelBody>
    </InspectorControls>
  );
}
