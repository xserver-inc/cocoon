import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save( { attributes } ) {
  const { content } = attributes;

  const classes = classnames( 'search-form', 'block-box' );
  const blockProps = useBlockProps.save( {
    className: classes,
  } );

  return (
    <div { ...blockProps }>
      <div className="sform">
        <RichText.Content value={ content } />
      </div>
      <div className="sbtn">{ __( '検索', THEME_NAME ) }</div>
    </div>
  );
}
