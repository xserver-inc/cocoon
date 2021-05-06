import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import classnames from 'classnames';

const DEFAULT_MSG = __( 'キーワード', THEME_NAME );

export default function edit({ attributes, setAttributes, className }) {
  const { content } = attributes;
  const classes = classnames('search-form', 'block-box', className);
  const blockProps = useBlockProps({
    className: classes,
  });

  return (
    <Fragment>
      <div { ...blockProps }>
        <div className="sform">
          <RichText
            value={ content }
            onChange={ ( value ) => setAttributes( { content: value } ) }
            placeholder={ DEFAULT_MSG }
          />
        </div>
        <div className="sbtn">
          { __( '検索', THEME_NAME ) }
        </div>
      </div>
    </Fragment>
  );
}