import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import {
  InspectorControls,
  InnerBlocks,
  withColors,
  PanelColorSettings,
  withFontSizes,
  useBlockProps,
} from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import classnames from 'classnames';

export function BlankBoxEdit( props ) {
  const {
    attributes,
    className,
    backgroundColor,
    setBackgroundColor,
    textColor,
    setTextColor,
    borderColor,
    setBorderColor,
    fontSize,
  } = props;

  const { customBackgroundColor, customTextColor, customBorderColor } =
    attributes;

  const classes = classnames( className, {
    'blank-box': true,
    'block-box': true,
    'has-text-color': textColor.color,
    'has-background': backgroundColor.color,
    'has-border-color': borderColor.color,
    [ backgroundColor.class ]: backgroundColor.class,
    [ textColor.class ]: textColor.class,
    [ borderColor.class ]: borderColor.class,
    [ fontSize.class ]: fontSize.class,
  } );

  const styles = {
    '--cocoon-custom-border-color': customBorderColor || undefined,
    '--cocoon-custom-background-color': customBackgroundColor || undefined,
    '--cocoon-custom-text-color': customTextColor || undefined,
  };

  const blockProps = useBlockProps( {
    className: classes,
    style: styles,
  } );

  return (
    <Fragment>
      <InspectorControls>
        <PanelColorSettings
          title={ __( '色設定', THEME_NAME ) }
          enableAlpha={true}
          colorSettings={ [
            {
              label: __( 'ボーダー色', THEME_NAME ),
              onChange: setBorderColor,
              value: borderColor.color,
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
          ] }
          __experimentalIsRenderedInSidebar={ true }
        />
      </InspectorControls>

      <div { ...blockProps }>
        <InnerBlocks />
      </div>
    </Fragment>
  );
}

export default compose( [
  withColors( 'backgroundColor', {
    textColor: 'color',
    borderColor: 'border-color',
    iconColor: 'icon-color',
  } ),
  withFontSizes( 'fontSize' ),
] )( BlankBoxEdit );
