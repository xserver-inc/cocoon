import { unregisterBlockType } from '@wordpress/blocks';
import { select, subscribe } from '@wordpress/data';
import domReady from '@wordpress/dom-ready';

const EDITOR_STORE_NAME = 'core/editor';
const TOGGLE_BOX_BLOCK_NAME = 'cocoon-blocks/toggle-box-1';

/**
 * パターン編集時だけ旧アコーディオンブロックを登録解除します。
 *
 * @param {Object}   dependencies                     WordPress APIの依存関係
 * @param {Function} dependencies.domReady            DOM準備後の実行関数
 * @param {Function} dependencies.select              ストア選択関数
 * @param {Function} dependencies.subscribe           ストア購読関数
 * @param {Function} dependencies.unregisterBlockType ブロック登録解除関数
 * @return {Function} 購読停止関数
 */
export const observePatternEditorToggleBox = ( {
  domReady: onDomReady = domReady,
  select: selectStore = select,
  subscribe: subscribeStore = subscribe,
  unregisterBlockType: unregisterToggleBox = unregisterBlockType,
} = {} ) => {
  let unsubscribe = null;
  let resolvedPostType;
  let isComplete = false;

  // 同期subscribeで解除関数が返る前の投稿タイプを保持する完了処理。
  const complete = () => {
    if (
      isComplete ||
      typeof unsubscribe !== 'function' ||
      typeof resolvedPostType !== 'string' ||
      resolvedPostType === ''
    ) {
      return;
    }

    // 登録解除による通知再入より先に確定する完了状態。
    isComplete = true;
    unsubscribe();

    if ( resolvedPostType === 'wp_block' ) {
      unregisterToggleBox( TOGGLE_BOX_BLOCK_NAME );
    }
  };

  // 投稿ストア未登録と初期化途中を待機対象にする安全確認。
  const checkCurrentPostType = () => {
    if ( isComplete ) {
      return;
    }

    const editorStore = selectStore( EDITOR_STORE_NAME );
    if (
      ! editorStore ||
      typeof editorStore.getCurrentPostType !== 'function'
    ) {
      return;
    }

    const postType = editorStore.getCurrentPostType();
    if ( typeof postType !== 'string' || postType === '' ) {
      return;
    }

    resolvedPostType = postType;
    complete();
  };

  unsubscribe = subscribeStore( checkCurrentPostType, EDITOR_STORE_NAME );
  complete();
  checkCurrentPostType();

  // ヘッダー先行時の後続ストア登録を拾うDOM準備通知。
  if ( ! isComplete ) {
    onDomReady( checkCurrentPostType );
  }

  return () => {
    if ( isComplete ) {
      return;
    }

    isComplete = true;
    if ( typeof unsubscribe === 'function' ) {
      unsubscribe();
    }
  };
};
