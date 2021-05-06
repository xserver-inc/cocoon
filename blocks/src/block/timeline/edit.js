import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import {
  InspectorControls,
  InnerBlocks,
  RichText,
  withColors,
  PanelColorSettings,
  withFontSizes,
  useBlockProps
} from '@wordpress/block-editor';
import {
  PanelBody,
  RangeControl,
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { compose } from '@wordpress/compose';
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
  } = props;

  const {
    title,
    items,
  } = attributes;

  const classes = classnames(className, {
      [ 'timeline-box' ]: true,
      [ 'cf' ]: true,
      [ 'block-box' ]: true,
      'has-text-color': textColor.color,
      'has-background': backgroundColor.color,
      'has-border-color': borderColor.color,
      'has-point-color': pointColor.color,
      [backgroundColor.class]: backgroundColor.class,
      [textColor.class]: textColor.class,
      [borderColor.class]: borderColor.class,
      [pointColor.class]: pointColor.class,
      [fontSize.class]: fontSize.class,
  });
  const blockProps = useBlockProps({
    className: classes,
  });

  return (
    <Fragment>
      <InspectorControls>

        <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>
        <RangeControl
          label={ __( 'アイテム数' ) }
          value={ items }
          onChange={ ( value ) => setAttributes( { items: value } ) }
          min={ 1 }
          max={ 50 }
        />
        </PanelBody>

        <PanelColorSettings
          title={ __( '色設定', THEME_NAME ) }
          colorSettings={[
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
          ]}
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

export default compose([
  withColors('backgroundColor', {textColor: 'color', borderColor: 'border-color', pointColor: 'point-color'}),
  withFontSizes('fontSize'),
])(TimelineEdit);