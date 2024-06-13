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
import { get } from 'lodash';

const ALLOWED_MEDIA_TYPES = [ 'image' ];

export default function edit( props ) {
  const { attributes, setAttributes, className } = props;
  const {
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

  const getCtaContent = () => {
    // mediaIdから画像URLを取得する
    const media = wp.data.select( 'core' ).getMedia( mediaId );
    const url = get( media, [ 'source_url' ] );
    setAttributes( { image: url } );

    return <ServerSideRender block={ props.name } attributes={ attributes } />;
  };

  const onRemoveImage = () => {
    setAttributes( {
      mediaId: undefined,
    } );
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
              label={ __( 'CTA見出し', THEME_NAME ) }
              value={ header }
              onChange={ ( value ) => setAttributes( { header: value } ) }
            />
            <SelectControl
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
              label={ __( 'CTA画像', THEME_NAME ) }
              __nextHasNoMarginBottom={ true }
            >
              <MediaUploadCheck>
                <MediaUpload
                  onSelect={ ( media ) =>
                    setAttributes( { mediaId: media.id } )
                  }
                  allowedTypes={ ALLOWED_MEDIA_TYPES }
                  value={ mediaId }
                  render={ ( { open } ) => (
                    <Button onClick={ open }>
                      { __( '画像を選択', THEME_NAME ) }
                    </Button>
                  ) }
                />
              </MediaUploadCheck>
              { !! mediaId && (
                <MediaUploadCheck>
                  <Button onClick={ onRemoveImage }>
                    { __( '画像を削除', THEME_NAME ) }
                  </Button>
                </MediaUploadCheck>
              ) }
            </BaseControl>
            <TextareaControl
              label={ __( 'CTAメッセージ', THEME_NAME ) }
              value={ message }
              onChange={ ( value ) => setAttributes( { message: value } ) }
            />
            <CheckboxControl
              label={ __( '自動的に段落を追加する', THEME_NAME ) }
              checked={ autoParagraph }
              onChange={ ( value ) =>
                setAttributes( { autoParagraph: value } )
              }
            />
            <TextControl
              label={ __( 'CTAボタンテキスト', THEME_NAME ) }
              value={ buttonText }
              onChange={ ( value ) => setAttributes( { buttonText: value } ) }
            />
            <TextControl
              label={ __( 'CTAボタンURL', THEME_NAME ) }
              value={ buttonURL }
              onChange={ ( value ) => setAttributes( { buttonURL: value } ) }
            />
            <SelectControl
              label={ __( 'CTAボタン色', THEME_NAME ) }
              value={ buttonColor }
              options={ [
                { label: __( '黒色', THEME_NAME ), value: 'btn-black' },
                { label: __( '赤色', THEME_NAME ), value: 'btn-red' },
                { label: __( 'ピンク', THEME_NAME ), value: 'btn-pink' },
                { label: __( '紫色', THEME_NAME ), value: 'btn-purple' },
                { label: __( '深紫', THEME_NAME ), value: 'btn-deep' },
                {
                  label: __( '紺色(インディゴ)', THEME_NAME ),
                  value: 'btn-indigo',
                },
                { label: __( '青色', THEME_NAME ), value: 'btn-blue' },
                { label: __( '水色', THEME_NAME ), value: 'btn-light-blue' },
                {
                  label: __( '明るい青(シアン)', THEME_NAME ),
                  value: 'btn-cyan',
                },
                {
                  label: __( '緑色がかった青(ティール)', THEME_NAME ),
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
                  label: __( '琥珀色(アンバー)', THEME_NAME ),
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
