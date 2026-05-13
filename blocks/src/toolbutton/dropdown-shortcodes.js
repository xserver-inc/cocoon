/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, ShortcodeToolbarButton } from '../helpers.js';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { registerFormatType, insert } from '@wordpress/rich-text';
import { BlockFormatControls } from '@wordpress/block-editor';
import { Slot, ToolbarGroup, ToolbarDropdownMenu } from '@wordpress/components';
import { Icon, shortcode } from '@wordpress/icons';
import { orderBy } from 'lodash';
import { createBlock } from '@wordpress/blocks';
import { useSelect, useDispatch } from '@wordpress/data';
const FORMAT_TYPE_NAME = 'cocoon-blocks/shortcodes';

//ショートコード作成関数
function registerShortcodeFormatType( name, title, code, icon ) {
  var formatType = 'cocoon-blocks/' + name;
  registerFormatType( formatType, {
    title: title,
    tagName: name,
    className: null,
    edit( { value, onChange } ) {
      const onToggle = () =>
        onChange( insert( value, code, value.start, value.end ) );

      return (
        <Fragment>
          <ShortcodeToolbarButton
            icon={ <Icon icon={ shortcode } size={ 32 } /> }
            title={ <span className={ name }>{ title }</span> }
            onClick={ onToggle }
          />
        </Fragment>
      );
    },
  } );
}

// ブロック要素を出力するショートコード作成関数
// 段落ブロック内にテキスト挿入する代わりに、ショートコードブロック（core/shortcode）として挿入する
function registerBlockShortcodeFormatType( name, title, code, icon ) {
  var formatType = 'cocoon-blocks/' + name;
  registerFormatType( formatType, {
    title: title,
    tagName: name,
    className: null,
    edit( { value, onChange } ) {
      // 現在選択中のブロック情報を取得
      const { selectedBlock, blockIndex, rootClientId } = useSelect(
        ( select ) => {
          const editor = select( 'core/block-editor' );
          const block = editor.getSelectedBlock();
          return {
            selectedBlock: block,
            blockIndex: block
              ? editor.getBlockIndex( block.clientId )
              : 0,
            rootClientId: block
              ? editor.getBlockRootClientId( block.clientId )
              : undefined,
          };
        }
      );
      // ブロック挿入・削除用のディスパッチ
      const { insertBlock, removeBlock } = useDispatch(
        'core/block-editor'
      );

      const onToggle = () => {
        // ショートコードブロックを作成して、現在のブロックの後に挿入
        const shortcodeBlock = createBlock( 'core/shortcode', {
          text: code,
        } );
        insertBlock( shortcodeBlock, blockIndex + 1, rootClientId );

        // 元の段落ブロックが空の場合は自動削除
        if (
          selectedBlock &&
          selectedBlock.name === 'core/paragraph' &&
          ! selectedBlock.attributes.content
        ) {
          removeBlock( selectedBlock.clientId );
        }
      };

      return (
        <Fragment>
          <ShortcodeToolbarButton
            icon={ <Icon icon={ shortcode } size={ 32 } /> }
            title={ <span className={ name }>{ title }</span> }
            onClick={ onToggle }
          />
        </Fragment>
      );
    },
  } );
}

//広告
registerBlockShortcodeFormatType( 'shortcode-ad', __( '広告', THEME_NAME ), '[ad]', [
  'fas',
  'ad',
] );
//新着記事一覧
registerBlockShortcodeFormatType(
  'shortcode-new-list',
  __( '新着記事一覧', THEME_NAME ),
  '[new_list count="5" type="default" cats="all" children="0" post_type="post"]',
  [ 'fas', 'th-list' ]
);
//人気記事一覧
registerBlockShortcodeFormatType(
  'shortcode-popular-list',
  __( '人気記事一覧', THEME_NAME ),
  '[popular_list days="all" rank="0" pv="0" count="5" type="default" cats="all"]',
  [ 'fas', 'th-list' ]
);
//ナビカード一覧
registerBlockShortcodeFormatType(
  'shortcode-navi-list',
  __( 'ナビカード一覧', THEME_NAME ),
  '[navi_list name="' +
    __( 'メニュー名', THEME_NAME ) +
    '" type="default" bold="0" arrow="0"]',
  [ 'fas', 'th-list' ]
);
//プロフィール
registerBlockShortcodeFormatType(
  'shortcode-profile',
  __( 'プロフィール', THEME_NAME ),
  '[author_box label="' + __( 'この記事を書いた人', THEME_NAME ) + '"]',
  [ 'fas', 'user-circle' ]
);
//Amazonリンク
registerBlockShortcodeFormatType(
  'shortcode-amazon',
  __( 'Amazonリンク', THEME_NAME ),
  '[amazon asin="ASIN" kw="' + __( 'キーワード', THEME_NAME ) + '"]',
  [ 'fab', 'amazon' ]
);
//Amazonリンク（商品名変更）
registerBlockShortcodeFormatType(
  'shortcode-amazon-title',
  __( 'Amazonリンク（商品名変更）', THEME_NAME ),
  '[amazon asin="ASIN" title="' +
    __( '商品名', THEME_NAME ) +
    '" kw="' +
    __( 'キーワード', THEME_NAME ) +
    '"]',
  [ 'fab', 'amazon' ]
);
//Amazonリンク（ボタン非表示）
registerBlockShortcodeFormatType(
  'shortcode-amazon-no-buttons',
  __( 'Amazonリンク（ボタン非表示）', THEME_NAME ),
  '[amazon asin="ASIN" kw="' +
    __( 'キーワード', THEME_NAME ) +
    '" amazon=0 rakuten=0 yahoo=0]',
  [ 'fab', 'amazon' ]
);
//楽天リンク
registerBlockShortcodeFormatType(
  'shortcode-rakuten',
  __( '楽天リンク', THEME_NAME ),
  '[rakuten id="ID" kw="' + __( 'キーワード', THEME_NAME ) + '"]',
  [ 'fas', 'registered' ]
);
//楽天リンク（商品名変更）
registerBlockShortcodeFormatType(
  'shortcode-rakuten-title',
  __( '楽天リンク（商品名変更）', THEME_NAME ),
  '[rakuten id="ID" title="' +
    __( '商品名', THEME_NAME ) +
    '" kw="' +
    __( 'キーワード', THEME_NAME ) +
    '"]',
  [ 'fas', 'registered' ]
);
//楽天リンク（ボタン非表示）
registerBlockShortcodeFormatType(
  'shortcode-rakuten-no-buttons',
  __( '楽天リンク（ボタン非表示）', THEME_NAME ),
  '[rakuten id="ID" kw="' +
    __( 'キーワード', THEME_NAME ) +
    '" amazon=0 rakuten=0 yahoo=0]',
  [ 'fas', 'registered' ]
);
//過去日時
registerShortcodeFormatType(
  'shortcode-ago',
  __( '過去日時', THEME_NAME ),
  '[ago from="YYYY/MM/DD"]',
  [ 'fas', 'calendar-alt' ]
);
//過去日時（年）
registerShortcodeFormatType(
  'shortcode-yago',
  __( '過去日時（年）', THEME_NAME ),
  '[yago from="YYYY/MM/DD"]',
  [ 'fas', 'calendar-alt' ]
);
//年齢
registerShortcodeFormatType(
  'shortcode-age',
  __( '年齢', THEME_NAME ),
  '[age birth="YYYY/MM/DD"]',
  [ 'fas', 'birthday-cake' ]
);
//ページ読み込み時の日付
registerShortcodeFormatType(
  'shortcode-date',
  __( 'ページ読み込み時の日付', THEME_NAME ),
  '[date format="Y/m/d"]',
  [ 'fas', 'birthday-cake' ]
);
//ページの更新日
registerShortcodeFormatType(
  'shortcode-updated',
  __( 'ページの更新日', THEME_NAME ),
  '[updated format="Y/m/d"]',
  [ 'fas', 'birthday-cake' ]
);
//カウントダウン
registerShortcodeFormatType(
  'shortcode-countdown',
  __( 'カウントダウン', THEME_NAME ),
  '[countdown to="YYYY/MM/DD"]',
  [ 'fas', 'calendar-day' ]
);
//評価スター
registerShortcodeFormatType(
  'shortcode-star',
  __( '評価スター', THEME_NAME ),
  '[star rate="3.7" max="5" number="1"]',
  [ 'fas', 'star' ]
);
//ログインコンテンツ
registerBlockShortcodeFormatType(
  'shortcode-login',
  __( 'ログインコンテンツ', THEME_NAME ),
  '[login_user_only msg="' +
    __(
      'こちらのコンテンツはログインユーザーのみに表示されます。',
      THEME_NAME
    ) +
    '"]' +
    __( '内容', THEME_NAME ) +
    '[/login_user_only]',
  [ 'fas', 'sign-in-alt' ]
);

// //XXXXXXXXX
// registerShortcodeFormatType(
//   'shortcode-',
//   __( 'XXXXXXXXX', THEME_NAME ),
//   '',
//   ['fas', '']
// );

// var name = 'shortcode-ad';
// var title = __( '広告', THEME_NAME );
// var formatType = 'cocoon-blocks/' + name;
// registerFormatType( formatType, {
//   title: title,
//   tagName: name,
//   className: null,
//   edit({value, onChange}){
//     const onToggle = () => onChange( insert( value, '[ad]', value.start, value.end ) );

//     return (
//       <Fragment>
//         <ShortcodeToolbarButton
//           icon={<FontAwesomeIcon icon={['fas', 'ad']} />}
//           title={<span className={name}>{title}</span>}
//           onClick={ onToggle }
//         />
//       </Fragment>
//     );
//   }
// } );

var isGeneralVisible = Number(
  gbSettings[ 'isGeneralVisible' ] ? gbSettings[ 'isGeneralVisible' ] : 0
);
if ( isGeneralVisible ) {
  registerFormatType( FORMAT_TYPE_NAME, {
    title: __( '汎用ショートコード', THEME_NAME ),
    tagName: 'span',
    className: 'shortcodes',
    edit( { isActive, value, onChange } ) {
      return (
        <BlockFormatControls>
          <div className="editor-format-toolbar block-editor-format-toolbar">
            <ToolbarGroup>
              <Slot name="Shortcode.ToolbarControls">
                { ( fills ) =>
                  fills.length !== 0 && (
                    <ToolbarDropdownMenu
                      icon={ <Icon icon={ shortcode } size={ 32 } /> }
                      label={ __( 'ショートコード', THEME_NAME ) }
                      className="shortcodes"
                      controls={ orderBy(
                        fills.map( ( [ { props } ] ) => props ),
                        'title'
                      ) }
                    />
                  )
                }
              </Slot>
            </ToolbarGroup>
          </div>
        </BlockFormatControls>
      );
    },
  } );
}
