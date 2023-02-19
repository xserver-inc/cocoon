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
	CheckboxControl,
	SearchControl,
	__experimentalNumberControl as NumberControl,
	__experimentalDivider as Divider,
	Disabled,
} from '@wordpress/components';
import { Fragment, useState } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/editor';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { THEME_NAME } from '../../helpers';

export default function edit( props ) {
	const { attributes, setAttributes, className } = props;
	const {
		count,
		type,
		bold,
		arrow,
		showAllCats,
		cats,
		children,
		horizontal,
		showAllTags,
		tags,
		modified,
		order,
		offset,
		sticky,
		snippet,
		post_type,
		taxonomy,
		author,
	} = attributes;

	const classes = classnames( 'new-list-box', 'block-box', {
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

	// 1カテゴリー分のcatsへの追加・削除およびCheckboxControlの生成
	function createCategory( isChecked, label, id ) {
		// cats文字列にidを追加する
		const addCat = ( id ) => {
			let catsString = '';
			if ( cats == '' ) {
				catsString = id;
			} else {
				let catsArray = cats.split( ',' );
				let found = false;
				for ( var i = 0; i < catsArray.length; i++ ) {
					if ( catsArray[ i ] == id ) {
						found = true;
						break;
					}
				}
				if ( found == false ) {
					catsArray.push( id );
				}
				catsString = catsArray.join( ',' );
			}
			setAttributes( { cats: catsString } );
		};

		// cats文字列からidを削除する
		const deleteCat = ( id ) => {
			let catsArray = cats.split( ',' );
			for ( var i = 0; i < catsArray.length; i++ ) {
				if ( catsArray[ i ] == id ) {
					catsArray.splice( i, 1 );
				}
			}
			setAttributes( { cats: catsArray.join( ',' ) } );
		};

		return (
			<CheckboxControl
				label={ label }
				checked={ isChecked }
				onChange={ ( isChecked ) => {
					if ( isChecked == true ) {
						addCat( id );
					} else {
						deleteCat( id );
					}
				} }
			/>
		);
	}

	// 入力文字列に応じてカテゴリーの一覧を出力する
	function createCategoryList( input ) {
		if ( categoryData == null ) return null;

		let control = [];
		let catsArray = cats.split( ',' );
		categoryData.forEach( ( record ) => {
			let isChecked = false;
			for ( var i = 0; i < catsArray.length; i++ ) {
				if ( catsArray[ i ] == String( record.id ) ) {
					isChecked = true;
					break;
				}
			}
			//検索文字列がある場合
			if ( input != '' ) {
				// recode.nameにinputが含まれるなら追加
				if ( record.name.indexOf( input ) != -1 )
					control.push(
						createCategory(
							isChecked,
							record.name,
							String( record.id )
						)
					);
			}
			// 検索文字列が無い場合は全て追加
			else {
				control.push(
					createCategory(
						isChecked,
						record.name,
						String( record.id )
					)
				);
			}
		} );

		if ( showAllCats ) {
			control = <Disabled> { control } </Disabled>;
		}
		return control;
	}

	// 可変コントロールの定義
	let catsTextControl = (
		<Fragment>
			<TextControl
				label={ __(
					'表示するカテゴリーをカンマ区切りで指定',
					THEME_NAME
				) }
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
				{ createCategoryList( catSearchInput ) }
			</PanelBody>
			<ToggleControl
				label={ __(
					'子カテゴリーの内容を含めて表示',
					THEME_NAME
				) }
				checked={ children }
				onChange={ ( isChecked ) =>
					setAttributes( { children: isChecked } )
				}
			/>
		</Fragment>
	);
	if ( showAllCats ) {
		catsTextControl = <Disabled>{ catsTextControl }</Disabled>;
	}

	let tagsTextControl = (
		<TextControl
			label={ __( 'タグ', THEME_NAME ) }
			value={ tags }
			onChange={ ( value ) => setAttributes( { tags: value } ) }
		/>
	);
	if ( showAllTags ) {
		tagsTextControl = <Disabled>{ tagsTextControl } </Disabled>;
	}

	const getNewListContent = () => {
		return (
			<ServerSideRender block={ props.name } attributes={ attributes } />
		);
	};

	return (
		<Fragment>
			<InspectorControls>
				<PanelBody
					title={ __( '基本設定', THEME_NAME ) }
					initialOpen={ true }
				>
					<NumberControl
						label={ __( '表示する記事の数', THEME_NAME ) }
						isShiftStepEnabled={ false }
						value={ count }
						onChange={ ( newValue ) =>
							setAttributes( { count: newValue } )
						}
						min={ 0 }
					/>
					<SelectControl
						label={ __( '表示タイプ', THEME_NAME ) }
						value={ type }
						onChange={ ( newType ) =>
							setAttributes( { type: newType } )
						}
						options={ [
							{
								label: __( '通常のリスト', THEME_NAME ),
								value: 'default',
							},
							{
								label: __(
									'カードの上下に区切り線を入れる',
									THEME_NAME
								),
								value: 'border_partition',
							},
							{
								label: __(
									'カードに枠線を表示する',
									THEME_NAME
								),
								value: 'border_square',
							},
							{
								label: __( '大きなサムネイル表示', THEME_NAME ),
								value: 'large_thumb',
							},
							{
								label: __(
									'大きなサムネイルにタイトルを重ねる',
									THEME_NAME
								),
								value: 'large_thumb_on',
							},
						] }
					/>
					<SelectControl
						label={ __( '表示順', THEME_NAME ) }
						value={ modified ? '1' : '0' }
						options={ [
							{
								label: __( '更新日順', THEME_NAME ),
								value: '1',
							},
							{
								label: __( '投稿日順', THEME_NAME ),
								value: '0',
							},
						] }
						onChange={ ( value ) => {
							if ( value === '0' ) {
								setAttributes( { modified: false } );
							} else {
								setAttributes( { modified: true } );
							}
						} }
					/>
					<SelectControl
						label={ __( '並び替え', THEME_NAME ) }
						value={ order }
						options={ [
							{ label: __( '昇順', THEME_NAME ), value: 'asc' },
							{ label: __( '降順', THEME_NAME ), value: 'desc' },
						] }
						onChange={ ( value ) => {
							setAttributes( { order: value } );
						} }
					/>
					<NumberControl
						label={ __( '読み飛ばし', THEME_NAME ) }
						isShiftStepEnabled={ false }
						value={ offset }
						onChange={ ( newValue ) =>
							setAttributes( { offset: newValue } )
						}
						min={ 0 }
					/>
					<Divider />
					<ToggleControl
						label={ __( '記事タイトルを太字にする', THEME_NAME ) }
						checked={ bold }
						onChange={ ( isChecked ) =>
							setAttributes( { bold: isChecked } )
						}
					/>
					<ToggleControl
						label={ __( 'カードに矢印を表示する', THEME_NAME ) }
						checked={ arrow }
						onChange={ ( isChecked ) =>
							setAttributes( { arrow: isChecked } )
						}
					/>
					<ToggleControl
						label={ __( '横並び表示', THEME_NAME ) }
						checked={ horizontal }
						onChange={ ( isChecked ) =>
							setAttributes( { horizontal: isChecked } )
						}
					/>
					<ToggleControl
						label={ __( '説明文の表示', THEME_NAME ) }
						checked={ snippet }
						onChange={ ( isChecked ) =>
							setAttributes( { snippet: isChecked } )
						}
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'フィルタ', THEME_NAME ) }
					initialOpen={ false }
				>
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
					<Divider />
					<ToggleControl
						label={ __( '全タグ表示', THEME_NAME ) }
						checked={ showAllTags }
						onChange={ ( isChecked ) => {
							setAttributes( { showAllTags: isChecked } );
						} }
					/>
					{ tagsTextControl }
					<Divider />
					<ToggleControl
						label={ __( '固定記事の表示', THEME_NAME ) }
						checked={ sticky }
						onChange={ ( isChecked ) =>
							setAttributes( { sticky: isChecked } )
						}
					/>
					<TextControl
						label={ __( '投稿タイプ', THEME_NAME ) }
						value={ post_type }
						onChange={ ( newValue ) =>
							setAttributes( { post_type: newValue } )
						}
					/>
					<TextControl
						label={ __( '検索グループ', THEME_NAME ) }
						value={ taxonomy }
						onChange={ ( newValue ) =>
							setAttributes( { taxonomy: newValue } )
						}
					/>
					<TextControl
						label={ __( '投稿ユーザ', THEME_NAME ) }
						value={ author }
						onChange={ ( newValue ) =>
							setAttributes( { author: newValue } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>{ getNewListContent() }</div>
		</Fragment>
	);
}
