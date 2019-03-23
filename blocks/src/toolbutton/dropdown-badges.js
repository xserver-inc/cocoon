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
const FORMAT_TYPE_NAME = 'cocoon-blocks/badges';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

registerFormatType( FORMAT_TYPE_NAME, {
  title: __( 'バッジ', THEME_NAME ),
  tagName: 'span',
  className: 'badges',
  edit( { isActive, value, onChange } ) {

    var cursolPositionObject = value.formats[value.start];
    var type = cursolPositionObject ? cursolPositionObject[0].type : '';

    return (
      <Fragment>
        <BlockControls>
          <Toolbar>
            <DropdownMenu
              icon={ <FontAwesomeIcon icon="tag" /> }
              label={__( 'バッジ', THEME_NAME )}
              className='badges'
              controls={ [
                  {
                      title: <span className="badge">{__( 'オレンジ', THEME_NAME )}</span>,
                      icon: 'tag',
                      isActive: type === 'cocoon-blocks/badge',
                      onClick: () => onChange( toggleFormat( value, { type: 'cocoon-blocks/badge' } ) )
                  },
                  {
                      title: <span className="badge-red">{__( '赤色', THEME_NAME )}</span>,
                      icon: 'tag',
                      isActive: type === 'cocoon-blocks/badge-red',
                      onClick: () => onChange( toggleFormat( value, { type: 'cocoon-blocks/badge-red' } ) )
                  },
                  {
                      title: <span className="badge-pink">{__( 'ピンク', THEME_NAME )}</span>,
                      icon: 'tag',
                      isActive: type === 'cocoon-blocks/badge-pink',
                      onClick: () => onChange( toggleFormat( value, { type: 'cocoon-blocks/badge-pink' } ) )
                  },
                  {
                      title: <span className="badge-purple">{__( '紫色', THEME_NAME )}</span>,
                      icon: 'tag',
                      isActive: type === 'cocoon-blocks/badge-purple',
                      onClick: () => onChange( toggleFormat( value, { type: 'cocoon-blocks/badge-purple' } ) )
                  },
                  {
                      title: <span className="badge-blue">{__( '青色', THEME_NAME )}</span>,
                      icon: 'tag',
                      isActive: type === 'cocoon-blocks/badge-blue',
                      onClick: () => onChange( toggleFormat( value, { type: 'cocoon-blocks/badge-blue' } ) )
                  },
                  {
                      title: <span className="badge-green">{__( '緑色', THEME_NAME )}</span>,
                      icon: 'tag',
                      isActive: type === 'cocoon-blocks/badge-green',
                      onClick: () => onChange( toggleFormat( value, { type: 'cocoon-blocks/badge-green' } ) )
                  },
                  {
                      title: <span className="badge-yellow">{__( '黄色', THEME_NAME )}</span>,
                      icon: 'tag',
                      isActive: type === 'cocoon-blocks/badge-yellow',
                      onClick: () => onChange( toggleFormat( value, { type: 'cocoon-blocks/badge-yellow' } ) )
                  },
                  {
                      title: <span className="badge-brown">{__( '茶色', THEME_NAME )}</span>,
                      icon: 'tag',
                      isActive: type === 'cocoon-blocks/badge-brown',
                      onClick: () => onChange( toggleFormat( value, { type: 'cocoon-blocks/badge-brown' } ) )
                  },
                  {
                      title: <span className="badge-grey">{__( '灰色', THEME_NAME )}</span>,
                      icon: 'tag',
                      isActive: type === 'cocoon-blocks/badge-grey',
                      onClick: () => onChange( toggleFormat( value, { type: 'cocoon-blocks/badge-grey' } ) )
                  },
              ] }
          />
          </Toolbar>
        </BlockControls>
      </Fragment>
    );
  },
} );