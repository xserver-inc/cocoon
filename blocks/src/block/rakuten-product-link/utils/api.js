/**
 * 楽天商品リンクブロック - REST API呼び出しユーティリティ
 */
import apiFetch from '@wordpress/api-fetch';

/**
 * 楽天商品をキーワードで検索
 * @param {string} keyword - 検索キーワード
 * @param {number} page - ページ番号
 * @param {number} purchaseType - 購入タイプ（0:通常, 1:定期, 2:頒布会）
 * @returns {Promise<Object>} 検索結果
 */
export async function searchRakutenProducts( keyword, page = 1, purchaseType = 0 ) {
  return await apiFetch( {
    path: '/cocoon/v1/rakuten/search',
    method: 'POST',
    data: { keyword, page, purchaseType },
  } );
}

/**
 * 商品コードで商品詳細情報を取得
 * @param {string} itemCode - 楽天商品コード
 * @returns {Promise<Object>} 商品情報
 */
export async function getRakutenItem( itemCode ) {
  return await apiFetch( {
    path: '/cocoon/v1/rakuten/get-item',
    method: 'POST',
    data: { itemCode },
  } );
}

/**
 * ブロック設定から静的HTMLプレビューを生成
 * @param {string} itemCode - 楽天商品コード
 * @param {Object} settings - ブロック設定
 * @returns {Promise<Object>} 静的HTML+商品データ
 */
export async function renderPreview( itemCode, settings ) {
  return await apiFetch( {
    path: '/cocoon/v1/rakuten/render-preview',
    method: 'POST',
    data: { itemCode, ...settings },
  } );
}
