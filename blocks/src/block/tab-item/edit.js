import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import classnames from 'classnames';
import memoize from 'memize';
import {subscribe} from '@wordpress/data';
import {TextControl} from '@wordpress/components';

export default function edit( props ) {
  const {clientId, attributes, setAttributes, className } = props;
  const { title, id } = attributes;
  const parentId = wp.data.select('core/block-editor').getBlockParentsByBlockName(clientId, ['cocoon-blocks/tab']);

  // 自身がparentIdの中のブロックのうち何番目かを取得し、ブロックのidとする
  const currentId = wp.data.select('core/block-editor').getBlockOrder(parentId).indexOf(clientId);

  // attributesのidを格納
  var savedId = id;

  // 状態変化時にコールされる関数を登録
  const updateId = subscribe(() => {
      var newId = wp.data.select('core/block-editor').getBlockOrder(parentId).indexOf(clientId);

      // idに変化があった場合はattributeを更新する
      if (savedId !== newId) {
        // 再帰呼出し
        updateId();
        setAttributes({id: newId});
        // 親ブロックに変化を通知する
        wp.data.dispatch('core/block-editor').updateBlockAttributes(parentId, {needUpdate: true});
      }
  });

  // タイトルとIDを更新
  const onChangeTitle = newTitle => {
    setAttributes({title: newTitle});
    setAttributes({id: currentId});
      // 親ブロックに変化を通知する
      wp.data.dispatch('core/block-editor').updateBlockAttributes(parentId, {needUpdate: true});
  }

  const classes = classnames( className, {
    'tab-content': true,
  });

  const blockProps = useBlockProps( {
    className: classes,
  });

  return (
    <Fragment>
      <div {...blockProps} >
        <TextControl
          className="tab-label"
          value={title}
          onChange={onChangeTitle}
          placeholder={__("タブのタイトルを追加", THEME_NAME)}
          type="text"
        />
        <InnerBlocks/>
      </div>
    </Fragment>
  );
}
