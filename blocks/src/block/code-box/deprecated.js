/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
import { THEME_NAME, BUTTON_BLOCK } from '../../helpers';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { InnerBlocks } = wp.editor;

const { createBlock } = wp.blocks;

export const deprecated = [];