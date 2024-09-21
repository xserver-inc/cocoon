import { THEME_NAME, hexToRgba } from '../../helpers';
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
  const { canvasSize, maximum, displayLegend, legendText, labels, data, chartId, chartColor } = attributes;
  const canvasRef = useRef(null);
  const chartInstanceRef = useRef(null); // useRefで管理

  const { isSelected } = useSelect((select) => ({
    isSelected: select(blockEditorStore).isBlockSelected(clientId),
  }));

  const { selectBlock } = useDispatch(blockEditorStore);

  useEffect(() => {
    if (!chartId) {
      const newChartId = `radarChart-${Math.random().toString(36).substr(2, 9)}`;
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
        labels: labels,
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
            min: 0,
            max: maximum,
            ticks: {
              stepSize: (maximum === 100) ? 10 : 1,
            },
          }
        },
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: displayLegend // 凡例表示の設定
          },
        },
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
  }, [canvasSize, maximum, displayLegend, legendText, labels, data, chartColor, chartId]);

  // マウスクリックでブロックを選択（フォーカス）する
  useEffect(() => {
    if (canvasRef.current) {
      const handleClick = () => {
        selectBlock(clientId);
      };
      canvasRef.current.addEventListener('click', handleClick);

      return () => {
        canvasRef.current.removeEventListener('click', handleClick);
      };
    }
  }, [canvasRef.current]);

    // クリック時にパーセンテージを更新する関数
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
            label={ __( '凡例を表示', THEME_NAME ) } // ToggleControlを追加
            checked={ displayLegend }
            onChange={ (value) => setAttributes({ displayLegend: value }) }
          />
          { displayLegend && (
            <TextControl
              label={ __( '凡例名', THEME_NAME ) }
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
