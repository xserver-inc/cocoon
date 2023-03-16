import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	InnerBlocks,
	withColors,
	withFontSizes,
	useBlockProps,
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
	__experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
} from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import classnames from 'classnames';

export function BlankBoxEdit( props ) {
	const {
		className,
		backgroundColor,
		setBackgroundColor,
		textColor,
		setTextColor,
		borderColor,
		setBorderColor,
		fontSize,
		clientId,
	} = props;

	const classes = classnames( className, {
		'blank-box': true,
		'block-box': true,
		'has-text-color': textColor.color,
		'has-background': backgroundColor.color,
		'has-border-color': borderColor.color,
		[ backgroundColor.class ]: backgroundColor.class,
		[ textColor.class ]: textColor.class,
		[ borderColor.class ]: borderColor.class,
		[ fontSize.class ]: fontSize.class,
	} );

	const blockProps = useBlockProps( {
		className: classes,
	} );

	return (
		<Fragment>
			<InspectorControls group="color">
				<ColorGradientSettingsDropdown
					enableAlpha={ false }
					panelId={ clientId }
					settings={ [
						{
							label: __( 'ボーダー色', THEME_NAME ),
							onColorChange: setBorderColor,
							colorValue: borderColor.color,
						},
						{
							label: __( '背景色', THEME_NAME ),
							onColorChange: setBackgroundColor,
							colorValue: backgroundColor.color,
						},
						{
							label: __( '文字色', THEME_NAME ),
							onColorChange: setTextColor,
							colorValue: textColor.color,
						},
					] }
					__experimentalIsItemGroup={ true }
					__experimentalIsRenderedInSidebar
					{ ...useMultipleOriginColorsAndGradients() }
				/>
			</InspectorControls>

			<div { ...blockProps }>
				<InnerBlocks />
			</div>
		</Fragment>
	);
}

export default compose( [
	withColors( 'backgroundColor', {
		textColor: 'color',
		borderColor: 'border-color',
		iconColor: 'icon-color',
	} ),
	withFontSizes( 'fontSize' ),
] )( BlankBoxEdit );
