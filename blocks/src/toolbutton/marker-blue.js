/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, MarkerToolbarButton } from '../helpers.js';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { registerFormatType, toggleFormat } from '@wordpress/rich-text';
import { RichTextShortcut } from '@wordpress/block-editor';
import { Icon, brush } from '@wordpress/icons';
const FORMAT_TYPE_NAME = 'cocoon-blocks/marker-blue';
const TITLE = __( '青色マーカー', THEME_NAME );

registerFormatType( FORMAT_TYPE_NAME, {
  title: TITLE,
  tagName: 'span',
  className: 'marker-blue',
  edit( { isActive, value, onChange } ) {
    const onToggle = () =>
      onChange( toggleFormat( value, { type: FORMAT_TYPE_NAME } ) );
    const shortcutType = 'primaryShift';
    const shortcutCharacter = 'b';

    return (
      <Fragment>
        <RichTextShortcut
          type={ shortcutType }
          character={ shortcutCharacter }
          onUse={ onToggle }
        />
        <MarkerToolbarButton
          icon={ <Icon icon={ brush } size={ 32 } /> }
          title={ <span className="marker-blue">{ TITLE }</span> }
          onClick={ onToggle }
          isActive={ isActive }
          shorcutType={ shortcutType }
          shorcutCharacter={ shortcutCharacter }
        />
      </Fragment>
    );
  },
} );
