import { THEME_NAME, hexToRgba, getCanvasId } from '../../helpers';
import { useRef, useEffect, useState } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import {
  store as blockEditorStore,
  InspectorControls,
  PanelColorSettings,
} from '@wordpress/block-editor';
import {
  PanelBody,
  TextControl,
  RangeControl,
  ButtonGroup,
  Button,
  ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const MIN_SIZE = 200;
const MAX_SIZE = 1280;
const DEFAULT_COLOR = 'rgba(255, 99, 132, 0.2)';
const DEFAULT_BORDER_COLOR = 'rgba(255, 99, 132, 0.9)';

export default function edit( props ) {
  const { attributes, setAttributes, clientId } = props;
  const { canvasSize, maximum, displayLegend, legendText, labels, data, chartId, displayAngleLines, displayLabelValue, chartColor } = attributes;
  const canvasRef = useRef(null);
  const chartInstanceRef = useRef(null); // useRefで管理

  const { isSelected } = useSelect((select) => ({
    isSelected: select(blockEditorStore).isBlockSelected(clientId),
  }));

  const { selectBlock } = useDispatch(blockEditorStore);

  useEffect(() => {
    if (!chartId) {
      const newChartId = getCanvasId();
      setAttributes({ chartId: newChartId });
    }
  }, []);

  // チャートの破棄関数
  const destroyChart = () => {
    if (chartInstanceRef.current) {
      chartInstanceRef.current.destroy();
      chartInstanceRef.current = null;
    }
  };

  const renderChart = () => {
    const ctx = canvasRef.current.getContext('2d');

    destroyChart(); // 既存のチャートがあれば破棄

    // 新しいチャートを生成
    chartInstanceRef.current = new Chart(ctx, {
      type: 'radar',
      data: {
        labels: labels.map((label, index) => displayLabelValue ? `${label} ( ${data[index]} )` : label),
        datasets: [{
          label: legendText,
          data: data,
          backgroundColor: chartColor ? hexToRgba(chartColor, 0.2) : DEFAULT_COLOR,
          borderColor: chartColor ? hexToRgba(chartColor, 0.9) : DEFAULT_BORDER_COLOR,
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          r: {
            angleLines: {
              display: displayAngleLines
            },
            min: 0,
            max: maximum,
            ticks: {
              stepSize: (maximum === 100) ? 10 : 1
            }
          }
        },
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: displayLegend // 凡例表示の設定
          }
        }
      },
    });
  };

  // チャートのレンダリングとサイズ調整
  useEffect(() => {
    if (canvasRef.current && canvasSize) {
      canvasRef.current.width = canvasSize;
      canvasRef.current.height = canvasSize;
      renderChart();
    }

    return () => {
      destroyChart(); // クリーンアップ時にチャートを破棄
    };
  }, [canvasSize, maximum, displayLegend, legendText, labels, data, chartColor, displayAngleLines, displayLabelValue, chartId]);

  // マウスクリックでブロックを選択（フォーカス）する
  useEffect(() => {
    const handleClick = () => {
      selectBlock(clientId);
    };

    if (canvasRef.current) {
      canvasRef.current.addEventListener('click', handleClick);
    }

    return () => {
      if (canvasRef.current) {
        canvasRef.current.removeEventListener('click', handleClick);
      }
    };
  }, [canvasRef, clientId, selectBlock]); // clientId と selectBlock を依存配列に追加

  // クリック時に最大値を更新する関数
  const handleMaximumChange = ( newMaximum ) => {
    setAttributes({ maximum: newMaximum });
  };

  return (
    <div className="radar-chart-block">
      <canvas ref={canvasRef} id={chartId} width={canvasSize} height={canvasSize} tabIndex="0" />
      <InspectorControls>
        <PanelBody title={ __( 'チャート設定', THEME_NAME ) }>
          <RangeControl
            label={ __( 'サイズ', THEME_NAME ) }
            value={ canvasSize }
            onChange={ ( value ) => setAttributes({ canvasSize: value }) }
            min={ MIN_SIZE }
            max={ MAX_SIZE }
            step="10"
          />
          <p>{ __( '最大値', THEME_NAME )}</p>
          <ButtonGroup>
            { [5, 10, 100].map( ( value ) => (
              <Button
                key={ value }
                isPrimary={ value === maximum }
                onClick={ () => handleMaximumChange( value ) }
              >
                { value }
              </Button>
            ) ) }
          </ButtonGroup>
          <br /><br />
          <ToggleControl
            label={ __( 'データ名を表示', THEME_NAME ) } // ToggleControlを追加
            checked={ displayLegend }
            onChange={ (value) => setAttributes({ displayLegend: value }) }
          />
          { displayLegend && (
            <TextControl
              label={ __( 'データ名', THEME_NAME ) }
              value={ legendText }
              onChange={ (value) => setAttributes({ legendText: value }) }
            />
          )}
          <TextControl
            label={ __( '項目', THEME_NAME ) + __( '（カンマ区切りで何項目でも入力可）', THEME_NAME ) }
            value={labels.join(', ')}
            onChange={(value) => setAttributes({ labels: value.split(',').map(label => label.trim()) })}
          />
          <TextControl
            label={ __( '値', THEME_NAME ) + __( '（カンマ区切りで項目の数だけ入力可）', THEME_NAME ) }
            value={data.join(', ')}
            onChange={(value) => {
              const newData = value.split(',').map(d => {
                let num = parseFloat(d.trim());
                num = Math.max(0, Math.min(maximum, num));
                if (isNaN(num)) {
                  num = '';
                }
                return num;
            });
            setAttributes({ data: newData });
            }}
          />
          <ToggleControl
            label={ __( 'アングルラインを表示', THEME_NAME ) } // ToggleControlを追加
            checked={ displayAngleLines }
            onChange={ (value) => setAttributes({ displayAngleLines: value }) }
          />
          <ToggleControl
            label={ __( '項目に値を表示', THEME_NAME ) } // ToggleControlを追加
            checked={ displayLabelValue }
            onChange={ (value) => setAttributes({ displayLabelValue: value }) }
          />
        </PanelBody>
        <PanelColorSettings
          title={ __( '色設定', THEME_NAME ) }
          colorSettings={[
            {
              label: __( 'チャートカラー', THEME_NAME ),
              onChange: ( newColor ) => setAttributes({ chartColor: newColor }),
              value: chartColor,
            },
          ]}
        />
      </InspectorControls>
    </div>
  );
}

wp.data.subscribe(() => {
  const blocks = wp.data.select('core/block-editor').getBlocks();

  blocks.forEach((block) => {
      // cocoon-blocks/radarブロックが追加または複製されたかをチェック
      if (block.name === 'cocoon-blocks/radar') {
          // ここで処理を行う
          // console.log('cocoon-blocks/radarブロックが複製されました', block);

          // 複製後に特定の処理を実行する例
          handleRadarBlockDuplication(block);
      }
  });
});

// 複製時の処理を定義する関数
function handleRadarBlockDuplication(block) {
  // ここにブロックに対して行いたい処理を書く
  // console.log('複製後の処理を実行', block);
  // console.log(block.attributes.chartId);
  block.attributes.chartId = getCanvasId();
  // console.log(block.attributes.chartId);
}


// wp.data.subscribe(() => {
//   const blocks = wp.data.select('core/block-editor').getBlocks();
//   const previousBlocks = wp.data.select('core/block-editor').getPreviousBlocks();

//   // ブロック数が変わっているか確認（ブロックが貼り付けられた場合）
//   if (blocks.length !== previousBlocks.length) {
//       const newBlock = blocks[blocks.length - 1]; // 最後に追加されたブロック

//       // 追加されたブロックがcocoon-blocks/radarかどうかをチェック
//       if (newBlock && newBlock.name === 'cocoon-blocks/radar') {
//           console.log('cocoon-blocks/radarブロックがクリップボードから貼り付けられました', newBlock);

//           // 貼り付け時の処理を実行する例
//           handleRadarBlockPaste(newBlock);
//       }
//   }
// });

// // 貼り付け時の処理を定義する関数
// function handleRadarBlockPaste(block) {
//   // ここにブロックに対して行いたい処理を書く
//   console.log('貼り付け後の処理を実行', block);
//   block.attributes.chartId = getCanvasId();
// }



