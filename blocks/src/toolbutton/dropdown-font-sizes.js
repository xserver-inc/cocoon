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
const { Toolbar, DropdownMenu } = wp.components;
const THEME_NAME = 'cocoon';
const FORMAT_TYPE_NAME = 'cocoon-blocks/font-sizes';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

var sizes = [12, 14, 16, 18, 20, 22, 24, 28, 32, 36, 40, 44, 48];
sizes.map((size, index) => {
  var name = 'fz-' + size + 'px';
  registerFormatType( 'cocoon-blocks/' + name, {
    title: name,
    tagName: 'span',
    className: name,
  } );
});

registerFormatType( FORMAT_TYPE_NAME, {
  title: __( 'フォントサイズ', THEME_NAME ),
  tagName: 'span',
  className: 'font-sizes',
  edit( { isActive, value, onChange } ) {

    var cursolPositionObject = value.formats[value.start];
    var type = cursolPositionObject ? cursolPositionObject[0].type : '';

    var controls = [];
    sizes.map((size, index) => {
      var name = 'fz-' + size + 'px';
      controls.push({
        title: <span className={name}>{size}px</span>,
        icon: 'edit',
        isActive: type === 'cocoon-blocks/' + name,
        onClick: () => onChange( toggleFormat( value, { type: 'cocoon-blocks/' + name } ) )
      });
    });

    return (
      <Fragment>
        <BlockControls>
          <Toolbar>
            <DropdownMenu
              icon={ <FontAwesomeIcon icon="text-height" /> }
              label={__( 'フォントサイズ', THEME_NAME )}
              className='font-sizes'
              controls={ controls }
          />
          </Toolbar>
        </BlockControls>
      </Fragment>
    );
  },
} );