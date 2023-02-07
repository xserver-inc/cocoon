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
	__experimentalNumberControl as NumberControl,
	__experimentalDivider as Divider,
	Disabled,
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/editor';

/**
 * Internal dependencies
 */
import { THEME_NAME } from '../../helpers';

export default function edit( props ) {
	const { attributes, setAttributes, className } = props;
	const {
		showAllDays,
		days,
		rank,
		pv,
		post_type,
		count,
		displayType,
		bold,
		arrow,
		showAllCats,
		cats,
		children,
		horizontal,
	} = attributes;

	const classes = classnames( 'popular-list-box', 'block-box', {
		[ className ]: !! className,
		[ attributes.className ]: !! attributes.className,
	} );
	setAttributes( { classNames: classes } );

	// 可変コントロールの定義
	let catsTextControl = (
		<TextControl
			label={ __( '表示するカテゴリをカンマ区切りで指定', THEME_NAME ) }
			value={ cats }
			onChange={ ( value ) => setAttributes( { cats: value } ) }
		/>
	);
	if ( showAllCats ) {
		catsTextControl = <Disabled>{ catsTextControl }</Disabled>;
	}

	let daysNumberControl = (
		<NumberControl
			label={ __( 'データの集計期間(日)', THEME_NAME ) }
			isShiftStepEnabled={ false }
			value={ days }
			onChange={ ( newValue ) => setAttributes( { days: newValue } ) }
			min={ 0 }
		/>
	);
	if ( showAllDays ) {
		daysNumberControl = <Disabled>{ daysNumberControl }</Disabled>;
	}

	const getPopularListContent = () => {
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
						value={ displayType }
						onChange={ ( newType ) =>
							setAttributes( { displayType: newType } )
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
						label={ __( 'ランキング番号表示', THEME_NAME ) }
						checked={ rank }
						onChange={ ( isChecked ) => {
							setAttributes( { rank: isChecked } );
						} }
					/>
					<ToggleControl
						label={ __( 'PV数表示', THEME_NAME ) }
						checked={ pv }
						onChange={ ( isChecked ) => {
							setAttributes( { pv: isChecked } );
						} }
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
					{ catsTextControl }
					<ToggleControl
						label={ __(
							'子カテゴリの内容を含めて表示',
							THEME_NAME
						) }
						checked={ children }
						onChange={ ( isChecked ) =>
							setAttributes( { children: isChecked } )
						}
					/>
					<Divider />
					<TextControl
						label={ __( '投稿タイプ', THEME_NAME ) }
						value={ post_type }
						onChange={ ( newValue ) =>
							setAttributes( { post_type: newValue } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>{ getPopularListContent() }</div>
		</Fragment>
	);
}
