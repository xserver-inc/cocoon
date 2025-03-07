import { __ } from '@wordpress/i18n';
import { createHigherOrderComponent } from '@wordpress/compose';
import { addFilter } from '@wordpress/hooks';
import { Fragment } from '@wordpress/element';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl } from '@wordpress/components';

// グループブロックにリンクURLとターゲットを追加
const withGroupBlockLink = createHigherOrderComponent((BlockEdit) => {
    return (props) => {
        if (props.name !== "core/group") return <BlockEdit {...props} />;

        const { attributes, setAttributes } = props;
        const url = attributes.customGroupLink || "";
        const target = attributes.customGroupLinkTarget || "_self"; // デフォルトは"_self"

        return (
            <Fragment>
            <BlockEdit {...props} />
            <InspectorControls>
                <PanelBody title={__('グループブロックのリンク設定', 'THEME_NAME')}>
                <TextControl
                    label={__('リンクURL', 'THEME_NAME')}
                    value={url}
                    onChange={(newUrl) => setAttributes({ customGroupLink: newUrl })}
                    placeholder="https://example.com"
                />
                <SelectControl
                    label={__('リンクターゲット', 'THEME_NAME')}
                    value={target}
                    options={[
                    { label: __('同じタブで開く', 'THEME_NAME'), value: "_self" },
                    { label: __('新しいタブで開く', 'THEME_NAME'), value: "_blank" },
                    ]}
                    onChange={(newTarget) => setAttributes({ customGroupLinkTarget: newTarget })}
                />
                </PanelBody>
            </InspectorControls>
            </Fragment>
        );
    };
}, "withGroupBlockLink");

addFilter("editor.BlockEdit", "cgb/group-block-link", withGroupBlockLink);

// 保存処理を拡張
const addGroupBlockAttributes = (settings) => {
    if (settings.name !== "core/group") return settings;

    return {
        ...settings,
        attributes: {
            ...settings.attributes,
            customGroupLink: { type: "string" },
            customGroupLinkTarget: { type: "string", default: "_self" }, // target属性を追加
        },
    };
};

addFilter("blocks.registerBlockType", "cgb/group-block-attributes", addGroupBlockAttributes);

// HTMLのカスタマイズ
const withSaveGroupBlock = (extraProps, blockType, attributes) => {
    if (blockType.name !== "core/group") return extraProps;

    if (attributes.customGroupLink) {
        extraProps["data-url"] = attributes.customGroupLink; // data-urlだけ追加
    }

    if (attributes.customGroupLinkTarget) {
        extraProps["target"] = attributes.customGroupLinkTarget; // target属性も追加
    }

    return extraProps;
};

addFilter("blocks.getSaveContent.extraProps", "cgb/group-block-save", withSaveGroupBlock);
