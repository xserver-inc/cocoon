/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, LetterToolbarButton } from '../helpers.js';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { registerFormatType, toggleFormat } from '@wordpress/rich-text';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faStrikethrough } from '@fortawesome/free-solid-svg-icons';
const FORMAT_TYPE_NAME = 'cocoon-blocks/strike';
const TITLE = __( '打ち消し線（訂正）', THEME_NAME );

registerFormatType( FORMAT_TYPE_NAME, {
  title: TITLE,
  tagName: 's',
  className: null,
  edit( { isActive, value, onChange } ) {
    const onToggle = () =>
      onChange( toggleFormat( value, { type: FORMAT_TYPE_NAME } ) );

    return (
      <Fragment>
        <LetterToolbarButton
          icon={ <FontAwesomeIcon icon={ faStrikethrough } /> }
          title={ <s>{ TITLE }</s> }
          onClick={ onToggle }
          isActive={ isActive }
        />
      </Fragment>
    );
  },
} );
