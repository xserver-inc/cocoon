import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/editor';
import classnames from 'classnames';

export default function edit(props) {
  const { attributes, setAttributes, className } = props;
  const { id } = attributes;
  const classes = classnames('ad-box', 'block-box',
    {
      [ className ]: !! className,
      [ attributes.className ]: !! attributes.className,
    }
  );
  setAttributes({ classNames: classes });

  const getAdContent = () => {
    let msg = '';
    if (gbSettings.isAdsVisible == 0) {
      msg = __('広告が非表示です。', THEME_NAME);
    }
    else if (gbSettings.isAdShortcodeEnable == 0) {
      msg = __('[ad]ショートカードが無効です。', THEME_NAME);
    }

    // メッセージが設定されていたらメッセージを表示する。
    if (msg !== '') {
      return (
        <div class='cocoon-render-message editor-ad-message'>
          {msg}
        </div>
      );
    }
    else {
      return (
        <ServerSideRender
          block={props.name}
          attributes={attributes}
        />
      );
    }
  }

  return (
    <Fragment>
      <div {...useBlockProps()}>
        {getAdContent()}
      </div>
    </Fragment>
  );
}
