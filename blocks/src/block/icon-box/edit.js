import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { InnerBlocks, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import classnames from 'classnames';


export default function edit({ attributes, setAttributes, className }) {
  const { style } = attributes;
  const classes = classnames('common-icon-box', 'block-box',
    {
      [ style ]: !! style,
      [ className ]: !! className,
    }
  );
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
                value: 'information-box',
                label: __( '補足情報(i)', THEME_NAME ),
              },
              {
                value: 'question-box',
                label: __( '補足情報(?)', THEME_NAME ),
              },
              {
                value: 'alert-box',
                label: __( '補足情報(!)', THEME_NAME ),
              },
              {
                value: 'memo-box',
                label: __( 'メモ', THEME_NAME ),
              },
              {
                value: 'comment-box',
                label: __( 'コメント', THEME_NAME ),
              },
              {
                value: 'ok-box',
                label: __( 'OK', THEME_NAME ),
              },
              {
                value: 'ng-box',
                label: __( 'NG', THEME_NAME ),
              },
              {
                value: 'good-box',
                label: __( 'GOOD', THEME_NAME ),
              },
              {
                value: 'bad-box',
                label: __( 'BAD', THEME_NAME ),
              },
              {
                value: 'profile-box',
                label: __( 'プロフィール', THEME_NAME ),
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
