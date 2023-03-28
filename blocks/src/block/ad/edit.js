import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import classnames from 'classnames';

export default function edit( props ) {
  const { attributes, setAttributes, className } = props;
  const classes = classnames( 'ad-box', 'block-box', {
    [ className ]: !! className,
    [ attributes.className ]: !! attributes.className,
  } );
  setAttributes( { classNames: classes } );

  const getAdContent = () => {
    let msg = '';
    let adVisible = false;
    if ( gbSettings.isAdsVisible == 0 ) {
      msg = __( '広告が非表示です。', THEME_NAME );
    } else if ( gbSettings.isAdShortcodeEnable == 0 ) {
      msg = __( '[ad]ショートコードが無効です。', THEME_NAME );
    }
    // 広告表示可能
    else {
      msg = __( '広告', THEME_NAME );
      adVisible = true;
    }
    setAttributes( { adVisible: adVisible } );

    const adMsgClasses = classnames(
      'cocoon-render-message',
      'editor-ad-message',
      {
        [ 'is-ads-visible' ]: gbSettings.isAdsVisible === 1,
        [ 'is-ad-shortcode-enable' ]: gbSettings.isAdShortcodeEnable === 1,
      }
    );

    return <div className={ adMsgClasses }>{ msg }</div>;
  };

  return (
    <Fragment>
      <div { ...useBlockProps() }>{ getAdContent() }</div>
    </Fragment>
  );
}
