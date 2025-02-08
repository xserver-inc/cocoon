import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { SelectControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/editor';
import classnames from 'classnames';

export default function edit( props ) {
  const { attributes, setAttributes, className } = props;
  const { id } = attributes;
  const classes = classnames( 'template-box', 'block-box', {
    [ 'template-' + id ]: !! ( id !== '-1' ),
    [ className ]: !! className,
    [ attributes.className ]: !! attributes.className,
  } );
  setAttributes( { classNames: classes } );

  // attributesのidが存在するかしないかを判断するフラグ
  let isTemplateIdExist = false;

  // ドロップダウンリストに表示される有効なテンプレートアイテムの数
  let abledDropdownListItemCount = 0;

  function createOptions() {
    var options = [];
    options.push( { value: '-1', label: __( '未選択', THEME_NAME ) } );
    if ( typeof gbTemplates !== 'undefined' ) {
      gbTemplates.forEach( ( temp ) => {
        if ( isTemplateIdExist === false && temp.id == id ) {
          isTemplateIdExist = true;
        }
        if ( temp.visible == '1' ) {
          options.push( {
            value: temp.id,
            label: temp.title,
            disabled: false,
          } );
          abledDropdownListItemCount += 1;
        } else {
          options.push( {
            value: temp.id,
            label: temp.title + __( '（リスト非表示）', THEME_NAME ),
            disabled: true,
          } );
        }
      } );
    }

    return options;
  }

  const getTemplateMessage = () => {
    let msg = '';
    const setmsg = __(
      'ダッシュボードメニューの「Cocoon設定」→「テンプレート」からテンプレートを作成してください。',
      THEME_NAME
    );
    if ( typeof gbTemplates === 'undefined' || gbTemplates.length === 0 ) {
      msg = __( 'テンプレートが登録されていません。', THEME_NAME ) + setmsg;
    } else if (
      typeof gbTemplates !== 'undefined' &&
      abledDropdownListItemCount === 0
    ) {
      //テンプレート非表示などで有効に選択できるテンプレートが存在しない場合
      msg =
        __( '有効なテンプレートが登録されていません。', THEME_NAME ) +
        setmsg +
        __(
          'もしくは登録されているテンプレートを表示設定にしてください。。',
          THEME_NAME
        );
    } else if ( typeof gbTemplates !== 'undefined' ) {
      //ドロップダウンにテンプレートの選択肢がある場合
      msg = __( 'テンプレートを選択してください。', THEME_NAME );
    } else {
      return '';
    }
    return (
      <div class="cocoon-render-message editor-template-message">{ msg }</div>
    );
  };

  const getTemplateContent = () => {
    if ( id == '-1' ) {
      return getTemplateMessage();
    } else {
      return (
        <ServerSideRender block={ props.name } attributes={ attributes } />
      );
    }
  };

  var options = createOptions();

  // テンプレートを消したりして存在しないランキングIDだった場合は-1をセットする
  // これをすることによりブロックエディターリロード時でも「テンプレートを選択してください。」などのエラーメッセージが出力される
  // ServerSideRenderも呼び出されない
  if ( ! isTemplateIdExist ) {
    setAttributes( { id: '-1' } );
  }

  return (
    <Fragment>
      <div { ...useBlockProps() }>
        <SelectControl
          label={ __( 'テンプレート', THEME_NAME ) }
          labelPosition="side"
          className="cocoon-render-dropdown editor-template-dropdown"
          value={ id }
          onChange={ ( value ) =>
            setAttributes( { id: value, classNames: classes } )
          }
          options={ options }
          __nextHasNoMarginBottom={ true }
        />
        { getTemplateContent() }
      </div>
    </Fragment>
  );
}
