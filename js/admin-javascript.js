/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

// 管理タブの何番目がクリックされたか
document.querySelectorAll( '#tabs > ul > li' ).forEach( ( tab, index ) => {
  tab.addEventListener( 'click', () => {
    document.getElementById( 'select_index' ).value = index;
  } );
} );

// トグルスイッチ
document.querySelectorAll( '.toggle-link' ).forEach( ( toggleLink ) => {
  toggleLink.addEventListener( 'click', () => {
    const toggleContent = toggleLink.nextElementSibling;
    if (
      toggleContent &&
      toggleContent.classList.contains( 'toggle-content' )
    ) {
      toggleContent.style.display =
        toggleContent.style.display === 'none' ? 'block' : 'none';
    }
  } );
} );

const skinOptions = document.querySelector( '.cocoon-skin-options[data-show-variants]' );
if ( skinOptions ) {
  const thumbnailSelector = '.cocoon-skin-option-thumbnail img';
  const noImageUri = skinOptions.dataset.noImageUri || '';
  // 画像読み込み失敗時のNo Image画像またはプレースホルダーへのフォールバック
  const fallbackThumbnail = ( image ) => {
    const thumbnail = image.parentElement;
    if ( ! thumbnail ) {
      return;
    }
    // 既存のNo Imageやなしアイコン自体の失敗時は再試行せずプレースホルダーへ退避
    if (
      noImageUri &&
      ! image.dataset.noImageFallback &&
      ! thumbnail.classList.contains( 'is-no-image' )
    ) {
      image.dataset.noImageFallback = '1';
      thumbnail.classList.add( 'is-no-image' );
      image.src = noImageUri;
      return;
    }
    thumbnail.classList.remove( 'is-no-image' );
    thumbnail.classList.add( 'is-placeholder' );
    image.remove();
  };
  // 一覧のサムネイルだけを対象にした読み込みエラーの処理
  skinOptions.addEventListener(
    'error',
    ( event ) => {
      if ( event.target.matches( thumbnailSelector ) ) {
        fallbackThumbnail( event.target );
      }
    },
    { capture: true }
  );
  skinOptions.querySelectorAll( thumbnailSelector ).forEach( ( image ) => {
    if ( image.complete && image.naturalWidth === 0 ) {
      fallbackThumbnail( image );
    }
  } );
}

skinOptions?.querySelectorAll( '.cocoon-radiobox-group' ).forEach( ( details, index ) => {
  const series = details.parentElement;
  const seriesLabel = details.querySelector( 'summary' ).textContent.trim();
  const author = details.querySelector( '.cocoon-radiobox-group-caption' );
  const variants = details.querySelector( 'ul' );
  const rows = Array.from( variants.children );
  const primaryList = document.createElement( 'ul' );
  const variantBar = document.createElement( 'div' );
  let primary = variants.querySelector( 'input:checked' )?.closest( 'li' ) ?? rows[ 0 ];

  series.classList.add( 'cocoon-skin-series' );
  author?.classList.replace( 'cocoon-radiobox-group-caption', 'cocoon-skin-option-author' );
  primaryList.className = 'cocoon-skin-series-primary';
  variantBar.className = 'cocoon-skin-series-variants';
  variantBar.setAttribute( 'role', 'group' );
  variantBar.setAttribute( 'aria-label', seriesLabel );
  variants.classList.add( 'cocoon-skin-series-options' );
  variants.id = 'cocoon-skin-series-options-' + index;
  variants.setAttribute( 'aria-label', seriesLabel );
  variants.hidden = true;

  // 各バリエーション行からの表示名とカラーコードの取得
  const getVariantMeta = ( row ) => {
    const labelNode = row.querySelector( '.cocoon-skin-option-label' );
    const swatchNode = row.querySelector( '.cocoon-skin-swatch' );
    const fullName =
      labelNode?.dataset.skinName?.trim() ||
      row.querySelector( 'label' )?.textContent.trim() ||
      '';
    const shortLabel = labelNode?.dataset.skinLabel?.trim() || fullName;
    const color =
      labelNode?.dataset.skinColor?.trim() ||
      swatchNode?.style.backgroundColor ||
      '';
    return { fullName, shortLabel, color };
  };

  const buttons = new Map();

  // 代表行の差し替えと共通作者・ボタン状態の同期
  function showPrimary( row ) {
    if ( primary && primary !== row && primary.parentElement === primaryList ) {
      // 以前の代表行を元の候補順へ戻すための挿入位置
      const next = rows
        .slice( rows.indexOf( primary ) + 1 )
        .find( ( candidate ) => candidate.parentElement === variants );
      variants.insertBefore( primary, next ?? null );
    }
    const action = row.querySelector( '.cocoon-skin-option-action' );
    if ( author ) {
      row.insertBefore( author, action );
    }
    primaryList.append( row );
    primary = row;
    buttons.forEach( ( button, targetRow ) => {
      const isCurrent = targetRow === primary;
      button.classList.toggle( 'is-active', isCurrent );
      button.setAttribute( 'aria-pressed', String( isCurrent ) );
    } );
  }

  // カード内のスウォッチまたはテキストチップによる切替ボタン生成
  rows.forEach( ( row ) => {
    const { fullName, shortLabel, color } = getVariantMeta( row );
    const button = document.createElement( 'button' );
    button.type = 'button';
    button.className = 'cocoon-skin-variant-button';
    button.title = shortLabel;
    button.setAttribute( 'aria-label', fullName );
    if ( color ) {
      button.classList.add( 'is-swatch' );
      const swatch = document.createElement( 'span' );
      swatch.className = 'cocoon-skin-variant-swatch';
      swatch.style.backgroundColor = color;
      swatch.setAttribute( 'aria-hidden', 'true' );
      button.append( swatch );
    } else {
      button.classList.add( 'is-chip' );
      button.textContent = shortLabel;
    }

    button.addEventListener( 'click', () => {
      const input = row.querySelector( 'input[type="radio"]' );
      showPrimary( row );
      if ( input && ! input.checked ) {
        input.checked = true;
        input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
      }
    } );

    const input = row.querySelector( 'input[type="radio"]' );
    input?.addEventListener( 'change', () => {
      if ( input.checked ) {
        showPrimary( row );
      }
    } );

    buttons.set( row, button );
    variantBar.append( button );
  } );

  showPrimary( primary );
  details.replaceWith( primaryList, variantBar, variants );
} );

const skinPreview = document.getElementById( 'cocoon-skin-preview' );
if ( skinPreview && typeof skinPreview.showModal === 'function' ) {
  const title = skinPreview.querySelector( 'h3' );
  const media = skinPreview.querySelector( '.cocoon-skin-preview-media' );
  const errorMessage = skinPreview.querySelector( '.cocoon-skin-preview-error' );
  const description = skinPreview.querySelector(
    '.cocoon-skin-preview-description'
  );
  const intro = skinPreview.querySelector( '.cocoon-skin-preview-intro' );
  let activeTrigger;

  document.querySelectorAll( '.cocoon-skin-preview-trigger' ).forEach( ( trigger ) => {
    trigger.setAttribute( 'aria-haspopup', 'dialog' );
    trigger.setAttribute( 'aria-controls', skinPreview.id );
    trigger.addEventListener( 'click', ( event ) => {
      if ( event.ctrlKey || event.metaKey || event.shiftKey || event.altKey ) {
        return;
      }
      event.preventDefault();

      // 選択中のラジオボタンと独立したプレビュー内容の切り替え
      const { skinName } = trigger
        .closest( 'li' )
        .querySelector( '[data-skin-name]' ).dataset;
      const image = document.createElement( 'img' );
      image.alt = skinName;
      errorMessage.hidden = true;
      // 切り替え前の画像から遅れて届いたエラー通知の除外
      image.addEventListener( 'error', () => {
        if ( image.parentElement === media ) {
          image.remove();
          errorMessage.hidden = false;
        }
      } );
      title.textContent = skinName;
      media.replaceChildren( image );
      image.src = trigger.href;
      description.textContent = trigger.dataset.skinDescription;
      description.hidden = ! description.textContent;
      intro.hidden = ! trigger.dataset.skinIntro;
      if ( ! intro.hidden ) {
        intro.href = trigger.dataset.skinIntro;
      }
      activeTrigger = trigger;
      skinPreview.showModal();
      skinPreview.scrollTop = 0;
    } );
  } );

  skinPreview
    .querySelector( '.cocoon-skin-preview-close' )
    .addEventListener( 'click', () => skinPreview.close() );
  skinPreview.addEventListener( 'close', () => {
    media.replaceChildren();
    activeTrigger?.focus( { preventScroll: true } );
    activeTrigger = null;
  } );
}

// WordPressの管理バー削除処理
function deleteWpAdminBar( selector ) {
  document.querySelectorAll( selector ).forEach( ( iframe ) => {
    iframe.addEventListener( 'load', () => {
      const iframeContent = iframe.contentDocument;
      if ( iframeContent ) {
        const wpAdminBar = iframeContent.getElementById( 'wpadminbar' );
        if ( wpAdminBar ) wpAdminBar.style.display = 'none';
        const adminPanel = iframeContent.querySelector( '.admin-panel' );
        if ( adminPanel ) adminPanel.style.display = 'none';
        // インラインスタイルを個別に設定し、他のプロパティ（WPコアのCSS変数等）を保持する
        iframeContent.documentElement.style.setProperty(
          'margin-top',
          '0px',
          'important'
        );
        // プレビュー内でadminbarを非表示にした際、ヘッダーモバイルボタンの位置ずれを防ぐため、CSS変数も0pxに設定
        iframeContent.documentElement.style.setProperty(
          '--wp-admin--admin-bar--height',
          '0px'
        );
      }
    } );
  } );
}

deleteWpAdminBar( '.iframe-demo' );
