/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../../helpers';

import { __ } from '@wordpress/i18n';
const { InnerBlocks, RichText } = wp.editor;

const { Fragment } = wp.element;

export default [
  {
    attributes: {
      label: {
        type: 'string',
        default: __( 'ラベル', THEME_NAME ),
      },
      title: {
        type: 'string',
        default: __( 'タイトル', THEME_NAME ),
      },
    },
    supports: {
      inserter: false,
    },

    edit( { attributes, setAttributes } ) {
      const { title, label } = attributes;
      return (
        <Fragment>
          <li className="timeline-item cf">
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
    },

    save( { attributes } ) {
      const { title, label } = attributes;
      return (
        <li className="timeline-item cf">
          <div className="timeline-item-label">
            <RichText.Content value={ label } />
          </div>
          <div className="timeline-item-content cf">
            <div className="timeline-item-title">
              <RichText.Content value={ title } />
            </div>
            <div className="timeline-item-snippet">
              <InnerBlocks.Content />
            </div>
          </div>
        </li>
      );
    },
  },
];
