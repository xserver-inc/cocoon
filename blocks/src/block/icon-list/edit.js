import { THEME_NAME, LIST_ICONS } from '../../helpers';
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
import {
  PanelBody,
  BaseControl,
  Button,
  withFallbackStyles,
} from '@wordpress/components';
import { Component, Fragment } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import classnames from 'classnames';
import { times } from 'lodash';

const ALLOWED_BLOCKS = [ 'core/list' ];

export function IconListEdit( props ) {
  const {
    attributes,
    setAttributes,
    mergeBlocks,
    onReplace,
    className,
    backgroundColor,
    setBackgroundColor,
    textColor,
    setTextColor,
    borderColor,
    setBorderColor,
    iconColor,
    setIconColor,
    fallbackBackgroundColor,
    fallbackTextColor,
    fallbackBorderColor,
    fallbackIconColor,
    fallbackFontSize,
    fontSize,
    setFontSize,
  } = props;

  const {
    title,
    icon,
    customBackgroundColor,
    customTextColor,
    customBorderColor,
    customIconColor,
  } = attributes;

  const classes = classnames( className, {
    'iconlist-box': true,
    'blank-box': true,
    [ icon ]: !! icon,
    'block-box': true,
    'has-text-color': textColor.color,
    'has-background': backgroundColor.color,
    'has-border-color': borderColor.color,
    'has-icon-color': iconColor.color,
    [ backgroundColor.class ]: backgroundColor.class,
    [ textColor.class ]: textColor.class,
    [ borderColor.class ]: borderColor.class,
    [ iconColor.class ]: iconColor.class,
    [ fontSize.class ]: fontSize.class,
  } );

  const styles = {
    '--cocoon-custom-border-color': customBorderColor || undefined,
    '--cocoon-custom-background-color': customBackgroundColor || undefined,
    '--cocoon-custom-text-color': customTextColor || undefined,
    '--cocoon-custom-icon-color': customIconColor || undefined,
  };

  const blockProps = useBlockProps( {
    className: classes,
    style: styles,
  } );

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>
          <BaseControl label={ __( 'アイコン', THEME_NAME ) }>
            <div className="icon-setting-buttons">
              { times( LIST_ICONS.length, ( index ) => {
                return (
                  <Button
                    variant="secondary"
                    isPrimary={ icon === LIST_ICONS[ index ].value }
                    className={ LIST_ICONS[ index ].label }
                    onClick={ () => {
                      setAttributes( {
                        icon: LIST_ICONS[ index ].value,
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
              label: __( 'アイコン色', THEME_NAME ),
              onChange: setIconColor,
              value: iconColor.color,
            },
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
        <div className="iconlist-title">
          <RichText
            value={ title }
            placeholder={ __( 'タイトルがある場合は入力', THEME_NAME ) }
            onChange={ ( value ) => setAttributes( { title: value } ) }
          />
        </div>
        <InnerBlocks
          template={ [ [ 'core/list' ] ] }
          templateLock="all"
          allowedBlocks={ ALLOWED_BLOCKS }
        />
      </div>
    </Fragment>
  );
}

export default compose( [
  withColors( 'backgroundColor', {
    textColor: 'color',
    borderColor: 'border-color',
    iconColor: 'icon-color',
  } ),
  withFontSizes( 'fontSize' ),
  // FallbackStyles,
] )( IconListEdit );
