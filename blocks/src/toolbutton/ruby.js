/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, LetterToolbarButton } from '../helpers.js';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
const { Fragment } = wp.element;
const { __ } = wp.i18n;
const { toggleFormat, registerFormatType, insert, applyFormat } = wp.richText;
const { RichTextToolbarButton, RichTextShortcut } = wp.editor;
const { SVG, Path } = wp.components;


var isRubyVisible = Number(gbSettings['isRubyVisible'] ? gbSettings['isRubyVisible'] : 0);

if (isRubyVisible) {
  registerFormatType( 'cocoon-blocks/rt', {
    title: __( 'ふりがな（ルビ）キャラクター', THEME_NAME ),
    tagName: 'rt',
    className: null,

    edit( {isActive, value, onChange} ) {
      return <Fragment></Fragment>;
    }

  } );

  registerFormatType( 'cocoon-blocks/ruby', {
    title: __( 'ふりがな（ルビ）', THEME_NAME ),
    tagName: 'ruby',
    className: null,

    edit ({ isActive, value, onChange }) {

      const onToggle = () => {
        let ruby = '';
        if ( ! isActive ) {
          ruby = window.prompt( __( 'ふりがな（ルビ）を入力してください。', THEME_NAME ) ) || value.text.substr( value.start, value.end -value.start );
          const rubyEnd   = value.end;
          const rubyStart = value.start;
          value = insert( value, ruby, rubyEnd );
          value.start = rubyStart;
          value.end   = rubyEnd + ruby.length;
          value = applyFormat( value, {
            type: 'cocoon-blocks/ruby'
          }, rubyStart, rubyEnd + ruby.length );
          value = applyFormat( value, {
            type: 'cocoon-blocks/rt'
          }, rubyEnd, rubyEnd + ruby.length );
        } else {
          value = toggleFormat( value, {
            type: 'cocoon-blocks/ruby'
          } );
        }
        return onChange( value );
      };

      // @see keycodes/src/index.js
      const shortcutType = 'primaryShift';
      const shortcutCharacter ='r';
      //const icon = (<FontAwesomeIcon icon={['fas', 'ellipsis-h']} />);
      return (
        <Fragment>
          <RichTextShortcut type={shortcutType} character={shortcutCharacter} onUse={onToggle}  />
          <RichTextToolbarButton icon={<FontAwesomeIcon icon={['fas', 'ellipsis-h']} />} title={__( 'ふりがな（ルビ）', THEME_NAME )} onClick={onToggle}
                                 isActive={isActive} shorcutType={shortcutType} shorcutCharacter={shortcutCharacter} />
        </Fragment>
      )
    }
  } );
}

