/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
import { CLICK_POINT_MSG } from '../../helpers';
import {
  isLegacyStyle,
  migrateLegacyStyleAttribute,
} from '../../style-attribute-compat';
import classnames from 'classnames';

import {
  InnerBlocks,
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';
const DEFAULT_BALLOON_STYLE = 'stn';

// WordPress 7.0で破損が起きる直前の共通属性を、現行block.jsonから独立して保持します。
const LEGACY_BALLOON_ATTRIBUTES = {
  name: {
    type: 'string',
    default: '',
  },
  index: {
    type: 'string',
    default: '0',
  },
  id: {
    type: 'string',
    default: '',
  },
  icon: {
    type: 'string',
    default: '',
  },
  position: {
    type: 'string',
    default: 'l',
  },
  iconstyle: {
    type: 'string',
    default: 'cb',
  },
  iconid: {
    type: 'number',
    default: 0,
  },
  backgroundColor: {
    type: 'string',
  },
  textColor: {
    type: 'string',
  },
  borderColor: {
    type: 'string',
  },
  customBackgroundColor: {
    type: 'string',
  },
  customTextColor: {
    type: 'string',
  },
  customBorderColor: {
    type: 'string',
  },
  fontSize: {
    type: 'string',
  },
  notNestedStyle: {
    type: 'boolean',
    default: true,
  },
  backgroundColorValue: {
    type: 'string',
  },
  textColorValue: {
    type: 'string',
  },
  borderColorValue: {
    type: 'string',
  },
};

// WordPress 7.0の文字列・破損オブジェクトを同じdeprecatedで読める当時の属性形状です。
const LEGACY_STYLE_ATTRIBUTES = {
  ...LEGACY_BALLOON_ATTRIBUTES,
  style: {
    type: [ 'string', 'object' ],
    default: DEFAULT_BALLOON_STYLE,
  },
};

// v2保存関数が評価していた旧style文字列の属性形状を固定します。
const LEGACY_V2_ATTRIBUTES = {
  ...LEGACY_BALLOON_ATTRIBUTES,
  style: {
    type: 'string',
    default: DEFAULT_BALLOON_STYLE,
  },
};

// deprecatedの検証条件が将来のsupports変更に追随しないよう、当時の値を固定します。
const LEGACY_STYLE_SUPPORTS = {
  anchor: true,
  html: false,
};

// deprecatedのHTMLを将来の共通ヘルパー変更から守るため、当時の吹き出しクラス生成を固定します。
const getLegacyBalloonClasses = ( id, style, position, iconstyle ) =>
  classnames( {
    'speech-wrap': true,
    [ `sb-id-${ id }` ]: !! id,
    [ `sbs-${ style }` ]: !! style,
    [ `sbp-${ position }` ]: !! position,
    [ `sbis-${ iconstyle }` ]: !! iconstyle,
    cf: true,
    'block-box': true,
  } );

// v1保存時の色クラスを将来のパレット変更から守るため、当時の対応表を固定します。
const LEGACY_COLOR_SLUGS = {
  '#e60033': 'red',
  '#e95295': 'pink',
  '#884898': 'purple',
  '#55295b': 'deep',
  '#1e50a2': 'indigo',
  '#0095d9': 'blue',
  '#2ca9e1': 'light-blue',
  '#00a3af': 'cyan',
  '#007b43': 'teal',
  '#3eb370': 'green',
  '#8bc34a': 'light-green',
  '#c3d825': 'lime',
  '#ffd900': 'yellow',
  '#ffc107': 'amber',
  '#f39800': 'orange',
  '#ea5506': 'deep-orange',
  '#954e2a': 'brown',
  '#dddddd': 'light-grey',
  '#ddd': 'light-grey',
  '#949495': 'grey',
  '#666666': 'dark-grey',
  '#666': 'dark-grey',
  '#333333': 'black',
  '#333': 'black',
  '#ffffff': 'white',
  '#fff': 'white',
};

// テーマのキーカラーだけは記事作成時と同じ動的値を優先し、それ以外を固定表から解決します。
const getLegacyColorSlug = ( color ) => {
  const legacyGbColors = window.gbColors;
  const legacyKeyColor =
    legacyGbColors !== undefined && legacyGbColors !== null
      ? legacyGbColors.keyColor
      : '#19448e';

  if ( color === legacyKeyColor ) {
    return 'key-color';
  }

  return Object.prototype.hasOwnProperty.call( LEGACY_COLOR_SLUGS, color )
    ? LEGACY_COLOR_SLUGS[ color ]
    : undefined;
};

// 現行直前のHTMLを再現し、正常な旧記事とWordPress 7.0で壊れた記事の両方を検出します。
function saveLegacyStyle( { attributes } ) {
  const {
    name,
    id,
    icon,
    style,
    position,
    iconstyle,
    backgroundColor,
    textColor,
    borderColor,
    customBackgroundColor,
    customTextColor,
    customBorderColor,
    fontSize,
    notNestedStyle,
    backgroundColorValue,
    textColorValue,
    borderColorValue,
  } = attributes;

  const backgroundClass = getColorClassName(
    'background-color',
    backgroundColor
  );
  const textClass = getColorClassName( 'color', textColor );
  const borderClass = getColorClassName( 'border-color', borderColor );
  const fontSizeClass = getFontSizeClass( fontSize );

  const classes = classnames(
    getLegacyBalloonClasses( id, style, position, iconstyle ),
    {
      'not-nested-style': notNestedStyle,
      'cocoon-block-balloon': true,
    }
  );

  const styles = {
    '--cocoon-custom-background-color': customBackgroundColor || undefined,
    '--cocoon-custom-text-color': customTextColor || undefined,
    '--cocoon-custom-border-color': customBorderColor || undefined,
  };

  if ( notNestedStyle ) {
    styles[ '--cocoon-custom-border-color' ] = borderColorValue;
    styles[ '--cocoon-custom-background-color' ] = backgroundColorValue;
    styles[ '--cocoon-custom-text-color' ] = textColorValue;
  }

  const blockProps = useBlockProps.save( {
    className: classes,
    style: styles,
  } );

  return (
    <div { ...blockProps }>
      <div className="speech-person">
        <figure className="speech-icon">
          <img src={ icon } alt={ name } className="speech-icon-image" />
        </figure>
        <div className="speech-name">
          <RichText.Content value={ name } />
        </div>
      </div>
      <div
        className={ classnames( {
          'speech-balloon': true,
          'has-text-color': textColor || customTextColor,
          'has-background': backgroundColor || customBackgroundColor,
          'has-border-color': borderColor || customBorderColor,
          [ textClass ]: textClass,
          [ backgroundClass ]: backgroundClass,
          [ borderClass ]: borderClass,
          [ fontSizeClass ]: fontSizeClass,
        } ) }
      >
        <InnerBlocks.Content />
      </div>
    </div>
  );
}

// どの旧形式からでも、deprecatedを経由せず現行属性へ直接移行します。
const migrateStyle = ( attributes ) =>
  migrateLegacyStyleAttribute(
    attributes,
    'balloonStyle',
    DEFAULT_BALLOON_STYLE
  );

const legacyStyle = {
  apiVersion: 3,
  supports: LEGACY_STYLE_SUPPORTS,
  attributes: LEGACY_STYLE_ATTRIBUTES,
  isEligible: isLegacyStyle,
  migrate: migrateStyle,
  save: saveLegacyStyle,
};

const v1 = {
  attributes: {
    content: {
      type: 'string',
      default: CLICK_POINT_MSG,
    },
    borderColor: {
      type: 'string',
      default: '',
    },
  },
  migrate( attributes ) {
    const { content, borderColor } = attributes;

    return {
      content,
      backgroundColor: undefined,
      customBackgroundColor: undefined,
      textColor: undefined,
      customTextColor: undefined,
      borderColor: getLegacyColorSlug( borderColor ),
      customBorderColor: undefined,
      fontSize: undefined,
      customFontSize: undefined,
      notNestedStyle: false,
      balloonStyle: DEFAULT_BALLOON_STYLE,
    };
  },

  save( { attributes } ) {
    const { borderColor } = attributes;
    const classes = classnames( {
      'blank-box': true,
      [ `bb-${ getLegacyColorSlug( borderColor ) }` ]:
        !! getLegacyColorSlug( borderColor ),
      'block-box': true,
    } );
    return (
      <div className={ classes }>
        <InnerBlocks.Content />
      </div>
    );
  },
};

const v2 = {
  apiVersion: 2,
  supports: {
    anchor: true,
  },
  attributes: LEGACY_V2_ATTRIBUTES,
  migrate( attributes ) {
    return {
      ...migrateStyle( attributes ),
      notNestedStyle: false,
    };
  },
  save( { attributes } ) {
    const {
      name,
      id,
      icon,
      style,
      position,
      iconstyle,
      backgroundColor,
      textColor,
      borderColor,
      customBorderColor,
      fontSize,
    } = attributes;

    const backgroundClass = getColorClassName(
      'background-color',
      backgroundColor
    );
    const textClass = getColorClassName( 'color', textColor );
    const borderClass = getColorClassName( 'border-color', borderColor );
    const fontSizeClass = getFontSizeClass( fontSize );

    const classes = getLegacyBalloonClasses( id, style, position, iconstyle );
    const blockProps = useBlockProps.save( {
      className: classes,
    } );

    return (
      <div { ...blockProps }>
        <div className="speech-person">
          <figure className="speech-icon">
            <img src={ icon } alt={ name } className="speech-icon-image" />
          </figure>
          <div className="speech-name">
            <RichText.Content value={ name } />
          </div>
        </div>
        <div
          className={ classnames( {
            'speech-balloon': true,
            'has-text-color': textColor,
            'has-background': backgroundColor,
            'has-border-color': borderColor || customBorderColor,
            [ textClass ]: textClass,
            [ backgroundClass ]: backgroundClass,
            [ borderClass ]: borderClass,
            [ fontSizeClass ]: fontSizeClass,
          } ) }
        >
          <InnerBlocks.Content />
        </div>
      </div>
    );
  },
};

const v3 = {
  apiVersion: 2,
  supports: {
    anchor: true,
  },
  attributes: {
    name: {
      type: 'string',
      default: '',
    },
    index: {
      type: 'string',
      default: '0',
    },
    id: {
      type: 'string',
      default: '',
    },
    icon: {
      type: 'string',
      default: '',
    },
    style: {
      type: 'string',
      default: 'stn',
    },
    position: {
      type: 'string',
      default: 'l',
    },
    iconstyle: {
      type: 'string',
      default: 'cb',
    },
    iconid: {
      type: 'number',
      default: 0,
    },
    backgroundColor: {
      type: 'string',
    },
    textColor: {
      type: 'string',
    },
    borderColor: {
      type: 'string',
    },
    customBackgroundColor: {
      type: 'string',
    },
    customTextColor: {
      type: 'string',
    },
    customBorderColor: {
      type: 'string',
    },
    fontSize: {
      type: 'string',
    },
  },
  migrate( attributes ) {
    return {
      ...migrateStyle( attributes ),
      notNestedStyle: false,
    };
  },
  save( { attributes } ) {
    const {
      name,
      id,
      icon,
      style,
      position,
      iconstyle,
      backgroundColor,
      textColor,
      borderColor,
      customBackgroundColor,
      customTextColor,
      customBorderColor,
      fontSize,
    } = attributes;

    const backgroundClass = getColorClassName(
      'background-color',
      backgroundColor
    );
    const textClass = getColorClassName( 'color', textColor );
    const borderClass = getColorClassName( 'border-color', borderColor );
    const fontSizeClass = getFontSizeClass( fontSize );

    const classes = getLegacyBalloonClasses( id, style, position, iconstyle );

    const styles = {
      '--cocoon-custom-background-color': customBackgroundColor || undefined,
      '--cocoon-custom-text-color': customTextColor || undefined,
      '--cocoon-custom-border-color': customBorderColor || undefined,
    };

    const blockProps = useBlockProps.save( {
      className: classes,
      style: styles,
    } );

    return (
      <div { ...blockProps }>
        <div className="speech-person">
          <figure className="speech-icon">
            <img src={ icon } alt={ name } className="speech-icon-image" />
          </figure>
          <div className="speech-name">
            <RichText.Content value={ name } />
          </div>
        </div>
        <div
          className={ classnames( {
            'speech-balloon': true,
            'has-text-color': textColor || customTextColor,
            'has-background': backgroundColor || customBackgroundColor,
            'has-border-color': borderColor || customBorderColor,
            [ textClass ]: textClass,
            [ backgroundClass ]: backgroundClass,
            [ borderClass ]: borderClass,
            [ fontSizeClass ]: fontSizeClass,
          } ) }
        >
          <InnerBlocks.Content />
        </div>
      </div>
    );
  },
};

export default [ legacyStyle, v3, v2, v1 ];
