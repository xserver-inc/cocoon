import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import {
  InnerBlocks,
  InspectorControls,
  useBlockProps,
} from '@wordpress/block-editor';
import { PanelBody, Button, TextControl, __experimentalDivider as Divider, } from '@wordpress/components';
import { Fragment, useEffect, RawHTML, useState } from '@wordpress/element';
import { useDispatch} from '@wordpress/data';
import {createBlock} from '@wordpress/blocks';
import classnames from 'classnames';

const ALLOWED_BLOCKS = [ 'cocoon-blocks/tab-item' ];

const TEMPLATE = [
  [ 'cocoon-blocks/tab-item', {isActive: true}],
  [ 'cocoon-blocks/tab-item']
];

export default function edit( props ) {
  const {
    clientId,
    attributes,
    setAttributes,
    className,
  } = props;

  const {
    tabLabelsArray,
  } = attributes;

  // useEffectで扱う変数・関数
  const [sourceIdx, setSourceIdx] = useState(-1);
  const [targetIdx, setTargetIdx] = useState(-1);
  const [removeIdx, setRemoveIdx] = useState(-1);
  const [addIdx, setAddIdx] = useState(-1);
  const [tabIdx, setTabIdx] = useState(0);
  const { replaceInnerBlocks } = useDispatch('core/block-editor');
  const { updateBlockAttributes } = useDispatch( 'core/block-editor' );

  // InnerBlocksを取得
  function getInnerBlocks(clientId) {
    return wp.data.select('core/block-editor').getBlocks(clientId);
  }

  useEffect(() => {
    //console.log("sourceIdx " + sourceIdx);
    //console.log("targetIdx " + targetIdx);
    //console.log("removeIdx " + removeIdx);
    //console.log("addIdx " + addIdx);

    // 入れ替え処理
    if (sourceIdx != -1 && targetIdx != -1) {
      // InnerBlocksを取得
      const innerBlocks = getInnerBlocks(clientId);
      //console.log(innerBlocks);

      // ソースブロックの情報を退避
      const sourceBlock = innerBlocks[sourceIdx];
      const sourceAttributes = sourceBlock.attributes;
      const sourceContent = sourceBlock.content;

      // ターゲットブロックの退避
      const targetBlock = innerBlocks[targetIdx];
      const targetAttributes = targetBlock.attributes;
      const targetContent = targetBlock.content;

      // ターゲット→ソース
      innerBlocks[sourceIdx] = targetBlock;
      innerBlocks[sourceIdx].atrributes = targetAttributes;
      innerBlocks[sourceIdx].content = targetContent;

      // ソース→ターゲット
      innerBlocks[targetIdx] = sourceBlock;
      innerBlocks[targetIdx].attributes = sourceAttributes;
      innerBlocks[targetIdx].content = sourceContent;

      // 再レンダリング
      replaceInnerBlocks(clientId, innerBlocks, false);

      // indexをリセット
      setSourceIdx(-1);
      setTargetIdx(-1);
    }

    // 削除処理
    if (removeIdx != -1) {
      // InnerBlocksを取得
      const innerBlocks = getInnerBlocks(clientId);

      // 指定したブロックを削除
      innerBlocks.splice(removeIdx, 1);

      // 再レンダリング
      replaceInnerBlocks(clientId, innerBlocks, false);

      // indexをリセット
      setRemoveIdx(-1);

      // 先頭を選択
      setTabIdx(0);
    }

    // 追加処理
    if (addIdx != -1) {
      // InnerBlocksを取得
      const innerBlocks = getInnerBlocks(clientId);

      // 新規ブロックを生成
      const newBlock = createBlock('cocoon-blocks/tab-item');
      innerBlocks.push(newBlock);

      // 再レンダリング
      replaceInnerBlocks(clientId, innerBlocks, false);

      // indexをリセット
      setAddIdx(-1);

      // 末尾を選択
      setTabIdx(innerBlocks.length - 1);
    }

    // タブ選択
    {
      const innerBlocks = getInnerBlocks(clientId);

      innerBlocks.map((innerBlock, index) => {
        if (tabIdx == index) {
          updateBlockAttributes(innerBlock.clientId, {
            isActive: true
          });
        }
        else {
          updateBlockAttributes(innerBlock.clientId, {
            isActive: false
          });
        }
      });
    }
  },[clientId, sourceIdx, targetIdx, removeIdx, addIdx, tabIdx]);

  // タブラベルの配列を入れ替える
  function replaceArrayElements(array, targetIdx, sourceIdx) {
    var newArray = array.concat();

    var val = newArray[sourceIdx];
    newArray.splice(sourceIdx, 1, newArray[targetIdx]);
    newArray.splice(targetIdx, 1, val);

    return newArray;
  }

  // タブ追加処理
  function addTab() {
    //console.log("addTab");
    var tabLabels = [];

    tabLabels = tabLabelsArray.concat(__('Tab', THEME_NAME) + ' ' + (tabLabelsArray.length + 1));

    setAddIdx(1);
    setAttributes({tabLabelsArray: tabLabels});
  }

  // タブラベル変更処理
  function changeTabLabel(index, value) {
    var tabLabels = tabLabelsArray.concat();
    //console.log(tabLabels);

    tabLabels[index] = value;
    setAttributes({tabLabelsArray: tabLabels});
  }

  // タブ削除処理
  function deleteTab (index) {
    var tabLabels = tabLabelsArray.concat();
    //console.log(tabLabels);

    if (tabLabels.length <= 1) {
      return;
    }
    //console.log('deleteTab: ' + index)

    tabLabels.splice(index, 1);
    //console.log(tabLabels);

    setRemoveIdx(index);
    setAttributes({tabLabelsArray: tabLabels});
  }

  // 指定したタブをひとつ上に移動する処理
  function moveTabUp (index) {
    var tabLabels = tabLabelsArray.concat();

    // タブがひとつしかない場合は実行しない
    if (tabLabels.count === 1) {
      return;
    }
    // 指定したタブが一番上の場合は実行しない
    if (index === 0) {
      return;
    }

    // タブラベル入れ替え
    tabLabels = replaceArrayElements(tabLabels, index - 1, index);
    // タブコンテンツ入れ替え
    setTargetIdx(index - 1);
    setSourceIdx(index);

    setAttributes({tabLabelsArray: tabLabels});
  }

  // 指定したタブをひとつ下に移動する処理
  function moveTabDown (index) {
    var tabLabels = tabLabelsArray.concat();

    // タブがひとつしかない場合は実行しない
    if (tabLabels.count === 1) {
      return;
    }
    // 指定したタブが一番下の場合は実行しない
    if (index + 1 === tabLabels.length) {
      return;
    }

    // タブラベル入れ替え
    tabLabels = replaceArrayElements(tabLabels, index + 1, index);
    // タブコンテンツ入れ替え
    setTargetIdx(index + 1);
    setSourceIdx(index);

    setAttributes({tabLabelsArray: tabLabels});
  }

  function updateTabIdx(index) {
    setTabIdx(index);

    // InnerBlocksを取得
    const innerBlocks = getInnerBlocks(clientId);

    innerBlocks.map((innerBlock, idx) => {
      // console.log(innerBlock);
    });
  }

  const classes = classnames( className, {
    'tab-block': true,
    'cocoon-block-tab': true,
  });

  const blockProps = useBlockProps( {
    className: classes,
  });

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={__('タブ設定', THEME_NAME)}>
          {tabLabelsArray.map((label, index) => {
            return (
              <div className={"tab-setting tab-setting-" + index}>
                <TextControl label={__('タブ', THEME_NAME) + (index + 1)} value={label} onChange={ (value) => changeTabLabel(index, value)}/>
                <Button disabled={tabLabelsArray.length > 1 ? false : true} onClick={() => {deleteTab(index)}}>{__('タブ削除', THEME_NAME)}</Button>
                <Button disabled={index === 0 ? true : false} onClick={() => moveTabUp(index)}>{__('上に移動', THEME_NAME)}</Button>
                <Button disabled={(index + 1) === tabLabelsArray.length ? true : false} onClick={() => {moveTabDown(index)}}>{__('下に移動', THEME_NAME)}</Button>
                <Divider />
              </div>
            );
          })}
          <Button onClick={() => {addTab()}}>{__('タブ追加', THEME_NAME)}</Button>
        </PanelBody>
      </InspectorControls>

      <div { ...blockProps }>
        <ul className="tab-label-group">
          {tabLabelsArray.map((label, index) => {
            return (<li class={"tab-label " + "tab-label-" + index + (tabIdx === index ? " is-active" : " ")} onClick={() => {updateTabIdx(index)}}><RawHTML>{label}</RawHTML></li>);
          })}
          <Button className="tab-add-button" onClick={() => {addTab()}}>+</Button>
        </ul>
        <div className="tab-content-group">
            <InnerBlocks
              allowedBlocks={ ALLOWED_BLOCKS }
              template={ TEMPLATE }
              templateLock='insert'
            />
        </div>
      </div>
    </Fragment>
  );
}