/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../helpers.js';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { registerFormatType, insert } from '@wordpress/rich-text';
import {
  RichTextToolbarButton,
  RichTextShortcut,
} from '@wordpress/block-editor';
import { Icon, html } from '@wordpress/icons';

const isPrivilegeActivationCodeAvailable = gbSettings[
  'isPrivilegeActivationCodeAvailable'
]
  ? gbSettings[ 'isPrivilegeActivationCodeAvailable' ]
  : '';

registerFormatType( 'cocoon-blocks/html', {
  title: __( 'HTML挿入', THEME_NAME ),
  tagName: 'span',
  className: 'insert-html',

  edit( { isActive, value, onChange } ) {
    const onToggle = () => {
      let html = '';
      // console.log(value);
      if ( value.end - value.start > 0 ) {
        value = insert(
          value,
          '[html]' +
            value.text.substr( value.start, value.end - value.start ) +
            '[/html]',
          value.start,
          value.end
        );
      } else {
        html =
          window.prompt( __( 'HTMLを入力してください。', THEME_NAME ) ) ||
          value.text.substr( value.start, value.end - value.start );
        if ( html ) {
          // console.log(html);
          value = insert(
            value,
            '[html]' + html + '[/html]',
            value.start,
            value.end
          );
        }
      }
      //console.log(value);
      return onChange( value );
    };

    // @see keycodes/src/index.js
    const shortcutType = 'primaryShift';
    const shortcutCharacter = 'h';
    return (
      <Fragment>
        <RichTextShortcut
          type={ shortcutType }
          character={ shortcutCharacter }
          onUse={ onToggle }
        />
        <RichTextToolbarButton
          icon={ <Icon icon={ html } size={ 32 } /> }
          title={ __( 'HTML挿入', THEME_NAME ) }
          onClick={ onToggle }
          isActive={ isActive }
          shorcutType={ shortcutType }
          shorcutCharacter={ shortcutCharacter }
          // className='abcddddddddddddddd'
        />
      </Fragment>
    );
  },
} );
