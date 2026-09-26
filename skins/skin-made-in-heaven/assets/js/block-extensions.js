(function (wp) {
  // WordPressが用意している共通の機能を変数に代入
  var addFilter = wp.hooks.addFilter;
  var createHigherOrderComponent = wp.compose.createHigherOrderComponent;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var PanelBody = wp.components.PanelBody;
  var ButtonGroup = wp.components.ButtonGroup;
  var Button = wp.components.Button;
  var el = wp.element.createElement;

  // 対象とするブロック名を一括管理するリスト
  var targetBlocks = ['cocoon-blocks/faq', 'cocoon-blocks/timeline'];

  // ブロックに新しい属性を追加
  addFilter(
    'blocks.registerBlockType',
    'hvn/block-header-attributes',
    function (settings, name) {
      // 対象ブロック以外の処理除外
      if (targetBlocks.indexOf(name) === -1) {
        return settings;
      }

      var currentAttributes = settings.attributes || {};

      // 新しい共通属性（hvnHeadingTag）の結合
      var newAttributes = Object.assign({}, currentAttributes, {
        hvnHeadingTag: { type: 'string', default: '' }
      });

      return Object.assign({}, settings, { attributes: newAttributes });
    }
  );

  // エディターの画面に独自の設定ボタンを挿入
  var withBlockHeaderControls = createHigherOrderComponent(function (BlockEdit) {
    return function (props) {
      // 対象ブロック以外の処理除外
      if (targetBlocks.indexOf(props.name) === -1) {
        return el(BlockEdit, props);
      }

      // ブロックが保持している現在の属性を取得
      var attributes = props.attributes || {};
      var setAttributes = props.setAttributes;

      // 共通化された属性名とパネルのタイトルをセット
      var currentValue = attributes.hvnHeadingTag || '';

      var buttons = [];
      var buttonItems = [
        { tag: '', label: 'div' },
        { tag: 'h2', label: 'h2' },
        { tag: 'h3', label: 'h3' },
        { tag: 'h4', label: 'h4' },
        { tag: 'h5', label: 'h5' },
        { tag: 'h6', label: 'h6' }
      ];

      // ボタンを作成
      buttonItems.forEach(function (item) {
        buttons.push(
          el(
            Button,
            {
              key: item.tag || 'default',
              // 現在保存されている値とこのボタンが一致したら青色
              isPrimary: currentValue === item.tag,

              // 一致していなければ白色
              isSecondary: currentValue !== item.tag,

              // ボタンがクリックされたときの処理
              onClick: function () {
                // クリックされたタグをセット
                setAttributes({ hvnHeadingTag: item.tag });
              }
            },
            item.label
          )
        );
      });

      // 最終的にエディター画面にレンダリングするHTMLを出力
      return el(
        wp.element.Fragment,
        null,
        el(BlockEdit, props),
        el(
          InspectorControls,
          null,
          el(
            // サイドパネルの中に折りたたみパネルを作成
            PanelBody,
            { title: '見出しタグ設定', initialOpen: true },
            el(ButtonGroup, null, buttons)
          )
        )
      );
    };
  }, 'withBlockHeaderControls');

  // 作成したサイドパネル拡張
  addFilter('editor.BlockEdit', 'hvn/block-header-controls', withBlockHeaderControls);
})(window.wp);
