import { THEME_NAME } from '../../helpers';
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
import { BaseControl, PanelBody, TextControl } from '@wordpress/components';
import { Fragment, useEffect } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import { useSelect } from '@wordpress/data';
import classnames from 'classnames';

export function FAQEdit( props ) {
  // console.log(props);
  const {
    className,
    setAttributes,
    questionColor,
    setQuestionColor,
    answerColor,
    setAnswerColor,
    backgroundColor,
    setBackgroundColor,
    textColor,
    setTextColor,
    borderColor,
    setBorderColor,
    fontSize,
    clientId,
  } = props;

  const {
    question,
    questionLabel,
    answerLabel,
    customBackgroundColor,
    customTextColor,
    customBorderColor,
    customQuestionColor,
    customAnswerColor,
    notNestedStyle,
    questionColorValue,
    answerColorValue,
    backgroundColorValue,
    textColorValue,
    borderColorValue,
  } = props.attributes;

  // 親ブロックのnotNestedStyleがfalseかどうかを判定
  const isParentNestedStyle = useSelect( ( select ) => {
    const parentBlocks =
      select( 'core/block-editor' ).getBlockParents( clientId );
    for ( const parentClientId of parentBlocks ) {
      const parentBlock =
        select( 'core/block-editor' ).getBlock( parentClientId );
      if (
        parentBlock.name === props.name &&
        parentBlock.attributes.notNestedStyle === false
      ) {
        return true;
      }
    }
    return false;
  } );

  // 親ブロックのnotNestedStyleがfalseの場合はnotNestedStyleをfalseにする
  if ( isParentNestedStyle && notNestedStyle ) {
    setAttributes( { notNestedStyle: false } );
  }

  useEffect( () => {
    setAttributes( { questionColorValue: questionColor.color } );
    setAttributes( { answerColorValue: answerColor.color } );
    setAttributes( { backgroundColorValue: backgroundColor.color } );
    setAttributes( { textColorValue: textColor.color } );
    setAttributes( { borderColorValue: borderColor.color } );
  }, [ questionColor, answerColor, borderColor, backgroundColor, textColor ] );

  const classes = classnames( className, {
    'faq-wrap': true,
    'blank-box': true,
    'block-box': true,
    'has-question-color': questionColor.color,
    'has-answer-color': answerColor.color,
    'has-text-color': textColor.color,
    'has-background': backgroundColor.color,
    'has-border-color': borderColor.color,
    [ questionColor.class ]: questionColor.class,
    [ answerColor.class ]: answerColor.class,
    [ backgroundColor.class ]: backgroundColor.class,
    [ textColor.class ]: textColor.class,
    [ borderColor.class ]: borderColor.class,
    [ fontSize.class ]: fontSize.class,
    'not-nested-style': notNestedStyle,
    'cocoon-block-faq': true,
  } );

  const styles = {
    '--cocoon-custom-question-color': customQuestionColor || undefined,
    '--cocoon-custom-answer-color': customAnswerColor || undefined,
    '--cocoon-custom-border-color': customBorderColor || undefined,
    '--cocoon-custom-background-color': customBackgroundColor || undefined,
    '--cocoon-custom-text-color': customTextColor || undefined,
  };

  if ( notNestedStyle ) {
    styles[ '--cocoon-custom-question-color' ] = questionColorValue;
    styles[ '--cocoon-custom-answer-color' ] = answerColorValue;
    styles[ '--cocoon-custom-border-color' ] = borderColorValue;
    styles[ '--cocoon-custom-background-color' ] = backgroundColorValue;
    styles[ '--cocoon-custom-text-color' ] = textColorValue;
  }

  const blockProps = useBlockProps( {
    className: classes,
    style: styles,
  } );

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={ __( '設定', THEME_NAME ) }>
          <BaseControl
            id="questionLabel"
            label={ __( '質問ラベル', THEME_NAME ) }
          >
            <TextControl
              value={ questionLabel }
              placeholder={ __( 'Q', THEME_NAME ) }
              onChange={ ( value ) =>
                setAttributes( { questionLabel: value } )
              }
              help={ __( '※2文字以下推奨', THEME_NAME ) }
            />
          </BaseControl>
          <BaseControl
            id="answerLabel"
            label={ __( '回答ラベル', THEME_NAME ) }
          >
            <TextControl
              value={ answerLabel }
              placeholder={ __( 'A', THEME_NAME ) }
              onChange={ ( value ) => setAttributes( { answerLabel: value } ) }
              help={ __( '※2文字以下推奨', THEME_NAME ) }
            />
          </BaseControl>
        </PanelBody>

        <PanelColorSettings
          title={ __( '色設定', THEME_NAME ) }
          enableAlpha={true}
          colorSettings={ [
            {
              label: __( '質問ラベル色', THEME_NAME ),
              onChange: setQuestionColor,
              value: questionColor.color,
            },
            {
              label: __( '回答ラベル色', THEME_NAME ),
              onChange: setAnswerColor,
              value: answerColor.color,
            },
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
        <dl className="faq">
          <dt className="faq-question faq-item">
            <div className="faq-question-label faq-item-label">
              { questionLabel }
            </div>
            <div className="faq-question-content faq-item-content">
              <RichText
                tagName="div"
                className="faq-question-content"
                placeholder={ __( '質問を入力してください…', THEME_NAME ) }
                value={ question }
                onChange={ ( value ) => setAttributes( { question: value } ) }
              />
            </div>
          </dt>
          <dd className="faq-answer faq-item">
            <div className="faq-answer-label faq-item-label">
              { answerLabel }
            </div>
            <div className="faq-answer-content faq-item-content">
              <InnerBlocks />
            </div>
          </dd>
        </dl>
      </div>
    </Fragment>
  );
}

export default compose( [
  withColors( 'backgroundColor', {
    textColor: 'color',
    borderColor: 'border-color',
    questionColor: 'question-color',
    answerColor: 'answer-color',
  } ),
  withFontSizes( 'fontSize' ),
] )( FAQEdit );
