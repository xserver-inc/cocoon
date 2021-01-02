/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, MarkerToolbarButton } from '../helpers.js';
const { Fragment } = wp.element;
const { __ } = wp.i18n;
const { registerFormatType, toggleFormat } = wp.richText;
const { RichTextShortcut, RichTextToolbarButton } = wp.editor;
const FORMAT_TYPE_NAME = 'cocoon-blocks/marker-under-blue';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
const TITLE = __( '青色アンダーラインマーカー', THEME_NAME );

registerFormatType( FORMAT_TYPE_NAME, {
  title: TITLE,
  tagName: 'span',
  className: 'marker-under-blue',
  edit({isActive, value, onChange}){
    // console.log(isActive);
    const onToggle = () => onChange(toggleFormat(value,{type:FORMAT_TYPE_NAME}));

    return (
      <Fragment>
        <MarkerToolbarButton
          icon={<FontAwesomeIcon icon="window-minimize" />}
          title={<span className="marker-under-blue">{TITLE}</span>}
          onClick={ onToggle }
          isActive={ isActive }
        />
      </Fragment>
    );
  }
} );
