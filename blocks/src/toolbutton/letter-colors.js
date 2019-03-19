/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

const { Fragment } = wp.element;
const { __ } = wp.i18n;
const { registerFormatType, toggleFormat } = wp.richText;
const { BlockControls, RichTextShortcut, RichTextToolbarButton } = wp.editor;
const THEME_NAME = 'cocoon';
const FORMAT_TYPE_NAME = 'cocoon-blocks/letter-colors';
import { Toolbar, DropdownMenu } from '@wordpress/components';

console.log('DropdownMenu');

registerFormatType( FORMAT_TYPE_NAME, {
  title: __( 'aaaaa色', THEME_NAME ),
  tagName: 'span',
  className: 'blue',
  edit( { isActive, value, onChange } ) {
    const onToggle = () => onChange( toggleFormat( value, { type: FORMAT_TYPE_NAME } ) );

    return (
      <Fragment>
            <DropdownMenu
        icon="move"
        label="Select a direction"
        controls={ [
            {
                title: 'Up',
                icon: 'arrow-up-alt',
                onClick: () => console.log( 'up' )
            },
            {
                title: 'Right',
                icon: 'arrow-right-alt',
                onClick: () => console.log( 'right' )
            },
            {
                title: 'Down',
                icon: 'arrow-down-alt',
                onClick: () => console.log( 'down' )
            },
            {
                title: 'Left',
                icon: 'arrow-left-alt',
                onClick: () => console.log( 'left' )
            },
        ] }
    />
      </Fragment>
    );
  },
} );