import { THEME_NAME, hexToRgba, getCanvasId, arrayValueTotal, getChartJsFontHeight } from '../../helpers';
import { useRef, useEffect, useState } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import {
  store as blockEditorStore,
  InspectorControls,
  PanelColorSettings,
  useBlockProps,
} from '@wordpress/block-editor';
import {
  PanelBody,
  TextControl,
  RangeControl,
  ButtonGroup,
  Button,
  ToggleControl,
} from '@wordpress/components';
import classnames from 'classnames';
import { __ } from '@wordpress/i18n';

const MIN_SIZE = 200;
const MAX_SIZE = 1280;
const DEFAULT_COLOR = 'rgba(255, 99, 132, 0.2)';
const DEFAULT_BORDER_COLOR = 'rgba(255, 99, 132, 0.9)';

export default function edit( props ) {
  const { attributes, setAttributes, clientId, className } = props;
  const {
    chartId,
    chartColor,
    backgroundColor,
    fontColor,
    gridColor,
    canvasSize,
    fontSize,
    fontWeight,
    maximum,
    displayTitle,
    title,
    displayLegend,
    legendText,
    labels,
    data,
    displayTotal,
    displayLabelValue,
    displayAngleLines,
    allowMaxOver,
  } = attributes;

  const canvasRef = useRef(null);
  const chartInstanceRef = useRef(null); // useRefで管理

  const { isSelected } = useSelect((select) => ({
    isSelected: select(blockEditorStore).isBlockSelected(clientId),
  }));

  const { selectBlock } = useDispatch(blockEditorStore);

  const classes = classnames( 'block-box', 'wp-block', 'radar-chart-block', {
    [ className ]: !! className,
    'is-selected': !! isSelected,
  } );
  const blockProps = useBlockProps( {
    className: classes,
  } );

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

    // キャンバス背景色を白にするカスタムプラグイン
    const customCanvasBackgroundColor = {
      id: 'customCanvasBackgroundColor',
      beforeDraw: (chart) => {
        const ctx = chart.ctx;
        const canvas = chart.canvas;
        ctx.save();
        ctx.globalCompositeOperation = 'destination-over'; // 背景色を他の要素の背後に描画
        ctx.fillStyle = backgroundColor; // キャンバスの背景色を白に設定
        ctx.fillRect(0, 0, canvas.width, canvas.height); // キャンバス全体に背景色を塗る
        ctx.restore();
      }
    };

    // 合計を表示するカスタムプラグイン
    const totalPlugin = {
      id: 'totalPlugin',
      afterDatasetsDraw(chart, args, options) {
        if (displayTotal) {
          const { ctx, chartArea: { top, left, right, bottom } } = chart;
          const centerX = (left + right) / 2;
          const centerY = (top + bottom + ((data.length % 2 === 0) ? 0 : getChartJsFontHeight(fontSize))) / 2;
          const total = `${__( '総計:', THEME_NAME )} ${arrayValueTotal(data)}`;

          ctx.save();
          ctx.font = fontSize;
          ctx.fillStyle = fontColor;
          ctx.textAlign = 'center';
          ctx.textBaseline = 'middle';
          ctx.fillText(total, centerX, centerY);
          ctx.restore();
        }
      }
    };

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
        layout: {
          padding: {
            top: 30,    // 上の余白
            bottom: 30, // 下の余白
            left: 30,   // 左の余白
            right: 30,  // 右の余白
          },
        },
        scales: {
          r: {
            angleLines: {
              display: displayAngleLines,
              color: hexToRgba(gridColor, 0.5), // アングルラインの色に設定
            },
            min: 0,
            max: maximum,
            ticks: {
              stepSize: (maximum === 100) ? 10 : 1,
              font: {
                size: fontSize, // 目盛のフォントサイズを指定
                weight: fontWeight,
              },
              color: fontColor,
              backdropColor: 'transparent', // 目盛の背景色を透明に
            },
            grid: {
              color: hexToRgba(gridColor, 0.5), // グリッド線の色を設定
            },
            pointLabels: {
              font: {
                size: fontSize, // フォントサイズを指定
                weight: fontWeight,
              },
              color: fontColor,
            }
          }
        },
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          title: {
            display: displayTitle, // タイトルの表示
            text: title, // タイトルのテキスト
            font: {
              size: fontSize + 2, // フォントサイズを指定
              weight: fontWeight + 100,
            },
            color: fontColor,
          },
          legend: {
            display: displayLegend, // 凡例表示の設定
            labels: {
              font: {
                size: fontSize, // 凡例のフォントサイズ
                weight: fontWeight,
              },
              color: fontColor,
            }
          },
          totalPlugin: true // 合計表示プラグインを有効化
        }
      },
      plugins: [customCanvasBackgroundColor, totalPlugin] // カスタム背景と合計表示プラグインをチャートに追加
    });
  };


  // チャートのレンダリングとサイズ調整
  useEffect(() => {
    if (canvasRef.current && canvasSize) {
      canvasRef.current.width = canvasSize;
      canvasRef.current.height = canvasSize;
      renderChart();
      chartInstanceRef.current.update(); // チャートを更新して再描画
    }

    return () => {
      destroyChart(); // クリーンアップ時にチャートを破棄
    };
  }, [
    chartId,
    chartColor,
    backgroundColor,
    fontColor,
    gridColor,
    canvasSize,
    fontSize,
    fontWeight,
    maximum,
    displayTitle,
    title,
    displayLegend,
    legendText,
    labels,
    data,
    displayTotal,
    displayAngleLines,
    allowMaxOver,
    displayLabelValue,
  ]);

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
    <div { ...blockProps }>
      <canvas
        ref={canvasRef}
        id={chartId}
        width={canvasSize}
        height={canvasSize}
        tabIndex="0"
        onClick={ () => selectBlock(clientId) }
        onFocus={ () => selectBlock(clientId) }
      />
      <InspectorControls>
        <PanelColorSettings
          title={ __( '色設定', THEME_NAME ) }
          colorSettings={[
            {
              label: __( 'チャート色', THEME_NAME ),
              onChange: ( newColor ) => setAttributes({ chartColor: newColor }),
              value: chartColor,
            },
            {
              label: __( '背景色', THEME_NAME ),
              onChange: ( newColor ) => setAttributes({ backgroundColor: newColor }),
              value: backgroundColor,
            },
            {
              label: __( '文字色', THEME_NAME ),
              onChange: ( newColor ) => setAttributes({ fontColor: newColor }),
              value: fontColor,
            },
            {
              label: __( 'グリッド色', THEME_NAME ) + __( '（※透過）', THEME_NAME ),
              onChange: ( newColor ) => setAttributes({ gridColor: newColor }),
              value: gridColor,
            },
          ]}
        />
        <PanelBody title={ __( 'チャート設定', THEME_NAME ) }>
          <RangeControl
            label={ __( 'キャンバスサイズ', THEME_NAME ) }
            value={ canvasSize }
            onChange={ ( value ) => setAttributes({ canvasSize: value }) }
            min={ MIN_SIZE }
            max={ MAX_SIZE }
            step="10"
          />
          <RangeControl
            label={ __( 'フォントサイズ', THEME_NAME ) }
            value={ fontSize }
            onChange={ ( value ) => setAttributes({ fontSize: value }) }
            min="8"
            max="24"
            step="1"
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
            label={ __( 'タイトルを表示する', THEME_NAME ) } // ToggleControlを追加
            checked={ displayTitle }
            onChange={ (value) => setAttributes({ displayTitle: value }) }
          />
          { displayTitle && (
            <TextControl
              label={ __( 'タイトル', THEME_NAME ) }
              value={ title }
              onChange={ (value) => setAttributes({ title: value }) }
              placeholder={ __( 'ここにタイトルを入力してください', THEME_NAME ) }
            />
          )}
          <ToggleControl
            label={ __( 'データ名を表示する', THEME_NAME ) } // ToggleControlを追加
            checked={ displayLegend }
            onChange={ (value) => setAttributes({ displayLegend: value }) }
          />
          { displayLegend && (
            <TextControl
              label={ __( 'データ名', THEME_NAME ) }
              value={ legendText }
              onChange={ (value) => setAttributes({ legendText: value }) }
              placeholder={ __( 'ここに凡例を入力してください', THEME_NAME ) }
            />
          )}
          <TextControl
            label={ __( '項目', THEME_NAME ) + __( '（カンマ区切りで何項目でも入力可）', THEME_NAME ) }
            value={labels.join(', ')}
            onChange={(value) => setAttributes({ labels: value.split(',').map(label => label.trim()) })}
            placeholder={ __( '項目A, 項目B, 項目C, 項目D, 項目E', THEME_NAME ) }
          />
          <TextControl
            label={ __( '値', THEME_NAME ) + __( '（カンマ区切りで項目の数だけ入力可）', THEME_NAME ) }
            value={data.join(', ')}
            onChange={(value) => {
              const newData = value.split(',').map(d => {
                let num = parseFloat(d.trim());
                if (!allowMaxOver) {
                  num = Math.max(0, Math.min(maximum, num));
                }

                if (isNaN(num)) {
                  num = '';
                }
                return num;
              });
              setAttributes({ data: newData });
            }}
            placeholder={ __( '1, 2, 3, 4, 5', THEME_NAME ) }
          />
          <ToggleControl
            label={ __( '総計を表示する', THEME_NAME ) } // ToggleControlを追加
            checked={ displayTotal }
            onChange={ (value) => setAttributes({ displayTotal: value }) }
          />
          <ToggleControl
            label={ __( '項目に値を表示する', THEME_NAME ) } // ToggleControlを追加
            checked={ displayLabelValue }
            onChange={ (value) => setAttributes({ displayLabelValue: value }) }
          />
          <ToggleControl
            label={ __( '値が最大値を超えるのを許可する', THEME_NAME ) } // ToggleControlを追加
            checked={ allowMaxOver }
            onChange={ (value) => setAttributes({ allowMaxOver: value }) }
          />
          <ToggleControl
            label={ __( 'アングルラインを表示する', THEME_NAME ) } // ToggleControlを追加
            checked={ displayAngleLines }
            onChange={ (value) => setAttributes({ displayAngleLines: value }) }
          />
        </PanelBody>
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

          // 複製後に特定の処理を実行する例
          handleRadarBlockDuplication(block);
      }
  });
});

// 複製時の処理を定義する関数
function handleRadarBlockDuplication(block) {
  // ここにブロックに対して行いたい処理を書く
  block.attributes.chartId = getCanvasId();
}


