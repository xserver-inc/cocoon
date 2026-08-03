/**
 * Cocoon WordPress Theme - Silk Skin
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 *
 * ブロックエディター拡張（enqueue_block_editor_assets 経由）。
 * 書式タイプ登録・ブロックフィルター追加はエディター UI 層の操作であり、
 * iframe の外側で実行される。
 */
/* global gbAffiliateTags, gbItemRankings, gbSettings, gbTemplates */
(function () {
  'use strict';

  var richText    = wp.richText;
  var el          = wp.element.createElement;
  var Fragment    = wp.element.Fragment;
  var useState    = wp.element.useState;
  var useSelect   = wp.data.useSelect;
  var blockEditor = wp.blockEditor;
  var components  = wp.components;
  var primitives  = wp.primitives;
  var compose     = wp.compose;
  var hooks       = wp.hooks;
  var __          = wp.i18n.__;
  var textDomain  = 'cocoon';

  // ==========================================================================
  // フォーマット: 下線（span.span-underline）
  // ==========================================================================
  richText.registerFormatType('silk/span-underline', {
    title: __('下線', textDomain),
    tagName: 'span',
    className: 'span-underline',
    edit: function (props) {
      return el(blockEditor.RichTextToolbarButton, {
        icon: el(
          primitives.SVG,
          { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24' },
          el(primitives.Path, {
            d: 'M7 18v1h10v-1H7zm5-2c1.5 0 2.6-.4 3.4-1.2.8-.8 1.1-2 1.1-3.5V5H15v5.8c0 1.2-.2 2.1-.6 2.8-.4.7-1.2 1-2.4 1s-2-.3-2.4-1c-.4-.7-.6-1.6-.6-2.8V5H7.5v6.2c0 1.5.4 2.7 1.1 3.5.8.9 1.9 1.3 3.4 1.3z',
          })
        ),
        title: __('下線', textDomain),
        onClick: function () {
          props.onChange(
            richText.toggleFormat(props.value, { type: 'silk/span-underline' })
          );
        },
        isActive: props.isActive,
      });
    },
  });

  // ==========================================================================
  // ユーティリティ: SelectControl ラッパー
  // ==========================================================================
  function selectComponent(label, value, options, tag, onChange) {
    return el(components.SelectControl, {
      label: label,
      value: value,
      options: [{ label: __('選択する', textDomain), value: '' }].concat(
        options
          .map(function (setting) {
            if (Number(setting.visible)) {
              return {
                label: setting.title,
                value: '[' + tag + ' id=' + setting.id + ']',
              };
            }
          })
          .filter(Boolean)
      ),
      onChange: onChange,
    });
  }

  // ==========================================================================
  // ブロックフィルター: ショートコードブロックにセレクター追加
  // ==========================================================================
  var addShortCodeControl = compose.createHigherOrderComponent(
    function (BlockEdit) {
      return function (props) {
        if (props.name !== 'core/shortcode' || !props.isSelected) {
          return el(BlockEdit, props);
        }

        var general = [
          { label: __('選択する', textDomain), value: '' },
          { label: __('広告', textDomain), value: '[ad]' },
          { label: __('新着記事一覧', textDomain), value: '[new_list count="5" type="default" cats="all" children="0" post_type="post"]' },
          { label: __('人気記事一覧', textDomain), value: '[popular_list days="all" rank="0" pv="0" count="5" type="default" cats="all"]' },
          { label: __('ナビカード一覧', textDomain), value: '[navi_list name="' + __('メニュー名', textDomain) + '" type="default" bold="0" arrow="0"]' },
          { label: __('プロフィール', textDomain), value: '[author_box label="' + __('この記事を書いた人', textDomain) + '"]' },
          { label: __('Amazonリンク', textDomain), value: '[amazon asin="ASIN" kw="' + __('キーワード', textDomain) + '"]' },
          { label: __('Amazonリンク（商品名変更）', textDomain), value: '[amazon asin="ASIN" title="' + __('商品名', textDomain) + '" kw="' + __('キーワード', textDomain) + '"]' },
          { label: __('Amazonリンク（ボタン非表示）', textDomain), value: '[amazon asin="ASIN" kw="' + __('キーワード', textDomain) + '" amazon=0 rakuten=0 yahoo=0]' },
          { label: __('楽天リンク', textDomain), value: '[rakuten id="ID" kw="' + __('キーワード', textDomain) + '"]' },
          { label: __('楽天リンク（商品名変更）', textDomain), value: '[rakuten id="ID" title="' + __('商品名', textDomain) + '" kw="' + __('キーワード', textDomain) + '"]' },
          { label: __('楽天リンク（ボタン非表示）', textDomain), value: '[rakuten id="ID" kw="' + __('キーワード', textDomain) + '" amazon=0 rakuten=0 yahoo=0]' },
          { label: __('過去日時', textDomain), value: '[ago from="YYYY/MM/DD"]' },
          { label: __('過去日時（年）', textDomain), value: '[yago from="YYYY/MM/DD"]' },
          { label: __('年齢', textDomain), value: '[age birth="YYYY/MM/DD"]' },
          { label: __('カウントダウン', textDomain), value: '[countdown to="YYYY/MM/DD"]' },
          { label: __('評価スター', textDomain), value: '[star rate="3.7" max="5" number="1"]' },
          { label: __('ログインコンテンツ', textDomain), value: '[login_user_only msg="' + __('こちらのコンテンツはログインユーザーのみに表示されます。', textDomain) + '"]' + __('内容', textDomain) + '[/login_user_only]' },
        ];

        var scChange = function (value) {
          props.setAttributes({ text: value });
        };

        var settings = [
          el(components.SelectControl, {
            label: __('汎用', textDomain),
            value: props.attributes.text,
            options: general,
            onChange: scChange,
          }),
        ];

        // gbSettings はメインテーマの init.php から wp_localize_script で注入される
        // silk スキン以外でも使用されるグローバル変数のため、存在チェックが必要
        if (typeof gbSettings !== 'undefined') {
          if (Number(gbSettings.isTemplateVisible) && typeof gbTemplates !== 'undefined') {
            settings.push(selectComponent(__('テンプレート', textDomain), props.attributes.text, gbTemplates, 'temp', scChange));
          }
          if (Number(gbSettings.isAffiliateVisible) && typeof gbAffiliateTags !== 'undefined') {
            settings.push(selectComponent(__('アフィリエイトタグ', textDomain), props.attributes.text, gbAffiliateTags, 'affi', scChange));
          }
          if (Number(gbSettings.isRankingVisible) && typeof gbItemRankings !== 'undefined') {
            settings.push(selectComponent(__('ランキング', textDomain), props.attributes.text, gbItemRankings, 'rank', scChange));
          }
        }

        return el(
          Fragment, {},
          el(blockEditor.InspectorControls, {},
            el(components.PanelBody, { title: __('ショートコード設定', textDomain) }, settings)
          ),
          el(BlockEdit, props)
        );
      };
    },
    'addShortCodeControl'
  );

  hooks.addFilter('editor.BlockEdit', 'silk/shortcode', addShortCodeControl);

  // ==========================================================================
  // ブロックフィルター: 囲みボタンブロックにアフィリエイトタグセレクター追加
  // ==========================================================================
  var addButtonWrapControl = compose.createHigherOrderComponent(
    function (BlockEdit) {
      return function (props) {
        if (
          !(typeof gbSettings !== 'undefined' && Number(gbSettings.isAffiliateVisible)) ||
          props.name !== 'cocoon-blocks/button-wrap-1' ||
          !props.isSelected
        ) {
          return el(BlockEdit, props);
        }

        var bwChange = function (value) {
          props.setAttributes({ tag: value });
        };

        return el(
          Fragment, {},
          el(blockEditor.InspectorControls, {},
            el(components.PanelBody, { title: __('ショートコード設定', textDomain) },
              typeof gbAffiliateTags !== 'undefined'
                ? selectComponent(__('アフィリエイトタグ', textDomain), props.attributes.tag, gbAffiliateTags, 'affi', bwChange)
                : null
            )
          ),
          el(BlockEdit, props)
        );
      };
    },
    'addButtonWrapControl'
  );

  hooks.addFilter('editor.BlockEdit', 'silk/button-wrap', addButtonWrapControl);

  // ==========================================================================
  // 書式タイプ: ブログカード URL 検索
  // ==========================================================================

  /**
   * ブログカード URL 検索 UI コンポーネント。
   *
   * 変更点:
   *   - Popover の `position` prop（WP 6.2 以降非推奨）を `placement` に変更
   */
  var BlogCardURL = function (props) {
    var openState  = useState(false);
    var isOpen     = openState[0];
    var setIsOpen  = openState[1];

    var wordState  = useState('');
    var searchWord = wordState[0];
    var setWord    = wordState[1];

    var popover = isOpen
      ? el(
          components.Popover,
          {
            className: 'blogcard-url-search',
            // WP 5.x〜6.1 用（WP 6.2 以降非推奨だが後方互換のため残す）
            position: 'bottom left',
            // WP 6.2+ 用（推奨、存在する場合 position より優先される）
            placement: 'bottom-start',
            onClose: function () {
              setIsOpen(false);
              setWord('');
            },
          },
          el(blockEditor.URLInput, {
            placeholder: __('検索ワードを入力する', textDomain),
            value: searchWord,
            onChange: function (value, post) {
              if (post) {
                props.onChange(
                  richText.insert(props.value, post.url, props.value.start, props.value.end)
                );
                setWord('');
              } else {
                setWord(value);
              }
            },
          })
        )
      : null;

    return el(
      Fragment, {},
      el(blockEditor.BlockControls, {},
        el(components.ToolbarGroup, {},
          el(components.ToolbarButton, {
            name: 'link',
            icon: el(
              primitives.SVG,
              { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24' },
              el(primitives.Path, {
                d: 'M12.5 14.5h-1V16h1c2.2 0 4-1.8 4-4s-1.8-4-4-4h-1v1.5h1c1.4 0 2.5 1.1 2.5 2.5s-1.1 2.5-2.5 2.5zm-4 1.5v-1.5h-1C6.1 14.5 5 13.4 5 12s1.1-2.5 2.5-2.5h1V8h-1c-2.2 0-4 1.8-4 4s1.8 4 4 4h1zm-1-3.2h5v-1.5h-5v1.5zM18 4H9c-1.1 0-2 .9-2 2v.5h1.5V6c0-.3.2-.5.5-.5h9c.3 0 .5.2.5.5v12c0 .3-.2.5-.5.5H9c-.3 0-.5-.2-.5-.5v-.5H7v.5c0 1.1.9 2 2 2h9c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z',
              })
            ),
            title: __('URL検索', textDomain),
            onClick: function () {
              setIsOpen(true);
              return false;
            },
          })
        )
      ),
      popover
    );
  };

  /**
   * ブログカードブロック選択時のみ BlogCardURL を表示するラッパー。
   *
   * 変更点:
   *   - withSelect + ifCondition（Gutenberg 16.x で削除済み）を
   *     useSelect を使ったシンプルな条件分岐コンポーネントに置き換え
   */
  var BlogCardURLWrapper = function (props) {
    // useSelect で現在選択中のブロックを取得する
    // （旧 withSelect + ifCondition パターンの代替）
    var selectedBlock = useSelect(function (select) {
      return select('core/block-editor').getSelectedBlock();
    }, []);

    // ブログカードブロックが選択されていない場合は何も表示しない
    if (!selectedBlock || selectedBlock.name !== 'cocoon-blocks/blogcard') {
      return null;
    }

    return el(BlogCardURL, props);
  };

  richText.registerFormatType('silk/blogcard-url', {
    title: __('URL検索', textDomain),
    tagName: 'url',
    className: null,
    edit: BlogCardURLWrapper,
  });

})();
