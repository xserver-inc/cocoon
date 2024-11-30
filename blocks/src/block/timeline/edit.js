import { THEME_NAME } from '../../helpers';
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
import { PanelBody, RangeControl } from '@wordpress/components';
import { Fragment, useEffect } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import { useSelect } from '@wordpress/data';
import classnames from 'classnames';
import memoize from 'memize';
import { times } from 'lodash';

const ALLOWED_BLOCKS = [ 'cocoon-blocks/timeline-item' ];

const getItemsTemplate = memoize( ( items ) => {
  return times( items, () => [ 'cocoon-blocks/timeline-item' ] );
} );

export function TimelineEdit( props ) {
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
    pointColor,
    setPointColor,
    fontSize,
    clientId,
  } = props;

  const {
    title,
    items,
    customBackgroundColor,
    customTextColor,
    customBorderColor,
    customPointColor,
    notNestedStyle,
    backgroundColorValue,
    textColorValue,
    borderColorValue,
    pointColorValue,
  } = attributes;

  // インターブロックの数を取得
  const innerBlockIds = useSelect( ( select ) =>
    select( 'core/block-editor' )
      .getBlocks( clientId )
      .map( ( block ) => block.clientId )
  );

  // インターブロックの数が変わったらitemsを更新
  useEffect( () => {
    if (
      ( items !== innerBlockIds.length )
      && ( innerBlockIds.length > 0 ) // タイムラインアイテムが0になることはないので除外
    ) {
      setAttributes( { items: innerBlockIds.length } );
    }
  }, [ innerBlockIds.length ] );

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
    setAttributes( { pointColorValue: pointColor.color } );
  }, [ backgroundColor, textColor, borderColor, pointColor ] );

  const classes = classnames( className, {
    'timeline-box': true,
    'cf': true,// eslint-disable-line prettier/prettier
    'block-box': true,
    'has-text-color': textColor.color,
    'has-background': backgroundColor.color,
    'has-border-color': borderColor.color,
    'has-point-color': pointColor.color,
    [ backgroundColor.class ]: backgroundColor.class,
    [ textColor.class ]: textColor.class,
    [ borderColor.class ]: borderColor.class,
    [ pointColor.class ]: pointColor.class,
    [ fontSize.class ]: fontSize.class,
    'not-nested-style': notNestedStyle,
    'cocoon-block-timeline': true,
  } );

  const styles = {
    '--cocoon-custom-border-color': customBorderColor || undefined,
    '--cocoon-custom-background-color': customBackgroundColor || undefined,
    '--cocoon-custom-text-color': customTextColor || undefined,
    '--cocoon-custom-point-color': customPointColor || undefined,
  };

  if ( notNestedStyle ) {
    styles[ '--cocoon-custom-border-color' ] = borderColorValue;
    styles[ '--cocoon-custom-background-color' ] = backgroundColorValue;
    styles[ '--cocoon-custom-text-color' ] = textColorValue;
    styles[ '--cocoon-custom-point-color' ] = pointColorValue;
  }

  const blockProps = useBlockProps( {
    className: classes,
    style: styles,
  } );

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>
          <RangeControl
            label={ __( 'アイテム数', THEME_NAME ) }
            value={ items }
            onChange={ ( value ) => setAttributes( { items: value } ) }
            min={ 1 }
            max={ 50 }
          />
        </PanelBody>

        <PanelColorSettings
          title={ __( '色設定', THEME_NAME ) }
          colorSettings={ [
            {
              label: __( 'ポイント色', THEME_NAME ),
              onChange: setPointColor,
              value: pointColor.color,
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
            {
              label: __( 'ボーダー色', THEME_NAME ),
              onChange: setBorderColor,
              value: borderColor.color,
            },
          ] }
          __experimentalIsRenderedInSidebar={ true }
        />
      </InspectorControls>

      <div { ...blockProps }>
        <div className="timeline-title">
          <RichText
            value={ title }
            onChange={ ( value ) => setAttributes( { title: value } ) }
            placeholder={ __( 'タイトル', THEME_NAME ) }
          />
        </div>
        <ul className="timeline">
          <InnerBlocks
            template={ getItemsTemplate( items ) }
            templateLock="all"
            allowedBlocks={ ALLOWED_BLOCKS }
          />
        </ul>
      </div>
    </Fragment>
  );
}

export default compose( [
  withColors( 'backgroundColor', {
    textColor: 'color',
    borderColor: 'border-color',
    pointColor: 'point-color',
  } ),
  withFontSizes( 'fontSize' ),
] )( TimelineEdit );
