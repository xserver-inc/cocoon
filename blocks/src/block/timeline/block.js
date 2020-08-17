/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, LIST_ICONS } from '../../helpers';
import { deprecated } from './deprecated';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';

const { __ } = wp.i18n;
const {
  registerBlockType,
} = wp.blocks;
const {
  InspectorControls,
  InnerBlocks,
  RichText,
  withColors,
  getColorClassName,
  PanelColorSettings,
  getFontSizeClass,
  withFontSizes,
  FontSizePicker,
  ContrastChecker,
} = wp.editor;
const {
  PanelBody,
  PanelColor,
  ColorPalette,
  SelectControl,
  BaseControl,
  RangeControl,
  withFallbackStyles,
} = wp.components;

const {
  Component,
  Fragment,
} = wp.element;

const {
  compose
} = wp.compose;


import memoize from 'memize';
import { times } from 'lodash';

//this.activateMode is not a function対策
//https://wpdevelopment.courses/how-to-fix-activatemode-is-not-a-function-error-in-gutenberg/
window.lodash = _.noConflict();

const ALLOWED_BLOCKS = [ 'cocoon-blocks/timeline-item' ];


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





const FallbackStyles = withFallbackStyles((node, ownProps) => {
  const {
    textColor,
    backgroundColor,
    borderColor,
    pointColor,
    fontSize,
    customFontSize,
  } = ownProps.attributes;
  const editableNode = node.querySelector('[contenteditable="true"]');
  //verify if editableNode is available, before using getComputedStyle.
  const computedStyles = editableNode ? getComputedStyle(editableNode) : null;
  return {
    fallbackBackgroundColor: backgroundColor || !computedStyles ? undefined : computedStyles.backgroundColor,
    fallbackTextColor: textColor || !computedStyles ? undefined : computedStyles.color,
    fallbackBorderColor: borderColor || !computedStyles ? undefined : computedStyles.color,
    fallbackPointColor: pointColor || !computedStyles ? undefined : computedStyles.color,
    fallbackFontSize: fontSize || customFontSize || !computedStyles ? undefined : parseInt( computedStyles.fontSize ) || undefined,
  }
});

const getItemsTemplate = memoize( ( items ) => {
  return times( items, () => [ 'cocoon-blocks/timeline-item' ] );
} );


class CocoonTimelineBlock extends Component {
  constructor() {
    super(...arguments);
  }

  render() {
    const {
      attributes,
      setAttributes,
      mergeBlocks,
      onReplace,
      className,
      backgroundColor,
      setBackgroundColor,
      textColor,
      setTextColor,
      borderColor,
      setBorderColor,
      pointColor,
      setPointColor,
      fallbackBackgroundColor,
      fallbackTextColor,
      fallbackBorderColor,
      fallbackPointColor,
      fallbackFontSize,
      fontSize,
      setFontSize,
    } = this.props;

    const {
      title,
      items,
    } = attributes;

    return (
      <Fragment>
        <InspectorControls>

          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>
          <RangeControl
            label={ __( 'アイテム数' ) }
            value={ items }
            onChange={ ( value ) => setAttributes( { items: value } ) }
            min={ 1 }
            max={ 50 }
          />
          </PanelBody>

          <PanelColorSettings
            title={ __( '色設定', THEME_NAME ) }
            colorSettings={[
              {
                label: __( 'ポイント色', THEME_NAME ),
                onChange: setPointColor,
                value: pointColor.color,
              },
              {
                label: __( '背景色', THEME_NAME ),
                onChange: setBackgroundColor,
                value: backgroundColor.color,
              },
              {
                label: __( '文字色', THEME_NAME ),
                onChange: setTextColor,
                value: textColor.color,
              },
              {
                label: __( 'ボーダー色', THEME_NAME ),
                onChange: setBorderColor,
                value: borderColor.color,
              },
            ]}
          />
          {/*
          <PanelBody title={ __( '文字サイズ', THEME_NAME ) } className="blocks-font-size">
            <FontSizePicker
              fallbackFontSize={ fallbackFontSize }
              value={ fontSize.size }
              onChange={ setFontSize }
            />
          </PanelBody>
          */}
        </InspectorControls>

        <div className={
          classnames(className, {
            [ 'timeline-box' ]: true,
            [ 'cf' ]: true,
            [ 'block-box' ]: true,
            'has-text-color': textColor.color,
            'has-background': backgroundColor.color,
            'has-border-color': borderColor.color,
            'has-point-color': pointColor.color,
            [backgroundColor.class]: backgroundColor.class,
            [textColor.class]: textColor.class,
            [borderColor.class]: borderColor.class,
            [pointColor.class]: pointColor.class,
            [fontSize.class]: fontSize.class,
          })
         }>
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
  }
}

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
    backgroundColor: {
      type: 'string',
    },
    customBackgroundColor: {
      type: 'string',
    },
    textColor: {
      type: 'string',
    },
    customTextColor: {
      type: 'string',
    },
    borderColor: {
      type: 'string',
    },
    customBorderColor: {
      type: 'string',
    },
    pointColor: {
      type: 'string',
    },
    customPointColor: {
      type: 'string',
    },
    fontSize: {
      type: 'string',
    },
    customFontSize: {
      type: 'string',
    },
  },

  edit: compose([
    withColors('backgroundColor', {textColor: 'color', borderColor: 'border-color', pointColor: 'point-color'}),
    withFontSizes('fontSize'),
    FallbackStyles,
  ])(CocoonTimelineBlock),
  save: props => {
    const {
      title,
      items,
      backgroundColor,
      customBackgroundColor,
      textColor,
      customTextColor,
      borderColor,
      customBorderColor,
      pointColor,
      customPointColor,
      fontSize,
      customFontSize,
    } = props.attributes;

    const backgroundClass = getColorClassName( 'background-color', backgroundColor );
    const textClass = getColorClassName( 'color', textColor );
    const borderClass = getColorClassName( 'border-color', borderColor );
    const pointClass = getColorClassName( 'point-color', pointColor );
    const fontSizeClass = getFontSizeClass( fontSize );


    const className = classnames( {
      [ 'timeline-box' ]: true,
      [ 'cf' ]: true,
      [ 'block-box' ]: true,
      'has-text-color': textColor || customTextColor,
      'has-background': backgroundColor || customBackgroundColor,
      'has-border-color': borderColor || customBorderColor,
      'has-point-color': pointColor || customPointColor,
      [ textClass ]: textClass,
      [ backgroundClass ]: backgroundClass,
      [ borderClass ]: borderClass,
      [ pointClass ]: pointClass,
      [ fontSizeClass ]: fontSizeClass,
    } );

    return (
      <div className={ className }>
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
  },

  //deprecated: deprecated,
});