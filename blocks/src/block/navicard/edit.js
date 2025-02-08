import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
  SelectControl,
  PanelBody,
  ToggleControl,
  __experimentalDivider as Divider,
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/editor';
import classnames from 'classnames';

export default function edit( props ) {
  const { attributes, setAttributes, className } = props;
  const { id, menuType, bold, arrow, horizontal } = attributes;
  const classes = classnames( 'navicard', 'block-box', {
    [ 'menu-' + id ]: !! ( id !== '-1' ),
    [ className ]: !! className,
    [ attributes.className ]: !! attributes.className,
  } );
  setAttributes( { classNames: classes } );

  let isNavicardIdExist = false;

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
        if ( isNavicardIdExist === false && menu.term_id == id ) {
          isNavicardIdExist = true;
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

  const getNavicardMessage = () => {
    let msg = '';
    const setmsg = __(
      'ダッシュボードメニューの「外観」→「メニュー」からメニューを作成してください。',
      THEME_NAME
    );
    if ( typeof gbNavMenus === 'undefined' || gbNavMenus.length === 0 ) {
      msg = __( 'メニューが登録されていません。', THEME_NAME ) + setmsg;
    } else if ( typeof gbNavMenus !== 'undefined' ) {
      msg = __( 'メニューを選択してください。', THEME_NAME );
    } else {
      return '';
    }

    return (
      <div class="cocoon-render-message editor-navicard-message">{ msg }</div>
    );
  };

  const getNavicardContent = () => {
    if ( id == '-1' ) {
      return getNavicardMessage();
    } else {
      return (
        <ServerSideRender block={ props.name } attributes={ attributes } />
      );
    }
  };

  var options = createOptions();

  if ( ! isNavicardIdExist ) {
    setAttributes( { id: '-1' } );
  }

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={ __( '基本設定', THEME_NAME ) } initialOpen={ true }>
          <SelectControl
            label={ __( '表示タイプ', THEME_NAME ) }
            value={ menuType }
            onChange={ ( newType ) => setAttributes( { menuType: newType } ) }
            options={ [
              {
                label: __( 'デフォルト', THEME_NAME ),
                value: 'default',
              },
              {
                label: __( 'カードの上下に区切り線を入れる', THEME_NAME ) + __( '（縦並び表示のみ）', THEME_NAME ),
                value: 'border_partition',
              },
              {
                label: __( 'カードに枠線を表示する', THEME_NAME ),
                value: 'border_square',
              },
              {
                label: __( '大きなサムネイル表示にする', THEME_NAME ),
                value: 'large_thumb',
              },
              {
                label: __( '大きなサムネイルにタイトルを重ねて表示する', THEME_NAME ),
                value: 'large_thumb_on',
              },
            ] }
            __nextHasNoMarginBottom={ true }
          />
          <Divider />
          <ToggleControl
            label={ __( '横並び表示にする', THEME_NAME ) }
            checked={ horizontal }
            onChange={ ( isChecked ) =>
              setAttributes( { horizontal: isChecked } )
            }
          />
          <ToggleControl
            label={ __( 'タイトルを太字にする', THEME_NAME ) }
            checked={ bold }
            onChange={ ( isChecked ) => setAttributes( { bold: isChecked } ) }
          />
          <ToggleControl
            label={ __( 'カードに矢印を表示する', THEME_NAME ) }
            checked={ arrow }
            onChange={ ( isChecked ) => setAttributes( { arrow: isChecked } ) }
          />
        </PanelBody>
      </InspectorControls>
      <div { ...useBlockProps() }>
        <SelectControl
          label={ __( 'メニュー', THEME_NAME ) }
          labelPosition="side"
          className="cocoon-render-dropdown editor-navicard-dropdown"
          value={ id }
          onChange={ ( value ) =>
            setAttributes( {
              id: value,
              classNames: classes,
              menuName: getMenuNameFromId( value ),
            } )
          }
          options={ options }
          __nextHasNoMarginBottom={ true }
        />
        { getNavicardContent() }
      </div>
    </Fragment>
  );
}
