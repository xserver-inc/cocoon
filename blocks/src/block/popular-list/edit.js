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
  SelectControl,
  PanelBody,
  TextControl,
  ToggleControl,
  SearchControl,
  RangeControl,
  __experimentalDivider as Divider,
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
  const {
    showAllDays,
    days,
    rank,
    pv,
    post_type,
    count,
    type,
    bold,
    arrow,
    showAllCats,
    cats,
    children,
    horizontal,
    ex_posts,
    ex_cats,
  } = attributes;

  const classes = classnames( 'popular-list-box', 'block-box', {
    [ className ]: !! className,
    [ attributes.className ]: !! attributes.className,
  } );
  setAttributes( { classNames: classes } );

  // カテゴリー検索文字列の保持
  const [ catSearchInput, setCatSearchInput ] = useState( '' );
  // 除外カテゴリー検索文字列の保持
  const [ exCatSearchInput, setExCatSearchInput ] = useState( '' );

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
        title={ __( '表示カテゴリー選択', THEME_NAME ) }
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
      <ToggleControl
        label={ __( '子カテゴリーの内容を含めて表示', THEME_NAME ) }
        checked={ children }
        onChange={ ( isChecked ) => setAttributes( { children: isChecked } ) }
      />
    </Fragment>
  );
  if ( showAllCats ) {
    catsTextControl = <Disabled>{ catsTextControl }</Disabled>;
  }

  let exCatsTextControl = (
    <Fragment>
      <TextControl
        label={ __( '除外するカテゴリーをカンマ区切りで指定', THEME_NAME ) }
        value={ ex_cats }
        onChange={ ( value ) => setAttributes( { ex_cats: value } ) }
      />
      <PanelBody
        title={ __( '除外カテゴリー選択', THEME_NAME ) }
        initialOpen={ true }
      >
        <SearchControl
          value={ exCatSearchInput }
          onChange={ setExCatSearchInput }
        />
        { CreateCategoryList(
          categoryData,
          exCatSearchInput,
          ex_cats,
          ( attr ) => {
            setAttributes( { ex_cats: attr } );
          }
        ) }
      </PanelBody>
    </Fragment>
  );
  if ( showAllCats == false ) {
    exCatsTextControl = <Disabled>{ exCatsTextControl }</Disabled>;
  }

  let daysNumberControl = (
    <SelectControl
      label={ __( 'データの集計期間', THEME_NAME ) }
      value={ days }
      onChange={ ( newValue ) =>
        setAttributes( { days: newValue } )
      }
      options={ [
        {
          label: __( '全期間', THEME_NAME ),
          value: "all",
        },
        {
          label: __( '1日', THEME_NAME ),
          value: "1",
        },
        {
          label: __( '3日', THEME_NAME ),
          value: "3",
        },
        {
          label: __( '1週間', THEME_NAME ),
          value: "7",
        },
        {
          label: __( '2週間', THEME_NAME ),
          value: "14",
        },
        {
          label: __( '1ヶ月', THEME_NAME ),
          value: "30",
        },
        {
          label: __( '2ヶ月', THEME_NAME ),
          value: "60",
        },
        {
          label: __( '3ヶ月', THEME_NAME ),
          value: "90",
        },
        {
          label: __( '半年', THEME_NAME ),
          value: "182",
        },
        {
          label: __( '1年', THEME_NAME ),
          value: "365",
        },
      ] }
    />
  );
  if ( showAllDays ) {
    daysNumberControl = <Disabled>{ daysNumberControl }</Disabled>;
  }

  const getPopularListContent = () => {
    return <ServerSideRender block={ props.name } attributes={ attributes } />;
  };

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={ __( '基本設定', THEME_NAME ) } initialOpen={ true }>
          <RangeControl
            label={ __( '表示する記事の数', THEME_NAME ) }
            isShiftStepEnabled={ false }
            value={ count }
            onChange={ ( newValue ) => setAttributes( { count: newValue } ) }
            min={ 1 }
          />
          <ToggleControl
            label={ __( '全期間集計', THEME_NAME ) }
            checked={ showAllDays }
            onChange={ ( isChecked ) => {
              setAttributes( { showAllDays: isChecked } );
            } }
          />
          { daysNumberControl }
          <SelectControl
            label={ __( '表示タイプ', THEME_NAME ) }
            value={ type }
            onChange={ ( newType ) => setAttributes( { type: newType } ) }
            options={ [
              {
                label: __( 'デフォルト', THEME_NAME ),
                value: 'default',
              },
              {
                label: __( 'カードの上下に区切り線を入れる', THEME_NAME ) + __( '（縦型表示のみ）', THEME_NAME ),
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
            label={ __( 'ランキング番号を表示する', THEME_NAME ) }
            checked={ rank }
            onChange={ ( isChecked ) => {
              setAttributes( { rank: isChecked } );
            } }
          />
          <ToggleControl
            label={ __( 'PV数を表示する', THEME_NAME ) }
            checked={ pv }
            onChange={ ( isChecked ) => {
              setAttributes( { pv: isChecked } );
            } }
          />
          <ToggleControl
            label={ __( 'カードに矢印を表示する', THEME_NAME ) }
            checked={ arrow }
            onChange={ ( isChecked ) => setAttributes( { arrow: isChecked } ) }
          />
        </PanelBody>
        <PanelBody title={ __( 'フィルタ', THEME_NAME ) } initialOpen={ false }>
          <ToggleControl
            label={ __( '全カテゴリー表示', THEME_NAME ) }
            checked={ showAllCats }
            onChange={ ( isChecked ) => {
              setAttributes( { showAllCats: isChecked } );
              // 全カテゴリー表示を切り替えた際は検索文字列をリセット
              setCatSearchInput( '' );
            } }
          />
          { catsTextControl }
          { exCatsTextControl }
          <Divider />
          <TextControl
            label={ __( '投稿タイプ', THEME_NAME ) }
            value={ post_type }
            onChange={ ( newValue ) =>
              setAttributes( { post_type: newValue } )
            }
          />
          <TextControl
            label={ __( '除外投稿(ID)', THEME_NAME ) }
            value={ ex_posts }
            onChange={ ( newValue ) => setAttributes( { ex_posts: newValue } ) }
          />
        </PanelBody>
      </InspectorControls>
      <div { ...useBlockProps() }>{ getPopularListContent() }</div>
    </Fragment>
  );
}
