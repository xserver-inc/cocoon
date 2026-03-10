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

  // ==========================================================================
  // フォーマット: 下線（span.span-underline）
  // ==========================================================================
  richText.registerFormatType('silk/span-underline', {
    title: '下線',
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
        title: '下線',
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
      options: [{ label: '選択する', value: '' }].concat(
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
          { label: '選択する', value: '' },
          { label: '広告', value: '[ad]' },
          { label: '新着記事一覧', value: '[new_list count="5" type="default" cats="all" children="0" post_type="post"]' },
          { label: '人気記事一覧', value: '[popular_list days="all" rank="0" pv="0" count="5" type="default" cats="all"]' },
          { label: 'ナビカード一覧', value: '[navi_list name="メニュー名" type="default" bold="0" arrow="0"]' },
          { label: 'プロフィール', value: '[author_box label="この記事を書いた人"]' },
          { label: 'Amazonリンク', value: '[amazon asin="ASIN" kw="キーワード"]' },
          { label: 'Amazonリンク（商品名変更）', value: '[amazon asin="ASIN" title="商品名" kw="キーワード"]' },
          { label: 'Amazonリンク（ボタン非表示）', value: '[amazon asin="ASIN" kw="キーワード" amazon=0 rakuten=0 yahoo=0]' },
          { label: '楽天リンク', value: '[rakuten id="ID" kw="キーワード"]' },
          { label: '楽天リンク（商品名変更）', value: '[rakuten id="ID" title="商品名" kw="キーワード"]' },
          { label: '楽天リンク（ボタン非表示）', value: '[rakuten id="ID" kw="キーワード" amazon=0 rakuten=0 yahoo=0]' },
          { label: '過去日時', value: '[ago from="YYYY/MM/DD"]' },
          { label: '過去日時（年）', value: '[yago from="YYYY/MM/DD"]' },
          { label: '年齢', value: '[age birth="YYYY/MM/DD"]' },
          { label: 'カウントダウン', value: '[countdown to="YYYY/MM/DD"]' },
          { label: '評価スター', value: '[star rate="3.7" max="5" number="1"]' },
          { label: 'ログインコンテンツ', value: '[login_user_only msg="こちらのコンテンツはログインユーザーのみに表示されます。"]内容[/login_user_only]' },
        ];

        var scChange = function (value) {
          props.setAttributes({ text: value });
        };

        var settings = [
          el(components.SelectControl, {
            label: '汎用',
            value: props.attributes.text,
            options: general,
            onChange: scChange,
          }),
        ];

        // gbSettings はメインテーマの init.php から wp_localize_script で注入される
        // silk スキン以外でも使用されるグローバル変数のため、存在チェックが必要
        if (typeof gbSettings !== 'undefined') {
          if (Number(gbSettings.isTemplateVisible) && typeof gbTemplates !== 'undefined') {
            settings.push(selectComponent('テンプレート', props.attributes.text, gbTemplates, 'temp', scChange));
          }
          if (Number(gbSettings.isAffiliateVisible) && typeof gbAffiliateTags !== 'undefined') {
            settings.push(selectComponent('アフィリエイトタグ', props.attributes.text, gbAffiliateTags, 'affi', scChange));
          }
          if (Number(gbSettings.isRankingVisible) && typeof gbItemRankings !== 'undefined') {
            settings.push(selectComponent('ランキング', props.attributes.text, gbItemRankings, 'rank', scChange));
          }
        }

        return el(
          Fragment, {},
          el(blockEditor.InspectorControls, {},
            el(components.PanelBody, { title: 'ショートコード設定' }, settings)
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
            el(components.PanelBody, { title: 'ショートコード設定' },
              typeof gbAffiliateTags !== 'undefined'
                ? selectComponent('アフィリエイトタグ', props.attributes.tag, gbAffiliateTags, 'affi', bwChange)
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
            placeholder: '検索ワードを入力する',
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
            title: 'URL検索',
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
    title: 'URL検索',
    tagName: 'url',
    className: null,
    edit: BlogCardURLWrapper,
  });

})();
