/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, FontSizeToolbarButton } from '../helpers.js';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { registerFormatType, toggleFormat } from '@wordpress/rich-text';
import { BlockFormatControls } from '@wordpress/block-editor';
import { Slot, Toolbar, DropdownMenu } from '@wordpress/components';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Icon, edit } from '@wordpress/icons'
import { orderBy } from 'lodash';
const FORMAT_TYPE_NAME = 'cocoon-blocks/font-sizes';

var sizes = [12, 14, 16, 18, 20, 22, 24, 28, 32, 36, 40, 44, 48];
sizes.map((size, index) => {
  var name = size + 'px';
  var clss = 'fz-' + name;
  var formatType = 'cocoon-blocks/' + clss;
  registerFormatType( formatType, {
    title: clss,
    tagName: 'span',
    className: clss,
    edit({isActive, value, onChange}){
      const onToggle = () => onChange(toggleFormat(value,{type:formatType}));

      return (
        <Fragment>
          <FontSizeToolbarButton
          icon={<Icon icon={edit} size={32} />}
            title={<span className={clss}>{name}</span>}
            onClick={ onToggle }
            isActive={ isActive }
          />
        </Fragment>
      );
    }
  } );
});

//ドロップダウン
var isFontSizeVisible = Number(gbSettings['isFontSizeVisible'] ? gbSettings['isFontSizeVisible'] : 0);
if (isFontSizeVisible) {
  registerFormatType( FORMAT_TYPE_NAME, {
    title: __( 'フォントサイズ', THEME_NAME ),
    tagName: 'span',
    className: 'font-sizes',
    edit({isActive, value, onChange}){

      return (
        <BlockFormatControls>
          <div className="editor-format-toolbar block-editor-format-toolbar">
            <Toolbar>
              <Slot name="FontSize.ToolbarControls">
                { ( fills ) => fills.length !== 0 &&
                  <DropdownMenu
                    icon={ <FontAwesomeIcon icon="text-height" /> }
                    label={__( 'フォントサイズ', THEME_NAME )}
                    className='font-sizes'
                    controls={ orderBy( fills.map( ( [ { props } ] ) => props ), 'title' ) }
                  />
                }
              </Slot>
            </Toolbar>
          </div>
        </BlockFormatControls>
      );
    }
  } );
}
