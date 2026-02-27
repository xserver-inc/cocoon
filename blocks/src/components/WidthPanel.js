import { THEME_NAME } from '../helpers';
import {
  __experimentalToolsPanel as ToolsPanel,
  __experimentalToolsPanelItem as ToolsPanelItem,
  __experimentalToggleGroupControl as ToggleGroupControl,
  __experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useViewportMatch } from '@wordpress/compose';

// ボタン幅設定パネル（3点メニュー付き）
export default function WidthPanel( { selectedWidth, setAttributes } ) {
  // モバイル判定（中サイズ未満ならモバイル）
  const isMobile = useViewportMatch( 'medium', '<' );

  // 3点メニューのドロップダウンをサイドバーの左側に表示する設定
  // WordPress Coreのボタンブロックと同じ位置に表示するための設定
  const dropdownMenuProps = ! isMobile
    ? {
        popoverProps: {
          placement: 'left-start',
          // サイドバー幅(280px) - 右パディング(16px) - ボタン幅(24px) = 240px
          offset: 240,
        },
      }
    : {};

  // 幅設定が変更されているかどうかを判定（デフォルトはundefined）
  const hasValue = () => !! selectedWidth;

  // 「すべてリセット」や個別リセット時に幅をundefinedに戻す
  const resetWidth = () => setAttributes( { width: undefined } );

  return (
    <ToolsPanel
      label={ __( '設定', THEME_NAME ) }
      // すべてリセット時のハンドラー
      resetAll={ () => resetWidth() }
      // 3点メニューの表示位置を制御するプロパティ
      dropdownMenuProps={ dropdownMenuProps }
    >
      <ToolsPanelItem
        label={ __( '幅', THEME_NAME ) }
        // この項目が変更済みかどうかを返す関数
        hasValue={ hasValue }
        // 個別リセット時のハンドラー
        onDeselect={ resetWidth }
        isShownByDefault
      >
        <ToggleGroupControl
          label={ __( '幅', THEME_NAME ) }
          isBlock
          // 選択済みのオプションを再クリックで解除可能にする
          isDeselectable
          value={ selectedWidth }
          onChange={ ( newWidth ) => setAttributes( { width: newWidth } ) }
          __nextHasNoMarginBottom={ true }
          __next40pxDefaultSize={ true }
        >
          { [ '25', '50', '75', '100' ].map( ( widthValue ) => (
            <ToggleGroupControlOption
              key={ widthValue }
              value={ widthValue }
              label={ `${ widthValue }%` }
            />
          ) ) }
        </ToggleGroupControl>
      </ToolsPanelItem>
    </ToolsPanel>
  );
}
