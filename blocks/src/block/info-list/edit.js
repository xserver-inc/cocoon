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
	__experimentalNumberControl as NumberControl,
	__experimentalDivider as Divider,
	Disabled,
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';
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

	const categoryData = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords( 'taxonomy', 'category' );
	} );

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

	function createCategoryList() {
		if ( categoryData == null ) return null;

		let control = [];
		categoryData.forEach( ( record ) => {
			let isChecked = false;
			let catsArray = cats.split( ',' );
			for ( var i = 0; i < catsArray.length; i++ ) {
				if ( catsArray[ i ] == String( record.id ) ) {
					isChecked = true;
					break;
				}
			}
			control.push(
				createCategory( isChecked, record.name, String( record.id ) )
			);
		} );
		if ( showAllCats ) {
			control = <Disabled> { control } </Disabled>;
		}
		return control;
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
							setAttributes( { showAllCats: isChecked } );
						} }
					/>
					<PanelBody title={ __( '表示カテゴリ', THEME_NAME ) }>
						{ createCategoryList() }
					</PanelBody>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>{ getInfoListContent() }</div>
		</Fragment>
	);
}
