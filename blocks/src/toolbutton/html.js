/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../helpers.js';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Fragment, useState, useRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { registerFormatType, insert } from '@wordpress/rich-text';
import {
  RichTextToolbarButton,
  RichTextShortcut,
} from '@wordpress/block-editor';
import { Icon, html } from '@wordpress/icons';
import { Modal, TextControl, Button } from '@wordpress/components';

const isPrivilegeActivationCodeAvailable = gbSettings[
  'isPrivilegeActivationCodeAvailable'
]
  ? gbSettings[ 'isPrivilegeActivationCodeAvailable' ]
  : '';

registerFormatType( 'cocoon-blocks/html', {
  title: __( 'HTML挿入', THEME_NAME ),
  tagName: 'span',
  className: 'insert-html',

  edit( { isActive, value, onChange } ) {
    // モーダルの開閉状態と入力値、エディタの選択位置を保持するステートを用意
    const [ isModalOpen, setIsModalOpen ] = useState( false );
    const [ htmlInputValue, setHtmlInputValue ] = useState( '' );
    const savedValueRef = useRef( null );

    // モーダルでOKボタンが押されたときの処理
    const onModalConfirm = () => {
      const saved = savedValueRef.current;
      if ( ! saved ) return;

      const inputHtml = htmlInputValue.trim();
      if ( inputHtml ) {
        // [html]ショートコードで囲んでエディタに挿入する
        const newValue = insert(
          value,
          '[html]' + inputHtml + '[/html]',
          saved.start,
          saved.end
        );
        onChange( newValue );
      }

      setIsModalOpen( false );
      setHtmlInputValue( '' );
      savedValueRef.current = null;
    };

    // モーダルでキャンセルされたときの処理
    const onModalClose = () => {
      setIsModalOpen( false );
      setHtmlInputValue( '' );
      savedValueRef.current = null;
    };

    const onToggle = () => {
      if ( value.end - value.start > 0 ) {
        // テキスト選択あり → 従来通り即座に [html]...[/html] で囲む
        const newValue = insert(
          value,
          '[html]' +
            value.text.substr( value.start, value.end - value.start ) +
            '[/html]',
          value.start,
          value.end
        );
        return onChange( newValue );
      }

      // テキスト選択なし → Modal を開いてユーザーにHTML入力を求める
      savedValueRef.current = { start: value.start, end: value.end };
      setHtmlInputValue( '' );
      setIsModalOpen( true );
    };

    // @see keycodes/src/index.js
    const shortcutType = 'primaryShift';
    const shortcutCharacter = 'h';
    return (
      <Fragment>
        <RichTextShortcut
          type={ shortcutType }
          character={ shortcutCharacter }
          onUse={ onToggle }
        />
        <RichTextToolbarButton
          icon={ <Icon icon={ html } size={ 32 } /> }
          title={ __( 'HTML挿入', THEME_NAME ) }
          onClick={ onToggle }
          isActive={ isActive }
          shorcutType={ shortcutType }
          shorcutCharacter={ shortcutCharacter }
          // className='abcddddddddddddddd'
        />
        { isModalOpen && (
          <Modal
            title={ __( 'HTML挿入', THEME_NAME ) }
            onRequestClose={ onModalClose }
            className="cocoon-html-insert-modal"
          >
            <TextControl
              label={ __( 'HTMLを入力してください。', THEME_NAME ) }
              value={ htmlInputValue }
              onChange={ setHtmlInputValue }
              onKeyDown={ ( e ) => {
                // 日本語入力中（IME変換中）のEnterでModalが意図せず閉じるのを防ぐ
                if ( e.key === 'Enter' && ! e.nativeEvent.isComposing ) {
                  e.preventDefault();
                  onModalConfirm();
                }
              } }
              help={ __(
                '挿入するHTMLコードを入力します。[html]...[/html] ショートコードとして埋め込まれます。',
                THEME_NAME
              ) }
            />
            <div style={ { marginTop: '16px', display: 'flex', gap: '8px', justifyContent: 'flex-end' } }>
              <Button variant="secondary" onClick={ onModalClose }>
                { __( 'キャンセル', THEME_NAME ) }
              </Button>
              <Button variant="primary" onClick={ onModalConfirm }>
                { __( 'OK', THEME_NAME ) }
              </Button>
            </div>
          </Modal>
        ) }
      </Fragment>
    );
  },
} );
