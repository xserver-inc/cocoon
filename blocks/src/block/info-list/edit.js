/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
  PanelBody,
  TextControl,
  ToggleControl,
  SearchControl,
  RangeControl,
  Disabled,
} from '@wordpress/components';
import { Fragment, useState } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/editor';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { THEME_NAME, CreateCategoryList } from '../../helpers';

export default function edit( props ) {
  const { attributes, setAttributes, className } = props;
  const { count, showAllCats, cats, caption, showFrame, showDivider, modified } =
    attributes;

  const classes = classnames( 'info-list-box', 'block-box', {
    [ className ]: !! className,
    [ attributes.className ]: !! attributes.className,
  } );
  setAttributes( { classNames: classes } );

  // カテゴリー検索文字列の保持
  const [ catSearchInput, setCatSearchInput ] = useState( '' );

  // wp.coreから全カテゴリー情報の取得
  const categoryData = useSelect( ( select ) => {
    return select( 'core' ).getEntityRecords( 'taxonomy', 'category' );
  } );

  // 可変コントロールの定義
  let catsTextControl = (
    <Fragment>
      <TextControl
        label={ __( '表示するカテゴリーをカンマ区切りで指定', THEME_NAME ) }
        value={ cats }
        onChange={ ( value ) => setAttributes( { cats: value } ) }
      />
      <PanelBody
        title={ __( 'カテゴリー検索', THEME_NAME ) }
        initialOpen={ true }
      >
        <SearchControl
          value={ catSearchInput }
          onChange={ setCatSearchInput }
        />
        { CreateCategoryList( categoryData, catSearchInput, cats, ( attr ) => {
          setAttributes( { cats: attr } );
        } ) }
      </PanelBody>
    </Fragment>
  );
  if ( showAllCats ) {
    catsTextControl = <Disabled>{ catsTextControl }</Disabled>;
  }

  const getInfoListContent = () => {
    return <ServerSideRender block={ props.name } attributes={ attributes } />;
  };

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={ __( '基本設定', THEME_NAME ) } initialOpen={ true }>
          <TextControl
            label={ __( 'キャプション(枠線内表示)', THEME_NAME ) }
            value={ caption }
            onChange={ ( newValue ) => setAttributes( { caption: newValue } ) }
          />
          <RangeControl
            label={ __( '表示数', THEME_NAME ) }
            isShiftStepEnabled={ false }
            value={ count }
            onChange={ ( newValue ) => setAttributes( { count: newValue } ) }
            min={ 1 }
            max={ 100 }
          />
          <ToggleControl
            label={ __( '枠線を表示する', THEME_NAME ) }
            checked={ showFrame }
            onChange={ ( isChecked ) =>
              setAttributes( { showFrame: isChecked } )
            }
          />
          <ToggleControl
            label={ __( '仕切り線を表示する', THEME_NAME ) }
            checked={ showDivider }
            onChange={ ( isChecked ) =>
              setAttributes( { showDivider: isChecked } )
            }
          />
          <ToggleControl
            label={ __( '更新日順に並び替える', THEME_NAME ) }
            checked={ modified }
            onChange={ ( isChecked ) =>
              setAttributes( { modified: isChecked } )
            }
          />
        </PanelBody>
        <PanelBody title={ __( 'フィルタ', THEME_NAME ) } initialOpen={ false }>
          <ToggleControl
            label={ __( '全カテゴリーを表示する', THEME_NAME ) }
            checked={ showAllCats }
            onChange={ ( isChecked ) => {
              setAttributes( { showAllCats: isChecked } );
              // 全カテゴリー表示を切り替えた際は検索文字列をリセット
              setCatSearchInput( '' );
            } }
          />
          { catsTextControl }
        </PanelBody>
      </InspectorControls>
      <div { ...useBlockProps() }>{ getInfoListContent() }</div>
    </Fragment>
  );
}
