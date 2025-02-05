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
  const classes = classnames( 'box-menu-box', 'block-box', {
    [ 'box-menu-' + id ]: !! ( id !== '-1' ),
    [ className ]: !! className,
    [ attributes.className ]: !! attributes.className,
  } );
  setAttributes( { classNames: classes } );

  // attributesのidが存在するかしないかを判断するフラグ
  let isBoxMenuIdExist = false;

  // ドロップダウンリストに表示される有効なランキングアイテムの数
  let abledDropdownListItemCount = 0;

  function getMenuNameFromId( id ) {
    var name = '';
    if ( typeof gbNavMenus !== 'undefined' ) {
      for ( let menu of gbNavMenus ) {
        if ( menu.term_id == id ) {
          name = menu.name;
          break;
        }
      }
    }
    return name;
  }

  function createOptions() {
    var options = [];
    options.push( { value: '-1', label: __( '未選択', THEME_NAME ) } );
    if ( typeof gbNavMenus !== 'undefined' ) {
      gbNavMenus.forEach( ( menu ) => {
        if ( isBoxMenuIdExist === false && menu.term_id == id ) {
          isBoxMenuIdExist = true;
        }
        options.push( {
          value: menu.term_id,
          label: menu.name,
          disabled: false,
        } );
        abledDropdownListItemCount += 1;
      } );
    }

    return options;
  }

  const getBoxMenuMessage = () => {
    let msg = '';
    const setmsg = __(
      'ダッシュボードメニューの「外観」→「メニュー」からボックスメニューを作成してください。',
      THEME_NAME
    );
    if ( typeof gbNavMenus === 'undefined' || gbNavMenus.length === 0 ) {
      msg = __( 'ボックスメニューが登録されていません。', THEME_NAME ) + setmsg;
    } else if (
      typeof gbNavMenus !== 'undefined' &&
      abledDropdownListItemCount === 0
    ) {
      msg =
        __( '有効なボックスメニューが登録されていません。', THEME_NAME ) +
        setmsg +
        __(
          'もしくは登録されているボックスメニューを表示設定にしてください。',
          THEME_NAME
        );
    } else if ( typeof gbNavMenus !== 'undefined' ) {
      msg = __( 'ボックスメニューを選択してください。', THEME_NAME );
    } else {
      return '';
    }
    return (
      <div class="cocoon-render-message editor-box-menu-message">{ msg }</div>
    );
  };

  const getBoxMenuContent = () => {
    if ( id == '-1' ) {
      return getBoxMenuMessage();
    } else {
      return (
        <ServerSideRender block={ props.name } attributes={ attributes } />
      );
    }
  };

  var options = createOptions();

  if ( ! isBoxMenuIdExist ) {
    setAttributes( { id: '-1' } );
  }

  return (
    <Fragment>
      <div { ...useBlockProps() }>
        <SelectControl
          label={ __( 'ボックスメニュー', THEME_NAME ) }
          labelPosition="side"
          className="cocoon-render-dropdown editor-box-menu-dropdown"
          value={ id }
          onChange={ ( value ) =>
            setAttributes( {
              id: value,
              classNames: classes,
              boxName: getMenuNameFromId( value ),
            } )
          }
          options={ options }
          __nextHasNoMarginBottom={ true }
        />
        { getBoxMenuContent() }
      </div>
    </Fragment>
  );
}
