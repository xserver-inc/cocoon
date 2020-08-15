/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, AffiliateToolbarButton } from '../helpers.js';
const { Fragment } = wp.element;
const { __ } = wp.i18n;
const { registerFormatType, insert } = wp.richText;
const { BlockFormatControls } = wp.editor;
const { Slot, Toolbar, DropdownMenu } = wp.components;
const FORMAT_TYPE_NAME = 'cocoon-blocks/affiliates';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { orderBy } from 'lodash';

var isAffiliateVisible = Number(gbSettings['isAffiliateVisible'] ? gbSettings['isAffiliateVisible'] : 0);
if (isAffiliateVisible) {
  gbAffiliateTags.map((affi, index) => {
    var name = 'affiliate-' + affi.id;
    var title = affi.title;
    var formatType = 'cocoon-blocks/' + name;
    if (affi.visible == '1') {
      registerFormatType( formatType, {
        title: title,
        tagName: name,
        className: null,
        edit({value, onChange}){
          const onToggle = () => onChange( insert( value, '[affi id=' + affi.id + ']', value.start, value.end ) );

          return (
            <Fragment>
              <AffiliateToolbarButton
                icon={'editor-code'}
                title={<span className={name}>{title}</span>}
                onClick={ onToggle }
              />
            </Fragment>
          );
        }
      } );
    }

  });

  registerFormatType( FORMAT_TYPE_NAME, {
    title: __( 'アフィリエイト', THEME_NAME ),
    tagName: 'span',
    className: 'affiliates',
    edit({isActive, value, onChange}){

      return (
        <BlockFormatControls>
          <div className="editor-format-toolbar block-editor-format-toolbar">
            <Toolbar>
              <Slot name="Affiliate.ToolbarControls">
                { ( fills ) => fills.length !== 0 &&
                  <DropdownMenu
                    icon={<FontAwesomeIcon icon={['fas', 'dollar-sign']} />}
                    label={__( 'アフィリエイトタグ', THEME_NAME )}
                    className='affiliates'
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
