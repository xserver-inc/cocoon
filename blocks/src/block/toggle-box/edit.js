import { THEME_NAME, getDateID } from '../../helpers';
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
import { Fragment, useEffect } from '@wordpress/element';
import { compose, useInstanceId } from '@wordpress/compose';
import { useSelect } from '@wordpress/data';
import classnames from 'classnames';

export function ToggleBoxEdit( props ) {
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
    dateID,
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
  }, [ borderColor, backgroundColor, textColor ] );

  const classes = classnames( className, {
    'toggle-wrap': true,
    'toggle-box': true,
    'block-box': true,
    'has-text-color': textColor.color,
    'has-background': backgroundColor.color,
    'has-border-color': borderColor.color,
    [ backgroundColor.class ]: backgroundColor.class,
    [ textColor.class ]: textColor.class,
    [ borderColor.class ]: borderColor.class,
    [ fontSize.class ]: fontSize.class,
    'not-nested-style': notNestedStyle,
    'cocoon-block-toggle': true,
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

  const instanceId = useInstanceId( ToggleBoxEdit );
  useEffect( () => {
    setAttributes( { dateID: getDateID() + instanceId } );
  }, [ instanceId ] );

  return (
    <Fragment>
      <InspectorControls>
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
        <input
          id={ 'toggle-checkbox-' + dateID }
          className="toggle-checkbox"
          type="checkbox"
        />
        <label
          className="toggle-button"
          htmlFor={ 'toggle-checkbox-' + dateID }
        >
          <RichText
            value={ content }
            onChange={ ( value ) => setAttributes( { content: value } ) }
          />
        </label>
        <div className="toggle-content">
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
] )( ToggleBoxEdit );
