import { THEME_NAME, ICONS } from '../../helpers';
import { __ } from '@wordpress/i18n';
import {
  InspectorControls,
  InnerBlocks,
  RichText,
  withColors,
  PanelColorSettings,
  withFontSizes,
  useBlockProps,
} from '@wordpress/block-editor';
import {
  PanelBody,
  BaseControl,
  Button,
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import classnames from 'classnames';
import { times } from 'lodash';

const CAPTION_BOX_CLASS = 'caption-box';
const DEFAULT_MSG = __( '見出し', THEME_NAME );

export function CaptionBoxEdit( props ) {
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
    content,
    icon,
  } = attributes;

  const classes = classnames(className, {
    [ CAPTION_BOX_CLASS ]: true,
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
          <BaseControl label={ __( 'アイコン', THEME_NAME ) }>
            <div className="icon-setting-buttons">
              { times( ICONS.length, ( index ) => {
                return (
                  <Button
                    isDefault
                    isPrimary={ icon === ICONS[index].value }
                    className={ICONS[index].label}
                    onClick={ () => {
                      setAttributes( { icon: ICONS[index].value } );
                    } }
                    key={ index }
                  >
                  </Button>
                );
              } ) }
            </div>
          </BaseControl>
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
        <div className={
          classnames('caption-box-label', 'block-box-label', 'box-label', icon)
        }>
          <span className={
            classnames('caption-box-label-text', 'block-box-label-text', 'box-label-text')
          }>
            <RichText
              value={ content }
              onChange={ ( value ) => setAttributes( { content: value } ) }
              placeholder={ DEFAULT_MSG }
            />
          </span>
        </div>
        <div className={classnames('caption-box-content', 'block-box-content', 'box-content')}>
          <InnerBlocks />
        </div>
      </div>

    </Fragment>
  );
}

export default compose([
  withColors('backgroundColor', {textColor: 'color', borderColor: 'border-color'}),
  withFontSizes('fontSize'),
])(CaptionBoxEdit);