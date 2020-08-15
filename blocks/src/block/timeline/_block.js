/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, BLOCK_CLASS} from '../../helpers';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { RichText, InnerBlocks, InspectorControls } = wp.editor;
const { PanelBody, RangeControl } = wp.components;
const { Fragment } = wp.element;
import memoize from 'memize';
import { times } from 'lodash';

//this.activateMode is not a function対策
//https://wpdevelopment.courses/how-to-fix-activatemode-is-not-a-function-error-in-gutenberg/
window.lodash = _.noConflict();

const ALLOWED_BLOCKS = [ 'cocoon-blocks/timeline-item' ];


//左カラム
registerBlockType( 'cocoon-blocks/timeline-item', {

  title: __( 'タイムラインアイテム', THEME_NAME ),
  parent: [
    'cocoon-blocks/timeline',
  ],
  icon: <FontAwesomeIcon icon={['far', 'square']} />,
  category: THEME_NAME + '-block',
  description: __( 'カラム左側に表示される内容内容を入力。', THEME_NAME ),

  attributes: {
    label: {
      type: 'string',
      default: __( 'ラベル', THEME_NAME ),
    },
    title: {
      type: 'string',
      default: __( 'タイトル', THEME_NAME ),
    },
  },
  supports: {
    inserter: false,
  },

  edit( { attributes, setAttributes } ) {
    const { title, label } = attributes;
    return (
      <Fragment>
        <li className="timeline-item cf">
          <div className="timeline-item-label">
            <RichText
              value={ label }
              onChange={ ( value ) => setAttributes( { label: value } ) }
              placeholder={ __( 'ラベル', THEME_NAME ) }
            />
          </div>
          <div className="timeline-item-content cf">
            <div className="timeline-item-title">
              <RichText
                value={ title }
                onChange={ ( value ) => setAttributes( { title: value } ) }
                placeholder={ __( 'タイトル', THEME_NAME ) }
              />
            </div>
            <div className="timeline-item-snippet">
              <InnerBlocks templateLock={ false } />
            </div>
          </div>
        </li>
      </Fragment>
    );
  },

  save( { attributes } ) {
    const { title, label } = attributes;
    return (
      <li className="timeline-item cf">
        <div className="timeline-item-label">
          <RichText.Content
            value={ label }
          />
        </div>
        <div className="timeline-item-content cf">
          <div className="timeline-item-title">
            <RichText.Content
              value={ title }
            />
          </div>
          <div className="timeline-item-snippet">
            <InnerBlocks.Content />
          </div>
        </div>
      </li>
    );
  }
} );


const getItemsTemplate = memoize( ( items ) => {
  return times( items, () => [ 'cocoon-blocks/timeline-item' ] );
} );

//classの取得
function getClasses() {
  const classes = classnames(
    {
      [ 'timeline-box' ]: true,
      [ 'cf' ]: true,
      [ 'block-box' ]: true,
    }
  );
  return classes;
}

//タイムライン
registerBlockType( 'cocoon-blocks/timeline', {

  title: __( 'タイムライン', THEME_NAME ),
  icon: <FontAwesomeIcon icon={['far', 'clock']} />,
  category: THEME_NAME + '-block',
  description: __( '時系列を表現するためのブロックです。', THEME_NAME ),
  keywords: [ 'timeline', 'box' ],

  attributes: {
    title: {
      type: 'string',
      default: __( 'タイムラインのタイトル', THEME_NAME ),
    },
    items: {
      type: 'integer',
      default: 1,
    },
  },

  edit( { attributes, setAttributes } ) {
    const { title, items } = attributes;
    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>
          <RangeControl
            label={ __( 'アイテム数' ) }
            value={ items }
            onChange={ ( value ) => setAttributes( { items: value } ) }
            min={ 1 }
            max={ 30 }
          />
          </PanelBody>
        </InspectorControls>
        <div className={ getClasses() }>
          <div class="timeline-title">
            <RichText
              value={ title }
              onChange={ ( value ) => setAttributes( { title: value } ) }
              placeholder={ __( 'タイトル', THEME_NAME ) }
            />
          </div>
          <ul className="timeline">
            <InnerBlocks
              template={ getItemsTemplate( items ) }
              templateLock="all"
              allowedBlocks={ ALLOWED_BLOCKS }
            />
          </ul>
        </div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    const { title, items } = attributes;
    return (
      <div className={ getClasses() }>
        <div class="timeline-title">
          <RichText.Content
              value={ title }
          />
        </div>
        <ul className="timeline">
          <InnerBlocks.Content />
        </ul>
      </div>
    );
  }
} );