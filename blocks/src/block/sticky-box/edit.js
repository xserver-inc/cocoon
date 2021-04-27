import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { InnerBlocks, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import classnames from 'classnames';

export default function edit({ attributes, setAttributes, className }) {
  const { style } = attributes;
  const classes = classnames('blank-box', 'block-box', 'sticky',
    {
      [ style ]: !! style,
      [ className ]: !! className,
    }
);;
  const blockProps = useBlockProps({
    className: classes,
  });

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>

          <SelectControl
            label={ __( 'タイプ', THEME_NAME ) }
            value={ style }
            onChange={ ( value ) => setAttributes( { style: value } ) }
            options={ [
              {
                value: '',
                label: __( '灰色', THEME_NAME ),
              },
              {
                value: 'st-yellow',
                label: __( '黄色', THEME_NAME ),
              },
              {
                value: 'st-red',
                label: __( '赤色', THEME_NAME ),
              },
              {
                value: 'st-blue',
                label: __( '青色', THEME_NAME ),
              },
              {
                value: 'st-green',
                label: __( '緑色', THEME_NAME ),
              },
            ] }
          />

        </PanelBody>
      </InspectorControls>

      <div { ...blockProps }>
        <InnerBlocks />
      </div>
    </Fragment>
  );
}