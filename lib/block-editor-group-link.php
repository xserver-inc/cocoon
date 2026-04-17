<?php // グループブロックの全体リンク化機能
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

// カスタム属性の登録
add_filter( 'register_block_type_args', 'cocoon_group_block_link_register_attributes', 10, 2 );
if ( !function_exists( 'cocoon_group_block_link_register_attributes' ) ):
function cocoon_group_block_link_register_attributes( $args, $block_type ) {
	if ( 'core/group' === $block_type ) {
		// すでに attributes が配列でない場合への備え
		if ( ! isset( $args['attributes'] ) ) {
			$args['attributes'] = array();
		}
		$args['attributes']['cocoonLinkUrl'] = array(
			'type'    => 'string',
			'default' => '',
		);
		$args['attributes']['cocoonLinkTarget'] = array(
			'type'    => 'boolean',
			'default' => false,
		);
	}
	return $args;
}
endif;

// フロントエンドでのレンダリング時の属性追加
add_filter( 'render_block', 'cocoon_group_block_link_render', 10, 2 );
if ( !function_exists( 'cocoon_group_block_link_render' ) ):
function cocoon_group_block_link_render( $block_content, $block ) {
	if ( 'core/group' !== $block['blockName'] ) {
		return $block_content;
	}

	// ブロックコンテンツが空の場合は処理をスキップ
	if ( empty( $block_content ) ) {
		return $block_content;
	}

	// ブロック属性が未設定の場合への備え
	$attrs = isset( $block['attrs'] ) ? $block['attrs'] : array();
	$url   = isset( $attrs['cocoonLinkUrl'] ) ? trim( $attrs['cocoonLinkUrl'] ) : '';

	if ( empty( $url ) ) {
		return $block_content;
	}

	$new_tab = isset( $attrs['cocoonLinkTarget'] ) && $attrs['cocoonLinkTarget'];

	// WP 6.2 未満の場合は処理をスキップ（WP_HTML_Tag_Processor が存在しないため Fatal Error になるのを防ぐ）
	if ( ! function_exists( 'is_wp_6_2_or_over' ) || ! is_wp_6_2_or_over() || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $block_content;
	}

	$processor = new WP_HTML_Tag_Processor( $block_content );
	if ( $processor->next_tag() ) {
		$processor->set_attribute( 'data-cocoon-group-link', esc_url( $url ) );
		if ( $new_tab ) {
			$processor->set_attribute( 'data-cocoon-group-link-target', '_blank' );
		}
		// アクセシビリティ用の属性を追加
		// role="link" + tabindex="0" により、ブロック内テキストがアクセシブルネームとなる
		// aria-label に生 URL を設定するとスクリーンリーダーが URL を1文字ずつ読み上げるため使用しない（WCAG 2.4.6）
		$processor->set_attribute( 'role', 'link' );
		$processor->set_attribute( 'tabindex', '0' );

		// キーボード用のハンドラを追加（WCAG 2.1 SC 2.1.1 対応）
		// 【設計意図】キーボード操作は PHP 側のインライン onkeydown で this.click() を呼び、
		// js/group-link.js 側の click イベントリスナーに処理を委譲する。
		// これにより安全チェック（プロトコル検証・インタラクティブ要素判定）の
		// ロジック重複を避けている。JS 側に keydown リスナーを移動しないこと。
		$processor->set_attribute( 'onkeydown', "if(event.target === this && (event.key === 'Enter' || event.key === ' ')){ event.preventDefault(); this.click(); }" );

		// フロントエンド用のクラスを追加
		$processor->add_class( 'is-cocoon-group-link' );
	}

	return $processor->get_updated_html();
}
endif;
