
import { THEME_NAME, ICONS } from '../../helpers';
import { __ } from '@wordpress/i18n';
import {
  InspectorControls,
  RichText,
  withColors,
  PanelColorSettings,
  withFontSizes,
  useBlockProps,
} from '@wordpress/block-editor';
import {
  PanelBody,
  SelectControl,
  ToggleControl,
  BaseControl,
  Button,
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import classnames from 'classnames';
import { times } from 'lodash';

const MICRO_COPY_CLASS = 'micro-copy';

export function MicroBalloonEdit( props ) {
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
    type,
    isCircle,
    icon,
  } = attributes;

  const classes = classnames(className, {
    [ 'micro-balloon' ]: true,
    [ type ]: !! type,
    [ 'mc-circle' ]: !! isCircle,
    [ MICRO_COPY_CLASS ]: true,
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
            label={ __( 'タイプ', THEME_NAME ) }
            value={ type }
            onChange={ ( value ) => setAttributes( { type: value } ) }
            options={ [
              {
                value: 'micro-top',
                label: __( '下寄り', THEME_NAME ),
              },
              {
                value: 'micro-bottom',
                label: __( '上寄り', THEME_NAME ),
              },
            ] }
          />

          <ToggleControl
            label={ __( '円形にする', THEME_NAME ) }
            checked={ isCircle }
            onChange={ ( value ) => setAttributes( { isCircle: value } ) }
          />

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
      <div className="admin-micro-balloon-wrap wp-block micro-copy">
        <div { ...blockProps }>
          <span className="micro-balloon-content micro-content">
            { icon && <span className={classnames('micro-balloon-icon', 'micro-icon', icon)}></span> }
            <RichText
              value={ content }
              onChange={ ( value ) => setAttributes( { content: value } ) }
            />
          </span>
        </div>
      </div>
    </Fragment>
  );
}

export default compose([
  withColors('backgroundColor', {textColor: 'color', borderColor: 'border-color'}),
  withFontSizes('fontSize'),
])(MicroBalloonEdit);
