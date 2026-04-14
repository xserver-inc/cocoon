import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { Fragment, useState } from '@wordpress/element';
import { BlockControls } from '@wordpress/block-editor';
import { ToolbarGroup, ToolbarButton, Dropdown, TextControl, ToggleControl, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { link, linkOff } from '@wordpress/icons';
import { THEME_NAME } from '../defines';

/**
 * JS側でもカスタム属性を登録（PHP側と二重登録することでバリデーションエラーを防止）
 */
addFilter(
	'blocks.registerBlockType',
	'cocoon/group-link-add-attributes',
	( settings, name ) => {
		if ( name !== 'core/group' ) {
			return settings;
		}
		return {
			...settings,
			attributes: {
				...settings.attributes,
				cocoonLinkUrl: {
					type: 'string',
					default: '',
				},
				cocoonLinkTarget: {
					type: 'boolean',
					default: false,
				},
			},
		};
	}
);

/**
 * グループブロック（core/group）のツールバーにリンク設定用のコントロール（ポップオーバー）を追加するコンポーネントです。
 */
const withGroupLinkControls = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		// グループブロック以外の場合は、そのまま元のブロックを返す
		if ( props.name !== 'core/group' ) {
			return <BlockEdit { ...props } />;
		}

		const { attributes, setAttributes } = props;
		const { cocoonLinkUrl, cocoonLinkTarget } = attributes;
		// ポップオーバー内で編集中の一時的な値を保持するローカルstate
		const [ urlValue, setUrlValue ] = useState( cocoonLinkUrl || '' );
		const [ newTabValue, setNewTabValue ] = useState( !! cocoonLinkTarget );

		// すでにリンクURLが設定されているかを判定
		const hasLink = !! cocoonLinkUrl;

		return (
			<Fragment>
				<BlockEdit { ...props } />
				<BlockControls group="other">
					<ToolbarGroup>
						{ /* ツールバーに追加されるドロップダウンボタンの設定 */ }
						<Dropdown
							popoverProps={{
								placement: 'bottom-start',
								className: 'cocoon-group-link-popover',
							}}
							renderToggle={ ( { isOpen, onToggle } ) => (
								<ToolbarButton
									icon={ hasLink ? linkOff : link }
									label={ hasLink ? __( 'リンク編集', THEME_NAME ) : __( 'リンク追加', THEME_NAME ) }
									onClick={ () => {
										if ( ! isOpen ) {
											setUrlValue( cocoonLinkUrl || '' );
											setNewTabValue( !! cocoonLinkTarget );
										}
										onToggle();
									} }
									isActive={ hasLink }
									aria-expanded={ isOpen }
								/>
							) }
							renderContent={ ( { onClose } ) => (
								<div className="cocoon-group-link-popover__content">
									<TextControl
										label={ __( 'リンクURL', THEME_NAME ) }
										value={ urlValue }
										onChange={ setUrlValue }
										placeholder="https://example.com"
										help={ __( '設定するとグループブロック全体がリンク化されます。', THEME_NAME ) }
										__nextHasNoMarginBottom
									/>
									<ToggleControl
										label={ __( '新しいタブで開く', THEME_NAME ) }
										checked={ newTabValue }
										onChange={ ( checked ) => setNewTabValue( checked ) }
										__nextHasNoMarginBottom
									/>
									<div className="cocoon-group-link-popover__actions">
										<Button
											variant="primary"
											onClick={ () => {
												const safeUrl = urlValue.trim();

												// プロトコルのチェック用に空白と制御文字を除外した文字列を作成
												const sanitizedForCheck = safeUrl.replace( /[\u0000-\u001F\u007F-\u009F\s]+/g, '' ).toLowerCase();
												// javascript:, vbscript:, data: などの危険なプロトコルを除外する
												if (
													sanitizedForCheck.startsWith( 'javascript:' ) ||
													sanitizedForCheck.startsWith( 'vbscript:' ) ||
													sanitizedForCheck.startsWith( 'data:' )
												) {
													// 不正なURLの場合は保存せず、ポップオーバーを閉じずに留める
													setUrlValue( '' );
													return;
												}

												setAttributes( {
													cocoonLinkUrl: safeUrl,
													cocoonLinkTarget: newTabValue,
												} );
												onClose();
											} }
											disabled={ ! urlValue.trim() }
											size="compact"
										>
											{ __( '適用', THEME_NAME ) }
										</Button>
										{ hasLink && (
											<Button
												variant="tertiary"
												onClick={ () => {
													setAttributes( { cocoonLinkUrl: '', cocoonLinkTarget: false } );
													setUrlValue( '' );
													setNewTabValue( false );
													onClose();
												} }
												isDestructive
												size="compact"
											>
												{ __( 'リンク解除', THEME_NAME ) }
											</Button>
										) }
									</div>
								</div>
							) }
						/>
					</ToolbarGroup>
				</BlockControls>
			</Fragment>
		);
	};
}, 'withGroupLinkControls' );

addFilter(
	'editor.BlockEdit',
	'cocoon/with-group-link-controls',
	withGroupLinkControls
);

/**
 * エディター上で、リンク設定済みのグループブロックに対して視覚的なインジケーター（点線枠とリンクアイコン）を追加する処理です。
 */
const withGroupLinkIndicator = createHigherOrderComponent( ( BlockListBlock ) => {
	return ( props ) => {
		// グループブロック以外の場合はそのまま返す
		if ( props.name !== 'core/group' ) {
			return <BlockListBlock { ...props } />;
		}

		const { cocoonLinkUrl } = props.attributes;

		// リンクが設定されていない場合はそのまま返す
		if ( ! cocoonLinkUrl ) {
			return <BlockListBlock { ...props } />;
		}

		// リンク設定済みの場合、エディターでの視覚的な表示用のCSSクラスを追加する
		let extraClass = 'is-cocoon-group-link editor-cocoon-group-link-active';
		const className = props.className
			? props.className + ' ' + extraClass
			: extraClass;

		return <BlockListBlock { ...props } className={ className } />;
	};
}, 'withGroupLinkIndicator' );

addFilter(
	'editor.BlockListBlock',
	'cocoon/with-group-link-indicator',
	withGroupLinkIndicator
);
