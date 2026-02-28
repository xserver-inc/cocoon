/**
 * ブロックエディターでURL貼り付け時にoEmbed非対応URLをブログカードとして表示する
 *
 * oEmbed対応URL（YouTube, Twitter等）→ core/embed ブロック（従来通り）
 * oEmbed非対応URL → cocoon-blocks/embed-blogcard ブロック（PHPでブログカード生成）
 *
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
( function () {
  // WordPress APIへの参照
  var createElement = wp.element.createElement;
  var ServerSideRender = wp.serverSideRender;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var createBlock = wp.blocks.createBlock;
  var select = wp.data.select;
  var dispatch = wp.data.dispatch;
  var subscribe = wp.data.subscribe;

  wp.domReady( function () {
    // PHP側で既に登録されている場合は解除してからJS側で再登録する
    if ( wp.blocks.getBlockType( 'cocoon-blocks/embed-blogcard' ) ) {
      wp.blocks.unregisterBlockType( 'cocoon-blocks/embed-blogcard' );
    }

    // ブログカード埋め込みブロックを登録
    wp.blocks.registerBlockType( 'cocoon-blocks/embed-blogcard', {
      title: 'ブログカード（埋め込み）',
      icon: 'admin-links',
      category: 'cocoon-block',
      attributes: {
        url: { type: 'string', default: '' },
      },
      supports: {
        html: false,
        anchor: true,
      },
      // エディター表示: PHPのrender_callbackで生成したブログカードHTMLを表示
      edit: function ( props ) {
        var blockProps = useBlockProps();
        var url = props.attributes.url;
        if ( ! url ) {
          return createElement(
            'div',
            blockProps,
            createElement( 'p', null, 'URLが設定されていません' )
          );
        }
        // ServerSideRenderでPHP側のrender_callbackを呼び出してプレビュー表示
        return createElement(
          'div',
          blockProps,
          createElement( ServerSideRender, {
            block: 'cocoon-blocks/embed-blogcard',
            attributes: { url: url },
          } )
        );
      },
      // ダイナミックブロック：保存時はPHPのrender_callbackがHTMLを出力する
      save: function () {
        return null;
      },
    } );

    // ==============================================================================
    // oEmbedの解決はWordPressコアに任せ、解決失敗したembedブロックを監視して変換する
    // ==============================================================================
    var isMonitoring = false;
    var processedBlocks = new Set(); // 変換済み・処理済みのブロックIDを記憶

    function monitorEmbedBlocks() {
      if ( isMonitoring ) return;
      isMonitoring = true;

      subscribe( function () {
        // ブロックエディターのデータを取得
        var editorSelect = select( 'core/block-editor' );
        if ( ! editorSelect ) return;

        var blocks = editorSelect.getBlocks();
        if ( ! blocks || blocks.length === 0 ) return;

        // core/embed ブロックを探す
        blocks.forEach( function ( block ) {
          if ( block.name !== 'core/embed' ) {
            return;
          }

          // 既に処理済みのブロックはスキップ
          if ( processedBlocks.has( block.clientId ) ) {
            return;
          }

          var url = block.attributes.url;
          if ( ! url ) {
            return;
          }

          // WordPressコアがoEmbedプロキシで取得したプレビューデータを確認
          // data(url) ではなく getEmbedPreview(url) を使用
          var coreSelect = select( 'core' );
          if ( ! coreSelect || ! coreSelect.getEmbedPreview ) {
            return;
          }

          var preview = coreSelect.getEmbedPreview( url );

          // プレビューデータの取得中（undefined）の場合はまだ判定しない
          if ( preview === undefined ) {
            return;
          }

          // ===== 変換条件の判定 =====
          // 1. 自サイトのURL（内部リンク）かどうか
          // エディターの現在のホスト名と比較する
          var isInternal = false;
          try {
            var urlObj = new URL( url );
            if ( urlObj.hostname === window.location.hostname ) {
              isInternal = true;
            }
          } catch ( e ) {
            // URLパースエラー時は無視
          }

          // 2. oEmbed解決に失敗したかどうか
          // previewable が false、またはhtmlが空の場合（「埋め込めませんでした」状態）
          var isFailed =
            preview === false ||
            ( preview && ( preview.html === false || preview.html === '' ) );

          // 3. WordPressコアが知っているエンベッド対象（プロバイダー）かどうか
          // 公式のoEmbed互換サービス（Twitter, YouTubeなど）として認識されている場合、
          // `providerNameSlug` に 'twitter' などの値が入ります。
          // 一般のURL（kabutan.jpなど）はマッチしないため空になります。
          var isKnownProvider = !! (
            block.attributes && block.attributes.providerNameSlug
          );

          // 追加の処理方針の決定
          var shouldConvertToBlogcard = false;

          if ( isInternal ) {
            // 自サイトのURLは、プレビューの成否に関わらず内部ブログカード化
            shouldConvertToBlogcard = true;
          } else if ( isFailed && ! isKnownProvider ) {
            // 外部URLのうち、公式プロバイダーに該当せず、エンベッド取得に失敗したもの（＝一般URL）
            // これらは単なる外部リンクとみなし、外部ブログカード化
            shouldConvertToBlogcard = true;
          }
          // 上記以外（isKnownProvider だがエラーになったTwitter等）は shouldConvertToBlogcard = false

          if ( shouldConvertToBlogcard ) {
            // ブログカードブロックに置換
            processedBlocks.add( blockKey ); // 無限ループ防止

            // DOMの更新と競合しないよう setTimeout で非同期置換
            setTimeout( function () {
              var newBlock = createBlock( 'cocoon-blocks/embed-blogcard', {
                url: url,
              } );
              dispatch( 'core/block-editor' ).replaceBlock(
                block.clientId,
                newBlock
              );
            }, 0 );
          } else if ( preview && preview.html ) {
            // 成功した公式プロバイダーなど、正常なembedブロックなので処理済みとしてマーク
            processedBlocks.add( blockKey );
          } else {
            // エラーになった公式プロバイダー（Twitter等）は、WP標準のエラーUIのまま放置する
            processedBlocks.add( blockKey );
          }
        } );
      } );
    }

    // 監視をスタート
    monitorEmbedBlocks();
  } );
} )();
