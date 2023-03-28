/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, AffiliateToolbarButton } from '../helpers.js';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { registerFormatType, insert } from '@wordpress/rich-text';
import { BlockFormatControls } from '@wordpress/block-editor';
import { Slot, ToolbarGroup, ToolbarDropdownMenu } from '@wordpress/components';
import { Icon, code, currencyDollar } from '@wordpress/icons';
import { orderBy } from 'lodash';
const FORMAT_TYPE_NAME = 'cocoon-blocks/affiliates';

var isAffiliateVisible = Number(
  gbSettings[ 'isAffiliateVisible' ] ? gbSettings[ 'isAffiliateVisible' ] : 0
);
if ( isAffiliateVisible ) {
  gbAffiliateTags.map( ( affi, index ) => {
    var name = 'affiliate-' + affi.id;
    var title = affi.title;
    var formatType = 'cocoon-blocks/' + name;
    if ( affi.visible == '1' ) {
      registerFormatType( formatType, {
        title: title,
        tagName: name,
        className: null,
        edit( { value, onChange } ) {
          const onToggle = () =>
            onChange(
              insert(
                value,
                '[affi id=' + affi.id + ']',
                value.start,
                value.end
              )
            );

          return (
            <Fragment>
              <AffiliateToolbarButton
                icon={ <Icon icon={ code } size={ 32 } /> }
                title={ <span className={ name }>{ title }</span> }
                onClick={ onToggle }
              />
            </Fragment>
          );
        },
      } );
    }
  } );

  registerFormatType( FORMAT_TYPE_NAME, {
    title: __( 'アフィリエイト', THEME_NAME ),
    tagName: 'span',
    className: 'affiliates',
    edit( { isActive, value, onChange } ) {
      return (
        <BlockFormatControls>
          <div className="editor-format-toolbar block-editor-format-toolbar">
            <ToolbarGroup>
              <Slot name="Affiliate.ToolbarControls">
                { ( fills ) =>
                  fills.length !== 0 && (
                    <ToolbarDropdownMenu
                      icon={ <Icon icon={ currencyDollar } size={ 32 } /> }
                      label={ __( 'アフィリエイトタグ', THEME_NAME ) }
                      className="affiliates"
                      controls={ orderBy(
                        fills.map( ( [ { props } ] ) => props ),
                        'title'
                      ) }
                    />
                  )
                }
              </Slot>
            </ToolbarGroup>
          </div>
        </BlockFormatControls>
      );
    },
  } );
}
