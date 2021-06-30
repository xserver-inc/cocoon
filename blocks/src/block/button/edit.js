
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
  TextControl,
  ToggleControl,
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import classnames from 'classnames';

export function ButtonEdit( props ) {
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
    size,
    url,
    target,
    isCircle,
    isShine,
  } = attributes;

  const classes = classnames( {
    [BUTTON_BLOCK]: true,
  } );
  const blockProps = useBlockProps(className, {
    className: classes,
  });

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={ __( 'ボタン設定', THEME_NAME ) }>

          <TextControl
            label={ __( 'URL', THEME_NAME ) }
            value={ url }
            onChange={ ( value ) => setAttributes( { url: value } ) }
          />

          <SelectControl
            label={ __( 'リンクの開き方', THEME_NAME ) }
            value={ target }
            onChange={ ( value ) => setAttributes( { target: value } ) }
            options={ [
              {
                value: '_self',
                label: __( '現在のタブで開く', THEME_NAME ),
              },
              {
                value: '_blank',
                label: __( '新しいタブで開く', THEME_NAME ),
              },
            ] }
          />

          <SelectControl
            label={ __( 'サイズ', THEME_NAME ) }
            value={ size }
            onChange={ ( value ) => setAttributes( { size: value } ) }
            options={ [
              {
                value: 'btn-s',
                label: __( '小', THEME_NAME ),
              },
              {
                value: 'btn-m',
                label: __( '中', THEME_NAME ),
              },
              {
                value: 'btn-l',
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

      <div { ...blockProps }>
        <span
          className={classnames(className, {
            'btn': true,
            [ size ]: size,
            [ 'btn-circle' ]: !! isCircle,
            [ 'btn-shine' ]: !! isShine,
            'has-text-color': textColor.color,
            'has-background': backgroundColor.color,
            'has-border-color': borderColor.color,
            [backgroundColor.class]: backgroundColor.class,
            [textColor.class]: textColor.class,
            [borderColor.class]: borderColor.class,
            [fontSize.class]: fontSize.class,
          })}
          href={ url }
          target={ target }
        >
          <RichText
            value={ content }
            onChange={ ( value ) => setAttributes( { content: value } ) }
          />
        </span>
      </div>

    </Fragment>
  );
}

export default compose([
  withColors('backgroundColor', {textColor: 'color', borderColor: 'border-color'}),
  withFontSizes('fontSize'),
])(ButtonEdit);
