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
  // WordPress 6.x 以降で { default: Component } 形式に変わるケースに対応
  var ServerSideRender = ( wp.serverSideRender && wp.serverSideRender.default )
    || wp.serverSideRender
    || null;
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
        inserter: false,
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
    // ブログカード内のリンク遷移をエディター上で無効化する
    // ==============================================================================
    document.addEventListener(
      'click',
      function ( e ) {
        var link = e.target.closest(
          '.blogcard a, .internal-blogcard a, .external-blogcard a, .wp-block-cocoon-blocks-embed-blogcard a'
        );
        if ( link ) {
          e.preventDefault();
        }
      },
      true
    );

    // ==============================================================================
    // oEmbedの解決はWordPressコアに任せ、解決失敗したembedブロックを監視して変換する
    // ==============================================================================
    var isMonitoring = false;
    var processedBlocks = {}; // 変換済み・処理済みのブロックキーをオブジェクトで管理

    // デバウンス用タイマーID
    var debounceTimer = null;
    // デバウンスの待機時間（ミリ秒）
    var DEBOUNCE_DELAY = 300;

    // リトライスケジュール済みURL（同じURLで何度もsetTimeoutしないための管理用）
    var pendingRetryUrls = {};

    // 指定URLに対して遅延リトライをスケジュールする
    // Gutenbergのembed→paragraphフォールバックとの競合対策：
    // subscribeが発火しなくなった後でも確実にブロック変換を検出するため、
    // 複数の遅延でprocessEmbedBlocksを再実行する
    function scheduleRetries( url ) {
      if ( pendingRetryUrls[ url ] ) return;
      pendingRetryUrls[ url ] = true;
      setTimeout( processEmbedBlocks, 1000 );
      setTimeout( processEmbedBlocks, 2000 );
      setTimeout( processEmbedBlocks, 4000 );
    }

    // ネストを含む全ブロックから、処理対象のブロック（embedおよび単一URLのparagraph）を再帰的に収集する関数
    function collectTargetBlocks( blocks, result ) {
      for ( var i = 0; i < blocks.length; i++ ) {
        var block = blocks[ i ];

        // core/embed ブロック: URLを持つものを収集
        if ( block.name === 'core/embed' ) {
          if ( block.attributes && block.attributes.url ) {
            result.push( { block: block, url: block.attributes.url, isEmbed: true } );
          }
        }
        // core/paragraph ブロック: URLのみの段落を収集
        // ※ GutenbergがembedのoEmbed解決失敗時に自動でparagraphにフォールバックするケースに対応
        else if ( block.name === 'core/paragraph' ) {
          // RichTextData対応: WordPress 6.x ではcontent属性がRichTextDataオブジェクトの場合がある
          // String()で文字列に確実に変換してから文字列操作を行う
          var rawContent = block.attributes.content;
          if ( ! rawContent ) {
            // content属性なし → スキップ
          } else {
            var content = String( rawContent ).trim();
            if ( content ) {
              var matchedUrl = null;

              // パターン1: 素のURL文字列のみ
              if ( /^https?:\/\/[^\s<]+$/.test( content ) ) {
                matchedUrl = content;
              }
              // パターン2: <a>タグのみの構成（Gutenbergのエンベッドフォールバック形式）
              // 例: <a href="https://example.com">https://example.com</a>
              else if ( content.indexOf( '<a ' ) === 0 && content.lastIndexOf( '</a>' ) === content.length - 4 ) {
                var hrefMatch = content.match( /href="([^"]+)"/ );
                if ( hrefMatch ) {
                  var linkUrl = hrefMatch[ 1 ];
                  // リンクテキスト自体がURLと一致しているか確認（装飾テキストのリンクは除外）
                  var innerText = content.replace( /<[^>]+>/g, '' ).trim();
                  if ( innerText === linkUrl || innerText === decodeURI( linkUrl ) ) {
                    matchedUrl = linkUrl;
                  }
                }
              }

              if ( matchedUrl ) {
                result.push( { block: block, url: matchedUrl, isEmbed: false } );
              }
            }
          }
        }

        // ネストされたインナーブロックも再帰的に走査する
        if ( block.innerBlocks && block.innerBlocks.length > 0 ) {
          collectTargetBlocks( block.innerBlocks, result );
        }
      }
      return result;
    }

    // 現在エディターに存在するブロックIDを収集して、不要なエントリを掃除する
    function cleanupProcessedBlocks( allBlocks ) {
      // 全ブロックのclientIdをSetに収集
      var activeIds = {};
      function collectIds( blocks ) {
        for ( var i = 0; i < blocks.length; i++ ) {
          activeIds[ blocks[ i ].clientId ] = true;
          if ( blocks[ i ].innerBlocks && blocks[ i ].innerBlocks.length > 0 ) {
            collectIds( blocks[ i ].innerBlocks );
          }
        }
      }
      collectIds( allBlocks );

      // processedBlocksから、もう存在しないclientIdのエントリを削除
      var keys = Object.keys( processedBlocks );
      for ( var j = 0; j < keys.length; j++ ) {
        // キーは "clientId_url_type" 形式なのでclientId部分（UUID）を取り出す
        var clientId = keys[ j ].split( '_' )[ 0 ];
        if ( ! activeIds[ clientId ] ) {
          delete processedBlocks[ keys[ j ] ];
        }
      }
    }

    // メインの監視処理（デバウンスまたはリトライタイマーで呼ばれる）
    function processEmbedBlocks() {
      // ブロックエディターのデータを取得
      var editorSelect = select( 'core/block-editor' );
      if ( ! editorSelect ) return;

      var allBlocks = editorSelect.getBlocks();
      if ( ! allBlocks || allBlocks.length === 0 ) return;

      // 不要になったprocessedBlocksエントリを定期的にクリーンアップ
      cleanupProcessedBlocks( allBlocks );

      // ネストを含む全ブロックから対象ブロック（embedおよび単一URLのparagraph）を収集する
      var targetItems = collectTargetBlocks( allBlocks, [] );

      for ( var i = 0; i < targetItems.length; i++ ) {
        var item = targetItems[ i ];
        var block = item.block;
        var url = item.url;
        var isEmbed = item.isEmbed;

        // URL書き換え・ブロック種類変更対応（ブロックIDとURLと種類をセットで管理）
        var blockKey = block.clientId + '_' + url + '_' + ( isEmbed ? 'embed' : 'p' );

        // 既に判定済み・変換済みのブロックはスキップして無限ループ防止
        if ( processedBlocks[ blockKey ] ) {
          continue;
        }

        // WordPressコアがoEmbedプロキシで取得したプレビューデータを確認
        var coreSelect = select( 'core' );
        if ( ! coreSelect || ! coreSelect.getEmbedPreview || ! coreSelect.hasFinishedResolution ) {
          continue;
        }

        // oEmbed API経由でプロバイダー判定を行う
        // これを呼ぶことで、キャッシュがない場合は裏で自動的にAPIリクエスト（Resolver）が発火する
        var preview = coreSelect.getEmbedPreview( url );
        var isResolved = coreSelect.hasFinishedResolution( 'getEmbedPreview', [ url ] );

        // プレビューデータ取得が完了していない（API通信中）場合はまだ判定しない
        // ただし、Gutenbergのフォールバックとの競合に備えてリトライをスケジュールする
        if ( ! isResolved ) {
          scheduleRetries( url );
          continue;
        }

        // ===== 変換条件の判定 =====
        // 1. 自サイトのURL（内部リンク）かどうか
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
        var isFailed =
          ! preview ||
          preview === false ||
          ( preview && ( preview.html === false || preview.html === '' || preview.html === undefined ) ) ||
          ( preview && preview.message !== undefined ); // APIからのWP Error等

        // 3. WordPressコアが知っているエンベッド対象かどうか（embedブロックのみ保持）
        var isKnownProvider = isEmbed ? !! ( block.attributes && block.attributes.providerNameSlug ) : false;

        // 変換方針の決定
        var shouldConvertToBlogcard = false;

        if ( isInternal ) {
          // 自サイトのURLは、プレビューの成否に関わらず内部ブログカード化
          shouldConvertToBlogcard = true;
        } else if ( isFailed && ! isKnownProvider ) {
          // 外部URLのうち、公式プロバイダーに該当せず、エンベッド取得に失敗したもの
          // （Gutenbergが自動的にparagraphにフォールバックした場合もここに入る）
          shouldConvertToBlogcard = true;
        }

        if ( shouldConvertToBlogcard ) {
          // ブログカードブロックに置換
          processedBlocks[ blockKey ] = true; // 無限ループ防止

          // DOMの更新と競合しないよう setTimeout で非同期置換（クロージャでキャプチャ）
          ( function ( capturedBlock, capturedUrl ) {
            setTimeout( function () {
              var newBlock = createBlock( 'cocoon-blocks/embed-blogcard', {
                url: capturedUrl,
              } );
              dispatch( 'core/block-editor' ).replaceBlock(
                capturedBlock.clientId,
                newBlock
              );
            }, 0 );
          } )( block, url );

          // replaceBlockがGutenbergのフォールバックと競合して失敗する場合に備え、
          // 遅延リトライをスケジュールして次のサイクルでparagraphを再検出する
          scheduleRetries( url );
        } else {
          // 正常なembedブロックまたはエラーの公式プロバイダー等は処理済みとしてマーク
          processedBlocks[ blockKey ] = true;
        }
      }
    }

    function monitorEmbedBlocks() {
      if ( isMonitoring ) return;
      isMonitoring = true;

      subscribe( function () {
        // デバウンス: 連続するストア更新を間引いてパフォーマンスを改善
        if ( debounceTimer ) {
          clearTimeout( debounceTimer );
        }
        debounceTimer = setTimeout( processEmbedBlocks, DEBOUNCE_DELAY );
      } );
    }

    // 監視をスタート
    monitorEmbedBlocks();
  } );
} )();
