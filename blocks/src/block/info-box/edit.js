import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import {
  InnerBlocks,
  InspectorControls,
  useBlockProps,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import classnames from 'classnames';

export default function edit( { attributes, setAttributes, className } ) {
  const { style } = attributes;
  const classes = classnames( 'block-box', {
    [ style ]: !! style,
    [ className ]: !! className,
  } );
  const blockProps = useBlockProps( {
    className: classes,
  } );

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
                value: 'primary-box',
                label: __( 'プライマリー（濃い水色）', THEME_NAME ),
              },
              {
                value: 'secondary-box',
                label: __( 'セカンダリー（濃い灰色）', THEME_NAME ),
              },
              {
                value: 'info-box',
                label: __( 'インフォ（薄い青色）', THEME_NAME ),
              },
              {
                value: 'success-box',
                label: __( 'サクセス（薄い緑色）', THEME_NAME ),
              },
              {
                value: 'warning-box',
                label: __( 'ワーニング（薄い黄色）', THEME_NAME ),
              },
              {
                value: 'danger-box',
                label: __( 'デンジャー（薄い赤色）', THEME_NAME ),
              },
              {
                value: 'light-box',
                label: __( 'ライト（白色）', THEME_NAME ),
              },
              {
                value: 'dark-box',
                label: __( 'ダーク（暗い灰色）', THEME_NAME ),
              },
            ] }
            __nextHasNoMarginBottom={ true }
          />
        </PanelBody>
      </InspectorControls>

      <div { ...blockProps }>
        <InnerBlocks />
      </div>
    </Fragment>
  );
}
