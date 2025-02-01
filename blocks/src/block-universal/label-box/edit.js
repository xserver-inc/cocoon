import { THEME_NAME, ICONS } from '../../helpers';
import { __ } from '@wordpress/i18n';
import {
  InspectorControls,
  InnerBlocks,
  RichText,
  withColors,
  PanelColorSettings,
  withFontSizes,
  useBlockProps,
} from '@wordpress/block-editor';
import { PanelBody, BaseControl, Button } from '@wordpress/components';
import { Fragment, useEffect } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import { useSelect } from '@wordpress/data';
import classnames from 'classnames';
import { times } from 'lodash';

const CAPTION_BOX_CLASS = 'label-box';
const DEFAULT_MSG = __( '見出し', THEME_NAME );

export function LabelBoxEdit( props ) {
  const {
    attributes,
    setAttributes,
    className,
    backgroundColor,
    setBackgroundColor,
    textColor,
    setTextColor,
    borderColor,
    setBorderColor,
    fontSize,
    clientId,
  } = props;

  const {
    content,
    icon,
    customBackgroundColor,
    customTextColor,
    customBorderColor,
    notNestedStyle,
    backgroundColorValue,
    textColorValue,
    borderColorValue,
  } = attributes;

  // 親ブロックのnotNestedStyleがfalseかどうかを判定
  const isParentNestedStyle = useSelect( ( select ) => {
    const parentBlocks =
      select( 'core/block-editor' ).getBlockParents( clientId );
    for ( const parentClientId of parentBlocks ) {
      const parentBlock =
        select( 'core/block-editor' ).getBlock( parentClientId );
      if (
        parentBlock.name === props.name &&
        parentBlock.attributes.notNestedStyle === false
      ) {
        return true;
      }
    }
    return false;
  } );

  // 親ブロックのnotNestedStyleがfalseの場合はnotNestedStyleをfalseにする
  if ( isParentNestedStyle && notNestedStyle ) {
    setAttributes( { notNestedStyle: false } );
  }

  useEffect( () => {
    setAttributes( { backgroundColorValue: backgroundColor.color } );
    setAttributes( { textColorValue: textColor.color } );
    setAttributes( { borderColorValue: borderColor.color } );
  }, [ backgroundColor, textColor, borderColor ] );

  const classes = classnames( className, {
    [ CAPTION_BOX_CLASS ]: true,
    'block-box': true,
    'has-text-color': textColor.color,
    'has-background': backgroundColor.color,
    'has-border-color': borderColor.color,
    [ backgroundColor.class ]: backgroundColor.class,
    [ textColor.class ]: textColor.class,
    [ borderColor.class ]: borderColor.class,
    [ fontSize.class ]: fontSize.class,
    'not-nested-style': notNestedStyle,
    'cocoon-block-label-box': true,
  } );

  const styles = {
    '--cocoon-custom-border-color': customBorderColor || undefined,
    '--cocoon-custom-background-color': customBackgroundColor || undefined,
    '--cocoon-custom-text-color': customTextColor || undefined,
  };

  if ( notNestedStyle ) {
    styles[ '--cocoon-custom-border-color' ] = borderColorValue;
    styles[ '--cocoon-custom-background-color' ] = backgroundColorValue;
    styles[ '--cocoon-custom-text-color' ] = textColorValue;
  }

  const blockProps = useBlockProps( {
    className: classes,
    style: styles,
  } );

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>
          <BaseControl id="labelBoxIcon" label={ __( 'アイコン', THEME_NAME ) }>
            <div className="icon-setting-buttons">
              { times( ICONS.length, ( index ) => {
                return (
                  <Button
                    variant="secondary"
                    isPrimary={ icon === ICONS[ index ].value }
                    className={ ICONS[ index ].label }
                    onClick={ () => {
                      setAttributes( {
                        icon: ICONS[ index ].value,
                      } );
                    } }
                    key={ index }
                  ></Button>
                );
              } ) }
            </div>
          </BaseControl>
        </PanelBody>

        <PanelColorSettings
          title={ __( '色設定', THEME_NAME ) }
          enableAlpha={true}
          colorSettings={ [
            {
              label: __( 'ボーダー色', THEME_NAME ),
              onChange: setBorderColor,
              value: borderColor.color,
            },
            {
              label: __( '背景色', THEME_NAME ),
              onChange: setBackgroundColor,
              value: backgroundColor.color,
            },
            {
              label: __( '文字色', THEME_NAME ),
              onChange: setTextColor,
              value: textColor.color,
            },
          ] }
          __experimentalIsRenderedInSidebar={ true }
        />
      </InspectorControls>

      <div { ...blockProps }>
        <div
          className={ classnames(
            'label-box-label',
            'block-box-label',
            'box-label',
            icon
          ) }
        >
          <span
            className={ classnames(
              'label-box-label-text',
              'block-box-label-text',
              'box-label-text'
            ) }
          >
            <RichText
              value={ content }
              onChange={ ( value ) => setAttributes( { content: value } ) }
              placeholder={ DEFAULT_MSG }
            />
          </span>
        </div>
        <div
          className={ classnames(
            'label-box-content',
            'block-box-content',
            'box-content'
          ) }
        >
          <InnerBlocks />
        </div>
      </div>
    </Fragment>
  );
}

export default compose( [
  withColors( 'backgroundColor', {
    textColor: 'color',
    borderColor: 'border-color',
  } ),
  withFontSizes( 'fontSize' ),
] )( LabelBoxEdit );
