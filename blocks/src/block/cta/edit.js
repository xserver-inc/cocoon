import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
  SelectControl,
  Panel,
  PanelBody,
  TextControl,
  TextareaControl,
  CheckboxControl,
  Button,
  BaseControl,
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/editor';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import classnames from 'classnames';

const ALLOWED_MEDIA_TYPES = [ 'image' ];

export default function edit( props ) {
  const { attributes, setAttributes, className } = props;
  const {
    initialized,
    header,
    layout,
    mediaId,
    image,
    message,
    autoParagraph,
    buttonText,
    buttonURL,
    buttonColor,
  } = attributes;

  const classes = classnames( 'cta-block-box', 'block-box', {
    [ className ]: !! className,
    [ attributes.className ]: !! attributes.className,
  } );
  setAttributes( { classNames: classes } );

  // ブロック追加時の初回だけ初期値を流し込む
  if ( initialized === false ) {
    setAttributes( {
      initialized: true,
      header: __( 'CTA見出し', THEME_NAME ),
      mediaId: '',
      image: '',
      message: __( 'ここに訴求メッセージを入力してください。', THEME_NAME ),
      buttonText: __( 'この記事を読む', THEME_NAME ),
      buttonURL: './',
    } );
    return;
  }

  // 画像削除
  const onRemoveImage = () => {
    setAttributes( {
      mediaId: '',
      image: '',
    } );
  };

  // 画像選択
  const onSelectImage = ( media ) => {
    setAttributes( { mediaId: String( media.id ), image: media.url } );
  };

  const getCtaContent = () => {
    return <ServerSideRender block={ props.name } attributes={ attributes } />;
  };

  return (
    <Fragment>
      <InspectorControls>
        <Panel>
          <PanelBody
            title={ __( '基本設定', THEME_NAME ) }
            initialOpen={ true }
          >
            <TextControl
              className={ 'cta-text-control cta-header-text-control' }
              label={ __( '見出し', THEME_NAME ) }
              value={ header }
              onChange={ ( value ) => setAttributes( { header: value } ) }
            />
            <SelectControl
              className={ 'cta-select-control cta-image-layout-select-control' }
              label={ __( '画像とメッセージのレイアウト', THEME_NAME ) }
              value={ layout }
              options={ [
                {
                  label: __( '画像・メッセージを上下に配置', THEME_NAME ),
                  value: 'cta-top-and-bottom',
                },
                {
                  label: __( '画像・メッセージを左右に配置', THEME_NAME ),
                  value: 'cta-left-and-right',
                },
                {
                  label: __( 'メッセージ・画像を左右に配置', THEME_NAME ),
                  value: 'cta-right-and-left',
                },
              ] }
              onChange={ ( value ) => setAttributes( { layout: value } ) }
            />
            <BaseControl
              className={ 'cta-base-control cta-image-base-control' }
              label={ __( '画像', THEME_NAME ) }
              __nextHasNoMarginBottom={ true }
            >
              { <img src={ image } className="cta-image" alt="" /> }
              <div className="cta-btn-group">
                <MediaUploadCheck>
                  <MediaUpload
                    onSelect={ onSelectImage }
                    allowedTypes={ ALLOWED_MEDIA_TYPES }
                    value={ mediaId }
                    render={ ( { open } ) => (
                      <Button
                        onClick={ open }
                        variant="secondary"
                        className={ 'cta-btn cta-image-select-btn' }
                      >
                        { mediaId === ''
                          ? __( '選択', THEME_NAME )
                          : __( '置換', THEME_NAME ) }
                      </Button>
                    ) }
                  />
                </MediaUploadCheck>
                { mediaId !== '' && (
                  <MediaUploadCheck>
                    <Button
                      onClick={ onRemoveImage }
                      variant="secondary"
                      className="cta-btn cta-image-remove-btn"
                    >
                      { __( '削除', THEME_NAME ) }
                    </Button>
                  </MediaUploadCheck>
                ) }
              </div>

            </BaseControl>
            <TextareaControl
              className={ 'cta-textarea-control cta-message-textarea-control' }
              label={ __( 'メッセージ', THEME_NAME ) }
              value={ message }
              onChange={ ( value ) => setAttributes( { message: value } ) }
            />
            <CheckboxControl
              className={
                'cta-checkbox-control cta-paragraph-checkbox-control'
              }
              label={ __( '自動的に段落を追加する', THEME_NAME ) }
              checked={ autoParagraph }
              onChange={ ( value ) =>
                setAttributes( { autoParagraph: value } )
              }
            />
            <TextControl
              className={ 'cta-text-control cta-button-text-control' }
              label={ __( 'ボタンテキスト', THEME_NAME ) }
              value={ buttonText }
              onChange={ ( value ) => setAttributes( { buttonText: value } ) }
            />
            <TextControl
              className={ 'cta-text-control cta-button-url-text-control' }
              label={ __( 'ボタンURL', THEME_NAME ) }
              value={ buttonURL }
              onChange={ ( value ) => setAttributes( { buttonURL: value } ) }
            />
            <SelectControl
              className={ 'cta-select-control cta-button-color-select-control' }
              label={ __( 'ボタン色', THEME_NAME ) }
              value={ buttonColor }
              options={ [
                { label: __( '黒色', THEME_NAME ), value: 'btn-black' },
                { label: __( '赤色', THEME_NAME ), value: 'btn-red' },
                { label: __( 'ピンク', THEME_NAME ), value: 'btn-pink' },
                { label: __( '紫色', THEME_NAME ), value: 'btn-purple' },
                { label: __( '深紫', THEME_NAME ), value: 'btn-deep' },
                {
                  label: __( '紺色（インディゴ）', THEME_NAME ),
                  value: 'btn-indigo',
                },
                { label: __( '青色', THEME_NAME ), value: 'btn-blue' },
                { label: __( '水色', THEME_NAME ), value: 'btn-light-blue' },
                {
                  label: __( '明るい青（シアン）', THEME_NAME ),
                  value: 'btn-cyan',
                },
                {
                  label: __( '緑色がかった青（ティール）', THEME_NAME ),
                  value: 'btn-teal',
                },
                { label: __( '緑色', THEME_NAME ), value: 'btn-green' },
                {
                  label: __( '明るい緑', THEME_NAME ),
                  value: 'btn-light-green',
                },
                { label: __( 'ライム', THEME_NAME ), value: 'btn-lime' },
                { label: __( '黄色', THEME_NAME ), value: 'btn-yellow' },
                {
                  label: __( '琥珀色（アンバー）', THEME_NAME ),
                  value: 'btn-amber',
                },
                { label: __( 'オレンジ', THEME_NAME ), value: 'btn-orange' },
                {
                  label: __( 'ディープオレンジ', THEME_NAME ),
                  value: 'btn-deep-orange',
                },
                { label: __( '茶色', THEME_NAME ), value: 'btn-brown' },
                { label: __( '灰色', THEME_NAME ), value: 'btn-grey' },
              ] }
              onChange={ ( value ) => setAttributes( { buttonColor: value } ) }
            />
          </PanelBody>
        </Panel>
      </InspectorControls>
      <div { ...useBlockProps() }>{ getCtaContent() }</div>
    </Fragment>
  );
}
