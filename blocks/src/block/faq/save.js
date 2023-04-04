import {
  InnerBlocks,
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save( { attributes } ) {
  const {
    question,
    questionLabel,
    answerLabel,
    questionColor,
    answerColor,
    backgroundColor,
    textColor,
    borderColor,
    customQuestionColor,
    customAnswerColor,
    customBackgroundColor,
    customTextColor,
    customBorderColor,
    fontSize,
  } = attributes;

  const questionClass = getColorClassName( 'question-color', questionColor );
  const answerClass = getColorClassName( 'answer-color', answerColor );
  const backgroundClass = getColorClassName(
    'background-color',
    backgroundColor
  );
  const textClass = getColorClassName( 'color', textColor );
  const borderClass = getColorClassName( 'border-color', borderColor );
  const fontSizeClass = getFontSizeClass( fontSize );

  const className = classnames( {
    'faq-wrap': true,
    'blank-box': true,
    'block-box': true,
    'has-question-color': questionColor || customQuestionColor,
    'has-answer-color': answerColor || customAnswerColor,
    'has-text-color': textColor || customTextColor,
    'has-background': backgroundColor || customBackgroundColor,
    'has-border-color': borderColor || customBorderColor,
    [ questionClass ]: questionClass,
    [ answerClass ]: answerClass,
    [ textClass ]: textClass,
    [ backgroundClass ]: backgroundClass,
    [ borderClass ]: borderClass,
    [ fontSizeClass ]: fontSizeClass,
  } );

  const styles = {
    '--cocoon-custom-question-color': customQuestionColor || undefined,
    '--cocoon-custom-answer-color': customAnswerColor || undefined,
    '--cocoon-custom-background-color': customBackgroundColor || undefined,
    '--cocoon-custom-text-color': customTextColor || undefined,
    '--cocoon-custom-border-color': customBorderColor || undefined,
  };

  const blockProps = useBlockProps.save( {
    className: className,
    style: styles,
  } );

  return (
    <div { ...blockProps }>
      <dl className="faq">
        <dt className="faq-question faq-item">
          <div className="faq-question-label faq-item-label">
            { questionLabel }
          </div>
          <div className="faq-question-content faq-item-content">
            <RichText.Content value={ question } />
          </div>
        </dt>
        <dd className="faq-answer faq-item">
          <div className="faq-answer-label faq-item-label">{ answerLabel }</div>
          <div className="faq-answer-content faq-item-content">
            <InnerBlocks.Content />
          </div>
        </dd>
      </dl>
    </div>
  );
}
