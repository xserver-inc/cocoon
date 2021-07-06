import { THEME_NAME, BUTTON_BLOCK } from '../../helpers';
import { __ } from '@wordpress/i18n';
import {
  InspectorControls,
  RichText,
  withColors,
  PanelColorSettings,
  withFontSizes,
  FontSizePicker,
  useBlockProps,
} from '@wordpress/block-editor';
import {
  PanelBody,
  SelectControl,
  TextareaControl,
  ToggleControl
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import classnames from 'classnames';

export function ButtonWrapEdit( props ) {
  const {
    attributes,
    setAttributes,
    className,
    backgroundColor,
    setBackgroundColor,
    textColor,
    setTextColor,
    borderColor,
    setBorderColor,
    fallbackFontSize,
    fontSize,
    setFontSize,
  } = props;

  const {
    content,
    tag,
    size,
    isCircle,
    isShine,
  } = attributes;

  const classes = classnames(className, {
    [ 'btn-wrap' ]: true,
    [ 'btn-wrap-block' ]: true,
    [ BUTTON_BLOCK ]: true,
    [ size ]: size,
    [ 'btn-wrap-circle' ]: !! isCircle,
    [ 'btn-wrap-shine' ]: !! isShine,
    'has-text-color': textColor.color,
    'has-background': backgroundColor.color,
    'has-border-color': borderColor.color,
    [backgroundColor.class]: backgroundColor.class,
    [textColor.class]: textColor.class,
    [borderColor.class]: borderColor.class,
    [fontSize.class]: fontSize.class,
  });
  const blockProps = useBlockProps({
    className: classes,
    dangerouslySetInnerHTML: {__html: tag},
  });

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={ __( '囲みボタン設定', THEME_NAME ) }>

          <TextareaControl
            label={ __( 'リンクタグ・ショートコード', THEME_NAME ) }
            value={ tag }
            onChange={ ( value ) => setAttributes( { tag: value } ) }
          />

          <SelectControl
            label={ __( 'サイズ', THEME_NAME ) }
            value={ size }
            onChange={ ( value ) => setAttributes( { size: value } ) }
            options={ [
              {
                value: 'btn-wrap-s',
                label: __( '小', THEME_NAME ),
              },
              {
                value: 'btn-wrap-m',
                label: __( '中', THEME_NAME ),
              },
              {
                value: 'btn-wrap-l',
                label: __( '大', THEME_NAME ),
              },
            ] }
          />

          <ToggleControl
            label={ __( '円形にする', THEME_NAME ) }
            checked={ isCircle }
            onChange={ ( value ) => setAttributes( { isCircle: value } ) }
          />

          <ToggleControl
            label={ __( '光らせる', THEME_NAME ) }
            checked={ isShine }
            onChange={ ( value ) => setAttributes( { isShine: value } ) }
          />

        </PanelBody>

        <PanelBody title={ __( '文字サイズ', THEME_NAME ) } className="blocks-font-size">
          <FontSizePicker
            fallbackFontSize={ fallbackFontSize }
            value={ fontSize.size }
            onChange={ setFontSize }
          />
        </PanelBody>

        <PanelColorSettings
          title={ __( '色設定', THEME_NAME ) }
          colorSettings={[
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
      </InspectorControls>

      <span className={'button-wrap-msg'}>
        <RichText
          value={ content }
        />
      </span>
      <div { ...blockProps }>
      </div>

    </Fragment>
  );
}

export default compose([
  withColors('backgroundColor', {textColor: 'color', borderColor: 'border-color'}),
  withFontSizes('fontSize'),
])(ButtonWrapEdit);