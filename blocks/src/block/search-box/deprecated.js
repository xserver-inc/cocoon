/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../../helpers';
import classnames from 'classnames';

import { __ } from '@wordpress/i18n';
const { RichText } = wp.editor;
import { Fragment } from '@wordpress/element';
const DEFAULT_MSG = __( 'キーワード', THEME_NAME );

//classの取得
function getClasses() {
  const classes = classnames(
    {
      [ 'search-form' ]: true,
      [ 'block-box' ]: true,
    }
  );
  return classes;
}

export default [{
  attributes: {
    content: {
      type: 'string',
      default: DEFAULT_MSG,
    },
  },

  edit( { attributes, setAttributes, className } ) {
    const { content } = attributes;

    return (
      <Fragment>
        <div className={ classnames(getClasses(), className) }>
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
  },

  save( { attributes } ) {
    const { content } = attributes;
    return (

      <div className={ getClasses() }>
        <div className="sform">
          <RichText.Content
            value={ content }
          />
        </div>
        <div className="sbtn">{__( '検索', THEME_NAME )}</div>
      </div>
    );
  },
}];
