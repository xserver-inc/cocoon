/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import {
  InspectorControls,
  InnerBlocks,
  useBlockProps,
} from '@wordpress/block-editor';
import { PanelBody, TextareaControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';

export default function LoginUserOnlyEdit( { attributes, setAttributes } ) {
  const { msg } = attributes;

  const blockProps = useBlockProps( {
    className: 'login-user-only block-box',
    style: {
      border: '2px dotted #3f51b5',
      padding: '24px',
      position: 'relative',
      textAlign: 'center',
    },
  } );

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={ __( '設定', THEME_NAME ) }>
          <TextareaControl
            label={ __( '未ログインユーザーに表示するメッセージ', THEME_NAME ) }
            value={ msg }
            onChange={ ( newMsg ) => setAttributes( { msg: newMsg } ) }
            help={ __(
              'ログインしていないユーザーに対して、ここのメッセージが表示されます。',
              THEME_NAME
            ) }
          />
        </PanelBody>
      </InspectorControls>

      <div { ...blockProps }>
        <div
          style={ {
            position: 'absolute',
            top: '-12px',
            left: '16px',
            background: '#3f51b5',
            color: '#fff',
            padding: '4px 8px',
            fontSize: '12px',
            fontWeight: 'bold',
            borderRadius: '4px',
            zIndex: 1,
          } }
        >
          { __( 'ログインユーザー限定', THEME_NAME ) }
        </div>
        { /* 注意: エディタ上では常に中身（InnerBlocks）が表示されます */ }
        <div style={ { marginTop: '8px' } }>
          <InnerBlocks />
        </div>
      </div>
    </Fragment>
  );
}
