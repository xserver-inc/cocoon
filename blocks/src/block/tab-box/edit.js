import { THEME_NAME, LIST_ICONS } from '../../helpers';
import { __ } from '@wordpress/i18n';
import {
  InspectorControls,
  InnerBlocks,
  withColors,
  PanelColorSettings,
  withFontSizes,
  useBlockProps,
} from '@wordpress/block-editor';
import {
  PanelBody, SelectControl } from '@wordpress/components';
import { Component, Fragment } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import classnames from 'classnames';

const ALLOWED_BLOCKS = [ 'core/list' ];

export function TabBoxEdit( props ) {
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
    fontSize,
  } = props;

  const {
    label,
  } = attributes;

  const classes= classnames(className, {
      'blank-box': true,
      'bb-tab': true,
      [ label ]: !! label,
      'block-box': true,
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
  });

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>

          <SelectControl
            label={ __( 'ラベル', THEME_NAME ) }
            value={ label }
            onChange={ ( value ) => setAttributes( { label: value } ) }
            options={ [
              {
                value: 'bb-check',
                label: __( 'チェック', THEME_NAME ),
              },
              {
                value: 'bb-comment',
                label: __( 'コメント', THEME_NAME ),
              },
              {
                value: 'bb-point',
                label: __( 'ポイント', THEME_NAME ),
              },
              {
                value: 'bb-tips',
                label: __( 'ティップス', THEME_NAME ),
              },
              {
                value: 'bb-hint',
                label: __( 'ヒント', THEME_NAME ),
              },
              {
                value: 'bb-pickup',
                label: __( 'ピックアップ', THEME_NAME ),
              },
              {
                value: 'bb-bookmark',
                label: __( 'ブックマーク', THEME_NAME ),
              },
              {
                value: 'bb-memo',
                label: __( 'メモ', THEME_NAME ),
              },
              {
                value: 'bb-download',
                label: __( 'ダウンロード', THEME_NAME ),
              },
              {
                value: 'bb-break',
                label: __( 'ブレイク', THEME_NAME ),
              },
              {
                value: 'bb-amazon',
                label: __( 'Amazon', THEME_NAME ),
              },
              {
                value: 'bb-ok',
                label: __( 'OK', THEME_NAME ),
              },
              {
                value: 'bb-ng',
                label: __( 'NG', THEME_NAME ),
              },
              {
                value: 'bb-good',
                label: __( 'GOOD', THEME_NAME ),
              },
              {
                value: 'bb-bad',
                label: __( 'BAD', THEME_NAME ),
              },
              {
                value: 'bb-profile',
                label: __( 'プロフィール', THEME_NAME ),
              },
            ] }
          />
        </PanelBody>

        <PanelColorSettings
          title={ __( '色設定', THEME_NAME ) }
          colorSettings={[
            {
              label: __( '枠の色', THEME_NAME ),
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
          ]}
        />
      </InspectorControls>

      <div { ...blockProps }>
        <InnerBlocks />
      </div>

    </Fragment>
  );
}

export default compose([
  withColors('backgroundColor', {textColor: 'color', borderColor: 'border-color'}),
  withFontSizes('fontSize'),
])(TabBoxEdit);