/**
 * Amazon商品リンクブロック - REST API呼び出しユーティリティ
 */
import apiFetch from '@wordpress/api-fetch';

/**
 * Amazon商品をキーワードで検索
 * @param {string} keyword - 検索キーワード
 * @param {number} page - ページ番号（1〜10）
 * @returns {Promise<Object>} 検索結果
 */
export async function searchAmazonProducts( keyword, page = 1 ) {
  return await apiFetch( {
    path: '/cocoon/v1/amazon/search',
    method: 'POST',
    data: { keyword, page },
  } );
}

/**
 * ASINで商品詳細情報を取得
 * @param {string} asin - Amazon ASIN
 * @returns {Promise<Object>} 商品情報
 */
export async function getAmazonItem( asin ) {
  return await apiFetch( {
    path: '/cocoon/v1/amazon/get-item',
    method: 'POST',
    data: { asin },
  } );
}

/**
 * ブロック設定から静的HTMLプレビューを生成
 * @param {string} asin - Amazon ASIN
 * @param {Object} settings - ブロック設定
 * @returns {Promise<Object>} 静的HTML+商品データ
 */
export async function renderPreview( asin, settings ) {
  return await apiFetch( {
    path: '/cocoon/v1/amazon/render-preview',
    method: 'POST',
    data: { asin, ...settings },
  } );
}
