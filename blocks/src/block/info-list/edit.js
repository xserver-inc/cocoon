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
	CheckboxControl,
	SearchControl,
	__experimentalNumberControl as NumberControl,
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
	const { count, showAllCats, cats, caption, showFrame, showDivider } =
		attributes;

	const classes = classnames( 'info-list-box', 'block-box', {
		[ className ]: !! className,
		[ attributes.className ]: !! attributes.className,
	} );
	setAttributes( { classNames: classes } );

	// カテゴリ検索文字列の保持
	const [ catSearchInput, setCatSearchInput ] = useState( '' );

	// wp.coreから全カテゴリ情報の取得
	const categoryData = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords( 'taxonomy', 'category' );
	} );

	// 1カテゴリ分のcatsへの追加・削除およびCheckboxControlの生成
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

	// 入力文字列に応じてカテゴリの一覧を出力する
	function createCategoryList( input ) {
		if ( categoryData == null ) return null;

		let control = [];
		let catsArray = cats.split(',');
		// 検索文字列が空の場合は選択されているカテゴリの一覧を表示する
		if (input == '') {
			categoryData.forEach((record) => {
				for (var i = 0; i < catsArray.length; i++) {
					if (catsArray[i] == String(record.id)) {
						control.push(createCategory(true, record.name, String(record.id)));
					}
				}
			});
		}
		// 検索文字列がある場合は文字列を含むカテゴリの一覧を表示する
		else {
			categoryData.forEach((record) => {
				console.log(record);
				let isChecked = false;
				// record.nameにinputが含まれるなら
				if (record.name.indexOf(input) != -1) {
					for (var i = 0; i < catsArray.length; i++) {
						if (catsArray[i] == String(record.id)) {
							isChecked = true;
							break;
						}
					}
					control.push(
						createCategory(
							isChecked,
							record.name,
							String(record.id)
						)
					);
				}
			});
		}
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
					'表示するカテゴリをカンマ区切りで指定',
					THEME_NAME
				) }
				value={ cats }
				onChange={ ( value ) => setAttributes( { cats: value } ) }
			/>
			<PanelBody title={ __( 'カテゴリ検索', THEME_NAME ) }>
				<SearchControl
					value={ catSearchInput }
					onChange={ setCatSearchInput }
				/>
				{ createCategoryList( catSearchInput ) }
			</PanelBody>
		</Fragment>
	);
	if ( showAllCats ) {
		catsTextControl = <Disabled>{ catsTextControl }</Disabled>;
	}

	const getInfoListContent = () => {
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
					<TextControl
						label={ __( 'キャプション(枠線内表示)', THEME_NAME ) }
						value={ caption }
						onChange={ ( newValue ) =>
							setAttributes( { caption: newValue } )
						}
					/>
					<NumberControl
						label={ __( '表示数', THEME_NAME ) }
						isShiftStepEnabled={ false }
						value={ count }
						onChange={ ( newValue ) =>
							setAttributes( { count: newValue } )
						}
						min={ 0 }
					/>
					<ToggleControl
						label={ __( '枠線表示', THEME_NAME ) }
						checked={ showFrame }
						onChange={ ( isChecked ) =>
							setAttributes( { showFrame: isChecked } )
						}
					/>
					<ToggleControl
						label={ __( '仕切り線表示', THEME_NAME ) }
						checked={ showDivider }
						onChange={ ( isChecked ) =>
							setAttributes( { showDivider: isChecked } )
						}
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'フィルタ', THEME_NAME ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __( '全カテゴリ表示', THEME_NAME ) }
						checked={ showAllCats }
						onChange={ ( isChecked ) => {
							setAttributes({ showAllCats: isChecked });
							// 全カテゴリ表示を切り替えた際は検索文字列をリセット
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
