import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';

export default function edit( { attributes, setAttributes } ) {
  const { title, label } = attributes;

  const blockProps = useBlockProps( {
    className: 'timeline-item cf',
  } );

  return (
    <Fragment>
      <li { ...blockProps }>
        <div className="timeline-item-label">
          <RichText
            value={ label }
            onChange={ ( value ) => setAttributes( { label: value } ) }
            placeholder={ __( 'ラベル', THEME_NAME ) }
          />
        </div>
        <div className="timeline-item-content cf">
          <div className="timeline-item-title">
            <RichText
              value={ title }
              onChange={ ( value ) => setAttributes( { title: value } ) }
              placeholder={ __( 'タイトル', THEME_NAME ) }
            />
          </div>
          <div className="timeline-item-snippet">
            <InnerBlocks templateLock={ false } />
          </div>
        </div>
      </li>
    </Fragment>
  );
}
