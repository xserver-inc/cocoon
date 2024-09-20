import { THEME_NAME, hexToRgba } from '../../helpers';
import { useRef, useEffect } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { store as blockEditorStore, InspectorControls, PanelColorSettings } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function edit( props ) {
  const { attributes, setAttributes, clientId } = props;
  const { labels, data, chartId, chartColor } = attributes; // chartColorを追加
  const canvasRef = useRef(null);

  let chartInstance = null;

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

  const renderChart = () => {
  const ctx = canvasRef.current.getContext('2d');

  if (chartInstance) {
    chartInstance.destroy();
  }

  chartInstance = new Chart(ctx, {
    type: 'radar',
    data: {
    labels: labels,
    datasets: [{
      data: data,
      backgroundColor: chartColor ? hexToRgba(chartColor, 0.2) : 'rgba(255, 99, 132, 0.2)', // 色をchartColorに変更
      borderColor: chartColor ? hexToRgba(chartColor, 0.9) : 'rgba(255, 99, 132, 0.9)', // 色をchartColorに変更
      borderWidth: 1
    }]
    },
    options: {
    scales: {
      r: {
      min: 0,
      max: 5,
      ticks: {
        stepSize: 1,
      },
      }
    },
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
      display: false
      },
    },
    },
  });
  };

  useEffect(() => {
  if (!chartId) return;

  renderChart();

  const handleResize = () => {
    renderChart();
  };

  window.addEventListener('resize', handleResize);

  return () => {
    window.removeEventListener('resize', handleResize);
    if (chartInstance) {
    chartInstance.destroy();
    }
  };
  }, [labels, data, chartColor, chartId]); // chartColorも依存配列に追加

  // Add click event to canvas to focus/select block
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

  return (
    <div className="radar-chart-block">
      <canvas ref={canvasRef} id={chartId} width="400" height="400" tabindex="0" />
      <InspectorControls>
        <PanelBody title={ __( 'チャート設定', THEME_NAME ) }>
          <TextControl
            label={ __( '項目', THEME_NAME ) + __( '（カンマ区切り）', THEME_NAME ) }
            value={labels.join(', ')}
            onChange={(value) => setAttributes({ labels: value.split(',').map(label => label.trim()) })}
          />
          <TextControl
            label={ __( '値', THEME_NAME ) + __( '（カンマ区切り）', THEME_NAME ) }
            value={data.join(', ')}
            onChange={(value) => {
              const newData = value.split(',').map(d => {
                let num = parseFloat(d.trim());
                num = Math.max(0, Math.min(5, num));
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
          colorSettings={ [
            {
              label: __( 'チャートカラー', THEME_NAME ),
              onChange: ( newColor ) => setAttributes( { chartColor: newColor } ) ,
              value: chartColor,
            },
          ] }
        />
      </InspectorControls>
    </div>
  );
}
