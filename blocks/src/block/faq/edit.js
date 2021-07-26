
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
import { Fragment } from '@wordpress/element';
import { compose } from '@wordpress/compose';
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
  } = props;

  const {
    question,
    questionLabel,
    answerLabel,
  } = props.attributes;

  const classes = classnames(className, {
    'faq-wrap': true,
    'blank-box': true,
    'block-box': true,
    'has-question-color': questionColor.color,
    'has-answer-color': answerColor.color,
    'has-text-color': textColor.color,
    'has-background': backgroundColor.color,
    'has-border-color': borderColor.color,
    [questionColor.class]: questionColor.class,
    [answerColor.class]: answerColor.class,
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
        <PanelBody
          title={ __( '設定', THEME_NAME ) }
        >
          <BaseControl
            label={ __( '質問ラベル', THEME_NAME ) }
          >
            <TextControl
              value={ questionLabel }
              placeholder={ __( 'Q', THEME_NAME ) }
              onChange={ ( value ) => setAttributes( { questionLabel: value } ) }
              help={ __( '※2文字以下推奨', THEME_NAME ) }
            />
          </BaseControl>
          <BaseControl
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
          colorSettings={[
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
          ]}
        />
      </InspectorControls>

      <div { ...blockProps }>
        <dl className="faq">
          <dt
            className="faq-question faq-item"
          >
            <div
              className="faq-question-label faq-item-label"
            >
              { questionLabel }
            </div>
            <div
              className="faq-question-content faq-item-content"
            >
              <RichText
                tagName="div"
                className="faq-question-content"
                placeholder={ __( '質問を入力してください…', THEME_NAME ) }
                value={ question }
                multiline={ false }
                onChange={(value) => setAttributes({ question: value })}
              />
            </div>
          </dt>
          <dd className="faq-answer faq-item">
            <div
              className="faq-answer-label faq-item-label"
            >
              { answerLabel }
            </div>
            <div
              className="faq-answer-content faq-item-content"
            >
              <InnerBlocks />
            </div>
          </dd>
        </dl>
      </div>

    </Fragment>
  );
}

export default compose([
  withColors('backgroundColor', {textColor: 'color', borderColor: 'border-color', questionColor: 'question-color', answerColor: 'answer-color'}),
  withFontSizes('fontSize'),
])(FAQEdit);
